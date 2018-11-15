<?php

namespace spec\Odin;

use Odin\CelestialConfiguration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class CelestialConfigurationSpec extends ObjectBehavior
{
    /** @var Filesystem */
    private $fileSystem;

    function let()
    {
        $this->fileSystem = new Filesystem();
        $this->fileSystem->mkdir('/tmp/odin', 0644);
    }

    function letGo()
    {
        $this->fileSystem->remove('/tmp/odin');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CelestialConfiguration::class);
    }

    function it_has_a_default_directory_to_render_images()
    {
        $this->directory()->shouldReturn(sys_get_temp_dir());
    }

    function it_has_an_existing_directory_to_render_images()
    {
        $this->beConstructedWith('/tmp/odin');
        $this->directory()->shouldReturn('/tmp/odin');
    }

    function it_throws_an_exception_if_the_directory_does_not_exist()
    {
        $this->beConstructedWith('/tmp/does/not/exist');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_the_directory_is_not_writable()
    {
        $this->fileSystem->chmod('/tmp/odin', 0444);
        $this->beConstructedWith('/tmp/odin');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_has_a_default_seed()
    {
        $this->beConstructedWith();
        $this->seed()->shouldBeInteger();
    }

    function it_has_a_fixed_seed()
    {
        $this->beConstructedWith(null, 42);
        $this->seed()->shouldReturn(42);
    }
}
