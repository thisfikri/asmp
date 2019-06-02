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
  * Activity_Log Class
  * 
  * Mengelola log aktivitas pengguna
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
class Activity_Log extends CI_Model {
	/**
	 * Digunakan untuk menyimpan tipe aksi
	 * @var string
	 */
	private $_action_type = '';

	/**
	 * Digunakan untuk menyimpan teks log
	 * @var string
	 */
	private $_log_text = '';

	/**
	 * Digunakan untuk menyimpan teks detail log
	 * @var string
	 */
	private $_detail_text = '';

	/**
	 * create_activity_log - Digunakan untuk membuat log aktivitas pengguna
	 *
	 * @since  1.0
	 * @access public
	 * @param  string $type Tipe log
	 * @param  string $msg  Pesan yang akan ditambahkan
	 * @param  array  $data Data tambahan
	 * @param  string $username Nama Pengguna
	 * @return void
	 */
	public function create_activity_log($type, $msg, $data = array(), $username)
	{
		switch ($type) {
			case 'login_activity':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-sign-in-alt fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'logout_activity':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-sign-out-alt fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'profile_update':
				// set action page
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-user-edit fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'save_settings':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-wrench fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'upload_glry_image':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-image fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'delete_glry_image':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-image fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'add_sk':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-paper-plane fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				// set detail text
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'move_to_trash':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'move_to_trash_all':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-trash fa-stack" style="margin-left: 2px;color: rgb(3, 70, 129)"><i class="fa fa-trash fa-stack-1x" style="margin-left: 5px;color: rgb(3, 70, 129)"></i></i></span>';
				break;
			case 'move_to_trash_wselected':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-trash fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (';
				if (array_key_exists('dari', $data))
				{
					for ($i = 0; $i < count($data['dari']); $i++)
					{
						$this->_detail_text .= 'Dari: '.$data['dari'][$i].' & No Surat: '.$data['no_surat'][$i].', ';
					}
				}
				else
				{
					for ($i = 0; $i < count($data['perihal_surat']); $i++)
					{
						$this->_detail_text .= 'Perihal Surat: '.$data['perihal_surat'][$i].' & No Surat: '.$data['no_surat'][$i].', ';
					}
				}
				$this->_detail_text .= ')';
				break;
			case 'view':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-eye fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'print':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-print fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'send':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-paper-plane fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'edit':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-edit fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'delete':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-times fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'deleteperm_all':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-times fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-times fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-times fa-stack-1x" style="padding: 2px 14px 0 0;color: rgb(3, 70, 129)"></i><i class="fa fa-times fa-stack-1x" style="padding: 2px 0 0 14px;color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'deleteperm_wselected':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-times fa-stack-1x" style="padding: 0 0 0 7px;color: rgb(3, 70, 129)"></i><i class="fa fa-times fa-stack-1x" style="padding: 0 7px 0 0;color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (';
				if (array_key_exists('dari', $data))
				{
					for ($i = 0; $i < count($data['dari']); $i++)
					{
						$this->_detail_text .= 'Dari: '.$data['dari'][$i].' & No Surat: '.$data['no_surat'][$i].', ';
					}
				}
				else
				{
					for ($i = 0; $i < count($data['perihal_surat']); $i++)
					{
						$this->_detail_text .= 'Perihal Surat: '.$data['perihal_surat'][$i].' & No Surat: '.$data['no_surat'][$i].', ';
					}
				}
				$this->_detail_text .= ')';
				break;
			case 'disposition':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-share-square fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'reply':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-reply fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Perihal Surat: '.$data['perihal_surat'].', No Surat: '.$data['no_surat'].')';
				break;
			case 'udelete':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-user-times fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Pengguna: '.$data['user'].')';
				break;
			case 'udelete_all':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-user fa-stack-1x" style="margin-top: 3px;left: -6px;font-size: 10px;color: rgb(3, 70, 129)"></i><i class="fa fa-user fa-stack-1x" style="font-size: 10px;color: rgb(3, 70, 129)"></i><i class="fa fa-user fa-stack-1x" style="margin: 3px 0 0 6px;font-size: 10px;color: rgb(3, 70, 129)"></i><i class="fa fa-times fa-stack-1x" style="margin: -6px 0 0 6px;font-size: 8px;color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'udelete_wselected':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-user fa-stack-1x" style="padding-right: 10px;color: rgb(3, 70, 129)"></i><i class="fa fa-user-times fa-stack-1x" style="padding-left: 5px;color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Pengguna: ';
				for ($i = 0; $i < count($data['names']); $i++)
				{
					$this->_detail_text .= $data['names'][$i].', ';
				}
				$this->_detail_text .= ')';
				break;
			case 'fsec_delete':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-users fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-times-circle fa-stack-1x" style="padding: 8px 0 0 16px;font-size: 8px;color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Nama Bidang/Bagian: '.$data['nbb'].')';
				break;
			case 'fsec_delete_all':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-users fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-times-circle fa-stack-1x" style="padding: 8px 0 0 16px;font-size: 8px;color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'fsec_delete_wselected':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-users fa-stack-1x" style="color: rgb(3, 70, 129)"></i><i class="fa fa-times-circle fa-stack-1x" style="padding: 8px 0 0 16px;font-size: 8px;color: rgb(3, 70, 129)"></i></span>';
				$this->_detail_text = ' (Bidang/Bagian: ';
				for ($i = 0; $i < count($data['bidbag']); $i++)
				{
					$this->_detail_text .= $data['bidbag'][$i].', ';
				}
				$this->_detail_text .= ')';
				break;
			case 'check_version':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-check-circle fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'download_update':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-download fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			case 'extract_file':
				$this->_action_type = '<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-archive fa-stack-1x" style="color: rgb(3, 70, 129)"></i></span>';
				break;
			default:
				$this->_action_type = 'Jenis Log Tidak Ada';
				break;
		}

