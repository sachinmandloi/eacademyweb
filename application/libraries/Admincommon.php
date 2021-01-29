<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admincommon{ 	
	function __Construct(){
		$this->CI = get_instance();
		$this->CI->load->model('db_model');
	}
	function admin_dashboard($uid){ 
	    $total_student=$this->CI->db_model->countAll('students use index (id)',array('admin_id'=>$uid,'status'=>'1'));
		$total_batch=$this->CI->db_model->countAll('batches use index (id)',array('admin_id'=>$uid));
		$active_batch=$this->CI->db_model->countAll('batches use index (id)',array('admin_id'=>$uid,'status'=>1));
		$inactive_batch=$this->CI->db_model->countAll('batches use index (id)',array('admin_id'=>$uid,'status'=>0));
		$total_question=$this->CI->db_model->countAll('questions use index(id)',array('admin_id'=>$uid));
		$vimp_question=$this->CI->db_model->countAll('questions use index(id)',array('admin_id'=>$uid,'category'=>2));
		$imp_question=$this->CI->db_model->countAll('questions use index(id)',array('admin_id'=>$uid,'category'=>1));
		$total_leave=$this->CI->db_model->countAll('leave_management use index(id)',array('admin_id'=>$uid));
		$student_leave=$this->CI->db_model->countAll('leave_management use index(id)',array('admin_id'=>$uid,'student_id !='=>0));
		$teacher_leave=$this->CI->db_model->countAll('leave_management use index(id)',array('admin_id'=>$uid,'teacher_id !='=>0));
		$present = $this->CI->db_model->countAll('attendance',array('date'=>date('Y-m-d')),'','','','','student_id');
		$data['total_student'] = $total_student;
		$data['total_present'] = $present;
		$data['total_batch'] = $total_batch;	
		$data['active_batch'] = $active_batch;	
		$data['inactive_batch'] = $inactive_batch;	
		$data['total_question'] = $total_question;
		$data['vimp_question'] = $vimp_question;
		$data['imp_question'] = $imp_question;
		$data['total_leave'] = $total_leave;	
		$data['student_leave'] = $student_leave;	
		$data['teacher_leave'] = $teacher_leave;	
		return $data;
	}
}