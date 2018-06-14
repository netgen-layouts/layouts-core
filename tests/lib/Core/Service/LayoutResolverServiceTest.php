<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Exception\NotFoundException;

abstract class LayoutResolverServiceTest extends ServiceTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->layoutService = $this->createLayoutService();

        $this->layoutResolverService = $this->createLayoutResolverService();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRule
     */
    public function testLoadRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(3);

        $this->assertTrue($rule->isPublished());
        $this->assertInstanceOf(Rule::class, $rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "999999"
     */
    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadRule(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleDraft
     */
    public function testLoadRuleDraft(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->assertTrue($rule->isDraft());
        $this->assertInstanceOf(Rule::class, $rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "999999"
     */
    public function testLoadRuleDraftThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadRuleDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleArchive
     */
    public function testLoadRuleArchive(): void
    {
        $ruleDraft = $this->layoutResolverService->loadRuleDraft(7);
        $this->layoutResolverService->publishRule($ruleDraft);

        $ruleArchive = $this->layoutResolverService->loadRuleArchive(7);

        $this->assertTrue($ruleArchive->isArchived());
        $this->assertInstanceOf(Rule::class, $ruleArchive);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRuleArchive
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "999999"
     */
    public function testLoadRuleArchiveThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadRuleArchive(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRules(): void
    {
        $rules = $this->layoutResolverService->loadRules();

        $this->assertCount(12, $rules);

        foreach ($rules as $rule) {
            $this->assertTrue($rule->isPublished());
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRulesWithLayout(): void
    {
        $rules = $this->layoutResolverService->loadRules(
            $this->layoutService->loadLayout(1)
        );

        $this->assertCount(2, $rules);

        foreach ($rules as $rule) {
            $this->assertTrue($rule->isPublished());
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadRules
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only published layouts can be used in rules.
     */
    public function testLoadRulesWithDraftLayoutThrowsBadStateException(): void
    {
        $this->layoutResolverService->loadRules(
            $this->layoutService->loadLayoutDraft(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCount(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount();

        $this->assertEquals(12, $ruleCount);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCountWithLayout(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayout(1)
        );

        $this->assertEquals(2, $ruleCount);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::getRuleCount
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "layout" has an invalid state. Only published layouts can be used in rules.
     */
    public function testGetRuleCountThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayoutDraft(1)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->layoutResolverService->matchRules('route', 'my_cool_route');

        $this->assertNotEmpty($rules);

        foreach ($rules as $rule) {
            $this->assertTrue($rule->isPublished());
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTarget
     */
    public function testLoadTarget(): void
    {
        $target = $this->layoutResolverService->loadTarget(7);

        $this->assertTrue($target->isPublished());
        $this->assertInstanceOf(Target::class, $target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "999999"
     */
    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadTarget(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTargetDraft
     */
    public function testLoadTargetDraft(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);

        $this->assertTrue($target->isDraft());
        $this->assertInstanceOf(Target::class, $target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadTargetDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "999999"
     */
    public function testLoadTargetDraftThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadTargetDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadCondition
     */
    public function testLoadCondition(): void
    {
        $condition = $this->layoutResolverService->loadCondition(1);

        $this->assertTrue($condition->isPublished());
        $this->assertInstanceOf(Condition::class, $condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "999999"
     */
    public function testLoadConditionThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadCondition(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadConditionDraft
     */
    public function testLoadConditionDraft(): void
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);

        $this->assertTrue($condition->isDraft());
        $this->assertInstanceOf(Condition::class, $condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::loadConditionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "999999"
     */
    public function testLoadConditionDraftThrowsNotFoundException(): void
    {
        $this->layoutResolverService->loadConditionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule($ruleCreateStruct);

        $this->assertTrue($createdRule->isDraft());
        $this->assertInstanceOf(Rule::class, $createdRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 3;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertTrue($updatedRule->isDraft());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertInstanceOf(Layout::class, $updatedRule->getLayout());
        $this->assertTrue($updatedRule->getLayout()->isPublished());
        $this->assertEquals(3, $updatedRule->getLayout()->getId());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertTrue($updatedRule->isDraft());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertInstanceOf(Layout::class, $updatedRule->getLayout());
        $this->assertTrue($updatedRule->getLayout()->isPublished());
        $this->assertEquals(2, $updatedRule->getLayout()->getId());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithEmptyLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 0;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        $this->assertTrue($updatedRule->isDraft());
        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertNull($updatedRule->getLayout());
        $this->assertEquals('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Only draft rules can be updated.
     */
    public function testUpdateRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = 3;
        $ruleUpdateStruct->comment = 'Updated comment';

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata(): void
    {
        $rule = $this->layoutResolverService->loadRule(4);

        $updatedRule = $this->layoutResolverService->updateRuleMetadata(
            $rule,
            new RuleMetadataUpdateStruct(
                [
                    'priority' => 50,
                ]
            )
        );

        $this->assertInstanceOf(Rule::class, $updatedRule);
        $this->assertEquals(50, $updatedRule->getPriority());
        $this->assertTrue($updatedRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateRuleMetadata
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Metadata can be updated only for published rules.
     */
    public function testUpdateRuleMetadataThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->updateRuleMetadata(
            $rule,
            new RuleMetadataUpdateStruct(
                [
                    'priority' => 50,
                ]
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(2);
        $copiedRule = $this->layoutResolverService->copyRule($rule);

        $this->assertEquals($rule->isPublished(), $copiedRule->isPublished());
        $this->assertInstanceOf(Rule::class, $copiedRule);
        $this->assertEquals(13, $copiedRule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(3);

        $draftRule = $this->layoutResolverService->createDraft($rule);

        $this->assertTrue($draftRule->isDraft());
        $this->assertInstanceOf(Rule::class, $draftRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(3);
        $this->layoutResolverService->createDraft($rule);

        $draftRule = $this->layoutResolverService->createDraft($rule, true);

        $this->assertTrue($draftRule->isDraft());
        $this->assertInstanceOf(Rule::class, $draftRule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Drafts can only be created from published rules.
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->createDraft($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. The provided rule already has a draft.
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $rule = $this->layoutResolverService->loadRule(3);
        $this->layoutResolverService->createDraft($rule);

        $this->layoutResolverService->createDraft($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "5"
     */
    public function testDiscardDraft(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $this->layoutResolverService->discardDraft($rule);

        $this->layoutResolverService->loadRuleDraft($rule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Only draft rules can be discarded.
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(5);
        $this->layoutResolverService->discardDraft($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertTrue($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRuleWithNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);
        $this->layoutResolverService->updateRule(
            $rule,
            new RuleUpdateStruct(['layoutId' => 0])
        );

        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertFalse($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRuleWithNoTargets(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $this->layoutResolverService->deleteTarget(
            $this->layoutResolverService->loadTargetDraft(9)
        );

        $this->layoutResolverService->deleteTarget(
            $this->layoutResolverService->loadTargetDraft(10)
        );

        $publishedRule = $this->layoutResolverService->publishRule($rule);

        $this->assertInstanceOf(Rule::class, $publishedRule);
        $this->assertTrue($publishedRule->isPublished());
        $this->assertFalse($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::publishRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Only draft rules can be published.
     */
    public function testPublishRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(5);
        $this->layoutResolverService->publishRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $restoredRule = $this->layoutResolverService->restoreFromArchive(
            $this->layoutResolverService->loadRuleArchive(2)
        );

        $this->assertInstanceOf(Rule::class, $restoredRule);
        $this->assertTrue($restoredRule->isDraft());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::restoreFromArchive
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Only archived rules can be restored.
     */
    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->layoutResolverService->restoreFromArchive(
            $this->layoutResolverService->loadRule(2)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteRule
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find rule with identifier "5"
     */
    public function testDeleteRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->deleteRule($rule);

        $this->layoutResolverService->loadRule($rule->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(4);

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        $this->assertInstanceOf(Rule::class, $enabledRule);
        $this->assertTrue($enabledRule->isEnabled());
        $this->assertTrue($enabledRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Only published rules can be enabled.
     */
    public function testEnableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Rule is already enabled.
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleIsAlreadyEnabled(): void
    {
        $rule = $this->layoutResolverService->loadRule(1);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Rule is missing a layout and cannot be enabled.
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleHasNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRule(11);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::enableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Rule is missing targets and cannot be enabled.
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleHasNoTargets(): void
    {
        $rule = $this->layoutResolverService->loadRule(12);

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(1);

        $disabledRule = $this->layoutResolverService->disableRule($rule);

        $this->assertInstanceOf(Rule::class, $disabledRule);
        $this->assertFalse($disabledRule->isEnabled());
        $this->assertTrue($disabledRule->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Only published rules can be disabled.
     */
    public function testDisableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(7);

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::disableRule
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Rule is already disabled.
     */
    public function testDisableRuleThrowsBadStateExceptionIfRuleIsAlreadyDisabled(): void
    {
        $rule = $this->layoutResolverService->loadRule(4);

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget(): void
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdTarget = $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );

        $this->assertTrue($createdTarget->isDraft());
        $this->assertInstanceOf(Target::class, $createdTarget);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Targets can be added only to draft rules.
     */
    public function testAddTargetThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Rule with ID "5" only accepts targets with "route_prefix" target type.
     */
    public function testAddTargetOfDifferentKindThrowsBadStateException(): void
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route'
        );

        $targetCreateStruct->value = 'some_route';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $updatedTarget = $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);

        $this->assertTrue($updatedTarget->isDraft());
        $this->assertInstanceOf(Target::class, $updatedTarget);

        $this->assertEquals('new_value', $updatedTarget->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "target" has an invalid state. Only draft targets can be updated.
     */
    public function testUpdateTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $target = $this->layoutResolverService->loadTarget(9);

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find target with identifier "9"
     */
    public function testDeleteTarget(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(9);

        $this->layoutResolverService->deleteTarget($target);

        $this->layoutResolverService->loadTargetDraft($target->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteTarget
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "target" has an invalid state. Only draft targets can be deleted.
     */
    public function testDeleteTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $target = $this->layoutResolverService->loadTarget(9);

        $this->layoutResolverService->deleteTarget($target);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition(): void
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'my_condition'
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRuleDraft(5);

        $createdCondition = $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );

        $this->assertTrue($createdCondition->isDraft());
        $this->assertInstanceOf(Condition::class, $createdCondition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::addCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "rule" has an invalid state. Conditions can be added only to draft rules.
     */
    public function testAddConditionThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'my_condition'
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRule(5);

        $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);

        $this->assertTrue($updatedCondition->isDraft());
        $this->assertInstanceOf(Condition::class, $updatedCondition);

        $this->assertEquals('new_value', $updatedCondition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::updateCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "condition" has an invalid state. Only draft conditions can be updated.
     */
    public function testUpdateConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $condition = $this->layoutResolverService->loadCondition(4);

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find condition with identifier "4"
     */
    public function testDeleteCondition(): void
    {
        $condition = $this->layoutResolverService->loadConditionDraft(4);
        $this->layoutResolverService->deleteCondition($condition);

        $this->layoutResolverService->loadConditionDraft($condition->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::deleteCondition
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     * @expectedExceptionMessage Argument "condition" has an invalid state. Only draft conditions can be deleted.
     */
    public function testDeleteConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $condition = $this->layoutResolverService->loadCondition(4);
        $this->layoutResolverService->deleteCondition($condition);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct(): void
    {
        $this->assertEquals(
            new RuleCreateStruct(),
            $this->layoutResolverService->newRuleCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct(): void
    {
        $this->assertEquals(
            new RuleUpdateStruct(),
            $this->layoutResolverService->newRuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct(): void
    {
        $this->assertEquals(
            new RuleMetadataUpdateStruct(),
            $this->layoutResolverService->newRuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct(): void
    {
        $createStruct = $this->layoutResolverService->newTargetCreateStruct('target');
        $createStruct->value = '42';

        $this->assertEquals(
            new TargetCreateStruct(
                [
                    'type' => 'target',
                    'value' => '42',
                ]
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct(): void
    {
        $updateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new TargetUpdateStruct(
                [
                    'value' => '42',
                ]
            ),
            $updateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct(): void
    {
        $createStruct = $this->layoutResolverService->newConditionCreateStruct('condition');
        $createStruct->value = 42;

        $this->assertEquals(
            new ConditionCreateStruct(
                [
                    'type' => 'condition',
                    'value' => '42',
                ]
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\LayoutResolverService::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct(): void
    {
        $updateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new ConditionUpdateStruct(
                [
                    'value' => '42',
                ]
            ),
            $updateStruct
        );
    }
}
