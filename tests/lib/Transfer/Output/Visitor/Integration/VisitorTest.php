<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Closure;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Diff;
use Diff_Renderer_Text_Unified;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Tests\Core\CoreTestCase;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class VisitorTest extends CoreTestCase
{
    /**
     * @var \Netgen\BlockManager\Transfer\Output\VisitorInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $subVisitorMock;

    /**
     * @var \Coduo\PHPMatcher\Factory\SimpleFactory
     */
    private $matcherFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->subVisitorMock = $this->createMock(VisitorInterface::class);

        $this->subVisitorMock
            ->expects(self::any())
            ->method('visit')
            ->will(self::returnValue(['sub_visit_value']));

        $this->matcherFactory = new SimpleFactory();
    }

    /**
     * @param mixed $value
     * @param bool $accepted
     *
     * @dataProvider acceptProvider
     */
    public function testAccept($value, bool $accepted): void
    {
        self::assertSame($accepted, $this->getVisitor()->accept($value));
    }

    /**
     * @param mixed $value
     * @param string $fixturePath
     *
     * @dataProvider visitProvider
     */
    public function testVisit($value, string $fixturePath): void
    {
        $fixturePath = __DIR__ . '/../../../_fixtures/output/' . $fixturePath;

        if (!file_exists($fixturePath)) {
            throw new RuntimeException(sprintf('%s file does not exist.', $fixturePath));
        }

        if ($value instanceof Closure) {
            // We're using closures as values in case data providers need dependencies
            // from setUp method, because data providers are executed before the setUp method
            // This rebinds the closure to $this, to get the instantiated dependencies
            // https://github.com/sebastianbergmann/phpunit/issues/3097
            $value = $value->call($this);
        }

        $expectedData = trim((string) file_get_contents($fixturePath));
        $visitedData = $this->getVisitor()->visit($value, $this->subVisitorMock);

        $matcher = $this->matcherFactory->createMatcher();
        $matchResult = $matcher->match($visitedData, json_decode($expectedData, true));

        if (!$matchResult) {
            $visitedData = json_encode($visitedData, JSON_PRETTY_PRINT);
            $diff = new Diff(explode(PHP_EOL, is_string($visitedData) ? $visitedData : ''), explode(PHP_EOL, $expectedData));

            self::fail($matcher->getError() . PHP_EOL . $diff->render(new Diff_Renderer_Text_Unified()));
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the visitor under test.
     */
    abstract public function getVisitor(): VisitorInterface;

    /**
     * Provides data for testing VisitorInterface::accept method.
     */
    abstract public function acceptProvider(): array;

    /**
     * Provides data for testing VisitorInterface::visit method.
     */
    abstract public function visitProvider(): array;
}
