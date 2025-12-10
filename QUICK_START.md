# 🚀 QUICK START - 5 Minutes Setup

## Prerequisites
✅ PHP 8.1+, Composer, Node.js, MySQL, Python 3.8+

## Step 1: Install Dependencies (2 mins)

**Option A - Automated (Recommended):**
```bash
# Windows
install.bat

# Linux/Mac
chmod +x install.sh && ./install.sh
```

**Option B - Manual:**
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Step 2: Configure Database (1 min)

Edit `.env` file:
```env
DB_DATABASE=face_recognition_db
DB_USERNAME=root
DB_PASSWORD=your_password

FACE_RECOGNITION_API_URL=http://localhost:8001
```

Create database:
```sql
CREATE DATABASE face_recognition_db;
```

Run migrations:
```bash
php artisan migrate
```

## Step 3: Build Assets (1 min)

```bash
npm run build
```

## Step 4: Start Servers (1 min)

**Terminal 1 - Python API:**
```bash
cd path/to/python/api
python main.py
# Should run on http://localhost:8001
```

**Terminal 2 - Laravel:**
```bash
php artisan serve
# Visit http://localhost:8000
```

## Step 5: Login & Test

1. **Admin Login:** http://localhost:8000/login
   - Email: `admin@example.com`
   - Password: `password`

2. **Add Employee:**
   - Go to Employees → Add Employee
   - Fill employee details
   - Save

3. **Register Face:**
   - Click "Register Face" on employee
   - Upload clear photo
   - Submit

4. **Test Attendance:**
   - Visit home page: http://localhost:8000
   - Allow camera access
   - Click SUBMIT
   - Should recognize face!

## 🎉 Done!

You're ready to use the system!

## 📚 Need Help?

- Full documentation: `README_WEB.md`
- Setup guide: `SETUP_GUIDE.md`
- Project summary: `PROJECT_SUMMARY.md`

## ⚠️ Important Notes

1. **Change admin password** after first login!
2. **Python API must run** on port 8001
3. **Camera requires** HTTPS or localhost
4. **Good lighting** needed for face recognition

## 🔧 Common Issues

**Database error?**
- Check DB credentials in `.env`
- Verify MySQL is running
- Database exists?

**Camera not working?**
- Allow browser permissions
- Use Chrome/Edge
- Check system camera access

**Face not recognized?**
- Register face first
- Check lighting
- Python API running?

**Assets not loading?**
```bash
npm run build
php artisan cache:clear
```

## 📱 URLs

- **Web App:** http://localhost:8000
- **Admin Login:** http://localhost:8000/login
- **Python API:** http://localhost:8001
- **API Docs:** http://localhost:8001/docs

---

**Default Meal Times:**
- Breakfast: 06:00 - 08:00
- Lunch: 11:00 - 13:00
- Dinner: 17:00 - 19:00

*(Configurable in Admin → Meal Times)*

---

Happy coding! 🚀
