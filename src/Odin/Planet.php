<?php

namespace Odin;

use Odin\Astronomical\Planet\Planet as InternalPLanet;
use Odin\Drawer\Gd\LayerOrchestrator;

class Planet
{
    /** @var string */
    private $biome;

    /** @var int */
    private $diameter;

    /** @var LayerOrchestrator */
    private $layerOrchestrator;

    public function __construct()
    {
        $this->layerOrchestrator = new LayerOrchestrator();
    }

    public function diameter(int $diameterInPixels): self
    {
        $this->diameter = $diameterInPixels;

        return $this;
    }

    public function lava(): self
    {
        $this->biome = 'Lava';

        return $this;
    }

    public function toxic()
    {
        $this->biome = 'Toxic';

        return $this;
    }

    public function coldGaz()
    {
        $this->biome = 'Cold Gaz';

        return $this;
    }

    public function hotGaz()
    {
        $this->biome = 'Hot Gaz';

        return $this;
    }

    public function hydroGaz()
    {
        $this->biome = 'Hydro Gaz';

        return $this;
    }

    public function atoll()
    {
        $this->biome = 'Atoll';

        return $this;
    }

    public function violet()
    {
        $this->biome = 'Violet';

        return $this;
    }

    public function ashes()
    {
        $this->biome = 'Ashes';

        return $this;
    }

    public function forest()
    {
        $this->biome = 'Forest';

        return $this;
    }

    public function render(): \SplFileObject
    {
        if (null === $this->diameter) {
            throw new \LogicException('The planet can not be rendered without a diameter.');
        }

        if (null === $this->biome) {
            throw new \LogicException('The planet can not be rendered without a biome.');
        }

        $planet = new InternalPLanet($this->biome, $this->diameter);
        $this->layerOrchestrator->initTransparentBaseLayer($this->diameter, $this->diameter);
        $this->layerOrchestrator->addLayer($planet->render(), -$this->diameter / 2, -$this->diameter / 2);

        $image = $this->layerOrchestrator->render();
        $imagePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('odin-planet-') . '.png';

        header('Content-Type: image/png');
        imagepng($image, $imagePath);
        imagedestroy($image);

        return new \SplFileObject($imagePath);
    }
}
