<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Symfony\Component\Form\FormBuilderInterface;

final class ItemLinkMapper extends Mapper
{
    public function getFormType(): string
    {
        return ContentBrowserDynamicType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'item_types' => $parameterDefinition->getOption('value_types'),
        ];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition): void
    {
        $form->setDataMapper(new ItemLinkDataMapper());
    }
}
