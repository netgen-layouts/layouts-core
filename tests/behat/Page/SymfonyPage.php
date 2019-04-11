<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Page;

use Behat\Mink\Session;
use Netgen\Layouts\Behat\Exception\PageException;
use Symfony\Component\Routing\RouterInterface;

abstract class SymfonyPage extends Page
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(Session $session, $minkParameters, RouterInterface $router)
    {
        parent::__construct($session, $minkParameters);

        $this->router = $router;
    }

    abstract public function getRouteName(): string;

    public function verifyRoute(array $requiredUrlParameters = []): void
    {
        $url = $this->getDriver()->getCurrentUrl();
        $parsedUrl = parse_url($url);

        if (!is_array($parsedUrl) || !array_key_exists('path', $parsedUrl)) {
            throw new PageException(sprintf('%s URL is not valid or does not contain a path', $url));
        }

        $matchedRoute = $this->router->match($parsedUrl['path']);

        $this->verifyStatusCode();
        $this->verifyRouteName($matchedRoute, $url);
        $this->verifyRouteParameters($requiredUrlParameters, $matchedRoute);
    }

    protected function getUrl(array $urlParameters = []): string
    {
        $path = $this->router->generate($this->getRouteName(), $urlParameters);

        return $this->makePathAbsolute($path);
    }

    private function makePathAbsolute(string $path): string
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/') . '/';

        return 0 !== mb_strpos($path, 'http') ? $baseUrl . ltrim($path, '/') : $path;
    }

    /**
     * @throws \Netgen\Layouts\Behat\Exception\PageException
     */
    private function verifyRouteName(array $matchedRoute, string $url): void
    {
        if ($matchedRoute['_route'] !== $this->getRouteName()) {
            throw new PageException(
                sprintf(
                    "Matched route '%s' does not match the expected route '%s' for URL '%s'",
                    $matchedRoute['_route'],
                    $this->getRouteName(),
                    $url
                )
            );
        }
    }

    /**
     * @throws \Netgen\Layouts\Behat\Exception\PageException
     */
    private function verifyRouteParameters(array $requiredUrlParameters, array $matchedRoute): void
    {
        foreach ($requiredUrlParameters as $key => $value) {
            if (!isset($matchedRoute[$key]) || $matchedRoute[$key] !== $value) {
                throw new PageException(
                    sprintf(
                        "Matched route does not match the expected parameter '%s'='%s' (%s found)",
                        $key,
                        $value,
                        $matchedRoute[$key] ?? 'null'
                    )
                );
            }
        }
    }
}
