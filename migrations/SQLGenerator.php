<?php

namespace App\Migration;

class SQLGenerator
{
  private static array $enumTypes = [];

  public static function generateCreateTable(string $table, array $columns, string $driver = 'pgsql'): string
  {
    $lines = [];
    $foreignKeys = [];

    foreach ($columns as $name => $attributes) {
      $line = "  {$name} ";

      // Type
      if (is_array($attributes['type']) && $attributes['type'][0] === 'ENUM') {
        $enumName = "{$table}_{$name}_enum";
        if ($driver === 'mysql') {
          // ENUM inline pour MySQL
          $values = array_map(fn($v) => "'" . addslashes($v) . "'", $attributes['type'][1]);
          $line .= 'ENUM(' . implode(', ', $values) . ')';
        } else {
          // ENUM global pour PostgreSQL
          self::$enumTypes[$enumName] = $attributes['type'][1];
          $line .= $enumName;
        }
      } else {
        $line .= $attributes['type'];
      }

      // NOT NULL
      if (!empty($attributes['not_null'])) {
        $line .= ' NOT NULL';
      }

      // UNIQUE
      if (!empty($attributes['unique'])) {
        $line .= ' UNIQUE';
      }

      // AUTO INCREMENT
      if (!empty($attributes['auto_increment']) && stripos($attributes['type'], 'int') !== false) {
        if ($driver === 'mysql') {
          $line .= ' AUTO_INCREMENT';
        } else {
          $line .= ' GENERATED ALWAYS AS IDENTITY';
        }
      }

      // DEFAULT
      if (isset($attributes['default'])) {
        $default = $attributes['default'];
        if (is_string($default) && strtoupper($default) === 'CURRENT_TIMESTAMP') {
          $line .= " DEFAULT $default";
        } else {
          $line .= " DEFAULT '" . addslashes($default) . "'";
        }
      }

      $lines[] = $line;

      // FOREIGN KEY
      if (isset($attributes['foreign'])) {
        [$refTable, $refCol] = $attributes['foreign'];
        $foreignKeys[] = "  FOREIGN KEY ({$name}) REFERENCES {$refTable}({$refCol})";
      }
    }

    // PRIMARY KEY
    foreach ($columns as $name => $attributes) {
      if (!empty($attributes['primary'])) {
        $lines[] = "  PRIMARY KEY ($name)";
        break;
      }
    }

    // Add foreign keys
    $lines = array_merge($lines, $foreignKeys);

    return "CREATE TABLE IF NOT EXISTS {$table} (\n" . implode(",\n", $lines) . "\n);";
  }

  public static function generateEnumTypes(): array
  {
    $queries = [];
    foreach (self::$enumTypes as $enumName => $values) {
      $escaped = array_map(fn($v) => "'" . addslashes($v) . "'", $values);
      $queries[] = "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = '{$enumName}') THEN CREATE TYPE {$enumName} AS ENUM (" . implode(', ', $escaped) . "); END IF; END $$;";
    }
    return $queries;
  }
}
