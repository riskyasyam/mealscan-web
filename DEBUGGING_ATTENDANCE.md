# 🔍 DEBUGGING ATTENDANCE PAGE - TROUBLESHOOTING GUIDE

## 📋 Current Issues

### ❌ Issue 1: "No active meal time" tetap muncul
### ❌ Issue 2: Wajah tidak terdeteksi otomatis

---

## 🎯 Expected Behavior vs Actual Behavior

### ✅ EXPECTED (Harapan):
1. **Meal Time Detection**
   - Jam 17:45 → Badge shows "Current: dinner" (warna ungu)
   - Badge otomatis update sesuai waktu di database

2. **Face Recognition (Auto-detect)**
   - Camera hidup → Video feed muncul (terbalik/mirror)
   - Wajah muncul di kamera → **OTOMATIS** NIK & Nama terisi
   - User input jumlah makanan (1-10)
   - User klik SUBMIT → Data masuk database
   - Modal success → Table refresh dengan data baru

### ❌ ACTUAL (Kenyataan):
1. Badge shows "No active meal time" (merah)
2. Wajah di kamera tidak terdeteksi otomatis
3. NIK & Nama tidak terisi otomatis

---

## 🔬 Diagnosis & Crosscheck

### 1️⃣ CHECK: Database Meal Time Settings

**Location:** Buka phpMyAdmin/Database → Tabel `meal_time_settings`

**Expected Data:**
```sql
SELECT * FROM meal_time_settings;
```

| id | meal_type | start_time | end_time | is_active | created_at | updated_at |
|----|-----------|------------|----------|-----------|------------|------------|
| 1  | breakfast | 06:00:00   | 08:00:00 | 1         | ...        | ...        |
| 2  | lunch     | 11:00:00   | 13:00:00 | 1         | ...        | ...        |
| 3  | dinner    | 17:00:00   | 19:00:00 | 1         | ...        | ...        |

**✅ Checklist:**
- [ ] Data ada 3 rows (breakfast, lunch, dinner)
- [ ] Column `start_time` format: HH:MM:SS (bukan timestamp/datetime)
- [ ] Column `end_time` format: HH:MM:SS
- [ ] Column `is_active` = 1 (bukan 0)
- [ ] Jam sekarang (17:45) berada dalam range dinner (17:00-19:00)

**❌ Jika data tidak ada:**
```bash
php artisan migrate:fresh --seed
```

---

### 2️⃣ CHECK: Laravel Attendance Controller

**Test Current Meal Type Detection:**

Buka terminal, jalankan:
```bash
php artisan tinker
```

Lalu test:
```php
use App\Models\MealTimeSetting;
use Carbon\Carbon;

// Check current time
echo Carbon::now()->format('H:i:s');  // Output: 17:45:00

// Check meal type detection
$mealType = MealTimeSetting::getCurrentMealType();
echo $mealType;  // Expected: "dinner"

// Check all settings
MealTimeSetting::where('is_active', true)->get();
```

**✅ Expected Output:**
```
"17:45:00"
"dinner"
Collection with 3 items (breakfast, lunch, dinner)
```

**❌ Jika output null atau kosong:**
- Problem di logic `MealTimeSetting::getCurrentMealType()`
- Check file: `app/Models/MealTimeSetting.php`

---

### 3️⃣ CHECK: Python API Status

**CRITICAL:** Face detection TIDAK akan bekerja jika Python API mati!

#### A. Check API Running
```bash
# Test if API alive
curl http://localhost:8001/health

# Expected Response:
{
  "status": "ok",
  "message": "Face Recognition API is running"
}
```

**❌ Jika error "Connection refused":**
→ Python API tidak running!

#### B. Start Python API

Di terminal lain (bukan Laravel terminal):
```bash
# Navigate to Python API folder
cd C:\path\to\python\api\folder

# Run API server
python main.py

# or
uvicorn main:app --host 0.0.0.0 --port 8001
```

**✅ Expected Output:**
```
INFO:     Uvicorn running on http://0.0.0.0:8001 (Press CTRL+C to quit)
INFO:     Started reloader process
INFO:     Started server process
INFO:     Waiting for application startup.
INFO:     Application startup complete.
```

#### C. Test Python API Endpoints

**1. Health Check:**
```bash
curl http://localhost:8001/health
```

**2. List Registered Employees:**
```bash
curl http://localhost:8001/employees
```

Expected:
```json
{
  "employees": ["123456", "789012"],
  "count": 2
}
```

**3. Test Recognition (Manual):**
```bash
# Save a test image, then:
curl -X POST http://localhost:8001/recognize -F "file=@test_face.jpg"
```

Expected:
```json
{
  "success": true,
  "employee_id": "123456",
  "employee_name": "John Doe",
  "similarity": 0.87
}
```

---

### 4️⃣ CHECK: Laravel Routes & Config

**Test Route:**
```bash
php artisan route:list --name=checkin
```

Expected output:
```
POST | /checkin | checkin | App\Http\Controllers\AttendanceController@checkIn
```

**Check Service Config:**

