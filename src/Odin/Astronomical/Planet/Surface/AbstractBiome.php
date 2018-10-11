<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

use MapGenerator\PerlinNoiseGenerator;
use Odin\Drawer\Gd\ColorHelper;
use Odin\Drawer\Gd\LayerOrchestrator;

/**
 * Abstract representation of a Biome.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
abstract class AbstractBiome implements BiomeInterface
{
    /**
     * @see http://libnoise.sourceforge.net/glossary/index.html#persistence
     * "A multiplier that determines how quickly the amplitudes diminish for each
     * successive octave in a Perlin-noise function."
     *
     * Example:
     * 0.99 => Lot of mini islands
     * 0.5 => Large continents
     *
     * 0.68 looks quite consistent
     *
     * @var float
     */
    protected $noisePersistence = 0.68;

    /**
     * {@inheritdoc}
     */
    public function generate(int $size)
    {
        $layerOrchestrator = new LayerOrchestrator();
        $layerOrchestrator->initBaseLayer($size, $size, '#000', LayerOrchestrator::TRANSPARENT);
        $surface = $layerOrchestrator->render();

        $seed = rand();

        $height = $size;
        $width = $size;

        $perlinNoiseGenerator = new PerlinNoiseGenerator();
        $perlinNoiseGenerator->setPersistence($this->noisePersistence);
        $perlinNoiseGenerator->setSize($size);
        $perlinNoiseGenerator->setMapSeed($seed);
        $map = $perlinNoiseGenerator->generate();

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

        $allocatedColors = $this->allocatePaletteColors($surface);

        for ($x = 0; $x < $width; ++$x) {
            for ($y = 0; $y < $height; ++$y) {
                $h = 255 * ($map[$y][$x] - $min) / $diff;
                $h = intval($h);

                // This is where each biome will draw its own surface
                $this->fillSurface($x, $y, $h, $surface, $allocatedColors);

                // Add texture (small grey dots with random opacity)
                $textureColor = imagecolorallocatealpha($surface, $h, $h, $h, rand(50, 110));
                imagesetpixel($surface, $x, $y, $textureColor);
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
