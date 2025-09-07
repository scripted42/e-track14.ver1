<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Leave;
use Carbon\Carbon;

class TodayAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        
        // Get all staff users (role_id 2,3,4,5)
        $staffUsers = User::whereIn('role_id', [2, 3, 4, 5])->get();
        
        if ($staffUsers->isEmpty()) {
            $this->command->info('No staff users found. Please run UserSeeder first.');
            return;
        }
        
        $this->command->info('Creating attendance data for ' . $today->format('Y-m-d'));
        
        // Clear existing attendance data for today
        Attendance::whereDate('timestamp', $today)->delete();
        
        // Create attendance records for each staff
        foreach ($staffUsers as $user) {
            // Random check-in time between 6:30 AM and 8:30 AM
            $checkInHour = rand(6, 8);
            $checkInMinute = rand(30, 59);
            $checkInTime = $today->copy()->setTime($checkInHour, $checkInMinute, 0);
            
            // Create check-in record
            Attendance::create([
                'user_id' => $user->id,
                'timestamp' => $checkInTime,
                'type' => 'checkin',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'accuracy' => 5.0,
                'status' => $checkInTime->hour > 7 ? 'terlambat' : 'hadir',
                'synced' => true
            ]);
            
            // Random check-out time between 2:00 PM and 5:00 PM
            $checkOutHour = rand(14, 17);
            $checkOutMinute = rand(0, 59);
            $checkOutTime = $today->copy()->setTime($checkOutHour, $checkOutMinute, 0);
            
            // Create check-out record
            Attendance::create([
                'user_id' => $user->id,
                'timestamp' => $checkOutTime,
                'type' => 'checkout',
                'latitude' => -7.250445,
                'longitude' => 112.768845,
                'accuracy' => 5.0,
                'status' => 'hadir',
                'synced' => true
            ]);
        }
        
        // Create some leave requests for today
        $this->createLeaveRequests($today);
        
        $this->command->info('Attendance data created successfully for ' . $staffUsers->count() . ' staff members.');
    }
    
    private function createLeaveRequests($today)
    {
        // Get some random staff users
        $staffUsers = User::whereIn('role_id', [2, 3, 4, 5])->inRandomOrder()->limit(3)->get();
        
        $leaveTypes = ['sakit', 'izin', 'cuti', 'dinas_luar'];
        $reasons = [
            'Sakit demam',
            'Keperluan keluarga',
            'Cuti tahunan',
            'Izin ke dokter',
            'Acara keluarga',
            'Keperluan pribadi'
        ];
        
        foreach ($staffUsers as $user) {
            // Create approved leave for today
            Leave::create([
                'user_id' => $user->id,
                'leave_type' => $leaveTypes[array_rand($leaveTypes)],
                'start_date' => $today,
                'end_date' => $today,
                'reason' => $reasons[array_rand($reasons)],
                'status' => 'disetujui',
                'approved_by' => 1, // Admin
                'approved_at' => $today->copy()->setTime(8, 0, 0),
                'created_at' => $today->copy()->setTime(7, 0, 0)
            ]);
        }
        
        // Create one pending leave request
        $pendingUser = User::whereIn('role_id', [2, 3, 4, 5])->inRandomOrder()->first();
        if ($pendingUser) {
            Leave::create([
                'user_id' => $pendingUser->id,
                'leave_type' => 'izin',
                'start_date' => $today->copy()->addDay(),
                'end_date' => $today->copy()->addDay(),
                'reason' => 'Keperluan mendesak',
                'status' => 'menunggu',
                'created_at' => $today->copy()->setTime(9, 0, 0)
            ]);
        }
    }
}