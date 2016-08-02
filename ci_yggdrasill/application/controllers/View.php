<?php

class View extends CI_Controller
{
    public function episode($group_id,$access_code)
    {
        $query = $this->db->get_where('episode', array('group_id' => $group_id, 'access_code' => $access_code), 1);
        $row = $query->row();
        if (isset($row))
            $ep_id=$row->ep_id;
        else
            show_404();
        
        $this->load->helper('url');
        $data['content']=$this->db->order_by('id','DESC')->get_where('chat', array('group_id' => $group_id, 'ep_id' => $ep_id))->result();
        $data['base_url']=base_url();
        $data['status']=$this->db->get_where('episode', array('group_id' => $group_id, 'ep_id' => $ep_id))->row()->status;
        $this->load->view('view', $data);
    }
}

?>