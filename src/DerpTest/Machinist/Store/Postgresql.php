<?php
namespace DerpTest\Machinist\Store;
use DerpTest\Machinist\Store\SqlStore;

/**
 * Postgresql Specific store support.
 */
class Postgresql extends SqlStore
{
    /**
     * Dictionary of primary key values for tables
     * @var array
     */
    protected $key_dict;

    /**
     * Dictionary of columns for tables
     * @var array
     */
    protected $column_dict;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->key_dict = array();
        $this->column_dict = array();
    }

    public function primaryKey($table)
    {
        if (!isset($this->key_dict[$table])) {
            $stmt = $this->pdo()->prepare("SELECT
                pg_attribute.attname
              FROM pg_index
              JOIN pg_attribute ON pg_attribute.attrelid = pg_index.indrelid
                AND pg_attribute.attnum = ANY(pg_index.indkey)
              WHERE pg_index.indrelid = '${table}'::regclass
              AND pg_index.indisprimary
            ;");
            $stmt->execute();
            $results = array();
            while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
                $results[] = $row->attname;
            }

            if (count($results) < 1) {
                $results = $this->columns($table);
            } else if (is_array($results) && count($results) == 1) {
                $results = array_pop($results);
            }
            $this->key_dict[$table] = $results;
        }
        return $this->key_dict[$table];
    }

    protected function columns($table)
    {
        if (!isset($this->column_dict[$table])) {
            $stmt = $this->pdo()->prepare("SELECT
                column_name
              FROM information_schema.COLUMNS
              WHERE TABLE_NAME = '${table}'
            ;");
            $stmt->execute();
            $columns = array();
            while ($row = $stmt->fetch()) {
                $columns[] = $row['column_name'];
            }
            $this->column_dict[$table] = $columns;
        }
        return $this->column_dict[$table];
    }

    public function quoteTable($table)
    {
        return '"' . $table . '"';
    }

    public function quoteColumn($column)
    {
        return '"' . $column . '"';
    }
}
