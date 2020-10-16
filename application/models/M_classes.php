<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_classes extends CI_Model
{
    private $_tblname = 'class';

    public function getClasses($id = null)
    {
        $this->db->select(['class.id', 'class.className as kelas',
                       'GROUP_CONCAT(users.id) as students_id',
                       'GROUP_CONCAT(users.name) as students_name',
                       'schedules.day as hari',
                       'schedules.id_lecturer as dosen', 'courses.name as mata_kuliah'])
             ->from($this->_tblname)
             ->join('students', 'students.id_class = class.id')
             ->join('users', 'students.id_user = users.id')
             ->join('schedules', 'class.id_schedule = schedules.id')
             ->join('courses', 'schedules.id_courses = courses.code')
             ->group_by('class.id');

        // mengambil class dengan id tertentu
         if ($id) {
           $this->db->where(['class.id' => $id]);
           return $this->db->get()->result_array();
         }

        // mengambil semua class
        return $this->db->get()->result_array();
    }

    public function addSchedule($data)
    {
        $this->db->insert('schedules', $data);
        return $this->db->affected_rows('schedules');
    }

    public function addClass($data)
    {
        return $this->db->insert('class', $data);
    }

    public function lastOfScheduleId()
    {
        // mengambil id dari data yang terakhir diinsert
        $this->db->select('id')
           ->order_by('id', 'desc')
           ->limit(1);
        return $this->db->get('schedules')->row_array();
    }

    public function lastOfClassId()
    {
        // mengambil id dari data yang terakhir diinsert
        $this->db->select('id')
           ->order_by('id', 'desc')
           ->limit(1);
        return $this->db->get($this->_tblname)->row_array();
    }

    public function addStudent($student) {
      return $this->db->insert('students', $student);
    }

    public function allClass()
    {
        return $this->db->get($this->_tblname)->result_array();
    }

    public function allCourses()
    {
        return $this->db->get('courses')->result_array();
    }

    public function deleteClass($id)
    {
        $this->db->delete('schedules', ['id' => $id]);
        return $this->db->affected_rows('schedules');
    }

    public function updateClass($id, $schedule = NULL, $class = NULL, $student)
    {
      $return;

      // ----- UPDATE TABLE schedules -----
      if ($schedule) {
        $day         = $schedule['day'];
        $id_lecturer = $schedule['id_lecturer'];
        $id_courses  = $schedule['id_courses'];

        $sql =
        "UPDATE schedules
        JOIN class ON class.id_schedule = schedules.id
        SET day = '$day', id_courses = $id_courses, id_lecturer = $id_lecturer
        WHERE class.id = $id";

        $this->db->query($sql);
      }

      // ----- UPDATE TABLE class ------
      if ($class) {
        $this->db->update('class', $class, ['id' => $id]);
      }

      // ----- UPDATE TABLE students -----
      if ($student) {
        if( $this->db->get_where('students', ['id_class' => $id])->num_rows() ) {
          $this->db->delete('students', ['id_class' => $id]);
        }

        foreach ($student as $st) {
          $this->db->insert('students', ['id_user' => $st, 'id_class'=>$id]);
        }
      }

      // jika berhasil mengubah class
      return TRUE;
    }
}
