<?php

declare(strict_types=1);

namespace Odin\Drawer\Gd;

class LayerOrchestrator
{
    private $image;
    private $width;
    private $height;

    public function initBaseLayer(int $width, int $height, string $hexColor = '#000', int $alpha = 0): void
    {
        $this->width = $width;
        $this->height = $height;

        $this->setBackgroundColor($hexColor, $alpha);
    }

    public function setBaseLayer($image): void
    {
        imagesavealpha($image, true);
        $this->image = $image;

        $this->width = imagesx($image);
        $this->height = imagesy($image);
    }

    public function setBackgroundColor(string $hexColor = '#000', int $alpha = 0): void
    {
        list($r, $g, $b) = ColorHelper::hexToRgb($hexColor);
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->image, true);
        $backgroundColor = imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
        imagefill($this->image, 0, 0, $backgroundColor);
    }

    public function addLayer($layer, int $dst_x = 0, int $dst_y = 0): void
    {
        $xSize = imagesx($layer);
        $ySize = imagesy($layer);

        imagecopy($this->image, $layer, $dst_x, $dst_y, 0, 0, $xSize, $ySize);
    }

    public function render()
    {
        return $this->image;
    }
}
