t  # 🔄 MIGRASI DATABASE BARU

## ⚠️ PENTING - Database Structure Changed!

Database structure telah diubah dari `employee_id` menjadi `nik`. Anda perlu melakukan migration ulang.

## 🗑️ Drop & Recreate Database

### Cara 1: Via Artisan (Recommended)

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
1. Drop semua tabel
2. Jalankan semua migration dari awal
3. Jalankan seeder (create admin user)

### Cara 2: Manual via MySQL

```bash
# Login ke MySQL
mysql -u root -p

# Drop dan create database
DROP DATABASE IF EXISTS face_recognition_db;
CREATE DATABASE face_recognition_db;
exit

# Jalankan migration
php artisan migrate --seed
```

## 📊 Perubahan Structure

### Tabel `employees` (SEBELUM)
- id
- employee_id (unique)
- name
- email (nullable)
- phone (nullable)
- department (nullable)
- position (nullable)
- is_active
- timestamps

### Tabel `employees` (SESUDAH)
- id
- **nik** (unique) ✨ BARU!
- name
- is_active
- timestamps

### Tabel Lainnya
- `face_embeddings`: `employee_id` → `nik`
- `attendance_logs`: `employee_id` → `nik`

## ✅ Verifikasi

Setelah migration, cek dengan:

```bash
php artisan tinker
```

```php
// Cek employees
Employee::count();

// Cek admin user
User::where('is_admin', true)->get();

// Cek meal time settings
MealTimeSetting::all();
```

## 🎯 Langkah Lengkap Setup Ulang

```bash
# 1. Drop dan recreate database
php artisan migrate:fresh --seed

# 2. Build assets (jika belum)
npm run build

# 3. Start Laravel server
php artisan serve

# 4. Start Python API (di folder lain)
# cd path/to/python/api
# python main.py
```

## 📝 Default Admin Login

Setelah seeding:
- **Email**: admin@example.com
- **Password**: password

atau

- **Email**: admin@sims.com
- **Password**: admin123

## 🔧 Jika Ada Error

### Error: "Column not found"
Jalankan fresh migration:
```bash
php artisan migrate:fresh --seed
```

### Error: "Table doesn't exist"
Pastikan database sudah dibuat:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS face_recognition_db;"
php artisan migrate --seed
```

### Error: "SQLSTATE[HY000] [2002]"
Pastikan MySQL/PostgreSQL service running:
```bash
# Windows
net start MySQL80

# Linux
sudo service mysql start
```

## 💡 Tips

1. **Backup Data Lama**: Jika ada data penting, backup dulu sebelum drop database
2. **Python API**: Pastikan Python API juga sudah update untuk gunakan `nik` instead of `employee_id`
3. **Face Embeddings**: Data face embeddings lama akan hilang, perlu register ulang

## 🎉 Setelah Setup

1. Login ke admin: http://localhost:8000/login
2. Tambah employee baru (hanya NIK & Nama)
3. Register face untuk employee
4. Test attendance di homepage

---

**Need Help?** Check `SETUP_GUIDE.md` untuk dokumentasi lengkap.
