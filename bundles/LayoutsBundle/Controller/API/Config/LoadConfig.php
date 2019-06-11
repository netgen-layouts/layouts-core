<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Controller\API\Config;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\Serializer\Values\ArrayValue;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class LoadConfig extends AbstractController
{
    /**
     * @var \Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var string
     */
    private $csrfTokenId;

    public function __construct(
        ConfigurationInterface $configuration,
        CsrfTokenManagerInterface $csrfTokenManager,
        string $csrfTokenId
    ) {
        $this->configuration = $configuration;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
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
                'csrf_token' => $this->csrfTokenManager->getToken($this->csrfTokenId)->getValue(),
            ]
        );
    }
}
