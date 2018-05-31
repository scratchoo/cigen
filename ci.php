<?php
// ------------------------------------------
//  DB Info
// ------------------------------------------

define("DB_NAME", 'depapp_development');
define("DEFAULT_DB_DRIVER", 'postgres'); // available drivers : 'postgres', 'mysql'

// ------------------------------------------

require_once("base/inflector.php"); // Needed to singularize plural controllers names
require_once("base/base_generator.php");
require_once("base/initializer.php");
require_once("base/controller_generator.php");
require_once("base/model_generator.php");
require_once("base/db_migrator.php");

if($argv[0] != 'ci.php')
{
    echo "File {$argv[0]} Not Found\n";
    exit();
}

array_shift($argv);

if(in_array($argv[0], ['db:create', 'db:drop', 'db:reset', 'db:migrate', 'migrate:up', 'migrate:down', 'db:rollback', 'db:migrate:to']))
{
    // https://stackoverflow.com/questions/9154065/how-do-i-run-codeigniter-migrations
    //array_shift($argv);
    $version = isset($argv[1]) ? $argv[1] : '';
    $db = new DB_migrator(DB_NAME);
    $migration_method = str_replace(':', '_', $argv[0]);
    $db->{$migration_method}($version);
}
elseif(in_array($argv[0], ['g', 'generate']))
{
    $database_type = DEFAULT_DB_DRIVER != null ? DEFAULT_DB_DRIVER : exit('DEFAULT_DB_DRIVER constant is not set in ci.php file');

    array_shift($argv);

    switch ($argv[0]) {

        case 'basics':
            $initializer = new Initializer();
            $initializer->init();
            break;

        case 'controller':
            array_shift($argv);
            $controller_name = array_shift($argv);
            $controller_methods = $argv;
            $scaffold = new Controller_generator($controller_name, $controller_methods);
            $scaffold->run();
            break;

        case 'model':
            array_shift($argv);
            $model_name = array_shift($argv);
            $model_name = $model_name;
            $fields = $argv;
            $migrator = new Model_generator($database_type, $model_name, $fields);
            $migrator->run();
            break;

        case 'migration':
            $db = new DB_migrator();
            $db->create_migration_file($argv);
            break;

        case 'scaffold':
            array_shift($argv);
            $model_name = array_shift($argv);
            $fields = $argv;

            // this will generate the migration file and related files
            $model_gen = new Model_generator($database_type, $model_name, $fields);
            $model_gen->run();

            $controller_name = Inflector::pluralize($model_name);
            $controller_gen = new Controller_generator($controller_name);
            $controller_gen->set_fields($model_gen->get_normalized_fields());
            $controller_gen->run();

            break;

        case '--help':
            echo PHP_EOL . "* Options :". PHP_EOL . "----------" . PHP_EOL;
            echo PHP_EOL . "basics : Generate some initial files/helpers" . PHP_EOL;
            echo PHP_EOL . "model : Generate model with attributes" . PHP_EOL;
            echo PHP_EOL . "migration : Generate a base empty migration file" . PHP_EOL;
            echo PHP_EOL . "scaffold : Generate scaffolding (controller+model+migration) for the passed resources" . PHP_EOL;
            echo PHP_EOL;
            break;

        default:
            echo "Option {$argv[0]} Not Found\n";
    }

}
else
{
    echo "Command {$argv[0]} Not Found\n";
    exit();
}
