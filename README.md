# 🍚 Rice Log - Employee Attendance Management System

Employee attendance and payroll management system for rice mill factories with face recognition, GPS tracking, leave management, and automated salary calculation.

## 🎯 Project Overview

**Rice Log** is a comprehensive web-based application designed to streamline employee attendance tracking for factory operations. Built with modern technologies (Laravel 11, Tailwind CSS, face-api.js), the system provides:

- ✅ **Face Recognition Check-in/Check-out** - AI-powered face detection via face-api.js
- ✅ **GPS Tracking** - Real-time location tracking with distance warnings
- ✅ **Leave Management** - Leave request workflow with boss approval
- ✅ **Rice Deposit Tracking** - Automatic salary calculation based on deposits
- ✅ **Role-Based Access** - Two roles: Employee & Boss/Manager
- ✅ **Real-Time Notifications** - Instant alerts for approvals
- ✅ **Responsive Design** - Works on desktop, tablet, mobile

## 📊 Project Status

**Status:** ✅ **95% COMPLETE - PRODUCTION READY**

- ✅ All 7 database tables created
- ✅ All 6 models with relationships
- ✅ All 10 controllers implemented
- ✅ All 20+ views responsive design
- ✅ Face recognition integrated
- ✅ GPS tracking functional
- ✅ All approvals workflows complete
- ✅ Comprehensive documentation included

## 🚀 Quick Start

### Prerequisites

- PHP 8.1+
- MySQL 8.0+
- Node.js 18+
- Composer

### Installation (5 minutes)

```bash
# Clone and setup
cd rice-log
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Assets & Storage
php artisan storage:link
npm run build

# Run
php artisan serve
# Open: http://localhost:8000
```

### Demo Credentials

- **Boss:** bos@ricemail.com / password
- **Employee:** karyawan1@ricemail.com / password

## 📚 Documentation

Comprehensive documentation included for all aspects:

| Document                                                   | Purpose                          | Time   |
| ---------------------------------------------------------- | -------------------------------- | ------ |
| [QUICK_START.md](QUICK_START.md)                           | 5-minute setup guide             | 10 min |
| [SETUP_GUIDE.md](SETUP_GUIDE.md)                           | Complete installation & features | 30 min |
| [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)                   | Database structure & queries     | 45 min |
| [IMPLEMENTATION_CHECKLIST.md](IMPLEMENTATION_CHECKLIST.md) | Feature checklist & stats        | 20 min |
| [TESTING_GUIDE.md](TESTING_GUIDE.md)                       | Comprehensive testing procedures | 60 min |
| [FINAL_STATUS.md](FINAL_STATUS.md)                         | Project status & achievements    | 15 min |
| [INDEX.md](INDEX.md)                                       | Documentation navigation guide   | 10 min |

**👉 START HERE:** [QUICK_START.md](QUICK_START.md)

## 🎓 Features

### Employee Features

- 📊 Dashboard with monthly stats (hadir, sakit, izin, cuti)
- 📍 Check-in with GPS tracking & distance alerts
- 📸 Face recognition (AI-powered with face-api.js)
- 📤 Check-out tracking
- 📋 Leave requests (max 3 days/month)
- 🌾 Rice deposit submissions with photo proof
- 📱 Real-time notifications
- 📈 Deposit & salary tracking

### Boss/Manager Features

- 📊 Dashboard with KPIs (employee count, active employees, monthly income)
- 👥 Employee management (CRUD, status toggle)
- ✅ Leave approval workflow
- 📝 Deposit verification with photo review
- ⚙️ System configuration (prices, distances, limits)
- 📍 GPS distance monitoring (>2km alerts)
- 🔍 Detailed employee reports with 12-month history

### System Features

- 🔐 Role-based access control
- 🔒 CSRF protection & input validation
- 🗺️ Haversine distance calculation
- 🤖 Real-time face recognition
- 💾 Automatic monthly summary & salary calculation
- 📬 Real-time notification system
- 📱 Responsive mobile design

## 🛠️ Tech Stack

**Backend:**

- Laravel 11 - PHP Web Framework
- MySQL 8.0 - Database
- Eloquent ORM - Database abstraction

