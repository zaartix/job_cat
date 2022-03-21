<?php
namespace Catalyst\Service;

use Catalyst\Exception\CSVStructureException;

class CsvService {

    private $errors = [];

    /**
     * @param $pathCsv string Path to CSV file
     * @return array Array ready for insert
     */
    public function processFile($pathCsv) {
        $f = fopen($pathCsv,'r');
        $readyArray = [];
        $rowNum = 0;
        while ($row = fgetcsv($f)) {
            if (count($row) != 3) {
                $this->addError('Row #'.$rowNum.' does not match required structure'."\n".implode(',',$row));
                $rowNum++;
                continue;
            }
            array_walk($row,function($txt){
                return trim($txt);
            });
            list($tName, $tSurname, $tEmail) = $row;
            $tName = ucfirst($tName);
            $tSurname = ucfirst($tSurname);
            $tEmail = mb_strtolower($tEmail);

            // This part checking head of CSV and validate email
            if ($tName && $tSurname && $this->validateEmail($tEmail)) {
                // this structure is more cheap for store in case of large file
                $readyArray[] = [$tName,$tSurname,$tEmail];
            } else {
                $this->addError('Row #'.$rowNum.' have invalid data'."\n".implode(',',$row));
            }
            $rowNum++;
        }
        return $readyArray;
    }

    /**
     * Regexp is more flexible, but filter_var is OK too
     * @param $email string Email
     * @return bool
     */
    private function validateEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $txt string error text
     * @return void
     */
    private function addError($txt) {
        $this->errors[] = $txt;
    }

    /**
     * @return array array of errors
     */
    public function getErrors() {
        return $this->errors;
    }
}