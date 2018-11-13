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
