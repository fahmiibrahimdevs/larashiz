# Standar Logging

| | |
|---|---|
| **Versi** | 1.0 |
| **Tanggal** | 2026-09-30 |
| **Status** | Aktif |
| **Berlaku untuk** | Semua aplikasi backend, API, dan service yang dikembangkan tim |

Kata kunci **WAJIB**, **DISARANKAN**, dan **DILARANG** dalam dokumen ini mengikuti makna umum pada RFC 2119 (MUST, SHOULD, MUST NOT).

---

## 1. Tujuan

Dokumen ini menetapkan cara menulis log yang konsisten, aman, dan berguna, sehingga:

- Troubleshooting dan debugging di production lebih cepat.
- Satu request bisa dilacak lintas service.
- Log bisa dicari, difilter, dan dianalisis secara otomatis.
- Data sensitif tidak bocor lewat log.

## 2. Prinsip Umum

1. **Log untuk manusia dan mesin.** Pesan harus mudah dibaca, datanya harus mudah di-parse.
2. **Log harus actionable.** Setiap log level WARN ke atas harus membantu seseorang mengambil tindakan.
3. **Konsisten.** Penamaan field, format waktu, dan level sama di semua service.
4. **Aman by default.** Kalau ragu sebuah data sensitif atau tidak, jangan di-log.
5. **Secukupnya.** Log yang terlalu banyak sama buruknya dengan tidak ada log.

---

## 3. Log Level

| Level | Kapan dipakai | Contoh |
|---|---|---|
| **DEBUG** | Detail teknis untuk development/troubleshooting. Non-aktif di production secara default. | `Query executed in 12ms` |
| **INFO** | Event normal yang penting secara bisnis atau operasional. | `Order created`, `User logged in`, `Service started` |
| **WARN** | Kondisi tidak ideal, tapi sistem masih berjalan normal. | `Retry ke-2 ke payment gateway`, `Deprecated endpoint dipanggil` |
| **ERROR** | Sebuah operasi gagal dan butuh perhatian. | `Gagal menyimpan order ke database` |
| **CRITICAL** | Sistem/komponen inti tidak bisa berfungsi. Butuh tindakan segera. | `Koneksi database putus total` |

### Aturan level per environment

| Environment | Level minimum |
|---|---|
| Development | `DEBUG` |
| Staging | `DEBUG` atau `INFO` |
| Production | `INFO` |

Level **DAPAT** dinaikkan sementara di production untuk investigasi, tapi **WAJIB** dikembalikan setelah selesai.

### Salah kaprah yang sering terjadi

- Validasi input gagal (user salah isi form) **bukan** ERROR. Gunakan INFO atau WARN, atau tidak di-log sama sekali.
- `404 Not Found` biasa **bukan** ERROR.
- Exception yang sudah ditangani dan ada fallback-nya cukup WARN.
- ERROR berarti ada yang perlu diperbaiki atau diselidiki. Kalau tidak ada yang perlu ditindaklanjuti, level-nya terlalu tinggi.

---

## 4. Format Log

Log **WAJIB** ditulis dalam format **JSON, satu event per baris** (JSON Lines) di environment staging dan production. Format teks biasa **DAPAT** dipakai di development lokal agar mudah dibaca.

### 4.1 Field standar

| Field | Status | Keterangan |
|---|---|---|
| `timestamp` | WAJIB | ISO 8601, UTC. Contoh: `2026-09-30T08:15:32.418Z` |
| `level` | WAJIB | `DEBUG`, `INFO`, `WARN`, `ERROR`, `CRITICAL` |
| `message` | WAJIB | Deskripsi singkat, statis, dan jelas (lihat bagian 5) |
| `service` | WAJIB | Nama aplikasi/service. Contoh: `order-api` |
| `env` | WAJIB | `development`, `staging`, `production` |
| `request_id` / `trace_id` | WAJIB untuk request-scoped log | ID unik untuk melacak satu request |
| `context` | DISARANKAN | Object berisi data pendukung (ID, durasi, dsb.) |
| `error` | WAJIB saat ada exception | Object berisi `type`, `message`, `stack` |
| `user_id` | DISARANKAN | ID internal user, **bukan** email/nama |
| `duration_ms` | DISARANKAN | Durasi operasi dalam milidetik |
| `version` | OPSIONAL | Versi aplikasi/build, berguna untuk korelasi dengan deployment |

### 4.2 Aturan penamaan field

- Gunakan **`snake_case`** (`order_id`, bukan `orderId` atau `OrderID`).
- Gunakan nama yang sama untuk hal yang sama di semua service (`user_id`, bukan campur `uid` dan `userId`).
- Satuan dicantumkan di nama field: `duration_ms`, `size_bytes`.
- Tipe data konsisten. Field `order_id` tidak boleh string di satu log dan integer di log lain.

