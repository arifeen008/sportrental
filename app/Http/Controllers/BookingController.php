<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FieldType;
use App\Models\HourlyRate;
use App\Models\MembershipTier;
use App\Models\PackageRate;
use App\Models\UserMembership;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function createHourly()
    {
        $confirmedBookings = Booking::where('payment_status', 'paid')
            ->where('booking_date', '>=', now()->toDateString())->get();
        return view('user.booking.create_hourly', compact('confirmedBookings'));
    }

/**
 * แสดงฟอร์มสำหรับ "การจองเหมาวัน"
 */
    public function createPackage()
    {
        $confirmedBookings = Booking::where('payment_status', 'paid')
            ->where('booking_date', '>=', now()->toDateString())->get();
        return view('user.booking.create_package', compact('confirmedBookings'));
    }

/**
 * แสดงฟอร์มสำหรับ "การจองด้วยบัตรสมาชิก"
 */
    public function createMembership()
    {
        $activeMembership = UserMembership::where('user_id', Auth::id())
            ->where('status', 'active')->where('expires_at', '>', now())
            ->where('remaining_hours', '>', 0)->with('membershipTier')->first();

        if (! $activeMembership) {
            return redirect()->route('dashboard')->with('error', 'คุณยังไม่มีบัตรสมาชิกที่สามารถใช้งานได้');
        }

        $confirmedBookings = Booking::where('payment_status', 'paid')
            ->where('booking_date', '>=', now()->toDateString())->get();

        return view('user.booking.create_membership', compact('activeMembership', 'confirmedBookings'));
    }
    public function confirm(Request $request)
    {

        $bookingDate = Carbon::parse($request->input('booking_date'));
        if ($bookingDate->isMonday()) {
            return redirect()->back()
                ->with('error', 'ขออภัย สนามปิดให้บริการทุกวันจันทร์')
                ->withInput();
        }

        $bookingType = $request->input('booking_type');
        if ($bookingType === 'hourly' || $bookingType === 'membership') {
            $startTime    = $request->input('start_time');
            $endTime      = $request->input('end_time');
            $boundaryTime = '18:00:00';

            // เช็คว่าเวลาเริ่มต้นอยู่ก่อน 18:00 และเวลาสิ้นสุดอยู่หลัง 18:00 หรือไม่
            if ($startTime < $boundaryTime && $endTime > $boundaryTime) {
                return redirect()->back()
                    ->with('error', 'ไม่สามารถจองคร่อมช่วงเวลา 18:00 น. ได้ กรุณาแยกทำ 2 รายการจอง')
                    ->withInput();
            }
        }

        $bookingType = $request->input('booking_type');
        $summary     = ['booking_inputs' => $request->all()];

        try {
            if ($bookingType === 'hourly' || $bookingType === 'membership') {

                $validated = $request->validate([
                    'field_type_id' => 'required|exists:field_types,id',
                    'booking_date'  => 'required|date',
                    'start_time'    => 'required|date_format:H:i',
                    'end_time'      => 'required|date_format:H:i|after:start_time',
                ]);

                $isBooked = Booking::where('field_type_id', $validated['field_type_id'])
                    ->where('booking_date', $validated['booking_date'])
                    ->where('payment_status', 'paid')
                    ->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time'])
                    ->exists();

                if ($isBooked) {
                    return redirect()->back()
                        ->with('error', 'ช่วงเวลาที่ท่านเลือกมีผู้จองแล้ว กรุณาเลือกเวลาใหม่')
                        ->withInput();
                }
            }

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

        return view('user.booking.confirm', compact('summary'));
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

        // =================== END: ส่วนที่เพิ่มเข้ามาใหม่ ===================

        // 4. ส่งข้อมูลสรุปกลับไป (เพิ่ม Key ใหม่เข้าไป)
        return [
            'title'                   => 'สรุปการจองรายชั่วโมง',
            'field_name'              => FieldType::find($fieldTypeId)->name,
            'booking_date'            => $bookingDate,
            'time_range'              => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'duration_in_hours'       => $durationInHours,
            'subtotal_price'          => $totalPrice,     // <-- เพิ่มใหม่: ราคาเต็มก่อนลด
            'discount_amount'         => $discountAmount, // <-- เพิ่มใหม่: ยอดส่วนลด
            'discount_reason'         => $discountReason, // <-- เพิ่มใหม่: เหตุผลของส่วนลด
            'total_price'             => $finalPrice,     // <-- อัปเดต: เป็นราคาสุทธิหลังลด
            'special_perks'           => null,
            'price_breakdown_details' => $priceBreakdown,
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

        // กำหนดเวลาเริ่มต้น-สิ้นสุดพื้นฐาน
        $startTime = Carbon::parse($rate->base_start_time);
        $endTime   = Carbon::parse($rate->base_end_time);

        if ($request->has('wants_overtime') && $request->input('wants_overtime') == '1') {
            $overtimeEndTime = Carbon::parse($request->input('overtime_end_time'));

            if ($overtimeEndTime->gt(Carbon::parse($rate->overtime_max_end_time))) {
                abort(400, 'ไม่สามารถจองล่วงเวลาเกิน ' . $rate->overtime_max_end_time);
            }

            $overtimeHours   = $startTime->diffInHours($overtimeEndTime) - 10; // หากเวลาพื้นฐานคือ 10 ชม. (8:00-18:00)
            $overtimeCost    = $overtimeHours * $rate->overtime_price_per_hour_per_field;
            $overtimeDetails = "{$overtimeHours} ชั่วโมง (ถึง {$overtimeEndTime->format('H:i')} น.)";

            // อัปเดตเวลาสิ้นสุดเป็นเวลาล่วงเวลา
            $endTime = $overtimeEndTime;
        }

        $totalPrice = $basePrice + $overtimeCost;

        // ================== ส่วนที่เพิ่มเข้ามาใหม่ ==================
        // คำนวณจำนวนชั่วโมงทั้งหมดจากเวลาเริ่มต้นและสิ้นสุดสุดท้าย
        $durationInHours = $startTime->diffInHours($endTime);
        // =======================================================

        $depositAmount   = $totalPrice * 0.5;
        $securityDeposit = 2000.00;

        return [
            'title'             => "สรุปการจองแบบเหมาวัน ({$rentalType})",
            'package_name'      => $packageName,
            'booking_date'      => $bookingDate,
            'time_range'        => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'base_price'        => $basePrice,
            'overtime_cost'     => $overtimeCost,
            'overtime_details'  => $overtimeDetails,
            'total_price'       => $totalPrice,
            'duration_in_hours' => $durationInHours, // <-- เพิ่ม Key นี้เข้ามาใน return array
            'deposit_amount'    => $depositAmount,
            'security_deposit'  => $securityDeposit,
            'special_perks'     => null,
        ];
    }
    private function calculateMembershipUsage(Request $request)
    {
        // หมายเหตุ: Logic ส่วนนี้เป็นการจำลอง เพราะยังไม่มีระบบว่า User ถือบัตรสมาชิกใบไหน
        // ในระบบจริง คุณต้อง Query หาบัตรสมาชิกที่ Active ของ User ที่ Login อยู่
        $bookingDate = Carbon::parse($request->input('booking_date'));
        $tier        = MembershipTier::where('tier_name', 'VIP 20 ชม.')->first(); // สมมติว่าผู้ใช้เป็น VIP

        $startTime = Carbon::parse($request->input('start_time'));
        $endTime   = Carbon::parse($request->input('end_time'));

        $hoursToDeduct = 0;
        for ($time = $startTime->copy(); $time < $endTime; $time->addHour()) {
            if ($time->hour >= 18) { // เวลา 18:00 เป็นต้นไปคือ Overtime
                $hoursToDeduct += $tier->overtime_hour_multiplier;
            } else {
                $hoursToDeduct += 1;
            }
        }

        return [
            'title'           => 'สรุปการใช้สิทธิ์บัตรสมาชิก',
            'field_name'      => FieldType::find($request->input('field_type_id'))->name,
            'booking_date'    => $bookingDate,
            'time_range'      => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'hours_to_deduct' => $hoursToDeduct,
            'total_price'     => 0, // การใช้บัตรสมาชิกไม่มีค่าใช้จ่าย แต่เป็นการหักชั่วโมง
            'special_perks'   => $tier->special_perks,
        ];

        // if ($membership->remaining_hours < $hoursToDeduct) {
        //      throw new Exception('ชั่วโมงในบัตรสมาชิกของคุณไม่เพียงพอ (ต้องการ ' . $hoursToDeduct . ' ชม. แต่เหลือ ' . $membership->remaining_hours . ' ชม.)');
        // }
    }

    private function getThaiDayOfWeek(Carbon $date)
    {
        $days = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        return $days[$date->dayOfWeek];
    }

    public function store(Request $request)
    {
        try {
            // ใช้ Transaction เพื่อให้แน่ใจว่าถ้ามีขั้นตอนไหนพลาด จะยกเลิกทั้งหมด
            $booking = DB::transaction(function () use ($request) {
                // 1. เตรียมข้อมูลที่จะบันทึก
                $dataToSave = [
                    'user_id'            => Auth::id(),
                    'booking_type'       => $request->input('booking_type'),
                    'booking_date'       => $request->input('booking_date'),
                    'notes'              => $request->input('notes'),
                    'base_price'         => $request->input('base_price', 0),
                    'overtime_charges'   => $request->input('overtime_charges', 0),
                    'discount'           => $request->input('discount', 0),
                    'total_price'        => $request->input('total_price'),
                    'duration_in_hours'  => $request->input('duration_in_hours', 0),
                    'hours_deducted'     => $request->input('hours_deducted'),
                    'user_membership_id' => $request->input('user_membership_id'),
                    'status'             => 'confirmed',
                    'payment_status'     => 'unpaid',
                ];

                if ($request->input('booking_type') === 'hourly' || $request->input('booking_type') === 'membership') {
                    $dataToSave['field_type_id'] = $request->input('field_type_id');
                    $dataToSave['start_time']    = $request->input('start_time');
                    $dataToSave['end_time']      = $request->input('end_time');
                } elseif ($request->input('booking_type') === 'daily_package') {
                    $packageRate = PackageRate::where('package_name', $request->input('package_name'))
                        ->where('rental_type', $request->input('rental_type'))
                        ->firstOrFail();
                    $dataToSave['start_time'] = $packageRate->base_start_time;
                    $dataToSave['end_time']   = $request->has('wants_overtime') ? $request->input('overtime_end_time') : $packageRate->base_end_time;
                    if ($request->input('package_name') !== 'เหมา 2 สนาม') {
                        $fieldType                   = FieldType::where('name', $request->input('package_name'))->first();
                        $dataToSave['field_type_id'] = $fieldType->id ?? null;
                    } else {
                        $dataToSave['field_type_id'] = null;
                    }
                    $dataToSave['price_calculation_details'] = ['rental_type' => $request->input('rental_type'), 'package_name' => $request->input('package_name')];
                }

                // 2. สร้างการจอง
                $booking = Booking::create($dataToSave);

                // 3. สร้าง Booking Code
                $booking->booking_code = now()->format('ymd') . '-' . $booking->id;
                $booking->save();

                // 4. ถ้าเป็นการใช้บัตรสมาชิก ให้หักชั่วโมง
                if ($booking->booking_type === 'membership') {
                    $membership = UserMembership::find($booking->user_membership_id);
                    if ($membership) {
                        $membership->remaining_hours -= $booking->hours_deducted;
                        if ($membership->remaining_hours <= 0) {
                            $membership->status = 'used_up';
                        }
                        $membership->save();
                    }
                }
                return $booking;
            });

            // บรรทัดสุดท้าย: ส่งผู้ใช้ไปที่หน้า dashboard
            return redirect()->route('user.dashboard')->with('success', 'การจองของคุณสำเร็จแล้ว! รหัสการจองคือ ' . $booking->booking_code);

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกการจอง: ' . $e->getMessage());
        }
    }

    /**
     * รับไฟล์สลิปที่อัปโหลด, จัดเก็บ, และอัปเดตสถานะการจอง
     */
    public function uploadSlip(Request $request, $id)
    {
        // 1. ค้นหาการจองด้วยตัวเองโดยใช้ id ที่รับเข้ามา
        // findOrFail จะค้นหาข้อมูล ถ้าไม่เจอจะแสดงหน้า 404 โดยอัตโนมัติ
        $booking = Booking::findOrFail($id);

        // 2. ตรวจสอบสิทธิ์ (Authorization) - ยังคงเหมือนเดิม
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'คุณไม่มีสิทธิ์ในการดำเนินการนี้');
        }

        // 3. ตรวจสอบข้อมูล (Validation) - ยังคงเหมือนเดิม
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file        = $request->file('slip_image');
        $extension   = $file->getClientOriginalExtension();
        $newFilename = now()->format('Ymd') . $booking->booking_code . '.' . $extension;
        // 4. จัดเก็บไฟล์ - ยังคงเหมือนเดิม
        // บอกให้เก็บไฟล์ในโฟลเดอร์ 'slips' บน disk ที่ชื่อว่า 'public'
        $path = $file->storeAs('slips', $newFilename, 'public');

        // 5. อัปเดตฐานข้อมูล - ยังคงเหมือนเดิม
        $booking->update([
            'slip_image_path' => $path,
            'payment_status'  => 'verifying',
        ]);

        // 6. ส่งกลับไปหน้า Dashboard พร้อมข้อความแจ้งเตือน - ยังคงเหมือนเดิม
        return redirect()->route('user.dashboard')->with('success', 'อัปโหลดสลิปสำเร็จแล้ว รอการตรวจสอบจากเจ้าหน้าที่');
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
        $confirmedBookings = Booking::where('payment_status', 'paid')
            ->where('booking_date', '>=', now()->toDateString())
            ->with('fieldType')->orderBy('booking_date')->orderBy('start_time')->get();

        // 4. ส่งข้อมูลทั้งหมดไปที่ View ใหม่
        return view('user.booking.membership_create', compact('activeMembership', 'confirmedBookings'));
    }

}
