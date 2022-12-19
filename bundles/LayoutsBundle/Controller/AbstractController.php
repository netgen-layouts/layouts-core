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
            'netgen_layouts.view.view_builder' => ViewBuilderInterface::class,
        ] + parent::getSubscribedServices();
    }

    /**
     * Builds the view from provided value.
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     */
    protected function buildView(
        $value,
        string $context = ViewInterface::CONTEXT_DEFAULT,
        array $parameters = [],
        ?Response $response = null
    ): ViewInterface {
        /** @var \Netgen\Layouts\View\ViewBuilderInterface $viewBuilder */
        $viewBuilder = $this->container->get('netgen_layouts.view.view_builder');
        $view = $viewBuilder->buildView($value, $context, $parameters);

        $view->setResponse($response instanceof Response ? $response : new Response());

        return $view;
    }
}
