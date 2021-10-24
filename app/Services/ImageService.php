<?php

namespace App\Services;

use App\Models\Image;

class ImageService
{
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
