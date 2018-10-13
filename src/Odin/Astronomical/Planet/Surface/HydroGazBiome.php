<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * TODO
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class HydroGazBiome extends AbstractGazBiome
{
    protected $roughness = 3;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Hydro Gaz';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#216176',
            'gaz1' => '#2aa996',
            'gaz2' => '#50bead',
            'gaz3' => '#8edace',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function fillSurface(int $x, int $y, int $h, $layer, array $colors): void
    {
        imagesetpixel($layer, $x, $y, $colors['water']);

        if ($h >= 50) {
            imagesetpixel($layer, $x, $y, $colors['gaz1']);
        }

        if ($h >= 100) {
            imagesetpixel($layer, $x, $y, $colors['gaz2']);
        }

        if ($h >= 150) {
            imagesetpixel($layer, $x, $y, $colors['gaz3']);
        }

        if ($h >= 180) {
            imagesetpixel($layer, $x, $y, $colors['gaz2']);
        }

        if ($h >= 220) {
            imagesetpixel($layer, $x, $y, $colors['water']);
        }

        $textureH = 120;
        $textureColor = imagecolorallocatealpha($layer, $textureH, $textureH, $textureH, rand(50, 110));
        imagesetpixel($layer, $x, $y, $textureColor);

//        $h = $h > 255 ? 255 : $h;
//        $h = $h < 0 ? 0 : $h;
//        $color = imagecolorallocatealpha($layer, $h, $h, $h, 0);
//        imagesetpixel($layer, $x, $y, $color);
    }
}
