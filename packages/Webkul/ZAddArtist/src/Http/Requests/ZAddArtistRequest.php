<?php

namespace Webkul\ZAddArtist\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ZAddArtistRequest extends FormRequest
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
            'name' => ['required'],
            'slug' => ['required', 'regex:/^[0-9a-z\-]+$/', 'unique:artistes'],
            'content' => ['nullable'],
            'facebook' => ['nullable'],
            'instagram' => ['nullable'],
            'tiktok' => ['nullable'],
            'soundcloud' => ['nullable'],
            'youtube' => ['nullable'],
            'visible' => ['nullable'],
            'image' => ['nullable', 'image']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => $this->input('slug') ?: Str::slug($this->input('name')),
        ]);
    
    }
}
