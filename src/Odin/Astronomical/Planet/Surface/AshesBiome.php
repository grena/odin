<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * No atmosphere.
 * Huge desert of dead and dark lands. No vegetation.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class AshesBiome extends AbstractBiome
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Ashes';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#000000',
            'shore' => '#9c7a14',
            'land' => '#343434',
            'ice' => '#c8c6bf'
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