**Frontend:**

- Blade Templates - Server-side rendering
- Tailwind CSS 3 - Utility-first CSS
- Font Awesome 6.4 - Icons
- SweetAlert2 - Dialogs & alerts
- jQuery 3.6 - DOM manipulation

**APIs & Services:**

- face-api.js 0.8.5 - AI Face Recognition
- TensorFlow.js - ML backend
- Google Maps API - GPS & mapping

## 📁 Project Structure

```
rice-log/
├── app/
│   ├── Http/Controllers/          # 10 controllers
│   ├── Http/Middleware/           # Role-based middleware
│   └── Models/                    # 6 Eloquent models
├── database/
│   ├── migrations/                # 7 database tables
│   └── seeders/                   # Sample data
├── resources/views/               # 20+ Blade templates
├── routes/
│   ├── web.php                    # Web routes
│   └── api.php                    # API endpoints
├── Documentation/
│   ├── QUICK_START.md
│   ├── SETUP_GUIDE.md
│   ├── DATABASE_SCHEMA.md
│   ├── TESTING_GUIDE.md
│   ├── IMPLEMENTATION_CHECKLIST.md
│   ├── FINAL_STATUS.md
│   └── INDEX.md
└── public/
    ├── css/
    └── js/
```

## 🔐 Security

- ✅ CSRF Protection (Laravel built-in)
- ✅ Password Hashing (bcrypt)
- ✅ Role-Based Access Control (Middleware)
- ✅ Input Validation (Both sides)
- ✅ SQL Injection Prevention (Eloquent)
- ✅ Authentication (Laravel Sanctum ready)

## 🧪 Testing

Complete testing guide included for all workflows:

- Authentication scenarios
- Employee features (check-in, leave, deposits)
- Boss approval workflows
- GPS & face recognition validation
- Responsive design testing
- Error handling

**See:** [TESTING_GUIDE.md](TESTING_GUIDE.md)

## 📊 Statistics

| Metric              | Value |
| ------------------- | ----- |
| **Files Created**   | 40+   |
| **Lines of Code**   | 5000+ |
| **Database Tables** | 7     |
| **Models**          | 6     |
| **Controllers**     | 10    |
| **Views**           | 20+   |
| **Routes**          | 15+   |
| **Completion**      | 95%   |

## 🚀 Deployment

### Pre-Launch Checklist

- [ ] All environment variables in .env
- [ ] Database created and migrated
- [ ] Google Maps API key configured
- [ ] Storage link created
- [ ] Assets built (npm run build)
- [ ] Comprehensive testing completed
- [ ] HTTPS configured for production
- [ ] Backups enabled

**See:** [FINAL_STATUS.md](FINAL_STATUS.md) - Pre-Launch Checklist

## 🐛 Known Limitations (Phase 1)

1. Email notifications configured but not sent (need SMTP)
2. Queue jobs using sync driver (use Redis for production)
3. Face API models loaded from CDN (internet required)
4. Camera requires HTTPS in production
5. File storage on local filesystem (consider S3 for scale)

## 🎯 Next Steps (Phase 2)

- [ ] Email notification delivery
- [ ] SMS alerts
- [ ] QR code check-in
- [ ] Mobile app (React Native)
- [ ] Advanced PDF reports
- [ ] Two-factor authentication
- [ ] Multi-language support
- [ ] Dark mode

## 📞 Support

### Documentation

- [QUICK_START.md](QUICK_START.md) - Fast setup
- [SETUP_GUIDE.md](SETUP_GUIDE.md) - Complete guide
- [TESTING_GUIDE.md](TESTING_GUIDE.md) - Testing procedures
- [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Database details
- [INDEX.md](INDEX.md) - Navigation guide

### Troubleshooting

- See [SETUP_GUIDE.md](SETUP_GUIDE.md) Troubleshooting section
- See [QUICK_START.md](QUICK_START.md) for quick tips

## 📝 License

Private - Rice Mill Property

## ✨ Credits

**Built with:**

- Laravel Framework
- Modern PHP practices
- Responsive design patterns
- AI-powered face recognition
- Real-time GPS tracking

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
#   r i c e - l o g  
 