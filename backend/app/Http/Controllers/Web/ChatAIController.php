<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAIController extends Controller
{
    private $openrouterApiKey;
    private $openrouterUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->openrouterApiKey = env('OPENROUTER_API_KEY');
    }

    public function index()
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Admin', $userRoles) && !in_array('Kepala Sekolah', $userRoles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return view('admin.chat-ai.index');
    }

    public function ask(Request $request)
    {
        $user = auth()->user();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (!in_array('Admin', $userRoles) && !in_array('Kepala Sekolah', $userRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses fitur ini.'
            ], 403);
        }

        $question = $request->input('question');
        
        if (empty($question)) {
            return response()->json([
                'success' => false,
                'message' => 'Pertanyaan tidak boleh kosong.'
            ]);
        }

        try {
            // Gather school context
            $context = $this->getSchoolContext($question);
            
            // Call QWEN via OpenRouter
            $response = $this->callQWEN($question, $context);
            
            return response()->json([
                'success' => true,
                'response' => $response,
                'context' => $context // For debugging
            ]);

        } catch (\Exception $e) {
            Log::error('AI Assistant Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pertanyaan. Silakan coba lagi.',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getSchoolContext($question)
    {
        $context = [
            'school_name' => 'SMP Negeri 14',
            'current_date' => Carbon::now()->format('d F Y'),
            'current_time' => Carbon::now()->format('H:i'),
            'attendance' => $this->getAttendanceData($question),
            'students' => $this->getStudentsData(),
            'leaves' => $this->getLeavesData(),
            'settings' => $this->getSettingsData()
        ];

        return $context;
    }

    private function getAttendanceData($question)
    {
        $date = $this->parseDateFromQuestion($question);
        
        $attendance = Attendance::with(['user.roles'])
            ->whereDate('created_at', $date)
            ->get();

        $onTimeCount = 0;
        $lateCount = 0;
        $absentCount = 0;

        $checkinEnd = Setting::getSettings()->checkin_end ?? '08:00';

        foreach ($attendance as $record) {
            if ($record->checkin_time) {
                $checkinTime = Carbon::parse($record->checkin_time);
                $checkinEndTime = Carbon::parse($date . ' ' . $checkinEnd);
                
                if ($checkinTime->lte($checkinEndTime)) {
                    $onTimeCount++;
                } else {
                    $lateCount++;
                }
            } else {
                $absentCount++;
            }
        }

        return [
            'date' => $date->format('d F Y'),
            'total_employees' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'Kepala Sekolah', 'Waka Kesiswaan', 'Guru', 'Staff Kesiswaan', 'Pegawai']);
            })->count(),
            'present' => $attendance->count(),
            'on_time' => $onTimeCount,
            'late' => $lateCount,
            'absent' => $absentCount,
            'attendance_rate' => $attendance->count() > 0 ? round(($attendance->count() / User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'Kepala Sekolah', 'Waka Kesiswaan', 'Guru', 'Staff Kesiswaan', 'Pegawai']);
            })->count()) * 100, 2) : 0
        ];
    }

    private function getStudentsData()
    {
        $totalStudents = Student::count();
        $presentToday = Student::whereHas('attendances', function($q) {
            $q->whereDate('created_at', Carbon::today());
        })->count();

        return [
            'total' => $totalStudents,
            'present_today' => $presentToday,
            'absent_today' => $totalStudents - $presentToday
        ];
    }

    private function getLeavesData()
    {
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $todayLeaves = Leave::whereDate('created_at', Carbon::today())->count();

        return [
            'pending' => $pendingLeaves,
            'today_submissions' => $todayLeaves
        ];
    }

    private function getSettingsData()
    {
        $settings = Setting::getSettings();
        
        return [
            'checkin_start' => $settings->checkin_start ?? '07:00',
            'checkin_end' => $settings->checkin_end ?? '08:00',
            'checkout_start' => $settings->checkout_start ?? '15:00',
            'checkout_end' => $settings->checkout_end ?? '17:00'
        ];
    }

    private function parseDateFromQuestion($question)
    {
        // Parse various date formats
        $patterns = [
            '/(\d{1,2})\s+(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})/i',
            '/(\d{1,2})\/(\d{1,2})\/(\d{4})/',
            '/(\d{4})-(\d{1,2})-(\d{1,2})/',
            '/(\d{1,2})-(\d{1,2})-(\d{4})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $question, $matches)) {
                if (strpos($pattern, 'januari|februari') !== false) {
                    // Indonesian month names
                    $monthNames = [
                        'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                        'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                        'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
                    ];
                    $month = $monthNames[strtolower($matches[2])];
                    return Carbon::create($matches[3], $month, $matches[1]);
                } else {
                    // Numeric dates
                    if (strpos($pattern, '(\d{4})-(\d{1,2})-(\d{1,2})') !== false) {
                        return Carbon::create($matches[1], $matches[2], $matches[3]);
                    } else {
                        return Carbon::create($matches[3], $matches[2], $matches[1]);
                    }
                }
            }
        }

        return Carbon::today();
    }

    private function callQWEN($question, $context)
    {
        if (empty($this->openrouterApiKey)) {
            throw new \Exception('OpenRouter API key tidak ditemukan. Silakan set OPENROUTER_API_KEY di .env');
        }

        $systemPrompt = $this->buildSystemPrompt($context);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openrouterApiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => 'http://localhost:8000',
            'X-Title' => 'E-Track14 AI Assistant'
        ])->post($this->openrouterUrl, [
            'model' => 'qwen/qwen-2.5-72b-instruct',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
                ],
                [
                    'role' => 'user',
                    'content' => $question
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
            'top_p' => 0.9
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenRouter API error: ' . $response->body());
        }

        $data = $response->json();
        
        if (!isset($data['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid response format from OpenRouter API');
        }

        return $data['choices'][0]['message']['content'];
    }

    private function buildSystemPrompt($context)
    {
        return "Anda adalah AI Assistant untuk sistem manajemen sekolah SMP Negeri 14. 

**KONTEKS SEKOLAH:**
- Nama Sekolah: {$context['school_name']}
- Tanggal Hari Ini: {$context['current_date']}
- Waktu Sekarang: {$context['current_time']}

**DATA KEHADIRAN HARI INI:**
- Total Pegawai: {$context['attendance']['total_employees']} orang
- Hadir: {$context['attendance']['present']} orang
- Tepat Waktu: {$context['attendance']['on_time']} orang
- Terlambat: {$context['attendance']['late']} orang
- Tidak Hadir: {$context['attendance']['absent']} orang
- Tingkat Kehadiran: {$context['attendance']['attendance_rate']}%

**DATA SISWA:**
- Total Siswa: {$context['students']['total']} orang
- Hadir Hari Ini: {$context['students']['present_today']} orang
- Tidak Hadir Hari Ini: {$context['students']['absent_today']} orang

**DATA IZIN:**
- Izin Menunggu: {$context['leaves']['pending']} pengajuan
- Pengajuan Hari Ini: {$context['leaves']['today_submissions']} pengajuan

**JADWAL SEKOLAH:**
- Check-in: {$context['settings']['checkin_start']} - {$context['settings']['checkin_end']}
- Check-out: {$context['settings']['checkout_start']} - {$context['settings']['checkout_end']}

**INSTRUKSI:**
1. Jawab dalam Bahasa Indonesia yang sopan dan profesional
2. Berikan analisis yang mendalam dan actionable insights
3. Gunakan data yang tersedia untuk memberikan jawaban yang akurat
4. Jika diminta data spesifik, berikan detail lengkap
5. Untuk pertanyaan tentang tanggal tertentu, gunakan data yang tersedia
6. Berikan rekomendasi yang praktis untuk perbaikan sistem
7. Format output dengan markdown untuk readability

**CONTOH RESPONSE:**
- Gunakan bullet points untuk list
- Gunakan **bold** untuk emphasis
- Gunakan tables untuk data terstruktur
- Berikan kesimpulan dan rekomendasi di akhir";
    }
}