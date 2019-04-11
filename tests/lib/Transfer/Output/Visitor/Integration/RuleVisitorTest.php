<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

abstract class RuleVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

        $this->getVisitor()->visit(new Rule());
    }

    public function getVisitor(): VisitorInterface
    {
        return new RuleVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Rule(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Rule { return $this->layoutResolverService->loadRule(2); }, 'rule/rule_2.json'],
            [function (): Rule { return $this->layoutResolverService->loadRule(11); }, 'rule/rule_11.json'],
        ];
    }
}
