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
 * Mail_Components_Filter Class
 *
 * Menyaring komponen - komponen surat
 *
 * @package ASMP
 * @category Model
 * @author ThisFikri
 */
class Mail_Components_Filter extends CI_Model
{
    public function validate_mail_number($mail_number)
    {
        $query = $this->db->where('mail_number', $mail_number)->get('incoming_mail');
        $im_count = $query->num_rows();

        $query = $this->db->where('mail_number', $mail_number)->get('outgoing_mail');
        $om_count = $query->num_rows();

        $query = $this->db->where('mail_number', $mail_number)->get('trash_can');
        $tc_count = $query->num_rows();

        if ($im_count === 0)
        {
            if ($om_count === 0)
            {
                if ($tc_count === 0)
                {
                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }

    }
}