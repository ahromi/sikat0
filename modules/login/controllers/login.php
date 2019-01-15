<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MX_Controller {

	public function __construct() 
	{
		parent::__construct();
		
		if(!isset($_SESSION)) 
		{ 
			session_start(); 
		} 

		
		//session_start();
		$this->load->model('model_login');//load model for login
	}
	
	function index()
	{
		if ($this->session->userdata('feun_abus') == 'true' && $this->session->userdata('feun_username') <> '')
		{
			redirect('index.php/dashboard');
		}
		
		$data['pesan']		= $this->session->flashdata('gagal');
		$data['pesan2']		= $this->session->flashdata('sukses');
		$this->load->view('view_login',$data);
		
	}
	
	function checkLogin()
	{
		if($this->input->post('uname') && $this->input->post('upass'))
		{
			//require_once ('system/libraries/func.php');
			//$deny_login = Services::bruteCheck();
			// if($deny_login) 
			// {
			//	$data['pesan']="Anda Sudah Mencapai Maksimal Login";
			//	$this->load->view('default',$data); 
			// } 
			// else 
			// {
				$this->load->library('form_validation'); //load library form_validation
				$this->form_validation->set_rules('uname', 'Username', 'required|xss_clean'); //cek, validasi username
				$this->form_validation->set_rules('upass', 'Password', 'required|xss_clean'); //cek, validasi password
				if ( $this->form_validation->run() == TRUE ) 
				{				
					$result = $this->model_login->masuk( 
										 mysql_real_escape_string($this->security->xss_clean($this->input->post('uname'))), 
										 mysql_real_escape_string($this->security->xss_clean($this->input->post('upass'))) 
									  );
									  
					if ( $result == TRUE) //apabila dilempar dari model true
					{ 	
						 $user=$this->input->post('uname');
						 $this->session->set_userdata('feun_abus', 'true');
						 $this->session->set_userdata('feun_username',$result[0]['username']);
						 $this->session->set_userdata('feun_fullname',$result[0]['fullname']);
						 $this->session->set_userdata('feun_key',$result[0]['private_key']);
						 $this->session->set_userdata('feun_id',$result[0]['id_user']); 
						 $this->session->set_userdata('feun_usertype',$result[0]['id_usertype']); 
						 $this->session->set_userdata('feun_email',$result[0]['email_user']); 
						 
						 redirect('index.php/dashboard');		
					}
					else
					{
						//Services::bruteCheck(true);
						//$data['pesan']="Login gagal<br> Periksa kembali Username, Password, dan Kode Verifikasi";
						$this->session->set_flashdata('gagal', 'Username/Password anda salah, silakan ulangi kembali');
						header('location: '.base_url());
					}
				}
				else
				{
					//Services::bruteCheck(true);
					$this->session->set_flashdata('gagal', 'Gagal Login, silakan ulangi kembali');
					header('location: '.base_url().'index.php/login');
					
				}
				$this->load->view('view_login');				
			//}
		}
		else
		{
			$this->load->view('view_login');
		}
	}
	
	/** function logout
	------------------------------------------------------------------------ **/
	function logout()
	{
		session_destroy();
		$this->session->sess_destroy();
		header('location:'.base_url());
	}
	
}

/* End of file login.php */
/* Location: ./modules/login/controllers/login.php */