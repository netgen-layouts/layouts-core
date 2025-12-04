<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type\DataMapper;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormInterface;
use Traversable;

use function sprintf;

final class ParameterValuesDataMapper implements DataMapperInterface
{
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (!$viewData instanceof ParameterStruct) {
            return;
        }

        $parametersForm = $this->findParametersForm($forms);

        foreach ($parametersForm as $form) {
            /** @var \Netgen\Layouts\Parameters\ParameterDefinition $parameterDefinition */
            $parameterDefinition = $form->getConfig()->getOption('ngl_parameter_definition');

            if ($parameterDefinition->isCompound && $form->has('_self')) {
                $form->get('_self')->setData($viewData->getParameterValue($form->getName()));

                foreach ($form as $childForm) {
                    if ($childForm->getName() === '_self') {
                        continue;
                    }

                    $childForm->setData($viewData->getParameterValue($childForm->getName()));
                }

                continue;
            }

            $form->setData($viewData->getParameterValue($form->getName()));
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        if (!$viewData instanceof ParameterStruct) {
            return;
        }

        $parametersForm = $this->findParametersForm($forms);

        foreach ($parametersForm as $form) {
            /** @var \Netgen\Layouts\Parameters\ParameterDefinition $parameterDefinition */
            $parameterDefinition = $form->getConfig()->getOption('ngl_parameter_definition');

            if ($parameterDefinition->isCompound && $form->has('_self')) {
                $viewData->setParameterValue($form->getName(), (bool) $form->get('_self')->getData());

                foreach ($form as $childForm) {
                    if ($childForm->getName() === '_self') {
                        continue;
                    }

                    $viewData->setParameterValue($childForm->getName(), $childForm->getData());
                }

                continue;
            }

            $viewData->setParameterValue($form->getName(), $form->getData());
        }
    }

    /**
     * @param \Traversable<\Symfony\Component\Form\FormInterface> $forms
     */
    private function findParametersForm(Traversable $forms): FormInterface
    {
        foreach ($forms as $form) {
            while ($form instanceof FormInterface && !$this->isParametersTypeForm($form)) {
                $form = $form->getParent();
            }

            if ($form instanceof FormInterface) {
                return $form;
            }
        }

        throw new RuntimeException(
            sprintf('Unable to find the form with "%s" type.', ParametersType::class),
        );
    }

    private function isParametersTypeForm(FormInterface $form): bool
    {
        return $form->getConfig()->getType()->getInnerType() instanceof ParametersType;
    }
}
