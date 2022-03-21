<?php
namespace Catalyst\Service;

use Catalyst\Exception\DbConnectException;

/* I'm prefers to use Doctrine ORM, but in this case it is too "heavy" and Doctrine not good for import a lot of data because of memory leaks */

class DbService {
    /**
     * @var false|\mysqli|null
     */
    public $mysqli;

    /**
     * @param $user ?string
     * @param $pass ?string
     * @param $host ?string
     * @param $db ?string
     */
    public function __construct(?string $user = 'root', ?string $pass = '', ?string $host = 'locahost', ?string $db = 'test')
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->mysqli = new \mysqli($host, $user, $pass, $db);
        if ($this->mysqli->connect_errno) {
            throw new DbConnectException('Can\'t connect to database. Error: '.$this->mysqli->connect_error);
        }
    }

    /**
     * @param $tName string name
     * @param $tSurname string surname
     * @param $tEmail string email
     * @return bool Success inserted
     */
    public function insert(string $tName, string $tSurname, string $tEmail): bool
    {
        // it is "cheaper" to replace data by unique key rather than check if exist every time
        $stmt = $this->mysqli->prepare('REPLACE INTO users (`name`, surname, email) values (?,?,?)');
        $stmt->bind_param("sss", $tName,$tSurname,$tEmail);
        return $stmt->execute();
    }
}