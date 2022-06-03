<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'user_id' => 'exists:users,id',
            'status' => 'required|boolean',
            'image' => 'nullable|string',
            'questions' => 'array',
            'description' => 'nullable|string|max:1000',
            'expire_date' => 'nullable|date|after:tomorrow',
        ];
    }
}
