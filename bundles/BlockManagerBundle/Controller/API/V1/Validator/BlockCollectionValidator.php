<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator;

use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints;

class BlockCollectionValidator extends Validator
{
    /**
     * Validates block creation parameters from the request.
     *
     * @param \Netgen\BlockManager\API\Values\Block\CollectionReference $collectionReference
     * @param int $newType
     * @param string $queryType
     *
     * @throws \Netgen\BlockManager\Exception\Validation\ValidationFailedException If validation failed
     */
    public function validateChangeCollectionType(CollectionReference $collectionReference, $newType, $queryType)
    {
        $this->validate(
            $newType,
            array(
                new Constraints\NotBlank(),
                new Constraints\Choice(
                    array(
                        'choices' => array(
                            Collection::TYPE_MANUAL,
                            Collection::TYPE_DYNAMIC,
                        ),
                        'strict' => true,
                    )
                ),
            ),
            'new_type'
        );

        if ($newType === Collection::TYPE_DYNAMIC) {
            $blockDefinition = $collectionReference->getBlock()->getDefinition();

            $collectionIdentifier = $collectionReference->getIdentifier();
            if ($blockDefinition->getConfig()->hasCollection($collectionIdentifier)) {
                $collectionConfig = $blockDefinition->getConfig()->getCollection($collectionIdentifier);

                if (!$collectionConfig->isValidQueryType($queryType)) {
                    throw new ValidationFailedException(
                        sprintf(
                            'Query type "%s" is not allowed in selected block.',
                            $queryType
                        )
                    );
                }
            }
        }
    }
}
