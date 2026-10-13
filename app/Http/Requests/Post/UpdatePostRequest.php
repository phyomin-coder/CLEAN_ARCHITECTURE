<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;
class UpdatePostRequest extends FormRequest
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
            'title' => [
                'required',
                'min:5',
                Rule::unique('posts','title')->ignore($this->route('post')),
            ],
            'description' => 'required|string|min:5|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.min' => 'The title must be at least 5 characters.',
            'title.unique' => 'This title is already in use.',
            'description.required' => 'The description field is required.', 
            'description.max' => 'The description field must be at most 255 characters.', 
        ];
    }
}
