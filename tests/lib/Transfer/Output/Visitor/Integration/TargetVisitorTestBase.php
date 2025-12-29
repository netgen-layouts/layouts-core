<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\Transfer\Output\Visitor\TargetVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\LayoutResolver\Target>
 */
abstract class TargetVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Target(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['target/target_1.json', 'c7c5cdca-02da-5ba5-ad9e-d25cbc4b1b46'],
            ['target/target_2.json', '0cd23062-3fa7-582f-b022-034595ec68d5'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new TargetVisitor();
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Target
    {
        return $this->layoutResolverService->loadTarget(Uuid::fromString($id));
    }
}
