    /*
    * Editing resource form
    */
    function edit($id)
    {
        $data['resource'] = $this->ResourceClassName->get_resource($id);
        $data['content'] = 'resources/edit';
        $this->load->view('layouts/application', $data);
    }
