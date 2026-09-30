# LARASHIZ GENERAL SOFTWARE ENGINEERING & CODE QUALITY RULES

Dokumen ini adalah pedoman fundamental rekayasa perangkat lunak (*Software Engineering Standards*) pada project **Larashiz**. Setiap penulisan, refactoring, dan eksekusi kode oleh AI Agent maupun pengembang **WAJIB** menerapkan prinsip **Clean Architecture** dan **Clean Code** layaknya seorang *Senior Software Engineer*.

---

## 1. Mindset Senior Software Engineer: Think Before You Code

1. **Analisis Mendalam Sebelum Menulis Kode**:
   - Sebelum menyentuh keyboard, pahami domain problem secara menyeluruh. Identifikasi alur data, batasan sistem, *edge cases*, potensi *bottleneck* performa, serta implikasi keamanan.
   - Jangan sekadar fokus pada "yang penting jalan" (*quick-and-dirty hack*). Fokus pada solusi yang kokoh, terstruktur, mudah diuji (*testable*), dan mudah dirawat (*maintainable*) untuk jangka panjang.
2. **Pragmatis, Bukan Over-Engineering (KISS & YAGNI)**:
   - *Keep It Simple, Stupid* (KISS) dan *You Aren't Gonna Need It* (YAGNI).
   - Hindari membuat lapisan abstraksi yang rumit dan tidak diperlukan sebelum ada kebutuhan nyata. Pilih solusi paling sederhana dan elegan yang menyelesaikan masalah secara tuntas.
3. **The Boy Scout Rule**:
   - Selalu tinggalkan kode dalam kondisi yang lebih bersih, rapi, dan terstruktur daripada saat Anda pertama kali membukanya (*leave the code cleaner than you found it*).

---

## 2. Penerapan Clean Architecture & Layered Boundaries

Larashiz menganut pola arsitektur berlapis (*Layered Architecture*) dengan batasan tanggung jawab yang jelas (*Separation of Concerns*):

```
┌─────────────────────────────────────────────────────────┐
│               PRESENTATION LAYER (UI)                   │
│   (Blade Views, Livewire Components, HTTP Controllers)   │
│   - Menerima request/event & input user                 │
│   - Validasi awal boundary                              │
│   - Delegasi ke Service Layer                           │
│   - DILARANG: Query DB kompleks, transaksi, rumus rumit │
└────────────────────────────┬────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│                APPLICATION SERVICE LAYER                │
│             (Service Classes, Actions, Jobs)            │
│   - Pusat orkestrasi alur bisnis (Business Logic)       │
│   - Pengelolaan Transaksi Database (DB::transaction)    │
│   - Eksekusi Standard Logging (INFO, WARN, ERROR)       │
│   - Integrasi external services & fail-safe handling   │
└────────────────────────────┬────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────┐
│              DOMAIN & DATA ACCESS LAYER                 │
│         (Eloquent Models, Enums, Value Objects)         │
│   - Representasi entitas data & relasi                  │
│   - Definisi $fillable & casts eksplisit                │
│   - Query Scopes yang reusable                          │
│   - Integritas data & state validation                  │
└─────────────────────────────────────────────────────────┘
```

### Aturan Batasan Layer:
1. **Controller / Livewire Component Wajib Ramping (Thin Controller)**:
   - Hanya bertugas menerima input pengguna, validasi boundary, memanggil Service, dan mengembalikan respon/tampilan.
   - **DILARANG KERAS** menulis query database kompleks, transaksi beruntun, atau rumus kalkulasi bisnis langsung di dalam Component / Controller.
2. **Service Class Sebagai Pusat Logika Bisnis (Rich Service)**:
   - Seluruh aturan bisnis, orkestrasi mutasi data, transaksi atomik, dan pencatatan audit log **WAJIB** berada di dalam Service Class (contoh: `PostService`, `ActivityLogService`).
3. **Dependency Injection & Inversion of Control (IoC)**:
   - Manfaatkan Laravel Service Container dan *constructor property promotion* PHP 8 untuk menginjeksi dependensi service. Hindari *hard-coded instantiation* (`new SomeClass()`) jika class tersebut memiliki dependensi luar atau perlu di-mock saat testing.

---

## 3. Prinsip Clean Code & Anti-Spaghetti

### A. Keterbacaan adalah Prioritas Tertinggi (Readability is King)
1. **Self-Documenting Code**:
   - Kode dibaca 10x lebih sering daripada ditulis. Tulis kode yang jelas dan ekspresif seperti membaca prosa bahasa Inggris.
   - Nama class, method, dan variabel harus mencerminkan niat dan tujuannya secara akurat (*intention-revealing names*).
   - ✅ **Baik**: `calculateMonthlyRevenue()`, `isSubscriptionActive()`, `getPaginatedActiveUsers()`.
   - ❌ **Buruk**: `calc()`, `chk()`, `data()`, `temp()`, `flag1`, `doProcess()`.
