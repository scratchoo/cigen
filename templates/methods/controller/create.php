    /*
    * Adding a new resource
    */
    function create()
    {
        $this->resource_validation();
        $params = $this->resource_params();
        $params = $this->security->xss_clean($params);
        if($this->ResourceClassName->add_resource($params)){
            redirect('resources/index');
        }else{
            $this->new();
        }
    }
