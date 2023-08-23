<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function books(){
        return $this->belongsToMany(Book::class)->withPivot("price");
    }

    public function profile(){
        return $this->belongsTo(Profile::class);
    }
    
    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function totalPrice(){
        $this->totalPrice = 0;
        foreach($this->books as $book){
            $this->totalPrice += $book->pivot->price * date_diff(date_create($this->due_date), date_create($this->created_at), true)->format('%m');
        }
        
    }
}
