<?php

/**
 * Test Script untuk QWEN + OpenRouter Integration
 * 
 * Jalankan dengan: php test_qwen_integration.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class QWENTester
{
    private $openrouterApiKey;
    private $openrouterUrl = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->openrouterApiKey = $_ENV['OPENROUTER_API_KEY'] ?? null;
        
        if (empty($this->openrouterApiKey)) {
            throw new Exception('OPENROUTER_API_KEY tidak ditemukan di .env file');
        }
    }

    public function testConnection()
    {
        echo "🔗 Testing OpenRouter Connection...\n";
        
        try {
            $response = Http::get('https://openrouter.ai/api/v1/models');
            
            if ($response->successful()) {
                echo "✅ Connection successful!\n";
                $models = $response->json();
                echo "📋 Available models: " . count($models['data']) . "\n";
                return true;
            } else {
                echo "❌ Connection failed: " . $response->status() . "\n";
                return false;
            }
        } catch (Exception $e) {
            echo "❌ Connection error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testQWENQuery($question)
    {
        echo "🤖 Testing QWEN Query...\n";
        echo "❓ Question: $question\n";
        
        try {
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
                        'content' => 'Anda adalah AI Assistant untuk sistem manajemen sekolah. Jawab dalam Bahasa Indonesia yang sopan dan profesional.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['choices'][0]['message']['content'] ?? 'No response';
                
                echo "✅ Query successful!\n";
                echo "📝 Response: $answer\n";
                echo "💰 Usage: " . json_encode($data['usage'] ?? []) . "\n";
                return true;
            } else {
                echo "❌ Query failed: " . $response->status() . "\n";
                echo "📄 Response: " . $response->body() . "\n";
                return false;
            }
        } catch (Exception $e) {
            echo "❌ Query error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function testSchoolContext()
    {
        echo "🏫 Testing School Context...\n";
        
        $context = [
            'school_name' => 'SMP Negeri 14',
            'current_date' => date('d F Y'),
            'current_time' => date('H:i'),
            'attendance' => [
                'total_employees' => 25,
                'present' => 20,
                'on_time' => 18,
                'late' => 2,
                'absent' => 5,
                'attendance_rate' => 80.0
            ],
            'students' => [
                'total' => 300,
                'present_today' => 280,
                'absent_today' => 20
            ],
            'leaves' => [
                'pending' => 3,
                'today_submissions' => 1
            ]
        ];

        $systemPrompt = $this->buildSystemPrompt($context);
        
        echo "📋 System Prompt Length: " . strlen($systemPrompt) . " characters\n";
        echo "📊 Context Data: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
        
        return $this->testQWENQuery("Berikan analisis kehadiran pegawai hari ini");
    }

    private function buildSystemPrompt($context)
    {
        return "Anda adalah AI Assistant untuk sistem manajemen sekolah {$context['school_name']}. 

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

**INSTRUKSI:**
1. Jawab dalam Bahasa Indonesia yang sopan dan profesional
2. Berikan analisis yang mendalam dan actionable insights
3. Gunakan data yang tersedia untuk memberikan jawaban yang akurat
4. Format output dengan markdown untuk readability";
    }

    public function runAllTests()
    {
        echo "🚀 Starting QWEN + OpenRouter Integration Tests\n";
        echo "=" . str_repeat("=", 50) . "\n\n";

        $tests = [
            'Connection Test' => [$this, 'testConnection'],
            'Basic Query Test' => function() { return $this->testQWENQuery('Halo, bagaimana kabar?'); },
            'School Context Test' => [$this, 'testSchoolContext'],
            'Complex Query Test' => function() { return $this->testQWENQuery('Buatkan analisis mendalam tentang performa kehadiran pegawai dan berikan rekomendasi untuk perbaikan'); }
        ];

        $results = [];
        foreach ($tests as $testName => $testFunction) {
            echo "\n" . str_repeat("-", 30) . "\n";
            echo "🧪 Running: $testName\n";
            echo str_repeat("-", 30) . "\n";
            
            $startTime = microtime(true);
            $result = $testFunction();
            $endTime = microtime(true);
            
            $results[$testName] = [
                'success' => $result,
                'duration' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ];
        }

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 Test Results Summary\n";
        echo str_repeat("=", 50) . "\n";
        
        foreach ($results as $testName => $result) {
            $status = $result['success'] ? '✅ PASS' : '❌ FAIL';
            echo sprintf("%-25s %s (%s)\n", $testName, $status, $result['duration']);
        }

        $passed = count(array_filter($results, fn($r) => $r['success']));
        $total = count($results);
        
        echo "\n📈 Overall: $passed/$total tests passed\n";
        
        if ($passed === $total) {
            echo "🎉 All tests passed! QWEN integration is ready! 🚀\n";
        } else {
            echo "⚠️  Some tests failed. Please check the configuration.\n";
        }
    }
}

// Run tests
try {
    $tester = new QWENTester();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure to set OPENROUTER_API_KEY in your .env file\n";
}
