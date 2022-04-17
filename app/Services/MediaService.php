<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;

/**
 * Description of Media
 *
 * @author satiro
 */
class MediaService
{
    private static $sizes = [
        'small' => 200,
    ];

    /**
     *
     * @param \SplFileInfo $file
     * @param Model $model
     * @param string $mediaType
     * @param string $folderPaph
     * @param type $main
     * @param type $isMain
     * @param type $order
     * @return type
     */
    public static function saveMedia($file, Model $model, string $mediaType, string $group, $main = 0, $isMain = 1, $order = 0)
    {
        $destinationPath = "{$mediaType}s/{$group}";

        $media = $model->media()->where('media_type', $mediaType)
                ->where('group', $group)
                ->where('main', $main)->first();
        //$url = self::saveFile($file, $destinationPath);
        $file_name = explode("/", $file);

        $url = "storage/$destinationPath/".$file_name[count($file_name)-1];

        $pathStorage = (config('filesystems.default') == 'local') ? 'public' : 'storage';

        $destinationPath = "$pathStorage/$destinationPath/".$file_name[count($file_name)-1];

        Storage::move("$file", "$destinationPath");

        if (!empty($media) && $isMain == 1) {
            $oldPath = $media->url;
            $media->url = $url;
            $media->save();
            self::deleteFile($oldPath);
            return $media;
        }
        return $model->media()->create([
            'media_type' => $mediaType,
            'url' => $url,
            'group' => $group,
            'main' => $main,
            'order' => $order,
        ]);
    }

    public static function saveFile(UploadedFile $file, string $folder = '') : array
    {
        $extension = $file->getClientOriginalExtension();
        $mime = str_replace('/', '-', $file->getMimeType());

        $pathStorage = "storage";

        if (!empty($folder)) {
            $pathStorage .= "/{$folder}";
        }

        if (config('filesystems.default') == 'local') {
            (new Filesystem())->ensureDirectoryExists(Storage::path($pathStorage));
        }

        Storage::put($pathStorage, $file);

        return [
            'path' => "{$pathStorage}/",
            'name' => $file->hashName(),
            'mime_type' => $mime,
            'extension' => $extension
        ];
    }

    public static function deleteFile($path)
    {
        return Storage::delete($path);
    }
}
