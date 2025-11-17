<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Controller\API;

use Coduo\PHPMatcher\PHPMatcher;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Zenstruck\Assert;
use Zenstruck\Browser\KernelBrowser as BaseKernelBrowser;

use function file_get_contents;
use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

final class KernelBrowser extends BaseKernelBrowser
{
    public function assertJsonIs(string $expected): self
    {
        $decoded = json_decode(
            (string) file_get_contents(__DIR__ . '/_responses/expected/' . $expected . '.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        $matcher = new PHPMatcher();
        $matchResult = $matcher->match($this->json()->decoded(), $decoded);

        if (!$matchResult) {
            $differ = new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n", false));
            $diff = $differ->diff(
                json_encode($decoded, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
                (string) $this->json(),
            );

            Assert::fail($matcher->error() . PHP_EOL . $diff);
        }

        return $this;
    }
}
