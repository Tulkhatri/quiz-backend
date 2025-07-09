<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
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
            'question_id'=>'required',
            'answer_text'=>'required|string',
            'is_correct'=>'required|string',
             ];
    }
       public function messages(): array
    {
        return [
            'answer_text.required' => 'Question is required.',
            'answer_text.string' => 'Question must be a valid string.',
            'question_id.required' => 'Please select question',
            'is_correct.required' => 'Please select one',
        ];
    }
}
