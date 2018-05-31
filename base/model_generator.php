<?php

/*
* Class Migrator : used to add generate migrations files
* USAGE :
* 1 - in the terminal: cd generators
* 2 - run a command like: php migration.php generate product title:string price:decimal
* 3 - or like: php scaffold.php g student
*/

class Model_generator extends Base_generator{

    protected $_model_name;
    protected $_table_name;
    protected $_migrations_folder = '../application/migrations/';
    protected $_normalized_fields = [];
    protected $_database_type;
    protected $_controller_name;
    protected $_model_file_name;

    function __construct($database_type, $model_name, $fields){
        $this->_model_name = $model_name;
        $this->_table_name = strtolower(Inflector::pluralize($this->_model_name));
        $this->normalize_fields($fields, $database_type);
        $this->_database_type = $database_type;
        $this->_controller_name = Inflector::pluralize($this->_model_name);
        $this->_model_file_name = ucfirst($model_name) . '.php';
    }


    function create_model_file()
    {
        $file_content = file_get_contents('templates/model.php');
        $file_content = $this->replace_resources_names($file_content, $this->_controller_name);
        file_put_contents("../application/models/".$this->_model_file_name, $file_content);
    }

    /*
    *   transform title:string to => array('title' => 'VARCHAR',
    */
    function normalize_fields($fields, $database_type)
    {

        foreach ($fields as $field)
        {
            if (strpos($field, ':') == false) {
                echo PHP_EOL;
                echo "== ERROR ========================================================" . PHP_EOL;
                echo "---> You need to specify a datatype for $field (example: $field:string or $field:integer....)" . PHP_EOL;
                echo "==================================================================" . PHP_EOL;
                echo PHP_EOL;
                exit();
            }
            $field_details = explode(":", $field);
            $field_name = $field_details[0];
            $field_type = $field_details[1];

            $fields_mapping_method = "{$database_type}_data_types";
            $field_type = $this->$fields_mapping_method($field_type);
            $this->_normalized_fields[$field_name] = $field_type;

        }
    }

	function get_normalized_fields(){
		return $this->_normalized_fields;
	}

    function run()
    {
        $this->create_model_file();
        $this->create_migrate_controller();
        $this->create_migrations_folder();
        $this->create_migration_file();
    }

    function create_migrate_controller()
    {
        $migrate_path = "../application/controllers/migrate.php";
        if(!file_exists($migrate_path)){
            $file_content = file_get_contents('templates/migration/Migrate.php');
            file_put_contents($migrate_path, $file_content);
        }
    }

    function create_migrations_folder()
    {
        if (!is_dir($this->_migrations_folder))
        {
            mkdir($this->_migrations_folder, 0777, true);
        }
    }

    function create_migration_file(){
        $timestamp = date('YmdHis');
        $migration_folder = "../application/migrations/";
        $migration_file_name = "{$timestamp}_create_{$this->_table_name}.php";
        if(!file_exists($this->_migrations_folder . $migration_file_name)){
            $file_content = file_get_contents('templates/migration/migration_file.php');
            $file_content = $this->replace_migration_classname($file_content);
            $file_content = $this->replace_up_content($file_content);
            $file_content = $this->replace_down_content($file_content);
            file_put_contents($this->_migrations_folder . $migration_file_name, $file_content);
        }
    }

    // copied from rails repo: https://github.com/rails/rails/blob/master/activerecord/lib/active_record/connection_adapters/abstract_mysql_adapter.rb#L148
    function mysql_data_types($field){
        $mysql_types = [
            // "primary_key" =>    "bigint auto_increment PRIMARY KEY",
            "primary_key"  =>   [ "name" => "bigint" ],
            "string"      =>    [ "name" => "varchar", "limit" => 255],
            "text"        =>    [ "name" => "text", "limit" => 65535 ],
            "integer"     =>    [ "name" => "int", "limit" => 4 ],
            "float"       =>    [ "name" => "float", "limit" => 24 ],
            "decimal"     =>    [ "name" => "decimal" ],
            "datetime"    =>    [ "name" => "datetime" ],
            "timestamp"   =>    [ "name" => "timestamp" ],
            "time"        =>    [ "name" => "time" ],
            "date"        =>    [ "name" => "date" ],
            "binary"      =>    [ "name" => "blob", "limit" => 65535],
            "boolean"     =>    [ "name" => "tinyint", "limit" => 1 ],
            "json"        =>    [ "name" => "json" ],
        ];
        return $mysql_types[$field];
    }

