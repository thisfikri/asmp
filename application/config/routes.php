<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

if (isset($_COOKIE['app_language']))
{
    $app_language = $_COOKIE['app_language'];
}
else
{
    $app_language = config_item('language');
}

$route['default_controller'] = 'front';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
if ($app_language == 'indonesia')
{
    $route['registrasi-awal'] = 'front/preregister';
    $route['pre_user'] = 'front/preregister_user';
    $route['login'] = 'auth/index';
    $route['logauth'] = 'auth/login_auth';
    $route['lupa-kata-sandi/(:any)'] = 'front/forgot_password/$1';
    $route['lupa-kata-sandi'] = 'front/forgot_password';
    $route['verifyfpw'] = 'front/verify_forgotpw';
    $route['resetpw'] = 'front/reset_password';
    $route['buat-akun-baru'] = 'front/create_new_account';
    $route['reset-kata-sandi'] = 'front/resetpw';
    $route['regnuser'] = 'front/register_new_account';

    // Admin
    $route['admin/dashboard'] = 'admin/index';
    $route['logout'] = 'admin/logout';
    $route['admin/user-management'] = 'admin/user_management';
    $route['admin/surat-masuk'] = 'admin/incoming_mail';
    $route['admin/pdf-layouts'] = 'admin/PDF_layout_list';
    $route['admin/pdf-editor/(:any)/(:any)'] = 'admin/PDF_editor/$1/$2';
    $route['admin/pdf-editor/view/(:any)/(:any)'] = 'admin/PDF_editor_viewer/$1/$2';
    $route['admin/pdf-layout/view/(:any)/(:any)/(:any)'] = 'admin/PDF_viewer/$1/$2/$3';
    $route['admin/plsc'] = 'admin/pdf_layout_stat_changer';
    $route['admin/rempl'] = 'admin/remove_pdf_layout';
    $route['admin/gedtrdta'] = 'admin/get_editor_data';
    $route['admin/upedtrdta'] = 'admin/update_pdf_e_laydata';
    $route['admin/bidang-bagian'] = 'admin/field_sections';
    // User
    $route['user/dashboard'] = 'user/index';
    $route['logout'] = 'user/logout';
    $route['user/surat-keluar'] = 'user/outgoing_mail';
}
else if ($app_language == 'english')
{
    $route['pre-register-page'] = 'front/preregister';
    $route['pre_user'] = 'front/preregister_user';
    $route['login'] = 'auth/index';
    $route['logauth'] = 'auth/login_auth';
    $route['forgot-password/(:any)'] = 'front/forgot_password/$1';
    $route['forgot-password'] = 'front/forgot_password';
    $route['create-new-account'] = 'front/create_new_account';
    $route['reset-password'] = 'auth/resetpw';
    $route['regnc'] = 'auth/registernc';

    // Admin
    $route['admin/dashboard'] = 'admin/index';
    $route['admin/user-management'] = 'admin/user_management';
    $route['admin/incoming-mail'] = 'admin/incoming_mail';
    $route['admin/bidang-bagian'] = 'admin/field_sections';

    //User
    $route['user/dashboard'] = 'user/dashboard';
}