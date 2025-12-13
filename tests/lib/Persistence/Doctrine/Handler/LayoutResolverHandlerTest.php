<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Handler;

use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Doctrine\Handler\LayoutResolverHandler;
use Netgen\Layouts\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Persistence\Values\Status;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\Persistence\Doctrine\TestCaseTrait;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\UuidGeneratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LayoutResolverHandler::class)]
#[CoversClass(LayoutResolverQueryHandler::class)]
final class LayoutResolverHandlerTest extends CoreTestCase
{
    use ExportObjectTrait;
    use TestCaseTrait;
    use UuidGeneratorTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createDatabase();
        $this->createHandlers();
    }

    public function testLoadRule(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(1, Status::Published);

        self::assertSame(
            [
                'description' => 'My description',
                'id' => 1,
                'isEnabled' => true,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'priority' => 9,
                'ruleGroupId' => 1,
                'status' => Status::Published,
                'uuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
            ],
            $this->exportObject($rule),
        );
    }

    public function testLoadRuleThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "999"');

        $this->layoutResolverHandler->loadRule(999, Status::Published);
    }

    public function testLoadRuleGroup(): void
    {
        $rule = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);

        self::assertSame(
            [
                'depth' => 1,
                'description' => 'My description',
                'id' => 2,
                'isEnabled' => true,
                'name' => 'First group',
                'parentId' => 1,
                'parentUuid' => RuleGroup::ROOT_UUID,
                'path' => '/1/2/',
                'priority' => 1,
                'status' => Status::Published,
                'uuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
            ],
            $this->exportObject($rule),
        );
    }

    public function testLoadRuleGroupThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "999"');

        $this->layoutResolverHandler->loadRuleGroup(999, Status::Published);
    }

    public function testLoadRulesForLayout(): void
    {
        $rules = $this->layoutResolverHandler->loadRulesForLayout(
            $this->layoutHandler->loadLayout(1, Status::Published),
        );

        self::assertCount(2, $rules);
        self::assertContainsOnlyInstancesOf(Rule::class, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    public function testGetRuleCountForLayout(): void
    {
        $rules = $this->layoutResolverHandler->getRuleCountForLayout(
            $this->layoutHandler->loadLayout(1, Status::Published),
        );

        self::assertSame(2, $rules);
    }

    public function testLoadRulesFromGroup(): void
    {
        $rules = $this->layoutResolverHandler->loadRulesFromGroup(
            $this->layoutResolverHandler->loadRuleGroup(2, Status::Published),
        );

        self::assertCount(2, $rules);
        self::assertContainsOnlyInstancesOf(Rule::class, $rules);

        $previousPriority = null;
        foreach ($rules as $index => $rule) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $rule->priority);
            }

            $previousPriority = $rule->priority;
        }
    }

    public function testGetRuleCountFromGroup(): void
    {
        $rules = $this->layoutResolverHandler->getRuleCountFromGroup(
            $this->layoutResolverHandler->loadRuleGroup(2, Status::Published),
        );

        self::assertSame(2, $rules);
    }

    public function testLoadRuleGroups(): void
    {
        $ruleGroups = $this->layoutResolverHandler->loadRuleGroups(
            $this->layoutResolverHandler->loadRuleGroup(1, Status::Published),
        );

        self::assertCount(2, $ruleGroups);
        self::assertContainsOnlyInstancesOf(RuleGroup::class, $ruleGroups);

        $previousPriority = null;
        foreach ($ruleGroups as $index => $ruleGroup) {
            if ($index > 0) {
                self::assertLessThanOrEqual($previousPriority, $ruleGroup->priority);
            }

            $previousPriority = $ruleGroup->priority;
        }
    }

    public function testGetRuleGroupCount(): void
    {
        $ruleGroups = $this->layoutResolverHandler->getRuleGroupCount(
            $this->layoutResolverHandler->loadRuleGroup(1, Status::Published),
        );

        self::assertSame(2, $ruleGroups);
    }

    public function testLoadTarget(): void
    {
        $target = $this->layoutResolverHandler->loadTarget(1, Status::Published);

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 1,
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Status::Published,
                'type' => 'route',
                'uuid' => 'c7c5cdca-02da-5ba5-ad9e-d25cbc4b1b46',
                'value' => 'my_cool_route',
            ],
            $this->exportObject($target),
        );
    }

    public function testLoadTargetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "999"');

        $this->layoutResolverHandler->loadTarget(999, Status::Published);
    }

    public function testLoadRuleTargets(): void
    {
        $targets = $this->layoutResolverHandler->loadRuleTargets(
            $this->layoutResolverHandler->loadRule(1, Status::Published),
        );

        self::assertNotEmpty($targets);
        self::assertContainsOnlyInstancesOf(Target::class, $targets);
    }

    public function testLoadRuleCondition(): void
    {
        $condition = $this->layoutResolverHandler->loadRuleCondition(1, Status::Published);

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'ruleUuid' => '55622437-f700-5378-99c9-7dafe89a8fb6',
                'status' => Status::Published,
                'type' => 'route_parameter',
                'uuid' => '35f4594c-6674-5815-add6-07f288b79686',
                'value' => [
                    'parameter_name' => 'some_param',
                    'parameter_values' => [1, 2],
                ],
            ],
            $this->exportObject($condition),
        );
    }

    public function testLoadRuleConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "999"');

        $this->layoutResolverHandler->loadRuleCondition(999, Status::Published);
    }

    public function testLoadRuleGroupCondition(): void
    {
        $condition = $this->layoutResolverHandler->loadRuleGroupCondition(5, Status::Published);

        self::assertSame(
            [
                'id' => 5,
                'ruleGroupId' => 2,
                'ruleGroupUuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
                'status' => Status::Published,
                'type' => 'condition1',
                'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                'value' => [
                    'some_other_value',
                ],
            ],
            $this->exportObject($condition),
        );
    }

    public function testLoadRuleGroupConditionThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "999"');

        $this->layoutResolverHandler->loadRuleGroupCondition(999, Status::Published);
    }

    public function testLoadRuleConditions(): void
    {
        $conditions = $this->layoutResolverHandler->loadRuleConditions(
            $this->layoutResolverHandler->loadRule(2, Status::Published),
        );

        self::assertNotEmpty($conditions);
        self::assertContainsOnlyInstancesOf(RuleCondition::class, $conditions);
    }

    public function testLoadRuleGroupConditions(): void
    {
        $conditions = $this->layoutResolverHandler->loadRuleGroupConditions(
            $this->layoutResolverHandler->loadRuleGroup(2, Status::Published),
        );

        self::assertNotEmpty($conditions);
        self::assertContainsOnlyInstancesOf(RuleGroupCondition::class, $conditions);
    }

    public function testRuleExists(): void
    {
        self::assertTrue($this->layoutResolverHandler->ruleExists(1, Status::Published));
    }

    public function testRuleNotExists(): void
    {
        self::assertFalse($this->layoutResolverHandler->ruleExists(999, Status::Published));
    }

    public function testRuleNotExistsInStatus(): void
    {
        self::assertFalse($this->layoutResolverHandler->ruleExists(1, Status::Archived));
    }

    public function testCreateRule(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->isEnabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Status::Draft;

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRule = $this->withUuids(
            fn (): Rule => $this->layoutResolverHandler->createRule($ruleCreateStruct, $ruleGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(12, $createdRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $createdRule->layoutUuid);
        self::assertSame(5, $createdRule->priority);
        self::assertTrue($createdRule->isEnabled);
        self::assertSame('My rule', $createdRule->description);
        self::assertSame(Status::Draft, $createdRule->status);
    }

    public function testCreateRuleWithCustomUuid(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = '0f714915-eef0-4dc1-b22b-1107cb1ab92b';
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->isEnabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Status::Draft;

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRule = $this->layoutResolverHandler->createRule($ruleCreateStruct, $ruleGroup);

        self::assertSame(12, $createdRule->id);
        self::assertSame('0f714915-eef0-4dc1-b22b-1107cb1ab92b', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $createdRule->layoutUuid);
        self::assertSame(5, $createdRule->priority);
        self::assertTrue($createdRule->isEnabled);
        self::assertSame('My rule', $createdRule->description);
        self::assertSame(Status::Draft, $createdRule->status);
    }

    public function testCreateRuleWithExistingUuidThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule with provided UUID already exists.');

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = '26768324-03dd-5952-8a55-4b449d6cd634';
        $ruleCreateStruct->layoutId = 'd8e55af7-cf62-5f28-ae15-331b457d82e9';
        $ruleCreateStruct->priority = 5;
        $ruleCreateStruct->isEnabled = true;
        $ruleCreateStruct->description = 'My rule';
        $ruleCreateStruct->status = Status::Draft;

        $this->layoutResolverHandler->createRule($ruleCreateStruct, $ruleGroup);
    }

    public function testCreateRuleWithNoPriority(): void
    {
        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = null;
        $ruleCreateStruct->priority = null;
        $ruleCreateStruct->isEnabled = false;
        $ruleCreateStruct->description = '';
        $ruleCreateStruct->status = Status::Draft;

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRule = $this->withUuids(
            fn (): Rule => $this->layoutResolverHandler->createRule($ruleCreateStruct, $ruleGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(12, $createdRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRule->uuid);
        self::assertSame(1, $createdRule->ruleGroupId);
        self::assertNull($createdRule->layoutUuid);
        self::assertSame(-11, $createdRule->priority);
        self::assertFalse($createdRule->isEnabled);
        self::assertSame('', $createdRule->description);
        self::assertSame(Status::Draft, $createdRule->status);
    }

    public function testCreateRuleWithNoPriorityAndNoRules(): void
    {
        // First delete all rules
        $rules = $this->layoutResolverHandler->loadRulesFromGroup(
            $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published),
        );

        foreach ($rules as $rule) {
            $this->layoutResolverHandler->deleteRule($rule->id);
        }

        $ruleCreateStruct = new RuleCreateStruct();
        $ruleCreateStruct->uuid = null;
        $ruleCreateStruct->layoutId = null;
        $ruleCreateStruct->priority = null;
        $ruleCreateStruct->isEnabled = false;
        $ruleCreateStruct->description = '';
        $ruleCreateStruct->status = Status::Draft;

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup('eb6311eb-24f6-4143-b476-99979a885a7e', Status::Published);

        $createdRule = $this->layoutResolverHandler->createRule($ruleCreateStruct, $ruleGroup);

        self::assertSame(0, $createdRule->priority);
        self::assertSame(Status::Draft, $createdRule->status);
    }

    public function testUpdateRule(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = '7900306c-0351-5f0a-9b33-5d4f5a1f3943';
        $ruleUpdateStruct->description = 'New description';

        $updatedRule = $this->layoutResolverHandler->updateRule(
            $this->layoutResolverHandler->loadRule(3, Status::Published),
            $ruleUpdateStruct,
        );

        self::assertSame(3, $updatedRule->id);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame(2, $updatedRule->ruleGroupId);
        self::assertSame('7900306c-0351-5f0a-9b33-5d4f5a1f3943', $updatedRule->layoutUuid);
        self::assertSame('New description', $updatedRule->description);
        self::assertSame(Status::Published, $updatedRule->status);
    }

    public function testUpdateRuleWithRemovalOfLinkedLayout(): void
    {
        $ruleUpdateStruct = new RuleUpdateStruct();
        $ruleUpdateStruct->layoutId = false;

        $updatedRule = $this->layoutResolverHandler->updateRule(
            $this->layoutResolverHandler->loadRule(3, Status::Published),
            $ruleUpdateStruct,
        );

        self::assertSame(3, $updatedRule->id);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame(2, $updatedRule->ruleGroupId);
        self::assertNull($updatedRule->layoutUuid);
        self::assertSame(Status::Published, $updatedRule->status);
    }

    public function testUpdateRuleWithDefaultValues(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(3, Status::Published);
        $ruleUpdateStruct = new RuleUpdateStruct();

        $updatedRule = $this->layoutResolverHandler->updateRule($rule, $ruleUpdateStruct);

        self::assertSame(3, $updatedRule->id);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $updatedRule->uuid);
        self::assertSame($rule->ruleGroupId, $updatedRule->ruleGroupId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $updatedRule->layoutUuid);
        self::assertSame($rule->description, $updatedRule->description);
        self::assertSame(Status::Published, $updatedRule->status);
    }

    public function testUpdateRuleMetadata(): void
    {
        $updatedRule = $this->layoutResolverHandler->updateRuleMetadata(
            $this->layoutResolverHandler->loadRule(5, Status::Published),
            RuleMetadataUpdateStruct::fromArray(
                [
                    'isEnabled' => false,
                    'priority' => 50,
                ],
            ),
        );

        self::assertSame(50, $updatedRule->priority);
        self::assertFalse($updatedRule->isEnabled);
        self::assertSame(Status::Published, $updatedRule->status);
    }

    public function testUpdateRuleMetadataWithDefaultValues(): void
    {
        $updatedRule = $this->layoutResolverHandler->updateRuleMetadata(
            $this->layoutResolverHandler->loadRule(5, Status::Published),
            new RuleMetadataUpdateStruct(),
        );

        self::assertSame(5, $updatedRule->priority);
        self::assertTrue($updatedRule->isEnabled);
        self::assertSame(Status::Published, $updatedRule->status);
    }

    public function testCopyRule(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(5, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(1, Status::Published);

        $copiedRule = $this->withUuids(
            fn (): Rule => $this->layoutResolverHandler->copyRule($rule, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                'aaa3659b-b574-5e6b-8902-0ea37f576469',
            ],
        );

        self::assertSame(12, $copiedRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRule->uuid);
        self::assertSame($targetGroup->id, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->isEnabled, $copiedRule->isEnabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame($rule->status, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 21,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 22,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'aaa3659b-b574-5e6b-8902-0ea37f576469',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleConditions($copiedRule),
            ),
        );
    }

    public function testCopyRuleToOtherGroup(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(5, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);

        $copiedRule = $this->withUuids(
            fn (): Rule => $this->layoutResolverHandler->copyRule($rule, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                'aaa3659b-b574-5e6b-8902-0ea37f576469',
            ],
        );

        self::assertSame(12, $copiedRule->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRule->uuid);
        self::assertSame($targetGroup->id, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->isEnabled, $copiedRule->isEnabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame($rule->status, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 21,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 22,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleId' => $copiedRule->id,
                    'ruleUuid' => $copiedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'aaa3659b-b574-5e6b-8902-0ea37f576469',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleConditions($copiedRule),
            ),
        );
    }

    public function testMoveRule(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(5, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(4, Status::Published);

        $movedRule = $this->layoutResolverHandler->moveRule($rule, $targetGroup);

        self::assertSame($rule->id, $movedRule->id);
        self::assertSame($rule->uuid, $movedRule->uuid);
        self::assertSame($targetGroup->id, $movedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $movedRule->layoutUuid);
        self::assertSame($rule->priority, $movedRule->priority);
        self::assertSame($rule->isEnabled, $movedRule->isEnabled);
        self::assertSame($rule->description, $movedRule->description);
        self::assertSame($rule->status, $movedRule->status);

        self::assertSame(
            [
                [
                    'id' => 9,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => '5104e4e7-1a20-5db8-8857-5ab99f1290b9',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 10,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => 'f0019d3e-5868-503d-b81b-5263af428495',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleTargets($movedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 4,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '7db46c94-3139-5a3d-9b2a-b2d28e7573ca',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleConditions($movedRule),
            ),
        );
    }

    public function testMoveRuleWithSpecifiedPriority(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(5, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(4, Status::Published);

        $movedRule = $this->layoutResolverHandler->moveRule($rule, $targetGroup, 42);

        self::assertSame($rule->id, $movedRule->id);
        self::assertSame($rule->uuid, $movedRule->uuid);
        self::assertSame($targetGroup->id, $movedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $movedRule->layoutUuid);
        self::assertSame(42, $movedRule->priority);
        self::assertSame($rule->isEnabled, $movedRule->isEnabled);
        self::assertSame($rule->description, $movedRule->description);
        self::assertSame($rule->status, $movedRule->status);

        self::assertSame(
            [
                [
                    'id' => 9,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => '5104e4e7-1a20-5db8-8857-5ab99f1290b9',
                    'value' => 'my_second_cool_',
                ],
                [
                    'id' => 10,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'route_prefix',
                    'uuid' => 'f0019d3e-5868-503d-b81b-5263af428495',
                    'value' => 'my_third_cool_',
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleTargets($movedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 4,
                    'ruleId' => $movedRule->id,
                    'ruleUuid' => $movedRule->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '7db46c94-3139-5a3d-9b2a-b2d28e7573ca',
                    'value' => ['some_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleConditions($movedRule),
            ),
        );
    }

    public function testMoveRuleToSameGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule is already in specified target group.');

        $rule = $this->layoutResolverHandler->loadRule(5, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(1, Status::Published);

        $this->layoutResolverHandler->moveRule($rule, $targetGroup);
    }

    public function testCreateRuleStatus(): void
    {
        $rule = $this->layoutResolverHandler->loadRule(3, Status::Published);
        $copiedRule = $this->layoutResolverHandler->createRuleStatus($rule, Status::Archived);

        self::assertSame($rule->id, $copiedRule->id);
        self::assertSame($rule->uuid, $copiedRule->uuid);
        self::assertSame($rule->ruleGroupId, $copiedRule->ruleGroupId);
        self::assertSame($rule->layoutUuid, $copiedRule->layoutUuid);
        self::assertSame($rule->priority, $copiedRule->priority);
        self::assertSame($rule->isEnabled, $copiedRule->isEnabled);
        self::assertSame($rule->description, $copiedRule->description);
        self::assertSame(Status::Archived, $copiedRule->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Status::Archived,
                    'type' => 'route',
                    'uuid' => '445e885e-1ad5-584b-b51b-263fb66805c2',
                    'value' => 'my_fourth_cool_route',
                ],
                [
                    'id' => 6,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Status::Archived,
                    'type' => 'route',
                    'uuid' => 'feee231e-4fee-514a-a938-a2769036c07b',
                    'value' => 'my_fifth_cool_route',
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleTargets($copiedRule),
            ),
        );

        self::assertSame(
            [
                [
                    'id' => 2,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Status::Archived,
                    'type' => 'route_parameter',
                    'uuid' => '9a6c8459-5fda-5d4b-b06e-06f637ab6e01',
                    'value' => [
                        'parameter_name' => 'some_param',
                        'parameter_values' => [3, 4],
                    ],
                ],
                [
                    'id' => 3,
                    'ruleId' => 3,
                    'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                    'status' => Status::Archived,
                    'type' => 'route_parameter',
                    'uuid' => 'dd49afcd-aab0-5970-b7b8-413238faf539',
                    'value' => [
                        'parameter_name' => 'some_other_param',
                        'parameter_values' => [5, 6],
                    ],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleConditions($copiedRule),
            ),
        );
    }

    public function testDeleteRule(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "3"');

        $this->layoutResolverHandler->deleteRule(3);

        $this->layoutResolverHandler->loadRule(3, Status::Published);
    }

    public function testDeleteRuleInOneStatus(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule with identifier "5"');

        $this->layoutResolverHandler->deleteRule(5, Status::Draft);

        // First, verify that NOT all rule statuses are deleted
        try {
            $this->layoutResolverHandler->loadRule(5, Status::Published);
        } catch (NotFoundException) {
            self::fail('Deleting the rule in draft status deleted other/all statuses.');
        }

        $this->layoutResolverHandler->loadRule(5, Status::Draft);
    }

    public function testRuleGroupExists(): void
    {
        self::assertTrue($this->layoutResolverHandler->ruleGroupExists(1, Status::Published));
    }

    public function testRuleGroupNotExists(): void
    {
        self::assertFalse($this->layoutResolverHandler->ruleGroupExists(999, Status::Published));
    }

    public function testRuleGroupNotExistsInStatus(): void
    {
        self::assertFalse($this->layoutResolverHandler->ruleGroupExists(1, Status::Archived));
    }

    public function testCreateRuleGroup(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = 'New rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->isEnabled = true;
        $ruleGroupCreateStruct->status = Status::Draft;

        $parentGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct, $parentGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('New rule group', $createdRuleGroup->name);
        self::assertSame('My rule group', $createdRuleGroup->description);
        self::assertSame(5, $createdRuleGroup->priority);
        self::assertTrue($createdRuleGroup->isEnabled);
        self::assertSame(Status::Draft, $createdRuleGroup->status);
    }

    public function testCreateRuleGroupWithCustomUuid(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = 'f06f245a-f951-52c8-bfa3-84c80154eadc';
        $ruleGroupCreateStruct->name = 'New rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->isEnabled = true;
        $ruleGroupCreateStruct->status = Status::Draft;

        $parentGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRuleGroup = $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct, $parentGroup);

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('New rule group', $createdRuleGroup->name);
        self::assertSame('My rule group', $createdRuleGroup->description);
        self::assertSame(5, $createdRuleGroup->priority);
        self::assertTrue($createdRuleGroup->isEnabled);
        self::assertSame(Status::Draft, $createdRuleGroup->status);
    }

    public function testCreateRuleGroupWithNoPriority(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = '';
        $ruleGroupCreateStruct->description = '';
        $ruleGroupCreateStruct->priority = null;
        $ruleGroupCreateStruct->isEnabled = false;
        $ruleGroupCreateStruct->status = Status::Draft;

        $parentGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $createdRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct, $parentGroup),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(5, $createdRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $createdRuleGroup->uuid);
        self::assertSame($parentGroup->depth + 1, $createdRuleGroup->depth);
        self::assertSame($parentGroup->path . $createdRuleGroup->id . '/', $createdRuleGroup->path);
        self::assertSame($parentGroup->id, $createdRuleGroup->parentId);
        self::assertSame($parentGroup->uuid, $createdRuleGroup->parentUuid);
        self::assertSame('', $createdRuleGroup->name);
        self::assertSame('', $createdRuleGroup->description);
        self::assertSame(-11, $createdRuleGroup->priority);
        self::assertFalse($createdRuleGroup->isEnabled);
        self::assertSame(Status::Draft, $createdRuleGroup->status);
    }

    public function testCreateRuleGroupWithNoPriorityAndNoRulesAndRuleGroups(): void
    {
        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = null;
        $ruleGroupCreateStruct->name = '';
        $ruleGroupCreateStruct->description = '';
        $ruleGroupCreateStruct->priority = null;
        $ruleGroupCreateStruct->isEnabled = false;
        $ruleGroupCreateStruct->status = Status::Draft;

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(3, Status::Published);

        $createdRuleGroup = $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct, $ruleGroup);

        self::assertSame(0, $createdRuleGroup->priority);
        self::assertSame(Status::Draft, $createdRuleGroup->status);
    }

    public function testCreateRootRuleGroupWithExistingRootRuleGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Root rule group already exists.');

        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->name = 'My rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->isEnabled = true;
        $ruleGroupCreateStruct->status = Status::Draft;

        $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct);
    }

    public function testCreateRuleGroupWithExistingUuidThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Argument "uuid" has an invalid state. Rule group with provided UUID already exists.');

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(RuleGroup::ROOT_UUID, Status::Published);

        $ruleGroupCreateStruct = new RuleGroupCreateStruct();
        $ruleGroupCreateStruct->uuid = 'b4f85f38-de3f-4af7-9a5f-21df63a49da9';
        $ruleGroupCreateStruct->name = 'My rule group';
        $ruleGroupCreateStruct->description = 'My rule group';
        $ruleGroupCreateStruct->priority = 5;
        $ruleGroupCreateStruct->isEnabled = true;
        $ruleGroupCreateStruct->status = Status::Draft;

        $this->layoutResolverHandler->createRuleGroup($ruleGroupCreateStruct, $ruleGroup);
    }

    public function testUpdateRuleGroup(): void
    {
        $ruleGroupUpdateStruct = new RuleGroupUpdateStruct();
        $ruleGroupUpdateStruct->name = 'New name';
        $ruleGroupUpdateStruct->description = 'New description';

        $updatedRuleGroup = $this->layoutResolverHandler->updateRuleGroup(
            $this->layoutResolverHandler->loadRuleGroup(3, Status::Published),
            $ruleGroupUpdateStruct,
        );

        self::assertSame(3, $updatedRuleGroup->id);
        self::assertSame('eb6311eb-24f6-4143-b476-99979a885a7e', $updatedRuleGroup->uuid);
        self::assertSame('New name', $updatedRuleGroup->name);
        self::assertSame('New description', $updatedRuleGroup->description);
        self::assertSame(Status::Published, $updatedRuleGroup->status);
    }

    public function testUpdateRuleGroupWithDefaultValues(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(3, Status::Published);
        $ruleGroupUpdateStruct = new RuleGroupUpdateStruct();

        $updatedRuleGroup = $this->layoutResolverHandler->updateRuleGroup($ruleGroup, $ruleGroupUpdateStruct);

        self::assertSame(3, $updatedRuleGroup->id);
        self::assertSame('eb6311eb-24f6-4143-b476-99979a885a7e', $updatedRuleGroup->uuid);
        self::assertSame($ruleGroup->name, $updatedRuleGroup->name);
        self::assertSame($ruleGroup->description, $updatedRuleGroup->description);
        self::assertSame(Status::Published, $updatedRuleGroup->status);
    }

    public function testUpdateRuleGroupMetadata(): void
    {
        $updatedRuleGroup = $this->layoutResolverHandler->updateRuleGroupMetadata(
            $this->layoutResolverHandler->loadRuleGroup(3, Status::Published),
            RuleGroupMetadataUpdateStruct::fromArray(
                [
                    'isEnabled' => false,
                    'priority' => 50,
                ],
            ),
        );

        self::assertSame(50, $updatedRuleGroup->priority);
        self::assertFalse($updatedRuleGroup->isEnabled);
        self::assertSame(Status::Published, $updatedRuleGroup->status);
    }

    public function testUpdateRuleGroupMetadataWithDefaultValues(): void
    {
        $updatedRuleGroup = $this->layoutResolverHandler->updateRuleGroupMetadata(
            $this->layoutResolverHandler->loadRuleGroup(3, Status::Published),
            new RuleGroupMetadataUpdateStruct(),
        );

        self::assertSame(2, $updatedRuleGroup->priority);
        self::assertFalse($updatedRuleGroup->isEnabled);
        self::assertSame(Status::Published, $updatedRuleGroup->status);
    }

    public function testCopyRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(1, Status::Published);

        $copiedRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->layoutResolverHandler->copyRuleGroup($ruleGroup, $targetGroup),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
            ],
        );

        self::assertSame(5, $copiedRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $copiedRuleGroup->depth);
        self::assertSame($targetGroup->path . $copiedRuleGroup->id . '/', $copiedRuleGroup->path);
        self::assertSame($targetGroup->id, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->isEnabled, $copiedRuleGroup->isEnabled);
        self::assertSame($ruleGroup->status, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 8,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );
    }

    public function testCopyRuleGroupWithChildren(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(1, Status::Published);

        $copiedRuleGroup = $this->withUuids(
            fn (): RuleGroup => $this->layoutResolverHandler->copyRuleGroup($ruleGroup, $targetGroup, true),
            [
                'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'efd1d54a-5d53-518f-91a5-f4965c242a67',
                '1169074c-8779-5b64-afec-c910705e418a',
                '5848c206-341b-4142-a2e0-b27a5309cc6c',
                'ecae57e9-42ca-423f-80f4-7c4c129fc439',
                '9da71481-a9ec-47f1-800f-b2fc4e0fd136',
                '85286fb0-efe7-4ab2-97a3-e4a130006afd',
                'a2bd079c-b008-41d5-a4fe-179f3bf1c3da',
                '4d8881cb-e9ac-4fd0-a0f2-69303192febd',
                'a802af45-7d1a-4c01-99e2-246f9d41541f',
                '094d64e6-b22f-46ef-b573-63ee2e308530',
                'ce747405-4641-436a-8fe2-7969354e6452',
                'aa82ce80-c4c4-4b80-819a-baf5c8af69d6',
                '81ecb297-52a0-4ec7-89d5-eebe595c8d2c',
                'b1e5bc79-610f-41b7-9505-7567435cb80e',
            ],
        );

        self::assertSame(5, $copiedRuleGroup->id);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $copiedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $copiedRuleGroup->depth);
        self::assertSame($targetGroup->path . $copiedRuleGroup->id . '/', $copiedRuleGroup->path);
        self::assertSame($targetGroup->id, $copiedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->isEnabled, $copiedRuleGroup->isEnabled);
        self::assertSame($ruleGroup->status, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 7,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'efd1d54a-5d53-518f-91a5-f4965c242a67',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 8,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '1169074c-8779-5b64-afec-c910705e418a',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );

        self::assertSame(1, $this->layoutResolverHandler->getRuleGroupCount($copiedRuleGroup));

        $copiedSubGroup = $this->layoutResolverHandler->loadRuleGroup('ce747405-4641-436a-8fe2-7969354e6452', Status::Published);

        self::assertSame(2, $this->layoutResolverHandler->getRuleCountFromGroup($copiedRuleGroup));
        self::assertSame(1, $this->layoutResolverHandler->getRuleCountFromGroup($copiedSubGroup));
    }

    public function testCopyRuleGroupBelowItselfThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group cannot be copied below itself or its children.');

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(4, Status::Published);

        $this->layoutResolverHandler->copyRuleGroup($ruleGroup, $targetGroup);
    }

    public function testMoveRuleGroup(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(3, Status::Published);

        $movedRuleGroup = $this->layoutResolverHandler->moveRuleGroup($ruleGroup, $targetGroup);

        self::assertSame($ruleGroup->id, $movedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $movedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $movedRuleGroup->depth);
        self::assertSame($targetGroup->path . $movedRuleGroup->id . '/', $movedRuleGroup->path);
        self::assertSame($targetGroup->id, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $movedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $movedRuleGroup->name);
        self::assertSame($ruleGroup->description, $movedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $movedRuleGroup->priority);
        self::assertSame($ruleGroup->isEnabled, $movedRuleGroup->isEnabled);
        self::assertSame($ruleGroup->status, $movedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleGroupConditions($movedRuleGroup),
            ),
        );
    }

    public function testMoveRuleGroupWithPriority(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(3, Status::Published);

        $movedRuleGroup = $this->layoutResolverHandler->moveRuleGroup($ruleGroup, $targetGroup, 42);

        self::assertSame($ruleGroup->id, $movedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $movedRuleGroup->uuid);
        self::assertSame($targetGroup->depth + 1, $movedRuleGroup->depth);
        self::assertSame($targetGroup->path . $movedRuleGroup->id . '/', $movedRuleGroup->path);
        self::assertSame($targetGroup->id, $movedRuleGroup->parentId);
        self::assertSame($targetGroup->uuid, $movedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $movedRuleGroup->name);
        self::assertSame($ruleGroup->description, $movedRuleGroup->description);
        self::assertSame(42, $movedRuleGroup->priority);
        self::assertSame($ruleGroup->isEnabled, $movedRuleGroup->isEnabled);
        self::assertSame($ruleGroup->status, $movedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => ['some_other_value'],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $movedRuleGroup->id,
                    'ruleGroupUuid' => $movedRuleGroup->uuid,
                    'status' => Status::Published,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => ['some_third_value'],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleGroupConditions($movedRuleGroup),
            ),
        );
    }

    public function testMoveRuleGroupToSameGroupThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group is already in specified target group.');

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(1, Status::Published);

        $this->layoutResolverHandler->moveRuleGroup($ruleGroup, $targetGroup);
    }

    public function testMoveRuleGroupBelowItselfThrowsBadStateException(): void
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('Rule group cannot be moved below itself or its children.');

        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $targetGroup = $this->layoutResolverHandler->loadRuleGroup(4, Status::Published);

        $this->layoutResolverHandler->moveRuleGroup($ruleGroup, $targetGroup);
    }

    public function testCreateRuleGroupStatus(): void
    {
        $ruleGroup = $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        $copiedRuleGroup = $this->layoutResolverHandler->createRuleGroupStatus($ruleGroup, Status::Archived);

        self::assertSame($ruleGroup->id, $copiedRuleGroup->id);
        self::assertSame($ruleGroup->uuid, $copiedRuleGroup->uuid);
        self::assertSame($ruleGroup->depth, $copiedRuleGroup->depth);
        self::assertSame($ruleGroup->path, $copiedRuleGroup->path);
        self::assertSame($ruleGroup->parentId, $copiedRuleGroup->parentId);
        self::assertSame($ruleGroup->parentUuid, $copiedRuleGroup->parentUuid);
        self::assertSame($ruleGroup->name, $copiedRuleGroup->name);
        self::assertSame($ruleGroup->description, $copiedRuleGroup->description);
        self::assertSame($ruleGroup->priority, $copiedRuleGroup->priority);
        self::assertSame($ruleGroup->isEnabled, $copiedRuleGroup->isEnabled);
        self::assertSame(Status::Archived, $copiedRuleGroup->status);

        self::assertSame(
            [
                [
                    'id' => 5,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Archived,
                    'type' => 'condition1',
                    'uuid' => 'b084d390-01ea-464b-8282-797b6ef9ef1e',
                    'value' => [
                        'some_other_value',
                    ],
                ],
                [
                    'id' => 6,
                    'ruleGroupId' => $copiedRuleGroup->id,
                    'ruleGroupUuid' => $copiedRuleGroup->uuid,
                    'status' => Status::Archived,
                    'type' => 'condition1',
                    'uuid' => '46390b11-e077-4979-95cb-782575a9562b',
                    'value' => [
                        'some_third_value',
                    ],
                ],
            ],
            $this->exportObjectList(
                $this->layoutResolverHandler->loadRuleGroupConditions($copiedRuleGroup),
            ),
        );
    }

    public function testDeleteRuleGroup(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "2"');

        $this->layoutResolverHandler->deleteRuleGroup(2);

        $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
    }

    public function testDeleteRuleGroupInOneStatus(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find rule group with identifier "2"');

        $this->layoutResolverHandler->deleteRuleGroup(2, Status::Draft);

        // First, verify that NOT all rule group statuses are deleted
        try {
            $this->layoutResolverHandler->loadRuleGroup(2, Status::Published);
        } catch (NotFoundException) {
            self::fail('Deleting the rule group in draft status deleted other/all statuses.');
        }

        $this->layoutResolverHandler->loadRuleGroup(2, Status::Draft);
    }

    public function testAddTarget(): void
    {
        $targetCreateStruct = new TargetCreateStruct();
        $targetCreateStruct->type = 'target';
        $targetCreateStruct->value = '42';

        $target = $this->withUuids(
            fn (): Target => $this->layoutResolverHandler->addTarget(
                $this->layoutResolverHandler->loadRule(1, Status::Published),
                $targetCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 21,
                'ruleId' => 1,
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Status::Published,
                'type' => 'target',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => '42',
            ],
            $this->exportObject($target),
        );
    }

    public function testUpdateTarget(): void
    {
        $targetUpdateStruct = new TargetUpdateStruct();
        $targetUpdateStruct->value = 'my_new_route';

        $target = $this->layoutResolverHandler->updateTarget(
            $this->layoutResolverHandler->loadTarget(1, Status::Published),
            $targetUpdateStruct,
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 1,
                'ruleUuid' => '26768324-03dd-5952-8a55-4b449d6cd634',
                'status' => Status::Published,
                'type' => 'route',
                'uuid' => 'c7c5cdca-02da-5ba5-ad9e-d25cbc4b1b46',
                'value' => 'my_new_route',
            ],
            $this->exportObject($target),
        );
    }

    public function testDeleteTarget(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find target with identifier "2"');

        $target = $this->layoutResolverHandler->loadTarget(2, Status::Published);

        $this->layoutResolverHandler->deleteTarget($target);

        $this->layoutResolverHandler->loadTarget(2, Status::Published);
    }

    public function testAddRuleCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->withUuids(
            fn (): RuleCondition => $this->layoutResolverHandler->addRuleCondition(
                $this->layoutResolverHandler->loadRule(3, Status::Published),
                $conditionCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 7,
                'ruleId' => 3,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => Status::Published,
                'type' => 'condition',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => ['param' => 'value'],
            ],
            $this->exportObject($condition),
        );
    }

    public function testAddRuleGroupCondition(): void
    {
        $conditionCreateStruct = new ConditionCreateStruct();
        $conditionCreateStruct->type = 'condition';
        $conditionCreateStruct->value = ['param' => 'value'];

        $condition = $this->withUuids(
            fn (): RuleGroupCondition => $this->layoutResolverHandler->addRuleGroupCondition(
                $this->layoutResolverHandler->loadRuleGroup(3, Status::Published),
                $conditionCreateStruct,
            ),
            ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        );

        self::assertSame(
            [
                'id' => 7,
                'ruleGroupId' => 3,
                'ruleGroupUuid' => 'eb6311eb-24f6-4143-b476-99979a885a7e',
                'status' => Status::Published,
                'type' => 'condition',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'value' => ['param' => 'value'],
            ],
            $this->exportObject($condition),
        );
    }

    public function testUpdateCondition(): void
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();
        $conditionUpdateStruct->value = ['new_param' => 'new_value'];

        $condition = $this->layoutResolverHandler->updateCondition(
            $this->layoutResolverHandler->loadRuleCondition(1, Status::Published),
            $conditionUpdateStruct,
        );

        self::assertSame(
            [
                'id' => 1,
                'ruleId' => 2,
                'ruleUuid' => '55622437-f700-5378-99c9-7dafe89a8fb6',
                'status' => Status::Published,
                'type' => 'route_parameter',
                'uuid' => '35f4594c-6674-5815-add6-07f288b79686',
                'value' => ['new_param' => 'new_value'],
            ],
            $this->exportObject($condition),
        );
    }

    public function testDeleteCondition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find condition with identifier "2"');

        $this->layoutResolverHandler->deleteCondition(
            $this->layoutResolverHandler->loadRuleCondition(2, Status::Published),
        );

        $this->layoutResolverHandler->loadRuleCondition(2, Status::Published);
    }

    private function createHandlers(): void
    {
        $this->layoutResolverHandler = $this->createLayoutResolverHandler();
        $this->layoutHandler = $this->createLayoutHandler();
    }
}
