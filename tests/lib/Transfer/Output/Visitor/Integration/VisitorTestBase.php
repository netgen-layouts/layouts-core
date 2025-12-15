<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Coduo\PHPMatcher\PHPMatcher;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function json_encode;
use function mb_trim;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

/**
 * @template T of object
 */
abstract class VisitorTestBase extends CoreTestCase
{
    #[DataProvider('acceptDataProvider')]
    final public function testAccept(mixed $value, bool $accepted): void
    {
        self::assertSame($accepted, $this->getVisitor()->accept($value));
    }

    #[DataProvider('visitDataProvider')]
    final public function testVisit(string $fixturePath, string $id, string ...$additionalParameters): void
    {
        $fixturePath = __DIR__ . '/../../../_fixtures/output/' . $fixturePath;

        if (!file_exists($fixturePath)) {
            throw new RuntimeException(sprintf('%s file does not exist.', $fixturePath));
        }

        $value = $this->loadValue($id, ...$additionalParameters);

        $expectedData = mb_trim((string) file_get_contents($fixturePath));
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

    /**
     * Loads the value under test.
     *
     * @return T
     */
    abstract protected function loadValue(string $id, string ...$additionalParameters): object;
}
