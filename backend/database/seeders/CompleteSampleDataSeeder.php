<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\AttendanceQr;

class CompleteSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating complete sample data...');
        
        // Clear existing data
        $this->clearData();
        
        // Create class rooms
        $classRooms = $this->createClassRooms();
        
        // Create users
        $users = $this->createUsers($classRooms);
        
        // Create students
        $students = $this->createStudents($classRooms);
        
        // Create attendance
        $this->createAttendance($users);
        
        // Create student attendance
        $this->createStudentAttendance($students, $users);
        
        // Create leaves
        $this->createLeaves($users);
        
        // Create QR codes
        $this->createQRCodes();
        
        $this->command->info('Complete sample data created successfully!');
        $this->command->info('Summary:');
        $this->command->info('- Class Rooms: ' . count($classRooms));
        $this->command->info('- Students: ' . count($students));
        $this->command->info('- Gurus: 10');
        $this->command->info('- Pegawais: 10');
        $this->command->info('- Kepala Sekolah: 1');
        $this->command->info('- Waka Kurikulum: 1');
        $this->command->info('- Leave requests: ' . \App\Models\Leave::count());
    }
    
    private function clearData()
    {
        $this->command->info('Clearing existing data...');
        
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        StudentAttendance::truncate();
        Attendance::truncate();
        Leave::truncate();
        Student::truncate();
        ClassRoom::truncate();
        User::where('id', '>', 1)->delete();
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    private function createClassRooms()
    {
        $this->command->info('Creating class rooms...');
        
        $classRooms = [];
        $levels = ['7', '8', '9'];
        $classes = ['A', 'B', 'C'];
        
        foreach ($levels as $level) {
            foreach ($classes as $class) {
                $classRooms[] = ClassRoom::create([
                    'name' => $level . $class,
                    'level' => $level,
                    'description' => 'Kelas ' . $level . $class,
                    'is_active' => true,
                ]);
            }
        }
        
        return $classRooms;
    }
    
    private function createUsers($classRooms)
    {
        $this->command->info('Creating users...');
        
        $guruRole = Role::where('role_name', 'Guru')->first();
        $pegawaiRole = Role::where('role_name', 'Pegawai')->first();
        $kepalaSekolahRole = Role::where('role_name', 'Kepala Sekolah')->first();
        $wakaKurikulumRole = Role::where('role_name', 'Waka Kurikulum')->first();
        
        // Kepala Sekolah
        $kepalaSekolah = User::create([
            'name' => 'Dr. Ahmad Wijaya, M.Pd',
            'email' => 'kepsek@smpn14.sch.id',
            'password' => Hash::make('kepsek123'),
            'role_id' => $kepalaSekolahRole->id,
            'nip_nik' => '196512151990031001',
        ]);
        
        // Waka Kurikulum
        $wakaKurikulum = User::create([
            'name' => 'Siti Nurhaliza, S.Pd',
            'email' => 'waka@smpn14.sch.id',
            'password' => Hash::make('waka123'),
            'role_id' => $wakaKurikulumRole->id,
            'nip_nik' => '197203151995032002',
        ]);
        
        // 10 Guru
        $guruNames = [
            'Budi Santoso, S.Pd', 'Sari Indah, S.Pd', 'Ahmad Fauzi, S.Pd',
            'Dewi Kartika, S.Pd', 'Rudi Hartono, S.Pd', 'Maya Sari, S.Pd',
            'Eko Prasetyo, S.Pd', 'Lina Marlina, S.Pd', 'Agus Supriyanto, S.Pd',
            'Rina Wulandari, S.Pd'
        ];
        
        $gurus = [];
        foreach ($guruNames as $index => $name) {
            $guru = User::create([
                'name' => $name,
                'email' => 'guru' . ($index + 1) . '@smpn14.sch.id',
                'password' => Hash::make('guru123'),
                'role_id' => $guruRole->id,
                'nip_nik' => '198' . str_pad($index + 1, 13, '0', STR_PAD_LEFT),
            ]);
            $gurus[] = $guru;
        }
        
        // Assign walikelas
        foreach ($classRooms as $index => $classRoom) {
            if (isset($gurus[$index])) {
                $classRoom->update(['walikelas_id' => $gurus[$index]->id]);
            }
        }
        
        // 10 Pegawai
        $pegawaiNames = [
            'Siti Aminah', 'Bambang Sutrisno', 'Rina Sari', 'Dedi Kurniawan',
            'Maya Indah', 'Joko Susilo', 'Lina Marlina', 'Agus Wijaya',
            'Sari Dewi', 'Eko Prasetyo'
        ];
        
        $pegawais = [];
        foreach ($pegawaiNames as $index => $name) {
            $pegawai = User::create([
                'name' => $name,
                'email' => 'pegawai' . ($index + 1) . '@smpn14.sch.id',
                'password' => Hash::make('pegawai123'),
                'role_id' => $pegawaiRole->id,
                'nip_nik' => '199' . str_pad($index + 1, 13, '0', STR_PAD_LEFT),
            ]);
            $pegawais[] = $pegawai;
        }
        
        return [
            'kepalaSekolah' => $kepalaSekolah,
            'wakaKurikulum' => $wakaKurikulum,
            'gurus' => $gurus,
            'pegawais' => $pegawais
        ];
    }
    
    private function createStudents($classRooms)
    {
        $this->command->info('Creating students...');
        
        $students = [];
        $studentNames = [
            'Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Kartika', 'Eko Prasetyo',
            'Maya Sari', 'Rudi Hartono', 'Lina Marlina', 'Agus Supriyanto', 'Rina Wulandari',
            'Fajar Nugroho', 'Sari Indah', 'Dedi Kurniawan', 'Maya Indah', 'Joko Susilo',
            'Lina Sari', 'Agus Wijaya', 'Sari Dewi', 'Eko Prasetyo', 'Maya Kartika',
            'Rudi Santoso', 'Lina Indah', 'Agus Hartono', 'Sari Marlina', 'Eko Supriyanto',
            'Maya Wulandari', 'Rudi Nugroho', 'Lina Indah', 'Agus Kurniawan', 'Sari Susilo'
        ];
        
        foreach ($classRooms as $classRoom) {
            for ($i = 0; $i < 10; $i++) {
                $studentIndex = ($classRoom->id - 1) * 10 + $i;
                if (isset($studentNames[$studentIndex])) {
                    $student = Student::create([
                        'nisn' => 'NISN' . str_pad($studentIndex + 1, 6, '0', STR_PAD_LEFT),
                        'name' => $studentNames[$studentIndex],
                        'class_name' => $classRoom->name,
                        'class_room_id' => $classRoom->id,
                        'card_qr_code' => 'STU' . str_pad($studentIndex + 1, 4, '0', STR_PAD_LEFT),
                        'address' => 'Alamat siswa ' . $studentNames[$studentIndex],
                        'status' => 'Aktif',
                    ]);
                    $students[] = $student;
                }
            }
        }
        
        return $students;
    }
    
    private function createAttendance($users)
    {
        $this->command->info('Creating attendance data...');
        
        $allUsers = collect($users['gurus'])
            ->merge($users['pegawais'])
            ->merge([$users['kepalaSekolah'], $users['wakaKurikulum']]);
        
        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            
            if ($date->isWeekend()) continue;
            
            foreach ($allUsers as $user) {
                $statuses = ['hadir', 'hadir', 'hadir', 'terlambat', 'hadir'];
                $status = $statuses[array_rand($statuses)];
                
                $checkinTime = $date->copy()->setTime(7, rand(0, 30), 0);
                if ($status === 'terlambat') {
                    $checkinTime = $date->copy()->setTime(7, rand(31, 60), 0);
                }
                
                $checkoutTime = $date->copy()->setTime(15, rand(0, 30), 0);
                
                Attendance::create([
                    'user_id' => $user->id,
                    'type' => 'checkin',
                    'latitude' => -6.200000 + (rand(-100, 100) / 10000),
                    'longitude' => 106.816666 + (rand(-100, 100) / 10000),
                    'accuracy' => rand(5, 15),
                    'status' => $status,
                    'timestamp' => $checkinTime,
                ]);
                
                Attendance::create([
                    'user_id' => $user->id,
                    'type' => 'checkout',
                    'latitude' => -6.200000 + (rand(-100, 100) / 10000),
                    'longitude' => 106.816666 + (rand(-100, 100) / 10000),
                    'accuracy' => rand(5, 15),
                    'status' => 'hadir',
                    'timestamp' => $checkoutTime,
                ]);
            }
        }
    }
    
    private function createStudentAttendance($students, $users)
    {
        $this->command->info('Creating student attendance...');
        
        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            
            if ($date->isWeekend()) continue;
            
            foreach ($students as $student) {
                $statuses = ['hadir', 'hadir', 'hadir', 'terlambat', 'hadir', 'tidak_hadir'];
                $status = $statuses[array_rand($statuses)];
                
                if ($status !== 'tidak_hadir') {
                    $attendanceTime = $date->copy()->setTime(7, rand(0, 30), 0);
                    if ($status === 'terlambat') {
                        $attendanceTime = $date->copy()->setTime(7, rand(31, 60), 0);
                    }
                    
                    // Get a random teacher for this attendance
                    $teacher = $users['gurus'][array_rand($users['gurus'])];
                    
                    StudentAttendance::create([
                        'student_id' => $student->id,
                        'teacher_id' => $teacher->id,
                        'status' => $status === 'terlambat' ? 'hadir' : $status, // Map terlambat to hadir
                    ]);
                }
            }
        }
    }
    
    private function createLeaves($users)
    {
        $this->command->info('Creating leave requests...');
        
        $leaveTypes = ['izin', 'sakit', 'cuti', 'dinas_luar'];
        $leaveStatuses = ['menunggu', 'disetujui', 'ditolak'];
        
        $allUsers = collect($users['gurus'])
            ->merge($users['pegawais'])
            ->merge([$users['kepalaSekolah'], $users['wakaKurikulum']]);
        
        $usersWithLeaves = $allUsers->random(8);
        
        foreach ($usersWithLeaves as $user) {
            $numLeaves = rand(1, 3);
            
            for ($i = 0; $i < $numLeaves; $i++) {
                $leaveType = $leaveTypes[array_rand($leaveTypes)];
                $status = $leaveStatuses[array_rand($leaveStatuses)];
                
                $startDate = Carbon::now()->subDays(rand(1, 30));
                $endDate = $startDate->copy()->addDays(rand(1, 3));
                
                $leave = Leave::create([
                    'user_id' => $user->id,
                    'leave_type' => $leaveType,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'reason' => $this->getLeaveReason($leaveType),
                    'status' => $status,
                ]);
                
                if ($status !== 'menunggu') {
                    $leave->update([
                        'approved_by' => $users['kepalaSekolah']->id,
                        'approved_at' => $startDate->copy()->subDays(rand(1, 5)),
                    ]);
                }
            }
        }
    }
    
    private function createQRCodes()
    {
        $this->command->info('Creating QR codes...');
        
        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            
            if (!$date->isWeekend()) {
                AttendanceQr::create([
                    'qr_code' => 'QR_' . $date->format('Ymd') . '_' . rand(1000, 9999),
                    'valid_until' => $date->copy()->setTime(16, 0, 0),
                ]);
            }
        }
    }
    
    private function getLeaveReason($leaveType)
    {
        $reasons = [
            'izin' => [
                'Urusan keluarga yang tidak bisa dihindari',
                'Menghadiri acara keluarga',
                'Keperluan pribadi yang mendesak'
            ],
            'sakit' => [
                'Demam tinggi dan tidak bisa beraktivitas',
                'Flu berat dan batuk',
                'Sakit kepala migrain'
            ],
            'cuti' => [
                'Cuti tahunan untuk refreshing',
                'Cuti untuk liburan keluarga',
                'Cuti untuk perawatan kesehatan'
            ],
            'dinas_luar' => [
                'Mengikuti pelatihan guru',
                'Rapat koordinasi dengan dinas pendidikan',
                'Workshop peningkatan kompetensi'
            ]
        ];
        
        $typeReasons = $reasons[$leaveType] ?? ['Keperluan pribadi'];
        return $typeReasons[array_rand($typeReasons)];
    }
}
