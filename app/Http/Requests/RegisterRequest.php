<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_name'=>'required|string|min:4|max:20',
            'password'=>'required|string|min:8',
        ];
        
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
         
        $response = response()->json(["success"=>false,"message"=>$validator->errors()->first()]);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
