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
 * User Class
 *
 * Mengontrol halaman user.
 *
 * @package ASMP
 * @category Controller
 * @author ThisFikri
 */
class User extends CI_Controller
{
    /**
     * Mendefinisikan bahasa yang ada
     */
    private $_available_lang = array('bahasa', 'english');

    /**
     * Mendefinisikan Nama Pengguna yang login
     */
    private $_username = '';

    /**
     * Bahasa yang di set oleh aplikasi
     */
    private $_app_lang;

    /**
     * constructor ini digunakan untuk membuat table awal untuk registrasi awal
     * DOC[01] - ASMP Program Documentation
     */
    public function __construct()
    {
        parent::__construct();

        //$this->output->enable_profiler(TRUE);

        if (get_cookie('language') === NULL)
        {
            // set cookie dan session untuk bahasa
            setcookie('language', config_item('language'), 86000); /* expire in 1 hour */
            $this->session->set_userdata('language', config_item('language'));
            $this->_app_lang = $this->session->userdata('language');
        }
        else
        {
            // men-set bahasa untuk aplikasi
            $this->_app_lang = $this->input->cookie('language', TRUE);
        }

        // Memuat bahasa untuk front page
        $this->lang->load('adminp_lang', $this->_app_lang);

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

        $this->checker->preregister_status();

        if (!$this->checker->is_user())
        {
            $this->session->set_userdata('login_pg_msg', 'login_required');
            redirect('login', 'refresh');
        }

        $this->_username = $this->session->userdata('user_login');
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * index - Digunakan untuk menampilkan halaman Dashboard
     * DOC[02] - SIMAKWApp Program Documentation
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function index()
    {
        // jika url berakhiran user
        if (uri_string() == 'user')
        {
            redirect('user/dashboard', 'refresh');
        }

        /**
         * $username - Nama Pengguna yang diambil dari session saat ini
         *
         * @var string
         */
        $username = $this->_username;

        $query = $this->db->where('username', $username)->get('users');

        /**
         * $user_profile_data - Data profil pengguna yang diambil dari database
         *
         * @var object
         */
        $user_profile_data = $query->result();

        /**
         * $wdata - Where Data
         *
         * @var array
         */
        $wdata = array(
            'username' => $username,
            'status' => 'baru',
        );

        $query = $this->db->where($wdata)->get('incoming_mail');

        /**
         * $new_im_count - Varible yang menampung jumlah bari dari incoming_mail
         *
         * @var int
         */
        $new_im_count = $query->num_rows();

        /**
         * $new_im - Variable yang digunakan untuk menyetel notifikasi untuk surat masuk baru
         *
         * @var array
         */
        $new_im = array(
            'display' => false,
            'count' => 0,
        );

        // Cek jumlah surat masuk baru
        if ($new_im_count > 0)
        {
            $new_im = array(
                'display' => TRUE,
                'count' => $new_im_count,
            );
        }
        else
        {
            $new_im = array(
                'display' => FALSE,
                'count' => $new_im_count,
            );
        }

        $query = $this->db->where('username', $username)->get('incoming_mail');

        /**
         * $im_count - Menampung jumlah surat masuk yang ada pada incoming_mail
         *
         * @var int
         */
        $im_count = $query->num_rows();

        $query = $this->db->where('username', $username)->get('outgoing_mail');

        /**
         * $om_count - Digunakan untuk menampilkan jumlah surat keluar yang ada pada table
         *
         * @var int
         */
        $om_count = $query->num_rows();

        $query = $this->db->where(array('username' => $username, 'mail_type' => 'incoming_mail'))->get('trash_can');

        /**
         * $imtr_count - Menampung jumlah surat masuk yang ada pada tempat sampah
         *
         * @var int
         */
        $imtr_count = $query->num_rows();

        $query = $this->db->where(array('username' => $username, 'mail_type' => 'outgoing_mail'))->get('trash_can');

        /**
         * $omtr_count - Menampung jumlah surat keluar yang ada pada tempat sampah
         *
         * @var int
         */
        $omtr_count = $query->num_rows();

        $query = $this->db->where('username', $username)->get('users');

        /**
         * $row - Menapung data dari table user $username dan mengubah status welcome
         *
         * @var object
         */
        $row = $query->result();

        //Cek status notifikasi welcome
        if ($row[0]->welcome_status == 'TRUE')
        {
            $welcome_status = TRUE;
        }
        else
        {
            $welcome_status = false;
        }

        /**
         * $vdata - View Data
         * @var array
         */
        $vdata = array(
            'uprof_data' => $user_profile_data[0],
            'im_count' => $im_count,
            'om_count' => $om_count,
            'imtr_count' => $imtr_count,
            'omtr_count' => $omtr_count,
            'user_logs' => $this->activity_log->get_activity_log(),
            'welcome_status' => $welcome_status,
            'name' => $row[0]->true_name,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'new_im' => $new_im,
        );

        $this->load->view('user/dashboard', $vdata);
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function logout()
    {
        if ($this->input->post() && $this->input->is_ajax_request())
        {
            $data = array(
                'CSRF_client' => $this->input->post('token', TRUE),
                'CSRF_server' => $this->session->userdata('CSRF'),
            );

            if ($data['CSRF_client'] === $data['CSRF_server'])
            {
                $this->session->unset_userdata(array('user_login'));
                //$this->session->sess_destroy();
                $this->session->set_userdata('login_pg_msg', 'logout_true');
                // Tulis log
                $this->activity_log->create_activity_log('logout_activity', ' Telah Log Out', null, $this->_username);
                // Ubah tipe content ke JSON
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'success', 'message' => site_url('login')));
            }
            else
            {
                // Ubah tipe content ke JSON
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'failed', 'message' => $this->lang->line('logout_f')));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    public function image_upload()
    {
        if ($this->input->is_ajax_request())
        {
            error_reporting(E_ALL | E_STRICT);
            $query = $this->db->where('username', $this->_username)->get('users');
            $result = $query->result();
            if (!$result[0]->gallery_dir)
            {
                $this->db->where('username', $this->_username)->update('users', array('gallery_dir' => random_string('alnum', 28)));
                $query = $this->db->where('username', $this->_username)->get('users');
                $result = $query->result();
            }

            $imagedir_name = $result[0]->gallery_dir;
            $gallery_dir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery';

            if (!is_dir($gallery_dir)) {
                mkdir($gallery_dir, 0755);
            }

            $imagedir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery/' . $imagedir_name;
            
            if (is_dir($imagedir) === FALSE)
            {
                mkdir($imagedir, 0755);
            }

            if (is_dir($imagedir) === TRUE)
            {
                $options = array(
                    'accept_file_types' => '/\.(gif|jpe?g|png)$/i',
                    'max_file_size' => 4670016,
                    'min_file_size' => 1,
                    'correct_image_extensions' => TRUE,
                    'upload_dir' => $imagedir . '/',
                    'upload_url' => site_url('/gallery/' . $imagedir_name . '/'),
                );

                if ($this->input->server('REQUEST_METHOD') == 'DELETE')
                {
                    if ($this->input->get('t', TRUE) != $this->session->userdata('CSRF'))
                    {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'warning', 'message' => 'Token Tidak Sama!'));
                    }
                    else
                    {
                        $this->activity_log->create_activity_log('delete_glry_image', 'Telah menghapus foto dari gallery', null, $this->_username);
                        $this->load->library('UploadHandler', $options);
                    }
                }
                else
                {
                    $this->activity_log->create_activity_log('upload_glry_image', 'Telah mengupload foto ke gallery', null, $this->_username);
                    $this->load->library('UploadHandler', $options);
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Direktori Tidak Ditemukan!'));
            }
        }
        else
        {
            redirect(site_url('user'), 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    /**
     *
     */
    public function update_profile_picture()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = $this->input->post('imageData', TRUE);
            $query = $this->db->where('username', $this->_username)->get('users');
            $result = $query->result();
            $prev_pic = $result[0]->profile_picture;
            $data['url'] = explode('/', $data['url']);
            $imageName = $data['url'][count($data['url']) - 1];
            $this->db->where('username', $this->_username)->update('users', array('profile_picture' => xss_clean($imageName)));
            if ($this->db->affected_rows())
            {
                $gallery_dir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery/' . $result[0]->gallery_dir . '/';
                $filename = $gallery_dir . $imageName;
                $handle = fopen($filename, 'r');
                if (is_readable($filename))
                {
                    $filewr = fread($handle, filesize($filename));
                    fclose($handle);

                    $filename = dirname($this->input->server('SCRIPT_FILENAME')) . '/assets/images/profile-photo/' . $prev_pic;
                    if (unlink($filename))
                    {
                        $filename = dirname($this->input->server('SCRIPT_FILENAME')) . '/assets/images/profile-photo/' . $imageName;
                        $handle = fopen($filename, 'w');
                        if (is_writable($filename))
                        {
                            if (fwrite($handle, $filewr))
                            {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => 'Berhasil mengubah foto profil!'));
                            }
                        }
                    }
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => 'Gagal mengubah foto profil!'));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function settings()
    {
        if ($this->input->is_ajax_request())
        {
            $token = $this->input->post('t', TRUE);
            if ($token === $this->session->userdata('CSRF'))
            {
                $settings_data = array(
                    'multiple_remove_action' => $this->input->post('mulRemAct', TRUE),
                    'multiple_recovery_action' => $this->input->post('mulRecAct', TRUE),
                    'paging_status' => intval($this->input->post('pagingItem', TRUE)),
                    'row_limit' => $this->input->post('pagingLimit', TRUE),
                );

                if ($this->input->post('cmd', TRUE) == 'save_settings')
                {
                    header('Content-Type: application/json');
                    echo json_encode($this->app_settings->save_settings($this->_username, $settings_data));
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-tria"></i> Token is not match!'));
            }
        }
        else
        {
            $query = $this->db->where('username', $this->_username)->get('users');

            /**
             * $user_profile_data - Data profil pengguna yang diambil dari database
             *
             * @var object
             */
            $user_profile_data = $query->result();

            $row = $query->result();

            /**
             * $wdata - Where Data
             *
             * @var array
             */
            $wdata = array(
                'username' => $this->_username,
                'status' => 'baru',
            );

            $query = $this->db->where($wdata)->get('incoming_mail');

            /**
             * $new_im_count - Varible yang menampung jumlah bari dari incoming_mail
             *
             * @var int
             */
            $new_im_count = $query->num_rows();

            /**
             * $new_im - Variable yang digunakan untuk menyetel notifikasi untuk surat masuk baru
             *
             * @var array
             */
            $new_im = array(
                'display' => false,
                'count' => 0,
            );

            // Cek jumlah surat masuk baru
            if ($new_im_count > 0)
            {
                $new_im = array(
                    'display' => TRUE,
                    'count' => $new_im_count,
                );
            }
            else
            {
                $new_im = array(
                    'display' => FALSE,
                    'count' => $new_im_count,
                );
            }

            $data = array(
                'uprof_data' => $user_profile_data[0],
                'new_im' => $new_im,
                'settings' => $this->app_settings->get_settings($this->_username),
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            );
            $this->load->view('user/user-settings', $data, FALSE);
        }
    }
    
    /**
     *
    */
    public function incoming_mail()
    {
        $query = $this->db->where('username', $this->_username)->get('users');

        /**
         * $user_profile_data - Data profil pengguna yang diambil dari database
         *
         * @var object
         */
        $user_profile_data = $query->result();

        $row = $query->result();

        /**
         * $wdata - Where Data
         *
         * @var array
         */
        $wdata = array(
            'username' => $this->_username,
            'status' => 'baru',
        );

        $query = $this->db->where($wdata)->get('incoming_mail');

        /**
         * $new_im_count - Varible yang menampung jumlah bari dari incoming_mail
         *
         * @var int
         */
        $new_im_count = $query->num_rows();

        /**
         * $new_im - Variable yang digunakan untuk menyetel notifikasi untuk surat masuk baru
         *
         * @var array
         */
        $new_im = array(
            'display' => false,
            'count' => 0,
        );

        // Cek jumlah surat masuk baru
        if ($new_im_count > 0)
        {
            $new_im = array(
                'display' => TRUE,
                'count' => $new_im_count,
            );
        }
        else
        {
            $new_im = array(
                'display' => FALSE,
                'count' => $new_im_count,
            );
        }

        $data = array(
            'uprof_data' => $user_profile_data[0],
            'new_im' => $new_im,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture)
        );

        $this->load->view('user/incoming-mail', $data, FALSE);
    }

    // ------------------------------------------------------------------------

    public function outgoing_mail()
    {
        $query = $this->db->where('username', $this->_username)->get('users');

        /**
         * $user_profile_data - Data profil pengguna yang diambil dari database
         *
         * @var object
         */
        $user_profile_data = $query->result();

        $row = $query->result();

        /**
         * $wdata - Where Data
         *
         * @var array
         */
        $wdata = array(
            'username' => $this->_username,
            'status' => 'baru',
        );

        $query = $this->db->where($wdata)->get('incoming_mail');

        /**
         * $new_im_count - Varible yang menampung jumlah bari dari incoming_mail
         *
         * @var int
         */
        $new_im_count = $query->num_rows();

        /**
         * $new_im - Variable yang digunakan untuk menyetel notifikasi untuk surat masuk baru
         *
         * @var array
         */
        $new_im = array(
            'display' => false,
            'count' => 0,
        );

        // Cek jumlah surat masuk baru
        if ($new_im_count > 0)
        {
            $new_im = array(
                'display' => TRUE,
                'count' => $new_im_count,
            );
        }
        else
        {
            $new_im = array(
                'display' => FALSE,
                'count' => $new_im_count,
            );
        }

        $query = $this->db->where('username', $this->_username)->get('outgoing_mail');
        $om_result = $query->result();

        $data = array(
            'uprof_data' => $user_profile_data[0],
            'new_im' => $new_im,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'incoming_mail' => $om_result,
        );

        $this->load->view('user/outgoing-mail', $data, FALSE);
    }

    // ------------------------------------------------------------------------
    
    /**
     * Execute outgoing mail action
     *
     * @return void
    */
    public function om_action_exec()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $request_data = $this->input->post('request_data', TRUE);
            $request_data = json_decode($request_data, TRUE);
            switch ($request_data['action']) {
            	case 'load':
		            $this->om_handler->load_om($requested_data['om_data'], 'json');
					break;
                case 'send':
                    $this->om_handler->send_om($requested_data['om_data'], 'json');
                    break;
                case 'save':
                    $this->om_handler->save_om($requested_data['om_data'], 'json');
                    break;
                case 'throw':
                    $this->om_handler->throw_om($requested_data['om_data'], 'json');
                    break;
                default:
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'action not found!'
                        )
                    ));
                    break;
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }
}