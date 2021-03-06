<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class ServiceRequest extends FormRequest
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
            'serviceName' => 'required|unique_with:service,serviceSize',
            'serviceCategoryId' => 'required',
            'serviceSize' => 'required',
            'servicePrice' => 'numeric|required|between:0,99999999.99'
        ];
    }

    public function messages()
    {
        return [
            'serviceName.unique'  =>  'Service already exists',
            'serviceName.required' => 'Service name is required',
            'serviceCategoryId.required' => 'Category is required',
            'servicePrice.required' => 'Price is required',
            'servicePrice.numeric' => 'Price must be numeric',
            'serviceName.unique_with' => 'Service already exists',
        ];
    }

    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }
}
