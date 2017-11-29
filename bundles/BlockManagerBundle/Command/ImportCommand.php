<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Exception;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Input\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to import Netgen Layouts entities.
 */
final class ImportCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\Importer
     */
    private $importer;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ngbm:import')
            ->setDescription('Imports Netgen Layouts entities')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of the entity to import')
            ->addArgument('file', InputArgument::REQUIRED, 'JSON file to import')
            ->setHelp('The command <info>%command.name%</info> imports Netgen Layouts entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $type = $input->getArgument('type');
        $file = $input->getArgument('file');

        $data = file_get_contents($file);

        switch ($type) {
            case 'layout':
                $errorCount = $this->importLayouts($data);
                break;
            default:
                $this->io->error(sprintf("Unknown entity type '%s'", $type));

                return 1;
        }

        $errorCount > 0 ?
            $this->io->caution('Import completed with errors.') :
            $this->io->success('Import completed successfully.');

        return 0;
    }

    /**
     * Import new layouts from the given $data string.
     *
     * @param string $data
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If given $data string is malformed
     *
     * @return int The count of errors
     */
    private function importLayouts($data)
    {
        $errorCount = 0;

        $layouts = $this->decode($data);

        foreach ($layouts as $index => $layoutData) {
            try {
                $layout = $this->importer->importLayout($layoutData);

                $this->io->note(sprintf('Imported layout #%d into layout ID %d', $index, $layout->getId()));
            } catch (Exception $e) {
                $this->io->error(sprintf('Could not import layout with ID #%d', $index));
                $this->io->section('Exception stack:');
                $this->renderExceptionStack($e);
                $this->io->newLine();

                ++$errorCount;
            }
        }

        return $errorCount;
    }

    /**
     * Renders all stacked exception messages for the given $exception.
     *
     * @param \Exception $exception
     * @param int $number
     */
    private function renderExceptionStack(Exception $exception, $number = 0)
    {
        $this->io->writeln(sprintf(' #%d:', $number));
        $exceptionClass = get_class($exception);
        $this->io->writeln(sprintf('  - exception: %s', $exceptionClass));
        $this->io->writeln(sprintf('  - file: %s', $exception->getFile()));
        $this->io->writeln(sprintf('  - line: %d', $exception->getLine()));
        $this->io->writeln(sprintf('  - message: %s', $exception->getMessage()));

        $previous = $exception->getPrevious();

        if ($previous instanceof Exception) {
            $this->renderExceptionStack($exception, $number + 1);
        }
    }

    /**
     * Decode given JSON $data string.
     *
     * @param string $data
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If given $data string could not be decoded
     *
     * @return mixed
     */
    private function decode($data)
    {
        $value = json_decode($data, true);

        if (!is_array($value)) {
            $type = gettype($value);
            throw new RuntimeException(
                sprintf('Data is malformed, expected array, got %s', $type)
            );
        }

        return $value;
    }
}
