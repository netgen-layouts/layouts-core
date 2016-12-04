<?php

namespace Netgen\BlockManager\Tests\Parameters\Form\Type\DataMapper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;

abstract class DataMapperTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcherMock;

    public function setUp()
    {
        $this->dispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @param string $formName
     * @param mixed $formData
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getForm($formName, $formData = null)
    {
        $config = new FormConfigBuilder($formName, null, $this->dispatcherMock);

        $form = new Form($config);
        $form->setData($formData);

        return $form;
    }
}