2. **Hindari "Clever Code"**:
   - Jangan menulis one-liner yang rumit hanya untuk terlihat pintar. Lebih baik kode 3 baris yang eksplisit dan mudah dibaca daripada 1 baris rumit yang sulit di-debug.

### B. Anti-Spaghetti & Anti-God Class
1. **Single Responsibility Principle (SRP)**:
   - Satu fungsi atau class hanya boleh memiliki **satu alasan untuk berubah**.
   - Jika sebuah fungsi melakukan validasi, menghitung harga, mengirim email, menyimpan ke DB, dan mencatat log sekaligus, fungsi tersebut **wajib dipecah**.
2. **Fungsi Terfokus & Pendek**:
   - Panjang sebuah method idealnya berkisar antara 10–30 baris.
   - Terapkan *Single Level of Abstraction Principle* (SLAP): Satu method tidak boleh mencampur detail teknis tingkat rendah (seperti manipulasi string regex) dengan alur bisnis tingkat tinggi.
3. **Guard Clauses & Early Return (Max 2 Nesting Level)**:
   - Hindari struktur `if/else` bertingkat yang dalam (*arrow anti-pattern / pyramid of doom*).
   - Periksa kondisi invalid di awal fungsi, lalu langsung `return` atau lempar `Exception` (*fail-fast*).
   - ✅ **Pola Senior (Guard Clause)**:
     ```php
     public function publishPost(Post $post, User $user): bool
     {
         if (! $user->can('publish', $post)) {
             throw new UnauthorizedException('User cannot publish this post.');
         }

         if ($post->isPublished()) {
             return true;
         }

         return $this->executePublish($post);
     }
     ```
   - ❌ **Pola Spaghetti (Deep Nesting)**:
     ```php
     public function publishPost($post, $user)
     {
         if ($user) {
             if ($user->role == 'admin') {
                 if ($post->status != 1) {
                     // logic bertumpuk 5 level...
                 }
             }
         }
     }
     ```

### C. Bebas Magic Numbers & Magic Strings
- Dilarang keras menanam angka atau string misterius tanpa konteks di tengah logika.
- Wajib menggunakan **PHP 8.1+ Enums** bertipe (*backed enums*) atau konstanta class.
- ✅ **Baik**: `if ($post->status === PostStatus::PUBLISHED)` atau `self::MAX_LOGIN_ATTEMPTS`.
- ❌ **Buruk**: `if ($post->status == 1)` atau `if ($retries > 5)`.

### D. Zero Dead Code & Kebersihan Komentar
1. **Dilarang Meninggalkan Dead Code**:
   - Jangan pernah meninggalkan blok kode yang di-comment out di dalam repositori (*"just in case"*). Kita memiliki Git Version Control untuk melihat riwayat perubahan.
2. **Komentar Hanya untuk "Why", Bukan "What"**:
   - Jangan menulis komentar yang hanya mengulang apa yang ditulis oleh baris kode.
   - Komentar hanya diperbolehkan untuk menjelaskan *alasan bisnis yang tidak lazim*, *keputusan arsitektur khusus*, atau *workaround teknis pihak ketiga*.

---

## 4. Standar Implementasi CRUD Acuan Baku (Gold Standard: Modul Post)

Setiap pembuatan atau modifikasi fitur CRUD (Create, Read, Update, Delete) di aplikasi **Larashiz WAJIB** mencontoh dan mengikuti pola baku yang sudah terbukti solid pada **Modul Post**:

