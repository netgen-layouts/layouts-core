<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\Transfer\Input\Importer;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

/**
 * Command to import Netgen Layouts entities.
 */
final class ImportCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\Importer
     */
    private $importer;

    public function __construct(Importer $importer) {
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
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> imports Netgen Layouts entities.
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
                $this->importLayouts($data, $output);
                break;
            default:
                throw new RuntimeException("Unhandled type '{$type}'");
        }

        $output->writeln('Finished.');
    }

    /**
     * Import new layouts from the given $data string.
     *
     * @param string $data
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If given $data string is malformed
     */
    private function importLayouts($data, OutputInterface $output)
    {
        $layouts = $this->decode($data);

        if (!is_array($layouts)) {
            $type = gettype($layouts);
            throw new RuntimeException(
                "Data is malformed, expected array, got {$type}"
            );
        }

        foreach ($layouts as $index => $layoutData) {
            try {
                $layout = $this->importer->importLayout($layoutData);
            } catch (Exception $e) {
                $output->writeln("Could not import layout #{$index}");
                $output->writeln('Exception stack:');
                $this->renderExceptionStack($e, $output);
                $output->writeln('');

                continue;
            }

            $output->writeln("Imported layout #{$index} into Layout ID={$layout->getId()}");
            $output->writeln('');
        }
    }

    /**
     * Renders all stacked exception messages for the given $exception.
     *
     * @param \Exception $exception
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $number
     */
    private function renderExceptionStack(Exception $exception, OutputInterface $output, $number = 0)
    {
        $output->writeln(" #{$number}:");
        $exceptionClass = get_class($exception);
        $output->writeln("  - exception: {$exceptionClass}");
        $output->writeln("  - file: {$exception->getFile()}");
        $output->writeln("  - line: {$exception->getLine()}");
        $output->writeln("  - message: {$exception->getMessage()}");

        $previous = $exception->getPrevious();

        if ($previous instanceof Exception) {
            $this->renderExceptionStack($exception, $output, $number + 1);
        }
    }

    /**
     * Decode given JSON $data string.
     *
     * @param string $data
     *
     * @return mixed
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If given $data string could not be decoded
     */
    private function decode($data)
    {
        $value = json_decode($data, true);

        if ($value === null) {
            throw new RuntimeException(
                'Data is malformed, could not decode given JSON string'
            );
        }

        return $value;
    }
}
