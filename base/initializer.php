<?php

/*
* Class Initializer : used to add some base files like helpers/base_helper and core/MY_Security to the project
* USAGE :
* 1 - in the terminal go to generators folder: cd generators
* 2 - run the following command: php scaffold.php init
*/
class Initializer{

    function __construct(){}

    function init(){

        if (!is_dir("../application/assets")){
            $this->create_assets_folders();
            $this->create_assets_files();
        }

        if (!is_dir("../application/views/layouts")) {
            $this->create_layout_folder();
            $this->create_master_page();
        }

        if(!file_exists("../application/helpers/base_helper.php")){
            $this->create_base_helper();
        }

        if(!file_exists("../application/core/MY_Security.php")){
            $this->create_mysecurity_file();
        }

    }

    function create_layout_folder()
    {
        mkdir("../application/views/layouts", 0777, true);
    }

    function create_master_page()
    {
        $file_content = file_get_contents('templates/views/layouts/application.php');
        file_put_contents("../application/views/layouts/application.php", $file_content);
    }

    function create_assets_folders()
    {
        mkdir("../application/assets", 0777, true);
        mkdir("../application/assets/stylesheets", 0777, true);
        mkdir("../application/assets/javascripts", 0777, true);
        mkdir("../application/assets/images", 0777, true);
    }

    function create_assets_files()
    {
        $css_content = file_get_contents('templates/assets/application.css');
        file_put_contents("../application/assets/stylesheets/application.css", $file_content);
        $js_content = file_get_contents('templates/assets/application.js');
        file_put_contents("../application/assets/javascripts/application.js", $js_content);
        $ujs_content = file_get_contents('templates/assets/ujs.js');
        file_put_contents("../application/assets/javascripts/ujs.js", $ujs_content);
        // TODO: use uglify js to compress js http://webdevzoom.com/compress-javascript-file-using-uglifyjs/
    }

    function create_base_helper(){
        $file_content = file_get_contents('templates/helpers/base_helper.php');
        file_put_contents("../application/helpers/base_helper.php", $file_content);
    }

    function create_mysecurity_file(){
        $file_content = file_get_contents('templates/core/MY_Security.php');
        file_put_contents("../application/core/MY_Security.php", $file_content);
    }

}
