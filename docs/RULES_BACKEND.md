# LARASHIZ BACKEND & ARCHITECTURE GUIDELINES

Dokumen ini adalah acuan standar baku pengembangan backend, arsitektur layer, perancangan skema database, indexing, transaksi, validasi keamanan, logging, optimasi performa query, dan konvensi kode PHP pada project **Larashiz**. Setiap penulisan dan eksekusi kode backend **WAJIB** mematuhi pedoman di bawah ini.

---

## 1. Standar Perancangan Skema Database & Evaluasi Indexing (Wajib Sebelum Eksekusi)

Sebelum menulis atau mengeksekusi migrasi database (`php artisan migrate`), **WAJIB** mengevaluasi kebutuhan indexing dan efisiensi query masa depan:

1. **Analisis Pola Akses & Kebutuhan Index**:
   - Setiap kolom yang digunakan untuk penyaringan (`WHERE`), pengurutan (`ORDER BY`), pengelompokan (`GROUP BY`), atau relasi (`FOREIGN KEY`) **wajib di-index**.
   - Kolom yang sering dicari secara teks/pencarian wajib dipertimbangkan index spesifik (misal: `index()`, `fulltext()`, atau prefix index).
2. **Strategi Composite Index (Multi-Column Index)**:
   - Jika terdapat query yang menyaring atau mengurutkan beberapa kolom sekaligus secara rutin, buat **Composite Index**.
   - Perhatikan aturan *Left-to-Right / Index Prefix*: tempatkan kolom dengan selektivitas tertinggi (kardinalitas tinggi / filter spesifik) di urutan pertama.
   - Contoh nyata di Larashiz:
     - `activity_logs`: index `['module', 'action']`, `['level', 'created_at']`, `['user_id', 'created_at']`, `['request_id']`.
     - `posts`: index `['status', 'published_at']`, `['user_id', 'status']`.
3. **Integritas Relasi & Foreign Key Constraints**:
   - Seluruh relasi antar tabel wajib didefinisikan dengan *foreign key constraint* yang jelas beserta aturan penghapusan yang aman (`onDelete('cascade')`, `nullOnDelete()`, atau `restrictOnDelete()`).
4. **Unique Constraints untuk Mencegah Race Conditions**:
   - Data yang harus unik secara bisnis (misal: `email`, `slug`, kombinasi `user_id` + `role_id`) **wajib** dilindungi oleh `unique()` constraint di level database, bukan hanya mengandalkan validasi aplikasi.
5. **Kardinalitas & Biaya Indexing (Index Cost Awareness)**:
   - Hindari over-indexing pada kolom dengan kardinalitas sangat rendah (misal kolom boolean murni) kecuali jika menjadi bagian dari composite index.
   - Ingat bahwa setiap index mempercepat query `SELECT`, tetapi menambah beban pada operasi `INSERT`, `UPDATE`, dan `DELETE`. Evaluasi secara seimbang.

---

## 2. Standar Transaksi Database (ACID & Data Integrity)

1. **Wajib Menggunakan `DB::transaction()` untuk Operasi Multi-Step**:
   - Setiap alur bisnis yang memutasi lebih dari satu baris atau tabel database (misal: simpan post + rekam audit log, create user + assign role, mutasi saldo/pesanan) **WAJIB** dibungkus dalam `DB::transaction(function () { ... })`.
   - Jika terjadi *unhandled exception* atau kegagalan di tengah proses, seluruh perubahan database akan di-*rollback* otomatis secara atomik sehingga database tidak pernah berada dalam kondisi data yatim (*corrupted / partial state*).
   - ✅ **Contoh Pola Transaksi Senior**:
     ```php
     return DB::transaction(function () use ($data, $user) {
         $post = Post::create($data);
         $this->activityLogger->log(
             level: LogLevel::INFO,
             message: 'Post created',
             context: ['post_id' => $post->id, 'title' => $post->title]
         );
         return $post;
     });
     ```
2. **Pemisahan Operasi I/O Lambat dari Blok Transaksi (Anti-Deadlock Rule)**:
   - **DILARANG KERAS** memanggil API pihak ketiga (*external HTTP requests*), pengiriman email sinkron, pembuatan file PDF berat, atau I/O lambat di dalam blok `DB::transaction()`.
   - Membuka transaksi database terlalu lama dapat mengunci baris/tabel (*row lock / table lock*), memicu *deadlock*, dan menghabiskan *connection pool*.
   - Jalankan operasi lambat secara asinkron menggunakan Laravel Queue/Job setelah transaksi database berhasil di-commit (*after commit*).

---

