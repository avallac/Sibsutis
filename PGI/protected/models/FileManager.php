<?php

class FileManager
{
    public static function getImages($type)
    {
        $img = [];
        if ($handle = opendir('img/')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry !== '.' && $entry !== '..' && preg_match("/$type/", $entry)) {
                    $img[] = $entry;
                }
            }
        }
        sort($img);
        return $img;
    }
}