<?php
namespace Catalyst\Service;

use Catalyst\Exception\DbConnectException;

/* I'm prefers to use Doctrine ORM, but in this case it is too "heavy" and Doctrine not good for import a lot of data because of memory leaks */

class DbService extends ConsoleService {
    /**
     * @var false|\mysqli
     */
    public $mysqli;

    /**
     * @param $user ?string
     * @param $pass ?string
     * @param $host ?string
     * @param $db ?string
     */
    public function __construct(?string $user = 'root', ?string $pass = '', ?string $host = 'localhost', ?string $db = 'test')
    {
        if (!class_exists('mysqli')) {
            throw new DbConnectException('MySQLi component is not installed');
        }
        $this->mysqli = new \mysqli($host, $user, $pass, $db);
        if ($this->mysqli->connect_errno) {
            throw new DbConnectException('Can\'t connect to database. Error: '.$this->mysqli->connect_error);
        }
    }

    /**
     * Insert/replace new row
     * @param $tName string name
     * @param $tSurname string surname
     * @param $tEmail string email
     * @return bool Success inserted
     */
    public function insert(string $tName, string $tSurname, string $tEmail): bool
    {
        // it is "cheaper" to replace data by unique key rather than check if exist every time. At least for current task
        $sql = 'REPLACE INTO users (name, surname, email) values (?,?,?)';
        $stmt = $this->mysqli->prepare($sql);
        $this->log('SQL: '.$sql,true);
        if ($stmt === false) {
            $this->log($this->mysqli->error);
            throw new DbConnectException('Table users possibly not created. Use key --create_table');
        }
        $stmt->bind_param("sss", $tName,$tSurname,$tEmail);
        $this->log('SQL params: '.print_r([$tName,$tSurname,$tEmail],true),true);
        return $stmt->execute();
    }

    /**
     * Create table "users"
     * @param $reCreate boolean Flag to drop table if already exists
     * @return bool
     */
    public function createTableUsers(bool $reCreate = false): bool
    {
        // name and surname are varchar not char because of possible dashes (in case if Elon Musk's son in CSV (his name is "X AE A-XII"))
        $sql = '
            CREATE TABLE `users` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` varchar(32) NOT NULL,
                `surname` varchar(32) NOT NULL,
                `email` varchar(64) NOT NULL
            ) COMMENT=\'Users inserted from CSV\' ENGINE=\'InnoDB\' COLLATE \'utf8_general_ci\'';
        $this->log('SQL: '.$sql,true);
        $isCreated = $this->mysqli->query($sql);
        // error means table already exists or some kind of low level error (no space left on HDD)
        if (!$isCreated && $reCreate) {
            $this->deleteTableUsers();
            $isCreated = $this->mysqli->query($sql); // if exception will be here it is some unexpected
        }

        if ($isCreated) {
            $sql = 'ALTER TABLE `users` ADD UNIQUE `email` (`email`)';
            $this->mysqli->query($sql);
            $this->log('SQL: '.$sql,true);
        }
        return $isCreated;
    }

    /**
     * Drop table "users"
     * @return void
     */
    public function deleteTableUsers()
    {
        $sql = 'DROP TABLE users';
        $this->mysqli->query($sql);
        $this->log('SQL: '.$sql,true);
    }
}