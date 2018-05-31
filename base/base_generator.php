<?php

class Base_generator{

    function __construct(){}

    function replace_resources_names($file_content, $controller_name)
    {
        $controller_class_name = ucfirst($controller_name);
        $model_class_name = ucfirst(Inflector::singularize($controller_class_name));
        $resources_var = strtolower($this->camelcase_to_underscore($controller_name));
        $resource_var = strtolower($this->camelcase_to_underscore($model_class_name));

        $file_content = str_replace('ResourcesClassName', $controller_class_name, $file_content);
        $file_content = str_replace('ResourceClassName', $model_class_name, $file_content);
        $file_content = str_replace('resources', $resources_var, $file_content);
        $file_content = str_replace('resource', $resource_var, $file_content);


        return $file_content;
    }

    // add a return to line character (\n)
    function add_line_return($file_path)
    {
        file_put_contents($file_path, "\n", FILE_APPEND);
    }

    function underscore_exists($string)
    {
        if (strpos($string, '_') !== false) {
            return true;
        }
    }

    function underscore_to_camelcase($string, $lower=true)
    {
        if($lower == true){
            // underscored to lower-camelcase
            // "my_test_method" -> "myTestMethod"
            preg_replace('/_(.?)/e',"strtoupper('$1')",$string);
        }else{
            // underscored to upper-camelcase
            // "my_test_method" -> "MyTestMethod"
            preg_replace('/(?:^|_)(.?)/e',"strtoupper('$1')",$string);
        }
    }

    function camelcase_to_underscore($string)
    {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $string));
    }


}
