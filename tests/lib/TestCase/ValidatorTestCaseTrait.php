<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\TestCase;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorTestCaseTrait
{
    final protected function createValidator(
        ?LayoutService $layoutService = null,
        ?LayoutResolverService $layoutResolverService = null,
        ?CmsItemLoaderInterface $cmsItemLoader = null,
    ): ValidatorInterface {
        $layoutService ??= self::createStub(LayoutService::class);
        $layoutResolverService ??= self::createStub(LayoutResolverService::class);
        $cmsItemLoader ??= self::createStub(CmsItemLoaderInterface::class);

        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(
                new ValidatorFactory(
                    $layoutService,
                    $layoutResolverService,
                    $cmsItemLoader,
                    $this->getConstraintValidators(),
                ),
            )
            ->getValidator();
    }

    /**
     * @return array<string, \Symfony\Component\Validator\ConstraintValidatorInterface>
     */
    protected function getConstraintValidators(): array
    {
        return [];
    }
}
