<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

final class LinkMapper extends Mapper
{
    public function getFormType(): string
    {
        return LinkType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'label' => false,
            'value_types' => $parameterDefinition->getOption('value_types'),
        ];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition): void
    {
        $form->setDataMapper(new LinkDataMapper($parameterDefinition));
    }
}
