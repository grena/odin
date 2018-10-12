<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * TODO
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class VioletBiome extends AbstractBiome
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
        return 'Violet';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#e14594',
            'shore' => '#9378f6',
            'land' => '#2b3595',
            'ice' => '#182952'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        imagesetpixel($layer, $x, $y, $colors['water']);

        if ($h >= 50 && $h < 105) {
            imagesetpixel($layer, $x, $y, $colors['land']);
        }

        if ($h >= 110 && $h < 150) {
            imagesetpixel($layer, $x, $y, $colors['land']);
        }

        if ($h >= 150 && $h < 180) {
            imagesetpixel($layer, $x - 1, $y - 1, $colors['water']);
            imagesetpixel($layer, $x, $y, $colors['shore']);
        }

        if ($h >= 200) {
            imagesetpixel($layer, $x, $y, $colors['ice']);
        }

        $textureColor = imagecolorallocatealpha($layer, $h, $h, $h, rand(50, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
