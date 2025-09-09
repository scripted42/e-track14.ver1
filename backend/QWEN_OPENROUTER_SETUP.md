# 🤖 QWEN + OpenRouter Setup Guide

## **Overview**
Implementasi AI Assistant menggunakan QWEN 2.5 72B via OpenRouter API untuk sistem manajemen sekolah E-Track14.

## **Keunggulan QWEN + OpenRouter**

### **✅ QWEN 2.5 72B:**
- **Multilingual Excellence**: Sangat baik untuk Bahasa Indonesia
- **Code Understanding**: Excellent untuk query database dan analisis data
- **Context Length**: 32K+ tokens (cukup untuk data sekolah lengkap)
- **Cost Effective**: Lebih murah dari GPT-4 Turbo
- **Performance**: Cepat dan akurat untuk structured data
- **Reasoning**: Kemampuan analisis yang mendalam

### **✅ OpenRouter:**
- **Multiple Models**: Akses ke berbagai LLM (QWEN, GPT, Claude, dll)
- **Unified API**: Satu API untuk semua model
- **Rate Limiting**: Built-in protection
- **Cost Transparency**: Clear pricing per token
- **Easy Integration**: Simple REST API
- **Reliability**: High uptime dan support

## **Setup Instructions**

### **1. Daftar OpenRouter Account**
```bash
# Kunjungi: https://openrouter.ai/
# Daftar account dan dapatkan API key
# Pilih plan yang sesuai (Free tier tersedia)
```

### **2. Konfigurasi Environment**
```bash
# Tambahkan ke .env file
OPENROUTER_API_KEY=sk-or-v1-your-api-key-here
OPENROUTER_MODEL=qwen/qwen-2.5-72b-instruct
```

### **3. Install Dependencies**
```bash
# Laravel sudah include Http client, tidak perlu install tambahan
# Pastikan Guzzle HTTP tersedia (default Laravel)
```

### **4. Test Configuration**
```bash
# Test API connection
php artisan tinker
>>> Http::get('https://openrouter.ai/api/v1/models')
```

## **API Pricing (OpenRouter)**

### **QWEN 2.5 72B Instruct:**
- **Input**: $0.0005 per 1K tokens
- **Output**: $0.002 per 1K tokens
- **Context**: 32K tokens

### **Cost Estimation untuk E-Track14:**
- **Pertanyaan sederhana**: ~$0.001-0.005
- **Analisis kompleks**: ~$0.01-0.05
- **Laporan bulanan**: ~$0.1-0.5

## **Features yang Tersedia**

### ** Data Analysis:**
- Real-time attendance analysis
- Student performance insights
- Leave management statistics
- Trend analysis dan predictions

### ** Natural Language Queries:**
- "Berapa pegawai yang terlambat hari ini?"
- "Buatkan laporan kehadiran bulan September"
- "Analisis performa siswa kelas 9A"
- "Rekomendasi untuk meningkatkan disiplin"

### ** Export & Reports:**
- Generate PDF reports
- Export data ke Excel
- Create visualizations
- Automated insights

## **Security & Privacy**

### **✅ Data Protection:**
- Data sekolah tidak disimpan di OpenRouter
- Hanya context yang relevan yang dikirim
- API key disimpan aman di .env
- Rate limiting untuk mencegah abuse

### **✅ Access Control:**
- Hanya Admin dan Kepala Sekolah
- Role-based authentication
- Session management
- Audit logging

## **Performance Optimization**

### ** Caching Strategy:**
```php
// Cache frequent queries
$context = Cache::remember('ai_context_' . $date, 300, function() {
    return $this->getSchoolContext();
});
```

### ** Response Optimization:**
```php
// Limit context size
'max_tokens' => 2000,
'temperature' => 0.7,
'top_p' => 0.9
```

## **Monitoring & Debugging**

### **📊 Logging:**
```php
// Log semua AI requests
Log::info('AI Request', [
    'question' => $question,
    'context' => $context,
    'response_time' => $responseTime
]);
```

### ** Error Handling:**
```php
// Graceful fallback
try {
    $response = $this->callQWEN($question, $context);
} catch (\Exception $e) {
    Log::error('AI Error: ' . $e->getMessage());
    return $this->getFallbackResponse($question);
}
```

## **Advanced Features**

### ** Memory System:**
- Conversation context
- User preferences
- Historical data analysis
- Learning from interactions

### ** Multi-Model Support:**
```php
// Switch models based on task
$model = $this->isComplexQuery($question) 
    ? 'qwen/qwen-2.5-72b-instruct'
    : 'qwen/qwen-2.5-7b-instruct';
```

### ** Custom Prompts:**
- Role-specific prompts
- Task-specific instructions
- Dynamic context building
- Adaptive responses

## **Troubleshooting**

### **❌ Common Issues:**

1. **API Key Invalid**
   ```bash
   # Check .env file
   grep OPENROUTER_API_KEY .env
   ```

2. **Rate Limit Exceeded**
   ```php
   // Implement retry logic
   $response = Http::retry(3, 1000)->post($url, $data);
   ```

3. **Model Not Available**
   ```php
   // Fallback to alternative model
   $model = 'qwen/qwen-2.5-7b-instruct';
   ```

4. **Context Too Large**
   ```php
   // Truncate context
   $context = array_slice($context, 0, 1000);
   ```

## **Best Practices**

### **✅ Do's:**
- Use specific, clear questions
- Provide relevant context
- Implement proper error handling
- Monitor API usage and costs
- Cache frequent responses

### **❌ Don'ts:**
- Don't send sensitive data
- Don't make too many requests
- Don't ignore rate limits
- Don't hardcode API keys
- Don't skip validation

## **Next Steps**

1. **Setup OpenRouter Account** ✅
2. **Configure Environment** ✅
3. **Test Basic Queries** 🔄
4. **Implement Advanced Features** 📋
5. **Monitor Performance** 📋
6. **Optimize Costs** 📋

## **Support**

- **OpenRouter Docs**: https://openrouter.ai/docs
- **QWEN Model Info**: https://huggingface.co/Qwen
- **Laravel HTTP Client**: https://laravel.com/docs/http-client

---

**Ready to implement intelligent AI Assistant! 🚀**
