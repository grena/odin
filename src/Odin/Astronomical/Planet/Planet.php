<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet;


use MapGenerator\PerlinNoiseGenerator;
use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\GradientAlpha;
use Odin\Orchestrator\LayerOrchestrator;

class Planet
{
    private $image;

    private $width;
    private $height;

    private $planetSize = 250;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function render()
    {
        $palette = $this->selectPalette();

        $this->image = $this->initializeImage();
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($this->image);
        $layerOrchestrator->addLayer($this->generateGlow($palette));

        $newLayer = $this->initializeImage();
        $layerOrchestratorT = new LayerOrchestrator();
        $layerOrchestratorT->setBaseLayer($newLayer);
        $layerOrchestratorT->addLayer($this->generatePlanet());
        $layerOrchestratorT->addLayer($this->generateSurface($palette), $this->width/4, $this->height/4);
        $layerOrchestratorT->addLayer($this->generateLittleShadow());
        $layerOrchestratorT->addLayer($this->generateShadow(), -20, rand(-30, 110));

        // Cut the extra shadow
        $this->applyMask($newLayer, $this->generateMask());

        $layerOrchestrator->addLayer($newLayer);

        return $layerOrchestrator->render();
    }

    private function generatePlanet()
    {
        $planet = $this->initializeImage();
        $blue = imagecolorallocate($planet, 62, 86, 124);
        imagefilledellipse($planet, $this->width / 2, $this->height / 2, $this->planetSize, $this->planetSize, $blue);

        return $planet;
    }

    private function generateGlow(array $palette)
    {
        $layer = $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        $w = (int) ($this->planetSize + 60);
        $h = (int) ($this->planetSize + 60);
        $glow = new GradientAlpha($w, $h, 'ellipse', $palette['water'], 0x00, 0x44, 0);

        $layerOrchestrator->addLayer($glow->image, $this->width / 2 - $w/2, $this->height / 2 - $h/2);

        return $layer;
    }

    private function generateLittleShadow()
    {
        $layer = $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        $w = (int) ($this->planetSize + 10);
        $h = (int) ($this->planetSize + 10);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#FFF', 0x33, 0x00, 0);

        $layerOrchestrator->addLayer($shadow->image, $this->width / 2 - $w/2, $this->height / 2 - $h/2);

        return $layer;
    }

    private function generateShadow()
    {
        $layer = $this->initializeImage(900, 900);
        $w = (int) ($this->planetSize * 1.4);
        $h = (int) ($this->planetSize * 1.4);
        imagefilledrectangle($layer, $w, 0, 900, 900, imagecolorallocate($layer, 0, 0, 0));
        imagefilledrectangle($layer, 0, $h, 900, 900, imagecolorallocate($layer, 0, 0, 0));

        // TODO: try a "inverted" shadow too (https://i1.wp.com/www.designshard.com/wp-content/uploads/2009/04/planet-tutorial.jpg?resize=578%2C300)
//        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0x00, 0xFF, 0);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0xFF, 0x00, 0);

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);
        $layerOrchestrator->addLayer($shadow->image);

