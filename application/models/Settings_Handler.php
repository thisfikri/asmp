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
  * Settings_Handler Class
  * 
  * Mengatasi Settings Aplikasi
  *
  * @package SRT_ASMP
  * @category Model
  * @author SRoot
  */
class Settings_Handler extends CI_Model {

    public function save_user_settings($username, $settings_data)
    {
        if (is_array($settings_data)) {
            if ($this->checker->is_user() || $this->checker->is_admin())
            {
                $this->db->where('username', $username)->update('user_settings', $settings_data);
                if ($this->db->affected_rows())
                {
                    return array('status' => 'success', 'message' => 'save settings success!');
                }
                else
                {
                    return array('status' => 'failed', 'message' => 'save settings failed!');
                }
            }
            else
            {
                $this->checker->login_status();
            } 
        }
        else
        {
            log_message('error', 'settings data must be an array');
            return array('status' => 'failed', 'message' => 'save settings failed!');
        }
    }

    public function get_user_settings($username, $result_dtype = 'o')
    {
            if ($this->checker->is_admin() || $this->checker->is_user())
            {
                $query = $this->db->where('username', $username)->get('user_settings');
                if ($result_dtype == 'o')
                {
                    $result = $query->result();
                    if ($result) {
                        return $result;
                    }
                    else
                    {
                        log_message('error', 'no settings found!');
                        return false;
                    }
                }
                else if ($result_dtype == 'arr')
                {
                    $result = $query->result_array();
                    if ($result) {
                        return $result;
                    }
                    else
                    {
                        log_message('error', 'no settings found!');
                        return false;
                    }
                }
            }
            else
            {
                $this->checker->login_status();
            }
    }

    public function get_user_setting($username, $setting_name)
    {
        if ($this->checker->is_user() || $this->checker->is_admin())
        {
            $settings = $this->get_user_settings($username, 'arr');
            return $setting[0][$setting_name];
        }
    }

    public function save_app_settings($settings)
    {
        if ($this->checker->is_admin())
        {
            $this->db->update('app_settings', $settings);
            if ($this->db->affected_rows())
            {
                return array('status' => 'success', 'message' => 'save settings success!');
            }
            else
            {
                return array('status' => 'failed', 'message' => 'save settings failed!');
            }
        }
        else if ($this->checker->is_user())
        {
            return 'this function only for admin!';
        }
        else
        {
            $this->checker->login_status();
        }
    }

    public function get_app_settings($result_dtype = 'o')
    {
        if ($this->checker->is_admin())
        {
            $query = $this->db->get('app_settings');
            if ($result_dtype === 'o')
            {
                $result = $query->result();
                if ($result) {
                    return $result;
                }
                else
                {
                    log_message('error', 'no settings found!');
                    return false;
                }
            }
            else if ($result_dtype === 'arr')
            {

                $result = $query->result_array();
                if ($result) {
                    return $result;
                }
                else
                {
                    log_message('error', 'no settings found!');
                    return false;
                }
            }
        }
        else if ($this->checker->is_user())
        {
            return 'this function only for admin!';
        }
        else
        {
            $this->checker->login_status();
        }
    }

    public function save_all_setting($username, $settings)
    {
        if ($this->checker->is_user() || $this->checker->is_admin())
        {
            $this->db->update('app_settings', $settings['app_settings']);
            if ($this->db->affected_rows())
            {
                $this->db->update('user_settings', $settings['user_settings']);
                if ($this->db->affected_rows())
                {
                    return array('status' => 'success', 'message' => 'semua pengaturan berhasil disimpan');
                }
                else
                {
                    return array('status' => 'failed', 'message' => 'hanya pengaturan aplikasi saja yang berhasil disimpan');
                }
            }
            else
            {
                return array('status' => 'failed', 'message' => 'semua pengaturan gagal disimpan');
            }
        }
        else
        {
            $this->checker->login_status();
        }
    }
}