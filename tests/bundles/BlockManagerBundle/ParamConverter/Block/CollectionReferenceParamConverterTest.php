<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter;
use PHPUnit\Framework\TestCase;

class CollectionReferenceParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->paramConverter = new CollectionReferenceParamConverter($this->blockServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('blockId', 'collectionIdentifier'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('collectionReference', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APICollectionReference::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $block = new Block();
        $collectionReference = new CollectionReference();

        $this->blockServiceMock
            ->expects($this->at(0))
            ->method('loadBlock')
            ->with($this->equalTo(42))
            ->will($this->returnValue($block));

        $this->blockServiceMock
            ->expects($this->at(1))
            ->method('loadCollectionReference')
            ->with($this->equalTo($block), $this->equalTo('default'))
            ->will($this->returnValue($collectionReference));

        $this->assertEquals(
            $collectionReference,
            $this->paramConverter->loadValueObject(
                array(
                    'blockId' => 42,
                    'collectionIdentifier' => 'default',
                    'published' => true,
                )
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Block\CollectionReferenceParamConverter::loadValueObject
     */
    public function testLoadValueObjectDraft()
    {
        $block = new Block();
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

        $this->assertEquals(
            $collectionReference,
            $this->paramConverter->loadValueObject(
                array(
                    'blockId' => 42,
                    'collectionIdentifier' => 'default',
                    'published' => false,
                )
            )
        );
    }
}
