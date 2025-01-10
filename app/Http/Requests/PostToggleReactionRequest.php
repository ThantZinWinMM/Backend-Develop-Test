<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostToggleReactionRequest extends FormRequest
{
    // public function authorize()
    // {
    //     return true;
    // }

    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'like' => filter_var($this->input('like'), FILTER_VALIDATE_BOOLEAN),
    //     ]);
    // }

    // public function rules()
    // {
    //     return [
    //         'post_id' => 'required|int|exists:posts,id',
    //         'like'    => 'required|boolean',
    //     ];
    // }

    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'like' => $this->normalizeLike(),
        ]);
    }

    private function normalizeLike(): bool
    {
        return filter_var($this->input('like'), FILTER_VALIDATE_BOOLEAN);
    }

    public function rules(): array
    {
        return [
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'like'    => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'post_id.required' => 'A post ID is required.',
            'post_id.exists'   => 'The selected post does not exist.',
            'like.required'    => 'You must specify whether you like the post.',
            'like.boolean'     => 'The like field must be true or false.',
        ];
    }
}
