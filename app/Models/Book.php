<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;


    protected $guarded = [];



    public function author(){
        return $this->belongsTo(Author::class);
    }

    
    public function genres(){
        // Model::class, "book_genre", "book_id", "genre_id"
        return $this->belongsToMany(Genre::class, "book_genre", "book_id", "genre_id");


        // Incase you have pivot values
        // return $this->belongsToMany(Genre::class, "book_genre", "book_id", "genre_id")->withPivot("column1", "column2");

    }
}
