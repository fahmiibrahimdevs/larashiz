# LARASHIZ FRONTEND & UI/UX GUIDELINES

Dokumen ini adalah acuan standar baku pengembangan antarmuka (UI/UX), komponen styling, struktur tabel data, modal, dan tata letak Blade pada project **Larashiz**. Setiap penambahan atau perubahan fitur frontend **WAJIB** mematuhi aturan di bawah ini.

---

## 1. Standar Prioritas Komponen Bawaan Template (Template-First Principle)

1. **Wajib Cek Komponen Template Terlebih Dahulu**:
   - Setiap ingin membuat elemen antarmuka (seperti badge, alert, tombol, modal, pagination, card, list-group, breadcrumb, dropdown, dll), **WAJIB** mengecek terlebih dahulu apakah komponen tersebut sudah disediakan oleh template bawaan (Shizuefi / Stisla).
   - Contoh pada Badge: Gunakan class murni bawaan template seperti:
     - Primary: `<span class="badge badge-primary">`
     - Success: `<span class="badge badge-success">`
     - Danger: `<span class="badge badge-danger">`
     - Warning: `<span class="badge badge-warning">`
     - Info: `<span class="badge badge-info">`
     - Secondary: `<span class="badge badge-secondary">`
     - Light: `<span class="badge badge-light border">`
2. **Dilarang Menambahkan Class Ad-Hoc / Custom pada Komponen Bawaan**:
   - Dilarang menambahkan modifier arbitrary/ad-hoc (seperti menambahkan `text-dark` pada `badge-warning`). Gunakan tampilan murni template.
   - Dilarang membuat class custom baru (misal `.custom-badge`, `.log-badge-soft-*`) untuk elemen yang sudah ada bawaannya di template.
3. **Persetujuan Wajib untuk Komponen Kustom Baru**:
   - Jika suatu komponen memang **belum ada** di template bawaan dan benar-benar memerlukan custom component CSS baru, **WAJIB** meminta persetujuan terlebih dahulu kepada pengguna sebelum membuatnya.

---

## 2. Larangan Utility Class Ad-Hoc di Blade (Component-First CSS)

1. **Dilarang Menuliskan Utility Arbitrary Tailwind di Blade**:
   - ❌ Jangan gunakan: `text-[11px]`, `text-[#1e293b]`, `px-[25px]`, `bg-[#f8fafc]`, inline `style="..."`.
   - ✅ Gunakan class komponen semantik terdaftar (contoh: `.filter-chip-item`, `.log-list-tile`).
2. **Alur Pembuatan Style Baru**:
   - Definisikan class komponen baru di `public/assets/css/components-tailwind.css` menggunakan sintaks `@apply` Tailwind.
   - Jalankan kompilasi: `npm run build:shizuefi`.
   - Panggil nama class komponen semantik tersebut di file Blade.

---

## 3. Standar Struktur Header Halaman & Section Title (Page Header Standard)

1. **Header Slot & Breadcrumb Baku (`<x-slot name="header">`)**:
   - Setiap halaman Blade / Livewire wajib mendefinisikan `<x-slot name="header">` yang berisi judul halaman `<h1>` dan breadcrumb navigasi:
     ```blade
     <x-slot name="header">
         <h1>Kelola Posts</h1>
         <div class="section-header-breadcrumb">
             <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
             <div class="breadcrumb-item">Posts</div>
         </div>
     </x-slot>
     ```
2. **Section Title & Section Lead Wajib**:
   - Tepat di bawah slot header, setiap halaman **wajib** menyertakan judul section dan deskripsi ringkas:
     ```blade
     <h2 class="section-title">Postingan</h2>
     <p class="section-lead mb-3">
         Kelola seluruh postingan artikel, berita, dan publikasi konten Anda di halaman ini.
     </p>
     ```
