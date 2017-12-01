<?php

namespace Netgen\BlockManager\Tests\TestCase;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class FormTestCase extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilder
     */
    protected $builder;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\Form\FormTypeInterface
     */
    protected $formType;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $validatorMock;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $factory;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validatorMock
            ->expects($this->any())
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addTypes($this->getTypes())
            ->addTypeExtension(new FormTypeValidatorExtension($this->validatorMock));

        foreach ($this->getTypeExtensions() as $typeExtension) {
            $this->factory->addTypeExtension($typeExtension);
        }

        $this->factory = $this->factory->getFormFactory();

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    abstract public function getMainType();

    /**
     * @return \Symfony\Component\Form\FormTypeExtensionInterface[]
     */
    public function getTypeExtensions()
    {
        return array();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        return array();
    }
}
