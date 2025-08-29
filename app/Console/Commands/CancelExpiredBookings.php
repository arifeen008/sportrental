<?php
namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels bookings that have expired payment deadlines (status: pending_payment).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Start a database transaction to ensure data integrity.
        DB::beginTransaction();

        try {
            // Find and count the number of expired bookings.
            $count = Booking::where('status', 'pending_payment')
                ->where('expires_at', '<', now())
                ->count();

            // If there are expired bookings, update their status.
            if ($count > 0) {
                Booking::where('status', 'pending_payment')
                    ->where('expires_at', '<', now())
                    ->update(['status' => 'cancelled']);

                $this->info("Successfully cancelled {$count} expired bookings.");
            } else {
                $this->info('No expired bookings to cancel.');
            }

            // Commit the transaction if everything is successful.
            DB::commit();

            return 0; // Return 0 for success.

        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs.
            DB::rollBack();
            $this->error('Failed to cancel expired bookings: ' . $e->getMessage());
            return 1; // Return 1 for failure.
        }
    }
}
