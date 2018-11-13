<?php

namespace spec\Odin;

use Odin\Planet;
use PhpSpec\Exception\Example\NotEqualException;
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
    
    function it_renders_the_same_planet_image_several_times_with_the_same_planet_object()
    {
        $planet = $this->diameter(50)->lava();
        $firstRendering = $planet->render()->getWrappedObject();
        $secondRendering = $planet->render()->getWrappedObject();

        $this->assertFilesContentIdentical($firstRendering, $secondRendering);
    }

    function it_renders_the_same_planet_with_a_given_seed()
    {
        $this->beConstructedWith(42);
        $planet = $this->diameter(50)->lava();

        $initialRendering = new \SplFileObject(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'odin-planet-seed-42.png');
        $laterRendering = $planet->render()->getWrappedObject();

        $this->assertFilesContentIdentical($initialRendering, $laterRendering);
    }

    /**
     * @throws NotEqualException
     */
    private function assertFilesContentIdentical(\SplFileObject $file1, \SplFileObject $file2)
    {
        // TODO: this method is very weak, because we don't compare the pixels
        // we compare the content of the files, they can be different even
        // if the images are identical
        $content1 = md5_file($file1->getRealPath());
        $content2 = md5_file($file2->getRealPath());

        if ($content1 !== $content2) {
            throw new NotEqualException('The images are not identical.', $content1, $content2);
        }
    }
}
