<?php
declare(strict_types=1);

error_reporting(E_ALL);

include_once (__DIR__ . '/../vendor/autoload.php');

use Odin\Astronomical\Planet\Planet;
use Odin\Astronomical\Star\Nebulae;
use Odin\Astronomical\Star\StarField;
use Odin\Drawer\Gd\LayerOrchestrator;
use Odin\Drawer\Gd\Text;

$WIDTH = 800;
$HEIGHT = 800;

$seed = rand();
//$seed = 1171281281;
mt_srand($seed);

$layerOrchestrator = new LayerOrchestrator();
$layerOrchestrator->initBaseLayer($WIDTH, $HEIGHT, '#000', 0);

// Back starfield
$starfield = new StarField($WIDTH, $HEIGHT);
$starfield->setBrightness(0, 15);
$layerOrchestrator->addLayer($starfield->render());
//
// Front starfield
$starfield = new StarField($WIDTH, $HEIGHT);
$starfield->setBrightness(0, 60);
$layerOrchestrator->addLayer($starfield->render());
//
// First blue nebulae
$nebulae = new Nebulae($WIDTH, $HEIGHT);
$nebulae->setColor('#4E2DB2');
$layerOrchestrator->addLayer($nebulae->render());

// Second pink/grey nebulae
$nebulae = new Nebulae($WIDTH, $HEIGHT);
$nebulae->setColor('#fb3a76');
$layerOrchestrator->addLayer($nebulae->render());

$biomes = [
    'Ashes',
    'Lava',
    'Forest',
    'Toxic',
    'Violet',
    'Cold Gaz',
    'Hydro Gaz',
    'Hot Gaz',
    'Atoll',
];
shuffle($biomes);
$biomeName = current($biomes);

// Planet
$planetSize = rand(75, 200) * 2;
$planet = new Planet($biomeName, $planetSize);
$layerOrchestrator->addLayer($planet->render(), 0, 0);

$planet = new Planet('Moon', 60);
$layerOrchestrator->addLayer($planet->render(), $planetSize + 250, 300);

$image = $layerOrchestrator->render();

Text::write($image, 'Seed: '.$seed, 10, 20);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
