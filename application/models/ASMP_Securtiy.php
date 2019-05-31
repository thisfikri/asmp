<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SRT_ASMP - (SRoot) Aplikasi Sistem Menejemen Perkantoran
 * 
 * @package SRT_ASMP
 * @author SRoot (Leader)
 * @copyright Copyright (c) 2018, CodeInAlfa <codeismywork01@gmail.com>
 * @link https://github.com/CodeInAlfa
 * @version BETA BUILD 02
 * @since Aplha 1.0.0
 * @license GPL Closed Source
 * 
 * Aplikasi ini dibuat dan dikembangkan untuk dipergunakan dalam hal administrasi perkantoran
 */

 /**
  * ASMP_Security Class
  * 
  * Mendefinisikan fungsi - fungsi keamanan aplikasi ASMP
  *
  * @package SRT_ASMP
  * @category Model
  * @author SRoot
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

    public function get_hashed_password($plain_password)
    {
        return password_hash($plain_password, PASSWORD_BCRYPT);
    }

    public function verify_hashed_password($plain_password, $hashed_password)
    {
        return password_verify($plain_password, $hashed_password) ? true : false;
    }
 }
 