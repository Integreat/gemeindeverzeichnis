<?php


namespace Integreat\Gemeindeverzeichnis\Command;

use Integreat\Gemeindeverzeichnis\Import\ImportInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command implements LoggerAwareInterface
{
    protected static $defaultName = 'import';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ImportInterface[]
     */
    protected $importers = [];

    public function __construct(array $importers, string $name = null)
    {
        $this->importers = $importers;
        $this->logger = new NullLogger();
        parent::__construct($name);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->addOption('all');
        foreach ($this->importers as $importer) {
            $this->addOption($importer->getName());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importers = $this->importers;

        usort($importers, function (ImportInterface $a, ImportInterface $b) {
            $aP = $a->getPriority();
            $bP = $b->getPriority();

            if ($aP == $bP) {
                return 0;
            }

            return ($aP > $bP) ? -1 : 1;
        });

        $all = true;
        foreach ($importers as $importer) {
            $all = $all && !$input->getOption($importer->getName());
        }
        $all = $all || $input->getOption('all');

        foreach ($importers as $importer) {
            $this->logger->notice('Run importer ' . $importer->getName());
            if ($input->getOption($importer->getName()) || $all) {
                $importer->import();
            }
        }
    }
}
