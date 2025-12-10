# Face Recognition Attendance System - Laravel Web Application

## 🎯 Overview

Web application for employee attendance using face recognition technology for breakfast, lunch, and dinner meals. Built with Laravel and integrated with Python face recognition API.

## ✨ Features

### Public Features
- **Face Recognition Check-in**: Real-time webcam face recognition for attendance
- **Live Attendance List**: View today's attendance in real-time
- **Meal Time Support**: Separate tracking for breakfast, lunch, and dinner

### Admin Features (Login Required)
- **Dashboard**: Overview statistics (total employees, registered faces, today's attendance)
- **Employee Management**: CRUD operations for employee data
- **Meal Time Settings**: Configure breakfast, lunch, and dinner time windows
- **Modern UI**: Clean, responsive design using Tailwind CSS

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Python 3.8+ (for Face Recognition API)

### Installation Steps

1. **Install PHP Dependencies**
```bash
composer install
```

2. **Install NPM Dependencies**
```bash
npm install
```

3. **Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=face_recognition_db
DB_USERNAME=root
DB_PASSWORD=your_password

# Face Recognition API URL
FACE_RECOGNITION_API_URL=http://localhost:8001
```

5. **Run Migrations**
```bash
php artisan migrate
```

This will create:
- `employees` table
- `face_embeddings` table
- `meal_time_settings` table (with default times)
- `attendance_logs` table
- Add `is_admin` field to `users` table
- Create default admin user (email: admin@example.com, password: password)

6. **Build Assets**
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

7. **Start Laravel Server**
```bash
php artisan serve
```

The application will be available at: http://localhost:8000

8. **Start Python Face Recognition API**

Make sure the Python API is running on port 8001. See the Python API documentation for setup instructions.

## 👤 Default Admin Login

- **Email**: admin@example.com
- **Password**: password

⚠️ **Important**: Change the default password immediately after first login!

## 📁 Project Structure

```
face-recognition-web/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php          # Admin dashboard & management
│   │   │   ├── AttendanceController.php     # Attendance check-in
│   │   │   └── Auth/
│   │   │       └── LoginController.php      # Authentication
│   │   └── Middleware/
│   │       └── AdminMiddleware.php          # Admin access control
│   ├── Models/
│   │   ├── Employee.php                     # Employee model
│   │   ├── FaceEmbedding.php               # Face data model
│   │   ├── MealTimeSetting.php             # Meal time config model
│   │   └── AttendanceLog.php               # Attendance record model
│   └── Services/
│       └── FaceRecognitionService.php       # Python API integration
├── database/
│   └── migrations/                          # Database migrations
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php               # Public layout
│   │   │   └── admin.blade.php             # Admin layout
│   │   ├── auth/
│   │   │   └── login.blade.php             # Login page
│   │   ├── admin/
│   │   │   ├── dashboard.blade.php         # Admin dashboard
│   │   │   ├── employees/                  # Employee management views
│   │   │   └── meal-times.blade.php        # Meal time settings
│   │   └── attendance/
│   │       └── index.blade.php             # Face recognition check-in
│   └── css/
│       └── app.css                          # Tailwind CSS
└── routes/
    └── web.php                              # Application routes
```

## 🔧 Configuration

### Meal Time Settings

Default meal times (configurable via admin panel):
- **Breakfast**: 06:00 - 08:00
- **Lunch**: 11:00 - 13:00
- **Dinner**: 17:00 - 19:00

### Face Recognition API

The application communicates with a Python FastAPI server for face recognition. Configure the API URL in `.env`:

```env
FACE_RECOGNITION_API_URL=http://localhost:8001
```

## 📝 Usage Guide

### For Employees

1. Visit the home page (http://localhost:8000)
2. Position your face in front of the camera
3. Click "SUBMIT" button
4. System will recognize your face and record attendance
5. Your attendance will appear in the list on the right side

### For Admins

1. Login at http://localhost:8000/login
2. **Dashboard**: View statistics and quick actions
3. **Employees**: 
   - Add new employees with employee ID, name, email, phone, department, position
   - Edit employee information
   - Delete employees (will also delete face data)
4. **Meal Times**:
   - Set start and end times for each meal
   - Enable/disable meal times
   - Changes take effect immediately

## 🎨 UI Features

- **Modern Design**: Clean and professional interface using Tailwind CSS
- **Responsive**: Works on desktop and tablet devices
- **Real-time Clock**: Live time display on attendance page
- **Live Camera Feed**: Mirrored webcam view for natural face positioning
- **Modal Notifications**: Success/error messages with animations
- **Color-coded Badges**: Visual indicators for meal types and statuses

## 🔐 Security

- Admin authentication required for management pages
- CSRF protection on all forms
- Password hashing using Laravel's bcrypt
- Middleware-based access control
- Session-based authentication

## 🐛 Troubleshooting

### Camera Not Working
- Check browser permissions for camera access
- Use HTTPS or localhost only (cameras require secure context)
- Try different browsers (Chrome/Edge recommended)

### Face Recognition Not Working
- Ensure Python API is running on port 8001
- Check API URL in `.env` file
- Verify good lighting and face positioning
- Check Python API logs for errors

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database exists
- Check if MySQL/PostgreSQL service is running

### Tailwind CSS Not Loading
- Run `npm run build` or `npm run dev`
- Clear browser cache
- Check Vite is running for development

## 🔄 Development

### Watch Mode (Auto-compile assets)
```bash
npm run dev
```

### Build for Production
```bash
npm run build
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📊 Database Schema

### employees
- employee_id (unique)
- name, email, phone
- department, position
- is_active

### face_embeddings
- employee_id (foreign key)
- embedding_path
- face_image_path
- confidence_score, bbox

### meal_time_settings
- meal_type (breakfast/lunch/dinner)
- start_time, end_time
- is_active

### attendance_logs
- employee_id (foreign key)
- meal_type
- attendance_date, attendance_time
- similarity_score, confidence_score
- Unique constraint: one attendance per employee per meal per day

## 🤝 Integration with Python API

The Laravel application integrates with the Python FastAPI through the `FaceRecognitionService` class:

- **Register Face**: `POST /api/face/register`
- **Recognize Face**: `POST /api/face/recognize`
- **Delete Face**: `DELETE /api/face/delete/{employee_id}`
- **Health Check**: `GET /health`

See `API_DOCS.md` in the Python project for complete API documentation.

## 📄 License

© 2025 IT-SIMS. All rights reserved.

## 👥 Support

For issues or questions:
1. Check the troubleshooting section
2. Review Python API documentation
3. Check browser console for JavaScript errors
4. Review Laravel logs in `storage/logs/`
