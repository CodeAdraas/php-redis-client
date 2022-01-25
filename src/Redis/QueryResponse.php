<?php

namespace Dodo\Redis;

class QueryResponse {
     /**
     * @var bool $status 
     * 
     * Status indication of last SQL query statement
     */
    public $status;

    /**
     * @var int $rows 
     * 
     * Rowcount deriving from only a SQL SELECT statement
     */
    public $rows = 0;

    /**
     * @var int $affected 
     * 
     * Indication wheteter rows where affected by either a SQL INSERT, UPDATE or DELETE statement 
     */
    private $affected;

    /**
     * @var array $result 
     * 
     * Array containing returned rows derived from SQL SELECT statement
     */
    private $result;

    /**
     * @var int|null $lastInsertedId
     * 
     * Array containing returned rows derived from SQL SELECT statement
     */
    private $lastInsertedId;

    /**
     * Instance construct, inapplicable
     */
    public function __construct(
        $status, 
        $rows,
        $affected,
        $result,
        $lastlastInsertedId
    ) {
        $this->status = $status;
        $this->rows = $rows;
        $this->affected = $affected;
        $this->result = $result;
        $this->lastlastInsertedId = $lastlastInsertedId;
    }
    
    public function hasRows()
    {
        return !!$this->rows;
    }

    public function hasAffected()
    {
        return $this->affected;
    }

    public function insertedId()
    {
        return !$this->lastInsertedId ? false : $this->lastInsertedId;
    }

    public function get()
    {
        return $this->result;
    }
}