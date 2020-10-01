<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Token {

  public function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
  }

  public function login_post() {
    // terima data dari form
    $email    = $this->post('email');
    $password = $this->post('password');

    $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required');

    // form validation false akan merespon input form yang false
    if (!$this->form_validation->run()) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status'  => FALSE,
        'message' => 'form tidak lengkap',
        'error'   => ['form_error' => $form_error]
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

    // menyapkan response json
    $response = [
      'status'  => TRUE,
      'message' => 'berhasil login',
      'data'    => [
                    'name' => $user_detail['name'],
                    'email' => $user_detail['email'],
                    'user_role' => $user_detail['type'],
                    'token' => $token
                  ]
    ];

    // response 200 user berhasil masuk
    $this->response($response, 200);
  }
  
}
?>
