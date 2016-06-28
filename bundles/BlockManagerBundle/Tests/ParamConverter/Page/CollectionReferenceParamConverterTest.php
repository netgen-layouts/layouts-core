<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\BlockManager\Core\Values\Page\BlockDraft;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter;
use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\API\Service\BlockService;
use PHPUnit\Framework\TestCase;

class CollectionReferenceParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new CollectionReferenceParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        self::assertEquals(array('blockId', 'collectionIdentifier'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        self::assertEquals('collectionReference', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        self::assertEquals(APICollectionReference::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\CollectionReferenceParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new BlockDraft();
        $collectionReference = new CollectionReference();

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadBlockDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $this->blockServiceMock
            ->expects($this->at(1))
            ->method('loadCollectionReference')
            ->with($this->equalTo($block), $this->equalTo('default'))
            ->will($this->returnValue($collectionReference));

        self::assertEquals(
            $collectionReference,
            $this->paramConverter->loadValueObject(
                array(
                    'blockId' => 42,
                    'collectionIdentifier' => 'default',
                )
            )
        );
    }
}
