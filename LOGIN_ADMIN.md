# 🔐 LOGIN ADMIN - Panduan

## Cara Login Admin

### 1. Jalankan Seeder (Jika Belum)

Jalankan perintah ini untuk membuat user admin:

```bash
php artisan db:seed
```

Atau jika sudah pernah run migration, gunakan:

```bash
php artisan db:seed --force
```

### 2. Akses Halaman Login

Buka browser dan akses:
```
http://localhost:8000/login
```

### 3. Gunakan Kredensial Admin

**Pilihan 1 - Admin Default:**
- **Email**: `admin@example.com`
- **Password**: `password`

**Pilihan 2 - SIMS Admin:**
- **Email**: `admin@sims.com`
- **Password**: `admin123`

### 4. Setelah Login

Setelah berhasil login, Anda akan diarahkan ke:
```
http://localhost:8000/admin/dashboard
```

## 🎯 Fitur Admin yang Tersedia

1. **Dashboard** - Melihat statistik
2. **Employees** - Kelola data karyawan
   - Tambah karyawan baru
   - Edit data karyawan
   - Register face untuk karyawan
   - Hapus karyawan
3. **Meal Times** - Setting waktu makan
   - Atur waktu breakfast (pagi)
   - Atur waktu lunch (siang)
   - Atur waktu dinner (malam)
   - Aktifkan/nonaktifkan waktu makan

## 🔧 Troubleshooting

### Migration Error Saat Seeding

Jika muncul error `is_admin column not found`, jalankan:

```bash
php artisan migrate:fresh --seed
```

⚠️ **PERINGATAN**: Ini akan menghapus semua data di database!

### Lupa Password?

Reset password melalui Tinker:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
$user = User::where('email', 'admin@example.com')->first();
$user->password = Hash::make('password_baru_anda');
$user->save();
exit
```

### User Sudah Ada?

Jika user admin sudah dibuat sebelumnya, seeder akan skip dan tidak membuat duplikat. Seeder menggunakan `firstOrCreate()` untuk menghindari duplikasi.

## 📝 Membuat Admin Baru Secara Manual

### Via Tinker:

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin Baru',
    'email' => 'adminbaru@example.com',
    'password' => Hash::make('password123'),
    'is_admin' => true
]);
exit
```

### Via Database Direct:

```sql
INSERT INTO users (name, email, password, is_admin, created_at, updated_at)
VALUES (
    'Admin Baru',
    'adminbaru@example.com',
    '$2y$12$...',  -- hash dari password
    1,
    NOW(),
    NOW()
);
```

## 🔒 Keamanan

### Ganti Password Default!

**PENTING**: Setelah login pertama kali, segera ganti password default!

Via Tinker:
```bash
php artisan tinker
```

```php
$user = User::where('email', 'admin@example.com')->first();
$user->password = Hash::make('password_baru_yang_kuat');
$user->save();
```

### Password yang Kuat

Gunakan password yang:
- ✅ Minimal 8 karakter
- ✅ Kombinasi huruf besar dan kecil
- ✅ Mengandung angka
- ✅ Mengandung simbol (!@#$%^&*)

## 🚀 Quick Start

```bash
# 1. Run migration dan seeder
php artisan migrate:fresh --seed

# 2. Start server
php artisan serve

# 3. Buka browser
# http://localhost:8000/login

# 4. Login dengan:
# Email: admin@example.com
# Password: password
```

## 📱 URLs Penting

- **Login Page**: http://localhost:8000/login
- **Admin Dashboard**: http://localhost:8000/admin/dashboard
- **Logout**: Klik tombol "Logout" di navbar
- **Home (Attendance)**: http://localhost:8000

## ✅ Checklist Setelah Setup

- [ ] Seeder berhasil dijalankan
- [ ] Bisa login dengan admin@example.com
- [ ] Dashboard admin muncul
- [ ] Bisa akses menu Employees
- [ ] Bisa akses menu Meal Times
- [ ] Password sudah diganti dari default

---

**Butuh bantuan?** Cek file `README_WEB.md` untuk dokumentasi lengkap.
