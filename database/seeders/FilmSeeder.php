<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmSeeder extends Seeder
{

    public function run(): void
    {

        Film::query()->delete();

        DB::table('films')->insert([
            [
                'title' => 'SOPIR ANGKOT MENEMBUS ASA DAN MASA ',
                'genre' => 'Action',
                'duration' => '1h 40m',
                'price' => 50000,
                'description' => 'Modernisasi telah mengubah wajah transportasi di Jember. Angkot, yang dulu menjadi urat nadi mobilitas masyarakat, kini mulai ditinggalkan. Kehadiran kendaraan pribadi dan ojek online yang dianggap lebih praktis membuat eksistensi angkot semakin terdesak.

Di tengah arus perubahan itu, hadir kisah Soekarno, seorang sopir angkot yang telah mengemudi sejak 1986. Ia menyaksikan langsung masa kejayaan angkot hingga kemerosotannya hari ini. Jika dulu penghasilan sehari bisa mencukupi kebutuhan seminggu, kini ia hanya bisa bersyukur ketika ada penumpang yang naik.

Film dokumenter ini tidak hanya menyoroti keteguhan seorang sopir untuk bertahan, tetapi juga merekam suara pengguna kendaraan pribadi dan pelanggan ojek online, yang menunjukkan alasan angkot kian tersisihkan. Dari berbagai perspektif inilah muncul refleksi: modernisasi membawa kemudahan, namun juga meninggalkan jejak kehilangan.

“Sopir Angkot: Menembus Asa & Masa” adalah potret tentang manusia, perubahan zaman, dan pertanyaan besar mampukah transportasi rakyat bertahan di era digital',
                'image' => 'Sopir.png',
            ],
            [
                'title' => 'Titip Pesan',
                'genre' => 'Animation',
                'duration' => '1h 43m',
                'price' => 40000,
                'description' => 'Elsa and Anna go on a magical adventure.',
                'image' => 'TitipPesan.png',
            ],

            [
                'title' => 'Tanah & Ikat',
                'genre' => 'Animation',
                'duration' => '1h 43m',
                'price' => 40000,
                'description' => 'Elsa and Anna go on a magical adventure.',
                'image' => 'Tanah.jpg',
            ],
            [
                'title' => 'Kepadamu Doa Yang Tak Terjawab',
                'genre' => 'Sedih',
                'duration' => '1h 43m',
                'price' => 40000,
                'description' => 'Elsa and Anna go on a magical adventure.',
                'image' => 'Kepadamu.jpg',
            ],
            [
                'title' => 'Left Hook',
                'genre' => 'Animation',
                'duration' => '1h 43m',
                'price' => 40000,
                'description' => 'Elsa and Anna go on a magical adventure.',
                'image' => 'Left.png',
            ],
            [
                'title' => 'Pulasara',
                'genre' => 'Animation',
                'duration' => '1h 43m',
                'price' => 40000,
                'description' => 'Elsa and Anna go on a magical adventure.',
                'image' => 'Pulasara.jpg',
            ],
        ]);
    }
}
