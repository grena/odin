<?php

declare(strict_types=1);

namespace Odin;

class CelestialConfiguration
{
    /** @var string */
    private $directory;

    /** @var int */
    private $seed;

    public function __construct(?string $directory = null, int $seed = null)
    {
        if (null !== $directory) {
            if (!is_dir($directory)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist.', $directory));
            }

            if (!is_writable($directory)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.', $directory));
            }
        }

        $this->directory = $directory ?? sys_get_temp_dir();
        $this->seed = $seed ?? rand();
    }

    public function directory(): string
    {
        return $this->directory;
    }

    public function seed(): int
    {
        return $this->seed;
    }
}
