<?php

class DB_migrator
{

    protected $_db_name;

    function __construct($db_name)
    {
        if(empty($db_name)){
            echo PHP_EOL;
            echo "Please Specify your database name at the top of 'ci.php' file! ( Location ---> cigen/ci.php )" . PHP_EOL;
            echo PHP_EOL;
            exit();
        }
        $this->_db_name = $db_name;
    }

    function create_migration_file($argv)
    {
        array_shift($argv);
        $migration_label= array_shift($argv);
        $timestamp = date('YmdHis');
        $migration_folder = "../application/migrations/";
        $migration_file_name = "{$timestamp}_{$migration_label}.php";
        if(!file_exists($migration_folder . $migration_file_name)){
            $file_content = file_get_contents('templates/migration/migration_file.php');
            $file_content = str_replace('MigrationName', $migration_label, $file_content);
            $file_content = str_replace('upContent', '', $file_content);
            $file_content = str_replace('downContent', '', $file_content);
            file_put_contents($migration_folder . $migration_file_name, $file_content);
        }
    }

    function db_migrate()
    {
        echo shell_exec("php ../index.php migrate do");
    }

    function db_migrate_up($version)
    {
        if(empty($version)){
            exit('Please specify a version to migrate up.' .PHP_EOL);
        }
      	$version = str_replace('VERSION=', '', $version);
        echo shell_exec("php ../index.php migrate do {$version}");
    }

    function db_migrate_down($version)
    {
        if(empty($version)){
            exit('Please specify a version to migrate down.' .PHP_EOL);
        }
        $version = str_replace('VERSION=', '', $version);
        $command = "php ../index.php migrate undo {$version}";
        echo shell_exec($command);
    }

    function db_rollback($version)
    {
        // one rollback from the last position
        $command = "php ../index.php migrate undo";
        echo shell_exec($command);
    }

    function db_migrate_to($version)
    {
        if(empty($version)){
            exit('Please specify a version to migrate back to it.' .PHP_EOL);
        }
        $command = "php ../index.php migrate version {$version}";
        echo shell_exec($command);
    }

    function db_create()
    {
        $command = "php ../index.php migrate create_db {$this->_db_name}";
        echo shell_exec($command);
    }

    function db_drop()
    {
        $command = "php ../index.php migrate drop_db {$this->_db_name}";
        echo shell_exec($command);
    }

    function db_reset()
    {
        $this->db_drop();
        $this->db_create();
        $this->db_migrate();
    }
}
