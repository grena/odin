<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * // TODO
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class AtollBiome extends AbstractBiome
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
    protected $noisePersistence = 0.99;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Atoll';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#5895be',
            'low_water' => '#58d6be',
            'forest' => '#2bc624',
            'mountain' => '#ab9d5a',
            'ice' => '#FFFFFF',
            'sand' => '#ffc300',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        imagesetpixel($layer, $x, $y, $colors['water']);

        if ($h >= 165) {
            imagesetpixel($layer, $x, $y, $colors['low_water']);
        }

        if ($h >= 175) {
            imagesetpixel($layer, $x, $y, $colors['sand']);
        }

        if ($h >= 210) {
            imagesetpixel($layer, $x, $y, $colors['forest']);
        }


        if ($h >= 220) {
            imagesetpixel($layer, $x, $y, $colors['mountain']);
        }

        if ($h >= 230) {
            imagesetpixel($layer, $x, $y, $colors['ice']);
        }


        $textureColor = imagecolorallocatealpha($layer, 50, 50, 50, rand(80, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
