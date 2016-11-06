<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\API\Values\QueryCreateStruct;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandlerWithRequiredParameter;
use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType as QueryTypeStub;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class CollectionValidatorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeRegistryMock;

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
        $this->queryTypeRegistryMock = $this->createMock(QueryTypeRegistryInterface::class);

        $this->queryTypeConfigMock = $this->createMock(Configuration::class);

        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $this->collectionValidator = new CollectionValidator($this->queryTypeRegistryMock);
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @dataProvider validateCollectionCreateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateCollectionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->collectionValidator->validateCollectionCreateStruct(new CollectionCreateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionUpdateStruct
     * @dataProvider validateCollectionUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateCollectionUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->collectionValidator->validateCollectionUpdateStruct(new CollectionUpdateStruct($params));
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @dataProvider validateItemCreateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateItemCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->collectionValidator->validateItemCreateStruct(new ItemCreateStruct($params));
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @dataProvider validateQueryCreateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateQueryCreateStruct(array $params, $isValid)
    {
        $handler = new QueryTypeHandlerWithRequiredParameter();
        $queryType = new QueryType(
            array(
                'type' => 'query_type',
                'handler' => new QueryTypeHandlerWithRequiredParameter(),
                'config' => $this->queryTypeConfigMock,
                'parameters' => $handler->getParameters(),
            )
        );

        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->will($this->returnValue($queryType));

        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->collectionValidator->validateQueryCreateStruct(new QueryCreateStruct($params));
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @dataProvider validateQueryUpdateStructProvider
     * @doesNotPerformAssertions
     */
    public function testValidateQueryUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->collectionValidator->validateQueryUpdateStruct(
            new Query(array('queryType' => new QueryTypeStub('query_type'))),
            new QueryUpdateStruct($params)
        );
    }

    public function validateCollectionCreateStructProvider()
    {
        return array(
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL, 'shared' => null), true),
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL, 'shared' => false), true),
            array(array('name' => 'Collection', 'type' => Collection::TYPE_MANUAL, 'shared' => true), true),
            array(array('name' => 'Collection', 'type' => Collection::TYPE_MANUAL, 'shared' => true), true),
            array(array('name' => 23, 'type' => Collection::TYPE_MANUAL, 'shared' => true), false),
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL, 'shared' => true), false),
            array(array('name' => '', 'type' => Collection::TYPE_MANUAL, 'shared' => true), false),
            array(array('name' => '   ', 'type' => Collection::TYPE_MANUAL, 'shared' => true), false),
            array(array('name' => null, 'type' => 23, 'shared' => null), false),
            array(array('name' => null, 'type' => null, 'shared' => null), false),
            array(array('name' => null, 'type' => 'type', 'shared' => null), false),
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL, 'shared' => 42), false),
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL, 'shared' => ''), false),
        );
    }

    public function validateCollectionUpdateStructProvider()
    {
        return array(
            array(array('name' => 'Collection'), true),
            array(array('name' => 23), false),
            array(array('name' => null), false),
            array(array('name' => ''), false),
            array(array('name' => '   '), false),
        );
    }

    public function validateItemCreateStructProvider()
    {
        return array(
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), true),
            array(array('valueId' => '42', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), true),
            array(array('valueId' => null, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => '', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => 'nonexistent', 'type' => Item::TYPE_MANUAL), false),
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
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => null,
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => '',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 42,
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => null,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => '',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 42,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => '',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => null,
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(),
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
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => null,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => '',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 42,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(),
                ),
                true,
            ),
        );
    }
}
