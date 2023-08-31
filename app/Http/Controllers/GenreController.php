<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class GenreController extends Controller
{
    
    public function store(Request $request){
        if(!$request->user()->can("create genres") /* return true or false */){
            return $this->responseForbidden("create genres", "You need to have create genres permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            'name' => "required|max:64|unique:genres",
            'description' => "sometimes|max:64|string",
            'book_id' => 'sometimes|array',
            'book_id.*' => 'sometimes|exists:books,id|integer'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }
        $validated = $validator->safe()->only("name", "description");
        $genre = Genre::create($validated);
        $genre->books()->sync($validator->validated()["book_id"] ?? []); // array of book_ids [1,2,3,4,5]
        $genre->books;
        Cache::forget("genres");
        return $this->responseCreated($genre, "Genre has been created!");
        
    }

    public function index(){
        $genres = Cache::remember('genres', now()->addDays(1), function(){
            $genres = Genre::all();
            $genres->each(function ($genre) {
                $genre->books;
            });
            return $genres;
        });
        return $this->responseOk($genres, "Genres has been retrieved!");
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

        $genres = Cache::remember("genres.page=" . $page .".numOfData=" . $inputs['numOfData'], now()->addSeconds(30), function() use($inputs, $page){
            $genres = genre::limit($inputs['numOfData'])->offset(($page - 1) * $inputs['numOfData'])->get();
            $genres->each(function($genre){
                $genre->books;
            });
            return $genres;
        });
        if(!$genres->count()){
            return $this->responseNotFound();
        }
        return $this->responseOk($genres, "Genres has been retrieved!");
    }

    

    public function update(Request $request, Genre $genre){
        if(!$request->user()->can("update genres") /* return true or false */){
            return $this->responseForbidden("update genres", "You need to have update genres permission to proceed!");
        }
        $validator = Validator::make($request->all(), [
            'name' => "required|max:64|unique:genres,id," . $genre->id,
            'description' => "sometimes|max:64|string",
            'book_id' => 'sometimes|array',
            'book_id.*' => 'sometimes|exists:books,id|integer'
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $genre->update($validator->safe()->except("book_id"));
        $genre->books()->sync($validator->validated()["book_id"] ?? []);
        $genre->books;
        $genre->author;
        Cache::forget("genres");
        return $this->responseOk($genre, "Genre has been updated!");
    }

    public function destroy(Request $request, Genre $genre){
        if(!$request->user()->can("delete genres") /* return true or false */){
            return $this->responseForbidden("delete genres", "You need to have delete genres permission to proceed!");
        }
        $genre->delete();
        Cache::forget("genres");
        return $this->responseOk(null, "Genre has been deleted!");
    }
    
}
