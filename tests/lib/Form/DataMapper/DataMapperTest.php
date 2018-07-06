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
     * @param string $formName
     * @param mixed $formData
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function getForm(string $formName, $formData = null): FormInterface
    {
        $config = new FormConfigBuilder($formName, null, $this->createMock(EventDispatcherInterface::class));

        $form = new Form($config);
        $form->setData($formData);

        return $form;
    }
}
