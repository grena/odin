<?php

declare(strict_types=1);

namespace Odin\Astronomical;

use MapGenerator\PerlinNoiseGenerator;
use Odin\Drawer\Gd\ColorHelper;
use Odin\Orchestrator\LayerOrchestrator;

class Nebulae
{
    private $image;

    private $hexColor = '#FFF';

    private $width;
    private $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function setColor(string $hexColor)
    {
        $this->hexColor = $hexColor;
    }

    public function render()
    {
        list($r, $g, $b) = ColorHelper::hexToRgb($this->hexColor);

        $seed = rand();
        $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($this->image);

        $nebulaeDisplayThreshold = 180; // the less, the more we display the nebulae (max 255)
        $nebulaeOpacity = 120; // 0 - 127 (127 = fully transparent) 115 ok

        $gen = new PerlinNoiseGenerator();
        $size = $this->width;
        $gen->setPersistence(0.75);
        $gen->setSize($size);
        $gen->setMapSeed($seed);
        $map = $gen->generate();

        $max = 0;
        $min = PHP_INT_MAX;
        for ($iy = 0; $iy < $this->height; $iy++) {
            for ($ix = 0; $ix < $this->width; $ix++) {
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

        for ($x = 0; $x < $this->width; ++$x) {
            for ($y = 0; $y < $this->height; ++$y) {
                $h = 255 * ($map[$y][$x] - $min) / $diff;
                $h = intval($h);
                $color = imagecolorallocatealpha($this->image, $h, $h, $h, $nebulaeOpacity);

                if ($h > $nebulaeDisplayThreshold) { // draw only if white > $nebulaeDisplayThreshold
                    imagesetpixel($this->image, $x, $y, $color);
                }
            }
        }

        $this->filterMultiplyColor($this->image, $r, $g, $b);

        // TODO: find a way to blur the nebulae without losing the opacity
        return $this->image;
    }

    private function initializeImage(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->image, true);
        $transparentBackground = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
        imagefill($this->image, 0, 0, $transparentBackground);
    }

    private function filterMultiplyColor($canvas, $filter_r, $filter_g, $filter_b)
    {
        $width = imagesx($canvas);
        $height = imagesy($canvas);

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $rgb = imagecolorat($canvas, $x, $y);
                $TabColors = imagecolorsforindex($canvas, $rgb);
                $color_r = intval(floor($TabColors['red'] * $filter_r / 255));
                $color_g = intval(floor($TabColors['green'] * $filter_g / 255));
                $color_b = intval(floor($TabColors['blue'] * $filter_b / 255));
                $color_alpha = $TabColors['alpha'];
                $newcol = imagecolorallocatealpha($canvas, $color_r, $color_g, $color_b, $color_alpha);

                if ($TabColors['alpha'] < 127) {
                    imagesetpixel($canvas, $x, $y, $newcol);
                }
            }
        }
    }
}
