<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form\DataMapper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\Form\FormInterface;

abstract class DataMapperTestBase extends TestCase
{
    /**
     * @param mixed $formData
     */
    protected function getForm(string $formName, $formData = null): FormInterface
    {
        $config = new FormConfigBuilder($formName, null, $this->createMock(EventDispatcherInterface::class));

        $form = new Form($config);
        $form->setData($formData);

        return $form;
    }
}
