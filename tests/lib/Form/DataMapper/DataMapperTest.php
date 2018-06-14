<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form\DataMapper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormInterface;

abstract class DataMapperTest extends TestCase
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $dispatcherMock;

    public function setUp(): void
    {
        $this->dispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @param string $formName
     * @param mixed $formData
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getForm(string $formName, $formData = null): FormInterface
    {
        $config = new FormConfigBuilder($formName, null, $this->dispatcherMock);

        $form = new Form($config);
        $form->setData($formData);

        return $form;
    }
}
