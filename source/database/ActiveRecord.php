<?php

namespace Source\Database;

use Exception;

abstract class ActiveRecord
{
    /** @var object|null */
    protected $data;

    /** @var \Exception|null */
    protected static $fail;

    /** @var string|null */
    protected $message;

    /** @var string */
    private static $sql;

    /** @var int|null */
    protected static $nresults;

    public function __construct(string $sql, $filter = null)
    {
        
        if ($filter) {
            $sql .= " WHERE " . $filter;
        }

        self::$sql = $sql;

    }

    public function setFields(array $fields): void
    {   
        if (!is_array($fields)) {
            throw new Exception("Method must receive parameter of type array");
            return;
        }

        if (empty($fields)) {
            throw new Exception("array empty");
            return;
        }

        $sql = self::$sql;
        $fields = implode(", ", $fields);
        $sql = "SELECT {$fields} FROM ({$sql})";
        self::$sql = $sql;
    }

    /**
     * Undocumented function
     *
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    public function load()
    {
        try {
            
            $conn = ConnectOracle::connectOracleDB();
            $stmt = ConnectOracle::parse($conn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
            ConnectOracle::execute($stmt);
            $stmt = ConnectOracle::parse($conn, self::$sql);

            if (!ConnectOracle::execute($stmt)) {
                ConnectOracle::closeCMConnection();
                throw new Exception("Erro na execução");
            }

            if ($object = oci_fetch_object($stmt)) {
                return $object;
            }

            ConnectOracle::closeCMConnection();

            return null;
            
        } catch (Exception $e) {
            $message = $e->getMessage();
            //$trace = $e->getTraceAsString();
            $response = ['exception' => ['class' => __CLASS__, 'method' => __METHOD__, 'data' => $message]];
            return $response;
        }

    }

    public static function all()
    {
        try {

            $results = array();
            $conn = ConnectOracle::connectOracleDB();            
            //conversão dos campos com formato de data
            $stmt = ConnectOracle::parse($conn, "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
            ConnectOracle::execute($stmt);

            $stmt = ConnectOracle::parse($conn, self::$sql);

            if (!ConnectOracle::execute($stmt)) {
                ConnectOracle::closeCMConnection();
                throw new Exception("Erro na execução");
            }           

           $rows = oci_fetch_all($stmt, $results, null, null, OCI_FETCHSTATEMENT_BY_ROW);

            // if ($rows>0) {
            //    self::$nresults = $rows;
            // }

            // while (($row = oci_fetch_array($stmt, OCI_ASSOC)) != false) {
            //     $results[] = $row;
            // }
            
            self::$nresults = count($results);

            ConnectOracle::closeCMConnection();
            
            return $results;

        } catch (Exception $e) {
            $message = $e->getMessage();
            //$trace = $e->getTraceAsString();
            $response = ['exception' => ['class' => __CLASS__, 'method' => __METHOD__, 'data' => $message]];
            return $response;
        }
    }

}
