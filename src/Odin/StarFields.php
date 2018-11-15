<?php

namespace Odin;

use Odin\Astronomical\Star\Nebulae;
use Odin\Astronomical\Star\StarField;
use Odin\Drawer\Gd\LayerOrchestrator;

class StarFields
{
    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var array */
    private $starFields = [];

    /** @var array */
    private $nebulaes = [];

    /** @var Configuration */
    private $configuration;

    /** @var LayerOrchestrator */
    private $layerOrchestrator;

    public function __construct(?Configuration $configuration = null)
    {
        $this->configuration = $configuration ?? new Configuration();
        $this->layerOrchestrator = new LayerOrchestrator();
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function addStarField(int $brightness): self
    {
        $starField = new StarField($this->width, $this->height);
        $starField->setBrightness(0, $brightness);

        $this->starFields[] = $starField;

        return $this;
    }

    public function addNebulae(int $width, int $height, string $color): self
    {
        $nebulae = new Nebulae($this->width, $this->height);
        $nebulae->setColor($color);

        $this->nebulaes[] = $nebulae;

        return $this;
    }

    public function render()
    {
        mt_srand($this->configuration->seed());

        if (null === $this->height) {
            throw new \LogicException('The star fields can not be rendered without a height.');
        }

        if (null === $this->width) {
            throw new \LogicException('The star fields can not be rendered without a width.');
        }

        if (empty($this->starFields)) {
            throw new \LogicException('The star fields can not be rendered without any actual field.');
        }

        $this->layerOrchestrator->initBaseLayer($this->width, $this->height, '#000', 0);

        foreach ($this->starFields as $starField) {
            $this->layerOrchestrator->addLayer($starField->render());
        }

        foreach ($this->nebulaes as $nebulae) {
            $this->layerOrchestrator->addLayer($nebulae->render());
        }

        $image = $this->layerOrchestrator->render();
        $imagePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'stars' . uniqid();

        imagepng($image, $imagePath);
        imagedestroy($image);

        return new \SplFileObject($imagePath);
    }
}
