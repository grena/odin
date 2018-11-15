<?php

namespace spec\Odin;

use Odin\CelestialConfiguration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CelestialConfigurationSpec extends ObjectBehavior
{
    function let()
    {
        mkdir('/tmp/odin');
        chmod('/tmp/odin', 0644);
    }

    function letGo()
    {
        rmdir('/tmp/odin');
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
        chmod('/tmp/odin', 0444);
        $this->beConstructedWith('/tmp/odin');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
