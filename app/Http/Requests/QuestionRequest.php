<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text'=>'required|string',
            'quiz_id'=>'required',
            'id' => 'nullable',
             ];
    }

     public function messages(): array
    {
        return [
            'question_text.required' => 'Question is required.',
            'question_text.string' => 'Question must be a valid string.',
            'quiz_id.required' => 'Please select quiz',
        ];
    }
}
