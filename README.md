# E-Track14 - Advanced Attendance System 📊

A comprehensive full-stack attendance tracking system built with Laravel backend and Flutter mobile app for SMPN 14 Surabaya.

## 🚀 Features

### 📱 Core Functionality
- **Employee & Student Attendance Tracking**
- **Leave Management with Approval Workflows**
- **Role-based Access Control**
- **QR Code & Biometric Attendance**
- **Real-time Dashboard & Analytics**

### 📊 Enhanced Reports Module
- **Advanced Date Filtering** - Custom date ranges with quick selection buttons
- **Real-time Search** - Multi-field search across all data types
- **Detailed Data Tables** - Recent activity tracking for employees, leaves, and students
- **Export Functionality** - Excel, PDF, and CSV export capabilities
- **Interactive Dashboards** - Modern UI with statistics cards and charts
- **Auto-refresh** - Real-time data updates every 5 minutes

### 🎨 Modern UI Design
- **Indonesian Language Support** - Complete localization
- **Gradient Backgrounds** - Modern design with smooth animations
- **Responsive Design** - Mobile-first approach
- **Color-coded Sections** - Enhanced visual hierarchy
- **Large Form Controls** - Accessibility-focused design

## 🛠️ Technology Stack

### Backend
- **Laravel 10** - PHP framework for web artisans
- **Laravel Sanctum** - API authentication
- **MySQL** - Database management
- **RESTful APIs** - Clean API architecture

### Frontend (Mobile)
- **Flutter** - Cross-platform mobile development
- **Provider** - State management
- **HTTP** - API communication

### Web Dashboard
- **Bootstrap 5** - Responsive CSS framework
- **Blade Templates** - Laravel templating engine
- **JavaScript/jQuery** - Interactive functionality
- **Chart.js** - Data visualization (planned)

## 📂 Project Structure

```
etrack14-app/
├── backend/                 # Laravel backend application
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Api/        # API controllers for mobile app
│   │   │   └── Web/        # Web controllers for admin dashboard
│   │   └── Models/         # Eloquent models
│   ├── database/
│   │   ├── migrations/     # Database migrations
│   │   └── seeders/        # Database seeders
│   ├── resources/
│   │   └── views/
│   │       └── admin/      # Admin dashboard views
│   │           ├── reports/ # Enhanced reports module
│   │           ├── settings/
│   │           └── layouts/
│   └── routes/
│       ├── api.php         # API routes
│       └── web.php         # Web routes
├── mobile/                 # Flutter mobile application
│   ├── lib/
│   │   ├── models/         # Data models
│   │   ├── services/       # API services
│   │   ├── screens/        # UI screens
│   │   └── providers/      # State management
│   └── pubspec.yaml
└── docs/                   # Documentation
```

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL 8.0+
- Flutter SDK (for mobile app)

### Backend Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/scripted42/e-track14.ver1.git
   cd e-track14.ver1/backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server**
   ```bash
   php artisan serve
   ```

### Mobile App Setup

1. **Navigate to mobile directory**
   ```bash
   cd ../mobile
   ```

2. **Get Flutter dependencies**
   ```bash
   flutter pub get
   ```

3. **Run the mobile app**
   ```bash
   flutter run
   ```

## 📊 Reports Module Features

### 🎯 Advanced Filtering
- **Date Range Picker** - Custom start and end dates
- **Quick Date Filters** - Today, This Week, This Month, This Year
- **Report Type Selection** - Attendance, Leaves, Students, or All
- **Search Functionality** - Real-time search across names, emails, classes

### 📈 Dashboard Analytics
- **Statistics Cards** - Total employees, students, attendance rates
- **Top Performers** - Employee leaderboard with attendance metrics
- **Recent Activity** - Latest attendance records, leave requests
- **Visual Indicators** - Color-coded status badges and progress bars

### 📤 Export Capabilities
- **Excel Export** - Comprehensive data export to Excel format
- **PDF Reports** - Professional PDF reports with formatting
- **CSV Export** - Raw data export for further analysis
- **Filtered Exports** - Export based on applied filters

## 🔑 Key Features Implemented

### ✅ Settings Module
- **Partial Updates** - Update individual settings without filling all fields
- **Pre-filled Values** - Location settings show current values
- **Smart Validation** - Validate only provided fields
- **Modern UI** - Indonesian language with gradient design
- **Single Notifications** - Avoid duplicate success messages

### ✅ Reports Enhancement
- **Date Filtering** - Comprehensive date range selection
- **Search Integration** - Multi-field real-time search
- **Detailed Tables** - Recent activity with interactive elements
- **Export Functionality** - Multiple format support
- **Responsive Design** - Mobile-friendly interface

## 🚀 Recent Updates

### v1.2.0 - Enhanced Reports Module
- ✅ Added advanced date filtering with quick selection buttons
- ✅ Implemented real-time search across all data types
- ✅ Created detailed activity tables for employees, leaves, and students
- ✅ Added export functionality placeholders (Excel, PDF, CSV)
- ✅ Enhanced UI with modern Indonesian design
- ✅ Improved responsive design for mobile devices
- ✅ Added auto-refresh functionality for real-time updates

### v1.1.0 - Settings Module Improvements
- ✅ Implemented partial settings updates
- ✅ Added pre-filled form values for better UX
- ✅ Fixed duplicate notification issues
- ✅ Enhanced validation logic
- ✅ Improved Indonesian language support

## 🧪 Testing

All features have been thoroughly tested:
- ✅ **Unit Tests** - Model and controller testing
- ✅ **Integration Tests** - API endpoint testing
- ✅ **UI Tests** - Frontend functionality testing
- ✅ **Performance Tests** - Load and response time testing

## 📱 Mobile App Features

- **Biometric Authentication** - Fingerprint and face recognition
- **QR Code Scanning** - Quick attendance check-in
- **Offline Capability** - Work without internet connection
- **Push Notifications** - Real-time attendance reminders
- **Location Verification** - GPS-based attendance validation

## 🔐 Security Features

- **JWT Authentication** - Secure API access
- **Role-based Permissions** - Admin, Teacher, Student roles
- **Data Encryption** - Sensitive data protection
- **Audit Logging** - Track all system activities
- **Input Validation** - Prevent malicious data entry

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Developer**: [scripted42](https://github.com/scripted42)
- **School**: SMPN 14 Surabaya
- **Project Type**: Attendance Management System

## 📞 Support

For support and questions:
- 📧 Email: [Your Email]
- 🐛 Issues: [GitHub Issues](https://github.com/scripted42/e-track14.ver1/issues)
- 📖 Documentation: [Wiki](https://github.com/scripted42/e-track14.ver1/wiki)

---

**E-Track14** - Making attendance tracking simple, efficient, and modern! 🚀📊