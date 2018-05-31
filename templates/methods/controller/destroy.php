    /*
    * Deleting resource
    */
    function destroy($id)
    {
        $data['resource'] = $this->ResourceClassName->get_resource($id);
        if(isset($data['resource']['id'])){
            $this->ResourceClassName->delete_resource($id);
            redirect('resources/index');
        }else{
            // flash  msg here
            redirect('resources/index');
        }
    }
