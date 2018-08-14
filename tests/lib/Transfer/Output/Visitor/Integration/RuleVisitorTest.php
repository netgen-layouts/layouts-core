<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Transfer\Output\Visitor\RuleVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class RuleVisitorTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
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
