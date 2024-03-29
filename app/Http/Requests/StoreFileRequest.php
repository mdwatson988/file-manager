<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{
    protected function prepareForValidation()
    {
        $paths = array_filter($this->relative_paths ?? [], fn($el) => $el != null);
//        dd($paths);

        $this->merge([
            'relative_paths' => $paths,
            'directory_name' => $this->detectDirectoryName($paths),
        ]);
    }

    public function detectDirectoryName($paths): ?string
    {
        if (!$paths) {
            return null;
        }

        return explode('/', $paths[0])[0];
    }

    protected function passedValidation(): void
    {
        $data = $this->validated();

        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files'])
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(),
            [
                'files.*' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if (!$this->dirctory_name) { // if we are not creating a directory
                            /** @var $value UploadedFile */
                            $file = File::query()
                                ->where('name', $value->getClientOriginalName())
                                ->where('parent_id', $this->parent_id)
                                ->where('created_by', Auth::id())
                                ->whereNull('deleted_at')
                                ->exists();

                            if ($file) {
                                $fail('File "' . $value->getClientOriginalName() . '" already exists.');
                            }
                        }
                    }
                ],
                'relative_paths' => 'array',
                'directory_name' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) {
                        if ($value) { // if uploading a directory
                            /** @var $value UploadedFile */
                            $directory = File::query()
                                ->where('name', $value)
                                ->where('parent_id', $this->parent_id)
                                ->where('created_by', Auth::id())
                                ->whereNull('deleted_at')
                                ->exists();

                            if ($directory) {
                                $fail('Folder "' . $value . '" already exists.');
                            }
                        }
                    }
                ]
            ]);
    }

    private function buildFileTree($file_paths, $files)
    {
        
    }
}
