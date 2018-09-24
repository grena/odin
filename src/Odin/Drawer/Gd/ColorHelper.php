<?php

declare(strict_types=1);

namespace Odin\Drawer\Gd;


class ColorHelper
{
    // $color received is the integer generated from imagecreatetruecolor()
    public static function rgbToHex(int $color)
    {
        $rgb = [0xFF & ($color >> 0x10), 0xFF & ($color >> 0x8), 0xFF & $color];
        list($r, $g, $b) = $rgb;

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    // #ff00ff -> array(255, 0, 255) or #f0f -> array(255, 0, 255)
    public static function hexToRgb($color): array
    {
        $color = str_replace('#', '', $color);
        $s = strlen($color) / 3;

        $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));

        return $rgb;
    }
}
