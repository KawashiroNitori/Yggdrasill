<?php

/**
 * @property  SenderModel $SenderModel
 */
class Sender extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('build_return');
        $this->load->model('SenderModel');
    }
    
    private function verify()
    {
        $signature=$this->input->get('signature');
        $timestamp=$this->input->get('timestamp');
        $nonce=$this->input->get('nonce');
        $sender=$this->input->get('sender');
        $msg=$this->input->raw_input_stream;
        if ($sender!=0 && $_SERVER['REQUEST_TIME']-$timestamp>30)
            die(build_return('invalid_time'));
        if (!$msg=json_decode($msg))
            die(build_return('illegal_msg'));
        else if ($sender===NULL || !$this->SenderModel->verifySender($signature,$timestamp,$nonce,$sender))
            die(build_return('verify_fail'));
        else if ($msg->type=='verify')
            exit(build_return('OK'));
        else if ($sender!=0 && $msg->type!='regcode' && !$this->SenderModel->verifyPermission($msg->group_id, $sender))
            die(build_return('access_denied'));
        
        return $msg;
        
    }
    
    public function process()
    {
        header("Content-type: application/json");
        header('Cache-Control: no-cache');

        $msg=$this->verify();
        $type='do'.str_replace(' ','',ucwords(str_replace('_',' ',$msg->type)));
        if (!method_exists($this,$type))
            die(build_return('unknown_command'));
        
        call_user_func_array(array($this,$type), array($msg));
    }

    private function doChat($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        
        $chat_text=trim(preg_replace('/\[CQ:.*\]/','',$msg->text));
        if (strlen($chat_text)==0 || 
        $msg->qq_id==1000000 || 
        strpos($chat_text,'(')===0 || 
        strpos($chat_text,'（')===0 || 
        strpos($chat_text,'）')===0 || 
        strpos($chat_text,'【')===0 || 
        $chat_text=='“”')
            die(build_return('OK', array('errmsg'=>'消息被忽略')));

        $this->SenderModel->insertChat($msg->group_id, $msg->qq_id, $msg->name, $chat_text);
        echo build_return('OK');
    }
    
    private function doDice($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        
        $this->SenderModel->insertDice($msg->group_id, $msg->qq_id, $msg->name, $msg->text, $msg->dice_result);
        echo build_return('OK');
    }
    
    private function doRollback($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        
        if (!$this->SenderModel->isRollbackable($msg->group_id, $msg->qq_id))
            die(build_return('no_rollback_msg'));
        
        $text=$this->SenderModel->getLastChat($msg->group_id, $msg->qq_id);
        if ($this->SenderModel->getTimeLastChatElapsed($msg->group_id, $msg->qq_id)>$msg->limit)
            die(build_return('timeout_rollback', array('text'=>$text)));
        
        $this->SenderModel->rollbackLastChat($msg->group_id, $msg->qq_id);
        echo build_return('OK', array('text'=>$text));
    }
    
    private function doStart($msg)
    {/*
        if ($this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_ended_episode'));*/
        
        if ($this->SenderModel->isPaused($msg->group_id) || $this->SenderModel->isRunning($msg->group_id))
        {
            $this->SenderModel->insertResume($msg->group_id, $msg->qq_id);
            $ep_id=$this->SenderModel->getLastEPID($msg->group_id);
            $access_code=$this->SenderModel->getAccessCode($msg->group_id, $ep_id);
            $content=$this->SenderModel->getLastContent($msg->group_id, $ep_id, 5);
            echo build_return('OK', array('resume'=>true, 'access_code'=>$access_code, 'content'=>$content));
        }
        else
        {
            $access_code=$this->SenderModel->insertStart($msg->group_id, $msg->qq_id);
            echo build_return('OK', array('resume'=>false, 'access_code'=>$access_code));
        }
    }
    
    private function doEnd($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        if (!isset($msg->name))
            $name=NULL;
        else
            $name=str_replace('#today#', date('Y.m.d', time()), $msg->name);
        $access_code=$this->SenderModel->insertEnd($msg->group_id, $msg->qq_id, $name);
        echo build_return('OK', array('access_code'=>$access_code));
    }    
    
    private function doPause($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        $this->SenderModel->insertPause($msg->group_id, $msg->qq_id);
        echo build_return('OK');
    }
    
    private function doBroadcast($msg)
    {
        if (!$this->SenderModel->isRunning($msg->group_id))
            die(build_return('no_running_episode'));
        
        $this->SenderModel->insertBroadcast($msg->group_id, $msg->text);
        echo build_return('OK');
    }
    
    private function doExport($msg)
    {
        if (is_numeric($msg->mode))
        {
            $ep_id=$msg->mode;
            $access_code=$this->SenderModel->getAccessCode($msg->group_id, $ep_id);
            $ep_name=$this->SenderModel->getEpisodeName($msg->group_id, $ep_id);
            $content=$this->SenderModel->getContentByEPID($msg->group_id, $ep_id);
        }
        else
        {
            $access_code=$this->SenderModel->getLatestAccessCode($msg->group_id);
            $ep_name=$this->SenderModel->getLatestEpisodeName($msg->group_id);
            $content=$this->SenderModel->getLatestContent($msg->group_id);
        }
        echo build_return('OK', array('access_code'=>$access_code, 'name'=>$ep_name, 'content'=>$content));
    }
    
    private function doStatus($msg)
    {
        $status=$this->SenderModel->getStatus($msg->group_id);
        echo build_return('OK', array('status'=>$status));
    }
    
    private function doGroupInfo($msg)
    {
        $group_name=html_entity_decode($msg->group_name);
        $this->SenderModel->updateGroupInfo($msg->group_id, $msg->admin, $group_name);
        echo build_return('OK');
    }
    
    private function doRegcode($msg)
    {
        $reg_code=$this->SenderModel->getRegcode($msg->qq_id);
        echo build_return('OK', array('reg_code'=>$reg_code));
    }
}

?>
