<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

use MapGenerator\PerlinNoiseGenerator;
use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\LayerOrchestrator;

/**
 * Abstract representation of a Gaz Biome.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
abstract class AbstractGazBiome implements BiomeInterface
{
    protected $roughness = 1; // 1 to ~15

    /**
     * {@inheritdoc}
     */
    public function generate(int $size)
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($size, $size, '#000', LayerOrchestrator::TRANSPARENT);
        $surface = $layerOrchestrator->render();

        $height = $size;
        $width = $size;

        $allocatedColors = $this->allocatePaletteColors($surface);

        // PAR LIGNE
        for ($y = 0; $y < $height; ++$y) {
            $h = intval(255 * $y / $height);
            for ($x = 0; $x < $width; ++$x) {
                $h = rand(0, 1) ? $h + $this->roughness : $h - $this->roughness;

                // This is where each biome will draw its own surface
                $this->fillSurface($x, $y, $h, $surface, $allocatedColors);
            }
        }

        // Center the biome surface on the layer
        $x = ($size / 2) - ($width / 2);
        $y = ($size / 2) - ($height / 2);

        $layerOrchestrator->addLayer($surface, $x, $y);

        return $layerOrchestrator->render();
    }

    /**
     * This method fills the surface of this Biome for a given $x and $y on the given $layer.
     * The $h is the "greyscale" level coming from the Perlin Noise. This is needed to define on which part
     * of the land the pixel belongs to.
     * The allocated $colors must be passed too.
     */
    abstract protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void;

    /**
     * For the given $layer, it will return an array of allocated colors based
     * on the color palette of this biome.
     *
     * @see https://secure.php.net/manual/fr/function.imagecolorallocate.php
     */
    private function allocatePaletteColors($layer): array
    {
        $allocatedColors = [];
        $palette = $this->getColorPalette();

        foreach ($palette as $colorName => $hexColor) {
            list($r, $g, $b) = ColorHelper::hexToRgb($hexColor);
            $allocatedColors[$colorName] = imagecolorallocate($layer, $r, $g, $b);
        }

        return $allocatedColors;
    }
}
