<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    public static function uploadFile(Request $request, $title = '', $folderImage = 'images')
    {
        try {
            $folderImage = 'images/' . date('Y') . '/' . date('m') . '/' . date('d');
            if (empty($title)) {
                $imageName   = Str::random('5') . '-' . time() . '-' . '.' . $request->image->getClientOriginalExtension();
            } else {
                $imageName   = Str::slug($title) . '-' . Str::random('5') . '.' . $request->image->getClientOriginalExtension();
            }
            $fileName    = Storage::disk('public')->putFileAs($folderImage, $request->image, $imageName);
            return $fileName;
        } catch (\Throwable $th) {
            return "";
        }
    }
}
