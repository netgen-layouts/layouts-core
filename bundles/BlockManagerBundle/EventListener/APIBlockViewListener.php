<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;

class APIBlockViewListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     */
    public function __construct(
        BlockService $blockService,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        FormFactoryInterface $formFactory
    ) {
        $this->blockService = $blockService;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->formFactory = $formFactory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Adds an inline edit form to API block views.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface || $view->getContext() !== ViewInterface::CONTEXT_API) {
            return;
        }

        $block = $view->getBlock();
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition($block->getDefinitionIdentifier());

        if (!$blockDefinition->getConfiguration()->hasForm('inline_edit')) {
            return;
        }

        $updateStruct = $this->blockService->newBlockUpdateStruct();
        $updateStruct->setParameters($block->getParameters());
        $updateStruct->viewType = $block->getViewType();
        $updateStruct->name = $block->getName();

        $form = $this->formFactory->create(
            $blockDefinition->getConfiguration()->getForm('inline_edit'),
            $updateStruct,
            array('blockDefinition' => $blockDefinition)
        );

        $event->getParameterBag()->set('form', $form->createView());
    }
}
