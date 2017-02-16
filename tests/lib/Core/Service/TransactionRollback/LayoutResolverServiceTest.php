<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Exception;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition as PersistenceCondition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target as PersistenceTarget;

class LayoutResolverServiceTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testCreateRule()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('createRule')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createRule(new RuleCreateStruct());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRule(
            new Rule(array('published' => false)),
            new RuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateRuleMetadata()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->will($this->returnValue(new PersistenceRule()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('updateRuleMetadata')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateRuleMetadata(
            new Rule(array('published' => true)),
            new RuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->copyRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->createDraft(new Rule(array('published' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->discardDraft(new Rule(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->publishRule(new Rule(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteRule(new Rule());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->method('updateRuleMetadata')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->enableRule(new Rule(array('published' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->method('updateRuleMetadata')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->disableRule(new Rule(array('published' => true)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->addTarget(
            new Rule(array('published' => false)),
            new TargetCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testUpdateTarget()
    {
        $this->layoutResolverHandlerMock
            ->expects($this->at(0))
            ->method('loadTarget')
            ->will($this->returnValue(new PersistenceTarget()));

        $this->layoutResolverHandlerMock
            ->expects($this->at(1))
            ->method('updateTarget')
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateTarget(
            new Target(array('published' => false)),
            new TargetUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteTarget(new Target(array('published' => false)));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->addCondition(
            new Rule(array('published' => false)),
            new ConditionCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->updateCondition(
            new Condition(array('published' => false)),
            new ConditionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
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
            ->will($this->throwException(new Exception('Test exception text')));

        $this->persistenceHandler
            ->expects($this->once())
            ->method('rollbackTransaction');

        $this->layoutResolverService->deleteCondition(new Condition(array('published' => false)));
    }
}
