<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * TODO
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class LavaBiome extends AbstractBiome
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
    protected $noisePersistence = 0.75;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Lava';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#ff4f16',
            'shore' => '#ffb783',
            'land' => '#ff7716',
            'small_rock' => '#3f0203',
            'rock' => '#2e2e2e',
            'ashes' => '#901e26',
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
            imagesetpixel($layer, $x, $y, $colors['small_rock']);
        }

        if ($h >= 215) {
            imagesetpixel($layer, $x, $y, $colors['rock']);
        }

        if ($h >= 216) {
            imagesetpixel($layer, $x, $y, $colors['ashes']);
        }

        if ($h >= 218) {
            imagesetpixel($layer, $x, $y, $colors['rock']);
        }

        if ($h >= 220) {
            imagesetpixel($layer, $x, $y, $colors['ashes']);
        }

        if ($h >= 222) {
            imagesetpixel($layer, $x, $y, $colors['rock']);
        }

        $textureH = 255 - $h;
        $textureColor = imagecolorallocatealpha($layer, $textureH, $textureH, $textureH, rand(50, 110));
        imagesetpixel($layer, $x, $y, $textureColor);
    }
}
