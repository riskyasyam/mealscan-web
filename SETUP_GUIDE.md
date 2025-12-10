# Setup Guide - Face Recognition Attendance System

## 📋 Prerequisites Checklist

Before starting, ensure you have:

- [x] PHP 8.1 or higher
- [x] Composer
- [x] Node.js 16+ and NPM
- [x] MySQL or PostgreSQL
- [x] Python 3.8+ (for Face Recognition API)
- [x] Web browser with camera access (Chrome/Edge recommended)

## 🚀 Quick Installation (Recommended)

### Windows

1. Open Command Prompt or PowerShell in the project directory
2. Run the installation script:
```bash
install.bat
```

### Linux/Mac

1. Open Terminal in the project directory
2. Make the script executable and run:
```bash
chmod +x install.sh
./install.sh
```

The script will automatically:
- Install Composer dependencies
- Install NPM dependencies
- Create .env file
- Generate application key
- Run database migrations
- Build assets
- Create storage link

## 📝 Manual Installation

If you prefer manual installation or if the script fails:

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Database Setup

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=face_recognition_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:
```sql
CREATE DATABASE face_recognition_db;
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

This creates:
- `users` table (with admin user)
- `employees` table
- `face_embeddings` table
- `meal_time_settings` table (with default times)
- `attendance_logs` table

### Step 5: Build Assets

```bash
# For production
npm run build

# For development (with hot reload)
npm run dev
```

### Step 6: Create Storage Link

```bash
php artisan storage:link
```

## 🔧 Configuration

### Database Configuration

**MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=face_recognition_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

**PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=face_recognition_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

**SQLite (for testing):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Face Recognition API

Configure the Python API URL in `.env`:

```env
FACE_RECOGNITION_API_URL=http://localhost:8001
```

**Important**: Make sure the Python Face Recognition API is running before using the application!

## 🎮 Starting the Application

### 1. Start Python Face Recognition API

In the Python API directory:

```bash
# Windows
run.bat

# Linux/Mac
./run.sh
```

The Python API should be running on: `http://localhost:8001`

### 2. Start Laravel Server

In the Laravel project directory:

```bash
php artisan serve
```

The web application will be available at: `http://localhost:8000`

### 3. For Development (with hot reload)

Open another terminal and run:

```bash
npm run dev
```

This will watch for changes in your CSS/JS files and auto-rebuild.

## 👤 First Login

1. Visit: http://localhost:8000/login
2. Login with default credentials:
   - **Email**: admin@example.com
   - **Password**: password

3. **IMPORTANT**: Change the password immediately!
   - Go to user profile/settings (implement this if needed)
   - Or update directly in database

## ✅ Verification Steps

### 1. Check Database Connection

```bash
php artisan migrate:status
```

Should show all migrations as "Ran".

### 2. Check Python API

Visit: http://localhost:8001/docs

You should see the FastAPI interactive documentation.

### 3. Check Laravel App

Visit: http://localhost:8000

You should see the face recognition attendance page.

### 4. Check Camera Access

- Allow camera permissions when prompted
- You should see your webcam feed on the attendance page

## 🎯 Initial Setup Tasks

### 1. Add Your First Employee

1. Login to admin panel
2. Go to "Employees" → "Add Employee"
3. Fill in employee details:
   - Employee ID (unique)
   - Name
   - Email (optional)
   - Phone (optional)
   - Department (optional)
   - Position (optional)
4. Click "Create Employee"

### 2. Register Employee Face

1. In the employees list, click "Register Face"
2. Upload a clear photo of the employee's face
   - Good lighting
   - Face clearly visible
   - No blur
   - Single person only
3. Click "Register"
4. Wait for confirmation

### 3. Configure Meal Times (Optional)

1. Go to "Meal Times" in admin panel
2. Adjust times as needed:
   - Default Breakfast: 06:00 - 08:00
   - Default Lunch: 11:00 - 13:00
   - Default Dinner: 17:00 - 19:00
