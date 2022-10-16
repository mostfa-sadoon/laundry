<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait fileTrait
{
    function MoveImage($request_file,$public_path)
    {
        // This is Image Information ...
        $file = $request_file;
        $ext = $file->getClientOriginalExtension();
          //   $size = $file->getSize();
        // Move Image To Folder ..
        $fileNewName = 'file_' . time() . '.' . $ext;
        $file->move(public_path($public_path), $fileNewName);
        return $fileNewName;
    }
}
