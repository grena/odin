<?php

include_once '../vendor/autoload.php';

use MapGenerator\PerlinNoiseGenerator;

const HEIGHT = 500;
const WIDTH = 500;

$seed = rand();
//$seed = 1811198798;

mt_srand($seed);

$canvas = imagecreatetruecolor(WIDTH, HEIGHT);

createNebulae($canvas, $seed, [0, 32, 84]);

createStars($canvas, 0, 50);
createStars($canvas, 0, 50);
createStars($canvas, 0, 50);
createStars($canvas, 0, 255);

createPlanet($canvas);

// Draw debug infos
$font_file = './IBMPlexMono-Regular.ttf';
$white = imagecolorallocate($canvas, 255, 255, 255);
imagefttext($canvas, 8, 0, 10, 20, $white, $font_file, 'Seed: '.$seed);

// Output and free from memory
header('Content-Type: image/png');
imagepng($canvas);
imagedestroy($canvas);

function createPlanet($canvas): void
{
    $WIDTH = imagesx($canvas);
    $HEIGHT = imagesy($canvas);

    $black = imagecolorallocatealpha($canvas, 0, 30, 0, 0);
    $blue = imagecolorallocate($canvas, 62, 86, 124);
    $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
    $planetSize = 250;

    $glowSize = 2;

    $planetCanvas = imagecreatetruecolor($WIDTH, $HEIGHT);
    imagealphablending($planetCanvas, false);
    imagesavealpha($planetCanvas, true);
    imagefilledrectangle($planetCanvas, 0, 0, $WIDTH, $WIDTH, $transparent);

    imagefilledellipse($planetCanvas, $WIDTH / 2, $HEIGHT / 2, $planetSize, $planetSize, $blue);
    outputLayer($planetCanvas, 'planet');

    $planetShadow = new gd_gradient_alpha($WIDTH / 1.5, $HEIGHT / 1.5 , 'ellipse', '#000', 0xFF, 0x00, 0);
    outputLayer($planetCanvas, 'shadow');

    $dest_image = imagecreatetruecolor(WIDTH, HEIGHT);
    imagesavealpha($dest_image, true);
    $trans_background = imagecolorallocatealpha($dest_image, 0, 0, 0, 127);
    imagefill($dest_image, 0, 0, $trans_background);

    imagecopy($dest_image, $planetCanvas, 0, 0, 0, 0, WIDTH, HEIGHT);
    imagecopy($dest_image, $planetShadow->image, 25, 25, 0, 0, WIDTH, HEIGHT);
    outputLayer($planetCanvas, 'planet+shadow');

    $planetMask = imagecreatetruecolor($WIDTH, $HEIGHT);
    imagealphablending($planetMask, false);
    imagesavealpha($planetMask, true);
    imagefilledrectangle($planetMask, 0, 0, $WIDTH, $WIDTH, $transparent);
    imagefilledellipse($planetMask, $WIDTH / 2, $HEIGHT / 2, $planetSize, $planetSize, $black);
    outputLayer($planetCanvas, 'planetmask');

    imagealphamask($dest_image, $planetMask);
    imagecopy($canvas, $dest_image, 0, 0, 0, 0, $WIDTH, $HEIGHT);
}

function imagealphamask( &$picture, $mask ) {
    // Get sizes and set up new picture
    $xSize = imagesx( $picture );
    $ySize = imagesy( $picture );
    $newPicture = imagecreatetruecolor( $xSize, $ySize );
    imagesavealpha( $newPicture, true );
    imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

    // Resize mask if necessary
    if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
        $tempPic = imagecreatetruecolor( $xSize, $ySize );
        imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
        imagedestroy( $mask );
        $mask = $tempPic;
    }

    // Perform pixel-based alpha map application
    for( $x = 0; $x < $xSize; $x++ ) {
        for( $y = 0; $y < $ySize; $y++ ) {
            $alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
            //small mod to extract alpha, if using a black(transparent) and white
            //mask file instead change the following line back to Jules's original:
            //$alpha = 127 - floor($alpha['red'] / 2);
            //or a white(transparent) and black mask file:
            //$alpha = floor($alpha['red'] / 2);
            $alpha = $alpha['alpha'];
            $color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
            //preserve alpha by comparing the two values
            if ($color['alpha'] > $alpha)
                $alpha = $color['alpha'];
            //kill data for fully transparent pixels
            if ($alpha == 127) {
                $color['red'] = 0;
                $color['blue'] = 0;
                $color['green'] = 0;
            }
            imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
        }
    }

    // Copy back to original picture
    imagedestroy( $picture );
    $picture = $newPicture;
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}

