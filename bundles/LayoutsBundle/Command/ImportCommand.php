<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Command;

use Exception;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Input\ImporterInterface;
use Netgen\Layouts\Transfer\Input\ImportOptions;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SkippedResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

use function class_exists;
use function file_exists;
use function file_get_contents;
use function is_string;
use function method_exists;
use function sprintf;

/**
 * Command to import Netgen Layouts entities.
 */
final class ImportCommand extends Command
{
    private ImporterInterface $importer;

    private SymfonyStyle $io;

    public function __construct(ImporterInterface $importer)
    {
        $this->importer = $importer;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Imports Netgen Layouts entities')
            ->addArgument('file', InputArgument::REQUIRED, 'JSON file to import')
            ->addOption('mode', 'm', InputOption::VALUE_REQUIRED, 'Defines how to handle existing entities when importing', 'copy')
            ->setHelp('The command <info>%command.name%</info> imports Netgen Layouts entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $file = $input->getArgument('file');
        if (!is_string($file) || !file_exists($file)) {
            throw new RuntimeException('Provided file does not exist.');
        }

        $importMode = $input->getOption('mode');
        if (!is_string($importMode)) {
            $importMode = ImportOptions::MODE_COPY;
        }

        $errorCount = $this->importData(
            (string) file_get_contents($file),
            $importMode,
        );

        $errorCount > 0 ?
            $this->io->caution('Import completed with errors.') :
            $this->io->success('Import completed successfully.');

        return 0;
    }

    /**
     * Import new entities from the given data and returns the error count.
     */
    private function importData(string $data, string $mode): int
    {
        $errorCount = 0;
        $importOptions = (new ImportOptions())
            ->setMode($mode);

        foreach ($this->importer->importData($data, $importOptions) as $index => $result) {
            if ($result instanceof SkippedResult) {
                $this->io->note(
                    sprintf(
                        'Skipped importing %1$s #%2$d with UUID %3$s',
                        $result->getEntityType(),
                        $index + 1,
                        $result->getEntityId()->toString(),
                    ),
                );

                continue;
            }

            if ($result instanceof SuccessResult) {
                $this->io->note(
                    sprintf(
                        'Imported %1$s #%2$d into %1$s UUID %3$s',
                        $result->getEntityType(),
                        $index + 1,
                        $result->getEntityId()->toString(),
                    ),
                );

                continue;
            }

            if ($result instanceof ErrorResult) {
                $this->io->error(sprintf('Could not import %s #%d with UUID %s', $result->getEntityType(), $index + 1, $result->getEntityId()->toString()));
                $this->renderError($result->getError());
                $this->io->newLine();

                ++$errorCount;

                continue;
            }
        }

        return $errorCount;
    }

    /**
     * Renders the error to console output. It uses a verbose level to render it, to display as much info as possible.
     */
    private function renderError(Throwable $t): void
    {
        $previousVerbosity = $this->io->getVerbosity();
        $this->io->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $app = new Application();
        if (method_exists($app, 'renderThrowable')) {
            $app->renderThrowable($t, $this->io);
        } elseif (method_exists($app, 'renderException')) {
            if (!$t instanceof Exception && class_exists(FatalThrowableError::class)) {
                $t = new FatalThrowableError($t);
            }

            $app->renderException($t, $this->io);
        }

        $this->io->setVerbosity($previousVerbosity);
    }
}
