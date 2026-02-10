<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformationSchemaController extends Controller
{
    /**
     * Get Tables Containing the columns
     *
     * @param  string  $column_name
     * @return array
     */
    public static function tables(string $column_name)
    {
        $tables = [];

        $table_schema = config('database.connections.mysql.database');

        $rows = DB::connection('infodb')->select('select TABLE_NAME from COLUMNS where TABLE_SCHEMA=:TABLE_SCHEMA AND COLUMN_NAME=:COLUMN_NAME', [":TABLE_SCHEMA" => $table_schema, ":COLUMN_NAME" => $column_name]);

        foreach ($rows as $row) {
            $tables[] = $row->TABLE_NAME;
        }

        return $tables;
    }
}
