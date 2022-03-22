<?php
namespace Catalyst\Service;

use Symfony\Component\Console\Output\OutputInterface;

class CsvService {
    /** @var OutputInterface */
    private $output = null;
    /** @var int Total rows in CSV file */
    public int $fileRowsTotal = 0;
    /** @var int Valid rows in CSV file */
    public int $fileRowsValid = 0;

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
            array_walk($row,function($txt){
                return trim($txt);
            });
            list($tName, $tSurname, $tEmail) = $row;

            // may be it is useful to lowercase before capitalize
            $tName = ucfirst($tName);
            $tSurname = ucfirst($tSurname);
            $tEmail = mb_strtolower($tEmail);

            // This part checking head of CSV and validate email
            if ($tName && $tSurname && $this->validateEmail($tEmail)) {
                // this structure is more cheap for store in case of large file
                $readyArray[] = [$tName,$tSurname,$tEmail];
                $this->fileRowsValid++;
            } else {
                $this->log('Row #'.($this->fileRowsTotal-1).' have an invalid data'."\n".implode(',',$row));
            }
        }
        return $readyArray;
    }

    /**
     * Output log
     * @param $txt
     * @return void
     */
    private function log($txt) {
        if ($this->output instanceof OutputInterface) {
            if ($this->output->isVerbose()) {
                $this->output->writeln($txt);
            }
        }
    }

    /**
     * Regexp is more flexible, but filter_var is OK too
     * @param $email string Email
     * @return bool
     */
    private function validateEmail(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}