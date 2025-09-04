<?php

use DB\Identifier;

abstract class Model
{
    abstract public static function setup();
    protected static bool $initialized = false;
    protected static bool $schema_is_handled = false;
    protected static ?array $properties = null;
    protected static array $foreignKeys = [];

    protected static DB\DB $db;

    public static function init(?DB\DB $db)
    {
        if (!static::$initialized)
        {
            static::$db = $db ?? DB\DB::$db;

            static::setup();
            static::handleSchema();

            static::$initialized = true;
        }
    }

    public static function table(): string
    {   
        $className = Identifier::{static::class}();
        $className = basename(str_replace('\\', '/', $className));

        return $className;
    }

    /**
     * Add a foreign key constraint
     * 
     * @param string $column Local column name
     * @param string $referencedTable Referenced table name
     * @param string $referencedColumn Referenced column name
     * @return void
     */
    public static function addForeignKey(string $column, string $referencedTable, string $referencedColumn): void
    {
        static::$foreignKeys[] = [
            'column' => $column,
            'referencedTable' => $referencedTable,
            'referencedColumn' => $referencedColumn
        ];
    }

    /**
     * Get only static and DBData vars.
     * 
     * Thanks to https://www.php.net/manual/en/function.get-class-vars.php#109995
     * 
     * @return array
     */
    public static function getProperties()
    {
        if (!is_null(static::$properties))
        {
            return static::$properties;
        }

        $the_class = static::class; //get_called_class();
        $result = [];
        $the_class_vars = get_class_vars($the_class);
        // print_r($the_class_vars);
        foreach ($the_class_vars as $the_varname => $the_varval)
        {
            if (isset($the_class::$$the_varname) && ($the_class::$$the_varname instanceof DB\Column))
            {
                $the_class::$$the_varname->setName($the_varname);
                $the_class::$$the_varname->setTable(static::table());
                $result[] = $the_varname;
            }
        }
        static::$properties = $result;
        return static::$properties;
    }

    protected static function handleSchema()
    {
        if (static::$schema_is_handled)
        {
            return;
        }

        $table_name = static::table();
        $property_list = static::getProperties();
        $column_list = [];
        $column_defs = [];

        foreach ($property_list as $p)
        {
            $p_var = static::${$p};
            $p_type = $p_var->getType();
            $p_def = $p_var->getDefinition();
            $column_list[] = [$p, $p_type, $p_def];
            $column_defs[] = $p_var->getFullDefinition();
        }

        // Handle the table
        if (!static::$db->tableExists($table_name))
        {
            $q = static::$db->getConnection()->addTableQuery($table_name, $column_defs);
            error_log("Creating table with query: " . $q);
            static::$db->query($q);

            // echo static::$db->lastQuery;
            // echo static::$db->error;
        }


        // Handle the columns
        foreach ($column_list as $c)
        {
            $column_exists = static::$db->columnExists($table_name, $c[0]);
            if (!$column_exists)
            {
                $q = static::$db->getConnection()->addColumnQuery($table_name, $c[0], $c[1], $c[2]);
                static::$db->query($q);
            }

            /*elseif ($column['COLUMN_TYPE'] != $p_type)
            {
                static::$db->query("ALTER TABLE", $table_name, "MODIFY", $p, $p_type, $p_def, ";");
                // echo "MODIFY!".$column['COLUMN_TYPE']." ".$p_type;
                // echo static::$db->lastQuery;
            }*/
            // Since Sqlite doesn't support column modifications, we just "add" new columns.
            // To change type of a column:
            //      create a new property
            //      let the system handle the schema
            //      and then transfer data from the old column to the new one
            //      manually!
        }

        // Handle foreign keys
        if (!empty(static::$foreignKeys))
        {
            foreach (static::$foreignKeys as $fk)
            {
                try {
                    $fkQuery = static::$db->getConnection()->addForeignKeyQuery(
                        $table_name,
                        $fk['column'],
                        $fk['referencedTable'],
                        $fk['referencedColumn']
                    );
                    static::$db->query($fkQuery);
                } catch (\Exception $e) {
                    // Foreign key constraint may already exist, ignore the error
                    // echo "Foreign key constraint error: " . $e->getMessage();
                }
            }
        }

        // echo "HANDLED!";

        static::$schema_is_handled = true;
    }
}
