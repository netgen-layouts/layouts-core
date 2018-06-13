<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Validator;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Layout\Type\LayoutTypeInterface;
use Symfony\Component\Validator\Constraints;

final class LayoutValidator extends Validator
{
    /**
     * Validates the provided layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct $layoutCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct)
    {
        $layoutName = is_string($layoutCreateStruct->name) ?
            trim($layoutCreateStruct->name) :
            $layoutCreateStruct->name;

        $this->validate(
            $layoutName,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'name'
        );

        $layoutDescription = is_string($layoutCreateStruct->description) ?
            trim($layoutCreateStruct->description) :
            $layoutCreateStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
            );
        }

        $this->validate(
            $layoutCreateStruct->layoutType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => LayoutTypeInterface::class]),
            ],
            'layoutType'
        );

        $this->validateLocale($layoutCreateStruct->mainLocale, 'mainLocale');

        if ($layoutCreateStruct->shared !== null) {
            $this->validate(
                $layoutCreateStruct->shared,
                [
                    new Constraints\Type(['type' => 'bool']),
                ],
                'shared'
            );
        }
    }

    /**
     * Validates the provided layout update struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct $layoutUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutUpdateStruct(LayoutUpdateStruct $layoutUpdateStruct)
    {
        $layoutName = is_string($layoutUpdateStruct->name) ?
            trim($layoutUpdateStruct->name) :
            $layoutUpdateStruct->name;

        if ($layoutName !== null) {
            $this->validate(
                $layoutName,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                ],
                'name'
            );
        }

        $layoutDescription = is_string($layoutUpdateStruct->description) ?
            trim($layoutUpdateStruct->description) :
            $layoutUpdateStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
            );
        }
    }

    /**
     * Validates the provided layout create struct.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct $layoutCopyStruct
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutCopyStruct(LayoutCopyStruct $layoutCopyStruct)
    {
        $layoutName = is_string($layoutCopyStruct->name) ?
            trim($layoutCopyStruct->name) :
            $layoutCopyStruct->name;

        $this->validate(
            $layoutName,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'name'
        );

        $layoutDescription = is_string($layoutCopyStruct->description) ?
            trim($layoutCopyStruct->description) :
            $layoutCopyStruct->description;

        if ($layoutDescription !== null) {
            $this->validate(
                $layoutDescription,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
            );
        }
    }

    /**
     * Validates zone mappings for changing the provided layout type.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     * @param \Netgen\BlockManager\Layout\Type\LayoutTypeInterface $targetLayoutType
     * @param array $zoneMappings
     * @param bool $preserveSharedZones
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationException If the validation failed
     */
    public function validateChangeLayoutType(Layout $layout, LayoutTypeInterface $targetLayoutType, array $zoneMappings = [], $preserveSharedZones = true)
    {
        $seenZones = [];
        foreach ($zoneMappings as $newZone => $oldZones) {
            if (!$targetLayoutType->hasZone($newZone)) {
                throw ValidationException::validationFailed(
                    'zoneMappings',
                    sprintf(
                        'Zone "%s" does not exist in "%s" layout type.',
                        $newZone,
                        $targetLayoutType->getIdentifier()
                    )
                );
            }

            if (!is_array($oldZones)) {
                throw ValidationException::validationFailed(
                    'zoneMappings',
                    sprintf(
                        'The list of mapped zones for "%s" zone must be an array.',
                        $newZone
                    )
                );
            }

            foreach ($oldZones as $oldZone) {
                if (in_array($oldZone, $seenZones, true)) {
                    throw ValidationException::validationFailed(
                        'zoneMappings',
                        sprintf(
                            'Zone "%s" is specified more than once.',
                            $oldZone
                        )
                    );
                }

                $seenZones[] = $oldZone;

                if (!$layout->hasZone($oldZone)) {
                    throw ValidationException::validationFailed(
                        'zoneMappings',
                        sprintf(
                            'Zone "%s" does not exist in specified layout.',
                            $oldZone
                        )
                    );
                }
            }

            if ($preserveSharedZones && count($oldZones) > 1) {
                foreach ($oldZones as $oldZone) {
                    if ($layout->getZone($oldZone, true)->hasLinkedZone()) {
                        throw ValidationException::validationFailed(
                            'zoneMappings',
                            sprintf(
                                'When preserving shared layout zones, mapping for zone "%s" needs to be 1:1.',
                                $newZone
                            )
                        );
                    }
                }
            }
        }
    }
}
