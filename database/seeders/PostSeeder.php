<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samplePosts = [
            [
                'title' => 'Membangun Web Modern dengan Laravel dan Livewire',
                'slug' => 'membangun-web-modern-dengan-laravel-dan-livewire',
                'category' => 'Technology',
                'status' => 'published',
                'content' => 'Laravel dan Livewire memungkinkan pembuatan aplikasi web interaktif tanpa harus menulis banyak JavaScript kustom.',
            ],
            [
                'title' => 'Penerapan Clean Architecture pada Laravel',
                'slug' => 'penerapan-clean-architecture-pada-laravel',
                'category' => 'Technology',
                'status' => 'published',
                'content' => 'Memisahkan business logic ke dalam Service Layer dan Form Objects membuat kode lebih mudah diuji, dibaca, dan dikembangkan.',
            ],
            [
                'title' => 'Tips Produktivitas Developer Masa Kini',
                'slug' => 'tips-produktivitas-developer-masa-kini',
                'category' => 'Lifestyle',
                'status' => 'draft',
                'content' => 'Manajemen waktu dan pembagian fokus kerja adalah kunci efisiensi seorang developer profesional.',
            ],
            [
                'title' => 'Strategi Optimasi Database Indexing',
                'slug' => 'strategi-optimasi-database-indexing',
                'category' => 'Business',
                'status' => 'published',
                'content' => 'Penggunaan index pada kolom filter dan foreign key dapat mempercepat proses query jutaan baris data secara drastis.',
            ],
        ];

        foreach ($samplePosts as $post) {
            Post::firstOrCreate(['slug' => $post['slug']], $post);
        }

        if (Post::count() < 10) {
            Post::factory()->count(10)->create();
        }
    }
}
