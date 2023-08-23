<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class CustomerController extends Controller
{
    //
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'firstname' => "required|max:64|regex:/^[a-z ,.'-]+$/i",
            'middlename' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "required|max:64|regex:/^[a-z ,.'-]+$/i",
            "birthdate" => "required|max:64|date",
            "gender" => "required|max:64|integer",
        ]);

        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $customer = Customer::create($validator->validated());
        Cache::forget("customers");
        return $this->responseCreated($customer, "Customer has been created!");
        
    }

    public function index(){
        $customers = Cache::remember('customers', now()->addHours(1), function () {
            $customers = Customer::all();
            $customers->each(function($customer){
                
                $customer->created_at->timezone('Asia/Manila');
                $customer->updated_at->timezone('Asia/Manila');
                $customer->created_at = $customer->created_at->toDateTimeString();
                $customer->updated_at = $customer->updated_at->toDateTimeString();
            });
            return $customers;
        });
        return $this->responseOk($customers, "Customers has been retrieved!");

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

        $customers = Cache::remember("customers.page=" . $page .".numOfData=" . $inputs['numOfData'], now()->addSeconds(30), function() use($inputs, $page){
            $customers = customer::limit($inputs['numOfData'])->offset(($page - 1) * $inputs['numOfData'])->get();
            $customers->each(function($customer){
                $customer->books;
            });
            return $customers;
        });
        if(!$customers->count()){
            return $this->responseNotFound();
        }
        return $this->responseOk($customers, "Customers has been retrieved!");

    }

    public function update(Request $request, Customer $customer){
        $validator = Validator($request->all(), [
            'firstname' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            'middlename' => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "lastname" => "sometimes|max:64|regex:/^[a-z ,.'-]+$/i",
            "birthdate" => "sometimes|max:64|date",
            "gender" => "sometimes|max:64|integer",
        ]);
        if($validator->fails()){
            return $this->responseBadRequest($validator);
        }

        $customer->update($validator->validated());
        Cache::forget("customers");
        return $this->responseOk($customer, "Customer has been updated!");
    }

    public function destroy(Customer $customer){
        $customer->books()->delete();
        $customer->delete();
        Cache::forget("customers");
        return $this->responseOk(null, "Customer has been deleted!");
    }
}
