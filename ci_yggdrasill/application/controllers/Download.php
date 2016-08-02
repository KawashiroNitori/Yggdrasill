<?php

class Download extends CI_Controller
{
    public function episode($group_id, $access_code)
    {
        $this->load->library('user_agent');
        $query = $this->db->get_where('episode', array('group_id' => $group_id, 'access_code' => $access_code), 1);
        $row = $query->row();
        if (isset($row))
            $ep_id=$row->ep_id;
        else
            show_404();
       
        $this->load->helper('url');
        if ($this->agent->is_mobile())
            header('Location: '.base_url()."view/episode/$group_id/$access_code");
        
        $content=$this->db->get_where('chat', array('group_id' => $group_id, 'ep_id' => $ep_id))->result();
        
        $ep_info=$this->db->get_where('episode', array('group_id' => $group_id, 'ep_id' => $ep_id), 1)->row();
        $start_time=preg_replace('/[ :]/','_',$ep_info->start_time);
        $end_time=preg_replace('/[ :]/','_',$ep_info->end_time);
        $filename=$group_id.'_'.$start_time.'_'.$end_time.'.txt';
        
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=$filename");
        
        $platform=$this->agent->platform();
        
        if (stristr($platform,'Windows'))
            $newlineChar="\r\n";
        else if (stristr($platform,'Mac'))
            $newlineChar="\r";
        else
            $newlineChar="\n";
        //echo $platform.$newlineChar;
        
        foreach($content as $row)
        {
            if ($row->type=='start' || $row->type=='end')
                continue;
            else if ($row->type=='chat')
                echo $row->name.' '.$row->time."$newlineChar".$row->chat_text;
            else if ($row->type=='dice')
                echo $row->name.' '.$row->time."$newlineChar".'【投骰】 '.$row->chat_text.' = '.$row->dice_result;
            else if ($row->type=='broadcast')
                echo "=============================$newlineChar".$row->chat_text."$newlineChar=============================";
            
            echo "$newlineChar$newlineChar";
        }
    }
}

?>