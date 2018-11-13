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

    /** @var int */
    private $seed;

    public function __construct()
    {
        $this->layerOrchestrator = new LayerOrchestrator();
        $this->seed = rand();
    }

    public function diameter(int $diameterInPixels): self
    {
        $this->diameter = $diameterInPixels;

        return $this;
    }

    public function render(): \SplFileObject
    {
        mt_srand($this->seed);

        if (null === $this->diameter) {
            throw new \LogicException('The moon can not be rendered without a diameter.');
        }

        $moon = new Planet('Moon', $this->diameter);
        $this->layerOrchestrator->initTransparentBaseLayer($this->diameter, $this->diameter);
        $this->layerOrchestrator->addLayer($moon->render(), -$this->diameter / 2, -$this->diameter / 2);

        $image = $this->layerOrchestrator->render();
        $imagePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('odin-moon-') . '.png';

        header('Content-Type: image/png');
        imagepng($image, $imagePath);
        imagedestroy($image);

        return new \SplFileObject($imagePath);
    }
}
