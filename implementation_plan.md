# Rencana Implementasi: Login Dokter SSO/AD & Pembatasan Data Pasien

Memberikan tampilan data pasien yang dibatasi (scoped) bagi dokter setelah login melalui SSO/Active Directory. Ketika seorang dokter masuk ke sistem, aplikasi akan mengidentifikasi kode dokter mereka (`kd_dokter`) dan memfilter daftar pasien, statistik, serta dashboard agar hanya menampilkan pasien yang ditangani oleh dokter tersebut. Akun administrator tetap dapat melihat semua data pasien secara keseluruhan.

## Review Pengguna Diperlukan

> [!IMPORTANT]
> **Metode Pemetaan (Mapping) Akun AD ke Dokter**
> Kita perlu menentukan bagaimana akun Active Directory dihubungkan dengan tabel `dokter` di database. Ada tiga opsi yang diusulkan:
> 
> 1. **Opsi A (Username AD = kd_dokter)**: Username SSO/AD cocok langsung dengan kolom `dokter.kd_dokter` (contoh: username AD `100185` terpetakan ke `kd_dokter = '100185'`). *Direkomendasikan jika username AD sudah dikonfigurasi menggunakan nomor induk dokter/karyawan.*
> 2. **Opsi B (Email AD = Email Dokter)**: Pemetaan melalui alamat email. Jika akun AD memiliki atribut email yang cocok dengan `dokter.email`, maka akun tersebut akan terhubung.
> 3. **Opsi C (Tabel Pemetaan Kustom di DB)**: Membuat tabel pemetaan baru di database (misalnya `sso_doctor_mapping` dengan kolom `username_sso` dan `kd_dokter`). Opsi ini paling fleksibel karena tidak mengubah struktur tabel bawaan SIMRS Khanza dan mendukung username AD dengan format apa pun.

> [!WARNING]
> **Batasan Metrik Dashboard**
> Ketika dokter login, semua kartu metrik di dashboard (Total Pasien, Rawat Jalan, Rawat Inap, Resep Farmasi, dan Aktivitas Terbaru) akan difilter hanya untuk pasien milik dokter tersebut. Administrator akan tetap melihat metrik rumah sakit secara global. Mohon konfirmasi apakah perilaku dashboard ini sudah sesuai dengan kebutuhan Anda.

## Pertanyaan Terbuka

> [!NOTE]
> 1. Opsi Pemetaan SSO/AD mana (A, B, atau C) yang paling sesuai dengan konfigurasi server LDAP/AD Anda?
> 2. Untuk Rawat Jalan dan Rawat Inap, apakah dokter juga harus dapat melihat pasien di mana mereka ditetapkan sebagai **DPJP Tambahan** (dokter pendamping pada tabel `reg_dpjp_tambahan`)? Ataukah cukup menyaring berdasarkan DPJP Utama (`reg_periksa.kd_dokter`) saja?

---

## Rencana Perubahan Kode

### 1. Lapisan Autentikasi (Authentication Layer)

Mengidentifikasi kode dokter setelah berhasil terhubung (bind) dengan SSO/AD dan menyimpannya ke dalam session user.

#### [MODIFY] [AuthController.php](file:///C:/laragon/www/khanza/app/Http/Controllers/AuthController.php)
- Mengambil atribut AD (seperti username, email, atau ID karyawan) setelah proses login LDAP berhasil.
- Melakukan kueri ke tabel `dokter` menggunakan metode pemetaan yang dipilih (misal: mencari `dokter.kd_dokter = $username`).
- Jika data dokter ditemukan, tambahkan `kd_dokter` ke objek session user:
  ```php
  $user->kd_dokter = $doctor->kd_dokter;
  ```

---

### 2. Dashboard Filter

Membatasi metrik halaman utama dashboard dan aktivitas terbaru sesuai dengan dokter yang sedang login.

#### [MODIFY] [DashboardController.php](file:///C:/laragon/www/khanza/app/Http/Controllers/DashboardController.php)
- Memeriksa apakah `session('user')->kd_dokter` tersedia.
- Jika ada, tambahkan klausa `->where('kd_dokter', $kd_dokter)` (atau `reg_periksa.kd_dokter` di kueri yang sesuai) pada semua penghitung metrik:
  - Total Pasien
  - Rawat Jalan (ralan)
  - Rawat Inap (ranap)
  - Resep Obat (farmasi)
  - Aktivitas Terbaru (recent activities)

---

### 3. Rawat Jalan (Outpatient) Filter

Membatasi daftar pasien rawat jalan terdaftar agar hanya memunculkan pasien milik dokter yang bersangkutan.

#### [MODIFY] [RawatJalanController.php](file:///C:/laragon/www/khanza/app/Http/Controllers/RawatJalanController.php)
- Memeriksa apakah `session('user')->kd_dokter` tersedia pada method `index`.
- Jika ada, filter daftar antrian pasien:
  ```php
  $antrian->where('reg_periksa.kd_dokter', $kd_dokter);
  ```
  *(Atau menyertakan pengecekan tabel `reg_dpjp_tambahan` jika dokter pendamping juga harus bisa melihat pasien tersebut)*
- Filter juga kueri statistik harian (`$stats`) berdasarkan `kd_dokter`.

---

### 4. Rawat Inap (Inpatient) Filter

Membatasi daftar pasien rawat inap yang sedang dirawat sesuai dokter penanggung jawab.

#### [MODIFY] [RawatInapController.php](file:///C:/laragon/www/khanza/app/Http/Controllers/RawatInapController.php)
- Memeriksa apakah `session('user')->kd_dokter` tersedia pada method `index`.
- Jika ada, filter kueri rawat inap:
  ```php
  $query->where('reg_periksa.kd_dokter', $kd_dokter);
  ```

---

## Rencana Verifikasi

### Verifikasi Manual
1. **Tampilan Admin**:
   - Login menggunakan akun administrator (tanpa pemetaan `kd_dokter`).
   - Pastikan seluruh pasien di Dashboard, Rawat Jalan, dan Rawat Inap tetap terlihat semuanya tanpa filter.
2. **Tampilan Dokter**:
   - Login menggunakan akun AD test yang terpetakan ke salah satu kode dokter (misalnya `kd_dokter = '10'`).
   - Pastikan hanya pasien yang terdaftar di bawah kode dokter `10` yang muncul di halaman Rawat Jalan dan Rawat Inap.
   - Pastikan statistik Dashboard hanya menjumlahkan data pasien milik dokter `10`.
