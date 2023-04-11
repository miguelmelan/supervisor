<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FileUploadController extends Controller
{
    public function storeFile(StoreFileRequest $request)
    {
        return $this->store($request);
    }

    public function storeImage(StoreImageRequest $request)
    {
        return $this->store($request);
    }

    private function store($request)
    {
        $request->validated();
        $path = '';
        if ($request->hasFile('path')) {
            $path = $request->file('path')->store('storage/uploads');
        }

        FileUpload::create([
            'path' => $path,
        ]);

        return Redirect::back();
    }
}
