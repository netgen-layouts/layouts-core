<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type\DataMapper;

use ArrayIterator;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType;
use Netgen\BlockManager\Parameters\Value\LinkValue;

class LinkDataMapperTest extends DataMapperTest
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper
     */
    private $mapper;

    public function setUp()
    {
        parent::setUp();

        $parameter = new Parameter(
            array(
                'type' => new LinkType(
                    new ValueTypeRegistry(),
                    new RemoteIdConverter($this->createMock(ItemLoaderInterface::class))
                ),
            )
        );

        $this->mapper = new LinkDataMapper($parameter);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToForms()
    {
        $linkValue = new LinkValue(
            array(
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            )
        );

        $forms = new ArrayIterator(
            array(
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
                'url' => $this->getForm('url'),
            )
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $this->assertEquals('url', $forms['link_type']->getData());
        $this->assertEquals('?suffix', $forms['link_suffix']->getData());
        $this->assertEquals('1', $forms['new_window']->getData());
        $this->assertEquals('http://www.google.com', $forms['url']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapDataToForms
     */
    public function testMapDataToFormsWithInvalidData()
    {
        $linkValue = 42;

        $forms = new ArrayIterator(
            array(
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            )
        );

        $this->mapper->mapDataToForms($linkValue, $forms);

        $this->assertNull($forms['link_type']->getData());
        $this->assertNull($forms['link_suffix']->getData());
        $this->assertNull($forms['new_window']->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToData()
    {
        $forms = new ArrayIterator(
            array(
                'link_type' => $this->getForm('link_type', 'url'),
                'link_suffix' => $this->getForm('link_suffix', '?suffix'),
                'new_window' => $this->getForm('new_window', '1'),
                'url' => $this->getForm('url', 'http://www.google.com'),
            )
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals(
            new LinkValue(
                array(
                    'linkType' => 'url',
                    'link' => 'http://www.google.com',
                    'linkSuffix' => '?suffix',
                    'newWindow' => true,
                )
            ),
            $data
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper::mapFormsToData
     */
    public function testMapFormsToDataWithInvalidFormData()
    {
        $forms = new ArrayIterator(
            array(
                'link_type' => $this->getForm('link_type'),
                'link_suffix' => $this->getForm('link_suffix'),
                'new_window' => $this->getForm('new_window'),
            )
        );

        $this->mapper->mapFormsToData($forms, $data);

        $this->assertEquals(new LinkValue(), $data);
    }
}
