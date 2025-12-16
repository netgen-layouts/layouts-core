<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller;

use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends BaseAbstractController
{
    public static function getSubscribedServices(): array
    {
        return [
            ...parent::getSubscribedServices(),
            'netgen_layouts.view.view_builder' => ViewBuilderInterface::class,
        ];
    }

    /**
     * Builds the view from provided value.
     *
     * @param array<string, mixed> $parameters
     */
    final protected function buildView(
        object $value,
        string $context = ViewInterface::CONTEXT_DEFAULT,
        array $parameters = [],
        ?Response $response = null,
    ): ViewInterface {
        /** @var \Netgen\Layouts\View\ViewBuilderInterface $viewBuilder */
        $viewBuilder = $this->container->get('netgen_layouts.view.view_builder');
        $view = $viewBuilder->buildView($value, $context, $parameters);

        $view->response = $response ?? new Response();

        return $view;
    }
}
