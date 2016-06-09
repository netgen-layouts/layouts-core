<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Core\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Exception;

class LayoutResolverServiceTest extends \PHPUnit\Framework\TestCase
{
    use TestCase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverValidatorMock;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        $this->layoutResolverHandlerMock = $this->createMock(LayoutResolverHandler::class);

        $this->persistenceHandler
            ->expects($this->any())
            ->method('getLayoutResolverHandler')
            ->will($this->returnValue($this->layoutResolverHandlerMock));

        $this->layoutResolverValidatorMock = $this->createMock(LayoutResolverValidator::class);

        $this->layoutResolverService = $this->createLayoutResolverService($this->layoutResolverValidatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createRule
     * @expectedException \Exception
     */
    public function testCreateRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('createRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRule(new RuleCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     * @expectedException \Exception
     */
    public function testUpdateRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('updateRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRule(
            new RuleDraft(),
            new RuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     * @expectedException \Exception
     */
    public function testCopyRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('copyRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Exception
     */
    public function testCreateDraft()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('ruleExists')
            ->will($this->returnValue(false));

        $this->layoutResolverHandlerMock
            ->expects($this->at(2))
            ->method('deleteRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createDraft(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Exception
     */
    public function testDiscardDraft()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('deleteRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardDraft(new RuleDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     * @expectedException \Exception
     */
    public function testPublishRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('deleteRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRule(new RuleDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteRule
     * @expectedException \Exception
     */
    public function testDeleteRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('deleteRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Exception
     */
    public function testEnableRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will(
                $this->returnValue(
                    new PersistenceRule(
                        array(
                            'layoutId' => 42,
                            'enabled' => false,
                        )
                    )
                )
            );

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('getTargetCount')
            ->will($this->returnValue(2));

        $this->layoutResolverHandlerMock
            ->expects($this->at(2))
            ->method('enableRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Exception
     */
    public function testDisableRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will(
                $this->returnValue(
                    new PersistenceRule(
                        array(
                            'enabled' => true,
                        )
                    )
                )
            );

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('disableRule')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Exception
     */
    public function testAddTarget()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('loadRuleTargets')
            ->will($this->returnValue(array()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(2))
            ->method('addTarget')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->addTarget(
            new RuleDraft(),
            new TargetCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Exception
     */
    public function testDeleteTarget()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadTarget')
            ->will($this->returnValue(new PersistenceTarget()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('deleteTarget')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteTarget(new TargetDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     * @expectedException \Exception
     */
    public function testAddCondition()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('addCondition')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->addCondition(
            new RuleDraft(),
            new ConditionCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     * @expectedException \Exception
     */
    public function testUpdateCondition()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadCondition')
            ->will($this->returnValue(new PersistenceCondition()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('updateCondition')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateCondition(
            new ConditionDraft(),
            new ConditionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Exception
     */
    public function testDeleteCondition()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadCondition')
            ->will($this->returnValue(new PersistenceCondition()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('deleteCondition')
            ->will($this->throwException(new Exception()));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteCondition(new ConditionDraft());
    }
}
