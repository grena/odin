<?php

declare(strict_types=1);

namespace Odin\Drawer\Gd;

/**
 * Orchestrate image resources (seen as "layers").
 * This allows to stack layers to compose a brand new flattened layer.
 *
 * @author Adrien Pétremann <hello@grena.fr>
 */
class LayerOrchestrator
{
    /** @var int */
    public const TRANSPARENT = 127;

    private $image;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var array */
    private $layers = [];

    public function initBaseLayer(int $width, int $height, string $hexColor = '#000', int $alpha = 0): void
    {
        $this->width = $width;
        $this->height = $height;

        $this->setBackgroundColor($hexColor, $alpha);
    }

    public function initTransparentBaseLayer(int $width, int $height): void
    {
        $this->width = $width;
        $this->height = $height;

        $this->image = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($this->image, false);
        $transparency = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
        imagefill($this->image, 0, 0, $transparency);
        imagesavealpha($this->image, true);
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

    public function addLayer($layer, int $dstX = 0, int $dstY = 0): void
    {
        $this->layers[] = $layer;

        $xSize = imagesx($layer);
        $ySize = imagesy($layer);

        imagecopy($this->image, $layer, $dstX, $dstY, 0, 0, $xSize, $ySize);
    }

    public function render()
    {
        return $this->image;
    }
}
