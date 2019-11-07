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

    private $_om_auth = FALSE;
    /**
     * constructor ini digunakan untuk membuat table awal untuk registrasi awal
     * DOC[01] - ASMP Program Documentation
     */
    public function __construct()
    {
        parent::__construct();

        //$this->output->enable_profiler(TRUE);

        // set default time zone
		date_default_timezone_set('Asia/Jakarta');

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

        $query = $this->db->where('username', $this->_username)->get('users');
        $result = $query->result();

        $query = $this->db->where('field_section_name', $result[0]->position)->get('field_sections');
        $result = $query->result();

        if ($result[0]->task == 'normal_accept_sending')
        {
            $this->_om_auth = TRUE;
        }
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
         *
         */
        $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

        /**
         * $new_im_count - Varible yang menampung jumlah bari dari incoming_mail
         *
         * @var int
         */
        $new_im_count = $query->num_rows();

        /**
         *
         */

        $query = $this->db->where_in('status', array('lawas', 'lawas-dpss', 'lawas-accepted'))->like('username', $this->_username)->get('incoming_mail');

        /**
         *
         */
        $old_im = $query->num_rows();

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

        $query = $this->db->where(array('username' => $username, 'mail_type' => 'im'))->get('trash_can');

        /**
         * $imtr_count - Menampung jumlah surat masuk yang ada pada tempat sampah
         *
         * @var int
         */
        $imtr_count = $query->num_rows();

        $query = $this->db->where(array('username' => $username, 'mail_type' => 'om'))->get('trash_can');

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
            'name' => $row[0]->true_name,
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'new_im' => $new_im,
            'old_im' => $old_im,
            'om_auth' => $this->_om_auth,
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
                // $this->db->where('username', $this->_username)->update('users', array('logged' => 0));
                // if ($this->db->affected_rows())
                // {
                    $this->session->unset_userdata(array('user_login'));
                    //$this->session->sess_destroy();
                    $this->session->set_userdata('login_pg_msg', 'logout_true');
                    // Tulis log
                    $this->activity_log->create_activity_log('logout_activity', ' Telah Log Out', null, $this->_username);
                    // Ubah tipe content ke JSON
                    header('Content-Type: application/json');
                    // Mengirim output data ke client
                    echo json_encode(array('status' => 'success', 'message' => site_url('login')));
                // }
                // else
                // {
                //     // Ubah tipe content ke JSON
                //     header('Content-Type: application/json');
                //     // Mengirim output data ke client
                //     echo json_encode(array('status' => 'failed', 'message' => $this->lang->line('logout_f')));
                // }
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

            if (!is_dir($gallery_dir))
            {
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
                        echo json_encode(array('status' => 'warning', 'message' => '<i class="fa fa-exclamation-triangle"></i> Token Tidak Sama!'));
                    }
                    else
                    {
                        $this->activity_log->create_activity_log('delete_glry_image', 'Telah menghapus foto dari gallery', null, $this->_username);
                        $this->load->library('UploadHandler', $options);
                    }
                }
                else
                {
                    $this->load->library('UploadHandler', $options);
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> Direktori Tidak Ditemukan!'));
            }
        }
        else
        {
            redirect(site_url('user'), 'refresh');
        }
    }

    public function set_upld_log_activity()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            if ($this->input->post('status', TRUE) == TRUE)
            {
                $this->activity_log->create_activity_log('upload_glry_image', 'Telah mengupload foto ke gallery', null, $this->_username);
                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => 'success',
                    'message' => 'Upload Berhasil'
                    )
                ));
            }
            else if ($this->input->post('status', TRUE) == FALSE)
            {
                $this->activity_log->create_activity_log('upload_glry_image', 'Gagal mengupload foto ke gallery', null, $this->_username);
                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => 'success',
                    'message' => 'Upload Gagal'
                    )
                ));
                
            }
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
                
                if (file_exists($filename))
                {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'success', 'message' => 'Berhasil mengubah foto profil! > ' . $imageName));
                }
                else
                {
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => $imageName . ' Tidak Ditemukan'));
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal mengubah foto profil!'));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    public function update_profile()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $data = json_decode($this->input->post('requested_data', TRUE), TRUE);
            if ($data['token'] == $this->session->userdata('CSRF'))
            {
                if ($data['change_type'] == 'name')
                {
                    $query = $this->db->where('username', $this->_username)->get('users');
                    $user_data = $query->result()[0];
    
                    if (!empty($data['fdata']['username']) && !empty($data['fdata']['true_name']) && !empty($data['fdata']['password']) )
                    {
                        if ($this->asmp_security->verify_hashed_password($data['fdata']['password'], $user_data->password))
                        {
                            if ($data['fdata']['username'] !== $user_data->username && $data['fdata']['true_name'] !== $user_data->true_name)
                            {
                                $this->db->where('username', $this->_username)->update('users', array(
                                    'username' => $data['fdata']['username']
                                ));
                                
                                if ($this->db->affected_rows())
                                {
                                    $this->session->unset_userdata(array('admin_login'));

                                    $new_sess = array(
                                        'admin_login' => $data['fdata']['username']
                                    );

                                    $this->session->set_userdata($new_sess);

                                    $this->db->where('username', $data['fdata']['username'])->update('users', array(
                                        'true_name' => $data['fdata']['true_name']
                                    ));
                                    
                                    if ($this->db->affected_rows())
                                    {
                                        $this->activity_log->create_activity_log('profile_update', 'Nama pengguna dan nama asli berhasil diubah', null, $this->_username);
                                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                            'status' => 'success',
                                            'message' => 'Nama pengguna dan nama asli berhasil diubah',
                                            'data' => array(
                                                'change_count' => 2,
                                                'change_name1' => 'username',
                                                'change_name2' => 'true_name',
                                                'change_value1' => $data['fdata']['username'],
                                                'change_value2' => $data['fdata']['true_name']
                                            )
                                        )));
                                    }
                                    else
                                    {
                                        $this->activity_log->create_activity_log('profile_update', 'Nama pengguna berhasil diubah tapi nama asli gagal diubah', null, $this->_username);
                                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                            'status' => 'success',
                                            'message' => 'Nama pengguna berhasil diubah tapi nama asli gagal diubah',
                                            'data' => array(
                                                'change_count' => 1,
                                                'change_name' => 'username',
                                                'change_value' => $data['fdata']['username']
                                            )
                                        )));
                                    }
                                }
                                else
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Nama pengguna dan nama asli gagal diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'error',
                                        'message' => 'Nama pengguna dan nama asli gagal diubah'
                                    )));
                                }
                            }
                            else if ($data['fdata']['username'] !== $user_data->username)
                            {
                                $this->db->where('username', $this->_username)->update('users', array(
                                    'username' => $data['fdata']['username']
                                ));
                                
                                if ($this->db->affected_rows())
                                {
                                    $this->session->unset_userdata(array('admin_login'));

                                    $new_sess = array(
                                        'admin_login' => $data['fdata']['username']
                                    );

                                    $this->session->set_userdata($new_sess);

                                    $this->activity_log->create_activity_log('profile_update', 'Nama pengguna berhasil diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'success',
                                        'message' => 'Nama pengguna berhasil diubah',
                                        'data' => array(
                                            'change_count' => 1,
                                            'change_name' => 'username',
                                            'change_value' => $data['fdata']['username']
                                        )
                                    )));
                                }
                                else
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Nama pengguna gagal diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'error',
                                        'message' => 'Nama pengguna gagal diubah'
                                    )));
                                }
                            }
                            else if ($data['fdata']['true_name'] !== $user_data->true_name)
                            {
                                $this->db->where('username', $this->_username)->update('users', array(
                                    'true_name' => $data['fdata']['true_name']
                                ));
                                
                                if ($this->db->affected_rows())
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Nama asli berhasil diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'success',
                                        'message' => 'Nama asli berhasil diubah',
                                        'data' => array(
                                            'change_count' => 1,
                                            'change_name' => 'true_name',
                                            'change_value' => $data['fdata']['true_name']
                                        )
                                    )));
                                }
                                else
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Nama asli gagal diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'error',
                                        'message' => 'Nama asli gagal diubah'
                                    )));
                                }
                            }
                        }
                        else
                        {
                            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                'status' => 'error',
                                'message' => 'kata sandi tidak valid'
                            )));
                        }
                    }
                    else
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'warning',
                            'message' => 'nama pengguna, nama asli pengguna, dan kata sandi tidak boleh kosong'
                        )));
                    }
                }
                else if ($data['change_type'] == 'password')
                {
                    $query = $this->db->where('username', $this->_username)->get('users');
                    $user_data = $query->result()[0];
    
                    if (!empty($data['fdata']['old_password']) && !empty($data['fdata']['new_password']) && !empty($data['fdata']['new_password_confirm']))
                    {
                        if ($this->asmp_security->verify_hashed_password($data['fdata']['old_password'], $user_data->password))
                        {
                            if ($data['fdata']['new_password'] === $data['fdata']['new_password_confirm'])
                            {
                                $this->db->where('username', $this->_username)->update('users', array(
                                    'password' => $this->asmp_security->get_hashed_password($data['fdata']['new_password'])
                                ));
                                
                                if ($this->db->affected_rows())
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Kata sandi berhasil diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'success',
                                        'message' => 'Kata sandi berhasil diubah'
                                    )));
                                }
                                else
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'Kata sandi gagal diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'error',
                                        'message' => 'Kata sandi gagal diubah'
                                    )));
                                }
                            }
                            else
                            {
                                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                    'status' => 'error',
                                    'message' => 'kata sandi baru dan konfirmasi kata sandi baru harus sama'
                                )));
                            }
                        }
                        else
                        {
                            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                'status' => 'error',
                                'message' => 'kata sandi tidak valid'
                            )));
                        }
                    }
                    else
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'warning',
                            'message' => 'kata sandi lama tidak boleh kosong'
                        )));
                    }   
                }
                else if ($data['change_type'] == 'email')
                {
                    $query = $this->db->where('username', $this->_username)->get('users');
                    $user_data = $query->result()[0];
    
                    if (!empty($data['fdata']['password']) && !empty($data['fdata']['email']))
                    {
                        if ($this->asmp_security->verify_hashed_password($data['fdata']['password'], $user_data->password))
                        {
                            if ($data['fdata']['email'] !== $user_data->email)
                            {
                                $this->db->where('username', $this->_username)->update('users', array(
                                    'email' => $data['fdata']['email']
                                ));
                                
                                if ($this->db->affected_rows())
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'E-Mail berhasil diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'success',
                                        'message' => 'E-Mail berhasil diubah'
                                    )));
                                }
                                else
                                {
                                    $this->activity_log->create_activity_log('profile_update', 'E-Mail gagal diubah', null, $this->_username);
                                    $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                        'status' => 'error',
                                        'message' => 'E-Mail gagal diubah'
                                    )));
                                }
                            }
                        }
                        else
                        {
                            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                                'status' => 'warning',
                                'message' => 'kata sandi tidak valid'
                            )));
                        }
                    }
                    else
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => 'warning',
                            'message' => 'kata sandi tidak boleh kosong'
                        )));
                    } 
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => 'warning',
                    'message' => 'token tidak sama'
                )));
            }
        }
        else
        {
            
            redirect('login','refresh');
            
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

                if ($this->input->post('cmd', TRUE) == 'save_user_settings')
                {
                    header('Content-Type: application/json');
                    echo json_encode($this->app_settings->save_user_settings($this->_username, $settings_data));
                }
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'warning', 'message' => '<i class="fa fa-exclamation-triangle"></i> Token is not match!'));
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
             *
             */
            $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

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
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                'om_auth' => $this->_om_auth,
            );
            $this->load->view('user/user-settings', $data, FALSE);
        }
    }

    // ------------------------------------------------------------------------

    public function check_new_im()
    {
        if ($this->checker->is_user())
        {
            $query = $this->db->where_in('status', array('baru', 'baru-accepted', 'baru-dpss'))->like('username', $this->_username)->get('incoming_mail');
            $result = $query->result();
            $im_count = $query->num_rows();

            if ($result)
            {
                $im_status = true;
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'newSMstatus' => $im_status, 'newSMCount' => $im_count));
            }
            else
            {
                $im_status = false;
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'failed', 'newSMstatus' => $im_status, 'newSMCount' => $im_count));
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     *
     */
    public function incoming_mail($load_item = '')
    {
        if ($this->input->is_ajax_request() && $load_item === 'load')
        {
            $token = $this->input->post('t', TRUE);
            if ($token === $this->session->userdata('CSRF'))
            {
                $username = $this->session->userdata('user_login');
                $query = $this->db->where('username', $username)->get('incoming_mail');
                $result = array();
                $result = $query->result_array();
                $im_count = $query->num_rows();
                $im_data_keys = array_keys($result);
                $im_data = array();
                $settings = $this->app_settings->get_user_settings($this->_username);
                $paging = array();
                $paging['status'] = $settings[0]->paging_status;
                $paging['limit'] = $settings[0]->row_limit;

                $i = 0;
                if ($im_count > 0)
                {
                    for (; $i < $im_count; $i++)
                    {
                        unset($result[$i]['username']);
                        unset($result[$i]['last_modified']);
                    }

                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'data' => $result,
                            'paging' => $paging,
                        )
                    ));
                }
                else if ($im_count === 0)
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'data' => '<i class="fa fa-exclamation-circle"></i> Tidak Ada Surat Keluar.',
                            'paging' => $paging,
                        )
                    ));
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'success',
                        'message' => '<i class="fa fa-exclamation-circle"></i> Token Tidak Sama.',
                    )
                ));
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
             *
             */
            $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

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
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                'app_settings' => $this->app_settings->get_app_settings()[0],
                'om_auth' => $this->_om_auth,
            );

            $this->load->view('user/incoming-mail', $data, FALSE);
        }
    }

    // ------------------------------------------------------------------------

    public function outgoing_mail($load_item = '')
    {
        if ($this->_om_auth === FALSE)
        {
            redirect('login', 'refresh');
        }
        else
        {
            if ($this->input->is_ajax_request() && $load_item === 'load')
            {
                $token = $this->input->post('t', TRUE);
                if ($token === $this->session->userdata('CSRF'))
                {
                    $username = $this->session->userdata('user_login');
                    $query = $this->db->where('username', $username)->get('outgoing_mail');
                    $result = array();
                    $result = $query->result_array();
                    $om_count = $query->num_rows();
                    $om_data_keys = array_keys($result);
                    $om_data = array();
                    $settings = $this->app_settings->get_user_settings($this->_username);
                    $paging = array();
                    $paging['status'] = $settings[0]->paging_status;
                    $paging['limit'] = $settings[0]->row_limit;

                    $i = 0;
                    if ($om_count > 0)
                    {
                        for (; $i < $om_count; $i++)
                        {
                            unset($result[$i]['username']);
                            unset($result[$i]['last_modified']);
                            $query = $this->db->where('mail_number', $result[$i]['mail_number'])->get('incoming_mail');
                            $im_count = $query->num_rows();
                        }

                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array(
                                'status' => 'success',
                                'data' => $result,
                                'paging' => $paging,
                            )
                        ));
                    }
                    else if ($om_count === 0)
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array(
                                'status' => 'success',
                                'data' => '<i class="fa fa-exclamation-circle"></i> Tidak Ada Surat Keluar.',
                                'paging' => $paging,
                            )
                        ));
                    }
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
                 *
                 */
                $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

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

                $query = $this->db->where('layout_status', 'active')->get('pdf_layouts');
                $result = $query->result();
                $pdf_layouts = '';


                $i = 0;
                for(; $i < $query->num_rows(); $i++)
                {
                    $pdf_layouts .= $result[$i]->layout_name . ',';
                    log_message('error', $pdf_layouts);
                }

                $pdf_layouts = substr($pdf_layouts, 0, -1);

                $data = array(
                    'uprof_data' => $user_profile_data[0],
                    'new_im' => $new_im,
                    'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                    'app_settings' => $this->app_settings->get_app_settings()[0],
                    'om_auth' => $this->_om_auth,
                    'pdf_layouts' => $pdf_layouts
                );

                $this->load->view('user/outgoing-mail', $data, FALSE);
            }
        }
    }

    public function view_activity()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            if ($this->checker->is_user())
            {
                $mail_data = $this->input->post('mail_data', TRUE);
                $mail_data = json_decode($mail_data, TRUE);
                $token = $this->input->post('token', TRUE);
                if ($token == $this->session->userdata('CSRF'))
                {
                    // Tulis log
                    $this->activity_log->create_activity_log('view', 'Telah Melihat', $mail_data, $this->_username);

                    $page_url = urldecode($this->input->post('page_url', TRUE));
                    $page_url = explode('/', $page_url);

                    if ($page_url[5] == 'surat-masuk')
                    {
                        if ($mail_data['status'] == 'baru' || $mail_data['status'] == 'baru-accepted' || $mail_data['status'] == 'baru-dpss')
                        {
                            if ($mail_data['status'] == 'baru-accepted')
                            {
                                $status = 'lawas-accepted';
                            }
                            else if ($mail_data['status'] == 'baru-dpss')
                            {
                                $status = 'lawas-dpss';
                            }
                            else
                            {
                                $status = 'lawas';
                            }

                            $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $mail_data['mail_number'],
                            ))->update('incoming_mail', array('status' => $status));

                            if ($this->db->affected_rows())
                            {
                                $this->output->set_content_type('application/json')->set_output(json_encode(
                                    array(
                                        'status' => 'success',
                                        'mail_status' => $status,
                                    )
                                ));
                            }
                        }
                    }
                }
                else
                {
                    log_message('error', 'token tidak sama!');
                }
            }
        }
    }

    // ------------------------------------------------------------------------

    public function im_action_exec()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {
            $requested_data = $this->input->post('request_data', TRUE);
            $requested_data = json_decode($requested_data, TRUE);
            if ($requested_data['token'] == $this->session->userdata('CSRF'))
            {
                switch ($requested_data['action'])
                {
                case 'disposition':
                    $this->im_handler->disposition($requested_data['im_data'], 'json');
                    break;
                case 'reply':
                    $this->im_handler->reply($requested_data['im_data'], 'json');
                    break;
                case 'throw':
                    $this->im_handler->throw_im($requested_data['mail_data'], 'json');
                    break;
                default:
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'error',
                            'message' => '<i class="fa fa-exclamation-circle"></i> action not found!',
                        )
                    ));
                    break;
                    break;
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'warning',
                        'data' => '<i class="fa fa-exclamation-triangle"></i> Token Tidak Sama.',
                        'paging' => $paging,
                    )
                ));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
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
            $requested_data = $this->input->post('request_data', TRUE);
            $requested_data = json_decode($requested_data, TRUE);

            if ($requested_data['t'] == $this->session->userdata('CSRF'))
            {
                switch ($requested_data['action'])
                {
                case 'load':
                    $this->om_handler->load_om($requested_data['om_data'], 'json');
                    break;
                case 'send':
                    if (isset($requested_data['om_data']['pdf_layouts']) && isset($requested_data['om_data']['mail_number']) &&
                        isset($requested_data['om_data']['mail_subject']) && $requested_data['om_data']['editor_data'])
                        {
                        if ($this->mailcomp_filter->validate_mail_number($requested_data['om_data']['mail_number']))
                            {
                            $this->om_handler->send_om($requested_data['om_data'], 'json');
                        }
                            else
                            {
                            $this->output->set_content_type('application/json')->set_output(json_encode(
                                array('status' => 'warning',
                                    'message' => '<i class="fa fa-exclamation-triangle"></i> nomor surat sudah dipakai',
                                )
                            ));
                        }
                    }
                        else
                        {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'failed',
                                'message' => '<i class="fa fa-exclamation-circle">tidak ada yang boleh kosong saat membuat surat baru</i>',
                            )
                        ));
                    }
                    break;
                case 'save':
                    if (isset($requested_data['om_data']['pdf_layouts']) && isset($requested_data['om_data']['mail_number']) &&
                        isset($requested_data['om_data']['mail_subject']) && $requested_data['om_data']['editor_data'])
                        {
                        if ($this->mailcomp_filter->validate_mail_number($requested_data['om_data']['mail_number']))
                            {
                            $this->om_handler->save_om($requested_data['om_data'], 'json');
                        }
                            else
                            {
                            $this->output->set_content_type('application/json')->set_output(json_encode(
                                array('status' => 'warning',
                                    'message' => '<i class="fa fa-exclamation-triangle"></i> nomor surat sudah dipakai',
                                )
                            ));
                        }
                    }
                        else
                        {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'failed',
                                'message' => '<i class="fa fa-exclamation-circle"></i> tidak ada yang boleh kosong saat membuat surat baru',
                            )
                        ));
                    }
                    break;
                case 'throw':
                    $this->om_handler->throw_om($requested_data['mail_data'], 'json');
                    break;
                case 'update':
                    if (isset($requested_data['om_data']['pdf_layouts']) && isset($requested_data['om_data']['mail_number']) &&
                        isset($requested_data['om_data']['mail_subject']) && $requested_data['om_data']['editor_data'])
                        {
                        if ($this->mailcomp_filter->validate_mail_number($requested_data['om_data']['mail_number']))
                            {
                            $this->om_handler->update_om($requested_data['om_data'], 'json');
                        }
                            else
                            {
                            $this->output->set_content_type('application/json')->set_output(json_encode(
                                array('status' => 'warning',
                                    'message' => '<i class="fa fa-exclamation-triangle"></i> nomor surat sudah dipakai',
                                )
                            ));
                        }
                    }
                        else
                        {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array('status' => 'failed',
                                'message' => '<i class="fa fa-exclamation-circle"></i> tidak ada yang boleh kosong saat membuat surat baru',
                            )
                        ));
                    }
                    break;
                case 're_send':
                    $this->om_handler->resend_om($requested_data['om_data'], 'json');
                    break;
                default:
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'error',
                            'message' => '<i class="fa fa-exclamation-circle"></i> action not found!',
                        )
                    ));
                    break;
                }
            }
            else
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'warning',
                        'data' => '<i class="fa fa-exclamation-triangle"></i> Token Tidak Sama.',
                        'paging' => $paging,
                    )
                ));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }

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
                                'mail_number' => $data['item_data']['mail_numbers'][$i],
                            ))->delete('incoming_mail');
                            if ($this->db->affected_rows())
                                {
                                $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mail_numbers'][$i] . ' berhasil dihapus';
                            }
                                else
                                {
                                $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Surat Masuk ' . $data['item_data']['mail_numbers'][$i] . ' gagal dihapus';
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
                case 'outgoing_mail':
                    if ($data['all_item'] === 'true')
                        {
                        $query = $this->db->where('username', $this->_username)->get('outgoing_mail');
                        $result = $query->result_array();
                        $result_count = $query->num_rows();
                        $data = array();
                        $query = $this->db->get('trash_can');
                        $trash_count = $query->num_rows();

                        for ($i = 0; $i < $result_count; $i++)
                            {
                            $data['id'][$i] = $result[$i]['id'];
                            $result[$i]['id'] = $trash_count + 1;
                            $result[$i]['mail_type'] = 'om';
                            ++$trash_count;
                        }

                        // add result checking (IMPORTANT)
                        $this->db->insert_batch('trash_can', $result);
                        if ($this->db->affected_rows())
                            {
                            $this->db->where('username', $this->_username)->delete('outgoing_mail');
                            if ($this->db->affected_rows())
                                {
                                $min_id = min($data['id']);
                                $max_id = max($data['id']);
                                if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                                    {
                                    $query = $this->db->get('outgoing_mail');
                                    $row_count = $query->num_rows();
                                    $target_id = $min_id + 1;
                                    $target_break = $row_count + 1;
                                    $i = 0;
                                    while ($i <= $target_break)
                                        {
                                        if (!in_array($target_id, $data['id']))
                                            {
                                            $this->db->where('id', $target_id)->update('outgoing_mail', array('id' => $min_id));
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
                                    $query = $this->db->get('outgoing_mail');
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
                                            $this->db->where('id', $target_id)->update('outgoing_mail', array('id' => $min_id));
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
                                // Tulis log
                                $this->activity_log->create_activity_log('move_to_trash_all', ' Semua surat keluar berhasil dibuang', null, $this->_username);

                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua surat keluar berhasil dibuang'));
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal membuang semua surat keluar'));
                            }
                        }
                            else
                            {
                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal membuang semua surat keluar'));
                        }
                    }
                        else if ($data['selected_item'] === 'true')
                        {
                        $i = 0;
                        $result_message = array();
                        $activity_data = array();

                        for (; $i < count($data['item_data']['mail_numbers']); $i++)
                            {
                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $data['item_data']['mail_numbers'][$i],
                            ))->get('outgoing_mail');
                            $result = $query->result_array();

                            $activity_data['sender'][$i] = $result[0]['sender'];
                            $activity_data['mail_number'][$i] = $result[0]['mail_number'];

                            $query = $this->db->get('trash_can');
                            $trash_count = $query->num_rows();
                            $result[0]['id'] = $trash_count + 1;
                            $result[0]['mail_type'] = 'om';
                            $status = 'failed';

                            $this->db->insert('trash_can', $result[0]);
                            ++$trash_count;
                            if ($this->db->affected_rows())
                                {
                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_number' => $data['item_data']['mail_numbers'][$i],
                                ))->delete('outgoing_mail');
                                if ($this->db->affected_rows())
                                    {
                                    $status = 'success';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' berhasil dibuang';
                                }
                                    else
                                    {
                                    $status = 'failed';
                                    $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' gagal dibuang';
                                }
                            }
                                else
                                {
                                $status = 'failed';
                                $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' gagal dibuang';
                            }
                        }

                        $min_id = min($data['id']);
                        $max_id = max($data['id']);
                        if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                            {
                            $query = $this->db->get('outgoing_mail');
                            $row_count = $query->num_rows();
                            $target_id = $min_id + 1;
                            $target_break = $row_count + 1;
                            $i = 0;
                            while ($i <= $target_break)
                                {
                                if (!in_array($target_id, $data['id']))
                                    {
                                    $this->db->where('id', $target_id)->update('outgoing_mail', array('id' => $min_id));
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
                            $query = $this->db->get('outgoing_mail');
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
                                    $this->db->where('id', $target_id)->update('outgoing_mail', array('id' => $min_id));
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

                        if ($status == 'success')
                            {
                            // Tulis log
                            $this->activity_log->create_activity_log('move_to_trash_wselected', 'Surat Keluar Berhasil Dibuang', $activity_data, $this->_username);
                        }

                        header('Content-Type: application/json');
                        echo json_encode(array('status' => $status, 'message' => $result_message));
                    }
                    break;
                case 'trash_can':
                    if ($this->asmp_security->verify_hashed_password($data['item_data']['password'], $hashed_password))
                        {
                        if ($data['all_item'] === 'true')
                            {
                            $this->db->where('username', $this->_username)->delete('trash_can');
                            if ($this->db->affected_rows())
                                {
                                // Tulis log
                                $this->activity_log->create_activity_log('deleteperm_all', 'Semua surat berhasil dihapus', null, $this->_username);

                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Semua surat berhasil dihapus'));
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> Gagal menghapus semua surat'));
                            }
                        }
                            else if ($data['selected_item'] === 'true')
                            {
                            $i = 0;
                            $result_message = array();
                            $status = 'failed';
                            $activity_data = array();

                            for (; $i < count($data['item_data']['mail_numbers']); $i++)
                                {
                                $query = $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_number' => $data['item_data']['mail_numbers'][$i],
                                ))->get('trash_can');

                                $result = $query->result_array();

                                $activity_data['sender'][$i] = $result[0]['sender'];
                                $activity_data['mail_number'][$i] = $result[0]['mail_number'];

                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_number' => $data['item_data']['mail_numbers'][$i],
                                ))->delete('trash_can');
                                if ($this->db->affected_rows())
                                    {
                                    $status = 'success';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat ' . $data['item_data']['mail_numbers'][$i] . ' berhasil dibuang';
                                }
                                    else
                                    {
                                    $status = 'failed';
                                    $result_message[$i] = '<i class="fa fa-exclamation-circle"></i> Surat ' . $data['item_data']['mail_numbers'][$i] . ' gagal dibuang';
                                }
                            }

                            $min_id = min($data['id']);
                            $max_id = max($data['id']);
                            if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                                {
                                $query = $this->db->get('trash_can');
                                $row_count = $query->num_rows();
                                $target_id = $min_id + 1;
                                $target_break = $row_count + 1;
                                $i = 0;
                                while ($i <= $target_break)
                                    {
                                    if (!in_array($target_id, $data['id']))
                                        {
                                        $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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
                                $query = $this->db->get('trash_can');
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
                                        $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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

                            if ($status == 'success')
                                {
                                // Tulis log
                                $this->activity_log->create_activity_log('deleteperm_wselected', $result_message, $activity_data, $this->_username);

                            }

                            header('Content-Type: application/json');
                            echo json_encode(array('status' => 'success', 'message' => $result_message));
                        }
                            else
                            {
                            $query = $this->db->where('username', $this->_username)->get('users');
                            $result = $query->result();
                            $hashed_password = $result[0]->password;

                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $data['item_data']['mail_number'],
                            ))->get('trash_can');

                            $activity_data = $query->result_array();

                            $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $data['item_data']['mail_number'],
                            ))->delete('trash_can');
                            if ($this->db->affected_rows())
                                {
                                $query = $this->db->get('trash_can');
                                $row_count = $query->num_rows();
                                $i = 0;
                                $first_id = $data['id'] + 1;
                                $target_id = $data['id'];
                                $row_count = $row_count - ($data['id'] - 1);
                                for (; $i < $row_count; $i++)
                                    {
                                    $this->db->where('id', $first_id)->update('trash_can', array('id' => $target_id));
                                    ++$first_id;
                                    ++$target_id;
                                }

                                // Tulis log
                                $this->activity_log->create_activity_log('delete', 'Semua surat berhasil dihapus', $activity_data[0], $this->_username);

                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Surat berhasil dihapus'));
                            }
                                else
                                {
                                header('Content-Type: application/json');
                                echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> Surat gagal dihapus'));
                            }

                        }
                    }
                        else
                        {
                        header('Content-Type: application/json');
                        echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> Password Salah!'));
                    }
                    break;
                default:
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i>no item type'));
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
            redirect('user', 'refresh');
        }
    }

    /**
     * view the document as pdf in browser
     *
     * @param string $pdf_layout_name pdf layout name
     * @param string $t csrf token
     * @return void
     */
    public function PDF_viewer($pdf_layout_name, $mail_type, $mail_number, $t)
    {
        $pdf_layout_name = xss_clean(urldecode($pdf_layout_name));
        if ($t == $this->session->userdata('CSRF'))
        {
            $mail_number = urldecode($mail_number);
            $mail_number = preg_replace('/&sol;/', '/', $mail_number);
            //
            $query = $this->db->where('layout_name', ucwords($pdf_layout_name))->get('pdf_layouts');
            $result = $query->result();
            //
            $this->pdfcdmanp->convert_data($result[0]->layout_data);
            $pdf_layout_data = $this->pdfcdmanp->get_data();
            //
            $this->pdfcdmanp->convert_data($result[0]->layout_page_setup);
            $pdf_page_setup = $this->pdfcdmanp->get_data();
            //
            if ($mail_type == 'im')
            {
                $query = $this->db->where(array(
                    'username' => $this->_username,
                    'mail_number' => $mail_number,
                ))->get('incoming_mail');
                $mail_data = $query->result();
                $mail_type = 'Surat Masuk';
            }
            else if ($mail_type == 'om')
            {
                $query = $this->db->where(array(
                    'username' => $this->_username,
                    'mail_number' => $mail_number,
                ))->get('outgoing_mail');
                $mail_data = $query->result();
                $mail_type = 'Surat Keluar';
            }

            //
            $query = $this->db->get('app_settings');
            $settings_data = $query->result();
            //
            $pdflay_data_name = array_keys($pdf_layout_data);
            $document_name = $result[0]->layout_name . '.pdf';
            $pdf_txt_data = array(
                'idAndMailType' => $mail_data[0]->id . '.' . $mail_type,
                'docTitle' => $settings_data[0]->mail_document_heading,
                'docAddr' => $settings_data[0]->mail_document_address,
                'docContact' => $settings_data[0]->mail_document_contact,
                'docMailNum' => $mail_data[0]->mail_number,
                'docDate' => $mail_data[0]->date,
                'docFor' => $mail_data[0]->receiver,
                'docSubject' => $mail_data[0]->subject,
                'docContents' => $mail_data[0]->contents,
                'docSignature' => array(
                    'ftxt' => 'Hormat Saya,',
                    'stxt' => '',
                    'thtxt' => $mail_data[0]->sender,
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
                    if (array_key_exists('font_family', $pdf_layout_data[$data_name]) && array_key_exists('font_size', $pdf_layout_data[$data_name]))
                        {
                        $pdf->SetFont($pdf_layout_data[$data_name]['font_family'],
                            '', $pdf_layout_data[$data_name]['font_size']);
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

    public function trash_can($load_item = '')
    {
        if ($this->input->is_ajax_request() && $load_item === 'load')
        {
            $token = $this->input->post('t', TRUE);
            if ($token === $this->session->userdata('CSRF'))
            {
                $username = $this->session->userdata('user_login');
                $query = $this->db->where('username', $username)->get('trash_can');
                $result = array();
                $result = $query->result_array();
                $trash_count = $query->num_rows();

                $settings = $this->app_settings->get_user_settings($this->_username);
                $paging = array();
                $paging['status'] = $settings[0]->paging_status;
                $paging['limit'] = $settings[0]->row_limit;

                if ($trash_count !== 0)
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'data' => $result,
                            'paging' => $paging,
                        )
                    ));
                }
                else if ($trash_count === 0)
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'success',
                            'data' => '<i class="fa fa-exclamation-circle"></i> Tidak Ada Sampah.',
                            'paging' => $paging,
                        )
                    ));
                }
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
             *
             */
            $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

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
                'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
                'app_settings' => $this->app_settings->get_app_settings()[0],
                'om_auth' => $this->_om_auth,
            );

            $this->load->view('user/trash-can', $data, FALSE);
        }
    }

    public function recovery_mail()
    {
        if ($this->input->is_ajax_request())
        {
            $data = array(
                'id' => $this->input->post('id', TRUE),
                'item_type' => $this->input->post('item_type', TRUE),
                'item_data' => $this->input->post('item_data', TRUE),
                'all_item' => $this->input->post('all_item', TRUE),
                'selected_item' => $this->input->post('selected_item', TRUE),
                'token' => $this->input->post('token', TRUE),
            );
            $data['item_data'] = json_decode($data['item_data'], TRUE);
            if ($data['token'] == $this->session->userdata('CSRF'))
            {
                if ($data['all_item'] == 'true')
                {
                    $query = $this->db->where('username', $this->_username)->get('trash_can');
                    $result = $query->result_array();
                    $result_message = '';
                    $status = '';
                    $i = 0;

                    for (; $i < $query->num_rows(); $i++)
                    {
                        if ($result[$i]['mail_type'] == 'im')
                        {
                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_type' => 'im',
                            ))->get('trash_can');

                            $result = $query->result();
                            $tc_count = $query->num_rows();

                            $query = $this->db->get('incoming_mail');
                            $im_count = $query->num_rows();

                            $j = 0;
                            for (; $j < $tc_count; $j++)
                            {
                                $data['id'][$j] = $result[$j]->id;
                                $result[$j]->id = $im_count + 1;
                                unset($result[$j]->mail_type);
                                unset($result[$j]->last_modified);
                                ++$im_count;
                            }

                            $this->db->insert_batch('incoming_mail', $result);

                            if ($this->db->affected_rows())
                            {
                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_type' => 'im',
                                ))->delete('trash_can');

                                if ($this->db->affected_rows())
                                {
                                    $status = 'success';
                                    $result_message = '<i class="fa fa-check-circle"></i> Semua Surat berhasil dipulihkan';
                                }
                                else
                                {
                                    $status = 'failed';
                                    $result_message = '<i class="fa fa-exclamation-circle"></i> Semua Surat gagal dipulihkan';
                                }
                            }
                            else
                            {
                                $status = 'failed';
                                $result_message = '<i class="fa fa-exclamation-circle"></i> Semua Surat gagal dipulihkan';
                            }
                        }
                        else if ($result[$i]['mail_type'] == 'om')
                        {
                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_type' => 'om',
                            ))->get('trash_can');

                            $result = $query->result();
                            $tc_count = $query->num_rows();

                            $query = $this->db->get('outgoing_mail');
                            $om_count = $query->num_rows();

                            $j = 0;
                            for (; $j < $tc_count; $j++)
                            {
                                $data['id'][$j] = $result[$j]->id;
                                $result[$j]->id = $om_count + 1;
                                unset($result[$j]->mail_type);
                                unset($result[$j]->last_modified);
                                ++$om_count;
                            }

                            $this->db->insert_batch('outgoing_mail', $result);

                            if ($this->db->affected_rows())
                            {
                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_type' => 'om',
                                ))->delete('trash_can');

                                if ($this->db->affected_rows())
                                {
                                    $status = 'success';
                                    $result_message = '<i class="fa fa-check-circle"></i> Semua Surat berhasil dipulihkan';
                                }
                                else
                                {
                                    $status = 'failed';
                                    $result_message = '<i class="fa fa-exclamation-circle"></i> Semua Surat gagal dipulihkan';
                                }
                            }
                            else
                            {
                                $status = 'failed';
                                $result_message = '<i class="fa fa-exclamation-circle"></i> Semua Surat gagal dipulihkan';
                            }
                        }
                    }

                    if ($status == 'success')
                    {
                        // Tulis log
                        $this->activity_log->create_activity_log('recovery_all', ' Surat Keluar Berhasil dipulihkan', null, $this->_username);

                        $min_id = min($data['id']);
                        $max_id = max($data['id']);
                        if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                        {
                            $query = $this->db->get('trash_can');
                            $row_count = $query->num_rows();
                            $target_id = $min_id + 1;
                            $target_break = $row_count + 1;
                            $i = 0;
                            while ($i <= $target_break)
                            {
                                if (!in_array($target_id, $data['id']))
                                {
                                    $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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
                            $query = $this->db->get('trash_can');
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
                                    $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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
                    }

                    header('Content-Type: application/json');
                    echo json_encode(array('status' => $status, 'message' => $result_message));
                }
                else if ($data['selected_item'] == 'true')
                {
                    $result_message = array();
                    $status = 'failed';
                    $activity_data = array();
                    $i = 0;

                    for (; $i < count($data['item_data']['mail_numbers']); $i++)
                    {
                        $query = $this->db->where(array(
                            'username' => $this->_username,
                            'mail_number' => $data['item_data']['mail_numbers'][$i],
                        ))->get('trash_can');

                        $result = $query->result_array();

                        if ($result[0]['mail_type'] == 'im')
                        {
                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_type' => 'im',
                                'mail_number' => $data['item_data']['mail_numbers'][$i],
                            ))->get('trash_can');

                            $result = $query->result();
                            $tc_count = $query->num_rows();

                            $query = $this->db->get('incoming_mail');
                            $im_count = $query->num_rows();

                            $activity_data['sender'][$i] = $result[0]->sender;
                            $activity_data['mail_number'][$i] = $result[0]->mail_number;

                            $data['id'][$i] = $result[0]->id;
                            $result[0]->id = $im_count + 1;
                            unset($result[0]->mail_type);
                            unset($result[0]->last_modified);

                            $this->db->insert('incoming_mail', $result[0]);

                            if ($this->db->affected_rows())
                            {
                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_type' => 'im',
                                    'mail_number' => $data['item_data']['mail_numbers'][$i],
                                ))->delete('trash_can');

                                if ($this->db->affected_rows())
                                {
                                    $status = 'success';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mail_numbers'][$i] . ' berhasil dipulihkan';
                                }
                                else
                                {
                                    $status = 'failed';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mail_numbers'][$i] . ' gagal dipulihkan';
                                }
                            }
                            else
                            {
                                $status = 'failed';
                                $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Masuk ' . $data['item_data']['mail_numbers'][$i] . ' gagal dipulihkan';
                            }
                        }
                        else if ($result[0]['mail_type'] == 'om')
                        {
                            $query = $this->db->where(array(
                                'username' => $this->_username,
                                'mail_type' => 'om',
                                'mail_number' => $data['item_data']['mail_numbers'][$i],
                            ))->get('trash_can');

                            $result = $query->result();
                            $tc_count = $query->num_rows();

                            $query = $this->db->get('outgoing_mail');
                            $om_count = $query->num_rows();

                            $activity_data['sender'][$i] = $result[0]->sender;
                            $activity_data['mail_number'][$i] = $result[0]->mail_number;

                            $data['id'][$i] = $result[0]->id;
                            $result[0]->id = $om_count + 1;
                            unset($result[0]->mail_type);
                            unset($result[0]->last_modified);

                            $this->db->insert('outgoing_mail', $result[0]);

                            if ($this->db->affected_rows())
                            {
                                $this->db->where(array(
                                    'username' => $this->_username,
                                    'mail_type' => 'om',
                                    'mail_number' => $data['item_data']['mail_numbers'][$i],
                                ))->delete('trash_can');

                                if ($this->db->affected_rows())
                                {
                                    $status = 'success';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' berhasil dipulihkan';
                                }
                                else
                                {
                                    $status = 'failed';
                                    $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' gagal dipulihkan';
                                }
                            }
                            else
                            {
                                $status = 'failed';
                                $result_message[$i] = '<i class="fa fa-check-circle"></i> Surat Keluar ' . $data['item_data']['mail_numbers'][$i] . ' gagal dipulihkan';
                            }
                        }
                    }

                    if ($status == 'success')
                    {
                        // Tulis log
                        $this->activity_log->create_activity_log('recovery_wselected', ' Surat Keluar Berhasil dipulihkan', $activity_data, $this->_username);

                        $min_id = min($data['id']);
                        $max_id = max($data['id']);
                        if (in_array($min_id + (count($data['id']) - 1), $data['id']))
                        {
                            $query = $this->db->get('trash_can');
                            $row_count = $query->num_rows();
                            $target_id = $min_id + 1;
                            $target_break = $row_count + 1;
                            $i = 0;
                            while ($i <= $target_break)
                            {
                                if (!in_array($target_id, $data['id']))
                                {
                                    $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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
                            $query = $this->db->get('trash_can');
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
                                    $this->db->where('id', $target_id)->update('trash_can', array('id' => $min_id));
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
                    }

                    $result_message = implode(',', $result_message);
                    header('Content-Type: application/json');
                    echo json_encode(array('status' => $status, 'message' => $result_message));
                }
                else
                {
                    $query = $this->db->where(array(
                        'username' => $this->_username,
                        'mail_number' => $data['item_data']['mail_number'],
                    ))->get('trash_can');

                    $result = $query->result_array();

                    $query = $this->db->get('outgoing_mail');
                    $om_count = $query->num_rows();

                    if ($result[0]['mail_type'] == 'im')
                    {
                        $result[0]['id'] = $om_count + 1;
                        unset($result[0]['mail_type']);
                        unset($result[0]['last_modified']);

                        $this->db->insert('incoming_mail', $result[0]);
                        if ($this->db->affected_rows())
                        {
                            $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $result[0]['mail_number'],
                            ))->delete('trash_can');
                            if ($this->db->affected_rows())
                            {
                                $query = $this->db->get('trash_can');
                                $row_count = $query->num_rows();
                                $i = 0;
                                $first_id = $data['id'] + 1;
                                $target_id = $data['id'];
                                $row_count = $row_count - ($data['id'] - 1);
                                for (; $i < $row_count; $i++)
                                {
                                    $this->db->where('id', $first_id)->update('trash_can', array('id' => $target_id));
                                    ++$first_id;
                                    ++$target_id;
                                }

                                header('Content-Type: application/json');
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => '<i class="fa fa-check-circle"></i> Surat Masuk  berhasil dipulihkan')
                                );
                            }
                        }
                        else
                        {
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => '<i class="fa fa-check-circle"></i> Surat Masuk  gagal dipulihkan')
                            );
                        }
                    }
                    else if ($result[0]['mail_type'] == 'om')
                    {
                        $result[0]['id'] = $om_count + 1;
                        unset($result[0]['mail_type']);
                        unset($result[0]['last_modified']);

                        $this->db->insert('outgoing_mail', $result[0]);
                        if ($this->db->affected_rows())
                        {
                            $this->db->where(array(
                                'username' => $this->_username,
                                'mail_number' => $result[0]['mail_number'],
                            ))->delete('trash_can');
                            if ($this->db->affected_rows())
                            {
                                $query = $this->db->get('trash_can');
                                $row_count = $query->num_rows();
                                $i = 0;
                                $first_id = $data['id'] + 1;
                                $target_id = $data['id'];
                                $row_count = $row_count - ($data['id'] - 1);
                                for (; $i < $row_count; $i++)
                                {
                                    $this->db->where('id', $first_id)->update('trash_can', array('id' => $target_id));
                                    ++$first_id;
                                    ++$target_id;
                                }

                                // Tulis log
                                $this->activity_log->create_activity_log('recovery', ' Surat Keluar Berhasil dipulihkan', $result[0], $this->_username);

                                header('Content-Type: application/json');
                                echo json_encode(array(
                                    'status' => 'success',
                                    'message' => '<i class="fa fa-check-circle"></i> Surat Masuk  berhasil dipulihkan')
                                );
                            }
                        }
                        else
                        {
                            header('Content-Type: application/json');
                            echo json_encode(array(
                                'status' => 'success',
                                'message' => '<i class="fa fa-check-circle"></i> Surat Masuk  gagal dipulihkan')
                            );
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
        else
        {
            redirect('user', 'refresh');
        }
    }

    // ------------------------------------------------------------------------

    public function about_app()
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
         *
         */
        $query = $this->db->where_in('status', array('baru', 'baru-dpss', 'baru-accepted'))->like('username', $this->_username)->get('incoming_mail');

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
            'profile_url' => site_url('assets/images/profile-photo/' . $row[0]->profile_picture),
            'app_settings' => $this->app_settings->get_app_settings()[0],
            'om_auth' => $this->_om_auth,
        );

        $this->load->view('user/about', $data, FALSE);
    }
}