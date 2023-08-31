<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Book;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPermission();
    }


    private function createPermissions(){
        
        $permissions = [
            'view accounts', 'create accounts', 'update accounts', 'delete accounts',
            'view books', 'create books', 'update books', 'delete books',
            'view loans', 'create loans', 'update loans', 'delete loans',
            'view authors', 'create authors', 'update authors', 'delete authors',
            'view genres', 'create genres', 'update genres', 'delete genres',
            'view customers', 'create customers', 'update customers', 'delete customers',
        ];

        foreach($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }
    }

    private function createBooks(){
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
                    'description' => "No don't use it",
                    'price' => 100
                ],
                [
                    'title' => "Why is apple expensive?",
                    'description' => "IDK",
                    'price' => 200
                ],
                [
                    'title' => 'Are you rich?',
                    'description' => "ofcourse not",
                    'price' => 300
                ]
            ],
            [
                [
                    'title' => 'How to use Microsoft Products',
                    'description' => "No don't use it",
                    'price' => 100
                ],
                [
                    'title' => "Why is Microsoft expensive?",
                    'description' => "IDK",
                    'price' => 200
                ],
                [
                    'title' => 'Are you poor?',
                    'description' => "ofcourse not",
                    'price' => 300
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
