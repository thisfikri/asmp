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
 * Front Class
 *
 * Mengontrol halaman utama.
 *
 * @package ASMP
 * @category Controller
 * @author ThisFikri
 */
class Front extends CI_Controller
{
    /**
     * Mendefinisikan bahasa yang ada
     */
    private $_available_lang = array('indonesia', 'english');

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
            $this->_app_lang = get_cookie('language');
        }

        // Memuat bahasa untuk front page
        $this->lang->load('frontp_lang', $this->_app_lang);

        // Memuat dbforge
        $this->load->dbforge();

        // set default time zone
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
     * index - Mengecek apakah preregister sudah dilakukan atau belum
     * DOC[02] - ASMP Program Documentation
     *
     * @since 1.0
     * @access public
     * @return void
     */
    public function index()
    {
        $this->asmp_security->csrf_token('generate');
        // Mengecek status login
        $this->checker->login_status();

        // Mengecek preregister status
        $this->checker->preregister_status(TRUE);
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     *
     */
    public function set_app_lang()
    {
        // Mengecek preregister status
        $this->checker->preregister_status(TRUE);

        if ($this->input->post() && $this->input->is_ajax_request())
        {
            $idiom = $this->input->post('idiom', TRUE);
            // mengecek jika bahasa tersedia
            if (in_array($idiom, $this->_available_lang))
            {
                // set cookie dan session untuk bahasa
                setcookie('language', $idiom, 86000); /* expire in 1 hour */
                $this->session->set_userdata('language', $idiom);
                if ($this->session->userdata('language') == $idiom)
                {
                    $this->_app_lang = $idiom;
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(array('status' => 'success', 'message' => $this->lang->line('lang_has_set'))));
                }
                else
                {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(array('status' => 'failed', 'message' => $this->lang->line('lang_not_set'))));
                }
            }
            else
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => 'error', 'message' => $this->lang->line('lang_not_avaible'))));
            }
        }
        else
        {
            redirect(site_url(), 'refresh');
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    public function get_app_lang()
    {
        // Mengecek preregister status
        $this->checker->preregister_status(TRUE);

        if ($this->input->post() && $this->input->is_ajax_request())
        {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('lang' => $this->_app_lang)));
        }
        else
        {
            redirect(site_url(), 'refresh');
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    public function get_lang_line($line)
    {
        // Mengecek preregister status
        $this->checker->preregister_status(TRUE);

        if ($this->input->post() && $this->input->is_ajax_request())
        {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('line' => $this->lang->line($line))));
        }
        else
        {
            redirect(site_url(), 'refresh');
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * preregister - Fungsi preregister pada class Front adalah untuk menampilkan halaman registrasi awal,
     * dan membuat table yang dibutuhkan jiga belum dibuat
     * DOC[03] - ASMP Program Documentation
     *
     * @since 1.0
     * @access public
     * @return void
     */
    public function preregister()
    {
        if (!$this->session->has_userdata('pre_user_status'))
        {
            $this->checker->preregister_status(TRUE);
        }
        // Mengecek status login
        $this->checker->login_status();

        /**
         * Varible untuk menyimpan data tentang jenis pesan
         *
         * @var string
         */
        $msg_type = '';

        /**
         * Variable untuk menyimpan pesan
         * @var string
         */
        $msg = '';

        // Memuat dbforge
        $this->load->dbforge();

        /**
         * Field untuk dimasukan kedalam paramater pertama add_field untuk table users
         *
         * @var array
         */
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'auto_increment' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => TRUE,
            ),
            'true_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'password' => array(
                'type' => 'VARCHAR',
                'constraint' => '1024',
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'role' => array(
                'type' => 'VARCHAR',
                'constraint' => '6',
            ),
            'position' => array(
                'type' => 'VARCHAR',
                'constraint' => '55',
                'unique' => TRUE,
            ),
            'recovery_id' => array(
                'type' => 'VARCHAR',
                'constraint' => 70,
            ),
            'profile_picture' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
            ),
            'gallery_dir' => array(
                'type' => 'VARCHAR',
                'constraint' => '28',
                'unique' => TRUE,
            ),
            'logged' => array(
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada.
        // Mengeset field 'id' menjadi PRIMARY KEY
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('users', TRUE);

        /**
         * Field untuk dimasukan kedalam paramater pertama add_field untuk table user_settings
         *
         * @var array
         */
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'auto_increment' => TRUE,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'unique' => TRUE,
            ),
            'paging_status' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ),
            'row_limit' => array(
                'type' => 'INT',
                'constraint' => 30,
                'default' => 10,
            ),
            'app_theme' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'default' => 'default_theme',
            ),
            'multiple_remove_action' => array(
                'type' => 'CHAR',
                'constraint' => '8',
                'default' => 'all',
            ),
            'multiple_recovery_action' => array(
                'type' => 'CHAR',
                'constraint' => '8',
                'default' => 'all',
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada.
        // Mengeset field 'id' menjadi PRIMARY KEY
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('user_settings', TRUE);

        /**
         * Field untuk dimasukan kedalam parameter pertama add_field untuk table app_settings
         *
         * @var array
         */
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '5',
                'auto_increment' => TRUE,
            ),
            'mail_document_heading' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'ASMP.Co.Ltd',
            ),
            'mail_document_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'Jl.default No 99 Bandung',
            ),
            'mail_document_contact' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'Telp.012345678',
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada.
        // Mengeset field 'id' menjadi PRIMARY KEY
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('app_settings', TRUE);

        $query = $this->db->get('app_settings');
        $app_set_count = $query->num_rows();
        if ($app_set_count == 0)
        {
            $this->db->insert('app_settings', array('id' => 1));
            if (!$this->db->affected_rows())
            {
                log_message('error', 'failed to insert app settings');
            }
        }

        /**
         * Field untuk dimasukan kedalam paramater pertama add_field untuk table field_sections
         *
         * @var array
         */
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'auto_increment' => TRUE,
            ),
            'field_section_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => TRUE,
            ),
            'task' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada.
        // Mengeset field 'id' menjadi PRIMARY KEY
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('field_sections', TRUE);

        /**
         * Field untuk dimasukan kedalam paramater pertama add_field untuk table pdf_layouts
         *
         * @var array
         */
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'auto_increment' => TRUE,
            ),
            'layout_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'unique' => TRUE,
            ),
            'layout_data' => array(
                'type' => 'text',
            ),
            'layout_page_setup' => array(
                'type' => 'text',
            ),
            'layout_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => 'nonactive',
            ),
            'last_modified' => array(
                'type' => 'TIMESTAMP',
            ),
        );

        // Menambahkan field dan membuat table jika table tidak ada.
        // Mengeset field 'id' menjadi PRIMARY KEY
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('pdf_layouts', TRUE);

        // Mengambil data row dari table pdf_layout dengan nama 'default'
        $this->db->where('layout_name', 'default')->get('pdf_layouts');

        // Mengecek jika pdf_layout dengan nama 'default' tidak ada, maka data akan dibuat
        if (!$this->db->affected_rows())
        {
            $insert_data = array(
                'layout_name' => 'default',
                'layout_data' => '{"idAndMailType":{"xpos":5,"ypos":5,"font_family":"times","font_size":12,"font_style":"b"},
                "docTitle":{"xpos":77,"ypos":48,"font_family":"times","font_size":14,"font_style":"b"},
                "docAddr":{"xpos":74,"ypos":56,"font_family":"times","font_size":12,"font_style":"b"},
                "docContact":{"xpos":84,"ypos":63,"font_family":"times","font_size":12,"font_style":"b"},
                "line":{"xpos":18,"ypos":73,"x2pos":191,"y2pos":73},
                "docMailNum":{"xpos":18,"ypos":84,"font_family":"times","font_size":12,"font_style":""},
                "docDate":{"xpos":170,"ypos":84,"font_family":"times","font_size":12,"font_style":""},
                "docFor":{"xpos":18,"ypos":104,"font_family":"times","font_size":12,"font_style":""},
                "docSubject":{"xpos":48,"ypos":140,"font_family":"times","font_size":12,"font_style":""},
                "docContents":{"xpos":17,"ypos":155,"w":0,"h":10,"font_family":"times","font_size":12},
                "docSignature":{"fxpos":168,"fypos":195,"sxpos":178,"sypos":215,"thxpos":170,"thypos":223,"font_family":"times","font_size":12,"ftxt_font_style":"","stxt_font_style":"","thtxt_font_style":""}}',
                'layout_page_setup' => '{"orientation":"P","unit":"mm","format":"A4"}',
            );

            // Memasukan data kedalam table pdf_layouts
            $this->db->insert('pdf_layouts', $insert_data);

            // Jika data gagal dimasukan maka pesan dengan tipe error akan di set dan dibuat lognya
            if (!$this->db->affected_rows())
            {
                // Tipe error
                $msg_type = 'error';
                // Pesan error
                $msg = $this->lang->line('cdef_pdf_fail');
                // Log error disimpan di folder logs
                log_message('error', $msg);
            }
            else
            {
                /**
                 * Field untuk dimasukan kedalam paramater pertama add_field untuk table pdf_editor
                 *
                 * @var array
                 */
                $fields = array(
                    'id' => array(
                        'type' => 'BIGINT',
                        'constraint' => 20,
                        'auto_increment' => TRUE,
                    ),
                    'layout_name' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '50',
                        'unique' => TRUE,
                    ),
                    'layout_data' => array(
                        'type' => 'TEXT',
                    ),
                    'layout_page_setup' => array(
                        'type' => 'text',
                    ),
                    'last_modified' => array(
                        'type' => 'TIMESTAMP',
                    ),
                );

                // Menambahkan field dan membuat table jika table tidak ada.
                // Mengeset field 'id' menjadi PRIMARY KEY
                $this->dbforge->add_field($fields);
                $this->dbforge->add_key('id', TRUE);
                $this->dbforge->create_table('pdf_editor', TRUE);

                // Mengambil data row dari table pdf_editor dengan nama 'default'
                $this->db->where('layout_name', 'default')->get('pdf_editor');

                // Mengecek jika pdf_layout dengan nama 'default' tidak ada, maka data akan dibuat
                if (!$this->db->affected_rows())
                {
                    $insert_data = array(
                        'layout_name' => 'default',
                        'layout_data' => '{"idAndMailType":{"xpos":5,"ypos":5,"font_family":"times","font_size":12,"font_style":"b"},
                "docTitle":{"xpos":77,"ypos":48,"font_family":"times","font_size":14,"font_style":"b"},
                "docAddr":{"xpos":74,"ypos":56,"font_family":"times","font_size":12,"font_style":"b"},
                "docContact":{"xpos":84,"ypos":63,"font_family":"times","font_size":12,"font_style":"b"},
                "line":{"xpos":18,"ypos":73,"x2pos":191,"y2pos":73},
                "docMailNum":{"xpos":18,"ypos":84,"font_family":"times","font_size":12,"font_style":""},
                "docDate":{"xpos":170,"ypos":84,"font_family":"times","font_size":12,"font_style":""},
                "docFor":{"xpos":18,"ypos":104,"font_family":"times","font_size":12,"font_style":""},
                "docSubject":{"xpos":48,"ypos":140,"font_family":"times","font_size":12,"font_style":""},
                "docContents":{"xpos":17,"ypos":155,"w":0,"h":10,"font_family":"times","font_size":12},
                "docSignature":{"fxpos":168,"fypos":195,"sxpos":178,"sypos":215,"thxpos":170,"thypos":223,"font_family":"times","font_size":12,"ftxt_font_style":"","stxt_font_style":"","thtxt_font_style":""}}',
                        'layout_page_setup' => '{"orientation":"P","unit":"mm","format":"A4"}',
                    );

                    // Memasukan data kedalam table pdf_editor
                    $this->db->insert('pdf_editor', $insert_data);

                    // Jika data gagal dimasukan maka pesan dengan tipe error akan di set dan dibuat lognya
                    if (!$this->db->affected_rows())
                    {
                        $this->db->delete('pdf_layouts');
                        $this->db->delete('pdf_editor');
                        // Tipe error
                        $msg_type = 'error';
                        // Pesan error
                        $msg = $this->lang->line('cdef_pdf_fail');
                        // Log error disimpan di folder logs
                        log_message('error', $msg);
                    }
                }
            }
        }

        if ($this->session->userdata('pre_user_status') !== NULL)
        {
            // View data
            $data = array(
                'status' => $this->session->userdata('pre_user_status'),
                'title_status' => '',
            );

            $data['title_status'] = $this->lang->line('preregister_' . $data['status']);

            // Memuat View 'preregister_status'
            $this->load->view('pre-register-stat', $data);
        }
        else
        {
            // Memuat view 'preregister'
            $this->load->view('pre-register-page');
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * preregister_user - Fungsi preregister_user pada class Front adalah untuk membuat user dengan hak akses admin jika user dengan hak admin belum ada.
     * DOC[04] - ASMP Program Documentation
     *
     * @since 1.0
     * @access public
     * @return void
     */
    public function preregister_user()
    {
        if ($this->input->post() && $this->input->is_ajax_request())
        {
            // Mengambil data dari table preregister_status
            $query = $this->db->get('preregister_status');

            // Menyimpan hasil dari data di atas menjadi array
            $data = $query->result_array();

            // Mengecek apakah status adalah 'not_registered', jika benar maka perintah dibawahnya akan di eksekusi
            if ($data[0]['status'] === 'not_registered')
            {

                // Mengambil data dari table preregister_status
                $query = $this->db->get('users');

                // Mengambil jumlah dari data dari table di atas
                $uslength = $query->num_rows();

                /**
                 * Variable ini digunakan untuk menyimpan rules untuk set_rules
                 *
                 * @var array
                 */
                $rules = array(
                    array(
                        'field' => 'true_name',
                        'label' => 'Nama Anda',
                        'rules' => 'trim|xss_clean|required|is_unique[users.true_name]|alpha_numeric_spaces',
                    ),
                    array(
                        'field' => 'username',
                        'label' => 'Nama Pengguna',
                        'rules' => 'trim|xss_clean|required|is_unique[users.username]|min_length[6]|max_length[20]|alpha_numeric',
                    ),
                    array(
                        'field' => 'password',
                        'label' => 'Kata Sandi',
                        'rules' => 'trim|xss_clean|required|min_length[8]',
                    ),
                    array(
                        'field' => 'passconfirm',
                        'label' => 'Konfirmasi Kata Sandi',
                        'rules' => 'trim|xss_clean|required|matches[password]',
                    ),
                    array(
                        'field' => 'email',
                        'label' => 'Alamt E-Mail Pengguna',
                        'rules' => 'trim|xss_clean|required|is_unique[users.email]|valid_email',
                    ),
                    array(
                        'field' => 'field_section_name',
                        'label' => 'Nama Bidang/Bagian Anda',
                        'rules' => 'trim|xss_clean|required|is_unique[users.position]',
                    ),
                    array(
                        'field' => 'recovery_id',
                        'label' => 'Recovery ID',
                        'rules' => 'trim|xss_clean|required',
                    ),
                );

                // Mengeset rules/peraturan2 pada form field
                $this->form_validation->set_rules($rules);

                if ($this->form_validation->run() === FALSE)
                {
                    $this->output->set_content_type('application/json')->set_output(json_encode(
                        array(
                            'status' => 'error',
                            'message' => validation_errors('<i class="fa fa-exclamation-circle"></i> ', '|'))
                    ));
                }
                else
                {
                    $recovery_id = explode('-', $this->input->post('recovery_id', TRUE));
                    $recovery_id = implode('', $recovery_id);
                    $img_file_name = random_string('alnum', 8) . '_default-profile.png';

                    $query = $this->db->get('users');
                    $user_count = $query->num_rows();

                    /**
                     * Variabel ini digunakan untuk menyimpan nilai data yang akan dimasukan kedalam table
                     *
                     * @var array
                     */
                    $requested_data = array(
                        'id' => $user_count + 1,
                        'true_name' => $this->input->post('true_name', TRUE),
                        'username' => $this->input->post('username', TRUE),
                        'password' => $this->asmp_security->get_hashed_password($this->input->post('password', TRUE)),
                        'email' => $this->input->post('email', TRUE),
                        'role' => 'admin',
                        'position' => $this->input->post('field_section_name', TRUE),
                        'recovery_id' => $this->asmp_security->get_hashed_password($recovery_id),
                        'profile_picture' => $img_file_name,
                        'gallery_dir' => random_string('alnum', 28),
                    );

                    $gallery_dir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery/' . $requested_data['gallery_dir'];
                    if (is_dir($gallery_dir) === FALSE)
                    {
                        $gallery_dir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery';
                        
                        if (is_dir($gallery_dir) === FALSE)
                        {
                            mkdir($gallery_dir, 0755);
                        }
                        
                        if (is_dir($gallery_dir) !== FALSE)
                        {
                            $gallery_dir .= '/' . $requested_data['gallery_dir'];
                            log_message('error', $gallery_dir);
                            if (mkdir($gallery_dir, 0755))
                            {
                                $filename = dirname($this->input->server('SCRIPT_FILENAME')) . '/assets/images/default-profile.png';
                                $handle = fopen($filename, 'r');
                                if (is_readable($filename))
                                {
                                    $filewr = fread($handle, filesize($filename));
                                    fclose($handle);
                                    $filename = $gallery_dir . '/' . $img_file_name;
                                    $handle = fopen($filename, 'w');
                                    if (is_writable($filename))
                                    {
                                        if (fwrite($handle, $filewr))
                                        {
                                            log_message('info', 'profile photo has moved to gallery');
                                        }
                                        else
                                        {
                                            log_message('error', 'failed to write default-profile.png to ' . $gallery_dir);
                                        }
                                    }
                                    else
                                    {
                                        log_message('error', $filename . ' is not writeable.');
                                    }
                                }
                                else
                                {
                                    log_message('error', $filename . ' is not readable.');
                                }

                                fclose($handle);                                
                            }
                            else
                            {
                                log_message('error', 'failed to make directory ' . $gallery_dir);
                            }
                        }
                        else
                        {
                            log_message('error', 'failed to make directory ' . $gallery_dir);
                        }
                    }
                    else
                    {
                        $gallery_dir .= '/' . $requested_data['gallery_dir'];
                        $filename = base_url('assets/images/default-profile.png');
                        $handle = fopen($filename, 'r');
                        if (is_readable($filename))
                        {
                            $filewr = fread($handle, filesize($filename));
                            fclose($handle);
                            $filename = $gallery_dir . '/' . $img_file_name;
                            $handle = fopen($filename, 'w');
                            if (is_writable($filename))
                            {
                                if (fwrite($handle, $filename))
                                {
                                    log_message('info', 'default-profile.png has moved to gallery');
                                }
                                else
                                {
                                    log_message('error', 'failed to write default-profile.png to ' . $gallery_dir);
                                }
                            }
                            else
                            {
                                log_message('error', $filename . ' is not writeable.');
                            }
                        }
                        else
                        {
                            log_message('error', $filename . ' is not readable.');
                        }
                    }

                    // Mengambil data user dengan role 'admin'
                    $this->db->where('role', $requested_data['role'])->get('users');

                    // Jika user dengan role admin ditemukan
                    if ($this->db->affected_rows())
                    {
                        $this->output->set_content_type('application/json')->set_output(json_encode(
                            array(
                                'status' => 'warning',
                                'message' => $this->lang->line('admin_exists'))
                        ));
                    }
                    else
                    {
                        // Memasukkan data admin baru ke table users
                        $this->db->insert('users', $requested_data);
                        // Jika query sebelumnya berhasil maka kondisi 'if' akan aktif
                        if ($this->db->affected_rows())
                        {
                            // Mengambil data dari 'register_limit'
                            $query = $this->db->get('register_limit');
                            $result = $query->result();

                            /**
                             * Field untuk dimasukan kedalam paramater pertama add_field untuk table user_activity_logs
                             * @var array
                             */
                            $fields = array(
                                'id' => array(
                                    'type' => 'BIGINT',
                                    'constraint' => 255,
                                    'auto_increment' => TRUE,
                                ),
                                'username' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'log' => array(
                                    'type' => 'LONGTEXT',
                                ),
                                'last_modified' => array(
                                    'type' => 'TIMESTAMP',
                                ),
                            );

                            // Menambahkan field dan membuat table jika table tidak ada.
                            // Mengeset field 'id' menjadi PRIMARY KEY
                            $this->dbforge->add_field($fields);
                            $this->dbforge->add_key('id', TRUE);
                            $this->dbforge->create_table('user_activity_logs', TRUE);

                            /**
                             * Field untuk dimasukan kedalam paramater pertama add_field untuk table incoming_mail
                             * @var array
                             */
                            $fields = array(
                                'id' => array(
                                    'type' => 'BIGINT',
                                    'constraint' => 100,
                                    'auto_increment' => TRUE,
                                ),
                                'mail_number' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '50',
                                    'unique' => TRUE,
                                ),
                                'username' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'subject' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '30',
                                ),
                                'sender' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'receiver' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'contents' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '65000',
                                ),
                                'status' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'date' => array(
                                    'type' => 'DATE',
                                ),
                                'pdf_layout' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '40',
                                ),
                                'last_modified' => array(
                                    'type' => 'TIMESTAMP',
                                ),
                            );

                            // Menambahkan field dan membuat table jika table tidak ada.
                            // Mengeset field 'id' menjadi PRIMARY KEY
                            $this->dbforge->add_field($fields);
                            $this->dbforge->add_key('id', TRUE);
                            $this->dbforge->create_table('incoming_mail', TRUE);

                            /**
                             * Field untuk dimasukan kedalam paramater pertama add_field untuk table outgoing_mail
                             * @var array
                             */
                            $fields = array(
                                'id' => array(
                                    'type' => 'BIGINT',
                                    'constraint' => 100,
                                    'auto_increment' => TRUE,
                                ),
                                'mail_number' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '50',
                                    'unique' => TRUE,
                                ),
                                'username' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'subject' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '30',
                                ),
                                'sender' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'receiver' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'contents' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '65000',
                                ),
                                'status' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'date' => array(
                                    'type' => 'DATE',
                                ),
                                'pdf_layout' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '40',
                                ),
                                'last_modified' => array(
                                    'type' => 'TIMESTAMP',
                                ),
                            );

                            // Menambahkan field dan membuat table jika table tidak ada.
                            // Mengeset field 'id' menjadi PRIMARY KEY
                            $this->dbforge->add_field($fields);
                            $this->dbforge->add_key('id', TRUE);
                            $this->dbforge->create_table('outgoing_mail', TRUE);

                            /**
                             * Field untuk dimasukan kedalam paramater pertama add_field untuk table trash_can
                             * @var array
                             */
                            $fields = array(
                                'id' => array(
                                    'type' => 'BIGINT',
                                    'constraint' => 100,
                                    'auto_increment' => TRUE,
                                ),
                                'mail_number' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '50',
                                    'unique' => TRUE,
                                ),
                                'username' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'mail_type' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '30',
                                ),
                                'subject' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '30',
                                ),
                                'sender' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'receiver' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '55',
                                ),
                                'contents' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '65000',
                                ),
                                'status' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '20',
                                ),
                                'date' => array(
                                    'type' => 'DATE',
                                ),
                                'pdf_layout' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => '40',
                                ),
                                'last_modified' => array(
                                    'type' => 'TIMESTAMP',
                                ),
                            );

                            // Menambahkan field dan membuat table jika table tidak ada.
                            // Mengeset field 'id' menjadi PRIMARY KEY
                            $this->dbforge->add_field($fields);
                            $this->dbforge->add_key('id', TRUE);
                            $this->dbforge->create_table('trash_can', TRUE);

                            // Mengubah status preregister dari 'not_registered' menjadi 'registered'
                            $this->db->where(array('status' => 'not_registered'))->update('preregister_status', array('status' => 'registered'));
                            // Memasukkan data pada field_sections
                            $this->db->insert('field_sections', array(
                                'id' => $requested_data['id'],
                                'field_section_name' => $requested_data['position'],
                                'task' => 'leader_accept_lvl3_reply'
                            ));

                            // jika query sebelumnya berhasil
                            if ($this->db->affected_rows())
                            {
                                // Mengurangi jumlah register_limit
                                $this->db->update('register_limit', array('limit' => $result[0]->limit - 1));
                            }

                            $query = $this->db->where('username', $requested_data['username'])->get('users');

                            if ($query->result())
                            {
                                $this->db->insert('user_settings', array(
                                    'id' => $requested_data['id'],
                                    'username' => $requested_data['username']
                                ));

                                $sess = array(
                                    'pre_user_status' => 'success',
                                );

                                $this->session->set_userdata($sess);

                                $this->output->set_content_type('application/json')->set_output(json_encode(
                                    array(
                                        'status' => 'success',
                                        'message' => $this->lang->line('preregister_success'))
                                ));
                            }
                            else
                            {
                                $sess = array(
                                    'pre_user_status' => 'failed',
                                );

                                $this->session->set_userdata($sess);

                                $this->output->set_content_type('application/json')->set_output(json_encode(
                                    array('status' => 'failed',
                                        'message' => $this->lang->line('preregister_fail'))
                                ));
                            }
                        }
                    }
                }
            }
            else if ($data[0]['status'] === 'registered')
            {
                $this->output->set_content_type('application/json')->set_output(json_encode(
                    array(
                        'status' => 'warning',
                        'message' => $this->lang->line('preregister_exists'))
                ));
            }
        }
        else
        {
            redirect('login', 'refresh');
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * [create_new_account description]
     * @return [type] [description]
     */
    public function create_new_account()
    {
        // Mengecek status login
        $this->checker->login_status();

        // Mengecek preregister status
        $this->checker->preregister_status();

        if ($this->session->userdata('title_status') && $this->session->userdata('status'))
        {
            $data = array(
                'title_status' => $this->session->userdata('title_status'),
                'status' => $this->session->userdata('status'),
            );

            $this->load->view('cna-stat-page', $data);
        }
        else
        {
            $query = $this->db->get('register_limit');
            $result = $query->result();
            if ($result[0]->limit == 0)
            {
                redirect('login', 'refresh');
            }
            $query = $this->db->get('users');
            $result = $query->result_array();
            $forbidden_fs = array();
            for ($i = 0; $i < count($result); $i++)
            {
                $forbidden_fs[$i] = $result[$i]['position'];
            }
            $query = $this->db->where_not_in('field_section_name', $forbidden_fs)->get('field_sections');
            $result = $query->result();

            $data = array(
                'field_sections' => $result,
            );

            $this->load->view('cna-page', $data);
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * [register_new_account description]
     * @return [type] [description]
     */
    public function register_new_account()
    {
        if ($this->input->is_ajax_request() && $this->input->post())
        {

            if ($this->input->post('t', TRUE) == $this->session->userdata('CSRF'))
            {

                $form_rules = array(
                    array(
                        'field' => 'username',
                        'label' => 'Nama Pengguna',
                        'rules' => 'trim|xss_clean|required|is_unique[users.username]|alpha_numeric',
                    ),
                    array(
                        'field' => 'true_name',
                        'label' => 'Nama Asli Pengguna',
                        'rules' => 'trim|xss_clean|required|alpha_numeric_spaces',
                    ),
                    array(
                        'field' => 'password',
                        'label' => 'Kata Sandi',
                        'rules' => 'trim|xss_clean|required',
                    ),
                    array(
                        'field' => 'passconfirm',
                        'label' => 'Konfirmasi Kata Sandi',
                        'rules' => 'trim|xss_clean|required|matches[password]',
                    ),
                    array(
                        'field' => 'email',
                        'label' => 'Email Pengguna',
                        'rules' => 'trim|xss_clean|required|valid_email',
                    ),
                    array(
                        'field' => 'field_section',
                        'label' => 'Bidang/Bagian',
                        'rules' => 'trim|xss_clean|required|is_unique[users.position]',
                    ),
                    array(
                        'field' => 'recovery_id',
                        'label' => 'Recovery ID',
                        'rules' => 'trim|xss_clean|required',
                    ),
                );

                $this->form_validation->set_rules($form_rules);

                if ($this->form_validation->run() == FALSE)
                {
                    // Ubah tipe content ke JSON
                    header('Content-Type: application/json');
                    // Mengirim output data ke client
                    echo json_encode(array('status' => 'error', 'message' => validation_errors('<i class="fa fa-exclamation-circle"></i> ', '~')));
                }
                else
                {
                    $input_data = $this->input->post();
                    $query = $this->db->get('users');
                    $row_count = $query->num_rows();
                    $row_count += 1;
                    $img_file_name = random_string('alnum', 8) . '_default-profile.png';
                    unset($input_data['t']);
                    unset($input_data['passconfirm']);
                    $input_data['id'] = $row_count;
                    $input_data['password'] = $this->asmp_security->get_hashed_password($input_data['password']);
                    $input_data['recovery_id'] = explode('-', $input_data['recovery_id']);
                    $input_data['recovery_id'] = implode('', $input_data['recovery_id']);
                    $input_data['recovery_id'] = $this->asmp_security->get_hashed_password($input_data['recovery_id']);
                    $input_data['position'] = $input_data['field_section'];
                    $input_data['role'] = 'user';
                    $input_data['profile_picture'] = $img_file_name;
                    $input_data['gallery_dir'] = random_string('alnum', 28);
                    $gallery_dir = dirname($this->input->server('SCRIPT_FILENAME')) . '/gallery/' . $input_data['gallery_dir'];
                    if (is_dir($gallery_dir) === FALSE)
                    {
                        if (mkdir($gallery_dir, 0755))
                        {
                            $filename = dirname($this->input->server('SCRIPT_FILENAME')) . '/assets/images/default-profile.png';
                            $handle = fopen($filename, 'r');
                            if (is_readable($filename))
                            {
                                $filewr = fread($handle, filesize($filename));
                                fclose($handle);
                                $filename = $gallery_dir . '/' . $img_file_name;
                                $handle = fopen($filename, 'w');
                                if (is_writable($filename))
                                {
                                    if (fwrite($handle, $filewr))
                                    {
                                        log_message('info', 'profile photo has moved to gallery');
                                    }
                                    else
                                    {
                                        log_message('error', 'failed to write default-profile.png to ' . $gallery_dir);
                                    }
                                }
                                else
                                {
                                    log_message('error', $filename . ' is not writeable.');
                                }
                            }
                            else
                            {
                                log_message('error', $filename . ' is not readable.');
                            }

                            fclose($handle);
                        }
                        else
                        {
                            log_message('error', 'failed to make directory ' . $gallery_dir);
                        }
                    }
                    else
                    {
                        $filename = base_url('assets/images/default-profile.png');
                        $handle = fopen($filename, 'r');
                        if (is_readable($filename))
                        {
                            $filewr = fread($handle, filesize($filename));
                            fclose($handle);
                            $filename = $gallery_dir . '/' . $img_file_name;
                            $handle = fopen($filename, 'w');
                            if (is_writable($filename))
                            {
                                if (fwrite($handle, $filename))
                                {
                                    log_message('info', 'default-profile.png has moved to gallery');
                                }
                                else
                                {
                                    log_message('error', 'failed to write default-profile.png to ' . $gallery_dir);
                                }
                            }
                            else
                            {
                                log_message('error', $filename . ' is not writeable.');
                            }
                        }
                        else
                        {
                            log_message('error', $filename . ' is not readable.');
                        }
                    }

                    unset($input_data['field_section']);
                    $this->db->insert('users', $input_data);
                    if ($this->db->affected_rows())
                    {
                        $query = $this->db->get('user_settings');
                        $row_count = $query->num_rows();
                        $row_count += 1;
                        $this->db->insert('user_settings', array('id' => $row_count, 'username' => $input_data['username']));
                        if ($this->db->affected_rows())
                        {
                            $query = $this->db->get('register_limit');
                            $limit = $query->result();
                            $limit = $limit[0]->limit - 1;
                            $this->db->update('register_limit', array('limit' => $limit));
                            if (!$this->db->affected_rows())
                            {
                                log_message('error', 'register limit not decrase');
                            }
                            $s_session = array(
                                'title_status' => 'Registrasi Akun Berhasil!',
                                'status' => 'success',
                            );
                            $this->session->set_userdata($s_session);

                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'success', 'message' => site_url('buat-akun-baru')));
                        }
                        else
                        {
                            log_message('error', 'failed to make settings');
                            $this->db->where('username', $input_data['username'])->delete('users');

                            $s_session = array(
                                'title_status' => 'Registrasi Akun Gagal!',
                                'status' => 'failed',
                            );
                            $this->session->set_userdata($s_session);

                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'failed', 'message' => site_url('buat-akun-baru')));
                        }
                    }
                    else
                    {
                        log_message('error', 'failed to make user account');

                        $s_session = array(
                            'title_status' => 'Registrasi Akun Gagal!',
                            'status' => 'failed',
                        );
                        $this->session->set_userdata($s_session);

                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'failed', 'message' => site_url('buat-akun-baru')));
                    }
                }
            }
            else
            {
                log_message('error', 'token tidak sama');
                // Ubah tipe content ke JSON
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'error', 'message' => 'token tidak sama'));
            }
        }
        else
        {
            redirect(site_url());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------

    /**
     * forgot_password - Menampilkan halaman lupa password, dan memproses reset kata sandi
     * DOC[05] - ASMP Program Documentation
     *
     * @since 1.0 updated at 2.1 > 3.0
     * @access public
     * @return void
     */
    public function forgot_password($method = '')
    {
        // Mengecek status login
        $this->checker->login_status();

        // Mengecek preregister status
        $this->checker->preregister_status();

        /**
         * Digunakan untuk verifikasi reset password
         * @var string
         */
        $sk = $this->input->get('sk');

        /**
         * Metode yang diperbolehkan
         * @var array
         */
        $available_methods = array('email', 'recovery_id');

        /**
         * View data
         * @var array
         */
        $data = array(
            'msg' => '',
            'msg_type' => '',
            'respass_status' => FALSE, // Status reset password
            'failed_option' => FALSE,
        );

        if ($method != NULL && $sk == NULL)
        {
            if (in_array($method, $available_methods))
            {
                if ($method == 'email')
                {
                    if ($this->session->userdata('result_msg') || $this->session->userdata('dialogue_title'))
                    {
                        $data['dialogue_title'] = $this->session->userdata('dialogue_title');
                        $data['result_msg'] = $this->session->userdata('result_msg');
                        $data['failed_option'] = $this->session->userdata('failed_option');
                        $data['method'] = $method;

                        $this->load->view('forgotpw-rm', $data);
                    }
                    else
                    {
                        $this->load->view('forgotpw-email', $data);
                    }
                }
                else if ($method == 'recovery_id')
                {
                    if ($this->session->userdata('result_msg') || $this->session->userdata('dialogue_title'))
                    {
                        $data['dialogue_title'] = $this->session->userdata('dialogue_title');
                        $data['result_msg'] = $this->session->userdata('result_msg');
                        $data['method'] = $method;

                        $this->load->view('forgotpw-rm', $data);
                    }
                    else
                    {
                        $this->load->view('forgotpw-recoveryid', $data);
                    }
                }
            }
            else
            {
                $data['msg'] = '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('fpwm_not_exists');
                $data['msg_type'] = 'error';

                $this->load->view('forgotpw-page', $data);
            }
        }
        else if ($method != NULL && $sk != NULL && $this->session->userdata('secret_key') != NULL)
        {
            if ($this->asmp_security->verify_hashed_password('email', $this->session->userdata('method')))
            {
                if ($sk === $this->session->userdata('secret_key'))
                {
                    $data['respass_status'] = TRUE;
                }
                else
                {
                    $data['respass_status'] = FALSE;
                }

                if ($this->session->userdata('result_msg') || $this->session->userdata('dialogue_title'))
                {
                    $data['dialogue_title'] = $this->session->userdata('dialogue_title');
                    $data['result_msg'] = $this->session->userdata('result_msg');
                    $data['method'] = $method;

                    $this->load->view('forgotpw-rm', $data);
                }
                else
                {
                    $data['dialogue_title'] = $this->session->userdata('dialogue_title');

                    $this->load->view('forgotpw-email', $data);
                }
            }
            else if ($this->asmp_security->verify_hashed_password('recovery_id', $this->session->userdata('method')))
            {
                if ($sk === $this->session->userdata('secret_key'))
                {
                    $data['respass_status'] = TRUE;
                }
                else
                {
                    $data['respass_status'] = FALSE;
                }

                if ($this->session->userdata('result_msg') || $this->session->userdata('dialogue_title'))
                {
                    $data['dialogue_title'] = $this->session->userdata('dialogue_title');
                    $data['result_msg'] = $this->session->userdata('result_msg');
                    $data['method'] = $method;

                    $this->load->view('forgotpw-rm', $data);
                }
                else
                {
                    $data['dialogue_title'] = $this->session->userdata('dialogue_title');

                    $this->load->view('forgotpw-recoveryid', $data);
                }
            }
        }
        else if ($method != NULL && $sk != NULL && $this->session->userdata('secret_key') == NULL)
        {
            redirect('lupa-kata-sandi', 'refresh');
        }
        else
        {
            $this->load->view('forgotpw-page', $data);
        }
    }

    public function verify_forgotpw()
    {
        if ($this->input->post() && $this->input->is_ajax_request())
        {
            /**
             * Metode Reset Password
             * @var string
             */
            $method = $this->input->post('method');

            /**
             * Metode yang diperbolehkan
             * @var array
             */
            $available_methods = array('email', 'recovery_id');

            if (in_array($method, $available_methods))
            {
                if ($method == 'email')
                {
                    /**
                     * Data untuk form_validation rules
                     * @var array
                     */
                    $input_data = array(
                        array(
                            'label' => 'Nama Pengguna',
                            'field' => 'username',
                            'rules' => 'trim|xss_clean|required',
                            'errors' => array(
                                'required' => 'Nama Pengguna Harus Di Isi',
                            ),
                        ),
                        array(
                            'label' => 'E-Mail Pengguna',
                            'field' => 'email',
                            'rules' => 'trim|xss_clean|required|valid_email',
                            'errors' => array(
                                'required' => 'E-Mail Harus Di Isi',
                                'valid_email' => 'E-Mail Tidak Valid',
                            ),
                        ),
                    );

                    // set rules
                    $this->form_validation->set_rules($input_data);

                    // Mengirim email kembali jika request 'resend_email' ditemukan
                    if ($this->input->post('resend_email'))
                    {
                        /**
                         * Digunakan untuk menyimpan username pada resend email
                         * @var string
                         */
                        $username = $this->input->post('username', TRUE);

                        /**
                         * Digunakan untuk menyimpan email pada resend email
                         * @var string
                         */
                        $email = $this->input->post('email', TRUE);

                        /**
                         * Data yang digunakan untuk resend_email
                         * @var array
                         */
                        $resend_data = array(
                            'username' => $username,
                            'secret_key' => random_string('alnum', 30),
                            'method' => $this->asmp_security->get_hashed_password('email'),
                        );

                        $to = $email;

                        $link = base_url('lupa-kata-sandi/email?sk=' . $array['secret_key']);

                        $body = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <title>Reset Password</title>
                            </head>
                            <body>
                            <div class="contents-container">
                            <header class="contens-header">
                                <h2 class="contents-title">Reset Password</h2>
                            <header>
                            <p class="contents-text">
                                Kepada ' . $username . ',<br/>' . '
                                Ini adalah link untuk reset password -> <a href="' . $link . '" class="content-link">' . $link . '</a>
                            </p>
                            </div>
                            </body>
                            </html>
                        ';

                        if ($this->emailhandler->send_email($to, $this->lang->line('reset_password_title'), $body, TRUE))
                        {
                            $this->session->set_userdata($resend_data);
                            $this->session->unset_userdata('prev_input');
                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'success', 'message' => '<i class="fa fa-check-circle"></i> ' . $this->lang->line('email_send_success')));
                        }
                        else
                        {
                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'failed', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('email_send_fail')));
                        }

                        die();
                    }

                    if ($this->form_validation->run() == TRUE)
                    {
                        /**
                         * Digunakan untuk menyimpan username pada lupa kata sandi
                         * @var string
                         */
                        $username = $this->input->post('username', TRUE);

                        /**
                         * Digunakan untuk menyimpan email pada lupa kata sandi
                         * @var string
                         */
                        $email = $this->input->post('email', TRUE);

                        /**
                         * Digunakan untuk verifikasi data yang terdaftar
                         * @var boolean
                         */
                        $matches = FALSE;

                        /**
                         * Query untuk mengambil username dari tabel users
                         * @var object
                         */
                        $query = $this->db->where('username', $username)->get('users');

                        /**
                         * Menyimpan hasil dari Query sebelumnya
                         * @var object
                         */
                        $result = $query->result();

                        // jika hasilnya berjumlah 1
                        if ($query->num_rows() == 1)
                        {
                            if ($result[0]->email == $email)
                            {
                                $matches = TRUE;
                            }
                            else
                            {
                                // Ubah tipe content ke JSON
                                header('Content-Type: application/json');
                                // Mengirim output data ke client
                                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $username . $this->lang->line('not_have_email')));
                            }

                            if ($matches)
                            {
                                $array = array(
                                    'username' => $username,
                                    'secret_key' => random_string('alnum', 30),
                                    'method' => $this->asmp_security->get_hashed_password('email'),
                                    'dialogue_title' => '',
                                    'result_msg' => '',
                                );

                                $to = $email;
                                $link = base_url('lupa-kata-sandi/email?sk=' . $array['secret_key']);

                                $body = '
                                <!DOCTYPE html>
                                <html>
                                <head>
                                <meta charset="utf-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <title>Reset Password</title>
                                </head>
                                <body>
                                <div class="contents-container">
                                <header class="contens-header">
                                    <h2 class="contents-title">Reset Password</h2>
                                <header>
                                <p class="contents-text">
                                    Kepada ' . $username . ',<br/>' . '
                                    Ini adalah link untuk reset password -> <a href="' . $link . '" class="content-link">' . $link . '</a>
                                </p>
                                </div>
                                </body>
                                </html>
                            ';
                                if ($this->emailhandler->send_email($to, $this->lang->line('reset_password_title'), $body, TRUE))
                                {
                                    $array['dialogue_title'] = '<i class="fa fa-check-circle"></i>' . $this->lang->line('request_send_success');
                                    $array['result_msg'] = $this->lang->line('request_ss_msg');

                                    $this->session->set_userdata($array);

                                    // Ubah tipe content ke JSON
                                    header('Content-Type: application/json');
                                    // Mengirim output data ke client
                                    echo json_encode(array('status' => 'success', 'message' => base_url() . 'lupa-kata-sandi/email'));
                                }
                                else
                                {
                                    $fail_sess = array(
                                        'dialogue_title' => '',
                                        'result_msg' => '',
                                        'prev_input' => array(),
                                        'failed_option' => FALSE,
                                    );

                                    $fail_sess['dialogue_title'] = '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('request_send_fail');
                                    $fail_sess['result_msg'] = $this->lang->line('request_sf_msg');
                                    $fail_sess['prev_input'] = $this->input->post();
                                    $fail_sess['failed_option'] = TRUE;
                                    $this->session->unset_userdata('method');
                                    $this->session->set_userdata($fail_sess);

                                    // Ubah tipe content ke JSON
                                    header('Content-Type: application/json');
                                    // Mengirim output data ke client
                                    echo json_encode(array('status' => 'failed', 'message' => base_url() . 'lupa-kata-sandi/email'));
                                }

                            }
                        }
                        else
                        {
                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $username . $this->lang->line('not_registered') . '~'));
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
                else if ($method == 'recovery_id')
                {
                    /**
                     * Data untuk form_validation rules
                     * @var array
                     */
                    $input_data = array(
                        array(
                            'label' => 'Nama Pengguna',
                            'field' => 'username',
                            'rules' => 'trim|xss_clean|required',
                            'errors' => array(
                                'required' => 'Nama Pengguna Harus Di Isi',
                            ),
                        ),
                        array(
                            'label' => 'Recovery ID',
                            'field' => 'recovery_id',
                            'rules' => 'trim|xss_clean|required',
                            'errors' => array(
                                'required' => 'Recovery ID Harus Di Isi',
                            ),
                        ),
                    );

                    $this->form_validation->set_rules($input_data);

                    if ($this->form_validation->run() == TRUE)
                    {
                        /**
                         * Digunakan untuk menyimpan username pada lupa kata sandi
                         * @var string
                         */
                        $username = $this->input->post('username', TRUE);

                        /**
                         * Digunakan untuk menyimpan recovery id pada lupa kata sandi
                         * @var string
                         */
                        $recovery_id = $this->input->post('recovery_id', TRUE);
                        $recovery_id = explode('-', $this->input->post('recovery_id', TRUE));
                        $recovery_id = implode('', $recovery_id);
                        
                        /**
                         * Digunakan untuk verifikasi data yang terdaftar
                         * @var boolean
                         */
                        $matches = FALSE;

                        /**
                         * Query untuk mengambil username dari tabel users
                         * @var object
                         */
                        $query = $this->db->where('username', $username)->get('users');

                        /**
                         * Menyimpan hasil dari Query sebelumnya
                         * @var object
                         */
                        $result = $query->result();

                        // jika hasilnya berjumlah 1
                        if ($query->num_rows() == 1)
                        {
                            if ($this->asmp_security->verify_hashed_password($recovery_id, $result[0]->recovery_id))
                            {
                                $matches = TRUE;

                                if (password_needs_rehash($result[0]->recovery_id, PASSWORD_BCRYPT))
                                {
                                    $this->db->where('username', $username)->update('users', array('password' => $this->asmp_security->get_hashed_password($recovery_id)));

                                    if ($this->db->affected_rows())
                                    {
                                        log_message('info', 'Recovery ID is ReHashed!');
                                    }
                                }
                            }
                            else
                            {
                                // Ubah tipe content ke JSON
                                header('Content-Type: application/json');
                                // Mengirim output data ke client
                                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('rid_not_match') . '~'));
                            }

                        }
                        else
                        {
                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $username . $this->lang->line('not_registered') . '~'));
                        }

                        if ($matches === TRUE)
                        {
                            $array = array(
                                'username' => $username,
                                'secret_key' => random_string('alnum', 30),
                                'method' => $this->asmp_security->get_hashed_password('recovery_id'),

                            );

                            $this->session->set_userdata($array);

                            // Ubah tipe content ke JSON
                            header('Content-Type: application/json');
                            // Mengirim output data ke client
                            echo json_encode(array('status' => 'success', 'message' => base_url() . 'lupa-kata-sandi/recovery_id?sk=' . $array['secret_key']));
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
            }
            else
            {
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('fpwm_not_exists') . '~'));
            }
        }
        else
        {
            redirect('lupa-kata-sandi', 'refresh');
        }
    }

    public function reset_password()
    {
        if ($this->input->post() && $this->input->is_ajax_request())
        {
            if ($this->asmp_security->verify_hashed_password('email', $this->session->userdata('method')))
            {
                /**
                 * Data untuk form_validation rules
                 * @var array
                 */
                $input_data = array(
                    array(
                        'label' => 'Kata Sandi Baru',
                        'field' => 'new_password',
                        'rules' => 'trim|xss_clean|required',
                        'errors' => array(
                            'required' => 'Kata Sandi Baru Harus Di Isi',
                        ),
                    ),
                    array(
                        'label' => 'Konfirmasi Kata Sandi Baru',
                        'field' => 'new_password_confirm',
                        'rules' => 'trim|xss_clean|required|matches[new_password]',
                        'errors' => array(
                            'required' => 'Konfirmasi Kata Sandi Baru Harus Di Isi',
                            'matches' => 'Konfirmasi Kata Sandi Tidak Cocok',
                        ),
                    ),
                );

                $this->form_validation->set_rules($input_data);

                if ($this->form_validation->run() == TRUE)
                {
                    /**
                     * Digunakan untuk menyimpan username pada reset kata sandi
                     * @var string
                     */
                    $username = $this->session->userdata('username');

                    /**
                     * Digunakan untuk menyimpan username pada reset kata sandi
                     * @var string
                     */
                    $new_password = $this->input->post('new_password', TRUE);

                    // memperbaharui kata sandi sebelumnya dengan yang baru
                    $this->db->where('username', $username)->update('users', array('password' => $this->asmp_security->get_hashed_password($new_password)));

                    // jika berhasil
                    if ($this->db->affected_rows())
                    {
                        $unset_data = array('secret_key', 'method');
                        $this->session->unset_userdata($unset_data);

                        /**
                         * Result session
                         * @var array
                         */
                        $r_sess = array(
                            'dialogue_title' => '<i class="fa fa-check-circle"></i> ' . $this->lang->line('reset_password_success'),
                        );

                        $this->session->set_userdata($r_sess);

                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'success', 'message' => base_url() . 'lupa-kata-sandi/email'));
                    }
                    else
                    {
                        $unset_data = array('secret_key', 'method');
                        $this->session->unset_userdata($unset_data);

                        /**
                         * Result session
                         * @var array
                         */
                        $r_sess = array(
                            'dialogue_title' => '<i class="fa fa-check-circle"></i> ' . $this->lang->line('reset_password_fail'),
                            'failed_option' => TRUE,
                        );

                        $this->session->set_userdata($r_sess);

                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'success', 'message' => base_url() . 'lupa-kata-sandi/email'));
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
            else if ($this->asmp_security->verify_hashed_password('recovery_id', $this->session->userdata('method')))
            {
                /**
                 * Data untuk form_validation rules
                 * @var array
                 */
                $input_data = array(
                    array(
                        'label' => 'Kata Sandi Baru',
                        'field' => 'new_password',
                        'rules' => 'trim|xss_clean|required',
                        'errors' => array(
                            'required' => 'Kata Sandi Baru Harus Di Isi',
                        ),
                    ),
                    array(
                        'label' => 'Konfirmasi Kata Sandi Baru',
                        'field' => 'new_password_confirm',
                        'rules' => 'trim|xss_clean|required|matches[new_password]',
                        'errors' => array(
                            'required' => 'Konfirmasi Kata Sandi Baru Harus Di Isi',
                            'matches' => 'Konfirmasi Kata Sandi Tidak Cocok',
                        ),
                    ),
                );

                $this->form_validation->set_rules($input_data);

                if ($this->form_validation->run() == TRUE)
                {
                    /**
                     * Digunakan untuk menyimpan username pada reset kata sandi
                     * @var string
                     */
                    $username = $this->session->userdata('username');

                    /**
                     * Digunakan untuk menyimpan username pada reset kata sandi
                     * @var string
                     */
                    $new_password = $this->input->post('new_password', TRUE);

                    // memperbaharui kata sandi sebelumnya dengan yang baru
                    $this->db->where('username', $username)->update('users', array('password' => $this->asmp_security->get_hashed_password($new_password)));

                    // jika berhasil
                    if ($this->db->affected_rows())
                    {
                        $unset_data = array('username', 'secret_key', 'method');
                        $this->session->unset_userdata($unset_data);

                        /**
                         * Result session
                         * @var array
                         */
                        $r_sess = array(
                            'dialogue_title' => '<i class="fa fa-check-circle"></i> ' . $this->lang->line('reset_password_success'),
                        );

                        $this->session->set_userdata($r_sess);

                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'success', 'message' => base_url() . 'lupa-kata-sandi/recovery_id'));
                    }
                    else
                    {
                        $unset_data = array('username', 'secret_key', 'method');
                        $this->session->unset_userdata($unset_data);

                        /**
                         * Result session
                         * @var array
                         */
                        $r_sess = array(
                            'dialogue_title' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('reset_password_fail'),
                        );

                        $this->session->set_userdata($r_sess);

                        // Ubah tipe content ke JSON
                        header('Content-Type: application/json');
                        // Mengirim output data ke client
                        echo json_encode(array('status' => 'failed', 'message' => base_url() . 'lupa-kata-sandi/recovery_id'));
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
                // Ubah tipe content ke JSON
                header('Content-Type: application/json');
                // Mengirim output data ke client
                echo json_encode(array('status' => 'error', 'message' => '<i class="fa fa-exclamation-circle"></i> ' . $this->lang->line('fpwm_not_valid') . '~'));
            }
        }
        else
        {
            redirect('lupa-kata-sandi', 'refresh');
        }
    }
}

/* End of file Front.php */