		/**
		 * Query untuk mengambil data dari table users
		 * @var object
		 */
		$query = $this->db->where('username', $username)->get('users');

		/**
		 * Hasil dari query sebelumnya
		 * @var object
		 */
		$result = $query->result();

		/**
		 * Nama dari pengguna yang sedang log-in
		 * @var string
		 */
		$name = $result[0]->true_name;

		// set default time zone
		date_default_timezone_set('Asia/Jakarta');

		// mengeset teks log
		$this->_log_text = $this->_action_type . ' ' . $name . ': ' . $msg . $this->_detail_text . ' ~ ' . date('Y-m-d h:i:s A') . ' ~';

		/**
		 * Query untuk mengambil data dari table user_activity_logs
		 * @var [type]
		 */
		$query = $this->db->get('user_activity_logs');

		/**
		 * Jumlah keseluruhan log yang ada di database
		 * @var [type]
		 */
		$logs_count = $query->num_rows();

		/**
		 * Data untuk dimasukkan ke dalam table user_activity_logs
		 * @var array
		 */
		$data_to_insert = array(
			'id' => $logs_count + 1,
			'username' => $username,
			'log' => $this->_log_text
		);

		// memasukkan data log kedalam table user_activity_logs
		$this->db->insert('user_activity_logs', $data_to_insert);

		if (!$this->db->affected_rows())
		{
			log_message('error','Create User Activity Log Failed');
		}
	}

	public function get_activity_log()
	{
		if ($this->checker->is_admin()) {
			/**
        	 * [$query description]
        	 * @var [type]
        	 */
        	$query = $this->db->where('username', $this->session->userdata('admin_login'))->order_by('id', 'desc')->get('user_activity_logs');

        	/**
        	 * [$user_logs description]
        	 * @var [type]
        	 */
        	
        	return $query->result();
		}
		else if ($this->checker->is_user())
		{
			/**
        	 * [$query description]
        	 * @var [type]
        	 */
        	$query = $this->db->where('username', $this->session->userdata('user_login'))->order_by('id', 'desc')->get('user_activity_logs');

        	/**
        	 * [$user_logs description]
        	 * @var [type]
        	 */
        	
        	return $query->result();
		}
		else
		{
			return 'Harus Login Terlebih Dahulu.';
		}
	}
}