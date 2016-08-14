<?php

class Group extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('build_return');
        $this->load->model('GroupModel');
    }
    
    public function get_episode($group_id)
    {
        if (!in_array($group_id, $this->session->groups))
        {
            header('HTTP/1.1 403 Forbidden');
            return;
        }
        session_write_close();
        header('Content: application/json');
        $ep=$this->GroupModel->getEpisodeInfo($group_id);
        if (!$ep)
            die(build_return('group_not_exist'));
        $output=array();
        foreach($ep as $row)
        {
            $newRow=array();
            $newRow['uuid']=$row->uu_id;
            $newRow['name']=$row->name;
            $newRow['access_code']=$row->access_code;
            $newRow['status']=$row->status;
            $newRow['start_time']=strtotime($row->start_time);
            $newRow['end_time']=strtotime($row->end_time);
            $output[]=$newRow;
        }
        echo build_return('OK', array('info'=>$output));
    }
    
    public function get_chat($group_id, $lastUUID='')
    {
        session_write_close();
        $life=25;
        while ($life--)
        {
            $res=$this->GroupModel->getLatestChat($group_id, $lastUUID);
            $output=array();
            if ($res)
            {
                foreach($res as $row)
                {
                    if ($row->type=='rollback')
                        continue;
                    $newRow=array();
                    $newRow['uuid']=$row->uu_id;
                    $newRow['type']=$row->type;
                    $newRow['name']=htmlentities($row->name, ENT_QUOTES, 'UTF-8');
                    $newRow['qq_id']=(int)$row->qq_id;
                    $newRow['chat_text']=str_replace("\n", '<br>', htmlentities($row->chat_text, ENT_QUOTES, 'UTF-8'));
                    $newRow['time']=strtotime($row->time);
                    if ($row->type=='dice')
                        $newRow['dice_result']=$row->dice_result;
                    $output[]=$newRow;
                        
                }
                echo build_return('OK', array('content'=>$output));
                return;
            }
            sleep(1);
        }
        
        echo build_return('no_data');
    }
}

?>
