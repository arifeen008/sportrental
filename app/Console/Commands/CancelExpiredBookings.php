<?php
namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * ชื่อและลายเซ็นของ command
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * คำอธิบายของ command
     */
    protected $description = 'ยกเลิกการจองที่หมดเวลาชำระเงินแล้ว (สถานะ pending_payment)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ค้นหาการจองที่สถานะเป็น 'pending_payment' และหมดเวลาแล้ว
        $expiredBookings = Booking::where('status', 'pending_payment')
            ->where('expires_at', '<', now())
            ->get();

        $count = $expiredBookings->count();
        if ($count > 0) {
            foreach ($expiredBookings as $booking) {
                // เปลี่ยนสถานะเป็น 'cancelled'
                $booking->status = 'cancelled';
                $booking->save();
            }
            $this->info("Cancelled {$count} expired bookings.");
        } else {
            $this->info('No expired bookings to cancel.');
        }

        return 0;
    }
}
