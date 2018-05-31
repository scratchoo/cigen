<?php if( !defined('BASEPATH')) exit("No direct script access allowed");

class Migrate extends CI_Controller {

	protected $_migrations_path ;
	protected $_migration_files;

	public function __construct()
	{
		parent::__construct();

		$this->input->is_cli_request()
		or exit("Execute via command line: php index.php migrate");

		$this->load->library('migration');
		$this->_migrations_path = APPPATH . 'migrations/';
		$this->set_available_migrations();

		set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext){
			// error was suppressed with the @-operator
			if (0 === error_reporting()) {
				return false;
			}
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
	}

	function create_db($db_name){
		$this->load->dbforge();
		if ($this->dbforge->create_database($db_name));
		{
			echo PHP_EOL;
			echo "Database {$db_name} created!" . PHP_EOL;
			echo PHP_EOL;
		}
	}

	function drop_db($db_name){
		 try
		 {
			 $this->db->close();
 			if ($this->dbforge->drop_database($db_name))
 			{
 				echo PHP_EOL;
 				echo "Database {$db_name} deleted!" . PHP_EOL;
 				echo PHP_EOL;

 			}
		 }
		 catch (Exception $e)
		 {
		 	echo "\nError of connexion somewhere is opened and database cannot be deleted!, for a fix/hack open config/database.php and temporary set: \n\n'database' => '' \n\nthen re-run the command, and finally set back the previous value of database name\n\n";
		 }

	}

	public function do($version = null)
	{
		if(empty($version))
		{
			try
			{
				if( $this->migration->latest() === FALSE)
				{
					show_error($this->migration->error_string());
				}
				else
				{
					echo PHP_EOL;
					echo 'Mirgrated successfully!' . PHP_EOL;
					echo PHP_EOL;
				}
			}
			catch(Exception $e)
			{
				echo $e->getMessage() . PHP_EOL;
			}
		}
		else
		{
			$before_up_version = $this->get_db_version();

			if(array_key_exists($version, $this->_migration_files))
			{
				if($version < $before_up_version)
				{
					$this->up_migration($version, $before_up_version);
				}
				else
				{
					$current_version = $this->get_db_version();
					echo PHP_EOL;
					echo "---> 0 file migrated !!! \n\n Sorry, you can only use migrate:up for old migrations than the current one. \n\n Just run 'php ci.php migrate' to migrate all newest migration files > $current_version" . PHP_EOL;
					echo PHP_EOL;
				}
			}
			else
			{
				echo PHP_EOL;
				echo "The migration you're trying to migrate doesn't exists!" . PHP_EOL;
				echo PHP_EOL;
			}
		}
	}

	public function undo($version=null)
	{
		$before_down_version = $this->get_db_version();

		if(empty($version))
		{
			// set $version to the last file in migrations folder
			$version = $this->last_key($this->_migration_files);
		}

		if(count($this->_migration_files) < 1)
		{
			echo PHP_EOL;
			echo "No migration file existing" . PHP_EOL;
			echo PHP_EOL;
			exit();
		}
		elseif(count($this->_migration_files) > 1)
		{
			if(array_key_exists($version, $this->_migration_files))
			{
				// $before_down_version == $version means this file is already migrated
				// if the version to undo is the last file in migrations folder
				// it's necessary to check && $this->last_key($this->_migration_files) == $version because if the version we want to rollback isn't the last then we should keep the old db migration version, otherwhise we change it to the last - 1
				if($before_down_version == $version && $this->last_key($this->_migration_files) == $version)
				{
					// so get the version of file before it, else it's not yet migrated so keep the version that is already in database (it will throw an error anyway when down is called)
					$before_down_version = $this->before_last_key($this->_migration_files);
				}
				// execute the following code, if the file version is not the latest, it will execute down without changing the previous value of db migration version
				$this->down_migration($version, $before_down_version);
			}
			else
			{
				echo PHP_EOL;
				echo "The migration you're trying to rollback doesn't exists!" . PHP_EOL;
				echo PHP_EOL;
			}


		}
		else
		{ // count($this->_migration_files) == 1

			if(array_key_exists($version, $this->_migration_files))
			{
				$db_migration_version = $this->get_db_version();
				if($before_down_version == $version){ // means this file is already migrated
					// so get because there is no file before it then set it to '0'
					$before_down_version = '0';
				}
				else
				{ // else don't execute down because it's not migrated
					exit();
				}
				$this->down_migration($version, $before_down_version);
			}
			else
			{
				echo PHP_EOL;
				echo "The migration you're trying to rollback doesn't exists!" . PHP_EOL;
				echo PHP_EOL;
			}
		}
	}

	function up_migration($version, $before_up_version)
	{
		$migration_full_filename = $this->_migration_files[$version];
		include_once $this->_migrations_path . $migration_full_filename;
		$filename_without_timestamp = preg_replace('#\d+_#', "", $migration_full_filename);
		$class_name = 'Migration_' . str_replace('.php', '', $filename_without_timestamp);
		$migration = new $class_name();
		$migration->up();
		$this->set_db_version($before_up_version);
		echo PHP_EOL;
		echo 'Migrated the file ' . $migration_full_filename . ' successfully.' . PHP_EOL;
		echo PHP_EOL;
	}

	function down_migration($version, $before_down_version)
	{

		$migration_full_filename = $this->_migration_files[$version];
		include_once $this->_migrations_path . $migration_full_filename;
		$filename_without_timestamp = preg_replace('#\d+_#', "", $migration_full_filename);
		$class_name = 'Migration_' . str_replace('.php', '', $filename_without_timestamp);
		$migration = new $class_name();
		$migration->down();
		rename($this->_migrations_path.$migration_full_filename, $this->_migrations_path.'rolledback_'.$migration_full_filename);
		$this->set_db_version($before_down_version);
		echo PHP_EOL;
		echo $migration_full_filename . ' has been rolled back successfully' . PHP_EOL;
		echo PHP_EOL;
		echo 'NOTE: You can remove the file rolledback_'.$migration_full_filename . ' if you won\'t need it for future use' . PHP_EOL;
		echo PHP_EOL;
	}

	function set_available_migrations()
	{
		$this->load->helper('file');
		$migrations_versions = [];
		$migrations_file_names = get_filenames($this->_migrations_path);
		foreach ($migrations_file_names as $f)
		{
			$arr = preg_split("/\D+/", $f);
			if(is_numeric($arr[0]))
			{
				$migrations_versions[(int) $arr[0]] = $f;
			}
		}
		ksort($migrations_versions);
		$this->_migration_files = $migrations_versions;
	}

	function last_key($arr)
	{
		end($arr);         // move the internal pointer to the end of the array
		$key = key($arr);  // fetches the key of the element pointed to by the internal pointer
		return $key;
	}

	function before_last_key($arr)
	{
		end($arr);         // move the internal pointer to the end of the array
		prev($arr);
		$key = key($arr);
		return $key;
	}

	function get_db_version()
	{
		$row = $this->db->get('migrations')->row();
		return $row ? $row->version : '0';
	}

	function set_db_version($version)
	{
		$this->db->update('migrations', array('version' => $version));
	}

	function version($version = 0)
	{
		if ($this->migration->version(abs($version)) === FALSE)
		{
			show_error($this->migration->error_string());
		}
		echo PHP_EOL;
		echo "Migrated to the version - "  . $version . PHP_EOL;
		echo PHP_EOL;
	}

}
