# AGENT KNOWLEDGE BASE & SYSTEM DOCUMENTATION

> **Panduan & Dokumentasi Arsitektur Codebase `larashiz`**  
> Dokumen ini dirancang khusus sebagai referensi menyeluruh untuk agen AI / developer agar dapat langsung memahami struktur, dependensi, konvensi kode, alur autentikasi, serta sistem otorisasi tanpa perlu melakukan pelatihan ulang (onboarding) dari awal.

---

## 📌 Ringkasan Proyek

- **Nama Aplikasi**: `larashiz` (Laravel Application)
- **Framework Core**: [Laravel 13.x](file:///var/www/projects/laravel/larashiz/composer.json) (`laravel/framework: ^13.17`)
- **PHP Version**: `^8.3` (kompatibel hingga PHP 8.5)
- **Frontend Stack**: 
  - [Livewire v4.4](file:///var/www/projects/laravel/larashiz/composer.json) (`livewire/livewire`)
  - [Livewire Volt v1.7](file:///var/www/projects/laravel/larashiz/composer.json) (`livewire/volt`)
  - [Tailwind CSS v3/v4](file:///var/www/projects/laravel/larashiz/tailwind.config.js) & [Alpine.js](file:///var/www/projects/laravel/larashiz/resources/views/layouts/app.blade.php) (built-in dengan Livewire)
  - [Vite 8](file:///var/www/projects/laravel/larashiz/vite.config.js) (`laravel-vite-plugin`)
- **Starter Kit**: [Laravel Breeze](file:///var/www/projects/laravel/larashiz/composer.json) (Volt / Livewire Edition)
- **Role & Permission (RBAC)**: [Laratrust v8.5](file:///var/www/projects/laravel/larashiz/config/laratrust.php) (`santigarcor/laratrust`)
- **Database Default**: SQLite (`database/database.sqlite`), support MySQL/PostgreSQL

---

## 🗂️ Struktur Direktori & Komponen Inti

```
larashiz/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   └── VerifyEmailController.php    # Verifikasi email controller
│   │       └── Controller.php                   # Base controller
│   ├── Livewire/
│   │   ├── Actions/
│   │   │   └── Logout.php                       # Action logic untuk logout session
│   │   └── Forms/
│   │       └── LoginForm.php                    # Form object autentikasi Livewire Volt
│   ├── Models/
│   │   ├── Permission.php                       # Laratrust Permission Model
│   │   ├── Role.php                             # Laratrust Role Model
│   │   └── User.php                             # User Model (LaratrustUser + HasRolesAndPermissions)
│   ├── Providers/
│   │   └── AppServiceProvider.php               # Provider konfigurasi global
│   └── View/
│       └── Components/                          # Layout components (AppLayout, GuestLayout)
├── bootstrap/
│   ├── app.php                                  # Konfigurasi routing, middleware & exceptions
│   └── providers.php                            # List service providers
├── config/
│   ├── app.php, auth.php, database.php          # Konfigurasi core Laravel
│   ├── laratrust.php                            # Konfigurasi tabel, relasi, cache, & middleware Laratrust
│   ├── laratrust_seeder.php                     # Blueprint roles & permissions seeder
│   └── livewire.php                             # Konfigurasi Livewire & Volt
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── 2026_09_29_023948_laratrust_setup_tables.php # Migrasi tabel RBAC Laratrust
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── css/app.css                              # Tailwind CSS entry
│   ├── js/app.js                                # JS entry
│   └── views/
│       ├── components/                          # Blade UI components (button, input, modal, dropdown, etc.)
│       ├── layouts/
│       │   ├── app.blade.php                    # Layout utama user yang terotentikasi
│       │   └── guest.blade.php                  # Layout halaman guest (login, register)
│       ├── livewire/
│       │   ├── layout/navigation.blade.php      # Topbar navigasi & profile dropdown
│       │   ├── pages/auth/                      # Volt Single-file components untuk Auth
│       │   │   ├── login.blade.php
│       │   │   ├── register.blade.php
│       │   │   ├── forgot-password.blade.php
│       │   │   ├── reset-password.blade.php
│       │   │   ├── confirm-password.blade.php
│       │   │   └── verify-email.blade.php
│       │   └── profile/                         # Form update profile, password, hapus user
│       ├── dashboard.blade.php                  # Halaman dashboard utama
│       ├── profile.blade.php                    # Halaman profil user
│       └── welcome.blade.php                    # Landing page publik
├── routes/
│   ├── auth.php                                 # Route autentikasi berbasis Livewire Volt
│   ├── console.php                              # Artisan console commands/schedules
│   └── web.php                                  # Route web utama aplikasi
├── tests/
│   ├── Feature/Auth/                            # Pengujian alur otentikasi lengkap
│   └── Feature/ProfileTest.php                  # Pengujian update & delete profil
└── docs/
    └── AGENT.md                                 # Panduan ini
```

---

## 🔐 Sistem Autentikasi & Otorisasi (RBAC)

### 1. Model User & Laratrust Traits
Model [`User`](file:///var/www/projects/laravel/larashiz/app/Models/User.php) menggunakan PHP 8 Attribute syntax dan mengimplementasikan contract `LaratrustUser`:
```php
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements LaratrustUser
{
    use HasFactory, Notifiable, HasRolesAndPermissions;
    ...
}
```

### 2. Model Role & Permission
- [`App\Models\Role`](file:///var/www/projects/laravel/larashiz/app/Models/Role.php): Meng-extends `Laratrust\Models\Role`.
- [`App\Models\Permission`](file:///var/www/projects/laravel/larashiz/app/Models/Permission.php): Meng-extends `Laratrust\Models\Permission`.

### 3. Skema Tabel Database Laratrust
Migrasi [`2026_09_29_023948_laratrust_setup_tables.php`](file:///var/www/projects/laravel/larashiz/database/migrations/2026_09_29_023948_laratrust_setup_tables.php) mengelola:
- `roles` (`id`, `name`, `display_name`, `description`, `timestamps`)
- `permissions` (`id`, `name`, `display_name`, `description`, `timestamps`)
- `role_user` (relasi polymorphic many-to-many user <-> role)
- `permission_user` (relasi polymorphic user <-> permission)
- `permission_role` (relasi permission <-> role)

### 4. Penggunaan Role & Permission dalam Kode

#### A. Di Controller / Class PHP:
```php
// Cek Role
$user->hasRole('administrator'); // true/false
$user->hasRole(['administrator', 'superadministrator']); // salah satu match

// Cek Permission
$user->hasPermission('users-create'); // true/false

// Assign Role & Permission
$user->addRole('administrator');
$user->removeRole('administrator');
$user->givePermission('users-create');
$user->syncRoles(['administrator']);
```

#### B. Di Route Middleware ([`routes/web.php`](file:///var/www/projects/laravel/larashiz/routes/web.php)):
```php
// Proteksi route berdasarkan Role
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'role:administrator|superadministrator']);

// Proteksi route berdasarkan Permission
Route::get('/users/create', [UserController::class, 'create'])
    ->middleware(['auth', 'permission:users-create']);
```

#### C. Di Template Blade ([`resources/views`](file:///var/www/projects/laravel/larashiz/resources/views)):
```blade
@role('administrator')
    <a href="/admin">Menu Admin</a>
@endrole

@permission('users-create')
    <button>Tambah User Baru</button>
@endpermission

@ability('superadministrator,administrator', 'users-create,users-update')
    <!-- Ditampilkan jika user memiliki role ATAU permission yang cocok -->
@endability
```

---

## ⚡ Arsitektur Livewire & Volt

Aplikasi ini menggunakan **Livewire 4** dengan **Volt** (komponen single-file fungsional & class-based).

- **Volt Route Handling**:
  Rute autentikasi didefinisikan menggunakan `Volt::route()` di [`routes/auth.php`](file:///var/www/projects/laravel/larashiz/routes/auth.php).
- **Struktur Komponen Volt**:
  Template dan state logic berada dalam satu file blade di `resources/views/livewire/pages/auth/` atau `resources/views/livewire/profile/`.
  
  *Contoh pola Volt component:*
  ```blade
  <?php
  use Livewire\Volt\Component;
  use Livewire\Attributes\Validate;

  new class extends Component {
      #[Validate('required|string|email')]
      public string $email = '';
      
      public function submit(): void {
          $this->validate();
          // Logic eksekusi
      }
  }; ?>

  <div>
      <input type="email" wire:model="email" />
      <button wire:click="submit">Kirim</button>
  </div>
  ```

- **SPA Navigation**:
  Menggunakan fitur `wire:navigate` di link-link navigasi ([`resources/views/livewire/layout/navigation.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/livewire/layout/navigation.blade.php)) untuk transisi halaman cepat tanpa full reload.

---

## ⚙️ Perintah Artisan & Development Penting

### 1. Menjalankan Server & Asset Watcher
```bash
# Menjalankan PHP local server
php artisan serve

# Menjalankan Vite dev server
npm run dev

# Menjalankan build asset frontend produksi
npm run build
```

### 2. Database & Migrasi
```bash
# Menjalankan migrasi
php artisan migrate

# Rollback dan jalankan ulang semua migrasi
php artisan migrate:fresh

# Menjalankan seeder
php artisan db:seed
```

### 3. Laratrust RBAC Setup
Jika ingin membuat Role / Permission baru via Artisan:
```bash
# Membuat Role baru
php artisan laratrust:role <role_name>

# Membuat Permission baru
php artisan laratrust:permission <permission_name>

# Membuat Seeder Laratrust (jika ingin meregenerasi dari config/laratrust_seeder.php)
php artisan make:seeder LaratrustSeeder
```

### 4. Testing & Code Styling
```bash
# Menjalankan test suite (Pest / PHPUnit)
php artisan test

# Format kode otomatis menggunakan Laravel Pint
./vendor/bin/pint
```

---

## 📝 Konvensi & Pedoman Coding untuk Agen / Developer

1. **PHP 8.3+ Features**:
   - Manfaatkan typed properties, constructor property promotion, return types, dan PHP 8 attributes (seperti `#[Fillable]`, `#[Hidden]`, `#[Validate]`).
2. **Strict Typing & Modern Syntax**:
   - Selalu berikan type hint eksplisit pada parameter fungsi dan return value (`void`, `array`, `string`, `bool`, etc.).
3. **Penyimpanan State & Interaktivitas**:
   - Gunakan Livewire Volt untuk form dan komponen interaktif.
   - Gunakan Alpine.js untuk manipulasi UI sederhana di sisi client (dropdowns, modals, toggles).
4. **Keamanan & Otorisasi**:
   - Jangan pernah mengabaikan otorisasi: selalu manfaatkan middleware Laratrust (`role`, `permission`) pada route sensitif, atau cek `$user->hasPermission()` sebelum mutasi database.
5. **UI & Styling**:
   - Gunakan utility classes Tailwind CSS yang konsisten dengan tema yang sudah ada (`resources/views/components/`).
   - Pertahankan konsistensi desain yang elegan, bersih, dan responsif.
