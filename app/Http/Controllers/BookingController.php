<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FieldType;
use App\Models\HourlyRate;
use App\Models\PackageRate;
use App\Models\UserMembership;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function createHourly()
    {
        $confirmedBookings = Booking::where('status', 'confirmed')->where('booking_date', '>=', now()->toDateString())->get();
        return view('user.booking.create_hourly', compact('confirmedBookings'));
    }

    public function createPackage()
    {
        $confirmedBookings = Booking::where('status', 'confirmed')
            ->where('booking_date', '>=', today())
            ->with('fieldType')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        return view('user.booking.create_package', compact('confirmedBookings'));
    }

    public function createMembership()
    {
        $activeMembership = UserMembership::where('user_id', Auth::id())
            ->where('status', 'active')->where('expires_at', '>', now())
            ->where('remaining_hours', '>', 0)->with('membershipTier')->first();

        if (! $activeMembership) {
            return redirect()->route('dashboard')->with('error', 'คุณยังไม่มีบัตรสมาชิกที่สามารถใช้งานได้');
        }

        $confirmedBookings = Booking::where('status', 'confirmed')
            ->where('booking_date', '>=', now()->toDateString())->get();

        return view('user.booking.create_membership', compact('activeMembership', 'confirmedBookings'));
    }
    public function confirm(Request $request)
    {
        // --- 1. ตรวจสอบเงื่อนไขทางธุรกิจพื้นฐาน ---
        $bookingDate = Carbon::parse($request->input('booking_date'));
        if ($bookingDate->isMonday()) {
            return redirect()->back()->with('error', 'ขออภัย สนามปิดให้บริการทุกวันจันทร์')->withInput();
        }

        $bookingType = $request->input('booking_type');

                           // --- 2. ตรวจสอบความว่างของสนาม (Availability Check) ---
        $isBooked = false; // กำหนดค่าเริ่มต้น
        if ($bookingType === 'hourly' || $bookingType === 'membership') {
            $validated = $request->validate(['field_type_id' => 'required', 'start_time' => 'required', 'end_time' => 'required']);

            if ($validated['start_time'] < '18:00:00' && $validated['end_time'] > '18:00:00') {
                return redirect()->back()->with('error', 'ไม่สามารถจองคร่อมช่วงเวลา 18:00 น. ได้')->withInput();
            }

            $isBooked = Booking::where('field_type_id', $validated['field_type_id'])
                ->where('booking_date', $request->input('booking_date'))
                ->whereIn('status', ['paid', 'verifying', 'pending_payment']) // <-- ใช้ whereIn
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();
        } elseif ($bookingType === 'daily_package') {
            $isBooked = Booking::where('booking_date', $request->input('booking_date'))
                ->whereIn('status', ['paid', 'verifying', 'pending_payment']) // <-- ใช้ whereIn
                ->exists();
        }

        if ($isBooked) {
            return redirect()->back()->with('error', 'ขออภัย ช่วงเวลาหรือวันที่ท่านเลือกมีผู้จองแล้ว')->withInput();
        }

        // --- 3. ถ้าทุกอย่างถูกต้อง ให้คำนวณราคา/ชั่วโมง ---
        $summary = ['booking_inputs' => $request->all()];
        try {
            if ($bookingType === 'hourly') {
                $summary = array_merge($summary, $this->calculateHourlyRate($request));
            } elseif ($bookingType === 'daily_package') {
                $summary = array_merge($summary, $this->calculatePackageRate($request));
            } elseif ($bookingType === 'membership') {
                $summary = array_merge($summary, $this->calculateMembershipUsage($request));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        // --- 4. แสดงหน้ายืนยันข้อมูล ---
        return view('user.booking.confirm', compact('summary'));
    }
    public function store(Request $request)
    {
        try {
            // ใช้ Transaction เพื่อให้แน่ใจว่าถ้ามีขั้นตอนไหนพลาด จะยกเลิกทั้งหมด
            $booking = DB::transaction(function () use ($request) {
                // 1. ดึงวันที่จองจากฟอร์ม
                $bookingDate = Carbon::parse($request->input('booking_date'));

                // 2. นับจำนวนการจองที่มีอยู่แล้วใน "วันนั้น"
                $bookingsOnDate = Booking::where('booking_date', $bookingDate->toDateString())->count();

                // 3. สร้างลำดับใหม่ (นับจากของเดิม + 1)
                $newSequence = $bookingsOnDate + 1;

                // 4. สร้าง Booking Code รูปแบบใหม่
                $bookingCode = $bookingDate->format('ymd') . $newSequence;

                // 5. เตรียมข้อมูลทั้งหมดที่จะบันทึก
                $bookingType = $request->input('booking_type');
                $dataToSave  = [
                    'user_id'            => Auth::id(),
                    'booking_code'       => $bookingCode,
                    'booking_type'       => $bookingType,
                    'booking_date'       => $bookingDate,
                    'notes'              => $request->input('notes'),
                    'base_price'         => $request->input('base_price', 0),
                    'overtime_charges'   => $request->input('overtime_charges', 0),
                    'discount'           => $request->input('discount', 0),
                    'total_price'        => $request->input('total_price'), // ราคารวมทั้งหมด
                    'duration_in_hours'  => $request->input('duration_in_hours', 0),
                    'hours_deducted'     => $request->input('hours_deducted'),
                    'user_membership_id' => $request->input('user_membership_id'),
                    'status'             => 'pending_payment',
                    'expires_at'         => now()->addMinutes(15),
                    // เพิ่มการจัดการค่ามัดจำ
                    'deposit_amount'     => 0, // ค่าเริ่มต้น
                ];

                // 6. เพิ่มข้อมูลเฉพาะตามประเภทการจอง
                if ($bookingType === 'hourly' || $bookingType === 'membership') {
                    $dataToSave['field_type_id'] = $request->input('field_type_id');
                    $dataToSave['start_time']    = $request->input('start_time');
                    $dataToSave['end_time']      = $request->input('end_time');

                    if ($bookingType === 'hourly') {
                        // สำหรับรายชั่วโมง ยอดมัดจำคือยอดเต็ม
                        $dataToSave['deposit_amount'] = $request->input('total_price');
                    }

                } elseif ($bookingType === 'daily_package') {
                    $packageRate = PackageRate::where('package_name', $request->input('package_name'))
                        ->where('rental_type', $request->input('rental_type'))
                        ->firstOrFail();

                    $dataToSave['start_time'] = $packageRate->base_start_time;
                    $dataToSave['end_time']   = $request->has('wants_overtime') ? $request->input('overtime_end_time') : $packageRate->base_end_time;

                    // สำหรับเหมาวัน ยอดมัดจำคือ 50% ของราคารวมทั้งหมด
                    $dataToSave['deposit_amount'] = $request->input('deposit_amount');

                    if ($request->input('package_name') !== 'เหมา 2 สนาม') {
                        $fieldType                   = FieldType::where('name', $request->input('package_name'))->first();
                        $dataToSave['field_type_id'] = $fieldType->id ?? null;
                    } else {
                        $dataToSave['field_type_id'] = null;
                    }
                    $dataToSave['price_calculation_details'] = ['rental_type' => $request->input('rental_type'), 'package_name' => $request->input('package_name')];
                }

                if ($bookingType === 'membership') {
                    // ถ้าใช้บัตรสมาชิก ให้สถานะเป็น 'paid' (จ่ายแล้ว) และ 'confirmed' ทันที
                    $dataToSave['status']     = 'paid';
                    $dataToSave['expires_at'] = null;
                } else {
                    // ประเภทอื่น ให้เป็น 'pending_payment' เพื่อเข้าสู่กระบวนการชำระเงิน
                    $dataToSave['status']     = 'pending_payment';
                    $dataToSave['expires_at'] = now()->addMinutes(15);
                }

                // 7. สร้างการจองด้วยข้อมูลทั้งหมดในครั้งเดียว
                $booking = Booking::create($dataToSave);

                // 8. หักชั่วโมงสมาชิก (ถ้ามี)
                if ($booking->booking_type === 'membership') {
                    $membership = UserMembership::find($booking->user_membership_id);
                    if ($membership) {
                        $membership->remaining_hours -= $booking->hours_deducted;
                        if ($membership->remaining_hours <= 0) {$membership->status = 'used_up';}
                        $membership->save();
                    }
                }

                return $booking;
            });

            // Redirect ไปหน้าชำระเงิน
            if ($booking->status === 'pending_payment') {
                return redirect()->route('user.booking.payment', $booking);
            }
            $this->pushMessageToGroupFromBooking($booking);
            return redirect()->route('user.dashboard')->with('success', 'การจองด้วยบัตรสมาชิกสำเร็จแล้ว! รหัสการจองคือ ' . $booking->booking_code);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการสร้างการจอง: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * รับไฟล์สลิปที่อัปโหลด, จัดเก็บ, และอัปเดตสถานะการจอง
     */
    public function uploadSlip(Request $request, $id)
    {
        // 1. ค้นหาการจองพร้อมกับข้อมูลผู้ใช้ (Eager Loading)
        $booking = Booking::with(['user', 'fieldType'])->findOrFail($id);

        // 2. ตรวจสอบสิทธิ์
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ในการดำเนินการนี้');
        }

        // 3. ตรวจสอบข้อมูล
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 4. สร้างชื่อและจัดเก็บไฟล์
        $file        = $request->file('slip_image');
        $extension   = $file->getClientOriginalExtension();
        $newFilename = $booking->booking_code . '.' . $extension;
        $path        = $file->storeAs('slips', $newFilename, 'public');

        // 5. อัปเดตฐานข้อมูล
        $booking->update([
            'slip_image_path' => 'public/' . $path,
            'status'          => 'verifying',
        ]);

        // 6. เรียกใช้เมธอดใหม่เพื่อส่ง LINE Notify
        $this->pushMessageToGroupFromBooking($booking);

        // 7. ส่งกลับไปหน้า Dashboard
        return redirect()->route('user.dashboard')->with('success', 'อัปโหลดสลิปสำเร็จแล้ว รอการตรวจสอบจากเจ้าหน้าที่');
    }

    public function show(Booking $booking)
    {
        // 1. ตรวจสอบสิทธิ์ (Authorization)
        // ให้แน่ใจว่าผู้ใช้ที่ Login อยู่เป็นเจ้าของการจองนี้เท่านั้น
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'ACCESS DENIED'); // ป้องกันไม่ให้ดูการจองของคนอื่น
        }

        // 2. โหลดข้อมูลที่เกี่ยวข้องเพิ่มเติม (ถ้าจำเป็น)
        $booking->load('fieldType', 'user');

        // 3. ส่งข้อมูลไปยัง View ใหม่
        return view('user.booking.show', compact('booking'));
    }
    public function createMembershipBooking()
    {
        // 1. ตรวจสอบหาบัตรสมาชิกที่ใช้งานได้ของผู้ใช้
        $activeMembership = UserMembership::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('remaining_hours', '>', 0)
            ->with('membershipTier')
            ->first();

        // 2. ถ้าไม่พบบัตร ให้เด้งกลับไปหน้า Dashboard พร้อมข้อความแจ้งเตือน
        if (! $activeMembership) {
            return redirect()->route('dashboard')->with('error', 'คุณยังไม่มีบัตรสมาชิกที่สามารถใช้งานได้');
        }

        // 3. ดึงข้อมูลการจองอื่นๆ ที่จำเป็น (เหมือนเดิม)
        $confirmedBookings = Booking::where('status', 'paid')
            ->where('booking_date', '>=', now()->toDateString())
            ->with('fieldType')->orderBy('booking_date')->orderBy('start_time')->get();

        // 4. ส่งข้อมูลทั้งหมดไปที่ View ใหม่
        return view('user.booking.membership_create', compact('activeMembership', 'confirmedBookings'));
    }

    private function calculateHourlyRate(Request $request)
    {
        // --- 1. รับข้อมูลจากฟอร์มและแปลงเป็น Object ที่ใช้ง่าย ---
        $fieldTypeId = $request->input('field_type_id');
        $startTime   = Carbon::parse($request->input('start_time'));
        $endTime     = Carbon::parse($request->input('end_time'));
        $bookingDate = Carbon::parse($request->input('booking_date'));

        // --- 2. เตรียมตัวแปรสำหรับเก็บผลลัพธ์ ---
        $totalPrice      = 0;
        $priceBreakdown  = []; // สร้าง Array เพื่อเก็บรายละเอียดราคาแต่ละชั่วโมง
        $thaiDayOfWeek   = $this->getThaiDayOfWeek($bookingDate);
        $durationInHours = $startTime->diffInHours($endTime);

        // --- 3. วนลูปทีละชั่วโมงเพื่อค้นหาราคาที่ถูกต้อง ---
        for ($time = $startTime->copy(); $time < $endTime; $time->addHour()) {

            // Query หาราคาสำหรับ "ชั่วโมงนั้นๆ"
            $rate = HourlyRate::where('field_type_id', $fieldTypeId)
                ->where('day_of_week', $thaiDayOfWeek)
            // เช็คว่าชั่วโมงปัจจุบัน อยู่ในกรอบเวลาราคาไหน
                ->where('start_time', '<=', $time->format('H:i:s'))
                ->where('end_time', '>', $time->format('H:i:s')) // ใช้ > เพราะ end_time คือจุดเริ่มต้นของชั่วโมงถัดไป
                ->first();

            // --- 4. ตรวจสอบผลลัพธ์และคำนวณ ---
            if ($rate) {
                // ถ้าราคา ให้บวกเพิ่มเข้าไปในยอดรวม
                $totalPrice += $rate->price_per_hour;
                // เก็บรายละเอียดไว้แสดงผล
                $priceBreakdown[] = [
                    'time'  => $time->format('H:i') . ' - ' . $time->copy()->addHour()->format('H:i'),
                    'price' => $rate->price_per_hour,
                ];
            } else {
                // ถ้าไม่เจอราคาสำหรับชั่วโมงใดชั่วโมงหนึ่ง ให้หยุดทำงานและแจ้งเตือนทันที
                abort(404, "ระบบไม่พบเรทราคาสำหรับช่วงเวลา " . $time->format('H:i:s'));
            }
        }

                                // 2. คำนวณส่วนลด
        $discountAmount = 0;    // กำหนดค่าเริ่มต้นเป็น 0
        $discountReason = null; // สำหรับแสดงเหตุผลของส่วนลด

        // ตรวจสอบว่าจองตั้งแต่ 2 ชั่วโมงขึ้นไปหรือไม่
        if ($durationInHours >= 2) {
            $discountAmount = 100.00;
            $discountReason = 'ส่วนลดเมื่อใช้บริการครบ 2 ชั่วโมง';
        }

        // 3. คำนวณราคาสุทธิ
        $finalPrice = $totalPrice - $discountAmount;

        // 4. ส่งข้อมูลสรุปกลับไป (เพิ่ม Key ใหม่เข้าไป)
        return [
            'title'                   => 'สรุปการจองรายชั่วโมง',
            'field_name'              => FieldType::find($fieldTypeId)->name,
            'booking_date'            => $bookingDate,
            'time_range'              => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'duration_in_hours'       => $durationInHours,
            'subtotal_price'          => $totalPrice,
            'discount_amount'         => $discountAmount,
            'discount_reason'         => $discountReason,
            'total_price'             => $finalPrice,
            'special_perks'           => null,
            'price_breakdown_details' => $priceBreakdown,
            'hours_to_deduct'         => null,
        ];

    }

    private function calculatePackageRate(Request $request)
    {
        $packageName = $request->input('package_name');
        $rentalType  = $request->input('rental_type');
        $bookingDate = Carbon::parse($request->input('booking_date'));

        $rate = PackageRate::where('package_name', $packageName)
            ->where('rental_type', $rentalType)
            ->first();

        if (! $rate) {
            abort(404, "ไม่พบเรทราคาสำหรับแพ็กเกจและประเภทงานที่เลือก");
        }

        $basePrice       = $rate->base_price;
        $overtimeCost    = 0;
        $overtimeDetails = 'ไม่มี';
        $startTime       = Carbon::parse($rate->base_start_time);
        $endTime         = Carbon::parse($rate->base_end_time);

        if ($request->has('wants_overtime') && $request->input('wants_overtime') == '1') {
            $overtimeEndTime = Carbon::parse($request->input('overtime_end_time'));
            if ($overtimeEndTime->gt(Carbon::parse($rate->overtime_max_end_time))) {
                abort(400, 'ไม่สามารถจองล่วงเวลาเกิน ' . $rate->overtime_max_end_time);
            }
            $overtimeHours   = $startTime->diffInHours($overtimeEndTime) - 10;
            $overtimeCost    = $overtimeHours * $rate->overtime_price_per_hour_per_field;
            $overtimeDetails = "{$overtimeHours} ชั่วโมง (ถึง {$overtimeEndTime->format('H:i')} น.)";
            $endTime         = $overtimeEndTime;
        }

        $totalPrice      = $basePrice + $overtimeCost;
        $durationInHours = $startTime->diffInHours($endTime);

        $depositAmount = $totalPrice * 0.5;

        // และลบออกจาก return array ด้วย
        return [
            'title' => "สรุปการจองแบบเหมาวัน ({$rentalType})",
            'package_name' => $packageName,
            'booking_date' => $bookingDate,
            'time_range' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'base_price' => $basePrice,
            'overtime_cost' => $overtimeCost,
            'overtime_details' => $overtimeDetails,
            'total_price' => $totalPrice,
            'deposit_amount' => $depositAmount,
            'duration_in_hours' => $durationInHours,
            'special_perks' => null,
            'hours_to_deduct' => null,
            'discount_amount' => 0,
            'discount_reason' => null,
        ];
    }
    private function calculateMembershipUsage(Request $request)
    {
        $bookingDate = Carbon::parse($request->input('booking_date'));

        // 1. ดึงข้อมูลบัตรสมาชิกที่ Active อยู่ของผู้ใช้
        $membership = UserMembership::where('user_id', Auth::id())
            ->where('status', 'active')->where('expires_at', '>', now())
            ->with('membershipTier')->first();

        if (! $membership) {
            throw new Exception('ไม่พบบัตรสมาชิกที่สามารถใช้งานได้');
        }

        $tier            = $membership->membershipTier;
        $startTime       = Carbon::parse($request->input('start_time'));
        $endTime         = Carbon::parse($request->input('end_time'));
        $durationInHours = $startTime->diffInHours($endTime);

        // 2. คำนวณชั่วโมงที่จะถูกหัก
        $hoursToDeduct = 0;
        for ($time = $startTime->copy(); $time < $endTime; $time->addHour()) {
            if ($time->hour >= 18) {
                $hoursToDeduct += $tier->overtime_hour_multiplier;
            } else {
                $hoursToDeduct += 1;
            }
        }

        // 3. ตรวจสอบว่าชั่วโมงคงเหลือเพียงพอหรือไม่
        if ($membership->remaining_hours < $hoursToDeduct) {
            throw new Exception('ชั่วโมงในบัตรสมาชิกของคุณไม่เพียงพอ (ต้องการ ' . $hoursToDeduct . ' ชม. แต่เหลือ ' . $membership->remaining_hours . ' ชม.)');
        }

        return [
            'title'              => 'สรุปการใช้สิทธิ์บัตรสมาชิก',
            'field_name'         => FieldType::find($request->input('field_type_id'))->name,
            'booking_date'       => $bookingDate,
            'time_range'         => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'duration_in_hours'  => $durationInHours,
            'hours_to_deduct'    => $hoursToDeduct,
            'user_membership_id' => $membership->id,
            'total_price'        => 0,
            'discount_amount'    => 0,
            'discount_reason'    => null,
        ];
    }

    private function getThaiDayOfWeek(Carbon $date)
    {
        $days = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        return $days[$date->dayOfWeek];
    }

    private function pushMessageToGroupFromBooking(Booking $booking)
    {
        $accessToken = 'UUuw3veqOqlr4y5kjaXM27jrs/qQHkqhtX2vFUmDwAXOzk1ixPyRjSsRH/6y/tBk8Z0rPSdCm061R/KNq0PORlLxqNaYhOb7u5AMpzszzIGET7G/3spPDBxIiMYlM/fdAzUksR9yZcWIhak5RVG3PQdB04t89/1O/w1cDnyilFU=';
        $groupId     = 'C8828a7ce6dd1f2f1d9ad3638489c6e9d';

        // 1. เตรียมข้อมูล "ประเภทการจอง"
        $bookingTypeDescription = '';
        switch ($booking->booking_type) {
            case 'hourly':
                $bookingTypeDescription = 'รายชั่วโมง';
                break;
            case 'daily_package':
                $bookingTypeDescription = 'เหมาวัน';
                break;
            case 'membership':
                $bookingTypeDescription = 'ใช้บัตรสมาชิก';
                break;
            default:
                $bookingTypeDescription = 'ไม่ระบุ';
                break;
        }

// 2. เตรียมข้อมูล "สนาม/แพ็กเกจ"
        $itemDetails = '';
        if ($booking->booking_type === 'daily_package') {
            $itemDetails = $booking->price_calculation_details['package_name'] ?? 'เหมาวัน';
        } else {
            // ถ้าเป็นประเภทอื่น ให้แสดงชื่อสนาม
            $itemDetails = optional($booking->fieldType)->name ?? 'ไม่ระบุ';
        }

// ตรวจสอบประเภทการจองเพื่อกำหนด "ยอดที่ต้องชำระ" ที่ถูกต้อง
        $paymentAmount = number_format($booking->total_price, 2);
        if ($booking->booking_type === 'daily_package') {
            $paymentAmount = number_format($booking->deposit_amount, 2);
        }

        $textMessage = "📌 แจ้งเตือนการจองใหม่\n" .
        "--------------------\n" .
        "รหัสการจอง : {$booking->booking_code}\n" .
        "ชื่อผู้จอง : {$booking->user->name}\n" .
        "ประเภทการจอง : {$bookingTypeDescription}\n" .
        "สนาม/แพ็กเกจ : {$itemDetails}\n" .
        "วันที่ :  " . thaidate('j F Y', (string) $booking->booking_date) . "\n" .
        "เวลา : " . Carbon::parse($booking->start_time)->format('H:i') . " - " . Carbon::parse($booking->end_time)->format('H:i') . " น.\n" .
            "รวมเวลา : {$booking->duration_in_hours} ชั่วโมง\n" .
            "ยอดชำระ : {$paymentAmount} บาท\n" .
            "เบอร์โทร :  {$booking->user->phone_number}\n ";

        $body = [
            'to'       => $groupId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $textMessage,
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/push', $body);

        if (! $response->successful()) {
            Log::error('LINE Push Failed', ['response' => $response->body()]);
        }
    }

/**
 * ตรวจสอบว่าช่วงเวลาที่ร้องขอมานั้นว่างหรือไม่
 */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'field_type_id' => 'required|exists:field_types,id',
            'booking_date'  => 'required|date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ]);

        $bookingDate = Carbon::parse($validated['booking_date']);
        if ($bookingDate->isMonday()) {
            return response()->json([
                'available' => false,
                'message'   => 'ขออภัย สนามปิดให้บริการทุกวันจันทร์', // ส่งเหตุผลกลับไปด้วย
            ]);
        }

        $isBooked = Booking::where('field_type_id', $validated['field_type_id'])
            ->where('booking_date', $validated['booking_date'])
            ->whereIn('status', ['paid', 'verifying', 'pending_payment', 'confirmed'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        $message = $isBooked ? 'ขออภัย ช่วงเวลานี้มีผู้จองแล้ว' : 'ช่วงเวลานี้ว่าง สามารถจองได้';
        return response()->json([
            'available' => ! $isBooked,
            'message'   => $message, // ส่งข้อความสถานะกลับไปเสมอ
        ]);
    }

    public function showPayment(Booking $booking)
    {
        // 1. ตรวจสอบสิทธิ์: ให้แน่ใจว่าผู้ใช้ที่ Login อยู่เป็นเจ้าของการจองนี้
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'ACCESS DENIED');
        }

        // 2. ตรวจสอบสถานะ: หน้านี้จะเข้าได้ต่อเมื่อสถานะเป็น 'pending_payment' เท่านั้น
        if ($booking->status !== 'pending_payment') {
            return redirect()->route('user.dashboard')->with('error', 'การจองนี้ไม่อยู่ในสถานะที่ต้องชำระเงินแล้ว');
        }

        // 3. ตรวจสอบว่าหมดเวลาหรือยัง
        if (Carbon::parse($booking->expires_at)->isPast()) {
            // แต่เราดักไว้ก่อนเพื่อแสดงข้อความที่ชัดเจน
            return redirect()->route('user.dashboard')->with('error', 'การจองนี้หมดเวลาในการชำระเงินแล้ว');
        }

        // 4. ส่งข้อมูลไปยัง View ใหม่
        return view('user.booking.payment', compact('booking'));
    }
}
