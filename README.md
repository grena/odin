# Odin - The Celestial Planet Generator

![](odin-logo.png)

Odin aims to render randomly generated planets, moons and star fields as PNG images.

## Render a planet

```php
$planet = new Odin\Planet();
$planetImage = $planet
    ->diameter(300) // a 300px wide planet
    ->lava()        // a planet with the lava biome
    ->render();
// $planetImage is a \SplFileObject, you're free to do what you want with it
```

A planet can have the following biomes: `toxic`, `forest`, `ashes`, `violet`, `lava`, `atoll`, `coldGaz`, `hotGaz`, `hydroGaz`.  


## Render a moon

```php
$moon = new Odin\Moon();
$moonImage = $moon
    ->diameter(150) // a 150px wide moon
    ->render();
// $moonImage is a \SplFileObject, you're free to do what you want with it
```

## Rendering the same object later

It's possible to render the same moon in different PHP processes (it works also for the planets and star fields). To achieve that, you just need to pass the `seed` to your object.

```php
$seed = 42;
$moon = new Odin\Moon($seed);
$moonImage = $moon
    ->diameter(150)
    ->render();
```

## Rendering multiple times an object

It's possible to render the same planet several times (it works also for the moons and star fields). You'll get the same image results.

```php
$planet = new Odin\Planet();
$planet->diameter(300)->lava();

$firstImage = $planet->render();
// do some other stuff...
$secondImage = $planet->render();

// $firstImage and $secondImage are two different files. But their content are identical.
```
