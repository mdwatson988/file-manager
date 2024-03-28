<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDirectoryRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
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

        return Inertia::render('MyFiles', compact('files', 'directory'));
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

    private function getRoot(): File
    {
        return File::query()
            ->whereIsRoot()
            ->where('created_by', Auth::id())
            ->firstOrFail();
    }
}
