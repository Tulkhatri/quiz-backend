<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
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
            'title'=>'required|string|max:255',
            'category_id'=>'required',
            'difficulty_level_id'=>'required',
            'time_limit_minutes'=>'required'
             ];
    }

     public function messages(): array
    {
        return [
            'title.required' => 'Quiz title is required.',
            'title.string' => 'Quiz title must be a valid string.',
            'title.max' => 'Quiz title may not be greater than 255 characters.',
            'category_id.required' => 'Please select category',
            'difficulty_level_id.required' => 'Please select level',
            'time_limit_minutes.required' => 'Please enter time in minute',
        ];
    }
}
