<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\Page;

use Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter;
use Netgen\BlockManager\API\Values\Page\Zone as APIZone;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\API\Service\LayoutService;
use PHPUnit\Framework\TestCase;

class ZoneParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter
     */
    protected $paramConverter;

    public function setUp()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        $this->paramConverter = new ZoneParamConverter($this->layoutServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName()
    {
        $this->assertEquals(array('layoutId', 'zoneIdentifier'), $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName()
    {
        $this->assertEquals('zone', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::getSupportedClass
     */
    public function testGetSupportedClass()
    {
        $this->assertEquals(APIZone::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\Page\ZoneParamConverter::loadValueObject
     */
    public function testLoadValueObject()
    {
        $zone = new Zone();

        $this->layoutServiceMock
            ->expects($this->once())
            ->method('loadZone')
            ->with($this->equalTo(42), $this->equalTo('left'))
            ->will($this->returnValue($zone));

        $this->assertEquals(
            $zone,
            $this->paramConverter->loadValueObject(
                array(
                    'layoutId' => 42,
                    'zoneIdentifier' => 'left',
                )
            )
        );
    }
}
