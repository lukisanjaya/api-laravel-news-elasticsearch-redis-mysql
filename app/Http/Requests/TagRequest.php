<?php

namespace App\Http\Requests;

use App\Http\Validators\TagValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class TagRequest extends FormRequest
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
        $tag = new TagValidator();
        return $tag->rules();
    }


    public function messages()
    {
        return [
            'name.required' => 'Please fill the name'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation Errors',
            'errors'    => $validator->errors()
        ], Response::HTTP_BAD_REQUEST));
    }
}
