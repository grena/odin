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

        $this->assertFilesContentIdentical($firstRendering, $secondRendering);
    }

    function it_renders_the_same_moon_with_a_given_seed()
    {
        $this->beConstructedWith(42);
        $moon = $this->diameter(50);

        $initialRendering = new \SplFileObject(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'odin-moon-seed-42.png');
        $laterRendering = $moon->render()->getWrappedObject();

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
