<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet;


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
        $this->image = $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($this->image);

        $layerOrchestrator->addLayer($this->generatePlanet(), 0, 0);
        $layerOrchestrator->addLayer($this->generateShadow(), -20, -20);

        // Cut the extra shadow
        $this->applyMask($this->image, $this->generateMask());

        return $this->image;
    }

    private function generatePlanet()
    {
        $planet = $this->initializeImage();
        $blue = imagecolorallocate($planet, 62, 86, 124);
        imagefilledellipse($planet, $this->width / 2, $this->height / 2, $this->planetSize, $this->planetSize, $blue);

        return $planet;
    }

    private function generateShadow()
    {
        $w = (int) ($this->planetSize * 1.6);
        $h = (int) ($this->planetSize * 1.6);

        // TODO: try a "inverted" shadow too (https://i1.wp.com/www.designshard.com/wp-content/uploads/2009/04/planet-tutorial.jpg?resize=578%2C300)
//        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0x00, 0xFF, 0);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0xFF, 0x00, 0);

        return $shadow->image;
    }

    private function generateMask()
    {
        $mask = $this->initializeImage();
        $black = imagecolorallocatealpha($mask, 0, 30, 0, 0);

        imagefilledellipse($mask, $this->width / 2, $this->height / 2, $this->planetSize, $this->planetSize, $black);

        return $mask;
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

    private function initializeImage()
    {
        $canvas = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($canvas, true);
        $transparentBackground = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparentBackground);

        return $canvas;
    }
}
