<?php

namespace App\Services;

use App\Models\Image;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ImageService
{
    public function create($file, $parentModel = null)
    {
        $file_name = $file->getClientOriginalName();
        $localfolder = public_path('firebase-temp-uploads') . '/';
        if ($file->move($localfolder, $file_name)) {
            $uploadedfile = fopen($localfolder . $file_name, 'r');
            app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $file_name]);
            unlink($localfolder . $file_name);
        }
        else {
            // throw error
        }

        $expiresAt = strtotime('+1 year');  
        $imageReference = app('firebase.storage')->getBucket()->object($file_name);  
        if ($imageReference->exists())
            $link = $imageReference->signedUrl($expiresAt);

        $image = Image::create([
            'link' => $link,
            'imageable_type' => get_class($parentModel),
            'imageable_id' => $parentModel->id,
        ]);
        // if ($parentModel !== null) {
        //     $image->imageable()->associate($parentModel);
        // }

        return $image;
    }

    public function createMany($files, $parentModel = null)
    {
        $images = [];
        foreach ($files as $file) {
            $images[] = $this->create($file, $parentModel);
        }

        return $images;
    }

    public function delete($id)
    {
        return Image::destroy($id);
    }

    public function deleteMany($ids)
    {
        $numDeletedImages = 0;
        foreach ($ids as $id) {
            $numDeletedImages += $this->delete($id);
        }

        return $numDeletedImages;
    }
}
