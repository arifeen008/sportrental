<?php
namespace App\Http\Controllers;

use App\Models\MembershipPurchase;
use App\Models\MembershipTier;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    /**
     * แสดงหน้าสำหรับเลือกซื้อบัตรสมาชิก
     */
    public function index()
    {
        $tiers = MembershipTier::all();
        return view('user.membership.purchase', compact('tiers'));
    }

    /**
     * สร้าง "ใบสั่งซื้อ" สำหรับบัตรสมาชิก และ Redirect ไปหน้าชำระเงิน
     */
    public function store(Request $request)
    {
        $request->validate(['membership_tier_id' => 'required|exists:membership_tiers,id']);

        // ตรวจสอบว่ามีใบสั่งซื้อที่ยังไม่จ่ายเงิน หรือมีบัตรที่ยังใช้งานได้อยู่หรือไม่
        $hasPending = MembershipPurchase::where('user_id', Auth::id())->whereIn('status', ['pending_payment', 'verifying'])->exists();
        $hasActive  = UserMembership::where('user_id', Auth::id())->where('status', 'active')->where('expires_at', '>', now())->exists();

        if ($hasPending || $hasActive) {
            return redirect()->route('user.dashboard')->with('error', 'คุณมีรายการสั่งซื้อที่รอดำเนินการ หรือมีบัตรที่ยังใช้งานได้อยู่แล้ว');
        }

        $tier = MembershipTier::findOrFail($request->membership_tier_id);

        // สร้างใบสั่งซื้อใหม่
        $purchase = MembershipPurchase::create([
            'user_id'            => Auth::id(),
            'membership_tier_id' => $tier->id,
            'price'              => $tier->price,
            'purchase_code'      => 'P' . now()->format('ymd') . rand(1000, 9999),
            'status'             => 'pending_payment',
        ]);

        // ส่งผู้ใช้ไปที่หน้ารายละเอียดการสั่งซื้อเพื่อชำระเงิน
        // ส่งผู้ใช้ไปที่หน้ารายละเอียดการสั่งซื้อเพื่อชำระเงิน
        return redirect()->route('user.purchase.show', $purchase);
    }

    /**
     * แสดงหน้ารายละเอียดการสั่งซื้อเพื่อให้ผู้ใช้อัปโหลดสลิป
     */
    public function show(MembershipPurchase $purchase)
    {
        // ตรวจสอบว่าเป็นเจ้าของใบสั่งซื้อจริง
        if ($purchase->user_id !== Auth::id()) {
            abort(403);
        }

        $purchase->load(['user', 'membershipTier']);
        return view('user.membership.show_purchase', compact('purchase'));
    }

    /**
     * รับไฟล์สลิปสำหรับการซื้อบัตร, เปลี่ยนชื่อ, จัดเก็บ, และอัปเดตสถานะ
     */
    public function uploadSlip(Request $request, MembershipPurchase $purchase)
    {
        // 1. ตรวจสอบสิทธิ์ (เหมือนเดิม)
        if ($purchase->user_id !== Auth::id()) {
            abort(403);
        }

        // 2. ตรวจสอบข้อมูล (เหมือนเดิม)
        $request->validate([
            'slip_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ================== START: ส่วนที่แก้ไขใหม่ ==================

        // 3. สร้างชื่อไฟล์ใหม่ตามที่คุณต้องการ
        $file      = $request->file('slip_image');
        $extension = $file->getClientOriginalExtension(); // ดึงนามสกุลไฟล์เดิม (เช่น .jpg, .png)

        // สร้างชื่อไฟล์จาก ปีเดือนวัน_รหัสการซื้อ.นามสกุล
        $newFilename = now()->format('YmdHi') . '_purchase_' . $purchase->purchase_code . '.' . $extension;

        // 4. บันทึกไฟล์ด้วยชื่อใหม่ (ใช้ storeAs)
        // เก็บไฟล์ในโฟลเดอร์ 'membership_slips' บน disk ที่ชื่อว่า 'public'
        $path = $file->storeAs('membership_slips', $newFilename, 'public');

        // =================== END: ส่วนที่แก้ไขใหม่ ===================

        // 5. อัปเดตฐานข้อมูล (ตอนนี้จะเก็บ path ที่มีชื่อไฟล์ใหม่ของเรา)
        $purchase->update([
            'slip_image_path' => $path,
            'status'          => 'verifying',
        ]);

        // 6. (ถ้ามี) แจ้งเตือน LINE ไปหา Admin
        $this->sendPurchaseNotification($purchase);

        // 7. ส่งกลับไปหน้า Dashboard (เหมือนเดิม)
        return redirect()->route('user.dashboard')->with('success', 'แจ้งชำระเงินสำเร็จแล้ว รอการตรวจสอบและเปิดใช้งานบัตร');
    }

    private function sendPurchaseNotification($purchase)
    {
        $accessToken = 'UUuw3veqOqlr4y5kjaXM27jrs/qQHkqhtX2vFUmDwAXOzk1ixPyRjSsRH/6y/tBk8Z0rPSdCm061R/KNq0PORlLxqNaYhOb7u5AMpzszzIGET7G/3spPDBxIiMYlM/fdAzUksR9yZcWIhak5RVG3PQdB04t89/1O/w1cDnyilFU=';
        $groupId     = 'C8828a7ce6dd1f2f1d9ad3638489c6e9d';
        $purchase->load(['user', 'membershipTier']);

        $textMessage = "💳 มีการแจ้งชำระเงินค่าบัตรสมาชิก!\n" .
        "--------------------\n" .
        "รหัสสั่งซื้อ: {$purchase->purchase_code}\n" .
        "ชื่อผู้ซื้อ: {$purchase->user->name}\n" .
        "ประเภทบัตร: {$purchase->membershipTier->tier_name}\n" .
        "ราคา: " . number_format($purchase->price, 2) . " บาท\n" .
            "--------------------\n" .
            "กรุณาตรวจสอบสลิปและอนุมัติในหน้า Admin";
        $body = [
            'to'       => $groupId,
            'messages' => [['type' => 'text', 'text' => $textMessage]],
        ];
        // เรียก API
        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.line.me/v2/bot/message/push', $body);

            if (! $response->successful()) {
                Log::error('LINE Push Failed', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('LINE Push Exception', ['error' => $e->getMessage()]);
        }

    }
}
