<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * Nice atmosphere, water oceans.
 * Large green continents and some icy ones, too.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class ForestBiome extends AbstractBiome
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
    public function getName(): string
    {
        return 'Forest';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#3287c6',
            'low_water' => '#32bcc6',
            'shore' => '#34632d',
            'land' => '#448a3e',
            'forest' => '#2c672a',
            'deep_forest' => '#2c4b2a',
            'mountain' => '#705714',
            'low_mountain' => '#707b14',
            'ice' => '#FFFFFF',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        imagesetpixel($layer, $x, $y, $colors['water']);

        if ($h >= 60) {
            imagesetpixel($layer, $x, $y, $colors['low_water']);
        }

        if ($h >= 75) {
            imagesetpixel($layer, $x, $y, $colors['land']);
        }

        if ($h >= 120) {
            imagesetpixel($layer, $x, $y, $colors['forest']);

            if (rand(0, 25) === 1) {
                $size = rand(1, 2);
                $current = 0;
                while($current < $size) {
                    imagesetpixel($layer, $x - $current, $y, $colors['deep_forest']);
                    imagesetpixel($layer, $x - $current, $y - $current, $colors['deep_forest']);
                    imagesetpixel($layer, $x, $y, $colors['deep_forest']);

                    $current ++;
                }
            }
        }

        if ($h >= 145 && $h <= 150) {
            imagesetpixel($layer, $x, $y, $colors['low_water']);
        }

        if ($h >= 208) {
            imagesetpixel($layer, $x, $y, $colors['low_mountain']);
        }

        if ($h >= 210) {
            imagesetpixel($layer, $x, $y, $colors['mountain']);
        }

        if ($h >= 230) {
            imagesetpixel($layer, $x, $y, $colors['ice']);
        }

        $textureColor = imagecolorallocatealpha($layer, 50, 50, 50, rand(80, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
