<?php

namespace spec\Odin;

use Odin\Moon;
use PhpSpec\Exception\Example\NotEqualException;
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

    function it_renders_the_same_moon_image_several_times_with_the_same_moon_object()
    {
        $moon = $this->diameter(50);
        $firstRendering = $moon->render()->getWrappedObject();
        $secondRendering = $moon->render()->getWrappedObject();

        // TODO: this test is very weak, because we don't compare the pixels
        // we compare the content of the files, they can be different even
        // if the images are identical
        $firstRenderingContent = md5_file($firstRendering->getRealPath());
        $secondRenderingContent = md5_file($secondRendering->getRealPath());

        if ($firstRenderingContent !== $secondRenderingContent) {
            throw new NotEqualException('The images are not identical.', $firstRenderingContent, $secondRenderingContent);
        }
    }
}
