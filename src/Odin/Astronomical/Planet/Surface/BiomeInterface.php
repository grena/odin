<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;

/**
 * A Biome is the environment of a planet.
 * Its ecosystem, colors, ground, look & feel.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
interface BiomeInterface
{
    /**
     * Generate the representation of this biome and returns it as a resource image.
     */
    public function generate(int $size);

    /**
     * Return the name of this Biome.
     */
    public function getName(): string;

    /**
     * Return the color palette for this Biome.
     * A palette *MUST* contain the "water" key, as it is used for glowy color of the planet.
     */
    public function getColorPalette(): array;
}
