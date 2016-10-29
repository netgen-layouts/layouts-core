<?php

namespace Netgen\BlockManager\Tests\Core\Service\TransactionRollback;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\CollectionService;
use Netgen\BlockManager\Core\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Service\Mapper\CollectionMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use Netgen\BlockManager\Core\Service\Mapper\BlockMapper;
use Netgen\BlockManager\Core\Service\Mapper\LayoutMapper;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCaseTrait;
use PHPUnit\Framework\TestCase;

abstract class TransactionRollbackTest extends TestCase
{
    use TestCaseTrait;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Prepares the prerequisites for using services in tests.
     */
    public function preparePersistence()
    {
        $this->persistenceHandler = $this->createMock(Handler::class);
    }

    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(LayoutValidator $validator, LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        return new LayoutService(
            $validator,
            $this->createLayoutMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(
        BlockValidator $validator,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        return new BlockService(
            $validator,
            $this->createBlockMapper(),
            $this->createParameterMapper(),
            $this->persistenceHandler,
            $layoutTypeRegistry,
            $blockDefinitionRegistry
        );
    }

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\CollectionService
     */
    protected function createCollectionService(
        CollectionValidator $validator,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        return new CollectionService(
            $validator,
            $this->createCollectionMapper(),
            $this->createParameterMapper(),
            $this->persistenceHandler,
            $queryTypeRegistry
        );
    }

    /**
     * Creates a layout resolver service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected function createLayoutResolverService(LayoutResolverValidator $validator)
    {
        return new LayoutResolverService(
            $validator,
            $this->createLayoutResolverMapper(),
            $this->persistenceHandler
        );
    }

    /**
     * Creates the block mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\BlockMapper
     */
    protected function createBlockMapper()
    {
        return $this->createMock(BlockMapper::class);
    }

    /**
     * Creates the layout mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutMapper
     */
    protected function createLayoutMapper()
    {
        return $this->createMock(LayoutMapper::class);
    }

    /**
     * Creates the collection mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected function createCollectionMapper()
    {
        return $this->createMock(CollectionMapper::class);
    }

    /**
     * Creates the layout resolver mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    protected function createLayoutResolverMapper()
    {
        return $this->createMock(LayoutResolverMapper::class);
    }

    /**
     * Creates the parameter mapper under test.
     *
     * @return \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected function createParameterMapper()
    {
        $parameterMapper = $this->createMock(ParameterMapper::class);

        $parameterMapper
            ->expects($this->any())
            ->method('serializeValues')
            ->will($this->returnValue(array()));

        return $parameterMapper;
    }
}
