<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SRT_ASMP - (SRoot) Aplikasi Sistem Menejemen Perkantoran
 * 
 * @package SRT_ASMP
 * @author SRoot
 * @copyright Copyright (c) 2018, CodeInAlfa <codeismywork01@gmail.com>
 * @link https://github.com/CodeInAlfa
 * @version BETA BUILD 02
 * @since Aplha 1.0.0
 * @license GPL Closed Source
 * 
 * Aplikasi ini dibuat dan dikembangkan untuk dipergunakan dalam hal administrasi perkantoran
 */

 /**
  * Auth Class
  * 
  * Mengontrol autentikasi.
  *
  * @package SRT_ASMP
  * @category Controller
  * @author SRoot
  */
 class Auth extends CI_Controller
 {
    /**
     * Mendefinisikan bahasa yang ada
     */
    private $_available_lang = array('bahasa', 'english');

    /**
     * Bahasa yang di set oleh aplikasi
     */
    private $_app_lang;

    /**
     * constructor ini digunakan untuk membuat table awal untuk registrasi awal
     * DOC[01] - SRT_ASMP Program Documentation
     */
    public function __construct()
    {
        parent::__construct();

        //$this->output->enable_profiler(TRUE);
        
        if (get_cookie('language') === NULL)
        {
            // set cookie dan session untuk bahasa
            setcookie('language', config_item('language'), 86000);  /* expire in 1 hour */
            $this->session->set_userdata('language', config_item('language'));
            $this->_app_lang = $this->session->userdata('language');
        }
        else
        {
            // men-set bahasa untuk aplikasi
            $this->_app_lang = $this->input->cookie('language', TRUE);
        }

        // Memuat bahasa untuk front page
        $this->lang->load('frontp_lang', $this->_app_lang);

        // Memuat dbforge
        $this->load->dbforge();
        
        date_default_timezone_set('Asia/Jakarta');

        /**
         * Field untuk dimasukan kedalam table preregister_status
         *
         * @var array
         */
        $fields = array(
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'default' => 'not_registered',
                'unique' => TRUE,
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        /**
         * Field untuk dimasukan kedalam table register_limit
         *
         * @var array
         */
        $fields2 = array(
            'limit' => array(
                'type' => 'BIGINT',
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('preregister_status', TRUE);

        // Menambahkan field dan membuat table jika table tidak ada
        $this->dbforge->add_field($fields2);
        $this->dbforge->create_table('register_limit', TRUE);

        $preregister_status = $this->db->get('preregister_status');

        // Mengecek jika tidak ada result maka field 'status ' akan di isi dengan not_registered
        if (!$preregister_status->num_rows())
        {
            $this->db->insert('preregister_status', array('status' => 'not_registered'));
        }

        $register_limit = $this->db->get('register_limit');
        
        // Mengecek jika tidak ada result maka field 'limit' akan di isi dengan 1 (untuk digunakan di preregister)
        if (!$register_limit->num_rows())
        {
            $this->db->insert('register_limit', array('limit' => 1));
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * index - Untuk menampilkan halaman login dan memproses form login
     * DOC[02] - SRT_ASMP Program Documentation
     * 
     * @since 1.0
     * @access public
     * @return void
     */
    public function index()
    {
        $this->checker->preregister_status();
        $this->checker->login_status();
        $msg = $this->session->userdata('login_pg_msg');
    	switch ($msg)
        {
            case 'login_required':
                $msg = '<i class="fa fa-exclamation-triangle"></i> ' . $this->lang->line('login_required');
                $msg_type = 'warning';
                break;
            case 'logout_true':
                $msg = '<i class="fa fa-check-circle"></i> ' . $this->lang->line('logout_s');
                $msg_type = 'success';
                break;
            default:
                $msg = '';
                $msg_type = '';
                break;
        }

        $this->session->unset_userdata('login_pg_msg');

        $query_l = $this->db->get('register_limit');
        $result_l = $query_l->result();
        
        $data = array(
        	'msg' => $msg,
        	'msg_type' => $msg_type,
            'register_limit' => $result_l[0]->limit,
        );

        $this->load->view('login', $data);
    }
    
    // ---------------------------------------------------------------------------------------------------------------

    /**
     * login_auth - Untuk memproses login authentication
     * DOC[03] - SRT_ASMP Program Documentation
     *
     * @since 1.0
     * @access public
     * @return string tipe pesan dan pesan
     */
    public function login_auth()
    {
        if ($this->input->post())
        {
            /**
             * [$form_rules description]
             * @var array
             */
        	$form_rules = array(
                array(
                    'field' => 'username',
                    'label' => 'Nama Pengguna',
                    'rules' => 'trim|xss_clean|required'
                ),
                array(
                    'field' => 'password',
                    'label' => 'Kata Sandi',
                    'rules' => 'trim|xss_clean|required'
                )
            );

            $this->form_validation->set_rules($form_rules);
            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                    'username' => $this->input->post('username', TRUE),
                    'password' => $this->input->post('password', TRUE)
                );

                // Mengecek jika pengguna terdaftar
                $query = $this->db->where('username', $data['username'])->get('users');
                $result = $query->num_rows();

                if ($result == 1)
                {
                    $result = $query->result();
                    // Mengecek jika password valid atau tidak
                    if ($this->asmp_security->verify_hashed_password($data['password'], $result[0]->password))
                    {
                        $this->db->where('username', $data['username'])->update('users', array('welcome_status' => 'TRUE'));

                        if (password_needs_rehash($result[0]->password, PASSWORD_BCRYPT))
                        {
                            $this->db->where('username', $data['username'])->update('users', array('password' => $this->asmp_security->get_hashed_password($data['password'])));

                            if ($this->db->affected_rows())
                            {
                                log_message('info','Password is ReHashed!');
                            }
                        }

                        // Mengecek hak aksess
                        if ($result[0]->role == 'admin')
                        {
                            $new_sess = array(
                                'admin_login' => $data['username']

                            );
                            // Membuat sesi login untuk pengguna
                            $this->asmp_security->csrf_token('generate');
                            $this->session->set_userdata($new_sess);

                            // Tulis log
                            $this->activity_log->create_activity_log('login_activity', ' Telah Log In', null, $data['username']);

                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'success', 'message' => base_url('admin/dashboard')));
                        }
                        else if ($result[0]->role == 'user')
                        {
                            $new_sess = array(
                                'user_login' => $data['username'],
                            );
                            // Membuat sesi login untuk pengguna
                            $this->asmp_security->csrf_token('generate');
                            $this->session->set_userdata($new_sess);

                            // Tulis log
                            $this->activity_log->create_activity_log('login_activity', ' Telah Log In', null, $data['username']); 

                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'success', 'message' => base_url('user/dashboard')));
                        }
                    }
                    else
                    {
                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('password_not_valid') . '~'));
                    }
                }
                else
                {
                    // Ubah tipe content ke JSON
                    header('Content-Type: application/json');
                    // Mengirim output data ke client
                    echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $data['username'] . $this->lang->line('not_registered') . '~'));
                }
            }
            else
            {
                // Ubah tipe content ke JSON
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'error', 'message' => validation_errors('<i class="fa fa-exclamation-circle"></i> ', '~')));
            }
        }
        else
        {
            redirect('login','refresh');
        }
    }
 }
 