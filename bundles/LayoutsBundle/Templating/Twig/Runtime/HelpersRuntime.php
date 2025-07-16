<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

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
use Netgen\Layouts\Utils\BackwardsCompatibility\Locales;
use Netgen\Layouts\Utils\FlagGenerator;
use Ramsey\Uuid\Uuid;
use Throwable;
use Twig\Environment;
use Twig\Extension\CoreExtension;

use function array_unshift;
use function is_string;
use function method_exists;
use function twig_date_converter;

final class HelpersRuntime
{
    private LayoutService $layoutService;

    private LayoutResolverService $layoutResolverService;

    private ValueTypeRegistry $valueTypeRegistry;

    public function __construct(
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService,
        ValueTypeRegistry $valueTypeRegistry
    ) {
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
        $this->valueTypeRegistry = $valueTypeRegistry;
    }

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

            return $layout->getName();
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the rule group for the specified rule ID.
     */
    public function getRuleGroup(string $ruleId): RuleGroup
    {
        $rule = $this->layoutResolverService->loadRule(Uuid::fromString($ruleId));

        return $this->layoutResolverService->loadRuleGroup($rule->getRuleGroupId());
    }

    /**
     * Returns the rule group name for specified rule group ID.
     */
    public function getRuleGroupName(string $ruleGroupId): string
    {
        try {
            $ruleGroup = $this->layoutResolverService->loadRuleGroup(Uuid::fromString($ruleGroupId));

            return $ruleGroup->getName();
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the the name of the value type that the specified item wraps.
     */
    public function getValueTypeName(CmsItemInterface $cmsItem): string
    {
        try {
            return $this->valueTypeRegistry->getValueType($cmsItem->getValueType())->getName();
        } catch (ItemException $t) {
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
        } catch (Throwable $t) {
            return $countryCode;
        }
    }

    /**
     * @param \DateTimeInterface|string $dateTime
     */
    public function formatDateTime(Environment $twig, $dateTime, string $dateFormat = 'medium', string $timeFormat = 'medium'): string
    {
        $coreExtension = $twig->getExtension(CoreExtension::class);

        $dateTime = method_exists($coreExtension, 'convertDate') ?
            $coreExtension->convertDate($dateTime) :
            twig_date_converter($twig, $dateTime);

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
        $group = $this->layoutResolverService->loadRuleGroup($rule->getRuleGroupId());
        $parentGroups = [$group];

        $parentId = $group->getParentId();

        while ($parentId !== null) {
            $group = $this->layoutResolverService->loadRuleGroup($parentId);
            array_unshift($parentGroups, $group);
            $parentId = $group->getParentId();
        }

        return $parentGroups;
    }
}
