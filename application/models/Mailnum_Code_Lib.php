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
  * Mailnum_Code_Lib Class
  * 
  * Perpustakaan untuk menyimpan, mengkoversi/menerjemahkan kode pada nomor surat dan membuat nomor surat
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
  class Mailnum_Code_Lib extends CI_Model
  {
      /**
       * Undocumented variable
       *
       * @var array
       */
      private $mailnum_codes = array(
          ''
      );

      /**
       * Undocumented function
       *
       * @param array $data
       * @return void
       */
      public function create_mailnum(array $data)
      {
          
      }

      /**
       * Undocumented function
       *
       * @param string $mail_num
       * @return void
       */
      public function translate_mailnum(string $mail_num)
      {
          
      }
  }