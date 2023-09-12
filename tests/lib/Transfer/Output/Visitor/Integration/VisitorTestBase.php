<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Closure;
use Coduo\PHPMatcher\PHPMatcher;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function json_encode;
use function sprintf;
use function trim;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

/**
 * @template T of object
 */
abstract class VisitorTestBase extends CoreTestCase
{
    /**
     * @param mixed $value
     *
     * @dataProvider acceptDataProvider
     */
    public function testAccept($value, bool $accepted): void
    {
        self::assertSame($accepted, $this->getVisitor()->accept($value));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider visitDataProvider
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
        $visitedData = $this->getVisitor()->visit($value, new OutputVisitor([new VisitorStub()]));

        $matcher = new PHPMatcher();
        $matchResult = $matcher->match($visitedData, json_decode($expectedData, true, 512, JSON_THROW_ON_ERROR));

        if (!$matchResult) {
            $visitedData = json_encode($visitedData, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
            self::fail($matcher->error() . PHP_EOL . $differ->diff($expectedData, $visitedData));
        }

        // We fake the assertion count to disable risky warning
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the visitor under test.
     *
     * @return \Netgen\Layouts\Transfer\Output\VisitorInterface<T>
     */
    abstract public function getVisitor(): VisitorInterface;

    /**
     * Provides data for testing VisitorInterface::accept method.
     */
    abstract public static function acceptDataProvider(): iterable;

    /**
     * Provides data for testing VisitorInterface::visit method.
     */
    abstract public static function visitDataProvider(): iterable;
}
