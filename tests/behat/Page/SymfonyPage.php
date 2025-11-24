<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage as BaseSymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Uri\Rfc3986\Uri;

use function sprintf;
use function str_contains;

abstract class SymfonyPage extends BaseSymfonyPage
{
    public function verifyUrlFragment(string $fragment): void
    {
        $parsedUrl = new Uri($this->getDriver()->getCurrentUrl());

        if ($parsedUrl->getFragment() === null) {
            throw new UnexpectedPageException(sprintf('%s URL is not valid or does not contain a fragment', $this->getDriver()->getCurrentUrl()));
        }

        if (str_contains($parsedUrl->getFragment(), $fragment)) {
            return;
        }

        throw new UnexpectedPageException(sprintf('Expected to have "%s" fragment but found "%s" instead', $fragment, $parsedUrl->getFragment()));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function waitForElement(int $timeout, string $elementName, array $parameters = [], bool $waitForRemoval = false): void
    {
        $this->getDocument()->waitFor(
            $timeout,
            function () use ($elementName, $parameters, $waitForRemoval): bool {
                $hasElement = $this->hasElement($elementName, $parameters);

                return $waitForRemoval ? !$hasElement : $hasElement;
            },
        );
    }
}
