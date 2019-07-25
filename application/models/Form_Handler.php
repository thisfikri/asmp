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
  * Form_Handler Class
  * 
  * Mendefinisikan fungsi untuk validasi form
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
 class Form_Handler extends CI_Model
 {
     /**
      * Undocumented variable
      *
      * @var array
      */
     private $_avaible_options = array(
         'validate_text' => array('required', 'max_length', 'min_length', 'is_unique', 'xss_clean', 'matches', 'alpha', 'alpha_underscore', 'alpha_numeric'),
         'validate_number' => array('required', 'max_length', 'min_length', 'is_unique', 'xss_clean', 'matches', 'valid_number'),
         'validate_password' => array('required', 'max_length', 'min_length', 'is_unique', 'xss_clean', 'matches', 'pw_strength'),
         'validate_email' => array('required', 'max_length', 'min_length', 'is_unique', 'xss_clean', 'matches', 'valid_email')
     );

    /**
     * Undocumented function
     *
     * @param string $type
     * @param [mix] $data
     * @param array $options
     * @return void
     */
    public function validate_input(string $type, $data, array $options)
    {
        switch($type)
        {
            case 'text':
                return $this->_validate_text($data, $options);
                break;
            case 'number':
                return $this->_validate_number($data, $options);
                break;
            case 'password':
                return $this->_validate_password($data, $options);
                break;
            case 'email':
                return $this->_validate_email($data, $options);
                break;
            default:
                log_message('error', 'type not found!');
                return FALSE;
                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param string $data
     * @param array $options
     * @return void
     */
    private function _validate_text(string $field_label, string $data, array $options)
    {
        $not_valid_msg = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->_avaible_options['validate_text']))
            {
                switch($key)
                {
                    case 'required':
                        if (empty($data))
                        {
                            array_push($not_valid_msg, $field_label . ' harus di isi');
                        }
                        break;
                    case 'min_length':
                        if (!empty($data) && (count_chars($data) < $value))
                        {
                            array_push($not_valid_msg, $field_label . ' minimal ' . $value);
                        }
                        break;
                    case 'max_length':
                        if (!empty($data) && (count_chars($data) > $value))
                        {
                            array_push($not_valid_msg, $field_label . ' maksimal ' . $value);
                        }
                        break;
                    case 'is_unique':
                        if (!empty($data))
                        {
                            if (stripos('.', $value) !== -1)
                            {
                                $tb_field = explode('.', $value);
                                $query = $this->db->where($tb_field[1], $data)->get($tb_field[0]);
                                if ($query->result())
                                {
                                    array_push($not_valid_msg, $field_label . ' sudah dipakai');
                                }
                            }
                        }
                        break;
                    case 'xss_clean':
                        if (!empty($data))
                        {
                            $data = xss_clean($data);
                        }
                        break;
                    case 'matches':
                        if (!empty($data) && !empty($value))
                        {
                            if ($data !== $value)
                            {
                                array_push($not_valid_msg, $field_label . ' harus sama');
                            }
                        }
                        break;
                    case 'alpha':
                        if (!empty($data))
                        {
                            preg_match("/([0-9]|[!\"#$%&'()*+,./:;<=>?@[\]^`{|}~_])/", $data, $matches);
                            if ($matches)
                            {
                                array_push($not_valid_msg, $field_label . ' harus berupa huruf alphabet saja');
                            }
                        }
                        break;
                    case 'alpha_underscore':
                        if (!empty($data))
                        {
                            preg_match("/([0-9]|[!\"#$%&'()*+,./:;<=>?@[\]^`{|}~])/", $data, $matches);
                            if ($matches)
                            {
                                array_push($not_valid_msg, $field_label . ' harus berupa huruf alphabet atau underscore');
                            }
                        }
                        break;
                    case 'alpha_numeric':
                        if (!empty($data))
                        {
                            preg_match("/([!\"#$%&'()*+,./:;<=>?@[\]^`{|}~_])/", $data, $matches);
                            if ($matches)
                            {
                                array_push($not_valid_msg, $field_label . ' harus berupa huruf alphabet atau angka numerik');
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        if (count($not_valid_msg) !== 0)
        {
            return [FALSE, $not_valid_msg];
        }
        else if (count($not_valid_msg) === 0)
        {
            return [TRUE, $not_valid_msg];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param [type] $data
     * @param array $options
     * @return void
     */
    private function _validate_number($data, array $options)
    {
        $not_valid_msg = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->_avaible_options['validate_text']))
            {
                switch($key)
                {
                    case 'required':
                        if (empty($data))
                        {
                            array_push($not_valid_msg, $field_label . ' harus di isi');
                        }
                        break;
                    case 'min_length':
                        if (!empty($data) && (count_chars($data) < $value))
                        {
                            array_push($not_valid_msg, $field_label . ' minimal ' . $value);
                        }
                        break;
                    case 'max_length':
                        if (!empty($data) && (count_chars($data) > $value))
                        {
                            array_push($not_valid_msg, $field_label . ' maksimal ' . $value);
                        }
                        break;
                    case 'is_unique':
                        if (!empty($data))
                        {
                            if (stripos('.', $value) !== -1)
                            {
                                $tb_field = explode('.', $value);
                                $query = $this->db->where($tb_field[1], $data)->get($tb_field[0]);
                                if ($query->result())
                                {
                                    array_push($not_valid_msg, $field_label . ' sudah dipakai');
                                }
                            }
                        }
                        break;
                    case 'xss_clean':
                        if (!empty($data))
                        {
                            $data = xss_clean($data);
                        }
                        break;
                    case 'matches':
                        if (!empty($data) && !empty($value))
                        {
                            if ($data !== $value)
                            {
                                array_push($not_valid_msg, $field_label . ' harus sama');
                            }
                        }
                        break;
                    case 'valid_number':
                        if (!empty($data) && !is_numeric($data))
                        {
                            array_push($not_valid_msg, $field_label . ' harus berisi angka numerik');
                        }
                    default:
                        break;
                }
            }
        }

        if (count($not_valid_msg) !== 0)
        {
            return [FALSE, $not_valid_msg];
        }
        else if (count($not_valid_msg) === 0)
        {
            return [TRUE, $not_valid_msg];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param string $data
     * @param array $options
     * @return void
     */
    private function _validate_password(string $data, array $options)
    {
        $not_valid_msg = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->_avaible_options['validate_text']))
            {
                switch($key)
                {
                    case 'required':
                        if (empty($data))
                        {
                            array_push($not_valid_msg, $field_label . ' harus di isi');
                        }
                        break;
                    case 'min_length':
                        if (!empty($data) && (count_chars($data) < $value))
                        {
                            array_push($not_valid_msg, $field_label . ' minimal ' . $value);
                        }
                        break;
                    case 'max_length':
                        if (!empty($data) && (count_chars($data) > $value))
                        {
                            array_push($not_valid_msg, $field_label . ' maksimal ' . $value);
                        }
                        break;
                    case 'is_unique':
                        if (!empty($data))
                        {
                            if (stripos('.', $value) !== -1)
                            {
                                $tb_field = explode('.', $value);
                                $query = $this->db->where($tb_field[1], $data)->get($tb_field[0]);
                                if ($query->result())
                                {
                                    array_push($not_valid_msg, $field_label . ' sudah dipakai');
                                }
                            }
                        }
                        break;
                    case 'xss_clean':
                        if (!empty($data))
                        {
                            $data = xss_clean($data);
                        }
                        break;
                    case 'matches':
                        if (!empty($data) && !empty($value))
                        {
                            if ($data !== $value)
                            {
                                array_push($not_valid_msg, $field_label . ' harus sama');
                            }
                        }
                        break;
                    case 'pw_strength':
                        if (!empty($data))
                        {
                            if (!$this->_password_strength_validator($data, $value))
                            {
                                array_push($not_valid_msg, $field_label . ' terlalu lemah ' . $field_label . ' harus berupa kombinasi huruf,angka, dan simbol');
                            }
                        }
                    default:
                        break;
                }
            }
        }

        if (count($not_valid_msg) !== 0)
        {
            return [FALSE, $not_valid_msg];
        }
        else if (count($not_valid_msg) === 0)
        {
            return [TRUE, $not_valid_msg];
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param string $password
     * @param integer $str_lvl
     * @return void
     */
    private function _password_strength_validator(string $password, int $str_lvl = 2)
    {
        switch($str_lvl)
        {
            case 1:
                break;
            case 2:
                break;
            case 3:
                break;
            default:
                log_message('error', 'password level not found');
                return FALSE;
                break;
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Undocumented function
     *
     * @param string $data
     * @param array $options
     * @return void
     */
    private function _validate_email(string $data, array $options)
    {
        $not_valid_msg = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->_avaible_options['validate_text']))
            {
                switch($key)
                {
                    case 'required':
                        if (empty($data))
                        {
                            array_push($not_valid_msg, $field_label . ' harus di isi');
                        }
                        break;
                    case 'min_length':
                        if (!empty($data) && (count_chars($data) < $value))
                        {
                            array_push($not_valid_msg, $field_label . ' minimal ' . $value);
                        }
                        break;
                    case 'max_length':
                        if (!empty($data) && (count_chars($data) > $value))
                        {
                            array_push($not_valid_msg, $field_label . ' maksimal ' . $value);
                        }
                        break;
                    case 'is_unique':
                        if (!empty($data))
                        {
                            if (stripos('.', $value) !== -1)
                            {
                                $tb_field = explode('.', $value);
                                $query = $this->db->where($tb_field[1], $data)->get($tb_field[0]);
                                if ($query->result())
                                {
                                    array_push($not_valid_msg, $field_label . ' sudah dipakai');
                                }
                            }
                        }
                        break;
                    case 'xss_clean':
                        if (!empty($data))
                        {
                            $data = xss_clean($data);
                        }
                        break;
                    case 'matches':
                        if (!empty($data) && !empty($value))
                        {
                            if ($data !== $value)
                            {
                                array_push($not_valid_msg, $field_label . ' harus sama');
                            }
                        }
                        break;
                    case 'valid_email':
                        if (!empty($data))
                        {
                            
                        }
                    default:
                        break;
                }
            }
        }

        if (count($not_valid_msg) !== 0)
        {
            return [FALSE, $not_valid_msg];
        }
        else if (count($not_valid_msg) === 0)
        {
            return [TRUE, $not_valid_msg];
        }
    }
 }