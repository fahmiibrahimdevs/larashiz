# Larashiz - Laravel Admin Starter with Shizuefi Template

Larashiz adalah starter kit Laravel 12 modern yang mengintegrasikan **Livewire 3 (Volt)**, **Laratrust (Role & Permission)**, dan template admin **SHIZUEFI** berbasis **Tailwind CSS**.

---

## 🚀 Fitur Utama

- **Authentication & User Management**:
  - Laravel Breeze + Livewire Volt.
  - Multi-role support (Admin & User) menggunakan **Laratrust**.
  - **Status Akun Aktif / Non-Aktif**: User baru terdaftar berstatus `Inactive` dan harus diaktifkan terlebih dahulu oleh Admin.
  - Halaman Manajemen User khusus Admin untuk mengaktifkan/menonaktifkan akun.
- **SHIZUEFI Admin UI Template**:
  - 82 halaman demo lengkap siap pakai diubah ke Blade template (`/shizuefi/...`).
  - Layout terstruktur: Default Layout, Transparent Sidebar, & Top Navigation.
  - Styling modular menggunakan **Tailwind CSS**.
- **Standar Asset & Build System**:
  - Standalone Tailwind CSS v4 CLI compiler super cepat (<200ms).
  - Script build dan watch otomatis (`npm run build:shizuefi` & `npm run watch:shizuefi`).

---

## 🔑 Akun Default (Seeder)

Setelah menjalankan database seeder, akun default berikut siap digunakan:

| Role | Email | Password | Status |
| :--- | :--- | :--- | :--- |
| **Admin** | `fahmi@admin.com` | `1` | `Active` (Dapat langsung login & mengelola user) |
| **User** | `fahmi@user.com` | `1` | `Inactive` (Perlu diaktifkan oleh admin) |

---

## 🛠️ Instalasi & Setup

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone https://github.com/fahmiibrahimdevs/larashiz.git
cd larashiz

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment & Database Setup

```bash
# Copy file environment
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed
```

### 3. Menjalankan Aplikasi

Jalankan Laravel dev server:
```bash
php artisan serve
```
Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

## 🎨 Customizing Tailwind CSS & Komponen Shizuefi

Source file Tailwind CSS Shizuefi berada di folder `public/assets/css/`:

| Source File | Keterangan & Fungsi |
| :--- | :--- |
| `public/assets/css/components-tailwind.css` | Komponen UI Shizuefi (misal: `.card`, `.navbar`, `.main-sidebar`, `.badge`, `.table`, `.article`, modal, dsb.) |
| `public/assets/css/style-tailwind.css` | Styling global, warna tema (`.bg-primary`, `.text-primary`), tombol (`.btn`), form, typography, layout |
| `public/assets/css/bootstrap-tailwind.css` | Sistem grid (`.row`, `.col-*`) dan utilitas Bootstrap berbasis Tailwind |

### Cara Custom Komponen / Utility
Buka salah satu source CSS di atas, lalu tambahkan utility class menggunakan `@apply`:
```css
/* Contoh custom styling */
.btn-custom {
  @apply px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-md transition-all duration-200;
}
```

### Command Compile CSS

- **Compile Sekali (Build)**:
  ```bash
  npm run build:shizuefi
  ```
- **Auto-Compile saat Development (Watch Mode)**:
  ```bash
  npm run watch:shizuefi
  ```

File hasil compile akan otomatis disimpan ke:
- `public/assets/css/style-tailwind.compiled.css`
- `public/assets/css/components-tailwind.compiled.css`
- `public/assets/css/bootstrap-tailwind.compiled.css`

---

## 📂 Struktur Asset & `public/node_modules`

### Mengapa ada `public/node_modules`?
- Seluruh package `npm` sebenarnya **100% tersimpan di root project** (`/node_modules`).
- `public/node_modules` **bukan folder duplikat**, melainkan **Symbolic Link (shortcut/pointer)** ke folder `../node_modules` di root yang otomatis dibuat oleh script `postinstall` saat Anda menjalankan `npm install`.
- **Alasannya**: Web server (Nginx/Apache/`php artisan serve`) hanya melayani file publik dari dalam folder `public/`. Symlink ini memungkinkan halaman demo Shizuefi memanggil library (seperti jQuery, Chart.js, SweetAlert, Select2) langsung dari root `node_modules` tanpa memakan ruang disk tambahan (0 byte).

---

## 📄 Lisensi

Proyek ini berada di bawah lisensi [MIT License](LICENSE).