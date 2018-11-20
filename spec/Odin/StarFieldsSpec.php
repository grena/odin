<?php

namespace spec\Odin;

use Odin\Configuration;
use Odin\StarFields;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;

class StarFieldsSpec extends ObjectBehavior
{
    /** @var Filesystem */
    private $fileSystem;

    private const PATH = '/tmp/odin';

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StarFields::class);
    }

    function let()
    {
        $this->fileSystem->mkdir(self::PATH, 0744);
        $this->beConstructedWith(new Configuration(self::PATH));
    }

    function letGo()
    {
        $this->fileSystem->remove(self::PATH);
    }

    function it_throws_an_exception_when_rendering_star_fields_without_height()
    {
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_throws_an_exception_when_rendering_star_fields_without_width()
    {
        $this->height(10);
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_throws_an_exception_when_rendering_star_fields_without_any_actual_field()
    {
        $this->height(10)->width(10);
        $this->shouldThrow(\LogicException::class)->during('render');
    }

    function it_renders_a_star_fields()
    {
        $this->height(10)->width(10)->addStarField(10)->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_a_star_fields_with_a_nebulae()
    {
        $this->height(10)->width(10)->addStarField(10)->addNebulae(10, 10, '#4E2DB2')->render()->shouldReturnAnInstanceOf(\SplFileObject::class);
    }

    function it_renders_the_same_star_fields_image_several_times_with_the_same_star_fields_object()
    {
        $starFields = $this->height(100)->width(100)->addStarField(100)->addNebulae(100, 100, '#4E2DB2');
        $firstRendering = $starFields->render()->getWrappedObject();
        $secondRendering = $starFields->render()->getWrappedObject();

        $this->assertFilesContentIdentical($firstRendering, $secondRendering);
    }

    function it_renders_the_same_star_fields_with_a_given_seed()
    {
        $this->beConstructedWith(new Configuration(self::PATH, 42));
        $starFields = $this->height(100)->width(100)->addStarField(100)->addNebulae(100, 100, '#4E2DB2');

        $initialRendering = new \SplFileObject(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'odin-star-field-seed-42.png');
        $laterRendering = $starFields->render()->getWrappedObject();

        $this->assertFilesContentIdentical($initialRendering, $laterRendering);
    }

    function it_renders_star_fields_image_in_a_particular_directory()
    {
        $fileSystem = new Filesystem();
        $fileSystem->mkdir('/tmp/odin-celestial-planet-generator', 0744);

        $this->beConstructedWith(new Configuration('/tmp/odin-celestial-planet-generator'));

        $starFields = $this->height(100)->width(100)->addStarField(100)->addNebulae(100, 100, '#4E2DB2');
        $starFields->render()->shouldReturnAnInstanceOf(\SplFileObject::class);

        $existingFiles = glob('/tmp/odin-celestial-planet-generator/*png');
        $fileSystem->remove('/tmp/odin-celestial-planet-generator');

        if (empty($existingFiles)) {
            throw new FailureException('Impossible to create star fields in a particular directory');
        }    }

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
