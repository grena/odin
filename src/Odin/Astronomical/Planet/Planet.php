<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet;

use MapGenerator\PerlinNoiseGenerator;
use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\GradientAlpha;
use Odin\Drawer\Gd\Text;
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

        $percentShiftShadowX = -30;
        $percentShiftShadowY = rand(-15, 15) * 2;

        $this->image = $this->initializeImage();
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($this->image);
        $layerOrchestrator->addLayer($this->generateGlow($palette), $percentShiftShadowX, $percentShiftShadowY);

        $planetLayer = $this->initializeImage();
        $planetLayers = new LayerOrchestrator();
        $planetLayers->setBaseLayer($planetLayer);
        $planetLayers->addLayer($this->generatePlanet());
        $planetLayers->addLayer($this->generateSurface($palette));
        $planetLayers->addLayer($this->generateLittleShadow());

//        $planetLayers->addLayer($this->generateLight(), $shadowX, $shadowY);

        // Randomly move the shadow
        $shadowX = ($percentShiftShadowX * $this->planetSize) / 100;
        $shadowY = ($percentShiftShadowY * $this->planetSize) / 100;
        $planetLayers->addLayer($this->generateShadow(), $shadowX, $shadowY);

        // Cut the extra shadow
        $this->applyMask($planetLayer, $this->generateMask());

        $layerOrchestrator->addLayer($planetLayer);

        $image = $layerOrchestrator->render();
        Text::write($image, 'Palette: '.$palette['NAME'], 10, 35);

        return $image;
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
        $glowness = 0xAA; // the more, the more it glows

        $layer = $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        $w = (int) ($this->planetSize + 60);
        $h = (int) ($this->planetSize + 60);
        $glow = new GradientAlpha($w, $h, 'ellipse', $palette['water'], 0x00, $glowness, 0);

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
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#FFF', 0x44, 0x00, 0);

        $layerOrchestrator->addLayer($shadow->image, $this->width / 2 - $w/2, $this->height / 2 - $h/2);

        return $layer;
    }

    /**
     * The shadow consists of a radial gradient from transparent to black.
     * The all the reste outside it is fully painted in black.
     *
     * @return resource
     */
    private function generateShadow()
    {
        $layerWidth = $this->width;
        $layerHeight = $this->height;

        $layer = $this->initializeImage();
        $w = (int) ($this->planetSize * 1.4);
        $h = (int) ($this->planetSize * 1.4);

        // TODO: try a "inverted" shadow too (https://i1.wp.com/www.designshard.com/wp-content/uploads/2009/04/planet-tutorial.jpg?resize=578%2C300)
//        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0x00, 0xFF, 0);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0xFF, 0x00, 0);

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        // Set the center of the radial gradient on the center of the planet
        $x = (($layerWidth - $this->planetSize) / 2) - (($w - $this->planetSize) / 2);
        $y = ($layerHeight - $this->planetSize) / 2 - (($h - $this->planetSize) / 2);

        $xStart = $x;
        $xEnd = $x + $w;
        $yStart = $y;
        $yEnd = $y + $h;

        // Fill up the rest of full black
        imagefilledrectangle($layer, 0, 0, $layerWidth, $yStart, imagecolorallocate($layer, 0, 0, 0));
        imagefilledrectangle($layer, 0, 0, $xStart, $layerHeight, imagecolorallocate($layer, 0, 0, 0));
        imagefilledrectangle($layer, $layerWidth, 0, $xEnd, $layerHeight, imagecolorallocate($layer, 0, 0, 0));
        imagefilledrectangle($layer, 0, $layerHeight, $layerWidth, $yEnd, imagecolorallocate($layer, 0, 0, 0));

        $layerOrchestrator->addLayer($shadow->image, $x, $y);

        return $layer;
    }

    /**
     * The shadow consists of a radial gradient from transparent to black.
     * The all the reste outside it is fully painted in black.
     *
     * @return resource
     */
    private function generateLight()
    {
        $layerWidth = $this->width;
        $layerHeight = $this->height;

        $layer = $this->initializeImage();
        $w = (int) ($this->planetSize * 1.4);
        $h = (int) ($this->planetSize * 1.4);

        // TODO: try a "inverted" shadow too (https://i1.wp.com/www.designshard.com/wp-content/uploads/2009/04/planet-tutorial.jpg?resize=578%2C300)
//        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0x00, 0xFF, 0);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#FFF', 0xFF, 0x00, 0);

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        // Set the center of the radial gradient on the center of the planet
        $x = (($layerWidth - $this->planetSize) / 2) - (($w - $this->planetSize) / 2);
        $y = ($layerHeight - $this->planetSize) / 2 - (($h - $this->planetSize) / 2);

        $xStart = $x;
        $xEnd = $x + $w;
        $yStart = $y;
        $yEnd = $y + $h;

        // Fill up the rest of full black
        imagefilledrectangle($layer, 0, 0, $layerWidth, $yStart, imagecolorallocate($layer, 255, 255, 255));
        imagefilledrectangle($layer, 0, 0, $xStart, $layerHeight, imagecolorallocate($layer, 255, 255, 255));
        imagefilledrectangle($layer, $layerWidth, 0, $xEnd, $layerHeight, imagecolorallocate($layer, 255, 255, 255));
        imagefilledrectangle($layer, 0, $layerHeight, $layerWidth, $yEnd, imagecolorallocate($layer, 255, 255, 255));

        $layerOrchestrator->addLayer($shadow->image, $x, $y);

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
        $layer = $this->initializeImage();
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($layer);

        $surface = $this->initializeImage($this->planetSize, $this->planetSize);
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
        list($r, $g, $b) = ColorHelper::hexToRgb($palette['shore']); // SHORE
        $shore = imagecolorallocate($surface, $r, $g, $b);
        list($r, $g, $b) = ColorHelper::hexToRgb($palette['land']); // LAND
        $land = imagecolorallocate($surface, $r, $g, $b);

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $h = 255 * ($map[$y][$x] - $min) / $diff;
                $h = intval($h);

                $color = $water;
                imagesetpixel($surface, $x, $y, $color);

                if ($h >= 50 && $h < 105) {
                    $color = $land;
                    imagesetpixel($surface, $x, $y, $color);
                }

                if ($h >= 110 && $h < 150) {
                    $color = $land;
                    imagesetpixel($surface, $x, $y, $color);
                }

                if ($h >= 150 && $h < 180) {
                    imagesetpixel($surface, $x -1, $y -1, $water);
                    imagesetpixel($surface, $x, $y, $shore);
                }

                if ($h >= 200) {
                    $color = $ice;
                    imagesetpixel($surface, $x, $y, $color);
                }

                // add texture
                $color = imagecolorallocatealpha($surface, $h, $h, $h, rand(50, 110));
                imagesetpixel($surface, $x, $y, $color);
            }
        }

        $x = ($this->width / 2) - ($width / 2);
        $y = ($this->height / 2) - ($height / 2);

        $layerOrchestrator->addLayer($surface, $x, $y);

        return $layerOrchestrator->render();
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
            'terran' => [
                'NAME' => 'Terran',
                'water' => '#426dfc',
                'shore' => '#519a47',
                'land' => '#3B5D38',
                'ice' => '#FFFFFF'
            ],
            'ashes' => [
                'NAME' => 'Ashes',
                'water' => '#000000',
                'shore' => '#9c7a14',
                'land' => '#343434',
                'ice' => '#c8c6bf'
            ],
            'toxic' => [
                'NAME' => 'Toxic',
                'water' => '#12e2a3',
                'shore' => '#ffffff',
                'land' => '#389168',
                'ice' => '#ddf516'
            ],
            'violet' => [
                'NAME' => 'Violet',
                'water' => '#e14594',
                'shore' => '#939cf6',
                'land' => '#2b3595',
                'ice' => '#182952'
            ],
            'lava' => [
                'NAME' => 'Lava',
                'water' => '#ff4f16',
                'shore' => '#ffa616',
                'land' => '#ff7716',
                'ice' => '#f8ec00'
            ],
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
