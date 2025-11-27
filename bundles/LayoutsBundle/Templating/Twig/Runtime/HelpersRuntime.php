<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use DateTimeInterface;
use IntlDateFormatter;
use IntlTimeZone;
use Locale;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Utils\FlagGenerator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Intl\Locales;
use Throwable;
use Twig\Environment;
use Twig\Extension\CoreExtension;

use function array_unshift;
use function is_string;

final class HelpersRuntime
{
    public function __construct(
        private LayoutService $layoutService,
        private LayoutResolverService $layoutResolverService,
        private ValueTypeRegistry $valueTypeRegistry,
    ) {}

    /**
     * Returns the locale name in specified locale.
     *
     * If $displayLocale is specified, name translated in that locale will be returned.
     */
    public function getLocaleName(string $locale, ?string $displayLocale = null): string
    {
        return Locales::getName($locale, $displayLocale);
    }

    /**
     * Returns the layout name for specified layout ID.
     */
    public function getLayoutName(string $layoutId): string
    {
        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString($layoutId));

            return $layout->name;
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Returns the rule group for the specified rule ID.
     */
    public function getRuleGroup(string $ruleId): RuleGroup
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString($ruleId));

        return $this->layoutResolverService->loadRuleGroup($rule->ruleGroupId);
    }

    /**
     * Returns the rule group name for specified rule group ID.
     */
    public function getRuleGroupName(string $ruleGroupId): string
    {
        try {
            $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString($ruleGroupId));

            return $ruleGroup->name;
        } catch (Throwable) {
            return '';
        }
    }

    /**
     * Returns the the name of the value type that the specified item wraps.
     */
    public function getValueTypeName(CmsItemInterface $cmsItem): string
    {
        try {
            return $this->valueTypeRegistry->getValueType($cmsItem->valueType)->name;
        } catch (ItemException) {
            return '';
        }
    }

    /**
     * Returns the country flag as an emoji string for provided country code.
     *
     * If the flag cannot be generated, the country code is returned as is.
     */
    public function getCountryFlag(string $countryCode): string
    {
        try {
            return FlagGenerator::fromCountryCode($countryCode);
        } catch (Throwable) {
            return $countryCode;
        }
    }

    public function formatDateTime(Environment $twig, DateTimeInterface|string $dateTime, string $dateFormat = 'medium', string $timeFormat = 'medium'): string
    {
        $coreExtension = $twig->getExtension(CoreExtension::class);

        $dateTime = $coreExtension->convertDate($dateTime);

        $formatValues = [
            'none' => IntlDateFormatter::NONE,
            'short' => IntlDateFormatter::SHORT,
            'medium' => IntlDateFormatter::MEDIUM,
            'long' => IntlDateFormatter::LONG,
            'full' => IntlDateFormatter::FULL,
        ];

        $formatter = IntlDateFormatter::create(
            Locale::getDefault(),
            $formatValues[$dateFormat],
            $formatValues[$timeFormat],
            IntlTimeZone::createTimeZone($dateTime->getTimezone()->getName()),
        );

        $formattedValue = $formatter->format($dateTime->getTimestamp());

        return is_string($formattedValue) ? $formattedValue : '';
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup[]
     */
    public function getParentRuleGroups(Rule $rule): array
    {
        $group = $this->layoutResolverService->loadRuleGroup($rule->ruleGroupId);
        $parentGroups = [$group];

        $parentId = $group->parentId;

        while ($parentId !== null) {
            $group = $this->layoutResolverService->loadRuleGroup($parentId);
            array_unshift($parentGroups, $group);
            $parentId = $group->parentId;
        }

        return $parentGroups;
    }
}
