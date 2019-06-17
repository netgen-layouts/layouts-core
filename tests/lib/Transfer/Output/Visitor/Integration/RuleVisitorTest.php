<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

abstract class RuleVisitorTest extends VisitorTest
{
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
            [function (): Rule { return $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')); }, 'rule/rule_2.json'],
            [function (): Rule { return $this->layoutResolverService->loadRule(Uuid::fromString('c6891782-9d3e-58b7-95ac-261f491cc1ae')); }, 'rule/rule_11.json'],
        ];
    }
}