## 3. Validasi Tanpa Celah & Keamanan (Bulletproof Security)

1. **Validasi Ketat di Pintu Masuk (Boundary Validation)**:
   - Jangan pernah mempercayai input yang berasal dari pengguna (*never trust user input*).
   - Seluruh input (HTTP Request, Livewire Properties, API Payloads) wajib divalidasi secara komprehensif:
     - **Tipe Data & Format**: `string`, `integer`, `numeric`, `boolean`, `array`, `date`, `email:rfc,dns`.
     - **Batasan Panjang & Ukuran**: `min:x`, `max:255` (mencegah *buffer overflow* dan serangan payload raksasa).
     - **Integritas Relasi**: `exists:table,column`, `unique:table,column,except_id`.
     - **Enum / Whitelist Values**: `Rule::enum(StatusEnum::class)` atau `Rule::in(['draft', 'published'])`.
     - **Upload File**: Selalu batasi MIME types (`mimes:jpg,jpeg,png,pdf`) dan ukuran maksimal (`max:2048` KB).
2. **Sanitasi & Normalisasi Input**:
   - Selalu normalisasi data sebelum diproses (misal: `trim()` spasi berlebih, konversi email ke `strtolower()`, penghapusan tag HTML berbahaya).
3. **Otorisasi Ketat & Pencegahan IDOR (Insecure Direct Object Reference)**:
   - Sebelum mengeksekusi operasi bisnis atau mutasi data, selalu verifikasi hak akses pengguna:
     - Role & Permission: `auth()->user()->hasRole()`, `hasPermission()`.
     - Resource Ownership & Policy: `$this->authorize('update', $post)` atau `Gate::authorize()`.
   - Pastikan pengguna tidak dapat memanipulasi data milik pengguna lain hanya dengan menebak parameter `id`.
4. **Pencegahan Mass Assignment**:
   - Seluruh Eloquent Model wajib mendefinisikan properti `$fillable` secara eksplisit.
   - **DILARANG KERAS** menggunakan `$guarded = []` pada model tanpa perlindungan ketat.
5. **Pencegahan SQL Injection & XSS**:
   - Selalu gunakan Eloquent / Query Builder parameter binding (`where('slug', $slug)` atau `whereRaw('status = ?', [$status])`).
   - Dilarang menggabungkan variabel string langsung ke dalam raw SQL query (`whereRaw("status = '$status'")`).
6. **Rate Limiting & Perlindungan Brute Force**:
   - Seluruh endpoint autentikasi (login, register, reset password, OTP) dan endpoint mutasi data sensitif wajib dilindungi oleh rate limiter Laravel (`throttle:6,1` atau `RateLimiter::tooManyAttempts()`).

---

## 4. Optimasi Performa Query & Skalabilitas

1. **Pencegahan N+1 Problem (Eager Loading Wajib)**:
   - Selalu gunakan eager loading `with(['relation'])` atau `loadMissing()` saat mengambil kumpulan data (*collection*) yang relasinya akan diakses di dalam perulangan (*loop*) atau view.
   - **DILARANG** memanggil relasi dinamis (`$item->user->name`) di dalam perulangan tanpa eager loading.
2. **Wajib Pagination untuk Dataset Dinamis**:
   - Selalu gunakan `paginate($perPage)` atau `simplePaginate()` untuk query tabel operasional yang datanya bertambah seiring waktu.
   - **DILARANG** menggunakan `->get()` atau `->all()` tanpa batas limit pada tabel operasional/log.
3. **Pilih Kolom Spesifik (Selective Columns)**:
   - Pada tabel dengan banyak kolom teks panjang (`text`, `longtext`, `json`), hindari `SELECT *`. Pilih hanya kolom yang dibutuhkan: `->select(['id', 'title', 'slug', 'created_at'])`.
4. **Pengecekan Eksistensi & Agregasi yang Efisien**:
   - Gunakan `->exists()` atau `->doesntExist()` untuk mengecek keberadaan data, bukan `->count() > 0` atau `->first() !== null`.
   - Gunakan fungsi agregat langsung dari database (`->count()`, `->sum()`, `->avg()`), bukan memanggil collection method pada seluruh record (`$items->all()->count()`).
5. **Pemrosesan Data Skala Besar (Large Dataset Processing)**:
   - Saat memproses ribuan/jutaan baris pada background job atau Artisan Command, gunakan `chunkById($chunkSize)` atau `lazyById()` untuk menjaga penggunaan memori tetap rendah dan konstan.

---

## 5. Standar Logging & Keamanan Data (Standard Logging v1.0)

