<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPurchase;
use App\Models\UserMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class MembershipPurchaseController extends Controller
{
    /**
     * แสดงหน้ารายการสั่งซื้อบัตรสมาชิกทั้งหมด
     */
    public function index()
    {
        $purchases = MembershipPurchase::with(['user', 'membershipTier'])
            ->orderByRaw("FIELD(status, 'verifying', 'pending_payment', 'completed', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.purchases.index', compact('purchases'));
    }

    /**
     * อนุมัติการสั่งซื้อและสร้างบัตรสมาชิกให้ผู้ใช้
     */
    public function approve(MembershipPurchase $purchase)
    {
        // ใช้ Transaction เพื่อความปลอดภัย
        DB::transaction(function () use ($purchase) {
            // 1. อัปเดตสถานะใบสั่งซื้อเป็น 'completed'
            $purchase->update([
                'status'      => 'completed',
                'approved_by' => Auth::id(),
            ]);

            // 2. สร้างบัตรสมาชิกใหม่ให้ผู้ใช้
            UserMembership::create([
                'user_id'            => $purchase->user_id,
                'membership_tier_id' => $purchase->membership_tier_id,
                'card_number'        => 'MEM-' . strtoupper(Str::random(8)),
                'initial_hours'      => $purchase->membershipTier->included_hours,
                'remaining_hours'    => $purchase->membershipTier->included_hours,
                'activated_at'       => now(),
                'expires_at'         => now()->addDays($purchase->membershipTier->validity_days),
                'status'             => 'active',
            ]);
        });

        // อาจจะเพิ่มการแจ้งเตือน LINE หรือ Email ไปหาลูกค้าตรงนี้

        return redirect()->route('admin.purchases.index')->with('success', 'อนุมัติการซื้อบัตรและเปิดใช้งานบัตรให้ผู้ใช้เรียบร้อยแล้ว');
    }

    /**
     * ปฏิเสธการสั่งซื้อ
     */
    public function reject(Request $request, MembershipPurchase $purchase)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $purchase->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return redirect()->route('admin.purchases.index')->with('success', 'ปฏิเสธรายการสั่งซื้อเรียบร้อยแล้ว');
    }
}
