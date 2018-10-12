<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet;

use Odin\Astronomical\Planet\Surface\BiomeInterface;
use Odin\Astronomical\Planet\Surface\BiomeSurfaceGeneratorRegistry;
use Odin\Drawer\Gd\GradientAlpha;
use Odin\Drawer\Gd\LayerOrchestrator;
use Odin\Drawer\Gd\Text;

class Planet
{
    private $image;

    /** @var int */
    private $layerWidth;

    /** @var int */
    private $layerHeight;

    /** @var string */
    private $biome;

    /** @var int */
    private $planetSize;

    public function __construct(string $biome, ?int $planetSize = null)
    {
        $this->biome = $biome;

        if (null !== $planetSize) {
            $this->planetSize = $planetSize;
        } else {
            $this->planetSize = $this->makeEven(rand(150, 250));
        }

        $this->layerWidth = $this->planetSize * 2;
        $this->layerHeight = $this->planetSize * 2;
    }

    public function render()
    {
        $generatorRegistry = new BiomeSurfaceGeneratorRegistry();
        /** @var BiomeInterface $surfaceGenerator */
        $surfaceGenerator = $generatorRegistry->forBiome($this->biome);

        $percentShiftShadowX = -30;
        $percentShiftShadowY = $this->makeEven(rand(-30, 30));

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerWidth, $this->layerHeight, '#000', 127);
        $layerOrchestrator->addLayer($this->generateGlow($surfaceGenerator->getColorPalette()), $percentShiftShadowX / 2, $percentShiftShadowY / 2);

        $planetLayers = new LayerOrchestrator();
        $planetLayers->initBaseLayer($this->layerWidth, $this->layerHeight, '#000', 127);
        $planetLayer = $planetLayers->render();

        // Generate surface
        $surface = $surfaceGenerator->generate($this->planetSize);
        $x = ($this->layerWidth / 2) - ($this->planetSize / 2);
        $y = ($this->layerHeight / 2) - ($this->planetSize / 2);
        $planetLayers->addLayer($surface, $x, $y);

        // Generate small brightness on planet
        $planetLayers->addLayer($this->generateBrightness());

        // Randomly move the shadow
        $shadowX = $this->makeEven(($percentShiftShadowX * $this->planetSize) / 100);
        $shadowY = $this->makeEven(($percentShiftShadowY * $this->planetSize) / 100);
        $planetLayers->addLayer($this->generateShadow(), $shadowX, $shadowY);

        // Cut the extra shadow
        $this->applyMask($planetLayer, $this->generateMask());

        $layerOrchestrator->addLayer($planetLayer);

        $image = $layerOrchestrator->render();
        Text::write($image, 'Palette: '.$surfaceGenerator->getName(), 10, 35);

        $this->image = $layerOrchestrator->render();

        return $image;
    }

    private function generateGlow(array $palette)
    {
        $glowness = 0xAA; // the more, the more it glows

        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerHeight, $this->layerWidth, '#000', 127);
        $layer = $layerOrchestrator->render();

        $w = $this->makeEven(round($this->planetSize * 1.2));
        $h = $this->makeEven(round($this->planetSize * 1.2));
        $glow = new GradientAlpha($w, $h, 'ellipse', $palette['water'], 0x00, $glowness, 0);

        $x = ($this->layerWidth / 2) - ($w / 2);
        $y = ($this->layerHeight / 2) - ($h / 2);

        $layerOrchestrator->addLayer($glow->image, $x, $y);

        return $layer;
    }

    private function generateBrightness()
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerHeight, $this->layerWidth, '#000', 127);
        $layer = $layerOrchestrator->render();

        $w = (int) ($this->planetSize + 10);
        $h = (int) ($this->planetSize + 10);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#FFF', 0x44, 0x00, 0);

        $layerOrchestrator->addLayer($shadow->image, $this->layerWidth / 2 - $w/2, $this->layerHeight / 2 - $h/2);

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
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerHeight, $this->layerWidth, '#000', 127);
        $layer = $layerOrchestrator->render();

        $layerWidth = $this->layerWidth;
        $layerHeight = $this->layerHeight;

        $w = $this->makeEven($this->planetSize * 1.4);
        $h = $this->makeEven($this->planetSize * 1.4);

        // TODO: try a "inverted" shadow too (https://i1.wp.com/www.designshard.com/wp-content/uploads/2009/04/planet-tutorial.jpg?resize=578%2C300)
//        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0x00, 0xFF, 0);
        $shadow = new GradientAlpha($w, $h, 'ellipse', '#000', 0xFF, 0x00, 0);

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

    private function generateMask()
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($this->layerHeight, $this->layerWidth, '#000', 127);
        $mask = $layerOrchestrator->render();

        $black = imagecolorallocatealpha($mask, 0, 30, 0, 0);

        imagefilledellipse($mask, $this->layerWidth / 2, $this->layerHeight / 2, $this->planetSize, $this->planetSize, $black);

        return $mask;
    }

    // TODO: move in a Drawer\Gd class
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

    // TODO: move to dedicated Math class
    private function makeEven($number): int
    {
        $number = intval($number);

        if ($number % 2 === 0) {
            return $number;
        }

        return $number + 1;
    }
}
