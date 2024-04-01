<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDirectoryRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FileController extends Controller
{
    public function renderMyFiles(string $directory = null): Response
    {
        if ($directory) {
            $directory = File::query()
                ->where('path', $directory)
                ->where('created_by', Auth::id())
                ->firstOrFail();
        }
        $directory ??= $this->getRoot();

        $files = File::query()
            ->where('parent_id', $directory->id)
            ->where('created_by', Auth::id())
            ->orderBy('is_directory', 'desc')
            ->orderBy('created_at')
            ->paginate(10);

        $files = FileResource::collection($files);

        $ancestors = FileResource::collection([...$directory->ancestors, $directory]);
//        $directory = new FileResource($directory);

        return Inertia::render('MyFiles', compact('files', 'directory', 'ancestors'));
    }

    public function createDirectory(StoreDirectoryRequest $request): void
    {
        $data = $request->validated();

        $parent = $request->parent;
        $parent ??= $this->getRoot();

        $file = new File();
        $file->is_directory = 1;
        $file->name = $data['name'];

        $parent->appendNode($file);
    }

    public function store(StoreFileRequest $request): void
    {
        $data = $request->validated();
        $fileTree = $request->file_tree;
        $parent = $request->parent;
        $user = $request->user();

        $parent ??= $this->getRoot();

        if (!empty($fileTree)) {
            $this->storeFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                $this->storeFile($file, $user, $parent);
            }
        }
    }

    private function storeFile(UploadedFile $file, User $user, File $parent): void
        /** @var UploadedFile $file */
    {
        $path = $file->store('/files/' . $user->id);

        $model = new File();
        $model->storage_path = $path;
        $model->is_directory = 0;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        $parent->appendNode($model);
    }

    private function storeFileTree(array $fileTree, File $parent, User $user): void
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $directory = new File();
                $directory->name = $name;
                $directory->is_directory = 1;

                $parent->appendNode($directory);

                $this->storeFileTree($file, $directory, $user);
            } else {
                $this->storeFile($file, $user, $parent);
            }
        }
    }

    private function getRoot(): File
    {
        return File::query()
            ->whereIsRoot()
            ->where('created_by', Auth::id())
            ->firstOrFail();
    }
}
