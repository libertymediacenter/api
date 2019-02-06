<?php

namespace App\Http\Requests;

use App\Models\Library;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LibraryUpdateRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'          => 'sometimes|string',
            'type'          => [
                'sometimes',
                'string',
                Rule::in([Library::MOVIE, Library::SHOW, Library::SPORTS, Library::OTHER]),
            ],
            'metadata_lang' => 'sometimes|string',
            'path'          => 'sometimes|string',
        ];
    }
}
