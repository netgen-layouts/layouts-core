<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule as RuleValue;
use Netgen\BlockManager\Transfer\Output\Visitor\Rule;

abstract class RuleTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new RuleValue());
    }

    public function getVisitor()
    {
        return new Rule();
    }

    public function acceptProvider()
    {
        return [
            [new RuleValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->layoutResolverService->loadRule(2); }, 'rule/rule_2.json'],
            [function () { return $this->layoutResolverService->loadRule(11); }, 'rule/rule_11.json'],
        ];
    }
}
