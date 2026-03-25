<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:'.config('files.max_upload_count')],
            'files.*' => [
                File::types(config('files.allowed_mimes'))
                    ->max(config('files.max_size_kb')),
            ],
        ];
    }
}
