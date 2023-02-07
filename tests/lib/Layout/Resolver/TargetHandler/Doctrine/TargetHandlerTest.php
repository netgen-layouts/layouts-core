<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetHandler\Doctrine;

use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use Netgen\Layouts\Layout\Resolver\TargetHandler\Doctrine\Route;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Value;

final class TargetHandlerTest extends TargetHandlerTestBase
{
    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRulesWithNoTargetMatch(): void
    {
        $rules = $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
            $this->getTargetIdentifier(),
            'some_non_existent_route',
        );

        self::assertEmpty($rules);
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler::matchRules
     * @covers \Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler::matchRules
     */
    public function testMatchRulesWithNonExistentTargetHandler(): void
    {
        $this->expectException(TargetHandlerException::class);
        $this->expectExceptionMessage('Doctrine target handler for "non_existent" target type does not exist.');

        $this->handler->matchRules(
            $this->handler->loadRuleGroup(RuleGroup::ROOT_UUID, Value::STATUS_PUBLISHED),
            'non_existent',
            'value',
        );
    }

    /**
     * Returns the target handler identifier under test.
     */
    protected function getTargetIdentifier(): string
    {
        return 'route';
    }

    /**
     * Creates the handler under test.
     */
    protected function getTargetHandler(): TargetHandlerInterface
    {
        return new Route();
    }
}
