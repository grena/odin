<?php
declare(strict_types=1);

error_reporting(E_ALL);

include_once ('../vendor/autoload.php');

use Odin\Astronomical\StarField;
use Odin\Orchestrator\LayerOrchestrator;

$WIDTH = 500;
$HEIGHT = 500;

$layerOrchestrator = new LayerOrchestrator();
$layerOrchestrator->initBaseLayer($WIDTH, $HEIGHT, '#000', 0);

$starfield = new StarField($WIDTH, $HEIGHT);
$starfield->setBrightness(0, 80);

$layerOrchestrator->addLayer($starfield->render());
$image = $layerOrchestrator->render();

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