    // copied from rails repo: https://github.com/rails/rails/blob/master/activerecord/lib/active_record/connection_adapters/postgresql_adapter.rb
    function postgres_data_types($field){

        $postgres_types = [
            // "primary_key"  =>   "bigserial primary key",
            "primary_key"  =>   [ "name" => "bigserial" ],
            "string"       =>   [ "name" => "character varying" ],
            "text"         =>   [ "name" => "text" ],
            "integer"      =>   [ "name" => "integer", "limit" => "4" ],
            "float"        =>   [ "name" => "float" ],
            "decimal"      =>   [ "name" => "decimal" ],
            "datetime"     =>   [ "name" => "timestamp" ],
            "time"         =>   [ "name" => "time" ],
            "date"         =>   [ "name" => "date" ],
            "daterange"    =>   [ "name" => "daterange" ],
            "numrange"     =>   [ "name" => "numrange" ],
            "tsrange"      =>   [ "name" => "tsrange" ],
            "tstzrange"    =>   [ "name" => "tstzrange" ],
            "int4range"    =>   [ "name" => "int4range" ],
            "int8range"    =>   [ "name" => "int8range" ],
            "binary"       =>   [ "name" => "bytea" ],
            "boolean"      =>   [ "name" => "boolean" ],
            "xml"          =>   [ "name" => "xml" ],
            "tsvector"     =>   [ "name" => "tsvector" ],
            "hstore"       =>   [ "name" => "hstore" ],
            "inet"         =>   [ "name" => "inet" ],
            "cidr"         =>   [ "name" => "cidr" ],
            "macaddr"      =>   [ "name" => "macaddr" ],
            "uuid"         =>   [ "name" => "uuid" ],
            "json"         =>   [ "name" => "json" ],
            "jsonb"        =>   [ "name" => "jsonb" ],
            "ltree"        =>   [ "name" => "ltree" ],
            "citext"       =>   [ "name" => "citext" ],
            "point"        =>   [ "name" => "point" ],
            "line"         =>   [ "name" => "line" ],
            "lseg"         =>   [ "name" => "lseg" ],
            "box"          =>   [ "name" => "box" ],
            "path"         =>   [ "name" => "path" ],
            "polygon"      =>   [ "name" => "polygon" ],
            "circle"       =>   [ "name" => "circle" ],
            "bit"          =>   [ "name" => "bit" ],
            "bit_varying"  =>   [ "name" => "bit varying" ],
            "money"        =>   [ "name" => "money" ],
            "interval"     =>   [ "name" => "interval" ],
            "oid"          =>   [ "name" => "oid" ],
        ];
        return $postgres_types[$field];
    }


    function replace_migration_classname($file_content){
        $content_replacement = 'create_' . $this->_table_name;
        $file_content = str_replace('MigrationName', $content_replacement, $file_content);
        return $file_content;
    }

    function replace_up_content($file_content){
        $content_replacement = $this->get_up_content();
        $file_content = str_replace('upContent', $content_replacement, $file_content);
        return $file_content;
    }

    function replace_down_content($file_content){
        $content_replacement = $this->get_down_content();
        $file_content = str_replace('downContent', $content_replacement, $file_content);
        return $file_content;
    }

    function get_up_content()
    {
        $return_plus_2tabulations = "\n\t\t";
        $return_plus_3tabulations = "\n\t\t\t";
        $return_plus_4tabulations = "\n\t\t\t\t";

        $content = '$this->dbforge->add_field(array(';
        $content .= $return_plus_3tabulations;
        $primary_key_type = ($this->_database_type == 'postgres') ? $this->postgres_data_types("primary_key")["name"] : $this->mysql_data_types("primary_key")["name"] ;
        $content .= "'id' => array(";
        $content .= $return_plus_4tabulations;
        $content .= "'type' => '{$primary_key_type}',";
        $content .= $return_plus_4tabulations;
        $content .= "'unsigned' => TRUE,";
        $content .= $return_plus_4tabulations;
        $content .= "'auto_increment' => TRUE";
        $content .= $return_plus_3tabulations;
        $content .= "),";

        foreach ($this->_normalized_fields as $field_name => $data_type) {
            $content .= $return_plus_3tabulations;
            $content .= "'{$field_name}' => array(";
            $content .= $return_plus_4tabulations;
            $content .= "'type' => '{$data_type['name']}'";
            $content .= ',';
            if (count($data_type) > 1 && isset($data_type['limit'])) {
                $content .= $return_plus_4tabulations;
                $content .= "'constraint' => '{$data_type['limit']}',";
            }
			$content .= $return_plus_4tabulations;
            $content .= "'null' => TRUE";
            $content .= $return_plus_3tabulations;
            $content .= "),";
        }

        $datetime_type = ($this->_database_type == 'postgres') ? $this->postgres_data_types("datetime")["name"] : $this->mysql_data_types("datetime")["name"] ;

        // commit to support timestamp as default: https://github.com/bcit-ci/CodeIgniter/commit/21b7a2a2d00bd5645b2ca1afcfa4098e207292a4
        // thread: https://github.com/bcit-ci/CodeIgniter/issues/4852
        // until then we use $this->dbforge->add_field....
        // $content .= $return_plus_3tabulations;
        // $content .= "'created_at' => array(";
        // $content .= $return_plus_4tabulations;
        // $content .= "'type' => '{$datetime_type}',";
        // $content .= $return_plus_4tabulations;
        // $content .= "'default' => CURRENT_TIMESTAMP";
        // $content .= $return_plus_3tabulations;
        // $content .= "),";
        //
        // $content .= $return_plus_3tabulations;
        // $content .= "'updated_at' => array(";
        // $content .= $return_plus_4tabulations;
        // $content .= "'type' => '{$datetime_type}',";
        // $content .= $return_plus_4tabulations;
        // $content .= "'default' => CURRENT_TIMESTAMP";
        // $content .= $return_plus_3tabulations;
        // $content .= ")";

        $content .= $return_plus_2tabulations;
        $content .= "));";

        $content .= $return_plus_2tabulations;
        $content .= '$this->dbforge->add_field("created_at '.$datetime_type.' NOT NULL DEFAULT CURRENT_TIMESTAMP");';
        $content .= $return_plus_2tabulations;
        $content .= '$this->dbforge->add_field("updated_at '.$datetime_type.' NOT NULL DEFAULT CURRENT_TIMESTAMP");';


        $content .= $return_plus_2tabulations;
        $content .= '$this->dbforge->add_key("id", TRUE);';
        $content .= $return_plus_2tabulations;
        $content .= '$this->dbforge->create_table("' . $this->_table_name . '");';
        return $content;
    }

    function get_down_content(){
        $content = '$this->dbforge->drop_table("' . $this->_table_name . '");';
        return $content;
    }

}
