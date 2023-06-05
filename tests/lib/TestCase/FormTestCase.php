<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class FormTestCase extends TestCase
{
    protected FormTypeInterface $formType;

    protected FormFactoryInterface $factory;

    private MockObject $validatorMock;

    private MockObject $dispatcherMock;

    private FormBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validatorMock
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $factoryBuilder = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addTypes($this->getTypes())
            ->addTypeExtension(new FormTypeValidatorExtension($this->validatorMock));

        foreach ($this->getTypeExtensions() as $typeExtension) {
            $factoryBuilder->addTypeExtension($typeExtension);
        }

        $this->factory = $factoryBuilder->getFormFactory();

        $this->dispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $this->dispatcherMock, $this->factory);
    }

    abstract protected function getMainType(): FormTypeInterface;

    /**
     * @return \Symfony\Component\Form\FormTypeExtensionInterface[]
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    protected function getTypes(): array
    {
        return [];
    }
}
