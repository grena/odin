<?php

namespace spec\Odin;

use Odin\Planet;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PlanetSpec extends ObjectBehavior
{
    private const DIAMETER = 50;

    function it_is_initializable()
    {
        $this->shouldHaveType(Planet::class);
    }

    function it_throws_an_exception_when_rendering_a_planet_without_diameter()
    {
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_throws_an_exception_when_rendering_a_planet_without_biome()
    {
        $this->diameter(self::DIAMETER);
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_renders_a_lava_planet()
    {
        $this->diameter(self::DIAMETER)->lava()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_toxic_planet()
    {
        $this->diameter(self::DIAMETER)->toxic()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_cold_gaz_planet()
    {
        $this->diameter(self::DIAMETER)->coldGaz()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_hot_gaz_planet()
    {
        $this->diameter(self::DIAMETER)->hotGaz()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_hydro_gaz_planet()
    {
        $this->diameter(self::DIAMETER)->hydroGaz()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_an_atoll_planet()
    {
        $this->diameter(self::DIAMETER)->atoll()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_violet_planet()
    {
        $this->diameter(self::DIAMETER)->violet()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_ashes_planet()
    {
        $this->diameter(self::DIAMETER)->ashes()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_forest_planet()
    {
        $this->diameter(self::DIAMETER)->forest()->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }
}
