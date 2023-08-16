<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Book;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                "firstname" => "Steve",
                "lastname" => "Jobs",
                "penname" => "S. Jobs"
            ],
            [
                "firstname" => "Bill",
                "lastname" => "Gates"
            ]
        ];
        $books = [
            [
                [
                    'title' => 'How to use Apple Products',
                    'description' => "No don't use it"
                ],
                [
                    'title' => "Why is apple expensive?",
                    'description' => "IDK"
                ],
                [
                    'title' => 'Are you rich?',
                    'description' => "ofcourse not"
                ]
            ],
            [
                [
                    'title' => 'How to use Microsoft Products',
                    'description' => "No don't use it"
                ],
                [
                    'title' => "Why is Microsoft expensive?",
                    'description' => "IDK"
                ],
                [
                    'title' => 'Are you poor?',
                    'description' => "ofcourse not"
                ]
            ]
        ];

        foreach($authors as $index => $author){
            $author = Author::create($author);
            foreach($books[$index] as $book){
                $author->books()->create($book);
            }
        }

    }
}
