<?php

declare(strict_types=1);

namespace Odin\Drawer\Gd;

class GradientAlpha
{
    public $image;

    // Constructor. Creates, fills and returns an image
    function __construct($w, $h, $d, $rgb, $as, $ae, $step = 0)
    {
        $this->width = $w;
        $this->height = $h;
        $this->direction = $d;
        $this->color = $rgb;
        $this->alphastart = $as;
        $this->alphaend = $ae;
        $this->step = intval(abs($step));

        // Attempt to create a blank image in true colors, or a new palette based image if this fails
        if (function_exists('imagecreatetruecolor')) {
            $this->image = imagecreatetruecolor($this->width, $this->height);
        } elseif (function_exists('imagecreate')) {
            $this->image = imagecreate($this->width, $this->height);
        } else {
            die('Unable to create an image');
        }

        $transparent = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
        imagealphablending($this->image, false);
        imagesavealpha($this->image, true);
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $transparent);

        // Fill it
        $this->fillalpha($this->image, $this->direction, $this->color, $this->alphastart, $this->alphaend);

        // Show it
//        $this->display($this->image);

        // Return it
//        return $this->image;
    }


    // Displays the image with a portable function that works with any file type
    // depending on your server software configuration
    function display($im)
    {
        if (function_exists("imagepng")) {
            header("Content-type: image/png");
            imagepng($im);
        } elseif (function_exists("imagegif")) {
            header("Content-type: image/gif");
            imagegif($im);
        } elseif (function_exists("imagejpeg")) {
            header("Content-type: image/jpeg");
            imagejpeg($im, "", 0.5);
        } elseif (function_exists("imagewbmp")) {
            header("Content-type: image/vnd.wap.wbmp");
            imagewbmp($im);
        } else {
            die("Doh ! No graphical functions on this server ?");
        }

        return true;
    }


    // The main function that draws the gradient
    function fillalpha($im, $direction, $rgb, $as, $ae)
    {
        list($r, $g, $b) = ColorHelper::hexToRgb($rgb);
        $a1 = $this->a2sevenbit($as);
        $a2 = $this->a2sevenbit($ae);

        switch ($direction) {
            case 'horizontal':
                $line_numbers = imagesx($im);
                $line_width = imagesy($im);
                break;
            case 'vertical':
                $line_numbers = imagesy($im);
                $line_width = imagesx($im);
                break;
            case 'ellipse':
                $width = imagesx($im);
                $height = imagesy($im);
                $rh = $height > $width ? 1 : $width / $height;
                $rw = $width > $height ? 1 : $height / $width;
                $line_numbers = min($width, $height);
                $center_x = $width / 2;
                $center_y = $height / 2;
                imagefill($im, 0, 0, imagecolorallocatealpha($im, $r, $g, $b, $a1));
                break;
            case 'ellipse2':
                $width = imagesx($im);
                $height = imagesy($im);
                $rh = $height > $width ? 1 : $width / $height;
                $rw = $width > $height ? 1 : $height / $width;
                $line_numbers = sqrt(pow($width, 2) + pow($height, 2));
                $center_x = $width / 2;
                $center_y = $height / 2;
                break;
            case 'circle':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = sqrt(pow($width, 2) + pow($height, 2));
                $center_x = $width / 2;
                $center_y = $height / 2;
                $rh = $rw = 1;
                break;
            case 'circle2':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = min($width, $height);
                $center_x = $width / 2;
                $center_y = $height / 2;
                $rh = $rw = 1;
                imagefill($im, 0, 0, imagecolorallocatealpha($im, $r, $g, $b, $a1));
                break;
            case 'square':
            case 'rectangle':
                $width = imagesx($im);
                $height = imagesy($im);
                $line_numbers = max($width, $height) / 2;
                break;
            case 'diamond':
                $width = imagesx($im);
                $height = imagesy($im);
                $rh = $height > $width ? 1 : $width / $height;
                $rw = $width > $height ? 1 : $height / $width;
                $line_numbers = min($width, $height);
                break;
            default:
        }

        for ($i = 0; $i < $line_numbers; $i = $i + 1 + $this->step) {
            $old_a = (empty($a)) ? $a2 : $a;
            $a = ($a2 - $a1 != 0) ? intval($a1 + ($a2 - $a1) * ($i / $line_numbers)) : $a1;

            if ("$old_a" != "$a") {
                $fill = imagecolorallocatealpha($im, $r, $g, $b, $a);
            }
            switch ($direction) {
                case 'vertical':
                    imagefilledrectangle($im, 0, $i, $line_width, $i + $this->step, $fill);
                    break;
                case 'horizontal':
                    imagefilledrectangle($im, $i, 0, $i + $this->step, $line_width, $fill);
                    break;
                case 'ellipse':
                case 'ellipse2':
                case 'circle':
                case 'circle2':
                    imagefilledellipse(
                        $im,
                        $center_x,
                        $center_y,
                        ($line_numbers - $i) * $rh,
                        ($line_numbers - $i) * $rw,
                        $fill
                    );
                    break;
                case 'square':
                case 'rectangle':
                    imagefilledrectangle(
                        $im,
                        $i * $width / $height,
                        $i * $height / $width,
                        $width - ($i * $width / $height),
                        $height - ($i * $height / $width),
                        $fill
                    );
                    break;
                case 'diamond':
                    imagefilledpolygon(
                        $im,
                        array(
                            $width / 2,
                            $i * $rw - 0.5 * $height,
                            $i * $rh - 0.5 * $width,
                            $height / 2,
                            $width / 2,
                            1.5 * $height - $i * $rw,
                            1.5 * $width - $i * $rh,
                            $height / 2,
                        ),
                        4,
                        $fill
                    );
                    break;
                default:
            }
        }
    }

    function a2sevenbit($alpha)
    {
        return (abs($alpha - 255) >> 1);
    }
}