File: `config/services.php`
```php
'face_recognition' => [
    'api_url' => env('FACE_RECOGNITION_API_URL', 'http://localhost:8001'),
],
```

File: `.env`
```env
FACE_RECOGNITION_API_URL=http://localhost:8001
```

**Test dari Laravel:**
```bash
php artisan tinker
```

```php
use App\Services\FaceRecognitionService;

$service = new FaceRecognitionService();
$health = $service->checkHealth();
dd($health);  // Should return ["status" => "ok"]
```

---

### 5️⃣ CHECK: Browser Console (Developer Tools)

**How to Open:**
1. Buka attendance page: http://localhost:8000
2. Press **F12** atau Right-click → Inspect
3. Pilih tab **Console**

**Look for Errors:**

#### ✅ GOOD (No Errors):
```
Response: {success: true, nik: "123456", employee_name: "John Doe", ...}
```

#### ❌ BAD (Errors):

**Error Type 1: Camera Permission**
```
Error accessing webcam: NotAllowedError: Permission denied
```
**Fix:** Allow camera permission di browser settings

**Error Type 2: API Connection Failed**
```
POST http://localhost:8000/checkin 500 (Internal Server Error)
```
**Fix:** Check Laravel logs: `storage/logs/laravel.log`

**Error Type 3: Python API Timeout**
```
Error: Terjadi kesalahan: cURL error 7: Failed to connect to localhost port 8001
```
**Fix:** Python API tidak running, start dulu!

**Error Type 4: No Active Meal Time**
```
Response: {success: false, message: "Tidak dalam waktu makan."}
```
**Fix:** Database meal_time_settings salah atau kosong

---

## 🔧 Python API Requirements Checklist

### File Structure Expected:
```
python-api/
├── main.py                 # FastAPI app
├── requirements.txt        # Dependencies
├── face_embeddings/        # Folder untuk simpan embedding
│   ├── 123456.npy         # NIK karyawan
│   └── 789012.npy
└── uploads/               # Temp folder (optional)
```

### Python API Endpoints Required:

#### 1. POST /register
Register face embedding untuk employee baru
```python
@app.post("/register")
async def register_face(
    file: UploadFile,
    employee_id: str = Form(...),
    employee_name: str = Form(...)
):
    # Save embedding to face_embeddings/{employee_id}.npy
    return {"success": True, "message": "Face registered"}
```

#### 2. POST /recognize
Recognize face dari camera capture
```python
@app.post("/recognize")
async def recognize_face(file: UploadFile):
    # Load image, extract embedding
    # Compare dengan semua embeddings di face_embeddings/
    # Return employee dengan similarity tertinggi
    return {
        "success": True,
        "employee_id": "123456",  # NIK
        "employee_name": "John Doe",
        "similarity": 0.87,
        "confidence": 0.92
    }
```

#### 3. DELETE /delete/{employee_id}
Delete face embedding
```python
@app.delete("/delete/{employee_id}")
async def delete_face(employee_id: str):
    # Delete file face_embeddings/{employee_id}.npy
    return {"success": True}
```

#### 4. GET /health
Health check
```python
@app.get("/health")
async def health_check():
    return {"status": "ok", "message": "Face Recognition API is running"}
```

#### 5. GET /employees
List registered employees
```python
@app.get("/employees")
async def list_employees():
    # List all .npy files in face_embeddings/
    return {"employees": ["123456", "789012"], "count": 2}
```

---

## 📝 Test Flow - Step by Step

### Scenario 1: Register New Employee

1. **Admin Panel:**
   - Login: http://localhost:8000/login (admin@example.com / password)
   - Go to: Employees → Create Employee
   - Input: NIK = "123456", Name = "John Doe"
   - Save

2. **Register Face:**
   - Di list employees, click "Register Face" pada John Doe
   - Upload foto wajah John Doe
   - Submit

3. **Backend Flow:**
   ```
   Laravel → POST /admin/employees/{id}/register-face
   Laravel Controller → FaceRecognitionService::registerFace()
   Service → HTTP POST to Python API http://localhost:8001/register
   Python API → Extract embedding → Save to face_embeddings/123456.npy
   Python API → Response {"success": true}
   Service → Save to DB face_embeddings table (nik, embedding_path)
   Controller → Redirect with success message
   ```

4. **Verify:**
   - Check folder `python-api/face_embeddings/123456.npy` exists
   - Check DB table `face_embeddings` ada row dengan nik "123456"

---

### Scenario 2: Attendance Check-In

1. **Attendance Page:**
   - Buka: http://localhost:8000
   - Allow camera permission
   - Camera feed harus muncul (video terbalik/mirror)

2. **Expected Auto-Detection (BELUM IMPLEMENT!):**
   ```
   ⚠️ CATATAN: Code sekarang TIDAK auto-detect!
   User harus klik SUBMIT untuk capture & recognize.
   ```

