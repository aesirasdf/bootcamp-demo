<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(){
        $books = Cache::remember('books', now()->addDays(1), function(){
            $books = Book::all();
            $books->each(function ($book) {
                $book->author;
                $book->genres;
                $book->loans;
            });
            return $books;
        });
        return $this->responseOk($books, "Books has been retrieved!");
    }

    public function paginate(Request $request, $page){
        $inputs = [
            "page" => $page,
            "numOfData" => $request->get("numOfData") ?? 25,
        ];

        $validator = Validator::make($inputs, [
            'page' => 'required|integer|min:1',
            'numOfData' => 'required|integer|min:1|max:100'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $books = Cache::remember("books.page=" . $page .".numOfData=" . $inputs['numOfData'], now()->addSeconds(30), function() use($inputs, $page){
            $books = book::limit($inputs['numOfData'])->offset(($page - 1) * $inputs['numOfData'])->get();
            $books->each(function($book){
                $book->author;
                $book->genres;
            });
            return $books;
        });
        if(!$books->count()){
            return $this->responseNotFound();
        }
        return $this->responseOk($books, "Books has been retrieved!");
    }

    public function store(Request $request){
        if(!$request->user()->can("create books") /* return true or false */){
            return $this->responseForbidden("create books", "You need to have create books permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            "title" => "required|max:255|string",
            "description" => "required|max:2000|string",
            "author_id" => "required|exists:authors,id",
            "genre_id" => 'sometimes|array',
            'genre_id.*' => 'sometimes|exists:genres,id|integer',
            'price' => 'required|numeric|min:1|max:1000000'
        ]);
        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $book = Book::create($validator->safe()->only("title", "description", "author_id", "price"));
        if(isset($validator->validated()["genre_id"])){
            $book->genres()->sync($validator->validated()["genre_id"]);
        }
        $book->genres;
        $book->author;
        Cache::forget("books");
        return $this->responseCreated($book, "Book has been created!");
    }

    public function update(Request $request, Book $book){
        if(!$request->user()->can("update books") /* return true or false */){
            return $this->responseForbidden("update books", "You need to have update books permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            "title" => "sometimes|max:255|string",
            "description" => "sometimes|max:2000|string",
            "author_id" => "sometimes|exists:authors,id",
            "genre_id" => 'sometimes|array',
            'genre_id.*' => 'sometimes|exists:genres,id|integer',
            'price' => 'sometimes|numeric|min:1|max:1000000'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $book->update($validator->safe()->except("genre_id"));
        $book->genres()->sync($validator->validated()["genre_id"] ?? []);
        $book->genres;
        $book->author;
        Cache::forget("books");
        return $this->responseOk($book, "Book has been updated!");
    }

    public function destroy(Request $request, Book $book){
        if(!$request->user()->can("delete books") /* return true or false */){
            return $this->responseForbidden("delete books", "You need to have delete books permission to proceed!");
        }
        $book->delete();
        Cache::forget("books");
        return $this->responseOk(null, "Book has been deleted!");
    }

}
