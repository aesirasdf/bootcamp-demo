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
        if(!$request->user()->can("create authors") /* return true or false */){
            return $this->responseForbidden("create authors", "You need to have create authors permission to proceed!");
        }
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
        $this->log($request, "create author", $validator->validated(), "authors", $author);
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
        if(!$request->user()->can("update authors") /* return true or false */){
            return $this->responseForbidden("update authors", "You need to have update authors permission to proceed!");
        }
        $validator = Validator($request->all(), [
            'firstname' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            'middlename' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "penname" => "sometimes|max:64|string",
        ]);
        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }
        $orig = $author->toArray();
        $author->update($validator->validated());
        $dirtyorig = array(); // empty array of list that will be changed
        $dirtynew = array();
        // dd($validator->validated(), $orig);
        foreach($validator->validated() as $key => $value){
            if($value != $orig[$key]){
                $dirtyorig[$key] = $orig[$key]; // columns that has been changed
                $dirtynew[$key] = $value; // columns that has been changed
            }
        }
        $this->log($request, "update author", [
            "old" => $dirtyorig,
            "new" => $dirtynew
        ], "authors", $author);
        Cache::forget("authors");
        return $this->responseOk($author, "Author has been updated!");
    }

    public function destroy(Request $request, Author $author){
        if(!$request->user()->can("delete authors") /* return true or false */){
            return $this->responseForbidden("delete authors", "You need to have delete authors permission to proceed!");
        }
        $orig = $author->toArray();
        $this->log($request, "delete author", $orig, "authors", $author);
        $author->books()->delete();
        $author->delete();
        Cache::forget("authors");
        return $this->responseOk(null, "Author has been deleted!");
    }
}
