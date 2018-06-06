<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Closure;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class VisitorTest extends ServiceTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $subVisitorMock;

    /**
     * @var \Coduo\PHPMatcher\Factory\SimpleFactory
     */
    private $matcherFactory;

    public function setUp()
    {
        parent::setUp();

        $this->subVisitorMock = $this->createMock(VisitorInterface::class);

        $this->subVisitorMock
            ->expects($this->any())
            ->method('visit')
            ->will($this->returnValue('sub_visit_value'));

        $this->matcherFactory = new SimpleFactory();
    }

    /**
     * @param mixed $value
     * @param bool $accepted
     *
     * @dataProvider acceptProvider
     */
    public function testAccept($value, $accepted)
    {
        $this->assertEquals($accepted, $this->getVisitor()->accept($value));
    }

    /**
     * @param mixed $value
     * @param string $fixturePath
     *
     * @dataProvider visitProvider
     */
    public function testVisit($value, $fixturePath)
    {
        $fixturePath = __DIR__ . '/_fixtures/' . $fixturePath;

        if (!file_exists($fixturePath)) {
            throw new RuntimeException(sprintf('%s file does not exist.', $fixturePath));
        }

        if (is_callable($value)) {
            // We're using closures as values in case data providers need dependencies
            // from setUp method, because data providers are executed before the setUp method
            // This rebinds the closure to $this, to get the instantiated dependencies
            // https://github.com/sebastianbergmann/phpunit/issues/3097
            $value = Closure::bind($value, $this);
            $value = $value();
        }

        $expectedData = trim((string) file_get_contents($fixturePath));
        $visitedData = $this->getVisitor()->visit($value, $this->subVisitorMock);

        $matcher = $this->matcherFactory->createMatcher();
        $matchResult = $matcher->match($visitedData, json_decode($expectedData, true));

        if (!$matchResult) {
            $diff = new Diff(explode(PHP_EOL, json_encode($visitedData, JSON_PRETTY_PRINT)), explode(PHP_EOL, $expectedData));

            $this->fail($matcher->getError() . PHP_EOL . $diff->render(new Diff_Renderer_Text_Unified()));
        }

        // Fake assertion to disable risky flag
        $this->assertTrue(true);
    }

    /**
     * Returns the visitor under test.
     *
     * @return \Netgen\BlockManager\Transfer\Output\VisitorInterface
     */
    abstract public function getVisitor();

    /**
     * Provides data for testing VisitorInterface::accept method.
     *
     * @return array
     */
    abstract public function acceptProvider();

    /**
     * Provides data for testing VisitorInterface::visit method.
     *
     * @return array
     */
    abstract public function visitProvider();
}
