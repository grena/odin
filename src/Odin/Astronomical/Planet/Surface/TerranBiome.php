<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * Nice atmosphere, water oceans.
 * Large green continents and some icy ones, too.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class TerranBiome extends AbstractBiome
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
    protected $noisePersistence = 0.65;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Terran';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#0e008e',
            'low_water' => '#0e22c9',
            'shore' => '#34632d',
            'land' => '#3B5D38',
            'forest' => '#2c4f2a',
            'dirt' => '#605938',
            'ice' => '#FFFFFF',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        imagesetpixel($layer, $x, $y, $colors['forest']);

        if ($h >= 50 && $h < 106) {
            imagesetpixel($layer, $x, $y, $colors['land']);
            $textureColor = imagecolorallocatealpha($layer, 0, 0, 0, rand(50, 110));
            imagesetpixel($layer, $x, $y, $textureColor);
        }

        if ($h >= 110 && $h < 150) {
            imagesetpixel($layer, $x, $y, $colors['forest']);
        }

        if ($h >= 150 && $h < 185) {
            imagesetpixel($layer, $x - 1, $y - 1, $colors['water']);
            $textureColor = imagecolorallocatealpha($layer, 0, 0, 0, rand(50, 110));
            imagesetpixel($layer, $x - 1, $y - 1, $textureColor);
            imagesetpixel($layer, $x, $y, $colors['shore']);
        }

        if ($h >= 190) {
            imagesetpixel($layer, $x, $y, $colors['forest']);
        }

        if ($h >= 202) {
            imagesetpixel($layer, $x, $y, $colors['water']);
        }

        if ($h >= 204) {
            imagesetpixel($layer, $x, $y, $colors['forest']);
        }

        if ($h >= 205) {
            imagesetpixel($layer, $x, $y, $colors['water']);
        }

        if ($h >= 215) {
            imagesetpixel($layer, $x, $y, $colors['forest']);
            $textureColor = imagecolorallocatealpha($layer, 0, 0, 0, rand(50, 110));
            imagesetpixel($layer, $x, $y, $textureColor);
        }

        if ($h >= 240) {
            imagesetpixel($layer, $x, $y, $colors['ice']);
        }

        $textureColor = imagecolorallocatealpha($layer, $h, $h, $h, rand(50, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
