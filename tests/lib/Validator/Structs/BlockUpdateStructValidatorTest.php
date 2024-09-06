<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Structs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Utils\Hydrator;
use Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Netgen\Layouts\Validator\Structs\BlockUpdateStructValidator;
use stdClass;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class BlockUpdateStructValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        $this->constraint = new BlockUpdateStructConstraint();

        $handler = new BlockDefinitionHandler();
        $this->constraint->payload = Block::fromArray(
            [
                'viewType' => 'large',
                'mainLocale' => 'en',
                'definition' => BlockDefinition::fromArray(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                        'configProvider' => ConfigProvider::fromShortConfig(['large' => ['standard']]),
                    ],
                ),
            ],
        );

        parent::setUp();
    }

    /**
     * @param mixed[] $value
     *
     * @covers \Netgen\Layouts\Validator\Structs\BlockUpdateStructValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $value, bool $isValid): void
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        (new Hydrator())->hydrate($value, $blockUpdateStruct);

        $this->assertValid($isValid, $blockUpdateStruct);
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\BlockUpdateStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\BlockUpdateStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidBlock(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\API\Values\Block\Block", "stdClass" given');

        $this->constraint->payload = new stdClass();
        $this->assertValid(true, new BlockUpdateStruct());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Structs\BlockUpdateStructValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "Netgen\\\Layouts\\\API\\\Values\\\Block\\\BlockUpdateStruct", "int(eger)?" given$/');

        $this->constraint->payload = new Block();
        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            [
                [
                    'locale' => '',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en-US',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en_US.utf8',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'nonexistent',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => null,
                    'itemViewType' => null,
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => '',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => '',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'nonexistent',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => null,
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => '',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => '',
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => null,
                        'css_id' => 'id',
                    ],
                ],
                false,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_id' => 'id',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                        'css_id' => null,
                    ],
                ],
                true,
            ],
            [
                [
                    'locale' => 'en',
                    'alwaysAvailable' => true,
                    'viewType' => 'large',
                    'itemViewType' => 'standard',
                    'name' => 'My block',
                    'parameterValues' => [
                        'css_class' => 'class',
                    ],
                ],
                true,
            ],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new BlockUpdateStructValidator();
    }
}
