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

  public function __construct(){
    parent::__construct();
    $this->load->model('m_users', 'user');
  }

  // menyiapkan token
  public function generateToken($user){
    $date = new DateTime();

    $payload['id']    = $user['id'];
    $payload['name']  = $user['name'];
    $payload['email'] = $user['email'];
    $payload['iat']   = $date->getTimestamp(); //waktu di buat
    $payload['exp']   = $date->getTimestamp() + 3600; //satu jam

    return $token = JWT::encode($payload, $this->_secretkey);
  }

  // authentikasi token untuk method request
  public function authToken(){
    $token = $this->input->get_request_header('Authorization');
    $token = explode(" ", $token);
    $token = $token[1];

    try {
      $decode = JWT::decode($token, $this->_secretkey,array('HS256'));

      // jika email pada token ada pada db
      if ($this->user->getUser($decode->email)) {
        return true;
      }
    } catch (Exception $e) {
      exit('Token expired');
    }
  }
}

?>
