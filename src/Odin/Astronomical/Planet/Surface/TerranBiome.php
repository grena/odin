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
            'water' => '#426dfc',
            'shore' => '#519a47',
            'land' => '#3B5D38',
            'ice' => '#FFFFFF'
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
