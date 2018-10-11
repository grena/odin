<?php
declare(strict_types=1);

error_reporting(E_ALL);

include_once (__DIR__ . '/../vendor/autoload.php');

use Odin\Astronomical\Nebulae;
use Odin\Astronomical\Planet\Planet;
use Odin\Astronomical\Planet\Surface\Biome;
use Odin\Astronomical\StarField;
use Odin\Drawer\Gd\LayerOrchestrator;
use Odin\Drawer\Gd\Text;

$WIDTH = 500;
$HEIGHT = 500;

$seed = rand();
mt_srand($seed);

$layerOrchestrator = new LayerOrchestrator();
$layerOrchestrator->initBaseLayer($WIDTH, $HEIGHT, '#000', 0);

// Back starfield
$starfield = new StarField($WIDTH, $HEIGHT);
$starfield->setBrightness(0, 30);
$layerOrchestrator->addLayer($starfield->render());

// Front starfield
$starfield = new StarField($WIDTH, $HEIGHT);
$starfield->setBrightness(0, 80);
$layerOrchestrator->addLayer($starfield->render());

// First blue nebulae
$nebulae = new Nebulae($WIDTH, $HEIGHT);
$nebulae->setColor('#4E2DB2');
$layerOrchestrator->addLayer($nebulae->render());

// Second pink/grey nebulae
$nebulae = new Nebulae($WIDTH, $HEIGHT);
$nebulae->setColor('#fb3a76');
$layerOrchestrator->addLayer($nebulae->render());

// Planet
$planet = new Planet('Lava');
$layerOrchestrator->addLayer($planet->render(), 0, 0);
//$planet = new Planet($WIDTH, $HEIGHT);
//$layerOrchestrator->addLayer($planet->render(), -300, -400);

$image = $layerOrchestrator->render();

Text::write($image, 'Seed: '.$seed, 10, 20);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
