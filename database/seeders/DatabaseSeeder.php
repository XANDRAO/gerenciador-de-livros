<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $author=
        \App\Models\Author::create([

            "name" => "Juliano"

        ]);

        \App\Models\Book::create([
        'title' => "God of war",
        'author_id' => $author->id,
        'publisher'=> "Santa Monica",
        'publication_year' => "2005",
        'pages_amount' => 300,
        'isbn_number' => "1234567890",
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
