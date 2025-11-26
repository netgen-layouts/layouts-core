<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[CoversClass(LayoutResolverStructBuilder::class)]
abstract class LayoutResolverServiceTestBase extends CoreTestCase
{
    use ExportObjectTrait;
    use UuidGeneratorTrait;

    public function testLoadRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));

        self::assertTrue($rule->isPublished());
    }

    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRule(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleDraft(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        self::assertTrue($rule->isDraft());
    }

    public function testLoadRuleDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleArchive(): void
    {
        $ruleDraft = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $this->layoutResolverService->publishRule($ruleDraft);

        $ruleArchive = $this->layoutResolverService->loadRuleArchive(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        self::assertTrue($ruleArchive->isArchived());
    }

    public function testLoadRuleArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleArchive(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroup->isPublished());
    }

    public function testLoadRuleGroupThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroup(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleGroupDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroup->isDraft());
    }

    public function testLoadRuleGroupDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleGroupArchive(): void
    {
        $ruleGroupDraft = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->publishRuleGroup($ruleGroupDraft);

        $ruleGroupArchive = $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroupArchive->isArchived());
    }

    public function testLoadRuleGroupArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRulesForLayout(): void
    {
        $rules = $this->layoutResolverService->loadRulesForLayout(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertCount(2, $rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    public function testLoadRulesForLayoutThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only published layouts can be used in rules.');

        $this->layoutResolverService->loadRulesForLayout(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );
    }

    public function testGetRuleCountForLayout(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCountForLayout(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertSame(2, $ruleCount);
    }

    public function testGetRuleCountForLayoutThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only published layouts can be used in rules.');

        $this->layoutResolverService->getRuleCountForLayout(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );
    }

    public function testLoadRulesFromGroup(): void
    {
        $rules = $this->layoutResolverService->loadRulesFromGroup(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertCount(2, $rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    public function testLoadRulesFromGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rules can be loaded only from published rule groups.');

        $this->layoutResolverService->loadRulesFromGroup(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testGetRuleCountFromGroup(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCountFromGroup(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame(2, $ruleCount);
    }

    public function testGetRuleCountFromGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule count can be fetched only for published rule groups.');

        $this->layoutResolverService->getRuleCountFromGroup(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testLoadRuleGroups(): void
    {
        $ruleGroups = $this->layoutResolverService->loadRuleGroups(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertCount(1, $ruleGroups);

        foreach ($ruleGroups as $ruleGroup) {
            self::assertTrue($ruleGroup->isPublished());
        }
    }

    public function testLoadRuleGroupsThrowsBadStateExceptionWithNonPublishedParentGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "parentGroup" has an invalid state. Rule groups can be loaded only from published parent groups.');

        $this->layoutResolverService->loadRuleGroups(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testGetRuleGroupCount(): void
    {
        $ruleGroupCount = $this->layoutResolverService->getRuleGroupCount(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame(1, $ruleGroupCount);
    }

    public function testGetRuleGroupCountThrowsBadStateExceptionWithNonPublishedParentGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "parentGroup" has an invalid state. Rule group count can be fetched only for published parent groups.');

        $this->layoutResolverService->getRuleGroupCount(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testMatchRules(): void
    {
        $rules = $this->layoutResolverService->matchRules(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID)),
            'route',
            'my_cool_route',
        );

        self::assertNotEmpty($rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    public function testLoadTarget(): void
    {
        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5f086fc4-4e1c-55eb-ae54-79fc296cda37'));

        self::assertTrue($target->isPublished());
    }

    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadTarget(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadTargetDraft(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        self::assertTrue($target->isDraft());
    }

    public function testLoadTargetDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadTargetDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleCondition(Uuid::fromString('35f4594c-6674-5815-add6-07f288b79686'));

        self::assertTrue($condition->isPublished());
    }

    public function testLoadRuleConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleCondition(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleConditionDraft(): void
    {
        $condition = $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        self::assertTrue($condition->isDraft());
    }

    public function testLoadRuleConditionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleGroupCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        self::assertTrue($condition->isPublished());
    }

    public function testLoadRuleGroupConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testLoadRuleGroupConditionDraft(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        self::assertTrue($condition->isDraft());
    }

    public function testLoadRuleGroupConditionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    public function testRuleExists(): void
    {
        self::assertTrue($this->layoutResolverService->ruleExists(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634')));
    }

    public function testRuleExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutResolverService->ruleExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    public function testCreateRule(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->ruleGroupId->toString());
        self::assertTrue($createdRule->isDraft());
    }

    public function testCreateRuleWithCustomUuid(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->uuid = Uuid::fromString('0f714915-eef0-4dc1-b22b-1107cb1ab92b');

        $createdRule = $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRule->isDraft());
        self::assertSame($ruleCreateStruct->uuid->toString(), $createdRule->id->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->ruleGroupId->toString());
    }

    public function testCreateRuleWithAssignedLayout(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->layoutId = Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136');

        $createdRule = $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRule->isDraft());
        self::assertInstanceOf(Layout::class, $createdRule->getLayout());
        self::assertSame($ruleCreateStruct->layoutId->toString(), $createdRule->getLayout()->id->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->ruleGroupId->toString());
    }

    public function testCreateRuleThrowsBadStateExceptionWithExistingUuid(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule with provided UUID already exists.');

        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->uuid = Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634');

        $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testCreateRuleThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rules can be created only in published groups.');

        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->uuid = Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634');

        $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testUpdateRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9');
        $ruleUpdateStruct->description = 'Updated description';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertInstanceOf(Layout::class, $updatedRule->getLayout());
        self::assertTrue($updatedRule->getLayout()->isPublished());
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $updatedRule->getLayout()->id->toString());
        self::assertSame('Updated description', $updatedRule->description);
    }

    public function testUpdateRuleWithNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->description = 'Updated description';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertInstanceOf(Layout::class, $updatedRule->getLayout());
        self::assertTrue($updatedRule->getLayout()->isPublished());
        self::assertSame('71cbe281-430c-51d5-8e21-c3cc4e656dac', $updatedRule->getLayout()->id->toString());
        self::assertSame('Updated description', $updatedRule->description);
    }

    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;
        $ruleUpdateStruct->description = 'Updated description';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertNull($updatedRule->getLayout());
        self::assertSame('Updated description', $updatedRule->description);
    }

    public function testUpdateRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be updated.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = Uuid::fromString('d8e55af7-cf62-5f28-ae15-331b457d82e9');
        $ruleUpdateStruct->description = 'Updated description';

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);
    }

    public function testUpdateRuleMetadata(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $struct->priority = 50;

        $updatedRule = $this->layoutResolverService->updateRuleMetadata(
            $rule,
            $struct,
        );

        self::assertSame(50, $updatedRule->priority);
        self::assertTrue($updatedRule->isPublished());
    }

    public function testUpdateRuleMetadataThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Metadata can be updated only for published rules.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $struct->priority = 50;

        $this->layoutResolverService->updateRuleMetadata($rule, $struct);
    }

    public function testCopyRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('4f63660c-bd58-5efa-81a8-6c81b4484a61'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $copiedRule = $this->layoutResolverService->copyRule($rule, $targetGroup);

        self::assertSame($rule->isPublished(), $copiedRule->isPublished());
        self::assertSame($targetGroup->id->toString(), $copiedRule->ruleGroupId->toString());
        self::assertNotSame($rule->id->toString(), $copiedRule->id->toString());
    }

    public function testCopyRuleToDifferentRuleGroup(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRule = $this->layoutResolverService->copyRule($rule, $targetGroup);

        self::assertSame($rule->isPublished(), $copiedRule->isPublished());
        self::assertSame($targetGroup->id->toString(), $copiedRule->ruleGroupId->toString());
        self::assertNotSame($rule->id->toString(), $copiedRule->id->toString());
    }

    public function testCopyRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be copied.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $this->layoutResolverService->copyRule($rule, $targetGroup);
    }

    public function testCopyRuleThrowsBadStateExceptionWithNonPublishedTargetGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rules can be copied only to published groups.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRule($rule, $targetGroup);
    }

    public function testMoveRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRule = $this->layoutResolverService->moveRule($rule, $targetGroup);

        self::assertTrue($movedRule->isPublished());
        self::assertSame($rule->id->toString(), $movedRule->id->toString());
        self::assertSame($rule->priority, $movedRule->priority);
        self::assertSame($targetGroup->id->toString(), $movedRule->ruleGroupId->toString());
    }

    public function testMoveRuleWithNewPriority(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRule = $this->layoutResolverService->moveRule($rule, $targetGroup, 42);

        self::assertTrue($movedRule->isPublished());
        self::assertSame($rule->id->toString(), $movedRule->id->toString());
        self::assertSame(42, $movedRule->priority);
        self::assertSame($targetGroup->id->toString(), $movedRule->ruleGroupId->toString());
    }

    public function testMoveRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be moved.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRule($rule, $targetGroup);
    }

    public function testMoveRuleThrowsBadStateExceptionWithNonPublishedTargetRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rules can be moved only to published groups.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRule($rule, $targetGroup);
    }

    public function testCreateRuleDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));

        $draftRule = $this->layoutResolverService->createRuleDraft($rule);

        self::assertTrue($draftRule->isDraft());
    }

    public function testCreateRuleDraftWithDiscardingExistingDraft(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));
        $this->layoutResolverService->createRuleDraft($rule);

        $draftRule = $this->layoutResolverService->createRuleDraft($rule, true);

        self::assertTrue($draftRule->isDraft());
    }

    public function testCreateRuleDraftThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Drafts can only be created from published rules.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->createRuleDraft($rule);
    }

    public function testCreateRuleDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. The provided rule already has a draft.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('23eece92-8cce-5155-9fef-58fb5e3decd6'));
        $this->layoutResolverService->createRuleDraft($rule);

        $this->layoutResolverService->createRuleDraft($rule);
    }

    public function testDiscardRuleDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "de086bdf-0014-5f4f-89e4-fc0aff21da90"');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->discardRuleDraft($rule);

        $this->layoutResolverService->loadRuleDraft($rule->id);
    }

    public function testDiscardRuleDraftThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be discarded.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->discardRuleDraft($rule);
    }

    public function testPublishRule(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $publishedRule = $this->layoutResolverService->publishRule($rule);

        self::assertTrue($publishedRule->isPublished());
        self::assertTrue($publishedRule->enabled);

        try {
            $this->layoutResolverService->loadRuleDraft($rule->id);
            self::fail('Draft rule still exists after publishing.');
        } catch (NotFoundException) {
            // Do nothing
        }
    }

    public function testPublishRuleThrowsBadStateExceptionWithNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only draft rules can be published.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $this->layoutResolverService->publishRule($rule);
    }

    public function testRestoreRuleFromArchive(): void
    {
        $restoredRule = $this->layoutResolverService->restoreRuleFromArchive(
            $this->layoutResolverService->loadRuleArchive(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')),
        );

        self::assertTrue($restoredRule->isDraft());
    }

    public function testRestoreRuleFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived rules can be restored.');

        $this->layoutResolverService->restoreRuleFromArchive(
            $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')),
        );
    }

    public function testDeleteRule(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "de086bdf-0014-5f4f-89e4-fc0aff21da90"');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->deleteRule($rule);

        $this->layoutResolverService->loadRule($rule->id);
    }

    public function testRuleGroupExists(): void
    {
        self::assertTrue($this->layoutResolverService->ruleGroupExists(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')));
    }

    public function testRuleGroupExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutResolverService->ruleGroupExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    public function testCreateRuleGroup(): void
    {
        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');

        $createdRuleGroup = $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRuleGroup->isDraft());
        self::assertInstanceOf(UuidInterface::class, $createdRuleGroup->parentId);
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRuleGroup->parentId->toString());
    }

    public function testCreateRuleGroupWithCustomUuid(): void
    {
        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');
        $ruleGroupCreateStruct->uuid = Uuid::fromString('0f714915-eef0-4dc1-b22b-1107cb1ab92b');

        $createdRuleGroup = $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRuleGroup->isDraft());
        self::assertInstanceOf(UuidInterface::class, $createdRuleGroup->parentId);
        self::assertSame($ruleGroupCreateStruct->uuid->toString(), $createdRuleGroup->id->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRuleGroup->parentId->toString());
    }

    public function testCreateRuleGroupThrowsBadStateExceptionWithExistingUuid(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule group with provided UUID already exists.');

        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');
        $ruleGroupCreateStruct->uuid = Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e');

        $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testCreateRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "parentGroup" has an invalid state. Rule groups can be created only in published groups.');

        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');
        $ruleGroupCreateStruct->uuid = Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e');

        $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testUpdateRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $ruleGroupUpdateStruct = $this->layoutResolverService->newRuleGroupUpdateStruct();
        $ruleGroupUpdateStruct->name = 'Updated name';
        $ruleGroupUpdateStruct->description = 'Updated description';

        $updatedRuleGroup = $this->layoutResolverService->updateRuleGroup($ruleGroup, $ruleGroupUpdateStruct);

        self::assertTrue($updatedRuleGroup->isDraft());
        self::assertSame('Updated description', $updatedRuleGroup->description);
    }

    public function testUpdateRuleGroupThrowsBadStateExceptionWithNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only draft rule groups can be updated.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $ruleGroupUpdateStruct = $this->layoutResolverService->newRuleGroupUpdateStruct();
        $ruleGroupUpdateStruct->name = 'Updated name';
        $ruleGroupUpdateStruct->description = 'Updated description';

        $this->layoutResolverService->updateRuleGroup($ruleGroup, $ruleGroupUpdateStruct);
    }

    public function testUpdateRuleGroupMetadata(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $struct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();
        $struct->priority = 50;

        $updatedRuleGroup = $this->layoutResolverService->updateRuleGroupMetadata(
            $ruleGroup,
            $struct,
        );

        self::assertSame(50, $updatedRuleGroup->priority);
        self::assertTrue($updatedRuleGroup->isPublished());
    }

    public function testUpdateRuleGroupMetadataThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Metadata can be updated only for published rule groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $struct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();
        $struct->priority = 50;

        $this->layoutResolverService->updateRuleGroupMetadata($ruleGroup, $struct);
    }

    public function testCopyRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRuleGroup = $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->isPublished(), $copiedRuleGroup->isPublished());
        self::assertInstanceOf(UuidInterface::class, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->id->toString(), $copiedRuleGroup->parentId->toString());
        self::assertNotSame($ruleGroup->id->toString(), $copiedRuleGroup->id->toString());
    }

    public function testCopyRuleGroupToDifferentRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRuleGroup = $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->isPublished(), $copiedRuleGroup->isPublished());
        self::assertInstanceOf(UuidInterface::class, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->id->toString(), $copiedRuleGroup->parentId->toString());
        self::assertNotSame($ruleGroup->id->toString(), $copiedRuleGroup->id->toString());
    }

    public function testCopyRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be copied.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);
    }

    public function testCopyRuleGroupThrowsBadStateExceptionWithNonPublishedTargetGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rule groups can be copied only to published groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);
    }

    public function testMoveRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRuleGroup = $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);

        self::assertTrue($movedRuleGroup->isPublished());
        self::assertSame($ruleGroup->id->toString(), $movedRuleGroup->id->toString());
        self::assertSame($ruleGroup->priority, $movedRuleGroup->priority);
        self::assertInstanceOf(UuidInterface::class, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->id->toString(), $movedRuleGroup->parentId->toString());
    }

    public function testMoveRuleGroupWithNewPriority(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRuleGroup = $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup, 42);

        self::assertTrue($movedRuleGroup->isPublished());
        self::assertSame($ruleGroup->id->toString(), $movedRuleGroup->id->toString());
        self::assertSame(42, $movedRuleGroup->priority);
        self::assertInstanceOf(UuidInterface::class, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->id->toString(), $movedRuleGroup->parentId->toString());
    }

    public function testMoveRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be moved.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);
    }

    public function testMoveRuleGroupThrowsBadStateExceptionWithNonPublishedTargetRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rule groups can be moved only to published groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);
    }

    public function testCreateRuleGroupDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $draftRuleGroup = $this->layoutResolverService->createRuleGroupDraft($ruleGroup);

        self::assertTrue($draftRuleGroup->isDraft());
    }

    public function testCreateRuleGroupDraftWithDiscardingExistingDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $draftRuleGroup = $this->layoutResolverService->createRuleGroupDraft($ruleGroup, true);

        self::assertTrue($draftRuleGroup->isDraft());
    }

    public function testCreateRuleGroupDraftThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Drafts can only be created from published rule groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);
    }

    public function testCreateRuleGroupDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. The provided rule group already has a draft.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);

        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);
    }

    public function testDiscardRuleGroupDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "b4f85f38-de3f-4af7-9a5f-21df63a49da9"');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->discardRuleGroupDraft($ruleGroup);

        $this->layoutResolverService->loadRuleGroupDraft($ruleGroup->id);
    }

    public function testDiscardRuleGroupDraftThrowsBadStateExceptionWithNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only draft rule groups can be discarded.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->discardRuleGroupDraft($ruleGroup);
    }

    public function testPublishRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $publishedRuleGroup = $this->layoutResolverService->publishRuleGroup($ruleGroup);

        self::assertTrue($publishedRuleGroup->isPublished());
        self::assertTrue($publishedRuleGroup->enabled);

        try {
            $this->layoutResolverService->loadRuleGroupDraft($ruleGroup->id);
            self::fail('Draft rule group still exists after publishing.');
        } catch (NotFoundException) {
            // Do nothing
        }
    }

    public function testPublishRuleGroupThrowsBadStateExceptionWithNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only draft rule groups can be published.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->publishRuleGroup($ruleGroup);
    }

    public function testRestoreRuleGroupFromArchive(): void
    {
        $restoredRuleGroup = $this->layoutResolverService->restoreRuleGroupFromArchive(
            $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399')),
        );

        self::assertTrue($restoredRuleGroup->isDraft());
    }

    public function testRestoreRuleGroupFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived rule groups can be restored.');

        $this->layoutResolverService->restoreRuleGroupFromArchive(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    public function testDeleteRuleGroup(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "b4f85f38-de3f-4af7-9a5f-21df63a49da9"');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->deleteRuleGroup($ruleGroup);

        $this->layoutResolverService->loadRuleGroup($ruleGroup->id);
    }

    public function testEnableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        self::assertTrue($enabledRule->enabled);
        self::assertTrue($enabledRule->isPublished());
    }

    public function testEnableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be enabled.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->enableRule($rule);
    }

    public function testEnableRuleThrowsBadStateExceptionIfRuleIsAlreadyEnabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule is already enabled.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634'));

        $this->layoutResolverService->enableRule($rule);
    }

    public function testDisableRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634'));

        $disabledRule = $this->layoutResolverService->disableRule($rule);

        self::assertFalse($disabledRule->enabled);
        self::assertTrue($disabledRule->isPublished());
    }

    public function testDisableRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be disabled.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));

        $this->layoutResolverService->disableRule($rule);
    }

    public function testDisableRuleThrowsBadStateExceptionIfRuleIsAlreadyDisabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule is already disabled.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $this->layoutResolverService->disableRule($rule);
    }

    public function testEnableRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $enabledRuleGroup = $this->layoutResolverService->enableRuleGroup($ruleGroup);

        self::assertTrue($enabledRuleGroup->enabled);
        self::assertTrue($enabledRuleGroup->isPublished());
    }

    public function testEnableRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be enabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $this->layoutResolverService->enableRuleGroup($ruleGroup);
    }

    public function testEnableRuleGroupThrowsBadStateExceptionIfRuleGroupIsAlreadyEnabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule group is already enabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->enableRuleGroup($ruleGroup);
    }

    public function testDisableRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $disabledRuleGroup = $this->layoutResolverService->disableRuleGroup($ruleGroup);

        self::assertFalse($disabledRuleGroup->enabled);
        self::assertTrue($disabledRuleGroup->isPublished());
    }

    public function testDisableRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be disabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->disableRuleGroup($ruleGroup);
    }

    public function testDisableRuleGroupThrowsBadStateExceptionIfRuleGroupIsAlreadyDisabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule group is already disabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $this->layoutResolverService->disableRuleGroup($ruleGroup);
    }

    public function testAddTarget(): void
    {
        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix',
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $createdTarget = $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct,
        );

        self::assertTrue($createdTarget->isDraft());
    }

    public function testAddTargetThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Targets can be added only to draft rules.');

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route_prefix',
        );

        $targetCreateStruct->value = 'some_route_';

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct,
        );
    }

    public function testAddTargetOfDifferentKindThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Rule with UUID "de086bdf-0014-5f4f-89e4-fc0aff21da90" only accepts targets with "route_prefix" target type.');

        $targetCreateStruct = $this->layoutResolverService->newTargetCreateStruct(
            'route',
        );

        $targetCreateStruct->value = 'some_route';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addTarget(
            $rule,
            $targetCreateStruct,
        );
    }

    public function testUpdateTarget(): void
    {
        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $updatedTarget = $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);

        self::assertTrue($updatedTarget->isDraft());
        self::assertSame('new_value', $updatedTarget->value);
    }

    public function testUpdateTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "target" has an invalid state. Only draft targets can be updated.');

        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $targetUpdateStruct = $this->layoutResolverService->newTargetUpdateStruct();
        $targetUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateTarget($target, $targetUpdateStruct);
    }

    public function testDeleteTarget(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "5104e4e7-1a20-5db8-8857-5ab99f1290b9"');

        $target = $this->layoutResolverService->loadTargetDraft(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $this->layoutResolverService->deleteTarget($target);

        $this->layoutResolverService->loadTargetDraft($target->id);
    }

    public function testDeleteTargetThrowsBadStateExceptionOnNonDraftTarget(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "target" has an invalid state. Only draft targets can be deleted.');

        $target = $this->layoutResolverService->loadTarget(Uuid::fromString('5104e4e7-1a20-5db8-8857-5ab99f1290b9'));

        $this->layoutResolverService->deleteTarget($target);
    }

    public function testAddRuleCondition(): void
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $createdCondition = $this->layoutResolverService->addRuleCondition(
            $rule,
            $conditionCreateStruct,
        );

        self::assertTrue($createdCondition->isDraft());
    }

    public function testAddRuleConditionThrowsBadStateExceptionOnNonDraftRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Conditions can be added only to draft rules.');

        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addRuleCondition(
            $rule,
            $conditionCreateStruct,
        );
    }

    public function testAddRuleGroupCondition(): void
    {
        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $createdCondition = $this->layoutResolverService->addRuleGroupCondition(
            $ruleGroup,
            $conditionCreateStruct,
        );

        self::assertTrue($createdCondition->isDraft());
    }

    public function testAddRuleGroupConditionThrowsBadStateExceptionOnNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Conditions can be added only to draft rule groups.');

        $conditionCreateStruct = $this->layoutResolverService->newConditionCreateStruct(
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->addRuleGroupCondition(
            $ruleGroup,
            $conditionCreateStruct,
        );
    }

    public function testUpdateRuleCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateRuleCondition($condition, $conditionUpdateStruct);

        self::assertTrue($updatedCondition->isDraft());
        self::assertSame('new_value', $updatedCondition->value);
    }

    public function testUpdateRuleConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be updated.');

        $condition = $this->layoutResolverService->loadRuleCondition(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateRuleCondition($condition, $conditionUpdateStruct);
    }

    public function testUpdateRuleGroupCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateRuleGroupCondition($condition, $conditionUpdateStruct);

        self::assertTrue($updatedCondition->isDraft());
        self::assertSame('new_value', $updatedCondition->value);
    }

    public function testUpdateRuleGroupConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be updated.');

        $condition = $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateRuleGroupCondition($condition, $conditionUpdateStruct);
    }

    public function testDeleteCondition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "7db46c94-3139-5a3d-9b2a-b2d28e7573ca"');

        $condition = $this->layoutResolverService->loadRuleConditionDraft(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));
        $this->layoutResolverService->deleteCondition($condition);

        $this->layoutResolverService->loadRuleConditionDraft($condition->id);
    }

    public function testDeleteConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be deleted.');

        $condition = $this->layoutResolverService->loadRuleCondition(Uuid::fromString('7db46c94-3139-5a3d-9b2a-b2d28e7573ca'));
        $this->layoutResolverService->deleteCondition($condition);
    }

    public function testNewRuleCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleCreateStruct();

        self::assertSame(
            [
                'description' => '',
                'enabled' => true,
                'layoutId' => null,
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'layoutId' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleMetadataUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();

        self::assertSame(
            [
                'priority' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleGroupCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');

        self::assertSame(
            [
                'description' => '',
                'enabled' => true,
                'name' => 'Test group',
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleGroupUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleGroupUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleGroupMetadataUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();

        self::assertSame(
            [
                'priority' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewTargetCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newTargetCreateStruct('target');

        self::assertSame(
            [
                'type' => 'target',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewTargetUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newTargetUpdateStruct();

        self::assertSame([], $this->exportObject($struct));
    }

    public function testNewConditionCreateStruct(): void
    {
        $struct = $this->layoutResolverService->newConditionCreateStruct('condition');

        self::assertSame(
            [
                'type' => 'condition',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewConditionUpdateStruct(): void
    {
        $struct = $this->layoutResolverService->newConditionUpdateStruct();

        self::assertSame([], $this->exportObject($struct));
    }
}