3. **Current Flow (Manual Submit):**
   ```
   User → Posisi wajah di kamera
   User → Input jumlah makanan (1-10)
   User → Click SUBMIT button
   JavaScript → Capture frame dari video → Convert to base64
   JavaScript → POST to /checkin with {image: base64, quantity: 1}
   Laravel Controller → Decode base64 → Save temp file
   Controller → Call FaceRecognitionService::recognizeFace(tempFile)
   Service → POST to Python API http://localhost:8001/recognize
   Python API → Load image → Extract embedding
   Python API → Compare dengan semua embeddings di face_embeddings/
   Python API → Find best match (highest similarity)
   Python API → Response {"success": true, "employee_id": "123456", ...}
   Service → Return result ke Controller
   Controller → Check meal_type (getCurrentMealType)
   Controller → Check existing attendance today
   Controller → Save to attendance_logs table
   Controller → Response JSON
   JavaScript → Show modal success
   JavaScript → Reload page
   ```

4. **Verify:**
   - NIK & Nama otomatis terisi (setelah submit)
   - Modal success muncul
   - Table di kanan ada data baru
   - Database table `attendance_logs` ada row baru

---

## 🚨 Common Errors & Solutions

### Error 1: "No active meal time"
**Cause:** Database `meal_time_settings` kosong atau waktu salah

**Solution:**
```bash
# Re-seed database
php artisan migrate:fresh --seed

# Verify
php artisan tinker
MealTimeSetting::all();
```

---

### Error 2: "Wajah tidak dikenali"
**Cause:** 
- Employee belum register face
- Python API tidak running
- Similarity score terlalu rendah

**Solution:**
```bash
# 1. Check Python API running
curl http://localhost:8001/health

# 2. Check employee registered
curl http://localhost:8001/employees
# Should list employee NIK

# 3. Check embedding file exists
ls python-api/face_embeddings/
# Should see 123456.npy
```

---

### Error 3: Camera tidak muncul
**Cause:** Browser tidak dapat akses camera

**Solution:**
1. Browser Settings → Privacy → Camera → Allow localhost
2. Gunakan HTTPS (camera require secure context)
3. Check browser console error: `NotAllowedError`

---

### Error 4: "Failed to connect to Python API"
**Cause:** Python API mati atau port salah

**Solution:**
```bash
# Start Python API
cd python-api-folder
python main.py

# Verify running
curl http://localhost:8001/health
```

---

## 🎬 Complete Testing Checklist

### Pre-requisites:
- [ ] MySQL/PostgreSQL database running
- [ ] Python API running on port 8001
- [ ] Laravel server running on port 8000
- [ ] Browser camera permission granted

### Database:
- [ ] Table `meal_time_settings` has 3 rows
- [ ] Column `is_active` = 1 for all rows
- [ ] Current time falls within one meal range
- [ ] Table `employees` has at least 1 employee
- [ ] Table `face_embeddings` has embedding for that employee

### Python API:
- [ ] Health endpoint responds: `/health`
- [ ] Employees endpoint lists NIKs: `/employees`
- [ ] Embedding file exists: `face_embeddings/{nik}.npy`
- [ ] Recognize endpoint works with test image

### Laravel:
- [ ] Route `/checkin` exists
- [ ] FaceRecognitionService can connect to Python API
- [ ] MealTimeSetting::getCurrentMealType() returns correct meal
- [ ] Attendance page loads without errors

### Browser:
- [ ] Attendance page loads: http://localhost:8000
- [ ] Badge shows correct meal type (not "No active meal time")
- [ ] Camera feed appears
- [ ] Console shows no errors
- [ ] Submit button works

### End-to-End:
- [ ] Position face in camera
- [ ] Set quantity (1-10)
- [ ] Click SUBMIT
- [ ] NIK & Nama auto-filled
- [ ] Modal success appears
- [ ] Page reloads
- [ ] New row appears in attendance table
- [ ] Database `attendance_logs` has new record

---

## 📞 Debugging Commands Summary

```bash
# 1. Check meal time
php artisan tinker
MealTimeSetting::getCurrentMealType();

# 2. Check Python API
curl http://localhost:8001/health
curl http://localhost:8001/employees

# 3. Check Laravel routes
php artisan route:list --name=checkin

# 4. Check Laravel logs
tail -f storage/logs/laravel.log

# 5. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 6. Re-migrate database
php artisan migrate:fresh --seed
```

---

## 🎯 Next Steps

1. **Start Python API** (MOST IMPORTANT!)
2. **Verify meal time settings** in database
3. **Register employee face** via admin panel
4. **Test attendance** on homepage
5. **Check browser console** for errors
6. **Check Laravel logs** for backend errors
7. **Cross-check Python API** responses

---

## 📚 Related Files

- **Model:** `app/Models/MealTimeSetting.php` → `getCurrentMealType()`
- **Controller:** `app/Http/Controllers/AttendanceController.php` → `checkIn()`
- **Service:** `app/Services/FaceRecognitionService.php` → `recognizeFace()`
- **View:** `resources/views/attendance/index.blade.php` → JavaScript capture & submit
- **Migration:** `database/migrations/2025_12_10_000004_create_attendance_logs_table.php`
- **Config:** `config/services.php` → `face_recognition.api_url`

---

**Last Updated:** December 10, 2025 17:45 PM  
**Status:** 🔴 Needs Python API running + Database verification
