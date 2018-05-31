    /*
    * Show new form
    */
    function new()
    {
        $data['content'] = 'resources/new';
        $this->load->view('layouts/application', $data);
    }
