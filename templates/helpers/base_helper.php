<?php

    function pagination($options){
        $this->load->library('pagination');
        $config['base_url'] = site_url("{$options['base_url']}");
        $config['total_rows'] = $options['total_rows'];
        $config['per_page'] = $options['per_page'];
        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }



    function crud_rest_support(){

        $ci = & get_instance();

        /* in core MY_Security I overrided csrf_show_error which solve the problem of :
        * (browser clicking back button and submit the form will show csrf error
        * because browser have cache for the old csrf)
        * - In case it doesn't work uncomment the code bellow to clear cache everytime -
        */
        // clear cache to prevent backward access
        // this prevent csrf problem when you go back to a form (it force renewing the csrf token )
        // $ci->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        // $ci->output->set_header("Pragma: no-cache");
        # ===================================================================================================================================

        $controller = $ci->router->fetch_class();
        $method = $ci->router->fetch_method();


        if($method == 'show' || $method == 'edit' ){
            if(empty($ci->uri->segment(3)) || !empty($ci->uri->segment(4))){
                show_404();
            }
        }


        if($method == 'create'){
            if (!isset($_POST) || empty($_POST)) {
                show_404();
            }
        }

        if($method == 'update' || $method == 'destroy'){
            if(!isset($_POST) || empty($_POST) || empty($ci->uri->segment(3)) || !empty($ci->uri->segment(4))){
                show_404();
            }
        }

        // inspired from http://ahex.co/php-and-codeigniter/
        // if( isset($_POST) && isset($_POST['_method']) && in_array($_POST['_method'], ['put', 'delete']) ){
        //     $action_name = in_array($_POST['_method'], ['put', 'patch']) ? 'update' : 'destroy';
        //     if(empty($ci->uri->segment(3)) || !empty($ci->uri->segment(4))){
        //         show_404();
        //     }
        //     call_user_func(array($ci, $action_name), $arr_argument='');
        // }

    }
