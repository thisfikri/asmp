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
 * Admin Class
 *
 * Mengontrol halaman admin.
 *
 * @package ASMP
 * @category Controller
 * @author ThisFikri
 */
class Admin extends CI_Controller
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
            set_cookie('language', config_item('language'), 860000); /* expire in 1 hour */
            $this->session->set_userdata('language', config_item('language'));
            $this->_app_lang = $this->session->userdata('language');
        }
        else
        {
            // men-set bahasa untuk aplikasi
            $this->_app_lang = get_cookie('language');
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

        if (!$this->checker->is_admin())
        {
            $this->session->set_userdata('login_pg_msg', 'login_required');
            redirect('login', 'refresh');
        }

        $this->_username = $this->session->userdata('admin_login');
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
        // jika url berakhiran admin
        if (uri_string() == 'admin')
        {
            redirect('admin/dashboard', 'refresh');
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

        $query = $this->db->where('role', 'user')->get('users');

        /**
         * $user_count - Menampung jumlah pengguna dengan role 'user' pada table users
         *
         * @var int
         */
        $user_count = $query->num_rows();

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
            //'omtr_count' => $omtr_count,
            'user_count' => $user_count,
            'user_logs' => $this->activity_log->get_activity_log(),
            'welcome_status' => $welcome_status,
            'name' => $row[0]->true_name,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'new_im' => $new_im,
        );

        $this->load->view('admin/dashboard', $vdata);
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
                $this->session->unset_userdata(array('admin_login'));
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
            redirect(site_url('admin'), 'refresh');
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

    public function settings()
    {
        if ($this->input->is_ajax_request())
        {
            $data = $this->input->post('data', TRUE);
            $data = json_decode($data, TRUE);
            if ($data['t'] === $this->session->userdata('CSRF'))
            {
                if ($data['cmd'] == 'save_user_settings')
                {
                    $settings_data = array(
                        'paging_status' => $data['settings_data']['pagingItem'],
                        'row_limit' => $data['settings_data']['pagingLimit'],
                        'multiple_remove_action' => $data['settings_data']['mulRemAct'],
                        'multiple_recovery_action' => $data['settings_data']['mulRecAct']
                    );

                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        $this->app_settings->save_user_settings($this->_username, $settings_data)
                    ));
                }
                else if ($data['cmd'] == 'save_app_settings')
                {
                    $settings_data = array(
                        'mail_document_heading' => $data['settings_data']['companyName'],
                        'mail_document_address' => $data['settings_data']['companyAddress'],
                        'mail_document_contact' => $data['settings_data']['companyContact'],
                    );

                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        $this->app_settings->save_app_settings($settings_data)
                    ));
                }
                else if ($data['cmd'] == 'save_all_setting')
                {
                    $settings_data = array(
                        'app_settings' => array(
                            'mail_document_heading' => $data['settings_data']['companyName'],
                            'mail_document_address' => $data['settings_data']['companyAddress'],
                            'mail_document_contact' => $data['settings_data']['companyContact']
                        ),
                        'user_settings' => array(
                            'paging_status' => $data['settings_data']['pagingItem'],
                            'row_limit' => $data['settings_data']['pagingLimit'],
                            'multiple_remove_action' => $data['settings_data']['mulRemAct'],
                            'multiple_recovery_action' => $data['settings_data']['mulRecAct']
                        )
                    );

                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        $this->app_settings->save_all_setting($this->_username, $settings_data)
                    ));
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
                'settings' => $this->app_settings->get_user_settings($this->_username),
                'app_settings' => $this->app_settings->get_app_settings(),
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            );
            $this->load->view('admin/admin-settings', $data, FALSE);
        }
    }

    // ------------------------------------------------------------------------

    public function user_management($load_user = '')
    {
        if ($this->input->is_ajax_request() && $load_user === 'load')
        {
            $token = $this->input->post('t', TRUE);
            if ($token === $this->session->userdata('CSRF'))
            {
                $query = $this->db->where('role', 'user')->get('users');
                $result = $query->result_array();
                $user_count = $query->num_rows();
                $user_arr = array();
                $i = 0;
                $settings = $this->app_settings->get_user_settings($this->_username);
                $paging = array();
                $paging['paging_status'] = $settings[0]->paging_status;
                $paging['paging_limit'] = $settings[0]->row_limit;
                if ($user_count > 1)
                {
                    for (; $i < $user_count; $i++)
                    {
                        $user_arr[$i]['true_name'] = $result[$i]['true_name'];
                        $user_arr[$i]['position'] = $result[$i]['position'];
                    }
                }
                else if ($user_count === 0)
                {
                    $user_arr = '<i class="fa fa-exclamation-circle"></i> User Not Found.';
                }
                else
                {
                    $user_arr[0]['true_name'] = $result[0]['true_name'];
                    $user_arr[0]['position'] = $result[0]['position'];
                }

                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'data' => $user_arr, 'paging' => $paging));
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

            $query = $this->db->where('role', 'user')->get('users');
            $user_count = $query->num_rows();

            $data = array(
                'uprof_data' => $user_profile_data[0],
                'new_im' => $new_im,
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                'user_count' => $user_count,
            );

            $this->load->view('admin/user-management', $data, FALSE);
        }
    }

    // ------------------------------------------------------------------------

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

        $query = $this->db->where('username', $this->_username)->get('incoming_mail');
        $im_result = $query->result();

        $data = array(
            'uprof_data' => $user_profile_data[0],
            'new_im' => $new_im,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'incoming_mail' => $im_result,
        );

        $this->load->view('admin/incoming-mail', $data, FALSE);
    }

    public function field_sections($load_fs = '')
    {
        if ($this->input->is_ajax_request() && $load_fs === 'load')
        {
            $token = $this->input->post('t', TRUE);
            if ($token === $this->session->userdata('CSRF'))
            {
                $query = $this->db->get('field_sections');
                $result = $query->result_array();
                $user_count = $query->num_rows();
                $user_arr = array();
                $settings = $this->app_settings->get_user_settings($this->_username);
                $paging = array();
                $paging['status'] = $settings[0]->paging_status;
                $paging['limit'] = $settings[0]->row_limit;
                $query = $this->db->where('role', 'admin')->get('users');
                $admin_data = $query->result();
                $fs_task_hr = array(
                    'leader_accept_lvl3_reply' => 'Pimpinan, Menerima Surat Masuk Disposisi, Membalas Surat Masuk',
                    'normal_accept_sending' => 'Menerima Surat Masuk Balasan, Mengirim Surat Keluar',
                    'accept_lvl1_dpss' => 'Menerima Surat Masuk lvl1, Mengirim Surat Disposisi',
                    'accept_lvl2_dpss' => 'Menerima Surat Masuk lvl2, Mengirim Surat Disposisi',
                );
                $i = 0;
                if ($user_count > 1)
                {
                    for (; $i < $user_count; $i++)
                    {
                        $user_arr[$i]['field_section_name'] = $result[$i]['field_section_name'];
                        $user_arr[$i]['task'] = $fs_task_hr[$result[$i]['task']];
                        if ($admin_data[0]->position == $result[$i]['field_section_name'])
                        {
                            $user_arr[$i]['disable_action'] = TRUE;
                        }
                    }
                }
                else if ($user_count === 0)
                {
                    $user_arr = '<i class="fa fa-exclamation-circle"></i> Field/Section Not Found.';
                }
                else
                {
                    $user_arr[0]['field_section_name'] = $result[0]['field_section_name'];
                    $user_arr[0]['task'] = $fs_task_hr[$result[0]['task']];
                    if ($admin_data[0]->position == $result[0]['field_section_name'])
                    {
                        $user_arr[0]['disable_action'] = TRUE;
                    }
                }

                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'data' => $user_arr, 'paging' => $paging));
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Token is not match!'));
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

            $query = $this->db->get('field_sections');
            $field_sections = $query->result();

            $data = array(
                'uprof_data' => $user_profile_data[0],
                'new_im' => $new_im,
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                'field_sections' => $field_sections,
                'fs_task_hr' => array(
                    'leader_accept_lvl3_reply' => 'Pimpinan, Menerima Surat Masuk Disposisi, Membalas Surat Masuk',
                    'normal_accept_sending' => 'Menerima Surat Masuk Balasan, Mengirim Surat Keluar',
                    'accept_lvl1_dpss' => 'Menerima Surat Masuk lvl1, Mengirim Surat Disposisi',
                    'accept_lvl2_dpss' => 'Menerima Surat Masuk lvl2, Mengirim Surat Disposisi',
                ),
            );

            $this->load->view('admin/field-sections', $data, FALSE);
        }
    }

    // ------------------------------------------------------------------------

    public function add_field_section()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = array(
                'token' => $this->input->post('t', TRUE),
                'fs_name' => $this->input->post('fs_name', TRUE),
                'fs_task' => $this->input->post('fs_task', TRUE),
            );

            if ($data['fs_name'] == '')
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Nama Bidang/Bagian Tidak Boleh Kosong.'));
            }
            else if ($data['fs_task'] == '')
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Tugas Bidang/Bagian Tidak Boleh Kosong.'));
            }
            else
            {
                if ($data['token'] === $this->session->userdata('CSRF'))
                {
                    $query = $this->db->where('field_section_name', $data['fs_name'])->get('field_sections');
                    $result = $query->num_rows();

                    if ($result !== 0)
                    {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Nama Bidang/Bagian Sudah Ada!'));
                    }
                    else
                    {
                        $single_task_list = array(
                            'accept_lvl1_dpss',
                            'accept_lvl2_dpss',
                            'leader_accept_lvl3_reply',
                        );

                        if (in_array($data['fs_task'], $single_task_list))
                        {
                            $query = $this->db->where('task', $data['fs_task'])->get('field_sections');
                            $single_task = $query->num_rows();
                        }
                        else
                        {
                            $single_task = 0;
                        }

                        if ($single_task !== 0)
                        {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Tugas lvl1, lvl2, dan lv3 hanya diperbolehkan untuk satu bidang/bagian saja'));
                        }
                        else
                        {
                            $query = $this->db->get('field_sections');
                            $row_count = $query->num_rows();

                            $this->db->insert('field_sections', array('id' => $row_count + 1,
                                'field_section_name' => $data['fs_name'],
                                'task' => $data['fs_task'],
                            ));

                            if ($this->db->affected_rows())
                            {
                                $query = $this->db->get('register_limit');
                                $register_limit = $query->result();
                                $register_limit = $register_limit[0]->limit + 1;
                                $this->db->update('register_limit', array('limit' => $register_limit));
                                if (!$this->db->affected_rows())
                                {
                                    log_message('error', 'limit not increment');
                                }
                                $task_list = array(
                                    'normal_accept_sending' => 'Menerima Surat Masuk Balasan, Mengirim Surat Keluar',
                                    'accept_lvl1_dpss' => 'Menerima Surat Masuk lvl1, Mengirim Surat Disposisi',
                                    'accept_lvl2_dpss' => 'Menerima Surat Masuk lvl2, Mengirim Surat Disposisi',
                                );

                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Berhasil menambahkan bidang/bagian'));
                            }
                            else
                            {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menambahkan bidang/bagian'));
                            }
                        }
                    }
                }
                else
                {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Token tidak sama!'));
                }
            }
        }
        else
        {
            redirect('admin', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * show list, create, and delete pdf layout
     *
     * @return void
     */
    public function PDF_layout_list()
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

        $query = $this->db->where('username', $this->_username)->get('incoming_mail');
        $im_result = $query->result();

        $query = $this->db->get('pdf_layouts');
        $pdf_layouts = $query->result();

        $data = array(
            'uprof_data' => $user_profile_data[0],
            'new_im' => $new_im,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'pdf_layouts' => $pdf_layouts,
        );
        $this->load->view('admin/pdf-layout-list', $data);
    }

    /**
     * add pdf layout
     *
     * @return void
     */
    public function add_pdf_layout()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            // json string data
            $data = $this->input->post('data', TRUE);
            $data = json_decode($data, TRUE);
            if ($data['t'] == $this->session->userdata('CSRF'))
            {
                $layout_data = $this->pdfcdmanp->create_default_data($data['choiced_cd']);
                $pdf_page_setup = $this->pdfcdmanp->create_page_setup($data['page_setup']);
                $query = $this->db->get('pdf_layouts');
                $layouts_count = $query->num_rows();
                $this->db->insert('pdf_editor', array(
                    'id' => $layouts_count + 1,
                    'layout_name' => $data['layout_name'],
                    'layout_data' => $layout_data,
                    'layout_items' => implode(',', $data['choiced_cd']),
                    'pdf_page_setup' => $pdf_page_setup,
                ));
                if ($this->db->affected_rows())
                {
                    $this->db->insert('pdf_layouts', array(
                        'id' => $layouts_count + 1,
                        'pdf_layout_name' => $data['layout_name'],
                        'pdf_layout_data' => $layout_data,
                        'pdf_layout_status' => 'nonactive',
                        'pdf_page_setup' => $pdf_page_setup,
                    ));
                    if ($this->db->affected_rows())
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'success',
                            'message' => 'Pembuatan Layout Berhasil',
                        )));
                    }
                    else
                    {
                        log_message('error', 'failed to insert pdf_layouts');
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'failed',
                            'message' => 'Pembuatan Layout Gagal',
                        )));
                    }
                }
                else
                {
                    log_message('error', 'failed to insert pdf_editor');
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'failed',
                        'message' => 'Pembuatan Layout Gagal',
                    )));
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function pdf_layout_stat_changer()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = $this->input->post('data', TRUE);
            $data = json_decode($data, TRUE);
            if ($data['t'] == $this->session->userdata('CSRF'))
            {
                $this->db->where('pdf_layout_name', $data['layout_name'])->update('pdf_layouts', array('pdf_layout_status' => $data['pdf_status']));
                if ($this->db->affected_rows())
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'success',
                        'message' => 'Status PDF Layout Berhasil Diubah',
                    )));
                }
                else
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'failed',
                        'message' => 'Status PDF Layout Gagal Diubah',
                        'data' => $data,
                    )));
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function remove_pdf_layout()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = $this->input->post('data', TRUE);
            $data = json_decode($data, TRUE);
            // add token checker
            $this->db->where('pdf_layout_name', $data['layout_name'])->delete('pdf_layouts');
            if ($this->db->affected_rows())
            {
                $this->db->where('layout_name', $data['layout_name'])->delete('pdf_editor');
                if ($this->db->affected_rows())
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'success',
                        'message' => 'Layout Berhasil Dihapus',
                    )));
                }
                else
                {
                    log_message('error', 'failed to delte pdf_layout: step 2');
                }
            }
            else
            {
                log_message('error', 'failed to delete pdf_layout: step 1');
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * edit pdf layout
     *
     * @param string $pdf_layout_name pdf layout name
     * @param string $t csrf token
     * @return void
     */
    public function PDF_editor($pdf_layout_name, $t)
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

        $query = $this->db->where('username', $this->_username)->get('incoming_mail');
        $im_result = $query->result();
        $data = array(
            'uprof_data' => $user_profile_data[0],
            'new_im' => $new_im,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
        );
        $this->load->view('admin/pdf-editor', $data, FALSE);
    }

    // ------------------------------------------------------------------------

    public function PDF_editor_viewer($pdf_layout_name, $t)
    {
        $mail_type = 'im';
        if ($t == $this->session->userdata('CSRF'))
        {
            //
            $query = $this->db->where('layout_name', ucwords($pdf_layout_name))->get('pdf_editor');
            $result = $query->result();
            //
            $this->pdfcdmanp->convert_data($result[0]->layout_data);
            $pdf_layout_data = $this->pdfcdmanp->get_data();
            //
            $this->pdfcdmanp->convert_data($result[0]->pdf_page_setup);
            $pdf_page_setup = $this->pdfcdmanp->get_data();
            //
            $query = $this->db->where('username', $this->_username)->get('settings');
            $settings_data = $query->result();
            //
            $pdflay_data_name = array_keys($pdf_layout_data);
            $document_name = $result[0]->layout_name . '.pdf';
            $pdf_txt_data = array(
                'idAndMailType' => '1.Surat Masuk',
                'docTitle' => $settings_data[0]->mail_document_heading,
                'docAddr' => $settings_data[0]->mail_document_address,
                'docContact' => $settings_data[0]->mail_document_contact,
                'docMailNum' => 'Nomor Surat: XII/19/99/99',
                'docDate' => '17-2-2019',
                'docFor' => 'Untuk: Kepala Bagian',
                'docSubject' => 'Perihal: Rapat Umum',
                'docContents' => 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tempora in nostrum libero quibusdam voluptas repudiandae aperiam totam deleniti? Optio saepe quaerat quisquam reiciendis tempore vero perferendis tenetur ratione id fugit?',
                'docSignature' => array(
                    'ftxt' => 'Hormat Saya,',
                    'stxt' => '',
                    'thtxt' => 'Sekretaris',
                ),
            );
            $pdf = new TCPDF($pdf_page_setup['orientation'], $pdf_page_setup['unit'], $pdf_page_setup['format']);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor(PDF_AUTHOR);
            $pdf->SetTitle($pdf_txt_data['docSubject']);
            $pdf->SetSubject($pdf_txt_data['docSubject']);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            $pdf->SetXY(0, 0);
            for ($i = 0; $i < count($pdflay_data_name); $i++)
            {
                switch ($pdflay_data_name[$i])
                {
                case 'idAndMailType':
                    $data_name = 'idAndMailType';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['idAndMailType']);
                    }
                    break;
                case 'docTitle':
                    $data_name = 'docTitle';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data[$data_name]);
                    }
                    break;
                case 'docAddr':
                    $data_name = 'docAddr';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docAddr']);
                    }
                    break;
                case 'docContact':
                    $data_name = 'docContact';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docContact']);
                    }
                    break;
                case 'line':
                    $data_name = 'line';
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]) && array_key_exists('x2pos', $pdf_layout_data[$data_name]) && array_key_exists('y2pos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->Line(
                            $pdf_layout_data[$data_name]['xpos'],
                            $pdf_layout_data[$data_name]['ypos'],
                            $pdf_layout_data[$data_name]['x2pos'],
                            $pdf_layout_data[$data_name]['y2pos']
                        );
                    }
                    break;
                case 'docMailNum':
                    $data_name = 'docMailNum';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docMailNum']);
                    }
                    break;
                case 'docDate':
                    $data_name = 'docDate';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docDate']);
                    }
                    break;
                case 'docFor':
                    $data_name = 'docFor';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docFor']);
                    }
                    break;
                case 'docSubject':
                    $data_name = 'docSubject';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docSubject']);
                    }
                    break;
                case 'docContents':
                    $data_name = 'docContents';
                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        if (array_key_exists('w', $pdf_layout_data[$data_name]) && array_key_exists('h', $pdf_layout_data[$data_name]) && array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                            {
                            $pdf->writeHTMLCell($pdf_layout_data[$data_name]['w'], $pdf_layout_data[$data_name]['h'],
                                $pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos'], $pdf_txt_data['docContents']);
                        }
                    }
                    break;
                case 'docSignature':
                    $data_name = 'docSignature';
                    if (array_key_exists('fxpos', $pdf_layout_data[$data_name]) && array_key_exists('fypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['fxpos'], $pdf_layout_data[$data_name]['fypos']);
                        $fpos = 2;
                    }

                    if ($fpos == 2)
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['ftxt_font_style'], $pdf_layout_data[$data_name]['font_size']);

                        $pdf->Write(0, $pdf_txt_data['docSignature']['ftxt']);
                    }

                    if (array_key_exists('sxpos', $pdf_layout_data[$data_name]) && array_key_exists('sypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['sxpos'], $pdf_layout_data[$data_name]['sypos']);
                        $spos = 2;
                    }

                    if ($spos == 2)
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['stxt_font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $pdf->Write(0, $pdf_txt_data['docSignature']['stxt']);
                    }

                    if (array_key_exists('thxpos', $pdf_layout_data[$data_name]) && array_key_exists('thypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['thxpos'], $pdf_layout_data[$data_name]['thypos']);
                        $thpos = 2;
                    }

                    if ($thpos == 2)
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['thtxt_font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $pdf->Write(0, $pdf_txt_data['docSignature']['thtxt']);
                    }
                    break;
                }
            }
            $pdf->Output($document_name);
        }
    }

    // ------------------------------------------------------------------------

    public function get_editor_data()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = json_decode($this->input->post('data', TRUE), TRUE);
            if ($data['t'] == $this->session->userdata('CSRF'))
            {
                $query = $this->db->where('pdf_layout_name', $data['layout_name'])->get('pdf_layouts');
                $result = $query->result();

                if ($result)
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'success',
                        'message' => 'Data Layout Berhasil Dimuat',
                        'data' => json_decode($result[0]->pdf_layout_data, TRUE),
                        'page_setup' => json_decode($result[0]->pdf_page_setup),
                    )));
                }
                else
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => 'failed',
                        'message' => 'Data Layout Gagal Dimuat',
                    )));
                }
            }
        }

    }

    // ------------------------------------------------------------------------

    public function update_pdf_e_laydata()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = json_decode($this->input->post('data', TRUE), TRUE);
            if ($data['t'] == $this->session->userdata('CSRF'))
            {
                $data_update = array();
                if (is_array($data['new_data']))
                {
                    $data['new_data'] = json_encode($data['new_data']);
                }
                $data_update[$data['data_name']] = $data['new_data'];
                if ($data['save_data'] === TRUE)
                {
                    if ($data['new_layname'] !== FALSE)
                    {
                        $data_update['pdf_layout_name'] = $data['new_layname'];
                    }
                    $data_update['pdf_page_setup'] = json_encode($data['page_setup']);
                    $this->db->where('pdf_layout_name', $data['layout_name'])->update('pdf_layouts', $data_update);
                    if ($this->db->affected_rows())
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'success',
                            'message' => 'Data Layout Berhasil Diperbaharui',
                        )));
                    }
                    else
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'failed',
                            'message' => 'Data Layout Gagal Diperbaharui',
                        )));
                    }
                }
                else if ($data['save_data'] === FALSE)
                {
                    $this->db->where('layout_name', $data['layout_name'])->update('pdf_editor', $data_update);
                    if ($this->db->affected_rows())
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'success',
                            'message' => 'Data Layout Berhasil Diperbaharui',
                        )));
                    }
                    else
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'failed',
                            'message' => 'Data Layout Gagal Diperbaharui',
                        )));
                    }
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * view the document as pdf in browser
     *
     * @param string $pdf_layout_name pdf layout name
     * @param string $t csrf token
     * @return void
     */
    public function PDF_viewer($pdf_layout_name, $mail_type, $t)
    {
        $pdf_layout_name = xss_clean(urldecode($pdf_layout_name));
        if ($t == $this->session->userdata('CSRF'))
        {
            //
            $query = $this->db->where('pdf_layout_name', ucwords($pdf_layout_name))->get('pdf_layouts');
            $result = $query->result();
            //
            $this->pdfcdmanp->convert_data($result[0]->pdf_layout_data);
            $pdf_layout_data = $this->pdfcdmanp->get_data();
            //
            $this->pdfcdmanp->convert_data($result[0]->pdf_page_setup);
            $pdf_page_setup = $this->pdfcdmanp->get_data();
            //
            if ($mail_type == 'im')
            {
                $query = $this->db->where('username', $this->_username)->get('incoming_mail');
                $mail_data = $query->result();
                $mail_type = 'Surat Masuk';
            }
            else if ($mail_type == 'om')
            {
                $query = $this->db->where('username', $this->_username)->get('outgoing_mail');
                $mail_data = $query->result();
                $mail_type = 'Surat Keluar';
            }
            //
            $query = $this->db->where('username', $this->_username)->get('settings');
            $settings_data = $query->result();
            //
            $pdflay_data_name = array_keys($pdf_layout_data);
            $document_name = $result[0]->pdf_layout_name . '.pdf';
            $pdf_txt_data = array(
                'idAndMailType' => $mail_data[0]->id . '.' . $mail_type,
                'docTitle' => $settings_data[0]->mail_document_heading,
                'docAddr' => $settings_data[0]->mail_document_address,
                'docContact' => $settings_data[0]->mail_document_contact,
                'docMailNum' => $mail_data[0]->mail_number,
                'docDate' => $mail_data[0]->date,
                'docFor' => $mail_data[0]->for,
                'docSubject' => $mail_data[0]->subject,
                'docContents' => $mail_data[0]->contents,
                'docSignature' => array(
                    'ftxt' => 'Hormat Saya,',
                    'stxt' => '',
                    'thtxt' => $mail_data[0]->from,
                ),
            );
            $pdf = new TCPDF($pdf_page_setup['orientation'], $pdf_page_setup['unit'], $pdf_page_setup['format']);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor(PDF_AUTHOR);
            $pdf->SetTitle($pdf_txt_data['docSubject']);
            $pdf->SetSubject($pdf_txt_data['docSubject']);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->AddPage();
            $pdf->SetXY(0, 0);
            for ($i = 0; $i < count($pdflay_data_name); $i++)
            {
                switch ($pdflay_data_name[$i])
                {
                case 'idAndMailType':
                    $data_name = 'idAndMailType';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['idAndMailType']);
                    }
                    break;
                case 'docTitle':
                    $data_name = 'docTitle';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data[$data_name]);
                    }
                    break;
                case 'docAddr':
                    $data_name = 'docAddr';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docAddr']);
                    }
                    break;
                case 'docContact':
                    $data_name = 'docContact';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docContact']);
                    }
                    break;
                case 'line':
                    $data_name = 'line';
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]) && array_key_exists('x2pos', $pdf_layout_data[$data_name]) && array_key_exists('y2pos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->Line(
                            $pdf_layout_data[$data_name]['xpos'],
                            $pdf_layout_data[$data_name]['ypos'],
                            $pdf_layout_data[$data_name]['x2pos'],
                            $pdf_layout_data[$data_name]['y2pos']
                        );
                    }
                    break;
                case 'docMailNum':
                    $data_name = 'docMailNum';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docMailNum']);
                    }
                    break;
                case 'docDate':
                    $data_name = 'docDate';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docDate']);
                    }
                    break;
                case 'docFor':
                    $data_name = 'docFor';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docFor']);
                    }
                    break;
                case 'docSubject':
                    $data_name = 'docSubject';
                    $accept = 0;
                    if (array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos']);
                        $accept += 1;
                    }

                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        $accept += 1;
                    }

                    if ($accept == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docSubject']);
                    }
                    break;
                case 'docContents':
                    $data_name = 'docContents';
                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_style', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            $pdf_layout_data[$data_name]['font_style'], $pdf_layout_data[$data_name]['font_size']);
                        if (array_key_exists('w', $pdf_layout_data[$data_name]) && array_key_exists('h', $pdf_layout_data[$data_name]) && array_key_exists('xpos', $pdf_layout_data[$data_name]) && array_key_exists('ypos', $pdf_layout_data[$data_name]))
                            {
                            $pdf->writeHTMLCell($pdf_layout_data[$data_name]['w'], $pdf_layout_data[$data_name]['h'],
                                $pdf_layout_data[$data_name]['xpos'], $pdf_layout_data[$data_name]['ypos'], $pdf_txt_data['docContents']);
                        }
                    }
                    break;
                case 'docSignature':
                    $data_name = 'docSignature';
                    if (array_key_exists('fxpos', $pdf_layout_data[$data_name]) && array_key_exists('fypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['fxpos'], $pdf_layout_data[$data_name]['fypos']);
                        $fpos = 2;
                    }

                    if ($fpos == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docSignature']['ftxt']);
                    }

                    if (array_key_exists('sxpos', $pdf_layout_data[$data_name]) && array_key_exists('sypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['sxpos'], $pdf_layout_data[$data_name]['sypos']);
                        $spos = 2;
                    }

                    if ($spos == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docSignature']['stxt']);
                    }

                    if (array_key_exists('thxpos', $pdf_layout_data[$data_name]) && array_key_exists('thypos', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetXY($pdf_layout_data[$data_name]['thxpos'], $pdf_layout_data[$data_name]['thypos']);
                        $thpos = 2;
                    }

                    if ($thpos == 2)
                        {
                        $pdf->Write(0, $pdf_txt_data['docSignature']['thtxt']);
                    }
                    break;
                }
            }
            $pdf->Output($document_name);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @return void
     */
    public function load_pdf_layouts()
    {
        if ($this->input->post() && $this->input->is_ajax_request())
        {
            $data = $this->input->post('data', TRUE);
            if ($data['t'] == $this->session->userdata('CSRF'))
            {
                $query = $this->db->get('pdf_layouts');

                if ($this->db->affected_rows())
                {
                    $result = $query->result();
                    $pdf_layout_name = array();
                    foreach ($result as $key)
                    {
                        array_push($pdf_layout_name, $key->pdf_layout_name);
                    }
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode(array(
                            'status' => 'success',
                            'pdf_layout_name' => $pdf_layout_name,
                        )));
                }
                else
                {
                    $this->output->set_content_type('application/json')
                        ->set_output(json_encode(array(
                            'status' => 'failed',
                            'message' => 'gagal untuk memuat list layout',
                        )));
                }
            }
            else
            {
                log_message('error', 'token tidak sama');
                $this->output->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'status' => 'error',
                        'message' => 'token tidak sama',
                    )));

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
    public function remove_item()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = array(
                'id' => $this->input->post('id', TRUE),
                'item_type' => $this->input->post('item_type', TRUE),
                'item_data' => $this->input->post('item_data', TRUE),
                'all_item' => $this->input->post('all_item', TRUE),
                'selected_item' => $this->input->post('selected_item', TRUE),
                'token' => $this->input->post('t', TRUE),
            );

            if ($data['token'] == $this->session->userdata('CSRF'))
            {
                $query = $this->db->where('username', $this->_username)->get('users');
                $result = $query->result();
                $hashed_password = $result[0]->password;
                switch ($data['item_type'])
                {
                case 'user_management':
                    if ($this->asmp_security->verify_hashed_password($data['item_data']['password'], $hashed_password))
                        {
                        if ($data['all_item'] === 'true')
                            {
                            $this->db->where('role', 'user')->delete('users');
                            if ($this->db->affected_rows())
                                {
                                $this->db->where_not_in('id', '1')->delete('settings');
                                if ($this->db->affected_rows())
                                    {
                                    $query = $this->db->get('register_limit');
                                    $limit = $query->result();
                                    $query = $this->db->get('field_sections');
                                    $fs_count = $query->num_rows();
                                    $limit = $fs_count - 1;
                                    $this->db->update('register_limit', array('limit' => $limit));
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua akun berhasil dihapus'));
                                }
                                    else
                                    {
                                    log_message('error', 'failed to delete user settings');
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus semua akun'));
                                }
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus semua akun'));
                            }
                        }
                            else if ($data['selected_item'] === 'true')
                            {
                            $i = 0;
                            $result_message = array();
                            for (; $i < count($data['item_data']['names']); $i++)
                                {
                                $query = $this->db->where('true_name', $data['item_data']['names'][$i])->get('users');
                                $result = $query->result();
                                $data['id'][$i] = $result[0]->id;
                                $this->db->where('true_name', $data['item_data']['names'][$i])->delete('users');
                                if ($this->db->affected_rows())
                                    {
                                    $this->db->where('username', $result[0]->username)->delete('settings');
                                    if ($this->db->affected_rows())
                                        {
                                        $query = $this->db->get('register_limit');
                                        $limit = $query->result();
                                        $limit = $limit[0]->limit + 1;
                                        $this->db->update('register_limit', array('limit' => $limit));
                                        if (!$this->db->affected_rows())
                                            {
                                            log_message('error', 'register limit not decrase');
                                        }

                                        $result_message[$i] = '<i class="fa fa-check-circle"></i> Akun ' . $data['item_data']['names'][$i] . ' berhasil dihapus';
                                    }
                                        else
                                        {
                                        log_message('error', 'failed to delete ' . $data['item_data']['names'][$i] . ' settings');
                                        $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Akun ' . $data['item_data']['names'][$i] . ' gagal dihapus';
                                    }
                                }
                                    else
                                    {
                                    $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Akun ' . $data['item_data']['names'][$i] . ' gagal dihapus';
                                }
                            }
                            $min_id = min($data['id']);
                            $max_id = max($data['id']);
                            if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                                {
                                $query = $this->db->get('users');
                                $row_count = $query->num_rows();
                                $target_id = $min_id + 1;
                                $target_break = $row_count + 1;
                                $i = 0;
                                while ($i <= $target_break)
                                    {
                                    if (!in_array($target_id, $data['id']))
                                        {
                                        $this->db->where('id', $target_id)->update('users', array('id' => $min_id));
                                        $this->db->where('id', $target_id)->update('settings', array('id' => $min_id));
                                        ++$min_id;
                                        ++$target_id;
                                    }
                                        else
                                        {
                                        ++$target_id;
                                    }
                                    ++$i;
                                }
                            }
                                else
                                {
                                $query = $this->db->get('users');
                                $row_count = $query->num_rows();
                                $min_id = min($data['id']);
                                $max_id = max($data['id']);
                                $target_id = $min_id + 1;
                                $target_break = $row_count + 1;
                                $i = 0;
                                while ($i <= $target_break)
                                    {
                                    if (!in_array($target_id, $data['id']))
                                        {
                                        $this->db->where('id', $target_id)->update('users', array('id' => $min_id));
                                        $this->db->where('id', $target_id)->update('settings', array('id' => $min_id));
                                        ++$min_id;
                                        ++$target_id;
                                    }
                                        else
                                        {
                                        ++$target_id;
                                    }
                                    ++$i;
                                }
                            }
                            $result_message = implode(',', $result_message);
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => $result_message, 'ids' => $data['id']));
                        }
                            else
                            {
                            $query = $this->db->where('true_name', $data['item_data']['name'])->get('users');
                            $result = $query->result();
                            $this->db->where('true_name', $data['item_data']['name'])->delete('users');
                            if ($this->db->affected_rows())
                                {
                                $this->db->where('username', $result[0]->username)->delete('settings');
                                if ($this->db->affected_rows())
                                    {
                                    $query = $this->db->get('register_limit');
                                    $limit = $query->result();
                                    $limit = $limit[0]->limit + 1;
                                    $this->db->update('register_limit', array('limit' => $limit));
                                    if (!$this->db->affected_rows())
                                        {
                                        log_message('error', 'register limit not decrase');
                                    }
                                    $query = $this->db->get('users');
                                    $row_count = $query->num_rows();
                                    $i = 0;
                                    $first_id = $data['id'] + 1;
                                    $target_id = $data['id'];
                                    $row_count = $row_count - ($data['id'] - 1);
                                    for (; $i < $row_count; $i++)
                                        {
                                        $this->db->where('id', $first_id)->update('users', array('id' => $target_id));
                                        $this->db->where('id', $first_id)->update('settings', array('id' => $target_id));
                                        ++$first_id;
                                        ++$target_id;
                                    }
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Akun ' . $data['item_data']['name'] . ' berhasil dihapus'));
                                }
                                    else
                                    {
                                    log_message('error', 'failed to delete ' . $data['item_data']['name'] . ' settings');
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Akun ' . $data['item_data']['name'] . ' gagal dihapus'));
                                }
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Akun ' . $data['item_data']['name'] . ' gagal dihapus'));
                            }
                        }
                    }
                        else
                        {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> Password tidak valid!'));
                    }
                    break;
                case 'incoming_mail':
                    if ($data['all_item'] === 'true')
                        {
                        $this->db->where('username', $this->_username)->delete('incoming_mail');
                        if ($this->db->affected_rows())
                            {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua surat masuk berhasil dihapus'));
                        }
                            else
                            {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus semua surat masuk'));
                        }
                    }
                        else if ($data['selected_item'] === 'true')
                        {
                        $i = 0;
                        $result_message = array();
                        for (; $i < count($data['item_data']['names']); $i++)
                            {
                            $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $data['item_data']['mails_number'][$i],
                            ))->delete('incoming_mail');
                            if ($this->db->affected_rows())
                                {
                                $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mails_number'][$i] . ' berhasil dihapus';
                            }
                                else
                                {
                                $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Surat Masuk ' . $data['item_data']['mails_number'][$i] . ' gagal dihapus';
                            }
                        }
                        $result_message = implode(',', $result_message);
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'success', 'message' => $result_message));
                    }
                        else
                        {
                        $this->db->where(array(
                            'username' => $this->_username,
                            'mail_number' => $data['item_data']['mail_number'],
                        ))->delete('incoming_mail');
                        if ($this->db->affected_rows())
                            {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mail_number'] . ' berhasil dihapus'));
                        }
                            else
                            {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Surat Masuk ' . $data['item_data']['mail_number'] . ' gagal dihapus'));
                        }
                    }
                    break;
                case 'field_sections':
                    if ($this->asmp_security->verify_hashed_password($data['item_data']['password'], $hashed_password))
                        {
                        if ($data['all_item'] === 'true')
                            {
                            if ($this->appsettings->get_setting('multiple_remove_action') == 'all')
                                {
                                $this->db->where_not_in('task', 'leader_accept_lvl3_reply')->delete('field_sections');
                                if ($this->db->affected_rows())
                                    {
                                    $register_limit = 0;
                                    $this->db->update('register_limit', array('limit' => $register_limit));
                                    if (!$this->db->affected_rows())
                                        {
                                        log_message('error', 'limit not decrement!');
                                    }
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua bidang/bagian berhasil dihapus'));
                                }
                                    else
                                    {
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus bidang/bagian'));
                                }
                            }
                                else if ($this->appsettings->get_setting('multiple_remove_action') == 'selected')
                                {
                                $this->db->where_in('id', $data['id'])->delete('field_sections');
                                if ($this->db->affected_rows())
                                    {
                                    $query = $this->db->get('register_limit');
                                    $register_limit = $query->result();
                                    $register_limit = $register_limit[0]->limit - count($data['id']);
                                    $this->db->update('register_limit', array('limit' => $register_limit));
                                    if (!$this->db->affected_rows())
                                        {
                                        log_message('error', 'limit not increment');
                                    }
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua bidang/bagian yang dipilih berhasil dihapus'));
                                }
                                    else
                                    {
                                    header('Content-Type: application/json');
                                    echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus Semua bidang/bagian yang dipilih'));
                                }
                            }
                        }
                            else if ($data['selected_item'] === 'true')
                            {
                            $i = 0;
                            $result_message = array();
                            $query = $this->db->get('register_limit');
                            $register_limit = $query->result();
                            for (; $i < count($data['item_data']['field_sections']); $i++)
                                {
                                $this->db->where('field_section_name', $data['item_data']['field_sections'][$i])->delete('field_sections');
                                if ($this->db->affected_rows())
                                    {
                                    $register_limit = $register_limit[0]->limit - 1;
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Bidang/Bagian ' . $data['item_data']['field_sections'][$i] . ' berhasil dihapus';
                                }
                                    else
                                    {
                                    $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Bidang/Bagian ' . $data['item_data']['field_sections'][$i] . ' gagal dihapus';
                                }
                            }
                            $this->db->update('register_limit', array('limit' => $register_limit));
                            if (!$this->db->affected_rows())
                                {
                                log_message('error', 'limit not decrement!');
                            }
                            $result_message = implode('</br>', $result_message);
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'normal', 'message' => $result_message));
                        }
                            else
                            {
                            $this->db->where('field_section_name', $data['item_data']['field_section'])->delete('field_sections');
                            if ($this->db->affected_rows())
                                {
                                $query = $this->db->get('register_limit');
                                $register_limit = $query->result();
                                $register_limit = $register_limit[0]->limit - 1;
                                $this->db->update('register_limit', array('limit' => $register_limit));
                                if (!$this->db->affected_rows())
                                    {
                                    log_message('error', 'limit not decrement!');
                                }
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Bidang/Bagian ' . $data['item_data']['field_section'] . ' berhasil dihapus'));
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Bidang/Bagian ' . $data['item_data']['field_section'] . ' gagal dihapus'));
                            }
                        }
                    }
                        else
                        {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> Bidang/Bagian gagal dihapus. Kata Sandi Tidak Valid!', 'data' => $data));
                    }
                    break;
                case 'trash_can':

                    break;
                default:
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => 'no item type'));
                    break;
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-triangle"></i> Token tidak sama!'));
            }
        }
        else
        {
            redirect('admin', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    /**
     * show about application, and update button
     *
     * @return void
     */
    public function about()
    {

    }
}
