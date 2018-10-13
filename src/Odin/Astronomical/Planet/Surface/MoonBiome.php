<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * No atmosphere.
 * Huge desert of dead and dark lands. No vegetation.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class MoonBiome extends AbstractBiome
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
        return 'Moon';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#000000',
            'shore' => '#846711',
            'land' => '#343434',
            'ice' => '#c8c6bf'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        if ($h >= 15 && $h < 105) {
            imagesetpixel($layer, $x, $y, $colors['land']);
        }

        if ($h >= 110 && $h < 150) {
            imagesetpixel($layer, $x, $y, $colors['land']);
        }

        if ($h >= 200) {
            imagesetpixel($layer, $x, $y, $colors['ice']);
        }

        $textureColor = imagecolorallocatealpha($layer, $h, $h, $h, rand(50, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
