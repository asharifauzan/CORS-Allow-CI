<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_users extends CI_Model {

  private $_tblname = 'users';

  public function getUser($email) {
    $this->db->select('users.id, users.name, users.email, users.password, user_type.type');
    $this->db->from($this->_tblname);
    $this->db->join('user_type', 'users.id_type = user_type.id');
    $this->db->where(['email' => $email]);
    return $this->db->get()->row_array();
  }

  public function getUserById($id) {
    $this->db->select('users.id, users.name, users.email, users.password, user_type.type')
             ->from($this->_tblname)
             ->join('user_type', 'users.id_type = user_type.id')
             ->where(['users.id' => $id]);
    return $this->db->get()->row_array();
  }

  public function getUserByRole($role, $id) {
    $this->db->select('users.*')
    ->from('users')
    ->join('user_type', 'user_type.id = users.id_type')
    ->where('user_type.type', $role);

    // get all user by role
    if(!$id) {
      return $this->db->get()->result_array();
    }

    // get by id user role
    return $this->db->where('users.id', $id)
                    ->get()->result_array();
  }

  public function getUserCourses($id) {
    // mengambil course yang diikuti mahasiswa
    if ( $this->getUserById($id)['type'] == 'mahasiswa' ) {
      $this->db->select('courses.name as course')
               ->from('students')
               ->join('class', 'students.id_class = class.id')
               ->join('schedules', 'class.id_schedule = schedules.id')
               ->join('courses', 'schedules.id_courses = courses.code')
               ->where(['students.id_user' => $id]);
      return $this->db->get()->result_array();
    }

    // mengambil course yang diajar dosen
    $this->db->select('courses.name as course')
             ->from('schedules')
             ->join('courses', 'schedules.id_courses = courses.code')
             ->where(['schedules.id_lecturer' => $id]);
    return $this->db->get()->result_array();
  }

  public function getIdType($role) {
    return $this->db->select('id')
                    ->get_where('user_type', ['type' => $role])->row_array()['id'];
  }

  public function addUser($data) {
    return $this->db->insert('users', $data);
  }

  public function deleteUser($id) {
    return $this->db->delete('users', ['id' => $id]);
  }

  public function updateUser($id, $data) {
    $this->db->update('users', $data, ['id' => $id]);
    return $this->db->affected_rows();
  }

}
?>
