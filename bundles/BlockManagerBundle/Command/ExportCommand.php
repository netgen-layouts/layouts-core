<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Transfer\Serializer;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Export Block Manager entities.
 */
class ExportCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Transfer\Serializer
     */
    private $serializer;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * {@inheritdoc}
     *
     * @param \Netgen\BlockManager\Transfer\Serializer $serializer
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     */
    public function __construct(
        Serializer $serializer,
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService
    ) {
        $this->serializer = $serializer;
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('netgen_block_manager:export')
            ->setDescription('Exports Block Manager entities')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of the entity to export')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the entity to export')
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> exports Block Manager entities.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');

        switch ($type) {
            case 'layout':
                $value = $this->layoutService->loadLayout($id);
                $hash = $this->serializer->serializeLayout($value);
                break;
            case 'rule':
                $value = $this->layoutResolverService->loadRule($id);
                $hash = $this->serializer->serializeRule($value);
                break;
            default:
                throw new RuntimeException("Unhandled type '{$type}'");
        }

        $output->writeln(json_encode($hash, JSON_PRETTY_PRINT));
    }
}
