<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends Token {

  public function __construct() {
    parent::__construct();
    $this->roleValidation();
    $this->load->model('m_classes');
    $this->load->library('form_validation');
  }

  public function roleValidation() {
    $uri        = trim($_SERVER['REQUEST_URI'], '/');
    $uri        = trim($_SERVER['REQUEST_URI'], "\\");
    $req_method = $_SERVER['REQUEST_METHOD'];
    $uri_id     = '';

    // mendapatkan id
    while($uri[-1] != '/') {
      $uri_id = $uri[-1] . $uri_id;
      $uri    = substr($uri, 0, -1);
    }

    // validasi id
    if ($uri_id === '0') {
      $uri_id = 0;
    } else {
      $uri_id = intval($uri_id);
      if (!$uri_id) {
        $uri_id = NULL;
      }
    }

    // validasi uri sesuai role
    if($req_method != 'GET' OR $req_method == 'GET' && !$uri_id) {
      parent::authToken($admin_access = true);
    } else {
      parent::authToken();
    }
  }

  public function index_get($id = NULL) {
    // ambil daftar class
    $class = $this->m_classes->getClasses($id);
    // jika class tidak ada
    if(!$class) {
      $this->response([
        'status' => FALSE,
        'message' => 'Class not founded',
        'data' => ($class)
      ], 404);
    }

    // menampilkan student dalam bentuk array
    for ($i=0; $i < count($class); $i++) {
      $students_id   = explode(',', $class[$i]['students_id']);
      $students_name = explode(',', $class[$i]['students_name']);
      $all_students = [];

      for ($j=0; $j < count($students_id); $j++) {
        $all_students[$j] = ['id' => $students_id[$j], 'name' => $students_name[$j]];
      }

      $class[$i]['students'] = $all_students;
      unset($class[$i]['students_id']);
      unset($class[$i]['students_name']);
    }

    // memberikan response sukses dan mengirim daftar kelas
    $this->response([
      'status' => TRUE,
      'message' => 'Success fetch class',
      'data' => ($class)
    ], 400);
  }

  public function index_post() {
    // menangkap form schedule
    $schedule = [
                  'day'         => $this->post('hari'), //schedules.day
                  'id_courses'  => $this->post('mata_kuliah'), //schedules.id_courses
                  'id_lecturer' => $this->post('dosen') //schedules.id_lecturer
                ];


    // menangkap form class
    $class = [
      // 'id_schedule'  => $this->m_classes->lastOfScheduleId()['id'],
      'className'    => $this->post('nama_kelas'),
      'active'       => 1
    ];


    // mengankap form student
    $student = $this->post('mahasiswa');


    // jika class tidak terinsert
    if( !$this->m_classes->addClass($schedule, $class, $student) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'cannot add mahasiswa'
      ], 400);
    }

    // response berhasil jika semua data telah diinsert
    $this->response([
      'status' => TRUE,
      'message'  => 'Class succesfully added'
    ], 200);
  }

  public function index_delete($id) {
    // jika data tidak berhasil dihapus
    if( !$this->m_classes->deleteClass($id) ) {
      $this->response([
        'status' => FALSE,
        'message'  => 'Failed to deleted class'
      ], 400);
    }

    // response data ketika class berhasil dihapus
    $this->response([
      'status' => TRUE,
      'message'  => 'Class succesfully deleted'
    ], 200);
  }

  public function index_put($id) {
    // menangkap form schedules
    $schedule = [
                  'day'         => $this->put('hari'), //schedules.day
                  'id_courses'  => $this->put('mata_kuliah'), //schedules.id_courses
                  'id_lecturer' => $this->put('dosen') //schedules.id_lecturer
                ];

    // menangkap form class
    $class = [
      'className'    => $this->put('nama_kelas'),
      'active'       => $this->put('active')
    ];

    // menangkap form students
    $student = $this->put('mahasiswa');


    // jika class gagal update
    if( !$this->m_classes->updateClass($id, $schedule, $class, $student) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'Failed to updated class'
      ], 400);
    };

    // response berhasil update class
    $this->response([
      'status' => TRUE,
      'message'  => 'Class succesfully updated'
    ], 200);


  }


}
?>
