<?php

class Register extends CI_Controller
{
    public function index()
    {
        $base=base_url();
        if ($this->session->uid)
            redirect('/');
        else
            $this->load->view('register');
        
    }
}

?>