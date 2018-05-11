<?php

namespace Netgen\BlockManager\Behat\Page;

use Behat\Mink\Session;
use Netgen\BlockManager\Behat\Exception\PageException;
use Symfony\Component\Routing\RouterInterface;

abstract class SymfonyPage extends Page
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @param \Behat\Mink\Session $session
     * @param array $parameters
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(Session $session, array $parameters, RouterInterface $router)
    {
        parent::__construct($session, $parameters);

        $this->router = $router;
    }

    /**
     * @return string
     */
    abstract public function getRouteName();

    public function verifyRoute(array $requiredUrlParameters = [])
    {
        $url = $this->getDriver()->getCurrentUrl();
        $path = parse_url($url)['path'];

        $path = preg_replace('#^/app(_[\w]+)?\.php/#', '/', $path);
        $matchedRoute = $this->router->match($path);

        $this->verifyStatusCode();
        $this->verifyRouteName($matchedRoute, $url);
        $this->verifyRouteParameters($requiredUrlParameters, $matchedRoute);
    }

    protected function getUrl(array $urlParameters = [])
    {
        $path = $this->router->generate($this->getRouteName(), $urlParameters);

        return $this->makePathAbsolute($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function makePathAbsolute($path)
    {
        $baseUrl = rtrim($this->getParameter('base_url'), '/') . '/';

        return 0 !== mb_strpos($path, 'http') ? $baseUrl . ltrim($path, '/') : $path;
    }

    /**
     * @param array $matchedRoute
     * @param string $url
     *
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    private function verifyRouteName(array $matchedRoute, $url)
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
     * @param array $requiredUrlParameters
     * @param array $matchedRoute
     *
     * @throws \Netgen\BlockManager\Behat\Exception\PageException
     */
    private function verifyRouteParameters(array $requiredUrlParameters, array $matchedRoute)
    {
        foreach ($requiredUrlParameters as $key => $value) {
            if (!isset($matchedRoute[$key]) || $matchedRoute[$key] !== $value) {
                throw new PageException(
                    sprintf(
                        "Matched route does not match the expected parameter '%s'='%s' (%s found)",
                        $key,
                        $value,
                        isset($matchedRoute[$key]) ? $matchedRoute[$key] : 'null'
                    )
                );
            }
        }
    }
}
