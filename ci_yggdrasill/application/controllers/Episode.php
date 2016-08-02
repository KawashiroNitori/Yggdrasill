<?php

class Episode extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('user_agent');
        $this->load->model('GroupModel');
    }
    
    public function view($group_id,$access_code)
    {
        $query = $this->db->get_where('episode', array('group_id' => $group_id, 'access_code' => $access_code), 1);
        $row = $query->row();
        if (isset($row))
            $ep_id=$row->id;
        else
            show_404();
        if ($row->status=='running')
            $data['content']=$this->db->order_by('id','DESC')->get_where('chat', array('group_id' => $group_id, 'ep_id' => $ep_id))->result();
        else
            $data['content']=$this->db->get_where('chat', array('group_id' => $group_id, 'ep_id' => $ep_id))->result();
        $data['group_id']=$group_id;
        $data['access_code']=$access_code;
        $data['is_mobile']=$this->agent->is_mobile();
        $data['base_url']=base_url();
        $data['status']=$this->db->get_where('episode', array('group_id' => $group_id, 'id' => $ep_id))->row()->status;
        $data['group_name']=$this->GroupModel->getGroupName($group_id);
        $this->load->view('view', $data);
    }
    
    public function download($group_id, $access_code)
    {
        $query = $this->db->get_where('episode', array('group_id' => $group_id, 'access_code' => $access_code), 1);
        $ep_info = $query->row();
        if (isset($ep_info) && $ep_info->status=='ended')
            $ep_id=$ep_info->id;
        else
            show_404();
       
        if ($this->agent->is_mobile())
        {
            header('Location: '.base_url()."episode/view/$group_id/$access_code");
            die();
        }
        
        $platform=$this->agent->platform();
        if (stripos($platform, 'Windows')!==false)
            $eol="\r\n";
        else
            $eol="\n";
        
        $content=$this->db->get_where('chat', array('group_id' => $group_id, 'ep_id' => $ep_id))->result();
        /*
        $start_time=preg_replace('/[ :]/','_',$ep_info->start_time);
        $end_time=preg_replace('/[ :]/','_',$ep_info->end_time);*/
        $filename=$ep_info->name.'.txt';
        
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=$filename");
       
        
        foreach($content as $row)
        {
            if ($row->type=='start' || $row->type=='end')
                continue;
            else if ($row->type=='chat')
                echo $row->name.' '.$row->time.$eol.$row->chat_text;
            else if ($row->type=='dice')
                echo $row->name.' '.$row->time.$eol.'【投骰】 '.$row->chat_text.' = '.$row->dice_result;
            else if ($row->type=='broadcast')
                echo "=============================".$eol.$row->chat_text.$eol."=============================";
            else
                continue;
            
            echo $eol.$eol;
        }
    }
}

?>