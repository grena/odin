<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * Toxic atmosphere, acid oceans.
 * Corrosive, poisonous.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class ToxicBiome extends AbstractBiome
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Toxic';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#12e2a3',
            'shore' => '#ffffff',
            'land' => '#389168',
            'ice' => '#ddf516',
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
    }
}
