# Odin - The Celestial Planet Generator

![](odin-logo.png)

Odin aims to render randomly generated planets, moons and star fields as PNG images.

## Render a planet

```php
$planet = new Odin\Planet();
$image = $planet
    ->diameter(300) // a 300px wide planet
    ->lava()        // a planet with the lava biome
    ->render();
    
// $image is a \SplFileObject, you're free to do what you want with it
```

A planet can have the following biomes: `toxic`, `forest`, `ashes`, `violet`, `lava`, `atoll`, `coldGaz`, `hotGaz`, `hydroGaz`.  


## Render a moon

```php
$moon = new Odin\Moon();
$image = $moon
    ->diameter(150) // a 150px wide moon
    ->render();
    
// $image is a \SplFileObject, you're free to do what you want with it
```

## Render the same object later

It's possible to render the same moon in different PHP processes or requests (it works also for planets and star fields). To achieve that, you just need to pass the `seed` to your object.

```php
$seed = 42;
$moon = new Odin\Moon($seed);
$image = $moon
    ->diameter(150)
    ->render();
```

## Render multiple times an object

It's possible to render the same planet several times (it works also for moons and star fields). You'll get the same image results.

```php
$planet = new Odin\Planet();
$planet->diameter(300)->lava();

$firstImage = $planet->render();
// do some other stuff...
$secondImage = $planet->render();

// $firstImage and $secondImage are two different files, but their content are identical
```

## Launch the tests

```bash
./vendor/bin/phpspec run
```
