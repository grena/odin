<?php

declare(strict_types=1);

namespace Odin\Drawer\Gd;

class Text
{
    public static function write($image, $text, $x, $y)
    {
        $font_file = './IBMPlexMono-Regular.ttf';
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefttext($image, 8, 0, $x, $y, $white, $font_file, $text);
    }
}
