<?php

declare(strict_types=1);

namespace Odin;

use Odin\Astronomical\Planet\Planet;
use Odin\Drawer\Gd\LayerOrchestrator;

class Moon
{
    /** @var int */
    private $diameter;

    /** @var LayerOrchestrator */
    private $layerOrchestrator;

    /** @var CelestialConfiguration */
    private $configuration;

    public function __construct(?CelestialConfiguration $configuration = null)
    {
        $this->layerOrchestrator = new LayerOrchestrator();
        $this->configuration = $configuration ?? new CelestialConfiguration();
    }

    public function diameter(int $diameterInPixels): self
    {
        $this->diameter = $diameterInPixels;

        return $this;
    }

    public function render(): \SplFileObject
    {
        mt_srand($this->configuration->seed());

        if (null === $this->diameter) {
            throw new \LogicException('The moon can not be rendered without a diameter.');
        }

        $moon = new Planet('Moon', $this->diameter);
        $this->layerOrchestrator->initTransparentBaseLayer($this->diameter, $this->diameter);
        $this->layerOrchestrator->addLayer($moon->render(), -$this->diameter / 2, -$this->diameter / 2);

        $image = $this->layerOrchestrator->render();
        $imagePath =  $this->generateImagePath($this->configuration);

        header('Content-Type: image/png');
        imagepng($image, $imagePath);
        imagedestroy($image);

        return new \SplFileObject($imagePath);
    }

    private function generateImagePath(?CelestialConfiguration $configuration): string
    {
        $name = uniqid('odin-moon-') . '.png';
        $directory = sys_get_temp_dir();
        if (null !== $configuration) {
            $directory = $configuration->directory();
        }

        return $directory . DIRECTORY_SEPARATOR . $name;
    }
}
