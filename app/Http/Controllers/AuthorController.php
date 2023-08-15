<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\Author;

class AuthorController extends Controller
{
    //

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            'middlename' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "penname" => "sometimes|max:64|string",
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $author = Author::create($validator->validated());
        Cache::forget("authors");
        return $this->responseCreated($author, "Author has been created!");
        
    }

    public function index(){
        $authors = Cache::remember('authors', now()->addHours(1), function () {
            $authors = Author::all();
            $authors->each(function($author){
                $author->books;
            });
            return $authors;
        });
        return $this->responseOk($authors, "Authors has been retrieved!");

    }

    public function paginate(Request $request, int $page = 1){
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

        $authors = Cache::remember("authors.page=" . $page .".numOfData=" . $inputs['numOfData'], now()->addSeconds(30), function() use($inputs, $page){
            $authors = Author::limit($inputs['numOfData'])->offset(($page - 1) * $inputs['numOfData'])->get();
            $authors->each(function($author){
                $author->books;
            });
            return $authors;
        });
        if(!$authors->count()){
            return $this->responseNotFound();
        }
        return $this->responseOk($authors, "Authors has been retrieved!");

    }

    public function update(Request $request, Author $author){
        $validator = Validator($request->all(), [
            'firstname' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            'middlename' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "penname" => "sometimes|max:64|string",
        ]);
        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $author->update($validator->validated());
        Cache::forget("authors");
        return $this->responseOk($author, "Author has been updated!");
    }

    public function destroy(Author $author){
        $author->books()->delete();
        $author->delete();
        Cache::forget("authors");
        return $this->responseOk(null, "Author has been deleted!");
    }
}