3. **Dilarang Menuliskan Ulang Tag `<section>` dan `<div class="section-body">`**:
   - Layout utama [`layouts/app.blade.php`](file:///var/www/projects/laravel/larashiz/resources/views/layouts/app.blade.php) sudah secara otomatis membungkus `$slot` dengan `<section class="section custom-section">` dan `<div class="section-body">`.
   - **DILARANG KERAS** menambahkan wrapper `<section>` atau `<div class="section-body">` di dalam file view karena akan menciptakan nesting ganda (*redundant wrapper*) dan merusak kalkulasi margin pada mode mobile.

---

## 4. Standar Desain Tabel Data (Table Component Standard)

Semua halaman yang menampilkan tabel data (seperti pada modul **Posts** dan **Logs**) wajib mengikuti pola struktur HTML & class komponen baku berikut:

### Struktur Hierarki Tabel
```html
<div class="card">
    <!-- 1. Card Header -->
    <div class="card-header">
        <h4>Judul Tabel</h4>
    </div>

    <div class="card-body p-0">
        <!-- 2. Controls: Show Entries & Search Bar -->
        <div class="dt-control-wrapper">
            <div class="dt-show-entries">
                Show
                <select wire:model.live="perPage" class="dt-select-entries">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                Entries
            </div>

            <div class="dt-search-wrapper">
                <span class="mr-1">Search:</span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="dt-search-input"
                    placeholder="Search here..."
                >
            </div>
        </div>

        <!-- 3. Table Responsive Container (Smooth Horizontal Scroll) -->
        <div class="table-responsive">
            <table class="table table-clean">
                <thead class="bg-gray-100">
                    <tr>
                        <th style="width: 60px;" class="text-center text-nowrap">NO</th>
                        <th class="text-nowrap">KOLOM UTAMA</th>
                        <th style="width: 160px;" class="text-nowrap">KATEGORI</th>
                        <th style="width: 140px;" class="text-nowrap">STATUS</th>
                        <th class="text-center text-nowrap" style="width: 110px;">
                            <i class="fas fa-cog text-muted text-[13px]" title="Aksi"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $index => $item)
                        <tr wire:key="item-{{ $item->id }}">
                            <td class="text-center font-weight-600 text-muted align-middle">
                                {{ $items->firstItem() + $index }}
                            </td>
                            <!-- Kolom Konten Utama -->
                            <td class="align-middle">...</td>
                            <!-- Kolom Aksi -->
                            <td class="text-center align-middle text-nowrap">
                                <button type="button" class="btn btn-primary btn-action mr-1" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-action" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="fas fa-folder-open text-muted mb-2" style="font-size: 32px;"></i>
                                    <div class="font-weight-600 mt-2">Tidak ada data ditemukan</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 4. Footer: Info & Pagination -->
        <div class="dt-footer-wrapper">
            <div>
                Showing {{ $items->total() > 0 ? $items->firstItem() : 0 }} to {{ $items->total() > 0 ? $items->lastItem() : 0 }} of {{ $items->total() }} entries
            </div>
            <div>
                {{ $items->links('livewire.custom-pagination') }}
            </div>
        </div>
    </div>
</div>
```

### Aturan Wajib Tabel:
1. **Header Anti-Wrap (`text-nowrap`)**: Semua tag `<th>` pada `<thead>` **wajib** menggunakan class `text-nowrap` agar judul kolom tidak terlipat/patah ke bawah.
2. **Auto-Width untuk Kolom Utama**: Berikan ukuran width pasti pada kolom metadata ringkas (`NO`, `STATUS`, `AKSI`), dan biarkan kolom teks utama tanpa width tetap agar leluasa beradaptasi dengan lebar layar.
3. **Penomoran Konsisten**: Selalu gunakan formula `{{ $items->firstItem() + $index }}` agar nomor halaman pagination berlanjut dengan benar.
4. **Wire Key Wajib**: Setiap row `<tr>` dalam loop wajib memiliki `wire:key="item-{{ $item->id }}"`.
5. **Pagination Custom**: Gunakan view pagination kustom `$items->links('livewire.custom-pagination')`.

---

## 4. Standar Modal & Form Interactivity

1. **Header & Footer Standar Bawaan**:
   - Header: `<div class="modal-header">` polos (dilarang menambahkan background color custom seperti `bg-light`).
   - Footer: `<div class="modal-footer bg-whitesmoke br">` (mengikuti standar modal template Stisla).
2. **Layout Detail Modal (Mobile / Flutter-Style ListTile)**:
   - Menampilkan header status banner terpadu (`.log-detail-banner`).
   - Menggunakan grup list-tile bergaya mobile card (`.log-list-tile-group` dan `.log-list-tile`) untuk menyajikan metadata (User, IP, User-Agent, Request ID) daripada tabel HTML biasa.
3. **Form & Filter Real-Time**:
   - Pada filter/modal Livewire yang sudah berjalan reaktif secara *real-time*, **dilarang** menambahkan tombol "Terapkan Filter / Submit" ganda di modal kecuali jika form tersebut memerlukan submit manual.
4. **Standar Select2 di Dalam Bootstrap Modal & Pencegahan Focus Trap**:
   - **Hapus `tabindex="-1"` pada Modal**: Atribut `tabindex="-1"` pada modal mengaktifkan *Focus Trap* bawaan Bootstrap yang menyebabkan kotak pencarian (search box) Select2 tidak bisa diketik / kehilangan fokus. Hapus atribut `tabindex="-1"` pada elemen modal pembungkus.
   - **Wajib `dropdownParent`**: Selalu konfigurasi `dropdownParent: $('#modalId')` saat inisialisasi Select2 di dalam modal agar dropdown terpasang di konteks modal dan tidak tertutup backdrop.
   - **Override Enforce Focus di JavaScript**:
     ```javascript
     if ($.fn.modal && $.fn.modal.Constructor) {
         $.fn.modal.Constructor.prototype._enforceFocus = function() {};
     }
     ```
   - **Wajib `wire:ignore` pada Wrapper Select**: Selalu bungkus form-group Select2 dengan `wire:ignore` agar Livewire DOM morphing tidak mereset atau merusak instance Select2 saat komponen melakukan re-render.
   - **Sinkronisasi Dua Arah (`@this.set()`)**: Ikat event `change` Select2 ke state Livewire via `@this.set('form.field', value)` dan trigger update value Select2 saat modal dibuka (`open-modal`).

---

## 5. Standar Warna Primary & Konsistensi Tema Proyek

1. **Warna Primary Resmi Proyek (`#0b52aa`)**:
   - Seluruh elemen yang merepresentasikan warna utama/primary (tombol `.btn-primary`, tombol melayang / FAB `.btn-floating-filter`, active chip filters, active borders, SweetAlert confirm button) **WAJIB** menggunakan warna primary resmi proyek yaitu `#0b52aa` (atau class utility bawaan tema `.btn-primary` / `.bg-primary` / `var(--primary)`).
   - **DILARANG** menggunakan warna default Stisla lama ungu/indigo (`#6777ef`) pada styling baru, komponen baru, ataupun konfigurasi script dialog.
2. **Tombol Melayang (Floating Action Button / FAB)**:
   - Menggunakan background color primary `#0b52aa` dengan efek hover `#09458f` dan shadow lembut yang serasi (`rgba(11,82,170,0.4)`).

---

## 6. Standar Global Card & Spacing

1. **Tingkat Ketebalan Shadow**:
   - Seluruh kartu (`.card`, `.card-statistic-1`, `.shadow-sm`) menggunakan bayangan medium-soft yang seimbang:
     `box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.04)` dengan border halus `border: 1px solid #e9edf2`.
   - Dilarang menggunakan shadow hitam pekat/tebal atau shadow yang terlalu transparan/mati.
2. **Spacing Mobile Responsive**:
   - Pada layar mobile (`max-width: 767.98px`), margin bawah card diset `margin-bottom: 14px !important` agar jarak vertikal antar card tetap rapat dan proporsional.
   - Hindari menambahkan class `mb-4` ganda pada elemen kolom pembungkus card.

---

## 7. Alur Kompilasi Aset Frontend

Setiap perubahan CSS di `public/assets/css/components-tailwind.css` **wajib** dikompilasi ulang:
```bash
npm run build:shizuefi
```
