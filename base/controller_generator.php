<?php

/*
* Class Generator : used to add generate scaffolding
* USAGE :
* 1 - in the terminal: cd generators
* 2 - run a command like: php scaffold.php generate products
* 3 - or like: php scaffold.php g students index create custom_method
*/

class Controller_generator extends Base_generator
{
    protected $_controller_name;
    protected $_controller_class_name;
    protected $_model_class_name;
    protected $_controller_methods;
    protected $_app_controllers_folder = "../application/controllers/";
    protected $_db_fields;

    function __construct($controller_name, $controller_methods = []){

        /* ===============================================================================
        * To change naming of files and folders and class names then change the following
        * ===============================================================================
        */
        $this->_controller_name = $controller_name;
        $this->_controller_file_name = ucfirst($controller_name) . '.php';
        $this->_controller_class_name = ucfirst($controller_name);
        $this->_model_file_name = ucfirst(Inflector::singularize($this->_controller_class_name)) . '.php';
        $this->_model_class_name = ucfirst(Inflector::singularize($this->_controller_class_name));
        $this->_views_folder_name = strtolower($this->camelcase_to_underscore($controller_name));

        $this->_resources_var = strtolower($this->camelcase_to_underscore($controller_name));
        $this->_resource_var = strtolower($this->camelcase_to_underscore($this->_model_class_name));

        /* =============================================================================== */

        $this->_controller_methods = $controller_methods;
    }

    function set_fields($fields){
        $this->_db_fields = $fields;
    }

    function run()
    {
        $view_folder = '../application/views/'.$this->_views_folder_name;

        if(!is_dir($view_folder))
        {
            mkdir($view_folder, 0777, true);

            if(count($this->_controller_methods) <= 0)
            {
                $this->_controller_methods = array("index", "show", 'new', 'create', 'edit', 'update', 'destroy');
            }

            $this->add_controller_head();

            foreach ($this->_controller_methods as $index => $method)
            {
                if(file_exists("templates/methods/controller/{$method}.php")){
                    $this->add_controller_method($method);
                    $this->create_view_file($method);
                }else{
                    $this->add_custom_method($method);
                }
            }

            if(in_array('create', $this->_controller_methods) || in_array('update', $this->_controller_methods)){
                $this->add_controller_method('resource_params');
				$this->add_controller_method('resource_validation');

            }

            $this->add_controller_foot();

        }

    }

    function add_controller_head()
    {
        $file_content = file_get_contents('templates/controller_head.php');
        $file_content = $this->replace_resources_names($file_content, $this->_controller_name);
        file_put_contents($this->_app_controllers_folder . $this->_controller_file_name, $file_content);
        $this->add_line_return($this->_app_controllers_folder . $this->_controller_file_name);
    }

    function add_controller_foot()
    {
        $file_content = file_get_contents('templates/controller_foot.php');
        file_put_contents($this->_app_controllers_folder.$this->_controller_file_name, $file_content, FILE_APPEND);
    }

    function add_controller_method($method_name)
    {
        $file_content = file_get_contents("templates/methods/controller/{$method_name}.php");
        $file_content = $this->replace_resources_names($file_content, $this->_controller_name);
		if(in_array($method_name, ['resource_params']) && !empty($this->_db_fields)){
            $file_content = $this->replace_post_params($file_content, $method_name);
        }
        file_put_contents($this->_app_controllers_folder . $this->_controller_file_name, $file_content, FILE_APPEND);
        $this->add_line_return($this->_app_controllers_folder . $this->_controller_file_name);
    }

    function add_custom_method($method_name)
    {
        $file_content = file_get_contents("templates/methods/controller/custom_method.php");
        $file_content = file_get_contents("templates/methods/controller/custom_method.php");
        $file_content = $this->replace_resources_names($file_content, $this->_controller_name);
        $file_content = str_replace('custom_method', $method_name, $file_content);
        file_put_contents($this->_app_controllers_folder . $this->_controller_file_name, $file_content, FILE_APPEND);
        $this->add_line_return($this->_app_controllers_folder . $this->_controller_file_name);
        file_put_contents("../application/views/$this->_views_folder_name/{$method_name}.php", "");
    }

    function create_view_file($view_name)
    {
        $file_content = file_get_contents("templates/views/{$view_name}.php");
        $file_content = $this->replace_resources_names($file_content, $this->_controller_name);
        file_put_contents("../application/views/{$this->_views_folder_name}/{$view_name}.php", $file_content);
    }

    function replace_post_params($file_content, $method_name){

        $tabs = $method_name == 'update' ? "\t\t\t\t" : "\t\t\t";
        $return_with_tabs = $method_name == 'update' ? "\n\t\t\t\t" : "\n\t\t\t";
        // 'field_name' => $this->input->post('field_name'),
        $params = '';
        foreach ($this->_db_fields as $field_name => $field_type) {
            $params .= '"'.$field_name.'" => $this->input->post("'.$field_name.'"),';
            $params .= $return_with_tabs;
        }

        $file_content = str_replace('// "field_name" => $this->input->post("field_name"),', $params, $file_content);
        return $file_content;
    }


}
