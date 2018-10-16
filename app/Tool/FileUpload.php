<?php
/**
 * Created by PhpStorm.
 * User: ZRothschild
 * Date: 2018/1/19
 * Time: 16:16
 */

namespace App\Tool;


use Illuminate\Support\Facades\Storage;

class FileUpload
{

    /**
     * @param array $files
     * @param string $path
     * @return array
     */
    static function fileUploads(array $files,$path = 'idCard')
    {
        $tmpPath = [];
        foreach ($files as $key => $value){
            $tmpPath[$key] = Storage::putFile("public/$path/".date('Ymd'), $value);
        }
        return $tmpPath;
    }
}