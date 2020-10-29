<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken();
    $this->load->model('M_mahasiswa', 'mhs');
    $this->load->library('form_validation');
    $this->load->helper('string');
  }

  public function index_get($id = null) {
    $data = $this->mhs->getMahasiswa($id);

    // data gagal diambil
    if(!$data) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to get mahasiswa',
      ], 404);
    }

    // mereturn data jika sukses
    $this->response([
      'status' => TRUE,
      'message' => 'Success get mahasiswa',
      'data' => $data
    ], 200);
  }

  public function index_post() {
    // set rules validation
    $this->form_validation->set_rules('name', 'Name', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required');
    $this->form_validation->set_rules('phone', 'Phone', 'required|numeric');
    $this->form_validation->set_rules('gender', 'Gender', 'required|numeric');

    // jika form validation tidak terpenuhi
    if( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status'  => FALSE,
        'message' => 'form tidak lengkap',
        'error'   => ['form_error' => $form_error]
      ], 404);
    }

    // FORM VALIDATION BERHASIL
    // menyimpan data yang ditangkap
    $data = [
      'name'      => $this->post('name'),
      'email'     => $this->post('email'),
      'password'  => password_hash( $this->post('password'), PASSWORD_DEFAULT ),
      'birthday'  => $this->post('birthday'),
      'phone'     => $this->post('phone'),
      'address'   => $this->post('address'),
      'gender'    => $this->post('gender'),
      'picture'   => $this->do_upload(),
      'id_type'   => $this->mhs->getIdType(),
    ];

    // jika query addMahasiswa gagal
    if( !$this->mhs->addMahasiswa($data) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'Gagal menambah mahasiswa'
      ], 400);
    }

    // sukses menambah mahasiswa
    $this->response([
      'status' => TRUE,
      'message' => 'succesfully added new mahasiswa'
    ], 200);
  }

  public function do_upload() {
    $file = 'picture';

    $config['upload_path']    = './assets/img';
    $config['allowed_types']  = 'gif|jpg|png';
    $config['encrypt_name']   = TRUE;
    $config['max_size']       = 100;
    $config['max_width']      = 2049;
    $config['max_height']     = 2049;

    $this->load->library('upload', $config);

    // jika foto gagal upload/error
    if(!$this->upload->do_upload($file)) {
      $upload_error = $this->upload->display_errors();
      $this->response([
        'status' => FALSE,
        'message' => $upload_error
      ], 400);
    }

    return $this->upload->data()['file_name'];
  }

  public function index_delete($id) {
    // jika data tidak berhasil dihapus
    if( !$this->mhs->deleteMahasiswa($id) ) {
      $this->response([
        'status' => FALSE,
        'message'  => 'cannot delete mahasiswa'
      ], 400);
    }

    // response data ketika class berhasil dihapus
    $this->response([
      'status' => TRUE,
      'message'  => 'mahasiswa succesfully deleted'
    ], 200);
  }

}
?>
