<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Token {

  public function __construct() {
    parent::__construct();
    $this->filterURI();
    parent::authToken();
    $this->load->model('M_users', 'user');
    $this->load->library('form_validation');
    $this->load->helper('file');
  }

  // URI-1 harus mahasiswa | dosen
  public function filterURI() {
    $URI1 = $this->uri->segment(1);
    if ($URI1 == 'mahasiswa' OR $URI1 == 'dosen') {
      return;
    }

    show_404();
  }

  public function index_get($role, $id = null, $course = null) {
    switch ($course) {
      case 'courses':
        $data = $this->user->getUserCourses($id);
        break;
      case null:
      $data = $this->user->getUserByRole($role, $id);
        break;
      default:
        $this->response([
          'status'  => FALSE,
          'message' => 'Method not allowed'
        ], 404);
    }

    // data gagal diambil
    if(!$data) {
      $this->response([
        'status' => FALSE,
        'message' => "Failed to get $role",
      ], 404);
    }

    // mereturn data jika sukses
    $this->response([
      'status' => TRUE,
      'message' => "Success get $role",
      'data' => $data
    ], 200);
  }

  public function index_post($role) {
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
      'id_type'   => $this->user->getIdType($role),
    ];

    // jika query addUser gagal
    if( !$this->user->addUser($data) ) {
      unlink('assets/img'.$data['picture']);
      $this->response([
        'status' => FALSE,
        'message' => "Gagal menambah $role"
      ], 400);
    }

    // sukses menambah user
    $this->response([
      'status' => TRUE,
      'message' => "succesfully added new $role"
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

  public function index_delete($role, $id) {
    // jika data tidak berhasil dihapus
    if( !$this->user->deleteUser($id) ) {
      $this->response([
        'status' => FALSE,
        'message'  => "cannot delete $role"
      ], 400);
    }

    // response data ketika class berhasil dihapus
    $this->response([
      'status' => TRUE,
      'message'  => "$role succesfully deleted"
    ], 200);
  }

  public function update_post($role, $id) {
    // ---KHUSUS DOSEN & ADMIN ---
    // validasi memastikan user yang
    // akan diupdate adalah dirinya
    $user_token = parent::getToken()[1];
    $user       = parent::decodeToken($user_token);
    if ($user->type !== 'admin') {
      if ($user->id !== $id OR $user->type !== $role) {
        $this->response([
          'status' => FALSE,
          'message' => "Gagal update $role"
        ], 400);
      }
    }
    
    // set rules validation
    $this->form_validation->set_rules('name', 'name', 'required');
    $this->form_validation->set_rules('email', 'email', 'required|valid_email');
    $this->form_validation->set_rules('password', 'password', 'required');
    $this->form_validation->set_rules('phone', 'phone', 'required|numeric');
    $this->form_validation->set_rules('gender', 'gender', 'required|numeric');

    // jika form validation tidak terpenuhi
    if( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status'  => false,
        'message' => 'form tidak lengkap',
        'error'   => ['form_error' => $form_error]
      ], 404);
    }

    // form validation berhasil
    // menyimpan data yang ditangkap
    $data = [
      'name'      => $this->post('name'),
      'email'     => $this->post('email'),
      'password'  => password_hash( $this->post('password'), PASSWORD_DEFAULT ),
      'birthday'  => $this->post('birthday'),
      'phone'     => $this->post('phone'),
      'address'   => $this->post('address'),
      'gender'    => $this->post('gender')
    ];

    // jika picture juga diedit
    if($_FILES){
      $data['picture'] = $this->do_upload();
    }

    // jika query updateUser gagal
    if( !$this->user->updateUser($id, $data) ) {
      if ($data['picture']) {
        unlink('assets/img'.$data['picture']);
      }
      $this->response([
        'status' => FALSE,
        'message' => "Gagal update $role"
      ], 400);
    }

    // sukses update user
    $this->response([
      'status' => TRUE,
      'message' => "succesfully update $role"
    ], 200);

  }

}
?>
