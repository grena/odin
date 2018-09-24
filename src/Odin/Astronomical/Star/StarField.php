<?php

declare(strict_types=1);

namespace Odin\Astronomical;

use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\GradientAlpha;
use Odin\Orchestrator\LayerOrchestrator;

class StarField
{
    private $image;

    private $width;
    private $height;

    private $minBrightness = 0;
    private $maxBrightness = 100;

//    private $minShine = 0;
//    private $maxShine = 255;

    private $minShineHalo = 0;
    private $maxShineHalo = 8;

    private $percentSuperStars = 5;

    private $densityPercent = 20;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function setBrightness(int $minBrightness, int $maxBrightness)
    {
        $this->minBrightness = $minBrightness;
        $this->maxBrightness = $maxBrightness;
    }

    public function render($exportToFile = false)
    {
        $this->initializeImage();

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->setBaseLayer($this->image);

        for ($x = 0; $x < $this->width; ++$x) {
            for ($y = 0; $y < $this->height; ++$y) {
                // 100 percent density is too much, we use a percent of 1000
                $drawStar = rand(0, 2000) < $this->densityPercent;
                if(!$drawStar) continue;

                $isSuperStar = rand(0, 100) < $this->percentSuperStars;
                $brightness = rand($this->minBrightness, $this->maxBrightness);
                $white = intval(floor(255 * $brightness / 100));
                $starColor = imagecolorallocate($this->image, $white, $white, $white);

                if ($isSuperStar) {
                    // TODO: draw halo before
                    $haloSize = rand(5, 20) * 2;

                    $hexColor = ColorHelper::rgbToHex($starColor);
                    $halo = new GradientAlpha($haloSize, $haloSize, 'ellipse', $hexColor, 0x00, 0x33, 0);

                    $layerOrchestrator->addLayer($halo->image, $x-$haloSize/2, $y-$haloSize/2);

                    imagefilledellipse($this->image, $x, $y, 2, 2, $starColor);
                } else {
                    imagesetpixel($this->image, $x, $y, $starColor);
                }
            }
        }

        $this->image = $layerOrchestrator->render();

        return $this->image;
    }

    private function initializeImage(): void
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->image, true);
        $transparentBackground = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
        imagefill($this->image, 0, 0, $transparentBackground);
    }
}
