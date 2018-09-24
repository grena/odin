<?php

declare(strict_types=1);

namespace Odin\Orchestrator;

class LayerOrchestrator
{
    private $image;
    private $width;
    private $height;

    public function initBaseLayer(int $width, int $height, string $hexColor = '#000', int $alpha = 0)
    {
        $this->width = $width;
        $this->height = $height;

        $this->setBackgroundColor($hexColor, $alpha);
    }

    public function setBaseLayer($image)
    {
        imagesavealpha($image, true);
        $this->image = $image;

        $this->width = imagesx($image);
        $this->height = imagesy($image);
    }

    public function setBackgroundColor($hexColor = '#000', $alpha = 0)
    {
        list($r, $g, $b) = $this->hex2rgb($hexColor);
        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagesavealpha($this->image, true);
        $backgroundColor = imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
        imagefill($this->image, 0, 0, $backgroundColor);
    }

    public function addLayer($layer, int $dst_x = 0, int $dst_y = 0)
    {
        $xSize = imagesx($layer);
        $ySize = imagesy($layer);

        imagecopy($this->image, $layer, $dst_x, $dst_y, 0, 0, $xSize, $ySize);
    }

    public function render()
    {
        return $this->image;
    }

    // #ff00ff -> array(255,0,255) or #f0f -> array(255,0,255)
    private function hex2rgb($color)
    {
        $color = str_replace('#', '', $color);
        $s = strlen($color) / 3;
        $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
        $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));

        return $rgb;
    }

}
