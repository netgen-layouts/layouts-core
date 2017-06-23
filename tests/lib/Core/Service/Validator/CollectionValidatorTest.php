<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType as QueryTypeStub;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandlerWithRequiredParameter;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CollectionValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $collectionValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->collectionValidator = new CollectionValidator();
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @dataProvider validateCollectionCreateStructProvider
     */
    public function testValidateCollectionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateCollectionCreateStruct(
            new CollectionCreateStruct($params)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Manual collection cannot have a query.
     */
    public function testValidateCollectionCreateStructWithQueryInManualCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_MANUAL;

        $collectionCreateStruct->queryCreateStruct = new QueryCreateStruct(
            array(
                'queryType' => $this->getQueryType(),
                'parameterValues' => array(
                    'param' => 'value',
                ),
            )
        );

        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @expectedException \Netgen\BlockManager\Exception\Validation\ValidationException
     * @expectedExceptionMessage Dynamic collection needs to have a query.
     */
    public function testValidateCollectionCreateStructWithNoQueryInDynamicCollection()
    {
        $collectionCreateStruct = new CollectionCreateStruct();
        $collectionCreateStruct->type = Collection::TYPE_DYNAMIC;

        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @dataProvider validateItemCreateStructProvider
     */
    public function testValidateItemCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateItemCreateStruct(new ItemCreateStruct($params));
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @dataProvider validateQueryCreateStructProvider
     */
    public function testValidateQueryCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryCreateStruct(new QueryCreateStruct($params));
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @dataProvider validateQueryUpdateStructProvider
     */
    public function testValidateQueryUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationException::class);
        }

        // Fake assertion to fix coverage on tests which do not perform assertions
        $this->assertTrue(true);

        $this->collectionValidator->validateQueryUpdateStruct(
            new Query(array('queryType' => new QueryTypeStub('query_type'))),
            new QueryUpdateStruct($params)
        );
    }

    public function validateCollectionCreateStructProvider()
    {
        return array(
            array(array('type' => Collection::TYPE_MANUAL), true),
            array(array('type' => 23), false),
            array(array('type' => null), false),
            array(array('type' => 'type'), false),
            array(
                array(
                    'type' => Collection::TYPE_MANUAL,
                    'itemCreateStructs' => array(
                        new ItemCreateStruct(
                            array(
                                'valueId' => 42,
                                'valueType' => 'value',
                                'type' => Item::TYPE_MANUAL,
                            )
                        ),
                    ),
                ),
                true,
            ),
            array(
                array(
                    'type' => Collection::TYPE_MANUAL,
                    'itemCreateStructs' => array(
                        new ItemCreateStruct(
                            array(
                                'valueId' => null,
                                'valueType' => 'value',
                                'type' => Item::TYPE_MANUAL,
                            )
                        ),
                    ),
                ),
                false,
            ),
        );
    }

    public function validateItemCreateStructProvider()
    {
        return array(
            array(
                array('valueId' => 42, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL),
                true,
            ),
            array(
                array('valueId' => '42', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL),
                true,
            ),
            array(
                array('valueId' => null, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL),
                false,
            ),
            array(
                array('valueId' => '', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL),
                false,
            ),
            array(
                array('valueId' => 42, 'valueType' => 'nonexistent', 'type' => Item::TYPE_MANUAL),
                false,
            ),
            array(array('valueId' => 42, 'valueType' => '', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => 23), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => 'type'), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => null), false),
        );
    }

    public function validateQueryCreateStructProvider()
    {
        return array(
            array(
                array(
                    'queryType' => $this->getQueryType(),
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'queryType' => null,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'queryType' => 42,
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'queryType' => $this->getQueryType(),
                    'parameterValues' => array(
                        'param' => '',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'queryType' => $this->getQueryType(),
                    'parameterValues' => array(
                        'param' => null,
                    ),
                ),
                false,
            ),
            array(
                array(
                    'queryType' => $this->getQueryType(),
                    'parameterValues' => array(),
                ),
                false,
            ),
        );
    }

    public function validateQueryUpdateStructProvider()
    {
        return array(
            array(
                array(
                    'parameterValues' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'parameterValues' => array(
                        'param' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'parameterValues' => array(
                        'param' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'parameterValues' => array(),
                ),
                true,
            ),
        );
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType
     */
    protected function getQueryType()
    {
        $handler = new QueryTypeHandlerWithRequiredParameter();

        return new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandlerWithRequiredParameter(),
                'config' => $this->createMock(Configuration::class),
                'parameters' => $handler->getParameters(),
            )
        );
    }
}
