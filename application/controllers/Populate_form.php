<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Populate_form extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken($admin_access = true);
    $this->load->model(['m_classes', 'm_users']);
  }

  public function addClass_get() {
    $mahasiswa = $this->m_users->getUserByType(3);
    $dosen     = $this->m_users->getUserByType(2);
    $kelas     = $this->m_classes->allClass();
    $matkul    = $this->m_classes->allCourses();

    $this->response([
      'status' => TRUE,
      'message' => 'success',
      'data' => [
        'field' => [
          'mahasiswa' => $mahasiswa,
          'kelas'     => $kelas,
          'matkul'    => $matkul,
          'dosen'     => $dosen,
          'hari'      => array(
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jumat'
          )
        ]
      ]
    ], 200);
  }

}
?>
