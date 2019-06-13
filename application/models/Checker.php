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
  * Mendefinisikan fungsi - fungsi pengecekan aplikasi ASMP
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
 class Checker extends CI_Model
 {
    public function preregister_status($front_page = FALSE)
    {
        $query = $this->db->get('preregister_status');
        $result = $query->result();
        if ($result)
        {
            if ($result[0]->status == 'not_registered' && uri_string() !== 'registrasi-awal')
            {
                redirect('registrasi-awal', 'refresh');
            }
            else if ($result[0]->status == 'registered' && $front_page == TRUE)
            {
                redirect('login', 'refresh');
            }
        }
    }

    public function login_status()
    {
        if ($this->session->userdata('user_login') && $this->session->userdata('CSRF'))
        {
            redirect('user/dashboard', 'refresh');
        }
        else if ($this->session->userdata('admin_login') && $this->session->userdata('CSRF'))
        {
            redirect('admin/dashboard', 'refresh');
        }
    }

    public function is_admin()
    {
        if ($this->session->userdata('admin_login') && $this->session->userdata('CSRF'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function is_user()
    {
        if ($this->session->userdata('user_login') && $this->session->userdata('CSRF'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}