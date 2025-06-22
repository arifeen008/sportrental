<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\FieldType;
use App\Models\HourlyRate;
use App\Models\MembershipTier;
use App\Models\PackageRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        return view('user.booking.create');
    }

    public function confirm(Request $request)
    {
        // ================== ส่วนที่เพิ่มเข้ามาใหม่ ==================
        // 1. ตรวจสอบสนามว่างก่อนคำนวณราคา

        // เราจะตรวจสอบเฉพาะการจองประเภทรายชั่วโมงและสมาชิกเท่านั้น
        if ($request->input('booking_type') === 'hourly' || $request->input('booking_type') === 'membership') {

            // ดึงข้อมูลที่จำเป็นสำหรับการตรวจสอบ
            $validated = $request->validate([
                'field_type_id' => 'required|exists:field_types,id',
                'booking_date'  => 'required|date',
                'start_time'    => 'required|date_format:H:i',
                'end_time'      => 'required|date_format:H:i|after:start_time',
            ]);

            // ค้นหาการจองที่ "จ่ายเงินแล้ว" และมีเวลาซ้อนทับกัน
            $isBooked = Booking::where('field_type_id', $validated['field_type_id'])
                ->where('booking_date', $validated['booking_date'])
                ->where('payment_status', 'paid') // เช็คเฉพาะการจองที่จ่ายเงินและอนุมัติแล้ว
                ->where('start_time', '<', $validated['end_time'])
                ->where('end_time', '>', $validated['start_time'])
                ->exists();

            // ถ้าเจอว่ามีคนจองแล้ว ให้ Redirect กลับไปหน้าเดิมพร้อม Error
            if ($isBooked) {
                return redirect()->back()
                    ->with('error', 'ช่วงเวลาที่ท่านเลือกมีผู้จองแล้ว กรุณาเลือกเวลาใหม่')
                    ->withInput(); // withInput() จะช่วยกรอกข้อมูลที่ผู้ใช้เลือกไว้กลับไปในฟอร์มเหมือนเดิม
            }
        }
        // ================== จบส่วนที่เพิ่มเข้ามาใหม่ ==================

        // --- ส่วนคำนวณราคา (โค้ดเดิม ไม่ต้องแก้ไข) ---
        $bookingType = $request->input('booking_type');
        $summary     = [
            'booking_inputs' => $request->all(),
        ];

        if ($bookingType === 'hourly') {
            $summary = array_merge($summary, $this->calculateHourlyRate($request));
        } elseif ($bookingType === 'daily_package') {
            $summary = array_merge($summary, $this->calculatePackageRate($request));
        } elseif ($bookingType === 'membership') {
            $summary = array_merge($summary, $this->calculateMembershipUsage($request));
        }

        return view('user.booking.confirm', compact('summary'));
    }

    // --- เมธอดสำหรับคำนวณแต่ละประเภท ---

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

        // --- 5. ส่งข้อมูลสรุปกลับไป ---
        return [
            'title'                   => 'สรุปการจองรายชั่วโมง',
            'field_name'              => FieldType::find($fieldTypeId)->name,
            'booking_date_formatted'  => $bookingDate->format('d/m/Y'),
            'time_range'              => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'duration_in_hours'       => $durationInHours,
            'price_breakdown_details' => $priceBreakdown, // ส่งรายละเอียดเป็น Array
            'total_price'             => $totalPrice,
            'special_perks'           => null,
        ];
    }

    private function calculatePackageRate(Request $request)
    {
        $packageName = $request->input('package_name');
        $rentalType  = $request->input('rental_type'); // <-- รับค่าใหม่จากฟอร์ม
        $bookingDate = Carbon::parse($request->input('booking_date'));

        // ใช้ $rentalType จากฟอร์มในการค้นหา ไม่ hardcode แล้ว
        $rate = PackageRate::where('package_name', $packageName)
            ->where('rental_type', $rentalType)
            ->first();

        if (! $rate) {
            abort(404, "ไม่พบเรทราคาสำหรับแพ็กเกจและประเภทงานที่เลือก");
        }

        $basePrice       = $rate->base_price;
        $overtimeCost    = 0;
        $overtimeDetails = 'ไม่มี';

        if ($request->has('wants_overtime') && $request->input('wants_overtime') == '1') {
            $overtimeEndTime = Carbon::parse($request->input('overtime_end_time'));
            $baseEndTime     = Carbon::parse($rate->base_end_time);

            // ตรวจสอบว่าเวลา overtime ไม่เกินเวลาสูงสุดที่กำหนด
            if ($overtimeEndTime->gt(Carbon::parse($rate->overtime_max_end_time))) {
                abort(400, 'ไม่สามารถจองล่วงเวลาเกิน ' . $rate->overtime_max_end_time);
            }

            $overtimeHours   = $baseEndTime->diffInHours($overtimeEndTime);
            $overtimeCost    = $overtimeHours * $rate->overtime_price_per_hour_per_field;
            $overtimeDetails = "{$overtimeHours} ชั่วโมง (ถึง {$overtimeEndTime->format('H:i')} น.)";
        }

        $totalPrice = $basePrice + $overtimeCost;

        // =================== คำนวณค่ามัดจำและเงินประกัน ===================
        $depositAmount   = $totalPrice * 0.5;
        $securityDeposit = 2000.00;
        // ===============================================================

        return [
            'title'                  => "สรุปการจองแบบเหมาวัน ({$rentalType})",
            'package_name'           => $packageName,
            'booking_date_formatted' => $bookingDate->format('d/m/Y'),
            'time_range'             => 'เต็มวัน (08:00 - 18:00)' . ($overtimeCost > 0 ? ' + ล่วงเวลา' : ''),
            'base_price'             => $basePrice,
            'overtime_cost'          => $overtimeCost,
            'overtime_details'       => $overtimeDetails,
            'total_price'            => $totalPrice,
            'special_perks'          => null,
            'deposit_amount'         => $depositAmount,   // <-- ส่งค่ามัดจำ
            'security_deposit'       => $securityDeposit, // <-- ส่งค่าเงินประกัน
        ];
    }
    private function calculateMembershipUsage(Request $request)
    {
                                                                               // หมายเหตุ: Logic ส่วนนี้เป็นการจำลอง เพราะยังไม่มีระบบว่า User ถือบัตรสมาชิกใบไหน
                                                                               // ในระบบจริง คุณต้อง Query หาบัตรสมาชิกที่ Active ของ User ที่ Login อยู่
        $tier = MembershipTier::where('tier_name', 'VIP 20 ชม.')->first(); // สมมติว่าผู้ใช้เป็น VIP

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
            'title'                  => 'สรุปการใช้สิทธิ์บัตรสมาชิก',
            'field_name'             => FieldType::find($request->input('field_type_id'))->name,
            'booking_date_formatted' => Carbon::parse($request->input('booking_date'))->format('d/m/Y'),
            'time_range'             => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
            'hours_to_deduct'        => $hoursToDeduct,
            'total_price'            => 0, // การใช้บัตรสมาชิกไม่มีค่าใช้จ่าย แต่เป็นการหักชั่วโมง
            'special_perks'          => $tier->special_perks,
        ];
    }

    private function getThaiDayOfWeek(Carbon $date)
    {
        $days = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        return $days[$date->dayOfWeek];
    }

    public function store(Request $request)
    {
        // 1. เตรียมข้อมูลพื้นฐานที่จะบันทึกเหมือนกันทุกประเภท
        $dataToSave = [
            'user_id'        => Auth::id() ?? 1, // ในระบบจริงใช้ auth()->id()
            'booking_type'   => $request->input('booking_type'),
            'booking_date'   => $request->input('booking_date'),
            'total_price'    => $request->input('total_price'),
            'notes'          => $request->input('notes'),
            'status'         => 'confirmed', // หรือ 'pending_payment'
            'payment_status' => 'unpaid',
        ];

        // 2. จัดการข้อมูลที่แตกต่างกันตาม booking_type
        $bookingType = $request->input('booking_type');

        if ($bookingType === 'hourly' || $bookingType === 'membership') {
            // สำหรับรายชั่วโมงและสมาชิก
            $dataToSave['field_type_id']  = $request->input('field_type_id');
            $dataToSave['start_time']     = $request->input('start_time');
            $dataToSave['end_time']       = $request->input('end_time');
            $dataToSave['hours_deducted'] = $request->input('hours_deducted', null);
        } elseif ($bookingType === 'daily_package') {
            // สำหรับเหมาวัน
            $packageRate = PackageRate::where('package_name', $request->input('package_name'))
                ->where('rental_type', $request->input('rental_type'))
                ->first();

            // กำหนดเวลาเริ่มต้น-สิ้นสุดเองตามกฎของแพ็กเกจ
            $dataToSave['start_time'] = $packageRate->base_start_time;
            $dataToSave['end_time']   = $packageRate->base_end_time;

            // ถ้ามีการจองล่วงเวลา ให้ใช้เวลาสิ้นสุดของล่วงเวลา
            if ($request->has('wants_overtime')) {
                $dataToSave['end_time'] = $request->input('overtime_end_time');
            }

            // กรณี 'เหมา 2 สนาม' จะไม่บันทึก field_type_id
            if ($request->input('package_name') !== 'เหมา 2 สนาม') {
                // ค้นหา field_type_id จากชื่อแพ็กเกจ (เช่น "สนามกลางแจ้ง")
                $fieldType                   = FieldType::where('name', $request->input('package_name'))->first();
                $dataToSave['field_type_id'] = $fieldType ? $fieldType->id : null;
            } else {
                $dataToSave['field_type_id'] = null;
            }

            // บันทึกรายละเอียดการคำนวณลง JSON
            $dataToSave['price_calculation_details'] = [
                'rental_type'  => $request->input('rental_type'),
                'package_name' => $request->input('package_name'),
            ];
        }

        // 3. สร้าง Booking ด้วยข้อมูลที่คัดกรองและเตรียมไว้แล้ว
        $booking = Booking::create($dataToSave);

        // 4. Redirect ไปหน้าต่อไป
        return redirect()->route('user.dashboard')->with('success', 'การจองของคุณสำเร็จแล้ว! รหัสการจองคือ #' . $booking->id);
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

        // 4. จัดเก็บไฟล์ - ยังคงเหมือนเดิม
        // บอกให้เก็บไฟล์ในโฟลเดอร์ 'slips' บน disk ที่ชื่อว่า 'public'
        $path = $request->file('slip_image')->store('slips', 'public');

        // 5. อัปเดตฐานข้อมูล - ยังคงเหมือนเดิม
        $booking->update([
            'slip_image_path' => $path,
            'payment_status'  => 'verifying',
        ]);

        // 6. ส่งกลับไปหน้า Dashboard พร้อมข้อความแจ้งเตือน - ยังคงเหมือนเดิม
        return redirect()->route('user.dashboard')->with('success', 'อัปโหลดสลิปสำเร็จแล้ว รอการตรวจสอบจากเจ้าหน้าที่');
    }

    /**
     * ตรวจสอบว่าช่วงเวลาที่ร้องขอมานั้นว่างหรือไม่
     */
    public function checkAvailability(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมา
        $validated = $request->validate([
            'field_type_id' => 'required|exists:field_types,id',
            'booking_date'  => 'required|date',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
        ]);

        // 2. ค้นหาการจองที่ "ซ้อนทับ" กับเวลาที่ขอมา
        // เงื่อนไขการซ้อนทับคือ: (เวลาเริ่มของเรา < เวลาสิ้นสุดของเขา) AND (เวลาสิ้นสุดของเรา > เวลาเริ่มของเขา)
        $isBooked = Booking::where('field_type_id', $validated['field_type_id'])
            ->where('booking_date', $validated['booking_date'])
        // เพิ่มเงื่อนไขป้องกันการจองที่เสร็จสิ้นหรือยกเลิกไปแล้ว (ถ้ามี)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists(); // ใช้ exists() เพื่อความเร็วสูงสุด แค่ต้องการรู้ว่ามีหรือไม่

        // 3. ส่งผลลัพธ์กลับไปเป็น JSON
        return response()->json([
            'available' => ! $isBooked, // ถ้าเจอ ($isBooked=true) แปลว่าไม่ว่าง (available=false)
        ]);
    }
}
