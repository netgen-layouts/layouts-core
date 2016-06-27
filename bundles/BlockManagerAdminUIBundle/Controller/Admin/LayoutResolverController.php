<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\Admin;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    protected $targetTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface
     */
    protected $conditionTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface $targetTypeRegistry
     * @param \Netgen\BlockManager\Layout\Resolver\Registry\ConditionTypeRegistryInterface $conditionTypeRegistry
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        TargetTypeRegistryInterface $targetTypeRegistry,
        ConditionTypeRegistryInterface $conditionTypeRegistry
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
    }

    /**
     * Displays the index page of layout resolver admin interface.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render(
            'NetgenBlockManagerAdminUIBundle:admin/layout_resolver:index.html.twig',
            array(
                'rules' => $this->layoutResolverService->loadRules(),
                'target_types' => $this->targetTypeRegistry->getTargetTypes(),
                'condition_types' => $this->conditionTypeRegistry->getConditionTypes(),
            )
        );
    }
}
