<?php

namespace App\Services;

use App\Models\Image;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ImageService
{
    public function create($file, $parentModel = null)
    {
        $uploadedImage = Firebase::storage()
            ->getBucket()
            ->upload($file->get(), ['name' => '']);

        $image = Image::create([
            // TODO: this link is not correct and will be
            // changed in the future
            'link' => $uploadedImage->info()['mediaLink'],
        ]);
        if ($parentModel !== null) {
            $image->imageable()->associate($parentModel);
        }

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
