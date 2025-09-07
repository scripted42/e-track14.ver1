<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Leave;
use Carbon\Carbon;

class ComprehensiveAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating comprehensive attendance data...');
        
        // Clear existing data
        Attendance::truncate();
        Leave::truncate();
        
        // Get all staff users
        $staffUsers = User::whereIn('role_id', [2, 3, 4, 5])->get();
        
        if ($staffUsers->isEmpty()) {
            $this->command->info('No staff users found. Please run UserSeeder first.');
            return;
        }
        
        // Create data for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->subDays($i);
            $this->createDayData($date, $staffUsers);
        }
        
        $this->command->info('Comprehensive attendance data created successfully!');
    }
    
    private function createDayData($date, $staffUsers)
    {
        // Skip weekends
        if ($date->isWeekend()) {
            return;
        }
        
        foreach ($staffUsers as $user) {
            // 90% chance of attendance
            if (rand(1, 100) <= 90) {
                // Check-in time (6:30 AM - 8:30 AM)
                $checkInHour = rand(6, 8);
                $checkInMinute = rand(30, 59);
                $checkInTime = $date->copy()->setTime($checkInHour, $checkInMinute, 0);
                
                // Determine if late
                $isLate = $checkInTime->hour > 7;
                $status = $isLate ? 'terlambat' : 'hadir';
                
                // Create check-in record
                Attendance::create([
                    'user_id' => $user->id,
                    'timestamp' => $checkInTime,
                    'type' => 'checkin',
                    'latitude' => -7.250445 + (rand(-100, 100) / 10000),
                    'longitude' => 112.768845 + (rand(-100, 100) / 10000),
                    'accuracy' => rand(3, 8),
                    'status' => $status,
                    'synced' => true
                ]);
                
                // Check-out time (2:00 PM - 5:00 PM)
                $checkOutHour = rand(14, 17);
                $checkOutMinute = rand(0, 59);
                $checkOutTime = $date->copy()->setTime($checkOutHour, $checkOutMinute, 0);
                
                // Create check-out record
                Attendance::create([
                    'user_id' => $user->id,
                    'timestamp' => $checkOutTime,
                    'type' => 'checkout',
                    'latitude' => -7.250445 + (rand(-100, 100) / 10000),
                    'longitude' => 112.768845 + (rand(-100, 100) / 10000),
                    'accuracy' => rand(3, 8),
                    'status' => 'hadir',
                    'synced' => true
                ]);
            } else {
                // 10% chance of leave
                $this->createLeaveForUser($user, $date);
            }
        }
        
        // Create some random leave requests
        if (rand(1, 100) <= 20) { // 20% chance per day
            $randomUser = $staffUsers->random();
            $this->createLeaveForUser($randomUser, $date);
        }
    }
    
    private function createLeaveForUser($user, $date)
    {
        $leaveTypes = ['sakit', 'izin', 'cuti', 'dinas_luar'];
        $reasons = [
            'Sakit demam',
            'Keperluan keluarga',
            'Cuti tahunan',
            'Izin ke dokter',
            'Acara keluarga',
            'Keperluan pribadi',
            'Izin ke bank',
            'Keperluan administrasi',
            'Izin ke notaris',
            'Keperluan mendesak'
        ];
        
        $leaveType = $leaveTypes[array_rand($leaveTypes)];
        $status = rand(1, 100) <= 80 ? 'disetujui' : 'menunggu'; // 80% approved
        
        $leave = Leave::create([
            'user_id' => $user->id,
            'leave_type' => $leaveType,
            'start_date' => $date,
            'end_date' => $date,
            'reason' => $reasons[array_rand($reasons)],
            'status' => $status,
            'created_at' => $date->copy()->setTime(7, 0, 0)
        ]);
        
        if ($status === 'disetujui') {
            $leave->update([
                'approved_by' => 1, // Admin
                'approved_at' => $date->copy()->setTime(8, 0, 0)
            ]);
        }
    }
}

