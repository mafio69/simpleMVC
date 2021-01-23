<?php
namespace App\Config;

use Exception;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \PDO;
use \PDOException;
use PDOStatement;

class Model
{
    private PDO $dbh;
    private PDOStatement $stmt;
    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * Database constructor.
     *
     */
    public function __construct()
    {
        $this->logger = new Logger('logger');
        $this->logger->pushHandler(new StreamHandler(BASE_DIR . '/Logs/DB/dbLog.log', Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());

        try {
            $this->dbh = new PDO("mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . "", getenv('DB_USER'), getenv('DB_PASSWORD'));
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->logger->info("App run: " . date("Y-m-d H:i:s"));
        } catch (PDOException $e) {
            $this->prepareException($e, __METHOD__, '__construct()', 'Connect Fail');
        }
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function lastId(): string
    {
        return $this->dbh->lastInsertId();
    }

    public function bind(array $data): void
    {
        foreach ($data as $k => $dat) {
            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
    }

    public function getAll(string $tableName, string $order = null, $limit = null, bool $debug = false): array
    {
        $addLimit = $limit === null ? '' : ' LIMIT  ' . $limit;
        $orderBy = '';
        if ($order !== null) {
            $orderBy = "ORDER BY  " . $order . " DESC ";
        }
        $sql = "SELECT * FROM " . $tableName . " " . $orderBy . " " . $addLimit;


        if ($debug === true) {
            echo $sql;
        }

        $this->stmt = $this->dbh->prepare($sql);

        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($e, __METHOD__, $sql, 'getAll Fail');
        }

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param bool $debug
     * @param string $table
     * @param array $data
     *
     * @return array|bool|mixed
     */

    public function search(string $table, array $data, bool $debug = null)
    {
        $value = "SELECT * FROM $table WHERE ";

        foreach ($data as $key => $value) {
            $value .= '' . $key . ' = :' . $key . '  ';
        }

        $this->execute($value, $data, $debug);

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $table
     * @param array $data
     * @param bool|null $debug
     *
     * @return object
     */
    public function getFirst(string $table, array $data, bool $debug = null): object
    {
        $sql = "SELECT * FROM $table WHERE ";

        foreach ($data as $key => $value) {
            $sql .= '' . $key . ' = :' . $value . '  AND ';
        }
        $sql = substr($sql, 0, -4);
        $sql .= ' LIMIT 1 ';

        $this->execute($sql, $data, $debug);

        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sql
     * @param array $data
     * @param bool $debug
     *
     * @return array|bool|mixed
     */
    public function query(string $sql, array $data, bool $debug = false)
    {
        $this->execute($sql, $data, $debug);

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sql
     * @param array $data
     * @param bool $debug
     *
     * @return array|bool|mixed
     *
     * The key in the $data array should be the same as: placeholder
     */
    public function find(string $sql, array $data, bool $debug = false)
    {
        $this->execute($sql, $data, $debug);
        $test = $this->stmt->rowCount();

        if ($test == 1)
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        elseif ($test > 1)
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        else
            return false;
    }


    /**
     * @param string $sql
     * @param false $debug
     * @param array $data
     * @return mixed
     */
    public function execute(string $sql, array $data, bool $debug): bool
    {
        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        $this->bind($data);

        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($e, __METHOD__, $sql, 'Execute Fail');
        }

        return true;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param string $where
     * @param array $whereData
     * @param bool $debug
     * @return array|bool
     */
    public function update(string $tableName, array $data, string $where, array $whereData, bool $debug = false)
    {

        $sql = "UPDATE " . $tableName . " SET ";
        foreach ($data as $k => $field) {
            $sql .= $k . " =  :" . $k . ", ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ' WHERE ' . $where;

        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);

        $this->bind($data);
        $this->bind($whereData);

        try {
            $data = $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($e, __METHOD__, $sql, 'Update Fail');
        }

        return $data;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param bool $debug
     * @return array|bool
     */
    public function insert(string $tableName, array $data, bool $debug = false): bool
    {
        $sql = "INSERT INTO " . $tableName . " (";
        foreach ($data as $k => $field) {
            $sql .= $k . ", ";
        }

        $sql = rtrim($sql, ', ');
        $sql .= " ) VALUES ( ";
        foreach ($data as $k => $field) {
            $sql .= ":" . $k . ", ";
        }

        $sql = $sql = rtrim($sql, ', ');
        $sql .= " )";

        $this->execute($sql, $data, $debug);

        return $data;
    }

    public function prepareException(Exception $e, string $method, string $sql, string $title): void
    {
        /** @var Logger $logger */
        $this->logger->critical("{$title} | {$method} | {$sql}: {$e -> getMessage()}");
        if (getenv('APP_ENV') === 'dev') {
            echo $e->getMessage();
        }

        exit('<br> <h3 style="color: brown">Fail</h3>');
    }
}