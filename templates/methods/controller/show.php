    /*
    * Show resource
    */
    function show($id)
    {
        $data['resource'] = $this->ResourceClassName->get_resource($id);
        $data['content'] = 'resources/show';
        $this->load->view('layouts/application', $data);
    }
