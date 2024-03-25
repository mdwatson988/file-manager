<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDirectoryRequest;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class FileController extends Controller
{
    public function myFiles(): Response
    {
        return Inertia::render('MyFiles');
    }

    public function createDirectory(StoreDirectoryRequest $request): void
    {
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        $file = new File();
        $file->is_directory = 1;
        $file->name = $data['name'];

        $parent->appendNode($file);
    }

    private function getRoot(): File
    {
        return File::query()
            ->whereIsRoot()
            ->where('created_id', Auth::id())
            ->firstOrFail();
    }
}
