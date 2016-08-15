<?php

class Login extends CI_Controller
{
    public function index()
    {
        if ($this->session->uid)
            redirect('/');
        else
            $this->load->view('login');
    }
}

?>