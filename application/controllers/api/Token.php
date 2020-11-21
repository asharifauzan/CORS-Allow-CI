<?php
// load library rest server
use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

// load library rest server
use \Firebase\JWT\JWT;
require APPPATH . 'libraries/JWT.php';
require APPPATH . 'libraries/BeforeValidException.php';
require APPPATH . 'libraries/ExpiredException.php';
require APPPATH . 'libraries/SignatureInvalidException.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Token extends REST_Controller {

  private $_secretkey = 'elearning';
  private $_token;

  public function __construct(){
    parent::__construct();
    $this->load->model('m_users', 'user');
  }

  public function getToken() {
    return $this->_token;
  }

  public function generateToken($user){
    $date = new DateTime();

    $payload['id']    = $user['id'];
    $payload['name']  = $user['name'];
    $payload['email'] = $user['email'];
    $payload['type']  = $user['type'];
    $payload['iat']   = $date->getTimestamp(); //waktu di buat
    $payload['exp']   = $date->getTimestamp() + 3600 * 24; //24jam

    return $this->_token = JWT::encode($payload, $this->_secretkey);
  }

  public function authToken($admin_access = null){
    // mengambil bearer token
    $this->_token = $this->input->get_request_header('Authorization');
    $this->_token = explode(" ", $this->_token);

    // jika token tidak disertakan
    if (count($this->_token) == 1) {
      $this->response([
        'status' => FALSE,
        'message' => 'Masukkan token',
      ], 401);
    }

    try {

      $decode = $this->decodeToken($this->_token[1]);

      // ---- UNTUK AKSES ADMIN ----
      // jika user_role != admin mencoba masuk halamman admin only
      if ($admin_access === TRUE && $decode->type != 'admin') {
        $this->response([
          'status' => FALSE,
          'message' => 'Akses ditolak, anda bukan admin',
        ], 401);
      }

      // ---- AKSES UMUM LONCAT KESINI ----
      // jika token tidak menemukan user
      if (!$this->user->getUser($decode->email)) {
        $this->response([
          'status' => FALSE,
          'message' => 'Token tidak valid',
        ], 401);
      }

    } catch (Exception $e) {
      exit('Token expired');
    }
  }

  public function decodeToken($token) {
    return JWT::decode($token, $this->_secretkey, array('HS256'));
  }
}

?>
