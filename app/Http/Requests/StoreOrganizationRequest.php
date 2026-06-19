<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'yandex_url' => [
                'required',
                'url',
                'regex:/https?:\/\/(yandex\.ru|ya\.ru)\/maps\//',
            ],
        ];
    }
}