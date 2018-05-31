<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ResourceClassName extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*
     * Get resource by id
     */
    function get_resource($id)
    {
        $resource = $this->db->query("SELECT * FROM resources WHERE id = ?", array($id))->row_array();
        return $resource;
    }

    /*
     * Get all resources
     */
    function get_all_resources()
    {
        $resources = $this->db->query("SELECT * FROM resources ORDER BY id DESC")->result_array();
        return $resources;
    }

    /*
     * add new resource
     */
    function add_resource($params)
    {
        $this->db->insert('resources',$params);
        return $this->db->insert_id();
    }

    /*
     * update resource
     */
    function update_resource($id,$params)
    {
        $this->db->where('id',$id);
        return $this->db->update('resources', $params);
    }

    /*
     * delete resource
     */
    function delete_resource($id)
    {
        return $this->db->delete('resources', array('id'=>$id));
    }
}
