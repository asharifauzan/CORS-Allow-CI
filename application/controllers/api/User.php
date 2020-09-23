<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class User extends REST_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('m_users', 'user');
    $this->load->library('form_validation');
  }

  public function login_post() {
    // terima data dari form
    $email    = $this->post('email');
    $password = $this->post('password');

    $error = [];

    if ( empty($email) ) {
      $error['form_email'] = 'Harus diisi';
    }

    if( empty($password) ) {
      $error['form_password'] = 'Harus diisi';
    }

    if ($error) {
      $error['status'] = false;
      $error['message'] = 'Form tidak lengkap';
      $this->response($error, REST_Controller::HTTP_NOT_FOUND);
    }

    // cek apakah user ada pada database
    $user_detail = $this->user->getUser($email);
    if (!$user_detail) {
      $this->response([
        'status' => FALSE,
        'message' => 'Email/Password Salah'
      ], REST_Controller::HTTP_NOT_FOUND);
    }

    // response 404 jika user tidak ada
    $hash_password = $user_detail['password'];
    if ( !password_verify($password, $hash_password) ) {
      $this->response([
          'status' => FALSE,
          'message' => 'Email/Password Salah'
      ], REST_Controller::HTTP_NOT_FOUND);
    }

    // response 200 user berhasil masuk
    $this->response([
      'status' => TRUE,
      'message' => 'berhasil login',
      'user_role' => $user_detail['type']
    ], REST_Controller::HTTP_OK);
  }


}
?>
