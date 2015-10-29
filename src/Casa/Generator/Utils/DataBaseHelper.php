<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 10/26/2015
 * Time: 6:02 PM
 */

namespace Casa\Generator\Utils;
use DB;

class DataBaseHelper
{

    /**
     * Return a sql result with all foreign keys (data from information_scheme).
     *
     * @return mixed
     */
    public static function getAllForeignKeys()
    {
        $sql = 'SELECT * FROM information_schema.KEY_COLUMN_USAGE ';
        $sql .= 'WHERE REFERENCED_COLUMN_NAME IS NOT NULL AND REFERENCED_TABLE_SCHEMA = DATABASE()';
        $results = DB::select($sql);
        return $results;
    }

    public static function getForeignKeysFromTable($tableName)
    {
        $sql = 'SELECT * FROM information_schema.KEY_COLUMN_USAGE ';
        $sql .= 'WHERE REFERENCED_COLUMN_NAME IS NOT NULL AND REFERENCED_TABLE_SCHEMA = DATABASE()';
        $sql .= " AND TABLE_NAME = '$tableName'";
        $results = DB::select($sql);
        return $results;
    }

    public static function getReferencesFromTable($tableName)
    {
        $sql = 'SELECT * FROM information_schema.KEY_COLUMN_USAGE ';
        $sql .= 'WHERE REFERENCED_COLUMN_NAME IS NOT NULL AND REFERENCED_TABLE_SCHEMA = DATABASE()';
        $sql .= " AND REFERENCED_TABLE_NAME = '$tableName'";
        $results = DB::select($sql);
        return $results;
    }

    public static function getColumnFromTable($tableName, $type = 'string')
    {
        /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager  */
        $schema = DB::getDoctrineSchemaManager($tableName);

        /** @var \Doctrine\DBAL\Schema\Table  */
        $table = $schema->listTableDetails($tableName);

        $columns =  $table->getColumns();
        foreach($columns as $c)
        {
            if ($c->getType()->getName() == $type)
                return $c->getName();
        }
        return '';

    }

}