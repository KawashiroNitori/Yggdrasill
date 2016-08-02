<?php

class AuthModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function isValidRegcode($username, $regcode)
    {
        $regcode=trim($regcode);
        $res=$this->db->get_where('user', array('reg_code'=>$regcode), 1)->row();
        return isset($res) && $res->username=='';
    }
    
    public function isUsedUsername($username)
    {
        $username=trim($username);
        $res=$this->db->get_where('user', array('username'=>$username), 1)->row();
        return isset($res);
    }
    
    public function regUser($username, $password, $regcode)
    {
        $this->db->update('user', array('username'=>$username, 'password'=>hash('sha256', $password.$regcode)), array('reg_code'=>$regcode));
        return $this->db->get_where('user', array('username'=>$username), 1)->row();
    }
    
    public function updateUserPassword($username, $password)
    {
        $res=$this->db->get_where('user', array('username'=>$username), 1)->row();
        $this->db->update('user', array('password'=>hash('sha256', $password.$res->reg_code)), array('username'=>$username));
    }
    
    public function isCorrectPassword($username, $password)
    {
        $res=$this->db->get_where('user', array('username'=>$username), 1)->row();
        return isset($res) && $res->password==hash('sha256', $password.$res->reg_code);
    }
    
    public function getUserInfo($username)
    {
        return $this->db->get_where('user', array('username'=>$username), 1)->row();
    }
}

?>