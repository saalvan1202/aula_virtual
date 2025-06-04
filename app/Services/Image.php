<?php

namespace App\Services;

class Image
{
    public static function encode64($imagecode)
    {
        if(empty($imagecode)) return $imagecode;
        $content = base64_encode($imagecode);
        return static::base64($content);
    }
    public static function base64($content): string
    {
        return 'data:image/png;base64,' . $content;
    }
}
