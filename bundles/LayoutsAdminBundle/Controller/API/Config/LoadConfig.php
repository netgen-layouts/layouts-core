<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\API\Config;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\ArrayValue;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class LoadConfig extends AbstractController
{
    private ConfigurationInterface $configuration;

    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        ConfigurationInterface $configuration,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->configuration = $configuration;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Returns the general config.
     */
    public function __invoke(): ArrayValue
    {
        $this->denyAccessUnlessGranted('nglayouts:api:read');

        return new ArrayValue(
            [
                'automatic_cache_clear' => $this->configuration->getParameter('app.automatic_cache_clear'),
                'csrf_token' => $this->csrfTokenManager->getToken(
                    $this->configuration->getParameter('app.csrf_token_id'),
                )->getValue(),
            ],
        );
    }
}
