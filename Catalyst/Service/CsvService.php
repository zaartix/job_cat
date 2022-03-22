<?php
namespace Catalyst\Service;

use Symfony\Component\Console\Output\OutputInterface;

class CsvService {
    /** @var OutputInterface */
    private $output = null;
    /** @var int Total rows in CSV file */
    public $fileRowsTotal = 0;
    /** @var int Valid rows in CSV file */
    public $fileRowsValid = 0;

    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Retrieve valid rows as array from given file
     * @param $pathCsv string Path to CSV file
     * @return array Array ready for insert
     */
    public function processFile(string $pathCsv): array
    {
        // reset counters in case of several files
        $this->fileRowsTotal = 0;
        $this->fileRowsValid = 0;

        $f = fopen($pathCsv,'r');
        $readyArray = [];
        while ($row = fgetcsv($f)) {
            $this->fileRowsTotal++;
            if (count($row) != 3) {
                $this->log('Row #'.($this->fileRowsTotal-1).' does not match required structure'."\n".implode(',',$row));
                continue;
            }
            $this->log('Raw data:'."\n".var_export($row,true),true);
            $row = array_map(function($txt){
                return mb_strtolower(trim($txt));
            },$row);
            list($tName, $tSurname, $tEmail) = $row;

            $tName = $this->prepareName($tName);
            $tSurname = $this->prepareName($tSurname);
            $this->log('Prepared data:'."\n".var_export([$tName,$tSurname,$tEmail],true)."\n---------",true);

            // This part checking head of CSV and validate email
            if ($tName && $tSurname && $this->validateEmail($tEmail)) {
                // this structure is more cheap for store in case of large file
                $readyArray[] = [$tName,$tSurname,$tEmail];
                $this->fileRowsValid++;
            } else {
                $this->log('Row #'.($this->fileRowsTotal-1).' have an invalid data: '.implode(',',$row));
            }
        }
        return $readyArray;
    }


    /**
     * Name preparation
     * @param string $txt
     * @return string
     */
    private function prepareName(string $txt) {
        return ucfirst(preg_replace('~[^\w\'\-]~sui','',$txt));
    }

    /**
     * Log output
     * @param string $txt
     * @param $isDebug
     * @return void
     */
    private function log(string $txt, $isDebug = false) {
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

    /**
     * Email check, filter_var function is passing email "mo'connor@cat.net.nz"
     * @param $email string Email
     * @return bool
     */
    private function validateEmail(string $email): bool
    {
        if (preg_match('~^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$~',$email)) {
            return true;
        } else {
            return false;
        }
    }
}