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

    /**
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager
     * @param string $csrfTokenId
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager, $csrfTokenId)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->csrfTokenId = $csrfTokenId;
    }

    /**
     * Returns the general config.
     *
     * @return \Netgen\BlockManager\Serializer\Values\Value
     */
    public function __invoke()
    {
        return new Value(['csrf_token' => $this->csrfTokenManager->getToken($this->csrfTokenId)->getValue()]);
    }
}
