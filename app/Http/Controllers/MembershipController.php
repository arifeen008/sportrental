<?php
namespace App\Http\Controllers;

use App\Models\MembershipPurchase;
use App\Models\MembershipTier;
use App\Models\UserMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return redirect()->route('user.membership.purchase.show', $purchase);
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
        return view('membership.show_purchase', compact('purchase'));
    }

    /**
     * รับไฟล์สลิปสำหรับการซื้อบัตร
     */
    public function uploadSlip(Request $request, MembershipPurchase $purchase)
    {
        if ($purchase->user_id !== Auth::id()) {abort(403);}

        $request->validate(['slip_image' => 'required|image|max:2048']);

        $path = $request->file('slip_image')->store('membership_slips', 'public');

        $purchase->update([
            'slip_image_path' => $path,
            'status'          => 'verifying',
        ]);

        // อาจจะเพิ่มการแจ้งเตือน LINE ไปหา Admin ที่นี่

        return redirect()->route('dashboard')->with('success', 'แจ้งชำระเงินสำเร็จแล้ว รอการตรวจสอบและเปิดใช้งานบัตร');
    }
}
