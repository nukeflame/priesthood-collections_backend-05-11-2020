<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Models\Media;
use Carbon\Carbon;
use App\User;

trait FileUploadTrait
{

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request)
    {
        $uploadPath = storage_path(env('UPLOAD_PATH') . 'app/public/assets/images');
        $thumbPath = storage_path(env('UPLOAD_PATH') . 'app/public/assets/thumb');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0775);
            mkdir($thumbPath, 0775);
        }
        $finalRequest = $request;
        $mediaFiles = [] ;

        if ($finalRequest->has('checkGallery')) {
            foreach ($request->attachedFiles as $fileData) {
                $filename = Str::random(32).'.'.$fileData->getClientOriginalExtension();
                $image = Image::make($fileData);
                //thumb
                Image::make($fileData)->resize(50, 50)->save($thumbPath . '/' . $filename);
                $width = $image->width();
                $height = $image->height();
                $image->save($uploadPath . '/' . $filename);
                $m = new Media();
                $m->name = $filename;
                $m->width = $width;
                $m->height = $height;
                $m->product_thumb = 0;
                $m->user_id = $request->userId;
                $m->save();
                array_push($mediaFiles, $m->id);
                $finalRequest = new Request(array_merge($finalRequest->all(), ['attachedFiles' => $mediaFiles]));
            }
        }

        if ($finalRequest->has('checkThumb')) {
            if ($request->hasFile('productThumb')) {
                $userFile = User::find($request->userId);
                // //
                $fileData = $request->productThumb;
                $filename = Str::random(32).'.'.$fileData->getClientOriginalExtension();
                $image = Image::make($fileData);
                //thumb
                Image::make($fileData)->resize(50, 50)->save($thumbPath . '/' . $filename);
                $width = $image->width();
                $height = $image->height();
                $image->save($uploadPath . '/' . $filename);
                $media = new Media();
                $media->name = $filename;
                $media->width = $width;
                $media->height = $height;
                $media->product_thumb = 1;
                $media->user_id = $request->userId;
                $media->save();
                $finalRequest = new Request(array_merge($finalRequest->all(), ['productThumb' => $media->name, 'productThumbId' => $media->id]));
            }
        }
        
        return $finalRequest;
    }

    // save base64 image
    public function savebase64(Request $request)
    {
        $uploadPath = public_path(env('UPLOAD_PATH') . '/assets');
        $thumbPath = public_path(env('UPLOAD_PATH') . '/assets/thumb');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777);
            mkdir($thumbPath, 0777);
        }

        $finalRequest = $request;

        if ($request->has("avatar") && $request->avatar !== null) {
            $file = $request->avatar;

            // split base64 data
            $splited = explode(',', $file, 2);
            $mime = $splited[0];
            $data = $splited[1];

            // mime split without base64
            $mime_splited = explode(';', $mime, 2);
            $mime_ext = explode('/', $mime_splited[0], 2);

            // check if is ext
            if (count($mime_ext) == 2) {
                $extension = $mime_ext[1];
                if ($extension == 'jpeg') {
                    $extension = 'jpg';
                }

                $filename = str_random(10) . '.' . $extension;
                // decode base64
                $decodeImg = base64_decode($data);
                Image::make($decodeImg)->save($uploadPath . '/' . $filename);

                $finalRequest = new Request(array_merge($finalRequest->all(), ["avatar" => $filename]));
            }
        }

        return $finalRequest;
    }
}
