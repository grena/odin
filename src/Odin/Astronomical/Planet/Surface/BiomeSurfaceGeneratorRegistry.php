<?php

declare(strict_types=1);

namespace Odin\Astronomical\Planet\Surface;


class BiomeSurfaceGeneratorRegistry
{
    private $generatorClasses = [
        '\Odin\Astronomical\Planet\Surface\ToxicBiome',
        '\Odin\Astronomical\Planet\Surface\ForestBiome',
        '\Odin\Astronomical\Planet\Surface\AshesBiome',
        '\Odin\Astronomical\Planet\Surface\VioletBiome',
        '\Odin\Astronomical\Planet\Surface\LavaBiome',
        '\Odin\Astronomical\Planet\Surface\AtollBiome',
        '\Odin\Astronomical\Planet\Surface\MoonBiome',
        '\Odin\Astronomical\Planet\Surface\ColdGazBiome',
        '\Odin\Astronomical\Planet\Surface\HotGazBiome',
        '\Odin\Astronomical\Planet\Surface\HydroGazBiome',
    ];

    public function forBiome(string $biomeName)
    {
        foreach ($this->generatorClasses as $generatorClass) {
            $generator = new $generatorClass;

            if ($biomeName === $generator->getName()) {
                return $generator;
            }
        }

        throw new \InvalidArgumentException(
            sprintf('No generator found for biome "%s"', $biomeName)
        );
    }
}
