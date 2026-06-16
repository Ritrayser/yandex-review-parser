<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetReviewsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'page' => $this->input('page', 1),
            'per_page' => $this->input('per_page', 50),
        ]);
    }
}