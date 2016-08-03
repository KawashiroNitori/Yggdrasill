<?php
class SenderModel extends CI_Model
{
    private function getRandomString($len)
    {
        if ($len<=0)
            return '';
        $area='0123456789ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $size=strlen($area);
        $result=$area[rand(0,$size-1)];
        $ch=$area[rand(0,$size-1)];
        for($i=1;$i<$len;$i++)
        {
            while($ch==$result[$i-1])
                $ch=$area[rand(0,$size-1)];
            $result.=$ch;
        }
        return $result;
    }
    
    private function arrangeContent($content)
    {
        /*
        foreach ($content as $row)
        {
            if ($row['type']=='rollback')
            {
                unset($row);
                continue;
            }
            $row['text']=$row['chat_text'];
            unset($row['chat_text']);
            if ($row['type']=='dice')
                $row['dice_result']=(int)$row['dice_result'];
            else
                unset($row['dice_result']);
            $row['qq_id']=(int)$row['qq_id'];
            if ($row['type']=='broadcast')
                unset($row['name']);
            unset($row['id']);
            unset($row['ep_id']);
            unset($row['uu_id']);
            unset($row['group_id']);
        }
        return $content;*/
        $output=array();
        
        foreach ($content as $row)
        {
            $type=$row['type'];
            if ($type=='rollback' || $type=='resume' || $type=='pause')
                continue;
            $tmpRow=array(
                'type'=>$type,
                'time'=>$row['time'],
                'text'=>$row['chat_text'],
                'name'=>$row['name'],
                'qq_id'=>(int)$row['qq_id']
            );
            if ($type=='dice')
                $tmpRow['dice_result']=(int)$row['dice_result'];
            if ($type=='broadcast')
                unset($tmpRow['qq_id']);
            $output[]=$tmpRow;
        }
        
        return $output;
    }
    
    public function verifySender($signature, $timestamp, $nonce, $sender)
    {
        if ($sender==0)
            return true;
        
        $row=$this->db->get_where('sender', array('sender'=>$sender), 1)->row();
        if (!isset($row))
            return false;
        else
        {
            $token=$row->token;
            $tmpArr=array($token, $timestamp, $nonce, $sender);
            sort($tmpArr, SORT_STRING);
            $tmpStr=implode($tmpArr);
            $tmpHash=hash('sha256', $tmpStr);
            
            return $signature==$tmpHash;
        }
    }
    
    public function verifyPermission($group_id, $sender)
    {
        $row=$this->db->get_where('group_permission', array('group_id'=>$group_id), 1)->row();
        if (!isset($row))
        {
            $this->db->insert('group_permission', array('group_id'=>$group_id, 'qq_id'=>$sender));
            return true;
        }
        else
            return $row->qq_id==$sender;
    }
    
    public function isRunning($group_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        $row=$this->db->get_where('episode', array('id'=>$ep_id), 1)->row();
        if (!isset($row))
            return false;
        else
            return $row->status=='running';
    }
    
    public function isPaused($group_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        $row=$this->db->get_where('episode', array('id'=>$ep_id), 1)->row();
        if (!isset($row))
            return false;
        else
            return $row->status=='paused';
    }
    
    public function isRollbackable($group_id, $qq_id)
    {
        $id=$this->getLastEPID($group_id);
        $row=$this->db->get_where('chat', array('group_id'=>$group_id, 'ep_id'=>$id, 'qq_id'=>$qq_id, 'type'=>'chat'), 1)->row();
        return isset($row);
    }
    
