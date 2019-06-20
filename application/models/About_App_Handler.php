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
  * About_App_Handler Class
  * 
  * Mendefinisikan fungsi - fungsi keamanan aplikasi ASMP
  *
  * @package ASMP
  * @category Model
  * @author ThisFikri
  */
 class About_App_Handler extends CI_Model
 {
    private const APP_NAME = 'ASMP';
	private const APP_PRETTY_NAME = 'Aplikasi Sistem Menejemen Perkantoran';
	private const APP_VERSION = 1.0;
	private const APP_AUTHOR = 'Fikri Haikal';
	private const APP_DEV_NAME = 'ThisFikri';
	private const APP_PUBLISHER = 'Recodech';
	private const APP_PUBLISHER_EMAIL = 'corecodetech@gmail.com';
	private const APP_DEV_EMAIL = 'ocraineore@gmail.com';

    public function get_version($req = '')
	{
		if ($req === '') {
			$float_num = explode('.', (string) self::APP_VERSION);
			if (count($float_num) == 1) {
				$version = $float_num[0].'.0';
			} else {
				$version = self::APP_VERSION;
			}
			return $version;
		} else if ($req == 'fulltext') {
			$float_num = explode('.', (string) self::APP_VERSION);
			if (count($float_num) == 1) {
				$version = $float_num[0].'.0';
			} else {
				$version = self::APP_VERSION;
			}
			return self::APP_PRETTY_NAME.' V'.$version;
		}
	}

	public function get_copyright()
	{
		return '&copy;'.self::APP_PUBLISHER;
	}

	public function get_other_info($req = 'full')
	{
		switch ($req) {
			case 'full':
				return
				'Application Name:'.self::APP_NAME.
				'/Application Pretty Name:'.self::APP_PRETTY_NAME.
				'/Application Version:'.self::APP_VERSION.
				'/Application Author:'.self::APP_AUTHOR.
				'/Application DevName:'.self::APP_DEV_NAME.
				'/Application Publisher:'.self::APP_PUBLISHER;
				break;

			case 'app_name':
				return self::APP_NAME;
				break;
			
			case 'app_full_name':
				return self::APP_PRETTY_NAME;
				break;

			case 'app_author':
				return self::APP_AUTHOR;
				break;

			case 'app_dev_name':
				return self::APP_DEV_NAME;
				break;

			case 'app_publisher':
				return self::APP_PUBLISHER;
				break;
			case 'app_dev_email':
				return self::APP_DEV_EMAIL;
				break;
			default:
				return 'request or info not found';
				break;
		}
	}
 }