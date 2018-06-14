<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Config;

use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Controller;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class LoadConfig extends Controller
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var string
     */
    private $csrfTokenId;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, string $csrfTokenId)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
    }

    /**
     * Returns the general config.
     */
    public function __invoke(): Value
    {
        return new Value(['csrf_token' => $this->csrfTokenManager->getToken($this->csrfTokenId)->getValue()]);
    }
}
