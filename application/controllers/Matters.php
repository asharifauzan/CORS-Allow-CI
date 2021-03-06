<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Matters extends Token {
  public function __construct() {
    parent::__construct();
    $this->load->library('form_validation');
    $this->load->model('m_matters');
    $this->load->helper('file');
  }

  public function index_get($id = NULL) {
    $data = $this->m_matters->getMatters($id);

    if(!$data) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to get matters'
      ], 400);
    }

    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully get matters',
      'data' => $data
    ], 200);
  }

  public function index_post() {
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('id_schedules', 'ID_Schedules', 'required');

    // jika form validation gagal
    if ( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status' => FALSE,
        'message' => 'form tidak lengkap',
        'error' => ['form_error' => $form_error]
      ], 400);
    }

    // FORM VALIDATION SUKSES
    // mengambil data form
    $data = [
      'title'       => $this->post('title'),
      'description' => $this->post('description'),
      'filename'    => $this->do_upload(),
      'id_schedules' => $this->post('id_schedules')
    ];

    // jika gagal menambah matter
    if( !$this->m_matters->addMatter($data) ){
      unlink('assets/matters/'.$data['filename']);
      $this->response([
        'status' => FALSE,
        'message'=> 'Failed to added matter'
      ], 400);
    }

    // sukses menambah matter
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully added new matter'
    ]);
  }

  public function do_upload() {
    $file = 'filename';

    $config['upload_path']    = './assets/matters';
    $config['allowed_types']  = 'pdf|docx';
    $config['max_size']       = 5000; // 5MB

    $this->load->library('upload', $config);

    // jika file gagal upload/error
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
    // var_dump($id); die();
    // jika gagal delete matter
    if( !$this->m_matters->deleteMatter($id) ){
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to deleted matter'
      ]);
    }

    // sukses delete matter
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully deleted matter'
    ]);
  }

  public function update_post($id) {
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('id_schedules', 'ID_Schedules', 'required');

    // jika form validation gagal
    if ( !$this->form_validation->run() ) {
      $form_error = $this->form_validation->error_array();
      $this->response([
        'status' => FALSE,
        'message' => 'form tidak lengkap',
        'error' => ['form_error' => $form_error]
      ]);
    }

    // FORM VALIDATION BERHASIL
    // mengambil form data
    $data = [
      'title'        => $this->post('title'),
      'description'  => $this->post('description'),
      'id_schedules' => $this->post('id_schedules'),
    ];

    // jika filename ikut dirubah
    if ($_FILES) {
      $data['filename'] = $this->do_upload();
    }

    // jika gagal update matter
    if ( !$this->m_matters->updateMatter($data, $id) ) {
      if($data['filename']) {
        unlink('assets/matters/'.$data['filename']);
      }
      echo $this->db->last_query();
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to update matter'
      ]);
    }

    // berhasil update matter
    $this->response([
      'status' => TRUE,
      'message' => 'Succesfully updated matter'
    ]);
  }
}



?>