Mengacu pada spesifikasi lengkap di [docs/STANDARD_LOGGING.md](file:///var/www/projects/laravel/larashiz/docs/STANDARD_LOGGING.md):

1. **Log Level Baku**:
   - `DEBUG`: Detail teknis internal (development only).
   - `INFO`: Event normal yang penting secara bisnis/operasional (`Post created`, `User logged in`).
   - `WARN`: Kondisi tidak ideal, tetapi sistem tetap berjalan normal (`User login failed`, `Rate limit hit`).
   - `ERROR`: Operasi gagal dan butuh perhatian segera (`Post creation failed`, `Payment gateway timeout`).
   - `CRITICAL`: Komponen inti sistem terputus / tidak berfungsi total.
2. **Pesan Log Wajib Statis**:
   - String pesan log **wajib statis** tanpa penyisipan variabel dinamis di dalam string.
   - Seluruh variabel dinamis dimasukkan ke dalam parameter `context` dengan format `snake_case`.
3. **Trace ID / Request ID**:
   - Setiap log yang terikat request HTTP otomatis menyertakan `request_id` unik (via middleware `AssignRequestId`).
4. **Pencatatan Exception Lengkap**:
   - Saat menangani error, sertakan exception lengkap (`type`, `message`, `stack`, `file`, `line`) via object `error` di layer penanganan. Dilarang menelan exception diam-diam (`catch` kosong).
5. **Masking Otomatis Data Sensitif**:
   - Dilarang keras mencatat password, token, authorization header, secret key, atau kartu kredit.
   - Email dan nomor telepon otomatis disamarkan (`f****@domain.com`, `0812****7890`).
6. **Fail-Safe Logging Boundary**:
   - Operasi penulisan log audit tidak boleh menggagalkan proses bisnis utama jika storage log mengalami kendala sementara.

---

## 6. Standar Arsitektur Service Layer & CRUD Reference

1. **Pemisahan Tanggung Jawab (Separation of Concerns)**:
   - Livewire Component dan Controller hanya bertindak sebagai pintu masuk request, validasi input boundary, dan delegasi tampilan.
   - Seluruh *business logic*, operasi query kompleks, kalkulasi data, transaksi database, dan mutasi data **wajib** ditempatkan pada **Service Class**.
2. **Modul Post Sebagai Acuan Standar Baku (Gold Standard)**:
   - Contoh implementasi backend service layer yang sempurna ada pada [`App\Services\PostService`](file:///var/www/projects/laravel/larashiz/app/Services/PostService.php):
     - Metode baca terisolasi: `getPaginatedPosts(string $search, string $status, int $perPage, string $sortField, string $sortDir)`.
     - Metode mutasi terenkapsulasi dalam `DB::transaction()`: `createPost(array $data, User $author)`, `updatePost(Post $post, array $data)`, `deletePost(Post $post)`.
     - Logging otomatis berbasis [Standard Logging v1.0](file:///var/www/projects/laravel/larashiz/docs/STANDARD_LOGGING.md) di setiap aksi CRUD.
3. **Dependency Injection & Inversion of Control**:
   - Gunakan constructor injection atau method injection untuk memanggil service class di dalam Livewire / Controller.
4. **Response & Return Types yang Konsisten**:
   - Seluruh method service wajib memiliki *type hints* parameter dan *explicit return type*.

---

## 7. Standar Database & Isolasi Testing

1. **Isolasi Database Testing**:
   - File `phpunit.xml` dikonfigurasi menggunakan database testing terpisah (`DB_DATABASE=laravel_test`).
   - **DILARANG** menjalankan automated test (`php artisan test`) yang menyentuh database development utama (`laravel`).
2. **Penggunaan DatabaseTransactions**:
   - Setiap class Feature Test baru wajib menggunakan trait `Illuminate\Foundation\Testing\DatabaseTransactions` agar state database selalu di-rollback otomatis setelah test selesai.
3. **Sinkronisasi Migrasi**:
   - Pastikan migrasi terbaru selalu terpasang pada kedua database (`laravel` dan `laravel_test`).

---

## 8. Standar Code Formatter & Konvensi PHP 8.4

1. **Laravel Pint**:
   - Setiap perubahan file PHP **wajib** lolos format: `vendor/bin/pint --format agent`.
2. **Fitur Modern PHP 8**:
   - Gunakan *constructor property promotion*: `public function __construct(protected ActivityLogService $logger) {}`.
   - Gunakan *explicit return type declarations* dan type hints pada seluruh parameter method.
   - Gunakan *match expression* jika menggantikan switch-case panjang.
   - Selalu gunakan curly braces `{}` untuk seluruh struktur kontrol logika (`if`, `foreach`, `while`).
