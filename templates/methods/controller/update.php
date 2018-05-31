    /*
    * Updating resource
    */
    function update($id)
    {
        $this->resource_validation();
        $data['resource'] = $this->ResourceClassName->get_resource($id);
        if(isset($data['resource']['id'])) {
            $params = $this->resource_params();
            $params = $this->security->xss_clean($params);
            $this->ResourceClassName->update_resource($id, $params);
            redirect('resources/index');
        }else {
            $this->edit($id);
        }
    }
