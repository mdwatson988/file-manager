<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreDirectoryRequest extends ParentIdBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'name' => [
                    'required',
                    Rule::unique('files', 'name')
                        ->where('created_by', Auth::id())
                        ->where('parent_id', $this->parent_id)
                        ->whereNull('deleted_at'),
                    ],
            ]);
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Folder ":input" already exists.'
        ];
    }
}
