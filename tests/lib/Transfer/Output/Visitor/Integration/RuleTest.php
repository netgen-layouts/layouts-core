<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule as RuleValue;
use Netgen\BlockManager\Transfer\Output\Visitor\Rule;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class RuleTest extends VisitorTest
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
        $this->getVisitor()->visit(new RuleValue());
    }

    public function getVisitor(): VisitorInterface
    {
        return new Rule();
    }

    public function acceptProvider(): array
    {
        return [
            [new RuleValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APIRule { return $this->layoutResolverService->loadRule(2); }, 'rule/rule_2.json'],
            [function (): APIRule { return $this->layoutResolverService->loadRule(11); }, 'rule/rule_11.json'],
        ];
    }
}