### 4.3 Contoh log yang benar

**INFO:**

```json
{
  "timestamp": "2026-09-30T08:15:30.102Z",
  "level": "INFO",
  "service": "order-api",
  "env": "production",
  "request_id": "9f1c2b7e-4a3d-4b8e-9c11-2f6a7d5e8b30",
  "message": "Order created",
  "user_id": 1024,
  "context": {
    "order_id": "ORD-2026-0091",
    "total": 250000,
    "items_count": 3
  }
}
```

**ERROR:**

```json
{
  "timestamp": "2026-09-30T08:15:32.418Z",
  "level": "ERROR",
  "service": "order-api",
  "env": "production",
  "request_id": "9f1c2b7e-4a3d-4b8e-9c11-2f6a7d5e8b30",
  "message": "Payment gateway timeout",
  "user_id": 1024,
  "duration_ms": 5000,
  "context": {
    "order_id": "ORD-2026-0091",
    "gateway": "midtrans",
    "retry": 2
  },
  "error": {
    "type": "TimeoutException",
    "message": "Connection timed out after 5000ms",
    "stack": "TimeoutException: Connection timed out ...\n  at PaymentService->charge() ..."
  }
}
```

---

## 5. Penulisan Message

1. **Jelas dan spesifik.** Pembaca harus paham apa yang terjadi tanpa membuka kode.
2. **Statis.** Jangan menyisipkan variabel ke dalam `message`. Taruh variabel di `context`. Ini membuat log mudah dikelompokkan dan di-query.
3. **Satu bahasa, satu gaya.** Pilih Bahasa Indonesia atau Inggris untuk message dan pertahankan di seluruh service.
4. **Tanpa emoji, tanpa huruf kapital semua, tanpa tanda seru.**
5. **Gunakan kata kerja yang menjelaskan hasil.** Contoh: `Order created`, `Payment failed`, `Email sent`.

| ❌ Buruk | ✅ Baik |
|---|---|
| `Error occurred` | `Payment gateway timeout` |
| `Failed!!!` | `Failed to send email` + context `recipient_domain`, `smtp_code` |
| `User 1024 created order ORD-0091 total 250000` | message `Order created`, context `{ user_id, order_id, total }` |
| `here` / `test 123` | Hapus, jangan di-commit |

---

## 6. Apa yang Perlu Di-log

### WAJIB di-log

- **Startup dan shutdown** aplikasi (beserta versi dan konfigurasi non-sensitif).
- **Request masuk dan respons** pada boundary (method, path, status code, `duration_ms`), biasanya lewat middleware.
- **Panggilan ke sistem eksternal** (database lambat, API pihak ketiga, message queue) beserta durasi dan hasilnya.
- **Semua error/exception** yang tidak tertangani.
- **Event keamanan**: login berhasil/gagal, perubahan hak akses, reset password, akses ditolak.
- **Event bisnis penting**: pembuatan order, pembayaran, perubahan status kritis.
- **Perubahan konfigurasi** atau feature flag yang berdampak.

### TIDAK perlu di-log

- Setiap baris eksekusi kode ("masuk fungsi X", "keluar fungsi X") di level INFO.
- Data mentah dalam jumlah besar (seluruh response body, seluruh isi tabel).
- Log di dalam loop besar. Ringkas menjadi satu log di akhir (`processed 10000 rows, 3 failed`).

---

## 7. Data Sensitif

### DILARANG di-log dalam bentuk apa pun

- Password (termasuk hash), PIN, OTP
- Token: access token, refresh token, API key, session ID, secret
- Nomor kartu kredit/debit, CVV
- Nomor identitas (NIK, paspor, dsb.)
- Isi header `Authorization` dan `Cookie`

### Harus di-mask jika terpaksa dicantumkan

| Data | Contoh setelah masking |
|---|---|
| Email | `f***@mail.com` |
| Nomor telepon | `0812****5678` |
| Nomor kartu | `**** **** **** 4417` (maksimal 4 digit terakhir) |

### Praktik pendukung

- Gunakan **allowlist** (hanya field tertentu yang boleh masuk log), bukan denylist.
- Jangan pernah `log($request->all())` atau meng-dump seluruh object tanpa disaring.
- Terapkan masking otomatis di level logger/processor, jangan mengandalkan disiplin manual saja.
- Akses ke sistem log **WAJIB** dibatasi sesuai kebutuhan (least privilege).

---

## 8. Error Logging