        return $layer;
    }

    private function generateMask()
    {
        $mask = $this->initializeImage();
        $black = imagecolorallocatealpha($mask, 0, 30, 0, 0);

        imagefilledellipse($mask, $this->width / 2, $this->height / 2, $this->planetSize, $this->planetSize, $black);

        return $mask;
    }

    private function generateSurface(array $palette)
    {
        $surface = $this->initializeImage();
        $seed = rand();

        $height = $this->planetSize;
        $width = $this->planetSize;

        $gen = new PerlinNoiseGenerator();
        $size = $this->planetSize;
        // 0.99 => full mini islands, 0.5 => large continents
        $gen->setPersistence(0.68); // 0.68 is nice
        $gen->setSize($size);
        $gen->setMapSeed($seed);
        $map = $gen->generate();

        $max = 0;
        $min = PHP_INT_MAX;
        for ($iy = 0; $iy < $height; $iy++) {
            for ($ix = 0; $ix < $width; $ix++) {
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

        list($r, $g, $b) = ColorHelper::hexToRgb($palette['ice']); // ICE
        $ice = imagecolorallocate($surface, $r, $g, $b);
        list($r, $g, $b) = ColorHelper::hexToRgb($palette['water']); // WATER
        $water = imagecolorallocate($surface, $r, $g, $b);
        list($r, $g, $b) = ColorHelper::hexToRgb($palette['land']); // LAND
        $land = imagecolorallocate($surface, $r, $g, $b);
        list($r, $g, $b) = ColorHelper::hexToRgb('#fbffcc'); // SAND
        $sand = imagecolorallocate($surface, $r, $g, $b);

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $h = 255 * ($map[$y][$x] - $min) / $diff;
                $h = intval($h);

                $color = $water;

                if ($h >= 50 && $h < 105) {
                    $color = $land;
                }

                if ($h >= 110 && $h < 150) {
                    $color = $land;
                }

                if ($h >= 150 && $h < 180) {
                    $color = $water;
                }

                if ($h >= 200) {
                    $color = $ice;
                }

                // Color the pixel
                imagesetpixel($surface, $x, $y, $color);

                // add texture
                $color = imagecolorallocatealpha($surface, $h, $h, $h, rand(10, 110));
                imagesetpixel($surface, $x, $y, $color);
            }
        }

        return $surface;
    }

    private function applyMask(&$picture, $mask)
    {
        // Get sizes and set up new picture
        $xSize = imagesx($picture);
        $ySize = imagesy($picture);
        $newPicture = imagecreatetruecolor($xSize, $ySize);
        imagesavealpha($newPicture, true);
        imagefill($newPicture, 0, 0, imagecolorallocatealpha($newPicture, 0, 0, 0, 127));

        // Resize mask if necessary
        if ($xSize != imagesx($mask) || $ySize != imagesy($mask)) {
            $tempPic = imagecreatetruecolor($xSize, $ySize);
            imagecopyresampled($tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx($mask), imagesy($mask));
            imagedestroy($mask);
            $mask = $tempPic;
        }

        // Perform pixel-based alpha map application
        for ($x = 0; $x < $xSize; $x++) {
            for ($y = 0; $y < $ySize; $y++) {
                $alpha = imagecolorsforindex($mask, imagecolorat($mask, $x, $y));
                //small mod to extract alpha, if using a black(transparent) and white
                //mask file instead change the following line back to Jules's original:
                //$alpha = 127 - floor($alpha['red'] / 2);
                //or a white(transparent) and black mask file:
                //$alpha = floor($alpha['red'] / 2);
                $alpha = $alpha['alpha'];
                $color = imagecolorsforindex($picture, imagecolorat($picture, $x, $y));
                //preserve alpha by comparing the two values
                if ($color['alpha'] > $alpha) {
                    $alpha = $color['alpha'];
                }

                //kill data for fully transparent pixels
                if ($alpha == 127) {
                    $color['red'] = 0;
                    $color['blue'] = 0;
                    $color['green'] = 0;
                }

                imagesetpixel(
                    $newPicture,
                    $x,
                    $y,
                    imagecolorallocatealpha($newPicture, $color['red'], $color['green'], $color['blue'], $alpha)
                );
            }
        }

        // Copy back to original picture
        imagedestroy($picture);
        $picture = $newPicture;
    }

    private function selectPalette(): array
    {
        // TODO: find real names
        $palettes = [
            'earth' => ['water' => '#426dfc', 'land' => '#3B5D38', 'ice' => '#FFFFFF'],
            'desert' => ['water' => '#f7d3ba', 'land' => '#3B5D38', 'ice' => '#FFFFFF'],
            'savannah' => ['water' => '#e6b31e', 'land' => '#343434', 'ice' => '#fcfaf1'],
            'acid' => ['water' => '#12e2a3', 'land' => '#389168', 'ice' => '#ddf516'],
            'violet' => ['water' => '#e14594', 'land' => '#2b3595', 'ice' => '#182952'],
            'red' => ['water' => '#eaa81b', 'land' => '#7d0000', 'ice' => '#012c0b'],
        ];

        $keys = array_keys($palettes);
        shuffle($keys);

        $paletteName = current($keys);

        return $palettes[$paletteName];
    }

    private function initializeImage($width = null, $height = null)
    {
        $width = $width ? $width : $this->width;
        $height = $height ? $height : $this->height;

        $canvas = imagecreatetruecolor($width, $height);
        imagesavealpha($canvas, true);
        $transparentBackground = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparentBackground);

        return $canvas;
    }
}
