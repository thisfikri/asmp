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
 class ASMP_Securtiy extends CI_Model
 {
    /**
     * Digunakan untuk membuat dan men-generate CSRF Token
     * @since 1.0.0
     * @access public
     * @param string $cmd perintah yang diberikan pada fungsi ini
     * @return string
    */
    public function csrf_token($cmd)
    {
        switch ($cmd) {
            case 'generate':
                // all character which can be in random string
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 32; $i++) {
                    // choosing one character from all characters and adding it to random string
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                // store generated csrf token using sha512 hashing

		        $sess = array(
			        'CSRF' => hash('sha512',time().''.$randomString)
                );
                
                set_cookie('t', $sess['CSRF'], '876000');
                $this->session->set_userdata( $sess );
                break;
            case 'get':
                return $this->session->userdata('CSRF');
                break;
            default:
                log_message('error', 'Command Not Found');
                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param [type] $plain_password
     * @return void
     */
    public function get_hashed_password($plain_password)
    {
        return password_hash($plain_password, PASSWORD_BCRYPT);
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param [type] $plain_password
     * @param [type] $hashed_password
     * @return void
     */
    public function verify_hashed_password($plain_password, $hashed_password)
    {
        return password_verify($plain_password, $hashed_password) ? true : false;
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param boolean $allow
     * @return void
     */
    public function set_flp_code($allow = true)
    {
        if ($this->checker->is_admin())
        {
            $username = $this->session->userdata('admin_login');
        }
        else if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
        }

        $flp_code = random_string('alnum', 8);
        $hashed_flp_code = $this->get_hashed_password($flp_code);

        $this->db->where('username', $username)->update('users', array('flp_code' => $hashed_flp_code));
        if ($this->db->affected_rows())
        {
            log_message('info', 'force logut protection is set!');
            return $flp_code;
        }
        else
        {
            log_message('error', 'force logut protection is not set!');
            return $flp_code;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param boolean $allow
     * @return void
     */
    public function set_lr_code($allow = true)
    {
        if ($this->checker->is_admin())
        {
            $username = $this->session->userdata('admin_login');
        }
        else if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
        }
        $lr_code = random_string('alnum', 60);
        $hashed_lr_code = $this->get_hashed_password($lr_code);

        $this->db->where('username', $username)->update('users', array('flp_code' => $hashed_lr_code));
        if ($this->db->affected_rows())
        {
            log_message('info', 'long recovery code is set!');
            return $lr_code;
        }
        else
        {
            log_message('error', 'long recovery code is not set!');
            return $lr_code;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param boolean $allow
     * @return void
     */
    public function reset_flp_code($allow = true)
    {
        if ($this->checker->is_admin())
        {
            $username = $this->session->userdata('admin_login');
        }
        else if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
        }

        $flp_code = random_string('alnum', 8);
        $hashed_flp_code = $this->get_hashed_password($flp_code);

        $this->db->where('username', $username)->update('users', array('flp_code' => $hashed_flp_code));
        if ($this->db->affected_rows())
        {
            log_message('info', 'force logut protection is reset!');
            return $flp_code;
        }
        else
        {
            log_message('error', 'force logut protection is failed to reset!');
            return $flp_code;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param boolean $allow
     * @return void
     */
    public function reset_lr_code($allow = true)
    {
        if ($this->checker->is_admin())
        {
            $username = $this->session->userdata('admin_login');
        }
        else if ($this->checker->is_user())
        {
            $username = $this->session->userdata('user_login');
        }
        $lr_code = random_string('alnum', 60);
        $hashed_lr_code = $this->get_hashed_password($lr_code);

        $this->db->where('username', $username)->update('users', array('flp_code' => $hashed_lr_code));
        if ($this->db->affected_rows())
        {
            log_message('info', 'long recovery code is reset!');
            return $lr_code;
        }
        else
        {
            log_message('error', 'long recovery code is failed to reset!');
            return $lr_code;
        }
    }
 }
 