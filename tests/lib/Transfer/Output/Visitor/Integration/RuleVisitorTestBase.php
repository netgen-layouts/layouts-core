<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
abstract class RuleVisitorTestBase extends VisitorTestBase
{
    final public function getVisitor(): VisitorInterface
    {
        return new RuleVisitor();
    }

    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Rule(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Rule => $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')), 'rule/rule_2.json'],
            [fn (): Rule => $this->layoutResolverService->loadRule(Uuid::fromString('c6891782-9d3e-58b7-95ac-261f491cc1ae')), 'rule/rule_11.json'],
        ];
    }
}
