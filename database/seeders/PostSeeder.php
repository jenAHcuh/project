<?php

namespace Database\Seeders;

use App\Models\post;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $judul = [
            'test5r fr4f4r',
            'test5rfr4 f4r',
            'test  5rfrfrf  frf4f4r',
            'test5rf r4frf rff4r'
        ];

        foreach ($judul as $j) {
            $slug = Str::slug($j);
            $slugOri = $slug;
            $count = 1;
            while (post::where('slug', $slug)->exists()) {
                $slug = $slugOri . "-" . $count;
                $count++;
            }

            post::create([
                'title' => $j,
                'slug' => $slug,
                'description' => 'Deskripsi untuk ' . $j, // Space added
                'content' => 'konten untuk ' . $j, // Space added
                'status' => 'publish',
                'user_id' => '1'
            ]);
        }
    }
}