<?php

namespace Source\Database;

use Exception;

abstract class ActiveRecord
{

    /** @var string */
    private static $sql;

    /** @var int|null */
    protected $rows;

    /**
     * Store parameters to be replaced in sql statements 
     * On pattern, key => value
     * 
     * @var array
     */
    protected $parameters = [];

    public function __construct(string $sql, $filter = null)
    {
        
        if ($filter) {
            $sql .= " WHERE " . $filter;
        }

        self::$sql = $sql;

    }

    /**
     * @param string $param
     * @param $value
     */
    public function setParameter(string $param, $value): void
    {
        if (!isset($this->parameters[$param])) {
            $this->parameters[$param] = $value;
        }
    }

    public function setFields(array $fields): void
    {   
        if (!is_array($fields)) {
            throw new Exception("Method must receive parameter of type array");
        }

        if (empty($fields)) {
            throw new Exception("array empty");
        }

        $sql = self::$sql;
        $fields = implode(", ", $fields);
        $sql = "SELECT {$fields} FROM ({$sql})";
        self::$sql = $sql;
    }

    public function getTotal(): ? int
    {
        return $this->rows;
    }

    public function load()
    {
        try {
            
            $conn = ConnectOracle::connectOracleDB();
            $stmt = ConnectOracle::parse($conn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
            ConnectOracle::execute($stmt);
            $sql = $this->prepare();
            $stmt = ConnectOracle::parse($conn, $sql);

            if (!ConnectOracle::execute($stmt)) {
                ConnectOracle::closeCMConnection();
                throw new Exception("Oci execute error");
            }

            if ($object = oci_fetch_object($stmt)) {
                $this->rows = 1;
                return $object;
            }

            ConnectOracle::closeCMConnection();

            return null;
            
        } catch (Exception $e) {
            $message = $e->getMessage();
            //$trace = $e->getTraceAsString();
            $response = ['exception' => ['class' => __CLASS__, 
                                         'method' => __METHOD__, 
                                         'data' => $message]];
            return (object) $response;
        }

    }

    /**
     * Use param: ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD',
     * to format date return on results
     *
     * @return array|object
     */
    public function all()
    {
        try {
            $results = array();
            $conn = ConnectOracle::connectOracleDB();
            $stmt = ConnectOracle::parse($conn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
            ConnectOracle::execute($stmt);
            $sql = $this->prepare();
            $stmt = ConnectOracle::parse($conn, $sql);
            if (!ConnectOracle::execute($stmt)) {
                ConnectOracle::closeCMConnection();
                throw new Exception("Oci execute error");
            }
            $rows = oci_fetch_all($stmt, $results, null, null, OCI_FETCHSTATEMENT_BY_ROW);
            
            $this->rows = $rows;
            ConnectOracle::closeCMConnection();
            return $results;

        } catch (Exception $e) {
            $message = $e->getMessage();
            $response = ['exception' => ['class' => __CLASS__, 
                                         'method' => __METHOD__, 
                                         'data' => $message]];
            return (object) $response;
        }
    }

    /**
     * Checks if there are parameters to be replaced in sql string
     * @return string
     */
    private function prepare():string
    {
        $sql = self::$sql;
        if ($this->parameters) {
            foreach ($this->parameters as $param => $value) {
                $sql = str_replace($param, $value, $sql);
            }
        }
        return $sql;
    }
}