<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('GroupModel');
    }
     
	public function index()
	{
		//echo '<meta property="qc:admins" content="604141335444164165635541556317663757" />';
		//$this->load->view('welcome_message');
        if (!$this->session->uid)
            redirect('/login');
        else
        {
            $data=array();
            $data['username']=$this->session->username;
            $data['groupList']=$this->GroupModel->getGroupInfo($this->session->qq_id);
            $this->load->view('main_page', $data);
        }
            
	}
    
    
}
