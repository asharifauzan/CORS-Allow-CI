<?php
require APPPATH . 'controllers/api/Token.php';

defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends Token {

  public function __construct() {
    parent::__construct();
    parent::authToken($admin_access = true);
    $this->load->model('m_classes');
    $this->load->library('form_validation');
  }

  public function index_get($id = null) {
    // mengambil seluruh kelas pada databse
    $class = $this->m_classes->getClasses($id);

    // jika ada class tidak ada
    if(!$class) {
      $this->response([
        'status' => FALSE,
        'message' => 'Class not founded',
        'data' => ($class)
      ], 404);
    }

    // mengolah data $class
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

  public function add_post() {
    // -----MEMBUAT JADWAL PADA TABLE schedules-----
    $schedule = [
                  'day'         => $this->post('hari'), //schedules.day
                  'id_courses'  => $this->post('mata_kuliah'), //schedules.id_courses
                  'id_lecturer' => $this->post('dosen') //schedules.id_lecturer
                ];

    // jika schedule tidak terinsert
    if( !$this->m_classes->addSchedule($schedule) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'cannot add schedule'
      ], 400);
    };


    // -----MEMBUAT KELAS BARU PADA TABLE class YANG MEREFERENSIKAN schedules.id DIATAS-----
    $class = [
      'id_schedule'  => $this->m_classes->lastOfScheduleId()['id'],
      'className'    => $this->post('nama_kelas'),
      'active'       => 1
    ];

    // jika class tidak terinsert
    if( !$this->m_classes->addClass($class) ) {
      $this->response([
        'status' => FALSE,
        'message' => 'cannot add class'
      ], 400);
    };


    // -----MEMBUAT MAHASISWA YANG MENGIKUTI KELAS DIATAS-----
    $mahasiswa = $this->post('mahasiswa');
    $id_class  = $this->m_classes->lastOfClassId()['id'];

    for ($i=0; $i < count($mahasiswa); $i++) {
      $student = ['id_user' => $mahasiswa[$i], 'id_class' => $id_class];

      // jika class tidak terinsert
      if( !$this->m_classes->addStudent($student) ) {
        $this->response([
          'status' => FALSE,
          'message' => 'cannot add mahasiswa'
        ], 400);
      }

    }

    // response berhasil jika semua data telah diinsert
    $this->response([
      'status' => TRUE,
      'message'  => 'class succesfully added'
    ], 200);
  }

  public function delete_delete($id) {
    // jika data tidak berhasil dihapus
    if( !$this->m_classes->deleteClass($id) ) {
      $this->response([
        'status' => FALSE,
        'message'  => 'cannot delete class'
      ], 400);
    }

    // response data ketika class berhasil dihapus
    $this->response([
      'status' => TRUE,
      'message'  => 'class succesfully deleted'
    ], 200);
  }


}
?>
