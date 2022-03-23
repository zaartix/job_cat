<?php
namespace Catalyst\Service;

use Symfony\Component\Console\Output\OutputInterface;

class ConsoleService {
    /** @var OutputInterface */
    private $output = null;

    /**
     * Interface for output
     * @param OutputInterface $output
     * @return void
     */
    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Log output
     * @param string $txt
     * @param bool $isDebug
     * @return void
     */
    public function log(string $txt, bool $isDebug = false) {
        if ($this->output instanceof OutputInterface) {
            if ($isDebug) {
                if ($this->output->isDebug()) {
                    $this->output->writeln($txt);
                }
            } else {
                if ($this->output->isVerbose()) {
                    $this->output->writeln($txt);
                }
            }

        }
    }
}