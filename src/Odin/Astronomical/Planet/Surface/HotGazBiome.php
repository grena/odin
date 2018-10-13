<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * TODO
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class HotGazBiome extends AbstractGazBiome
{
    protected $roughness = 1;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Hot Gaz';
    }

    /**
     * {@inheritdoc}
     */
    public function getColorPalette(): array
    {
        return [
            'water' => '#972d07',
            'gaz1' => '#ff4b3e',
            'gaz2' => '#e09f3e',
            'gaz3' => '#ffe548',
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
            imagesetpixel($layer, $x, $y, $colors['gaz1']);
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