```
                               POLA ARSITEKTUR CRUD BAKU (POST MODULE)
 ┌──────────────────────────────────────────────────────────────────────────────────────┐
 │ 1. LIVEWIRE COMPONENT: app/Livewire/Posts/Index.php                                   │
 │    - Mengelola properti reaktif ($search, $status, $perPage, $sortField, $sortDir)   │
 │    - Menangani buka/tutup modal Form, Detail, dan Filter                             │
 │    - Validasi boundary Livewire Form ($this->validate())                             │
 │    - Menerima event hapus (#[On('delete-post')]) & trigger SweetAlert2 feedback      │
 │    - Delegasi seluruh query & eksekusi ke PostService via Dependency Injection       │
 ├──────────────────────────────────────────────────────────────────────────────────────┤
 │ 2. SERVICE LAYER: app/Services/PostService.php                                        │
 │    - Mengenkapsulasi query pencarian, filter status, pengurutan, & paginate()        │
 │    - Membungkus createPost, updatePost, deletePost dalam DB::transaction             │
 │    - Mencatat ActivityLogService (INFO jika sukses, ERROR jika Throwable tertangkap) │
 ├──────────────────────────────────────────────────────────────────────────────────────┤
 │ 3. ELOQUENT MODEL: app/Models/Post.php                                                │
 │    - Definisi atribut eksplisit (#[Fillable(['title', 'slug', 'body', ...])])        │
 │    - Relasi User (belongsTo) & Casts otomatis                                        │
 ├──────────────────────────────────────────────────────────────────────────────────────┤
 │ 4. VIEW & MODAL MODULAR: resources/views/livewire/posts/                              │
 │    - index.blade.php       : Kontainer tabel, controls search/perPage, pagination    │
 │    - form-modal.blade.php  : Modal dialog Create / Edit dengan real-time error       │
 │    - detail-modal.blade.php: Modal dialog View Detail Data                           │
 │    - filter-modal.blade.php: Modal dialog Filter Khusus Mobile Responsif             │
 ├──────────────────────────────────────────────────────────────────────────────────────┤
 │ 5. AUTOMATED FEATURE TEST: tests/Feature/PostCrudTest.php                             │
 │    - Menguji Create, Read, Update, Delete, Validation, Search, Filter, & Pagination  │
 │    - Menggunakan DatabaseTransactions pada database isolasi (laravel_test)           │
 └──────────────────────────────────────────────────────────────────────────────────────┘
```

### File-File Referensi Modul Post:
- **Component**: [`app/Livewire/Posts/Index.php`](file:///var/www/projects/laravel/larashiz/app/Livewire/Posts/Index.php)
- **Service**: [`app/Services/PostService.php`](file:///var/www/projects/laravel/larashiz/app/Services/PostService.php)
- **Model**: [`app/Models/Post.php`](file:///var/www/projects/laravel/larashiz/app/Models/Post.php)
- **Main View**: [`resources/views/livewire/posts/index.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/livewire/posts/index.blade.php)
- **Form Modal**: [`resources/views/livewire/posts/form-modal.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/livewire/posts/form-modal.blade.php)
- **Detail Modal**: [`resources/views/livewire/posts/detail-modal.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/livewire/posts/detail-modal.blade.php)
- **Filter Modal**: [`resources/views/livewire/posts/filter-modal.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/livewire/posts/filter-modal.blade.php)
- **Feature Tests**: [`tests/Feature/PostCrudTest.php`](file:///var/www/projects/laravel/larashiz/tests/Feature/PostCrudTest.php)

---

## 5. Protokol Anti-Halusinasi & Grounding (Fact-Checking)

Untuk mencegah kesalahan asumsi, sintaks fiktif, nama kolom salah, atau klaim sepihak:

1. **Database Schema Grounding (Cek Fakta Skema DB)**:
   - Dilarang menebak nama kolom. Selalu periksa file migrasi di `database/migrations/` sebelum menulis query, model attribute, atau validasi.
2. **API & Package Version Grounding (Cek Versi Dependensi)**:
   - Dilarang mengasumsikan method/fungsi dari package luar tersedia tanpa mengecek versi yang terpasang di `composer.json` atau `package.json`.
3. **Reuse Existing Components (Cek Komponen Eksisting)**:
   - Jangan membuat helper, modal, atau service baru jika komponen serupa sudah tersedia di project. Selalu periksa folder *sibling* sebelum membuat file baru.
4. **Named Routes Only (Gunakan Route Terdaftar)**:
   - Selalu gunakan `route('nama.route')`. Pastikan nama route benar-benar ada di `routes/web.php` atau `routes/auth.php`.
5. **Zero Silent Modification (Disiplin Ruang Lingkup)**:
   - Dilarang mengubah dependensi, konfigurasi environment, atau skema tabel di luar instruksi tanpa persetujuan eksplisit.
6. **Mandatory Proof of Execution (Bukti Eksekusi Nyata)**:
   - Dilarang menyatakan fitur selesai tanpa menjalankan:
     1. `vendor/bin/pint --format agent` (bebas error styling).
     2. `php artisan test --filter="NamaTest"` (test passing 100%).
     3. `npm run build:shizuefi` (jika ada perubahan file CSS).

---

## 6. Standar Pengujian & Verifikasi Kualitas

1. **No Code without Quality Proof**:
   - Setiap fitur baru atau perbaikan bug pada logika bisnis **wajib** disertai Automated Tests (Feature / Unit Test) yang memvalidasi skenario positif dan *failure modes*.
2. **Verifikasi Tooling & Linting Otomatis**:
   - File PHP **wajib lolos linter**: `vendor/bin/pint --format agent`.
   - File CSS/Frontend **wajib dikompilasi**: `npm run build:shizuefi`.
   - Test suite **wajib hijau**: `php artisan test --filter="TestClass"`.
