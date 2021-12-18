<?php

namespace App\Services;

use App\Models\Video;
use Google\Cloud\Storage\Bucket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class VideoService
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
        return Video::find($id);
    }

    public function create(UploadedFile $file, $parentModel = null)
    {
        $filename = Str::random(40) . '.' . $file->extension();
        $videoReference = $this->bucket->upload($file->get(), ['name' => $filename]);

        $expiresAt = strtotime('+1 year');
        if ($videoReference->exists()) {
            $link = $videoReference->signedUrl($expiresAt);
        }

        $video = new Video([
            'link' => $link,
            'name' => $filename,
        ]);
        $video->videoable()->associate($parentModel);
        $video->save();

        return $video;
    }

    public function delete($id)
    {
        $video = $this->find($id);
        $numDeletedVideo = $video->delete();

        $videoReference = $this->bucket->object($video->name);
        $videoReference->delete();

        return $numDeletedVideo;
    }
}
