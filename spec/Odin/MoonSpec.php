<?php

namespace spec\Odin;

use Odin\Moon;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MoonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Moon::class);
    }

    function it_throws_an_exception_when_rendering_a_planet_without_diameter()
    {
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_renders_a_moon_image()
    {
        $this->diameter(50)->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }
}
