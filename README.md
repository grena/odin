# Odin - The Celestial Planet Generator

![](odin-logo.png)

Odin aims to render randomly generated planets, moons and star fields as images.

## Render a moon

```php
// generate a 300px wide moon
$moon = new Odin\Moon(300);
$moonImage = $moon->render();
// $moonImage is a \SplFileObject, you're free to do what you want with it
```
