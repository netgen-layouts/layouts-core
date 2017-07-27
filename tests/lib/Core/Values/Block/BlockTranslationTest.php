<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\Core\Values\Block\BlockTranslation;
use Netgen\BlockManager\Exception\Core\ParameterException;
use PHPUnit\Framework\TestCase;

class BlockTranslationTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::isMainTranslation
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::hasParameter
     */
    public function testSetDefaultProperties()
    {
        $translation = new BlockTranslation();

        $this->assertNull($translation->getLocale());
        $this->assertNull($translation->isMainTranslation());
        $this->assertEquals(array(), $translation->getParameters());
        $this->assertFalse($translation->hasParameter('test'));

        try {
            $translation->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::isMainTranslation
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\BlockTranslation::hasParameter
     */
    public function testSetProperties()
    {
        $translation = new BlockTranslation(
            array(
                'locale' => 'en',
                'isMainTranslation' => true,
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        $this->assertEquals('en', $translation->getLocale());
        $this->assertTrue($translation->isMainTranslation());
        $this->assertEquals('some_value', $translation->getParameter('some_param'));
        $this->assertFalse($translation->hasParameter('test'));
        $this->assertTrue($translation->hasParameter('some_param'));

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $translation->getParameters()
        );

        try {
            $translation->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }
    }
}
