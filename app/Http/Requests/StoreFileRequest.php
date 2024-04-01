<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{
    protected function prepareForValidation(): void
    {
        $paths = array_filter($this->relative_paths ?? [], fn($el) => $el != null);

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
            'file_tree' => $this->buildFileTree($this->relative_paths, $data['files'])
        ]);
    }

    private function buildFileTree($filePaths, $files): array
    {
        $filePaths = array_slice($filePaths, 0, count($files));
        $filePaths = array_filter($filePaths, fn($el) => $el != null);

        $tree = [];

        foreach ($filePaths as $index => $filePath) {
            $parts = explode('/', $filePath);

            $currentNode = &$tree;
            foreach ($parts as $i => $part) {
                if (!isset($currentNode[$part])) {
                    $currentNode[$part] = [];
                }

                if ($i === count($parts) - 1) {
                    $currentNode[$part] = $files[$index];
                } else {
                    $currentNode = &$currentNode[$part];
                }
            }
        }

        return $tree;
    }

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
                'files.*' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) { //custom validation rule
                        /** @var $value UploadedFile */
                        if (!$this->directory_name) { // if we are not creating a directory
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
}
