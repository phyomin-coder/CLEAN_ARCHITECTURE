<?php

namespace App\Http\Requests\ToDo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateToDoRequest extends FormRequest
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
            'job' =>  [
                'required',
                'min:5',
                Rule::unique('to_dos','job')->ignore($this->route('todo')),
            ],
            'description' => 'required|string|min:5|max:255'
        ];
    }

     public function messages(): array
    {
        return [
            'job.required' => 'The job field is required.',
            'job.min' => 'The job must be at least 5 characters.',
            'job.unique' => 'This job is already in use.',
            'description.required' => 'The description field is required.', 
            'description.max' => 'The description field must be at most 255 characters.', 
        ];
    }
}
