<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use Ramsey\Uuid\Uuid;

abstract class LayoutResolverServiceTest extends CoreTestCase
{
    use ExportObjectTrait;
    use UuidGeneratorTrait;

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRule
     */
    public function testLoadRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));

        self::assertTrue($rule->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRule
     */
    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRule(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleDraft
     */
    public function testLoadRuleDraft(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        self::assertTrue($rule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleDraft
     */
    public function testLoadRuleDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleArchive
     */
    public function testLoadRuleArchive(): void
    {
        $ruleDraft = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $this->layoutResolverService->publishRule($ruleDraft);

        $ruleArchive = $this->layoutResolverService->loadRuleArchive(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        self::assertTrue($ruleArchive->isArchived());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleArchive
     */
    public function testLoadRuleArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleArchive(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRules(): void
    {
        $rules = $this->layoutResolverService->loadRules();

        self::assertCount(12, $rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRulesWithLayout(): void
    {
        $rules = $this->layoutResolverService->loadRules(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))
        );

        self::assertCount(2, $rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRulesWithDraftLayoutThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only published layouts can be used in rules.');

        $this->layoutResolverService->loadRules(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCount(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount();

        self::assertSame(12, $ruleCount);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCountWithLayout(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))
        );

        self::assertSame(2, $ruleCount);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCountThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only published layouts can be used in rules.');

        $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136'))
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::matchRules
     */
    public function testMatchRules(): void
    {
        $rules = $this->layoutResolverService->matchRules('route', 'my_cool_route');

        self::assertNotEmpty($rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadTarget
     */
    public function testLoadTarget(): void
    {
        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5f086fc4-4e1c-55eb-ae54-79fc296cda37'));

        self::assertTrue($target->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadTarget
     */
    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadTarget(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadTargetDraft
     */
    public function testLoadTargetDraft(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        self::assertTrue($target->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadTargetDraft
     */
    public function testLoadTargetDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadTargetDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadCondition
     */
    public function testLoadCondition(): void
    {
        $condition = $this->layoutResolverService->loadCondition(Uuid::fromString('35f4594c-6674-5815-add6-07f288b79686'));

        self::assertTrue($condition->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadCondition
     */
    public function testLoadConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadCondition(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadConditionDraft
     */
    public function testLoadConditionDraft(): void
    {
        $condition = $this->layoutResolverService->loadConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        self::assertTrue($condition->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadConditionDraft
     */
    public function testLoadConditionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadConditionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule($ruleCreateStruct);

        self::assertTrue($createdRule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9');
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertInstanceOf(Layout::class, $updatedRule->getLayout());
        self::assertTrue($updatedRule->getLayout()->isPublished());
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $updatedRule->getLayout()->getId()->toString());
        self::assertSame('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertInstanceOf(Layout::class, $updatedRule->getLayout());
        self::assertTrue($updatedRule->getLayout()->isPublished());
        self::assertSame('71cbe281-430c-51d5-8e21-c3cc4e656dac', $updatedRule->getLayout()->getId()->toString());
        self::assertSame('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;
        $ruleUpdateStruct->comment = 'Updated comment';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertNull($updatedRule->getLayout());
        self::assertSame('Updated comment', $updatedRule->getComment());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be updated.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9');
        $ruleUpdateStruct->comment = 'Updated comment';

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $struct = new RuleMetadataUpdateStruct();
        $struct->priority = 50;

        $updatedRule = $this->layoutResolverService->updateRuleMetadata(
            $rule,
            $struct
        );

        self::assertSame(50, $updatedRule->getPriority());
        self::assertTrue($updatedRule->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadataThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Metadata can be updated only for published rules.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $struct = new RuleMetadataUpdateStruct();
        $struct->priority = 50;

        $this->layoutResolverService->updateRuleMetadata($rule, $struct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6'));

        $copiedRule = $this->layoutResolverService->copyRule($rule);

        self::assertSame($rule->isPublished(), $copiedRule->isPublished());
        self::assertNotSame($rule->getId()->toString(), $copiedRule->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));

        $draftRule = $this->layoutResolverService->createDraft($rule);

        self::assertTrue($draftRule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));
        $this->layoutResolverService->createDraft($rule);

        $draftRule = $this->layoutResolverService->createDraft($rule, true);

        self::assertTrue($draftRule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Drafts can only be created from published rules.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->createDraft($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createDraft
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. The provided rule already has a draft.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));
        $this->layoutResolverService->createDraft($rule);

        $this->layoutResolverService->createDraft($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::discardDraft
     */
    public function testDiscardDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "de086bdf-0014-5f4f-89e4-fc0aff21da90"');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->discardDraft($rule);

        $this->layoutResolverService->loadRuleDraft($rule->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::discardDraft
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be discarded.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->discardDraft($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        self::assertTrue($publishedRule->isPublished());
        self::assertTrue($publishedRule->isEnabled());

        try {
            $this->layoutResolverService->loadRuleDraft($rule->getId());
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::publishRule
     */
    public function testPublishRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be published.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->publishRule($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::restoreFromArchive
     */
    public function testRestoreFromArchive(): void
    {
        $restoredRule = $this->layoutResolverService->restoreFromArchive(
            $this->layoutResolverService->loadRuleArchive(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6'))
        );

        self::assertTrue($restoredRule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::restoreFromArchive
     */
    public function testRestoreFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived rules can be restored.');

        $this->layoutResolverService->restoreFromArchive(
            $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6'))
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteRule
     */
    public function testDeleteRule(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "de086bdf-0014-5f4f-89e4-fc0aff21da90"');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->deleteRule($rule);

        $this->layoutResolverService->loadRule($rule->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        self::assertTrue($enabledRule->isEnabled());
        self::assertTrue($enabledRule->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be enabled.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRule
     */
    public function testEnableRuleThrowsBadStateExceptionIfRuleIsAlreadyEnabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule is already enabled.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634'));

        $this->layoutResolverService->enableRule($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634'));

        $disabledRule = $this->layoutResolverService->disableRule($rule);

        self::assertFalse($disabledRule->isEnabled());
        self::assertTrue($disabledRule->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be disabled.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRule
     */
    public function testDisableRuleThrowsBadStateExceptionIfRuleIsAlreadyDisabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule is already disabled.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $this->layoutResolverService->disableRule($rule);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTarget(): void
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $createdTarget = $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );

        self::assertTrue($createdTarget->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTargetThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Targets can be added only to draft rules.');

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix'
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
    public function testAddTargetOfDifferentKindThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule with UUID "de086bdf-0014-5f4f-89e4-fc0aff21da90" only accepts targets with "route_prefix" target type.');

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route'
        );

        $targetCreateStruct->value = 'some_route';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTarget(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $updatedTarget = $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);

        self::assertTrue($updatedTarget->isDraft());
        self::assertSame('new_value', $updatedTarget->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateTarget
     */
    public function testUpdateTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "target" has an invalid state. Only draft targets can be updated.');

        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteTarget
     */
    public function testDeleteTarget(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "5104e4e7-1a20-5db8-8857-5ab99f1290b9"');

        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $this->layoutResolverService->deleteTarget($target);

        $this->layoutResolverService->loadTargetDraft($target->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteTarget
     */
    public function testDeleteTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "target" has an invalid state. Only draft targets can be deleted.');

        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $this->layoutResolverService->deleteTarget($target);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddCondition(): void
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1'
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $createdCondition = $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );

        self::assertTrue($createdCondition->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addCondition
     */
    public function testAddConditionThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Conditions can be added only to draft rules.');

        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1'
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateCondition(): void
    {
        $condition = $this->layoutResolverService->loadConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);

        self::assertTrue($updatedCondition->isDraft());
        self::assertSame('new_value', $updatedCondition->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateCondition
     */
    public function testUpdateConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be updated.');

        $condition = $this->layoutResolverService->loadCondition(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateCondition($condition, $conditionUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteCondition
     */
    public function testDeleteCondition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "7db46c94-3139-5a3d-9b2a-b2d28e7573ca"');

        $condition = $this->layoutResolverService->loadConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));
        $this->layoutResolverService->deleteCondition($condition);

        $this->layoutResolverService->loadConditionDraft($condition->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteCondition
     */
    public function testDeleteConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be deleted.');

        $condition = $this->layoutResolverService->loadCondition(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));
        $this->layoutResolverService->deleteCondition($condition);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleCreateStruct();

        self::assertSame(
            [
                'layoutId' => null,
                'priority' => null,
                'enabled' => true,
                'comment' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleUpdateStruct();

        self::assertSame(
            [
                'layoutId' => null,
                'comment' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();

        self::assertSame(
            [
                'priority' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newTargetCreateStruct('target');

        self::assertSame(
            [
                'type' => 'target',
                'value' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newTargetUpdateStruct();

        self::assertSame(
            [
                'value' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newConditionCreateStruct('condition');

        self::assertSame(
            [
                'type' => 'condition',
                'value' => null,
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newConditionUpdateStruct();

        self::assertSame(
            [
                'value' => null,
            ],
            $this->exportObject($struct)
        );
    }
}
