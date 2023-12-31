<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
// use Illuminate\Support\Facades\Carbon;
use App\Models\Loan;
use App\Models\Book;

class LoanController extends Controller
{
    //

    public function store(Request $request){
        if(!$request->user()->can("create loans") /* return true or false */){
            return $this->responseForbidden("create loans", "You need to have create loans permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            'customer_id' => 'exists:customers,id|required',
            'months' => 'min:1|integer|required|max:12',
            'book_id' => 'array|min:1|required',
            'book_id.*' => 'exists:books,id'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }
        $validated = $validator->validated();
        $loan = Loan::create([
            'profile_id' => $request->user()->profile->id,
            'customer_id' => $validated['customer_id'],
            'due_date' => now()->addMonths($validated['months']),
        ]);
        $books = array(); // []
        // [1, 2, 3]
        foreach($validated['book_id'] as $book_id){
            $books[$book_id]["price"] = Book::find($book_id)->price;
            /* 
                [
                    100 => ['price' => Book::find(100)->price],
                    300 => ['price' => Book::find(300)->price],
                    3 => ['price' => Book::find(3)->price],
                ] 
            */ 
        }
        $loan->books()->sync($books);
        $loan->books;
        $loan->customer;
        $loan->profile;
        $loan->totalPrice();
        Cache::forget("loans");
        return $this->responseCreated($loan, "Loan has been created!");
    }

    
    public function index(Request $request){
        if(!$request->user()->can("view loans") /* return true or false */){
            return $this->responseForbidden("view loans", "You need to have view loans permission to proceed!");
        }
        $loans = Cache::remember('loans', now()->addDays(1), function(){
            $loans = Loan::all();
            $loans->each(function ($loan) {
                $loan->books;
                $loan->customer;
                $loan->profile;
                $loan->totalPrice();
            });
            return $loans;
        });
        return $this->responseOk($loans, "Loan has been retrieved!");
    }

    public function update(Request $request, Loan $loan){
        if(!$request->user()->can("update loans") /* return true or false */){
            return $this->responseForbidden("update loans", "You need to have update loans permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            'customer_id' => 'exists:customers,id|sometimes',
            'months' => 'min:1|integer|sometimes|max:12',
            'book_id' => 'array|min:1|sometimes',
            'book_id.*' => 'exists:books,id'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }
        $validated = $validator->validated();
        $loan->update([
            'customer_id' => $validated['customer_id'],
            'due_date' => $loan->created_at->addMonths($validated['months']),
        ]);

        if(isset($validated['book_id'])){
            $books = array(); // []
            // [1, 2, 3]
            foreach($validated['book_id'] as $book_id){
                $books[$book_id]["price"] = Book::find($book_id)->price;
                /* 
                    [
                        100 => ['price' => Book::find(100)->price],
                        300 => ['price' => Book::find(300)->price],
                        3 => ['price' => Book::find(3)->price],
                    ] 
                */ 
            }
            $loan->books()->sync($books);
        }
        $loan->books;
        $loan->customer;
        $loan->profile;
        $loan->totalPrice();
        Cache::forget("loans");
        return $this->responseOk($loan, "Loan has been updated!");
    }

    public function destroy(Request $request, Loan $loan){
        if(!$request->user()->can("delete loans") /* return true or false */){
            return $this->responseForbidden("delete loans", "You need to have delete loans permission to proceed!");
        }
        // $loan->books()->delete();
        $loan->delete();
        Cache::forget("loans");
        return $this->responseOk([], "Loan has been deleted!");
    }

    public function view(Request $request, Loan $loan){
        if(!$request->user()->can("view loans") /* return true or false */){
            return $this->responseForbidden("view loans", "You need to have view loans permission to proceed!");
        }
        $loan->books;
        $loan->customer;
        $loan->profile;
        $loan->totalPrice();
        return $this->responseOk($loan, "Loan has been retrieved!");
    }

}
