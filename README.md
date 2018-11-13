# Odin - The Celestial Planet Generator

![](odin-logo.png)

Odin aims to render randomly generated planets, moons and star fields as images.

## Render a moon

```php
$moon = new Odin\Moon();
$moonImage = $moon
    ->diameter(150) // a 150px wide moon
    ->render();
// $moonImage is a \SplFileObject, you're free to do what you want with it
```
