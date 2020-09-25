<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Token {

  public function __construct() {
    parent::__construct();
  }

  public function login_post() {
    // terima data dari form
    $email    = $this->post('email');
    $password = $this->post('password');

    $form_error = [];

    // memeriksa apa form terisi
    if ( empty($email) ) {
      $form_error['email'] = 'Harus diisi';
    }
    if( empty($password) ) {
      $form_error['password'] = 'Harus diisi';
    }

    // jika ada form input yang tidak lengkap
    if ($form_error) {
      $this->response([
        'status' => FALSE,
        'message' => 'form tidak lengkap',
        'form_error' => $form_error
      ], 404);
    }

    //  -------- AKAN DIEKSEKUSI JIKA FORM INPUT LENGKAP ------

    // cek apakah user ada pada database
    $user_detail = $this->user->getUser($email);

    if (!$user_detail) {
      $this->response([
        'status' => FALSE,
        'message' => 'Email/Password Salah'
      ], 404);
    }

    // response 404 jika user tidak ada
    $hash_password = $user_detail['password'];

    if ( !password_verify($password, $hash_password) ) {
      $this->response([
          'status' => FALSE,
          'message' => 'Email/Password Salah',
      ], 404);
    }


    // menyiapkan token
    $token = parent::generateToken($user_detail);

    // response 200 user berhasil masuk
    $this->response([
      'status' => TRUE,
      'message' => 'berhasil login',
      'name' => $user_detail['name'],
      'email' => $user_detail['email'],
      'user_role' => $user_detail['type'],
      'token' => $token
    ], 200);
  }
}
?>
