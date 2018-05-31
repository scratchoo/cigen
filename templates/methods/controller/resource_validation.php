	/*
	* validate parameters
	*/
	private function resource_validation(){
		$this->load->library('form_validation');
		// $this->form_validation->set_rules('field','field label','required');
		if($this->form_validation->run() == FALSE){ // if validation failed
			$failed_validation_render = [
				'create' => 'new',
				'update' => 'edit'
			];
			$current_method = $this->router->fetch_method();
			$action = $failed_validation_render[$current_method];
			$this->$action(); exit();
		}
	}
