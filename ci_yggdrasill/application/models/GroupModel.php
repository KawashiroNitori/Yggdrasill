<?php

class GroupModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SenderModel');
    }

    public function getGroupName($group_id)
    {
        $res=$this->db->get_where('group_permission', array('group_id'=>$group_id), 1)->row();
        return $res->group_name;
    }
    
    public function getGroupStatus($group_id)
    {
        $res=$this->db->select_max('id', 'id')->where('group_id',$group_id)->get('episode')->row();
        return $this->db->get_where('episode', array('id'=>$res->id))->row()->status;
    }

    public function getGroupInfo($qq_id)
    {
        $res=$this->db->distinct()->select('chat.group_id')
            ->select('group_permission.group_name AS name')
            ->from('chat')
            ->join('group_permission', 'group_permission.group_id=chat.group_id')
            ->where('chat.qq_id', $qq_id)
            ->get()->result();
        if (!$res)
            return false;
        foreach ($res as &$group)
        {
            $group->status=$this->getGroupStatus($group->group_id);
        }
        return $res;
    }
    
    public function getEpisodeInfo($group_id)
    {
        $res=$this->db->select()->where('group_id', $group_id)->order_by('id', 'DESC')->get('episode')->result();
        return $res;
    }
    
    public function getLatestChat($group_id, $lastUUID='')
    {
        if ($lastUUID!='')
            $lastRow=$this->db->get_where('chat', array('uu_id'=>$lastUUID), 1)->row();
        else
        {
            return $this->db->get_where('chat', array('group_id'=>$group_id, 'ep_id'=>$this->SenderModel->getLastEPID($group_id)))->result();
        }
    
        if (!$lastRow)
            return false;
        
        $res=$this->db->select()->where('group_id',$group_id)->where('id >',$lastRow->id)->get('chat')->result();
        return $res;
    }
}

?>
