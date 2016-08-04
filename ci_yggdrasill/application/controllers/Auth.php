<?php

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('build_return');
        $this->load->model('AuthModel');
    }
    
    public function register()
    {
        header('Content: application/json');
        $request=(object)['username'=>$this->input->post('username'), 'password'=>$this->input->post('password'), 'reg_code'=>$this->input->post('reg_code')];
        if (strlen($request->username)<4)
            die(build_return('too_short_username'));
        else if (strlen($request->password)<6)
            die(build_return('too_short_password'));
        else if (strlen($request->username)>18)
            die(build_return('too_long_username'));
        else if (strlen($request->password)>24)
            die(build_return('too_long_password'));
        else if (!preg_match('/^[a-zA-Z0-9_]+$/', $request->username))
            die(build_return('illegal_char_in_username'));
        else if (!$this->AuthModel->isValidRegcode($request->username, $request->reg_code))
            die(build_return('invalid_regcode'));
        else if ($this->AuthModel->isUsedUsername($request->username))
            die(build_return('duplicate_username'));
/*
        $pass=$request->password . $res->reg_code;
        $this->db->update('user',array('username'=>$request->username,'password'=>hash('sha256',$pass)),array('reg_code'=>$res->reg_code));*/
        
        $res=$this->AuthModel->regUser($request->username, $request->password, $request->reg_code);

        $this->session->set_userdata(array('uid'=>$res->id, 'username'=>$res->username, 'qq_id'=>$res->qq_id));
        
        echo build_return('OK');
    }
    
    public function login()
    {
        header('Content: application/json');
        $request=(object)['username'=>$this->input->post('username'), 'password'=>$this->input->post('password')];
        if ($request->username=='')
            die(build_return('empty_username'));
        else if ($request->password=='')
            die(build_return('empty_password'));
        else if (!$this->AuthModel->isCorrectPassword($request->username, $request->password))
            die(build_return('error_username_or_password'));
        
        $res=$this->AuthModel->getUserInfo($request->username);
        $this->session->set_userdata(array('uid'=>$res->id, 'username'=>$res->username, 'qq_id'=>$res->qq_id));
        
        echo build_return('OK');
    }
    
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/login');
        //build_return('OK');
    }
}

?>