    public function insertChat($group_id, $qq_id, $name, $chat_text)
    {
        $ep_id=$this->getLastEPID($group_id);
        $this->db->insert('chat', array('ep_id'=>$ep_id, 'chat_text'=>$chat_text, 'type'=>'chat', 'name'=>$name, 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (ep_id, chat_text, type, name, qq_id, group_id, uu_id) VALUES ($ep_id, '$chat_text', 'chat', '$name', $qq_id, $group_id, UUID())");
    }
    
    public function insertDice($group_id, $qq_id, $name, $chat_text, $dice_result)
    {
        $ep_id=$this->getLastEPID($group_id);
        $this->db->insert('chat', array('ep_id'=>$ep_id, 'chat_text'=>$chat_text, 'type'=>'dice', 'name'=>$name, 'qq_id'=>$qq_id, 'group_id'=>$group_id, 'dice_result'=>$dice_result, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (ep_id, chat_text, type, name, qq_id, group_id, dice_result, uu_id) VALUES ($ep_id, '$chat_text', 'dice', '$name', $qq_id, $group_id, $dice_result, UUID())");
    }
    
    public function insertBroadcast($group_id, $text)
    {
        $ep_id=$this->getLastEPID($group_id);
        $this->db->insert('chat', array('ep_id'=>$ep_id, 'chat_text'=>$text, 'type'=>'broadcast', 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (ep_id, chat_text, type, group_id, uu_id) VALUES ($ep_id, '$text', 'broadcast', $group_id, UUID())");
    }
    

    public function insertStart($group_id, $qq_id)
    {
        $access_code=$this->getRandomString(10);
        $this->db->insert('episode', array('group_id'=>$group_id, 'status'=>'running', 'access_code'=>$access_code, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO episode (group_id, status, access_code, uu_id) VALUES ($group_id, 'running', '$access_code', UUID())");
        $ep_id=$this->getLastEPID($group_id);
        $this->db->insert('chat', array('type'=>'start', 'ep_id'=>$ep_id, 'qq_id'=>$qq_id, 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (type, ep_id, qq_id, group_id, uu_id) VALUES ('start', $ep_id, $qq_id, $group_id, UUID())");
        return $access_code;
    }
    
    public function insertEnd($group_id, $qq_id, $name = NULL)
    {
        $ep_id=$this->getLastEPID($group_id);
        $ep_info=$this->db->get_where('episode', array('id'=>$ep_id), 1)->row();
        $this->db->insert('chat', array('type'=>'end', 'ep_id'=>$ep_id, 'qq_id'=>$qq_id, 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (type, ep_id, qq_id, group_id, uu_id) VALUES ('end', $ep_id, $qq_id, $group_id, UUID())");
        
        $start_time=preg_replace('/[ :]/','_',$ep_info->start_time);
        $end_time=preg_replace('/[ :]/','_',date("Y-m-d H:i:s",time()));
        
        if (!$name)
        {
            $name=$group_id.'_'.$start_time.'_'.$end_time;
        }
        
        $this->db->update('episode', array('end_time'=>$end_time, 'status'=>'ended', 'name'=>$name), array('id'=>$ep_id));
        return $ep_info->access_code;
    }
    
    public function insertPause($group_id, $qq_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        $this->db->insert('chat', array('type'=>'pause', 'ep_id'=>$ep_id, 'qq_id'=>$qq_id, 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (type, ep_id, qq_id, group_id, uu_id) VALUES ('pause', $ep_id, $qq_id, $group_id, UUID())");
        $this->db->update('episode', array('status'=>'paused'), array('id'=>$ep_id));
    }
    
    public function insertResume($group_id ,$qq_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        //$this->db->delete('chat', array('type'=>'pause', 'group_id'=>$group_id, 'ep_id'=>$ep_id));
        $this->db->insert('chat', array('type'=>'resume', 'ep_id'=>$ep_id, 'qq_id'=>$qq_id, 'group_id'=>$group_id, 'uu_id'=>uuid_create()));
        //$this->db->query("INSERT INTO chat (type, ep_id, qq_id, group_id, uu_id) VALUES ('resume', $ep_id, $qq_id, $group_id, UUID())");
        $this->db->update('episode', array('status'=>'running'), array('id'=>$ep_id));
    }
    
    public function rollbackLastChat($group_id, $qq_id)
    {
        $this->db->order_by('id', 'DESC')->limit(1)
        ->update('chat', array('type'=>'rollback'), array('group_id'=>$group_id, 'qq_id'=>$qq_id, 'type'=>'chat'));
    }
    
    public function getLastChat($group_id, $qq_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        return $this->db->order_by('id', 'DESC')
        ->get_where('chat', array('type'=>'chat', 'group_id'=>$group_id, 'ep_id'=>$ep_id, 'qq_id'=>$qq_id), 1)->row()->chat_text;
    }
    
    public function getLastEPID($group_id)
    {
        $row=$this->db->select_max('id', 'id')->where('group_id', $group_id)->get('episode')->row();
        if (!isset($row))
            return 0;
        else
            return $row->id;
    }
    
    public function getAccessCode($group_id, $ep_id)
    {
        return $this->db->get_where('episode', array('id'=>$ep_id), 1)->row()->access_code;
    }
    
    public function getTimeLastChatElapsed($group_id, $qq_id)
    {
        $row=$this->db->order_by('id', 'DESC')->get_where('chat', array('group_id'=>$group_id, 'qq_id'=>$qq_id), 1)->row();
        return time()-strtotime($row->time);
    }
    
    public function getEpisodeName($group_id, $ep_id)
    {
        return $this->db->get_where('episode', array('id'=>$ep_id), 1)->row()->name;
    }
    
    public function getStatus($group_id)
    {
        $ep_id=$this->getLastEPID($group_id);
        $result=$this->db->get_where('episode', array('id'=>$ep_id), 1)->row();
        if ($result)
            return $result->status;
        else
            return 'ended';
    }
    
    public function getLastContent($group_id, $ep_id, $limit = 5)
    {
        $content=$this->db->query("SELECT * FROM chat WHERE id IN (SELECT p.id FROM (SELECT id FROM chat WHERE type IN ('chat', 'dice', 'broadcast') AND group_id=$group_id AND ep_id=$ep_id ORDER BY id DESC LIMIT $limit) AS p) ORDER BY id")->result_array();
        
        $content=$this->arrangeContent($content);
        
        return $content;
    }
    
    public function getContentByEPID($group_id, $ep_id)
    {
        $content=$this->db->get_where('chat', array('ep_id'=>$ep_id))->result_array();
        $content=$this->arrangeContent($content);
        return $content;
    }
    
    public function getLatestAccessCode($group_id)
    {
        return $this->getAccessCode($group_id, $this->getLastEPID($group_id));
    }
    
    public function getLatestEpisodeName($group_id)
    {
        return $this->getEpisodeName($group_id, $this->getLastEPID($group_id));
    }
    
    public function getLatestContent($group_id)
    {
        return $this->getContentByEPID($group_id, $this->getLastEPID($group_id));
    }
    
    public function getRegcode($qq_id)
    {
        $row=$this->db->get_where('user', array('qq_id'=>$qq_id), 1)->row();
        if (!isset($row))
        {
            $reg_code=$this->getRandomString(16);
            $this->db->insert('user', array('qq_id'=>$qq_id, 'reg_code'=>$reg_code));
            return $reg_code;
        }
        else
            return $row->reg_code;
    }
    
    public function updateGroupInfo($group_id, $admin, $group_name)
    {
        $this->db->update('group_permission', array('group_name'=>$group_name, 'admin_qq'=>$admin), array('group_id', $group_id));
    }
}

?>
