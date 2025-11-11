<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

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
    final protected FormTypeInterface $formType;

    final protected FormFactoryInterface $factory;

    private FormBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formType = $this->getMainType();

        $validatorMock = $this->createMock(ValidatorInterface::class);
        $validatorMock
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $factoryBuilder = Forms::createFormFactoryBuilder()
            ->addType($this->formType)
            ->addTypes($this->getTypes())
            ->addTypeExtension(new FormTypeValidatorExtension($validatorMock));

        foreach ($this->getTypeExtensions() as $typeExtension) {
            $factoryBuilder->addTypeExtension($typeExtension);
        }

        $this->factory = $factoryBuilder->getFormFactory();

        $dispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->builder = new FormBuilder(null, null, $dispatcherMock, $this->factory);
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
