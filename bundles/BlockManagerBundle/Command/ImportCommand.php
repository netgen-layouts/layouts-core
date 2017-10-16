<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\Transfer\Input\Importer;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Importer command imports Block Manager entities.
 */
class ImportCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\Importer
     */
    private $importer;

    /**
     * @param \Netgen\BlockManager\Transfer\Input\Importer $importer
     */
    public function __construct(Importer $importer) {
        $this->importer = $importer;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('netgen_block_manager:import')
            ->setDescription('Imports Block Manager entities')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of the entity to import')
            ->addArgument('file', InputArgument::REQUIRED, 'JSON file to import')
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> exports Block Manager entities.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $file = $input->getArgument('file');

        $data = file_get_contents($file);

        switch ($type) {
            case 'layout':
                $layout = $this->importer->importLayout($data);
                $output->writeln("Imported into Layout ID#{$layout->getId()}");
                break;
            default:
                throw new RuntimeException("Unhandled type '{$type}'");
        }

        $output->writeln('Finished.');
    }
}
