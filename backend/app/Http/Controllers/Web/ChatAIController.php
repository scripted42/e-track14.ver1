<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ChatAIController extends Controller
{
    public function __construct()
    {
        // No middleware needed here - handled in routes
    }

    public function index()
    {
        // Check role access
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Admin', $userRoles) && !in_array('Kepala Sekolah', $userRoles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman tersebut. Role Anda: ' . implode(', ', $userRoles));
        }
        
        return view('admin.chat-ai.index');
    }

    public function ask(Request $request)
    {
        // Check role access
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Admin', $userRoles) && !in_array('Kepala Sekolah', $userRoles)) {
            return response()->json([
                'success' => false,
                'answer' => 'Anda tidak memiliki izin untuk mengakses fitur ini. Role Anda: ' . implode(', ', $userRoles)
            ], 403);
        }
        
        $question = $request->input('question');
        
        if (empty($question)) {
            return response()->json([
                'success' => false,
                'answer' => 'Silakan masukkan pertanyaan Anda.'
            ]);
        }

        try {
            // Parse intent dari pertanyaan
            $intent = $this->parseIntent($question);
            
            // Ambil data berdasarkan intent
            $data = $this->getSchoolData($intent);
            
            // Generate response
            $response = $this->generateResponse($intent, $data, $question);
            
            return response()->json([
                'success' => true,
                'answer' => $response,
                'intent' => $intent,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'answer' => 'Maaf, terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi.'
            ]);
        }
    }

    private function parseIntent($question)
    {
        $question = strtolower($question);
        
        // Keywords untuk deteksi intent
        $intents = [
            'attendance_today' => ['absensi', 'hadir', 'kehadiran', 'hari ini', 'pegawai', 'staff'],
            'attendance_date' => ['absensi', 'hadir', 'kehadiran', 'pegawai', 'staff', 'tanggal', 'september', 'oktober', 'november', 'desember', 'januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus'],
            'students_count' => ['siswa', 'total siswa', 'jumlah siswa', 'berapa siswa'],
            'leaves_pending' => ['izin', 'cuti', 'menunggu', 'pending', 'pengajuan'],
            'late_employees' => ['terlambat', 'telat', 'pegawai terlambat'],
            'attendance_stats' => ['statistik', 'persentase', 'rate', 'kehadiran'],
            'reports' => ['laporan', 'report', 'export', 'download'],
            'general' => ['halo', 'hai', 'help', 'bantuan']
        ];
        
        foreach ($intents as $intent => $keywords) {
            $matchCount = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($question, $keyword)) {
                    $matchCount++;
                }
            }
            if ($matchCount >= 2) {
                return $intent;
            }
        }
        
        return 'general';
    }

    private function getSchoolData($intent)
    {
        switch ($intent) {
            case 'attendance_date':
                return $this->getAttendanceByDate();
                
            case 'attendance_today':
                $today = Attendance::whereDate('timestamp', today())
                    ->where('type', 'checkin')
                    ->count();
                
                $totalEmployees = User::whereHas('roles', function($query) {
                    $query->where('name', '!=', 'Siswa');
                })->count();
                
                return [
                    'today_attendance' => $today,
                    'total_employees' => $totalEmployees,
                    'not_attended' => $totalEmployees - $today,
                    'date' => today()->format('d F Y')
                ];
                
            case 'students_count':
                $totalStudents = Student::count();
                $presentToday = StudentAttendance::whereDate('date', today())
                    ->where('status', 'present')
                    ->count();
                
                return [
                    'total_students' => $totalStudents,
                    'present_today' => $presentToday,
                    'absent_today' => $totalStudents - $presentToday,
                    'date' => today()->format('d F Y')
                ];
                
            case 'leaves_pending':
                $pending = Leave::where('status', 'pending')->count();
                $today = Leave::whereDate('created_at', today())->count();
                $approved = Leave::where('status', 'disetujui')
                    ->whereDate('created_at', today())->count();
                
                return [
                    'pending' => $pending,
                    'today_submissions' => $today,
                    'today_approved' => $approved,
                    'date' => today()->format('d F Y')
                ];
                
            case 'late_employees':
                $settings = Setting::getSettings();
                $checkinEnd = Carbon::createFromFormat('H:i:s', 
                    strlen($settings->checkin_end ?? '07:05:00') === 5 ? 
                    (($settings->checkin_end ?? '07:05') . ':00') : 
                    ($settings->checkin_end ?? '07:05:00')
                );
                
                $lateToday = Attendance::whereDate('timestamp', today())
                    ->where('type', 'checkin')
                    ->whereTime('timestamp', '>', $checkinEnd->format('H:i:s'))
                    ->count();
                
                $onTimeToday = Attendance::whereDate('timestamp', today())
                    ->where('type', 'checkin')
                    ->whereTime('timestamp', '<=', $checkinEnd->format('H:i:s'))
                    ->count();
                
                return [
                    'late_today' => $lateToday,
                    'on_time_today' => $onTimeToday,
                    'checkin_end' => $checkinEnd->format('H:i'),
                    'date' => today()->format('d F Y')
                ];
                
            case 'attendance_stats':
                $thisWeek = Attendance::whereBetween('timestamp', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->where('type', 'checkin')->count();
                
                $thisMonth = Attendance::whereMonth('timestamp', Carbon::now()->month)
                    ->whereYear('timestamp', Carbon::now()->year)
                    ->where('type', 'checkin')->count();
                
                return [
                    'this_week' => $thisWeek,
                    'this_month' => $thisMonth,
                    'week_start' => Carbon::now()->startOfWeek()->format('d M'),
                    'week_end' => Carbon::now()->endOfWeek()->format('d M'),
                    'month' => Carbon::now()->format('F Y')
                ];
                
            default:
                return [];
        }
    }

    private function generateResponse($intent, $data, $question)
    {
        switch ($intent) {
            case 'attendance_date':
                if (isset($data['error'])) {
                    return "❌ **Error**\n\n" . $data['error'];
                }
                
                $response = "📊 **Data Kehadiran Pegawai**\n\n";
                $response .= "📅 **Tanggal:** {$data['date']}\n";
                $response .= "⏰ **Batas Waktu Check-in:** {$data['checkin_end']}\n\n";
                
                $response .= "📈 **Ringkasan:**\n";
                $response .= "• Total pegawai: **{$data['total_employees']}** orang\n";
                $response .= "• Hadir: **{$data['present_count']}** orang\n";
                $response .= "• Tidak hadir: **{$data['absent_count']}** orang\n";
                $response .= "• Tepat waktu: **{$data['on_time_count']}** orang\n";
                $response .= "• Terlambat: **{$data['late_count']}** orang\n\n";
                
                if ($data['on_time_employees']->count() > 0) {
                    $response .= "✅ **Pegawai Tepat Waktu:**\n";
                    foreach ($data['on_time_employees'] as $employee) {
                        $response .= "• {$employee['name']} - {$employee['checkin_time']}\n";
                    }
                    $response .= "\n";
                }
                
                if ($data['late_employees']->count() > 0) {
                    $response .= "⏰ **Pegawai Terlambat:**\n";
                    foreach ($data['late_employees'] as $employee) {
                        $response .= "• {$employee['name']} - {$employee['checkin_time']}\n";
                    }
                    $response .= "\n";
                }
                
                if ($data['absent_employees']->count() > 0) {
                    $response .= "❌ **Pegawai Tidak Hadir:**\n";
                    foreach ($data['absent_employees'] as $employee) {
                        $response .= "• {$employee['name']}\n";
                    }
                }
                
                return $response;
                
            case 'attendance_today':
                return "📊 **Data Kehadiran Hari Ini**\n\n" .
                       "• Pegawai yang hadir: **{$data['today_attendance']}** orang\n" .
                       "• Total pegawai: **{$data['total_employees']}** orang\n" .
                       "• Belum hadir: **{$data['not_attended']}** orang\n" .
                       "• Tanggal: {$data['date']}";
                       
            case 'students_count':
                return "👥 **Data Siswa**\n\n" .
                       "• Total siswa: **{$data['total_students']}** orang\n" .
                       "• Hadir hari ini: **{$data['present_today']}** orang\n" .
                       "• Tidak hadir: **{$data['absent_today']}** orang\n" .
                       "• Tanggal: {$data['date']}";
                       
            case 'leaves_pending':
                return "📋 **Data Izin & Cuti**\n\n" .
                       "• Menunggu persetujuan: **{$data['pending']}** pengajuan\n" .
                       "• Pengajuan hari ini: **{$data['today_submissions']}** pengajuan\n" .
                       "• Disetujui hari ini: **{$data['today_approved']}** pengajuan\n" .
                       "• Tanggal: {$data['date']}";
                       
            case 'late_employees':
                return "⏰ **Data Keterlambatan**\n\n" .
                       "• Pegawai terlambat hari ini: **{$data['late_today']}** orang\n" .
                       "• Pegawai tepat waktu: **{$data['on_time_today']}** orang\n" .
                       "• Batas waktu check-in: **{$data['checkin_end']}**\n" .
                       "• Tanggal: {$data['date']}";
                       
            case 'attendance_stats':
                return "📈 **Statistik Kehadiran**\n\n" .
                       "• Minggu ini: **{$data['this_week']}** kehadiran\n" .
                       "• Bulan ini: **{$data['this_month']}** kehadiran\n" .
                       "• Periode minggu: {$data['week_start']} - {$data['week_end']}\n" .
                       "• Bulan: {$data['month']}";
                       
            case 'reports':
                return "📄 **Laporan & Export**\n\n" .
                       "Untuk mengakses laporan dan export data, silakan gunakan menu:\n" .
                       "• **Laporan** - untuk melihat berbagai laporan\n" .
                       "• **Export Data** - untuk download data dalam format Excel\n\n" .
                       "Atau tanyakan data spesifik yang ingin Anda export!";
                       
            case 'general':
                return "🤖 **AI Assistant Sekolah**\n\n" .
                       "Halo! Saya AI Assistant untuk sistem manajemen sekolah.\n\n" .
                       "**Saya bisa membantu dengan:**\n" .
                       "• Data kehadiran pegawai\n" .
                       "• Data siswa dan kelas\n" .
                       "• Data izin dan cuti\n" .
                       "• Statistik dan laporan\n\n" .
                       "**Contoh pertanyaan:**\n" .
                       "• \"Berapa pegawai yang hadir hari ini?\"\n" .
                       "• \"Berapa total siswa di sekolah?\"\n" .
                       "• \"Berapa pengajuan izin yang menunggu?\"\n" .
                       "• \"Siapa yang terlambat hari ini?\"";
                       
            default:
                return "Maaf, saya belum memahami pertanyaan Anda. Silakan coba dengan kata kunci seperti:\n" .
                       "• absensi, kehadiran\n" .
                       "• siswa, total siswa\n" .
                       "• izin, cuti\n" .
                       "• terlambat, telat\n" .
                       "• statistik, laporan";
        }
    }
    
    private function getAttendanceByDate()
    {
        // Parse tanggal dari pertanyaan
        $date = $this->parseDateFromQuestion();
        
        if (!$date) {
            return [
                'error' => 'Tidak dapat mengenali tanggal dalam pertanyaan Anda. Format yang didukung: "3 september 2025", "3 sep 2025", "2025-09-03"'
            ];
        }
        
        $settings = Setting::getSettings();
        $checkinEnd = Carbon::createFromFormat('H:i:s', 
            strlen($settings->checkin_end ?? '07:05:00') === 5 ? 
            (($settings->checkin_end ?? '07:05') . ':00') : 
            ($settings->checkin_end ?? '07:05:00')
        );
        
        // Data pegawai yang hadir
        $presentEmployees = Attendance::whereDate('timestamp', $date)
            ->where('type', 'checkin')
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'email');
            }])
            ->get();
        
        // Pisahkan on-time dan terlambat
        $onTimeEmployees = $presentEmployees->filter(function($attendance) use ($checkinEnd) {
            return Carbon::parse($attendance->timestamp)->format('H:i:s') <= $checkinEnd->format('H:i:s');
        });
        
        $lateEmployees = $presentEmployees->filter(function($attendance) use ($checkinEnd) {
            return Carbon::parse($attendance->timestamp)->format('H:i:s') > $checkinEnd->format('H:i:s');
        });
        
        // Data pegawai yang tidak hadir
        $totalEmployees = User::whereHas('roles', function($query) {
            $query->where('name', '!=', 'Siswa');
        })->count();
        
        $presentUserIds = $presentEmployees->pluck('user_id')->toArray();
        $absentEmployees = User::whereHas('roles', function($query) {
            $query->where('name', '!=', 'Siswa');
        })->whereNotIn('id', $presentUserIds)
        ->select('id', 'name', 'email')
        ->get();
        
        return [
            'date' => $date->format('d F Y'),
            'total_employees' => $totalEmployees,
            'present_count' => $presentEmployees->count(),
            'absent_count' => $absentEmployees->count(),
            'on_time_count' => $onTimeEmployees->count(),
            'late_count' => $lateEmployees->count(),
            'checkin_end' => $checkinEnd->format('H:i'),
            'on_time_employees' => $onTimeEmployees->map(function($attendance) {
                return [
                    'name' => $attendance->user->name,
                    'email' => $attendance->user->email,
                    'checkin_time' => Carbon::parse($attendance->timestamp)->format('H:i:s')
                ];
            })->values(),
            'late_employees' => $lateEmployees->map(function($attendance) {
                return [
                    'name' => $attendance->user->name,
                    'email' => $attendance->user->email,
                    'checkin_time' => Carbon::parse($attendance->timestamp)->format('H:i:s')
                ];
            })->values(),
            'absent_employees' => $absentEmployees->map(function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email
                ];
            })->values()
        ];
    }
    
    private function parseDateFromQuestion()
    {
        $question = strtolower(request()->input('question'));
        
        // Pattern untuk tanggal Indonesia
        $patterns = [
            // "3 september 2025", "3 sep 2025"
            '/(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember|jan|feb|mar|apr|mei|jun|jul|agu|sep|okt|nov|des)\s+(\d{4})/',
            // "2025-09-03", "03-09-2025"
            '/(\d{4})-(\d{1,2})-(\d{1,2})/',
            '/(\d{1,2})-(\d{1,2})-(\d{4})/',
            // "03/09/2025"
            '/(\d{1,2})\/(\d{1,2})\/(\d{4})/'
        ];
        
        $monthNames = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4,
            'jun' => 6, 'jul' => 7, 'agu' => 8, 'sep' => 9,
            'okt' => 10, 'nov' => 11, 'des' => 12
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $question, $matches)) {
                if (count($matches) == 4) {
                    // Format dengan nama bulan
                    $day = (int)$matches[1];
                    $monthName = strtolower($matches[2]);
                    $year = (int)$matches[3];
                    
                    if (isset($monthNames[$monthName])) {
                        $month = $monthNames[$monthName];
                        return Carbon::create($year, $month, $day);
                    }
                } elseif (count($matches) == 4 && strpos($matches[0], '-') !== false) {
                    // Format YYYY-MM-DD
                    $year = (int)$matches[1];
                    $month = (int)$matches[2];
                    $day = (int)$matches[3];
                    return Carbon::create($year, $month, $day);
                } elseif (count($matches) == 4 && strpos($matches[0], '/') !== false) {
                    // Format DD/MM/YYYY
                    $day = (int)$matches[1];
                    $month = (int)$matches[2];
                    $year = (int)$matches[3];
                    return Carbon::create($year, $month, $day);
                }
            }
        }
        
        return null;
    }
}