3. Enable/disable meal times as needed
4. Click "Update Settings"

### 4. Test Attendance

1. Go to home page (http://localhost:8000)
2. During an active meal time, position face in camera
3. Click "SUBMIT"
4. System should recognize the employee and record attendance

## 🐛 Troubleshooting

### Migration Errors

**Error: "Database does not exist"**
```bash
# Create database manually
mysql -u root -p
CREATE DATABASE face_recognition_db;
exit

# Run migrations again
php artisan migrate
```

**Error: "SQLSTATE[HY000] [1045] Access denied"**
- Check database credentials in `.env`
- Verify MySQL/PostgreSQL is running
- Test connection manually

### Camera Not Working

**Browser doesn't show camera**
- Check browser permissions
- Use HTTPS or localhost (required for camera access)
- Try different browser (Chrome/Edge recommended)

**Camera permission denied**
- Allow camera access in browser settings
- Check system camera permissions
- Restart browser

### Python API Connection Error

**"Failed to connect to face recognition service"**
- Verify Python API is running on port 8001
- Check `FACE_RECOGNITION_API_URL` in `.env`
- Test API: http://localhost:8001/health
- Check firewall settings

### Assets Not Loading

**Styles not appearing**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild assets
npm run build

# Clear browser cache
Ctrl+Shift+R (or Cmd+Shift+R on Mac)
```

### Face Recognition Not Working

**"Wajah tidak dikenali"**
- Ensure employee face is registered
- Check lighting conditions
- Position face clearly in camera
- Verify Python API is responding

**"Tidak ada wajah terdeteksi"**
- Ensure face is visible in camera
- Check lighting
- Move closer to camera
- Remove obstructions (masks, hats, etc.)

## 🔐 Security Recommendations

### Production Deployment

1. **Change Admin Password**
```bash
php artisan tinker
$user = User::where('email', 'admin@example.com')->first();
$user->password = Hash::make('new-secure-password');
$user->save();
```

2. **Update .env Settings**
```env
APP_ENV=production
APP_DEBUG=false
```

3. **Set Proper Permissions**
```bash
chmod -R 755 storage bootstrap/cache
```

4. **Enable HTTPS**
- Use SSL certificate
- Update APP_URL in .env

5. **Secure Database**
- Use strong passwords
- Restrict database access
- Regular backups

## 📊 Database Seeding (Optional)

To add sample data for testing:

1. Create a seeder:
```bash
php artisan make:seeder EmployeeSeeder
```

2. Add sample employees in the seeder

3. Run seeder:
```bash
php artisan db:seed --class=EmployeeSeeder
```

## 🔄 Updating the Application

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install
npm install

# Run new migrations
php artisan migrate

# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📱 Browser Compatibility

**Recommended Browsers:**
- ✅ Chrome 90+
- ✅ Edge 90+
- ✅ Firefox 88+
- ✅ Safari 14+

**Camera Features Require:**
- HTTPS or localhost
- Camera permissions
- WebRTC support

## 🆘 Getting Help

If you encounter issues:

1. Check error logs:
   - Laravel: `storage/logs/laravel.log`
   - Python API: Console output

2. Enable debug mode:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

3. Common commands:
```bash
# Clear all cache
php artisan optimize:clear

# View routes
php artisan route:list

# Check database connection
php artisan db:show

# View configuration
php artisan config:show database
```

## ✅ Post-Installation Checklist

- [ ] Database migrations successful
- [ ] Admin can login
- [ ] Python API is running
- [ ] Camera is accessible
- [ ] Can create employees
- [ ] Can register faces
- [ ] Face recognition works
- [ ] Attendance is recorded
- [ ] Meal times configured
- [ ] Admin password changed

## 🎉 You're All Set!

Your Face Recognition Attendance System is now ready to use!

- **Public URL**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/login
- **API Docs**: http://localhost:8001/docs

Happy tracking! 🚀
