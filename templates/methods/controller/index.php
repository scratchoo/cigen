    /*
     * Listing of resources
     */
    function index()
    {
        $this->load->library('paginator');
        $data['resources'] = $this->paginator->paginate('resources', ['per_page' => 1, 'query_string' => true, 'base_url' => 'posts/index']);

        $data['content'] = 'resources/index';
        $this->load->view('layouts/application', $data);
    }