function imagegradientellipsealpha($image, $cx, $cy, $w, $h, $ic, $oc)
{
    $w = abs($w);
    $h = abs($h);
    $oc = array(0xFF & ($oc >> 0x10), 0xFF & ($oc >> 0x8), 0xFF & $oc);
    $ic = array(0xFF & ($ic >> 0x10), 0xFF & ($ic >> 0x8), 0xFF & $ic);

    $c0 = ($oc[0] - $ic[0]) / $w;
    $c1 = ($oc[1] - $ic[1]) / $w;
    $c2 = ($oc[2] - $ic[2]) / $w;

    $ot = $oc >> 24;
    $it = $ic >> 24;
    $ct = ($ot - $it) / $w;

    $i = 0;
    $j = 0;
    $is = ($w < $h) ? ($w / $h) : 1;
    $js = ($h < $w) ? ($h / $w) : 1;

    while (1) {
        $r = $oc[0] - floor($i * $c0);
        $g = $oc[1] - floor($i * $c1);
        $b = $oc[2] - floor($i * $c2);
        $t = $ot - floor($i * $ct);
        $c = imagecolorallocatealpha($image, $r, $g, $b, $t);
        imageellipse($image, $cx, $cy, $w - $i, $h - $j, $c);
        if ($i < $w) {
            $i += $is;
        }
        if ($j < $h) {
            $j += $js;
        }
        if ($i >= $w && $j >= $h) {
            break;
        }
    }
}

function imagegradientellipse($image, $cx, $cy, $w, $h, $ic, $oc)
{
    $w = abs($w);
    $h = abs($h);
    $oc = array(0xFF & ($oc >> 0x10), 0xFF & ($oc >> 0x8), 0xFF & $oc);
    $ic = array(0xFF & ($ic >> 0x10), 0xFF & ($ic >> 0x8), 0xFF & $ic);
    $c0 = ($oc[0] - $ic[0]) / $w;
    $c1 = ($oc[1] - $ic[1]) / $w;
    $c2 = ($oc[2] - $ic[2]) / $w;
    $i = 0;
    $j = 0;
    $is = ($w < $h) ? ($w / $h) : 1;
    $js = ($h < $w) ? ($h / $w) : 1;
    while (1) {
        $r = $oc[0] - floor($i * $c0);
        $g = $oc[1] - floor($i * $c1);
        $b = $oc[2] - floor($i * $c2);
        $c = imagecolorallocate($image, $r, $g, $b);
        imagefilledellipse($image, $cx, $cy, $w - $i, $h - $j, $c);
        if ($i < $w) {
            $i += $is;
        }
        if ($j < $h) {
            $j += $js;
        }
        if ($i >= $w && $j >= $h) {
            break;
        }
    }
}

/**
 * @param $canvas
 */
function createNebulae($canvas, $seed, $colorsRgb = []): void
{
    $r = 0;
    $g = 0;
    $b = 0;

    if (!empty($colorsRgb)) {
        list($r, $g, $b) = $colorsRgb;
    }

    $WIDTH = imagesx($canvas);
    $HEIGHT = imagesy($canvas);

    $nebulaeDisplayThreshold = 150; // the less, the more we display the nebulae (max 255)
    $nebulaeOpacity = 90; // 0 - 127 (127 = fully transparent) 115 ok

    $gen = new PerlinNoiseGenerator();
    $size = $WIDTH;
    $gen->setPersistence(1);
    $gen->setSize($size);
    $gen->setMapSeed($seed);
    $map = $gen->generate();
    $max = 0;
    $min = PHP_INT_MAX;
    for ($iy = 0; $iy < $HEIGHT; $iy++) {
        for ($ix = 0; $ix < $WIDTH; $ix++) {
            $h = $map[$iy][$ix];
            if ($min > $h) {
                $min = $h;
            }
            if ($max < $h) {
                $max = $h;
            }
        }
    }
    $diff = $max - $min;
    for ($iy = 0; $iy < $HEIGHT; $iy++) {
        for ($ix = 0; $ix < $WIDTH; $ix++) {
            $h = 255 * ($map[$iy][$ix] - $min) / $diff;
            $color = imagecolorallocatealpha($canvas, $h, $h, $h, $nebulaeOpacity);

            if ($h > $nebulaeDisplayThreshold) { // draw only if white > $nebulaeDisplayThreshold
                imagesetpixel($canvas, $ix, $iy, $color);
            }
        }
    }

    filterMultiplyColor($canvas, $r, $g, $b);
}