1. **Sertakan exception lengkap**: `type`, `message`, dan `stack trace`. Jangan hanya `$e->getMessage()`.
2. **Log sekali saja.** Log error di layer yang menangani error tersebut. Jangan log lalu throw ulang lalu di-log lagi di layer atas, karena akan menghasilkan duplikat.
3. **Sertakan konteks bisnis**: ID entitas yang terlibat (`order_id`, `user_id`), bukan hanya pesan teknis.
4. **Jangan menelan exception diam-diam.** `catch` kosong tanpa log dilarang.
5. **Pesan ke user berbeda dari pesan log.** User melihat pesan ramah, log menyimpan detail teknis. Jangan tampilkan stack trace ke user.

---

## 9. Performa & Volume

- Logging **tidak boleh** menjadi bottleneck. Pakai logger asinkron atau buffered bila volume tinggi.
- Hindari membangun string/context yang mahal hanya untuk log level yang tidak aktif (misal DEBUG di production).
- Untuk event berulang berfrekuensi tinggi, gunakan **sampling** atau **rate limiting** (misal log 1 dari 100).
- Perhatikan ukuran per baris log. Potong field yang sangat panjang (misal maksimal 2 KB).

---

## 10. Operasional

### 10.1 Output

- Aplikasi **DISARANKAN** menulis log ke **stdout/stderr**, lalu infrastruktur (Docker, systemd, log shipper) yang mengumpulkannya, sesuai prinsip *The Twelve-Factor App*.
- Jika harus menulis ke file, **WAJIB** ada log rotation.

### 10.2 Rotation & retention (rekomendasi)

| Environment | Retensi |
|---|---|
| Development | 7 hari |
| Staging | 14 hari |
| Production | 30 hari (hot), arsip lebih lama bila dibutuhkan audit/kepatuhan |

Sesuaikan dengan kebijakan organisasi dan regulasi yang berlaku. Log audit/keamanan biasanya membutuhkan retensi lebih panjang.

### 10.3 Centralized logging

- Log semua service **DISARANKAN** dikumpulkan di satu platform (misalnya ELK/OpenSearch, Grafana Loki, atau layanan managed).
- Pastikan `request_id`/`trace_id` diteruskan antar service lewat header (misal `X-Request-ID`).

### 10.4 Monitoring & alerting

- Buat alert untuk lonjakan log `ERROR`/`CRITICAL`, bukan untuk setiap log ERROR tunggal.
- Alert harus menyertakan link ke query log yang relevan.
- Tinjau log level dan noise secara berkala. Log yang tidak pernah dibaca sebaiknya dihapus.

---

## 11. Contoh Implementasi (Laravel / Monolog)

### 11.1 Middleware `AssignRequestId`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignRequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();

        $request->headers->set('X-Request-ID', $requestId);

        Log::withContext([
            'request_id' => $requestId,
            'service'    => config('app.name', 'larashiz'),
            'env'        => config('app.env', 'production'),
        ]);

        $response = $next($request);
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
```

### 11.2 Penggunaan di Kode (via `ActivityLogService`)

```php
use App\Services\ActivityLogService;

$logger = app(ActivityLogService::class);

// INFO: event bisnis normal. Message statis, data di context.
$logger->info(
    action: 'CREATE',
    module: 'posts',
    message: 'Post created',
    context: [
        'post_id' => $post->id,
        'title' => $post->title,
        'category' => $post->category,
    ]
);

// WARN: kondisi tidak ideal tapi masih bisa lanjut
$logger->warning(
    action: 'LOGIN_FAILED',
    module: 'auth',
    message: 'User login failed',
    context: [
        'email' => $email,
        'reason' => 'invalid_credentials',
    ]
);

// ERROR: sertakan exception lengkap
try {
    $postService->createPost($data);
} catch (Throwable $e) {
    $logger->error(
        action: 'CREATE_FAILED',
        module: 'posts',
        message: 'Post creation failed',
        context: [
            'payload' => $data,
        ],
        exception: $e
    );

    throw $e;
}
```

---

## 12. Checklist

### Untuk developer (sebelum commit)

- [ ] Level log sesuai fungsinya (`DEBUG`, `INFO`, `WARN`, `ERROR`, `CRITICAL`)
- [ ] Message jelas, statis, dan tanpa variabel di dalamnya
- [ ] Data dinamis ada di `context` dengan nama field `snake_case`
- [ ] Ada `request_id` / `trace_id` pada log request-scoped
- [ ] Exception di-log lengkap (type, message, stack) dan tidak di-log ganda
- [ ] Tidak ada data sensitif (password, token, PII) di log
- [ ] Masking otomatis aktif untuk email (`f***@mail.com`) dan nomor telepon
- [ ] Tidak ada log debug sementara (`test`, `here`, `dd`) yang tertinggal
- [ ] Tidak ada log berlebihan di dalam loop
