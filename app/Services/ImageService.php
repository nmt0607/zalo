<?php

namespace App\Services;

use App\Models\Image;
use Google\Cloud\Storage\Bucket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * @var Bucket
     */
    protected $bucket;

    public function __construct()
    {
        $this->bucket = app('firebase.storage')->getBucket();
    }

    public function find($id)
    {
        return Image::find($id);
    }

    public function create(UploadedFile $file, $parentModel = null, $type = null)
    {
        $filename = Str::random(40) . '.' . $file->extension();
        $imageReference = $this->bucket->upload($file->get(), ['name' => $filename]);

        $expiresAt = strtotime('+1 year');
        if ($imageReference->exists()) {
            $link = $imageReference->signedUrl($expiresAt);
        }

        $image = Image::create([
            'link' => $link,
            'name' => $filename,
            'imageable_type' => get_class($parentModel),
            'imageable_id' => $parentModel->id,
            'type' => $type
        ]);

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
        $image = $this->find($id);
        $imageReference = $this->bucket->object($image->name);
        $imageReference->delete();

        return $image->delete();
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
