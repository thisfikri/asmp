<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ASMP - Aplikasi Sistem Menejemen Perkantoran
 *
 * @package ASMP
 * @author ThisFikri (Leader)
 * @copyright Copyright (c) 2018, Recodech <ocraineore@gmail.com>
 * @link https://github.com/codecoretech
 * @version BETA BUILD 02
 * @since Aplha 1.0.0
 * @license GNU GPL v3.0
 *
 * Aplikasi ini dibuat dan dikembangkan untuk dipergunakan dalam hal administrasi perkantoran
 */


 /**
  * ASMP_Security Class
  * 
  * Mendefinisikan fungsi - fungsi keamanan aplikasi ASMP
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
 class Themes_Handler extends CI_Model
 {
     /**
	 * [$front_page description]
	 * @var array
	 */
	private $_front_page = array(
		'pre-register' => array(
			'id-ID' => 'registrasi-awal',
			'en-US' => 'preregister'
		),
		'login' => array(
			'id-ID' => 'login',
			'en-US' => 'login'
		),
		'user-register' => array(
			'id-ID' => 'buat-akun-baru',
			'en-US' => 'user-register'
		),
		'forgotpw' => array(
			'id-ID' => 'lupa-kata-sandi',
			'en-US' => 'forgot-password'
		)
	);

	/**
	 * [$user_page description]
	 * @var array
	 */
	private $_user_page = array(
		'dashboard' => array(
			'id-ID' => 'dashboard',
			'en-US' => 'dashboard'
		),
		'profile' => array(
			'id-ID' => 'profil',
			'en-US' => 'profile'
		),
		'settings' => array(
			'id-ID' => 'pengaturan',
			'en-US' => 'settings'
		),
		'user-management' => array(
			'id-ID' => 'menejemen-pengguna',
			'en-US' => 'user-management'
		),
		'incoming-mail' => array(
			'id-ID' => 'surat-masuk',
			'en-US' => 'incoming-mail'
		),
		'pdf-layout' => array(
			'id-ID' => 'layout-pdf',
			'en-US' => 'pdf-layout'
		),
		'outgoing-mail' => array(
			'id-ID' => 'surat-keluar',
			'en-US' => 'outgoing-mail'
		),
		'create-om' => array(
			'id-ID' => 'buat-baru-surat-keluar',
			'en-US' => 'create-outgoing-mail'
		),
		'edit-om' => array(
			'id-ID' => 'edit-surat-keluar',
			'en-US' => 'edit-outgoing-mail'
		),
		'pdf-editor' => array(
			'id-ID' => 'pdf-editor',
			'en-US' => 'pdf-editor'
		),
		'field-sections' => array(
			'id-ID' => 'bidang-bagian',
			'en-US' => 'field-sections'
		),
		'about-app' => array(
			'id-ID' => 'tentang-aplikasi',
			'en-US' => 'about-application'
		)
	);

	private $_lang_id = array(
		'indonesia' => 'id-ID',
		'english' => 'en-US'
    );
    
    private $_footer_type = array(
        'front',
        'admin',
        'user'
	);
	
	public function get_page_title($page_name)
	{
		$lang = $this->_lang_id[config_item('language')];
		if (array_key_exists($page_name, $this->_front_page))
		{
			$page_title = explode('-', $this->_front_page[$page_name][$lang]);
			$page_title = ucwords(implode(' ', $page_title));
			return $page_title;
		}
		else if (array_key_exists($page_name, $this->_user_page))
		{
			$page_title = explode('-', $this->_user_page[$page_name][$lang]);
			$page_title = ucwords(implode(' ', $page_title));
			if ($page_name == 'pdf-layout')
				{
					$uri_seglen = count(explode('/', uri_string()));
					$page_title = explode('-', $this->_user_page[$page_name][$lang]);
					$page_title[1] = strtoupper($page_title[1]);
					$page_title = implode(' ', $page_title);

					if ($uri_seglen > 2)
					{
						$page_title = $page_title . ' - ' . ucwords($this->uri->segment($uri_seglen));
					}
				}
			return ucwords($page_title);
		}
	}

	public function get_header($page)
	{
		$lang = $this->_lang_id[config_item('language')];

		if (array_key_exists($page, $this->_front_page))
		{
			$page_title = explode('-', $this->_front_page[$page][$lang]);
			$page_title = ucwords(implode(' ', $page_title));

			$data = array(
				'page_title' => $page_title
			);

			$this->load->view('front-header', $data);
		}
		else if (array_key_exists($page, $this->_user_page))
		{
			$page_title = explode('-', $this->_user_page[$page][$lang]);
			$page_title = ucwords(implode(' ', $page_title));
			
			if ($this->checker->is_admin())
			{
				if ($page == 'pdf-layout')
				{
					$uri_seglen = count(explode('/', uri_string()));
					$page_title = explode('-', $this->_user_page[$page][$lang]);
					$page_title[1] = strtoupper($page_title[1]);
					$page_title = implode(' ', $page_title);

					if ($uri_seglen > 2)
					{
						$page_title = $page_title . ' - ' . ucwords($this->uri->segment($uri_seglen));
					}
				}

				$data = array(
					'page_title' => $page_title
				);

				$this->load->view('admin/admin-header', $data);
			}
			else if ($this->checker->is_user())
			{

				$data = array(
					'page_title' => $page_title
				);

				$this->load->view('user/user-header', $data);
			}
			else
			{
				$this->session->set_userdata('login_pg_msg', 'login_required');
				redirect('login','refresh');
			}
		}
    }
    
    public function get_footer($type)
    {
        if (in_array($type, $this->_footer_type))
        {
            switch ($type) {
                case 'front':
                    $this->load->view('front-footer');
                    break;
                case 'admin':
                    $this->load->view('admin-footer');
                case 'user':
                    $this->load->view('user-footer'); 
                default:
                    echo 'no footer in this theme';
                    break;
            }
        }
    }
 }