function createStars($canvas, int $minShine = 0, int $maxShine = 255): void
{
    $minShineHalo = 0;
    $maxShineHalo = 8;

    $percentSuperStars = 10;

    $numberOfStars = rand(HEIGHT, WIDTH);

    for ($i = 0; $i < $numberOfStars; $i++) {
        $greyScale = rand($minShine, $maxShine);
        $greyScaleHalo = rand($minShineHalo, $maxShineHalo);
        $color = imagecolorallocate($canvas, $greyScale, $greyScale, $greyScale);
        $haloSize = $greyScale / 10;

        $x = rand(0, WIDTH);
        $y = rand(0, HEIGHT);

        // For shiny stars (> 200 white), 30% chance to have an halo
        if (rand(0, 3) === 3 && $greyScale > 200) {
            // Create the light halo of a star, from darker big, to lighter in the center : ((o))
            $opacities = [];
            for ($j = $haloSize; $j > 0; $j--) {
                $opacity = (127 / $haloSize) * $j;
                $opacities[] = $opacity;
                $colorBlur = imagecolorallocatealpha($canvas, $greyScaleHalo, $greyScaleHalo, $greyScaleHalo, $opacity);

                imagefilledellipse($canvas, $x, $y, $j, $j, $colorBlur);
            }
        }

        // 10% chance super star
        if (rand(0, 100) < $percentSuperStars) {
            imagefilledellipse($canvas, $x, $y, 2, 2, $color);
        } else {
            imagesetpixel($canvas, $x, $y, $color);
        }
    }
}

function imagecopyAdd(&$dst_im, &$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
{
    for ($i = 0; $i < $src_w; $i++) {
        for ($c = 0; $c < $src_h; $c++) {
            $rgb1 = imagecolorat($dst_im, ($i + $dst_x), ($c + $dst_y));
            $colors1 = imagecolorsforindex($dst_im, $rgb1);
            $rgb2 = imagecolorat($src_im, ($i + $src_x), ($c + $src_y));
            $colors2 = imagecolorsforindex($src_im, $rgb2);
            $r = $colors1["red"] + $colors2["red"];
            if ($r > 255) {
                $r = 255;
            }
            $g = $colors1["green"] + $colors2["green"];
            if ($g > 255) {
                $g = 255;
            }
            $b = $colors1["blue"] + $colors2["blue"];
            if ($b > 255) {
                $b = 255;
            }
            $color = imagecolorallocate($dst_im, $r, $g, $b);
            imagesetpixel($dst_im, ($i + $dst_x), ($c + $dst_y), $color);
        }
    }
}

function filterMultiplyColor($canvas, $filter_r, $filter_g, $filter_b)
{
    $width = imagesx($canvas);
    $height = imagesy($canvas);

    for ($x = 0; $x < $width; ++$x) {
        for ($y = 0; $y < $height; ++$y) {
            $rgb = imagecolorat($canvas, $x, $y);
            $TabColors = imagecolorsforindex($canvas, $rgb);
            $color_r = floor($TabColors['red'] * $filter_r / 255);
            $color_g = floor($TabColors['green'] * $filter_g / 255);
            $color_b = floor($TabColors['blue'] * $filter_b / 255);
            $newcol = imagecolorallocate($canvas, $color_r, $color_g, $color_b);

            if ($TabColors['alpha'] < 127) {
                imagesetpixel($canvas, $x, $y, $newcol);
            }
        }
    }
}

function multiplyImage($dst, $src)
{
    $ow = imagesx($dst);
    $oh = imagesy($dst);

    $inv255 = 1.0 / 255.0;

    $c = imagecreatetruecolor($ow, $oh);
//    $c = $dst;

    for ($x = 0; $x < $ow; ++$x) {
        for ($y = 0; $y < $oh; ++$y) {
            $rgb_src = imagecolorsforindex($src, imagecolorat($src, $x, $y));
            $rgb_dst = imagecolorsforindex($dst, imagecolorat($dst, $x, $y));

            $r = $rgb_src['red'] * $rgb_dst['red'] * $inv255;
            $g = $rgb_src['green'] * $rgb_dst['green'] * $inv255;
            $b = $rgb_src['blue'] * $rgb_dst['blue'] * $inv255;

            $rgb = imagecolorallocate($c, $r, $g, $b);
            imagesetpixel($c, $x, $y, $rgb);
        }
    }

//    debug($c);
//    return $c;
}

function debug($canvas)
{
    // Output and free from memory
    header('Content-Type: image/png');
    imagepng($canvas);
    imagedestroy($canvas);
    exit;
}

function outputLayer($image, string $name): void
{
    $outputDir = '../rendered';
    imagepng($image, sprintf('%s/%s.png', $outputDir, $name), 9);
}


class gd_gradient_alpha
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

        list($r, $g, $b) = $this->hex2rgb($rgb);
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

    // #ff00ff -> array(255,0,255) or #f0f -> array(255,0,255)
    function hex2rgb($color)
    {
        $color = str_replace('#', '', $color);
        $s = strlen($color) / 3;
        $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));

        return $rgb;
    }

    function a2sevenbit($alpha)
    {
        return (abs($alpha - 255) >> 1);
    }

}
