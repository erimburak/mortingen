<?php

use DB\Identifier;

abstract class Model
{
    abstract public static function setup();
    protected static bool $initialized = false;
    protected static bool $schema_is_handled = false;
    protected static ?array $properties = null;

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

    public static function table(): Identifier
    {
        return Identifier::{static::class}();
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
                $the_class::$$the_varname->setTable($the_class);
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

        $table_name = static::class;
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

        // echo "HANDLED!";

        static::$schema_is_handled = true;
    }
}
