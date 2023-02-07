<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Service;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class LayoutResolverServiceTestBase extends CoreTestCase
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
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroup
     */
    public function testLoadRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroup->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroup
     */
    public function testLoadRuleGroupThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroup(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::__construct
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupDraft
     */
    public function testLoadRuleGroupDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroup->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupDraft
     */
    public function testLoadRuleGroupDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupArchive
     */
    public function testLoadRuleGroupArchive(): void
    {
        $ruleGroupDraft = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->publishRuleGroup($ruleGroupDraft);

        $ruleGroupArchive = $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        self::assertTrue($ruleGroupArchive->isArchived());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupArchive
     */
    public function testLoadRuleGroupArchiveThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRules(): void
    {
        $rules = $this->layoutResolverService->loadRules();

        self::assertCount(8, $rules);

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
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );

        self::assertCount(2, $rules);

        foreach ($rules as $rule) {
            self::assertTrue($rule->isPublished());
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRules
     */
    public function testLoadRulesThrowsBadStateExceptionWithNonPublishedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "layout" has an invalid state. Only published layouts can be used in rules.');

        $this->layoutResolverService->loadRules(
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCount(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount();

        self::assertSame(8, $ruleCount);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCount
     */
    public function testGetRuleCountWithLayout(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCount(
            $this->layoutService->loadLayout(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
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
            $this->layoutService->loadLayoutDraft(Uuid::fromString('81168ed3-86f9-55ea-b153-101f96f2c136')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRulesFromGroup
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRulesFromGroup
     */
    public function testLoadRulesFromGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rules can be loaded only from published rule groups.');

        $this->layoutResolverService->loadRulesFromGroup(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCountFromGroup
     */
    public function testGetRuleCountFromGroup(): void
    {
        $ruleCount = $this->layoutResolverService->getRuleCountFromGroup(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame(2, $ruleCount);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleCountFromGroup
     */
    public function testGetRuleCountFromGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule count can be fetched only for published rule groups.');

        $this->layoutResolverService->getRuleCountFromGroup(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroups
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroups
     */
    public function testLoadRuleGroupsThrowsBadStateExceptionWithNonPublishedParentGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "parentGroup" has an invalid state. Rule groups can be loaded only from published parent groups.');

        $this->layoutResolverService->loadRuleGroups(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleGroupCount
     */
    public function testGetRuleGroupCount(): void
    {
        $ruleGroupCount = $this->layoutResolverService->getRuleGroupCount(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame(1, $ruleGroupCount);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::getRuleGroupCount
     */
    public function testGetRuleGroupCountThrowsBadStateExceptionWithNonPublishedParentGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "parentGroup" has an invalid state. Rule group count can be fetched only for published parent groups.');

        $this->layoutResolverService->getRuleGroupCount(
            $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::matchRules
     */
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
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupCondition
     */
    public function testLoadRuleGroupCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        self::assertTrue($condition->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupCondition
     */
    public function testLoadRuleGroupConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupConditionDraft
     */
    public function testLoadRuleGroupConditionDraft(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        self::assertTrue($condition->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::loadRuleGroupConditionDraft
     */
    public function testLoadRuleGroupConditionDraftThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "ffffffff-ffff-ffff-ffff-ffffffffffff"');

        $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff'));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::ruleExists
     */
    public function testRuleExists(): void
    {
        self::assertTrue($this->layoutResolverService->ruleExists(Uuid::fromString('26768324-03dd-5952-8a55-4b449d6cd634')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::ruleExists
     */
    public function testRuleExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutResolverService->ruleExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRule(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();

        $createdRule = $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->getRuleGroupId()->toString());
        self::assertTrue($createdRule->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
    public function testCreateRuleWithCustomUuid(): void
    {
        $ruleCreateStruct = $this->layoutResolverService->newRuleCreateStruct();
        $ruleCreateStruct->uuid = Uuid::fromString('0f714915-eef0-4dc1-b22b-1107cb1ab92b');

        $createdRule = $this->layoutResolverService->createRule(
            $ruleCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRule->isDraft());
        self::assertSame($ruleCreateStruct->uuid->toString(), $createdRule->getId()->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->getRuleGroupId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
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
        self::assertSame($ruleCreateStruct->layoutId->toString(), $createdRule->getLayout()->getId()->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRule->getRuleGroupId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRule
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
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
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $updatedRule->getLayout()->getId()->toString());
        self::assertSame('Updated description', $updatedRule->getDescription());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithNoLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->description = 'Updated description';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertInstanceOf(Layout::class, $updatedRule->getLayout());
        self::assertTrue($updatedRule->getLayout()->isPublished());
        self::assertSame('71cbe281-430c-51d5-8e21-c3cc4e656dac', $updatedRule->getLayout()->getId()->toString());
        self::assertSame('Updated description', $updatedRule->getDescription());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRule
     */
    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;
        $ruleUpdateStruct->description = 'Updated description';

        $updatedRule = $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        self::assertTrue($updatedRule->isDraft());
        self::assertNull($updatedRule->getLayout());
        self::assertSame('Updated description', $updatedRule->getDescription());
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
        $ruleUpdateStruct->description = 'Updated description';

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleMetadata
     */
    public function testUpdateRuleMetadata(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('d5bcbdfc-2e75-5f06-8c47-c26d68bb7b5e'));

        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $struct->priority = 50;

        $updatedRule = $this->layoutResolverService->updateRuleMetadata(
            $rule,
            $struct,
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

        $struct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $struct->priority = 50;

        $this->layoutResolverService->updateRuleMetadata($rule, $struct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('4f63660c-bd58-5efa-81a8-6c81b4484a61'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $copiedRule = $this->layoutResolverService->copyRule($rule, $targetGroup);

        self::assertSame($rule->isPublished(), $copiedRule->isPublished());
        self::assertSame($targetGroup->getId()->toString(), $copiedRule->getRuleGroupId()->toString());
        self::assertNotSame($rule->getId()->toString(), $copiedRule->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRuleToDifferentRuleGroup(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRule = $this->layoutResolverService->copyRule($rule, $targetGroup);

        self::assertSame($rule->isPublished(), $copiedRule->isPublished());
        self::assertSame($targetGroup->getId()->toString(), $copiedRule->getRuleGroupId()->toString());
        self::assertNotSame($rule->getId()->toString(), $copiedRule->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be copied.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $this->layoutResolverService->copyRule($rule, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRule
     */
    public function testCopyRuleThrowsBadStateExceptionWithNonPublishedTargetGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rules can be copied only to published groups.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('816c00bb-8253-5bba-a067-ba6de1f94a65'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRule($rule, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRule
     */
    public function testMoveRule(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRule = $this->layoutResolverService->moveRule($rule, $targetGroup);

        self::assertTrue($movedRule->isPublished());
        self::assertSame($rule->getId()->toString(), $movedRule->getId()->toString());
        self::assertSame($rule->getPriority(), $movedRule->getPriority());
        self::assertSame($targetGroup->getId()->toString(), $movedRule->getRuleGroupId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRule
     */
    public function testMoveRuleWithNewPriority(): void
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRule = $this->layoutResolverService->moveRule($rule, $targetGroup, 42);

        self::assertTrue($movedRule->isPublished());
        self::assertSame($rule->getId()->toString(), $movedRule->getId()->toString());
        self::assertSame(42, $movedRule->getPriority());
        self::assertSame($targetGroup->getId()->toString(), $movedRule->getRuleGroupId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRule
     */
    public function testMoveRuleThrowsBadStateExceptionWithNonPublishedRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "rule" has an invalid state. Only published rules can be moved.');

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRule($rule, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRule
     */
    public function testMoveRuleThrowsBadStateExceptionWithNonPublishedTargetRule(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rules can be moved only to published groups.');

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRule($rule, $targetGroup);
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
            $this->layoutResolverService->loadRuleArchive(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')),
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
            $this->layoutResolverService->loadRule(Uuid::fromString('55622437-f700-5378-99c9-7dafe89a8fb6')),
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
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::ruleGroupExists
     */
    public function testRuleGroupExists(): void
    {
        self::assertTrue($this->layoutResolverService->ruleGroupExists(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::ruleGroupExists
     */
    public function testRuleGroupExistsReturnsFalse(): void
    {
        self::assertFalse($this->layoutResolverService->ruleGroupExists(Uuid::fromString('ffffffff-ffff-ffff-ffff-ffffffffffff')));
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroup
     */
    public function testCreateRuleGroup(): void
    {
        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');

        $createdRuleGroup = $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRuleGroup->isDraft());
        self::assertInstanceOf(UuidInterface::class, $createdRuleGroup->getParentId());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRuleGroup->getParentId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroup
     */
    public function testCreateRuleGroupWithCustomUuid(): void
    {
        $ruleGroupCreateStruct = $this->layoutResolverService->newRuleGroupCreateStruct('Test group');
        $ruleGroupCreateStruct->uuid = Uuid::fromString('0f714915-eef0-4dc1-b22b-1107cb1ab92b');

        $createdRuleGroup = $this->layoutResolverService->createRuleGroup(
            $ruleGroupCreateStruct,
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );

        self::assertTrue($createdRuleGroup->isDraft());
        self::assertInstanceOf(UuidInterface::class, $createdRuleGroup->getParentId());
        self::assertSame($ruleGroupCreateStruct->uuid->toString(), $createdRuleGroup->getId()->toString());
        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $createdRuleGroup->getParentId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroup
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroup
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroup
     */
    public function testUpdateRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $ruleGroupUpdateStruct = $this->layoutResolverService->newRuleGroupUpdateStruct();
        $ruleGroupUpdateStruct->name = 'Updated name';
        $ruleGroupUpdateStruct->description = 'Updated description';

        $updatedRuleGroup = $this->layoutResolverService->updateRuleGroup($ruleGroup, $ruleGroupUpdateStruct);

        self::assertTrue($updatedRuleGroup->isDraft());
        self::assertSame('Updated description', $updatedRuleGroup->getDescription());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroup
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroupMetadata
     */
    public function testUpdateRuleGroupMetadata(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $struct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();
        $struct->priority = 50;

        $updatedRuleGroup = $this->layoutResolverService->updateRuleGroupMetadata(
            $ruleGroup,
            $struct,
        );

        self::assertSame(50, $updatedRuleGroup->getPriority());
        self::assertTrue($updatedRuleGroup->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroupMetadata
     */
    public function testUpdateRuleGroupMetadataThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Metadata can be updated only for published rule groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $struct = $this->layoutResolverService->newRuleGroupMetadataUpdateStruct();
        $struct->priority = 50;

        $this->layoutResolverService->updateRuleGroupMetadata($ruleGroup, $struct);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRuleGroup
     */
    public function testCopyRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRuleGroup = $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->isPublished(), $copiedRuleGroup->isPublished());
        self::assertInstanceOf(UuidInterface::class, $copiedRuleGroup->getParentId());
        self::assertSame($targetGroup->getId()->toString(), $copiedRuleGroup->getParentId()->toString());
        self::assertNotSame($ruleGroup->getId()->toString(), $copiedRuleGroup->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRuleGroup
     */
    public function testCopyRuleGroupToDifferentRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $copiedRuleGroup = $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->isPublished(), $copiedRuleGroup->isPublished());
        self::assertInstanceOf(UuidInterface::class, $copiedRuleGroup->getParentId());
        self::assertSame($targetGroup->getId()->toString(), $copiedRuleGroup->getParentId()->toString());
        self::assertNotSame($ruleGroup->getId()->toString(), $copiedRuleGroup->getId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRuleGroup
     */
    public function testCopyRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be copied.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::copyRuleGroup
     */
    public function testCopyRuleGroupThrowsBadStateExceptionWithNonPublishedTargetGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rule groups can be copied only to published groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->copyRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRuleGroup
     */
    public function testMoveRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRuleGroup = $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);

        self::assertTrue($movedRuleGroup->isPublished());
        self::assertSame($ruleGroup->getId()->toString(), $movedRuleGroup->getId()->toString());
        self::assertSame($ruleGroup->getPriority(), $movedRuleGroup->getPriority());
        self::assertInstanceOf(UuidInterface::class, $movedRuleGroup->getParentId());
        self::assertSame($targetGroup->getId()->toString(), $movedRuleGroup->getParentId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRuleGroup
     */
    public function testMoveRuleGroupWithNewPriority(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $movedRuleGroup = $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup, 42);

        self::assertTrue($movedRuleGroup->isPublished());
        self::assertSame($ruleGroup->getId()->toString(), $movedRuleGroup->getId()->toString());
        self::assertSame(42, $movedRuleGroup->getPriority());
        self::assertInstanceOf(UuidInterface::class, $movedRuleGroup->getParentId());
        self::assertSame($targetGroup->getId()->toString(), $movedRuleGroup->getParentId()->toString());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRuleGroup
     */
    public function testMoveRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be moved.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::moveRuleGroup
     */
    public function testMoveRuleGroupThrowsBadStateExceptionWithNonPublishedTargetRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "targetGroup" has an invalid state. Rule groups can be moved only to published groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));
        $targetGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->moveRuleGroup($ruleGroup, $targetGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroupDraft
     */
    public function testCreateRuleGroupDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399'));

        $draftRuleGroup = $this->layoutResolverService->createRuleGroupDraft($ruleGroup);

        self::assertTrue($draftRuleGroup->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroupDraft
     */
    public function testCreateRuleGroupDraftWithDiscardingExistingDraft(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $draftRuleGroup = $this->layoutResolverService->createRuleGroupDraft($ruleGroup, true);

        self::assertTrue($draftRuleGroup->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroupDraft
     */
    public function testCreateRuleGroupDraftThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Drafts can only be created from published rule groups.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::createRuleGroupDraft
     */
    public function testCreateRuleGroupDraftThrowsBadStateExceptionIfDraftAlreadyExists(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. The provided rule group already has a draft.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);

        $this->layoutResolverService->createRuleGroupDraft($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::discardRuleGroupDraft
     */
    public function testDiscardRuleGroupDraft(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "b4f85f38-de3f-4af7-9a5f-21df63a49da9"');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->discardRuleGroupDraft($ruleGroup);

        $this->layoutResolverService->loadRuleGroupDraft($ruleGroup->getId());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::discardRuleGroupDraft
     */
    public function testDiscardRuleGroupDraftThrowsBadStateExceptionWithNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only draft rule groups can be discarded.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->discardRuleGroupDraft($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::publishRuleGroup
     */
    public function testPublishRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $publishedRuleGroup = $this->layoutResolverService->publishRuleGroup($ruleGroup);

        self::assertTrue($publishedRuleGroup->isPublished());
        self::assertTrue($publishedRuleGroup->isEnabled());

        try {
            $this->layoutResolverService->loadRuleGroupDraft($ruleGroup->getId());
            self::fail('Draft rule group still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::publishRuleGroup
     */
    public function testPublishRuleGroupThrowsBadStateExceptionWithNonDraftRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only draft rule groups can be published.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));
        $this->layoutResolverService->publishRuleGroup($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::restoreRuleGroupFromArchive
     */
    public function testRestoreRuleGroupFromArchive(): void
    {
        $restoredRuleGroup = $this->layoutResolverService->restoreRuleGroupFromArchive(
            $this->layoutResolverService->loadRuleGroupArchive(Uuid::fromString('91139748-3bf0-4c25-b45c-d3be6596c399')),
        );

        self::assertTrue($restoredRuleGroup->isDraft());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::restoreRuleGroupFromArchive
     */
    public function testRestoreRuleGroupFromArchiveThrowsBadStateExceptionOnNonArchivedLayout(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Only archived rule groups can be restored.');

        $this->layoutResolverService->restoreRuleGroupFromArchive(
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9')),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::deleteRuleGroup
     */
    public function testDeleteRuleGroup(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "b4f85f38-de3f-4af7-9a5f-21df63a49da9"');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->deleteRuleGroup($ruleGroup);

        $this->layoutResolverService->loadRuleGroup($ruleGroup->getId());
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
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRuleGroup
     */
    public function testEnableRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $enabledRuleGroup = $this->layoutResolverService->enableRuleGroup($ruleGroup);

        self::assertTrue($enabledRuleGroup->isEnabled());
        self::assertTrue($enabledRuleGroup->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRuleGroup
     */
    public function testEnableRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be enabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $this->layoutResolverService->enableRuleGroup($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::enableRuleGroup
     */
    public function testEnableRuleGroupThrowsBadStateExceptionIfRuleGroupIsAlreadyEnabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule group is already enabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->enableRuleGroup($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRuleGroup
     */
    public function testDisableRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $disabledRuleGroup = $this->layoutResolverService->disableRuleGroup($ruleGroup);

        self::assertFalse($disabledRuleGroup->isEnabled());
        self::assertTrue($disabledRuleGroup->isPublished());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRuleGroup
     */
    public function testDisableRuleGroupThrowsBadStateExceptionWithNonPublishedRuleGroup(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Only published rule groups can be disabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroupDraft(Uuid::fromString('b4f85f38-de3f-4af7-9a5f-21df63a49da9'));

        $this->layoutResolverService->disableRuleGroup($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::disableRuleGroup
     */
    public function testDisableRuleGroupThrowsBadStateExceptionIfRuleGroupIsAlreadyDisabled(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "ruleGroup" has an invalid state. Rule group is already disabled.');

        $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString('eb6311eb-24f6-4143-b476-99979a885a7e'));

        $this->layoutResolverService->disableRuleGroup($ruleGroup);
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addTarget
     */
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
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRuleDraft(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $createdCondition = $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct,
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
            'condition1',
        );

        $conditionCreateStruct->value = 'value';

        $rule = $this->layoutResolverService->loadRule(Uuid::fromString('de086bdf-0014-5f4f-89e4-fc0aff21da90'));

        $this->layoutResolverService->addCondition(
            $rule,
            $conditionCreateStruct,
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addRuleGroupCondition
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::addRuleGroupCondition
     */
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
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroupCondition
     */
    public function testUpdateRuleGroupCondition(): void
    {
        $condition = $this->layoutResolverService->loadRuleGroupConditionDraft(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $updatedCondition = $this->layoutResolverService->updateRuleGroupCondition($condition, $conditionUpdateStruct);

        self::assertTrue($updatedCondition->isDraft());
        self::assertSame('new_value', $updatedCondition->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::updateRuleGroupCondition
     */
    public function testUpdateRuleGroupConditionThrowsBadStateExceptionOnNonDraftCondition(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "condition" has an invalid state. Only draft conditions can be updated.');

        $condition = $this->layoutResolverService->loadRuleGroupCondition(Uuid::fromString('b084d390-01ea-464b-8282-797b6ef9ef1e'));

        $conditionUpdateStruct = $this->layoutResolverService->newConditionUpdateStruct();
        $conditionUpdateStruct->value = 'new_value';

        $this->layoutResolverService->updateRuleGroupCondition($condition, $conditionUpdateStruct);
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
                'comment' => '',
                'description' => '',
                'enabled' => true,
                'layoutId' => null,
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
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
                'comment' => null,
                'description' => null,
                'layoutId' => null,
            ],
            $this->exportObject($struct),
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
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleGroupCreateStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleGroupUpdateStruct
     */
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

    /**
     * @covers \Netgen\Layouts\Core\Service\LayoutResolverService::newRuleGroupMetadataUpdateStruct
     */
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
            $this->exportObject($struct),
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
            $this->exportObject($struct),
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
            $this->exportObject($struct),
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
            $this->exportObject($struct),
        );
    }
}
