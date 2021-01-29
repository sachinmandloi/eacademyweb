<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_profile extends CI_Controller {

	function __construct(){
		parent::__construct();		
		if(!empty($_SESSION['role'])){
	        if($_SESSION['role']=='1'){
	            redirect(base_url('admin/dashboard')); 
	        }else if($_SESSION['role']==3){
	            redirect(base_url('teacher/dashboard')); 
	        }
	    }else{
	        redirect(base_url('login'));
	    }
	    
        $uid = $this->session->userdata('uid');
        $studentData = $this->db_model->select_data('token, brewers_check, status','students  use index (id)',array('id'=>$uid),'1',array('id','desc'));
		if(!empty($studentData)){
    	   if(($studentData[0]['token'] !=1) || ($studentData[0]['status'] !=1) || ($studentData[0]['brewers_check'] !=$_SESSION['brewers_check'])){
        		if($this->session->all_userdata()){
                    $this->session->sess_destroy();
        			redirect(base_url('login'));
        		}
    	   }
		}
	// check select language
		$this->load->helper('language');
		$language = $this->general_settings('language_name');
		if($language=="french"){
			$this->lang->load('french_lang', 'french');
		}else if($language=="arabic"){
			$this->lang->load('arabic_lang', 'arabic');
		}else{
			$this->lang->load('english_lang', 'english');
		}
	}

	function general_settings($key_text=''){
        $data = $this->db_model->select_data('*','general_settings',array('key_text'=>$key_text),1);
        return $data[0]['velue_text'];
    }
	
	public function index()
	{
        $header['title'] = $this->lang->line('ltr_dashboard');
        $admin_id = $this->session->userdata('admin_id');
		$uid = $this->session->userdata('uid');
		$batch_id = $this->session->userdata('batch_id');
		$batch_id_like = '"'.$this->session->userdata('batch_id').'"';
        $data['total_mock_test']=$this->db_model->countAll('exams use index (id)',array('admin_id'=>$admin_id,'status'=>1,'type'=>1,'mock_sheduled_date'=>date('Y-m-d'),'batch_id'=>$batch_id));
        $data['upcoming_mock_test']=$this->db_model->countAll('exams use index (id)',array('admin_id'=>$admin_id,'status'=>1,'type'=>1,'mock_sheduled_date >'=>date('Y-m-d'),'batch_id'=>$batch_id));
        
        
        $data['previous_mock_test']=$this->db_model->countAll('exams use index (id)',array('admin_id'=>$admin_id,'status'=>1,'type'=>1,'mock_sheduled_date <'=>date('Y-m-d'),'batch_id'=>$batch_id));
		$data['total_homework']=$this->db_model->countAll('homeworks use index (id)',array('admin_id'=>$admin_id,'batch_id'=>$batch_id));
		
		$data['today_homework']=$this->db_model->countAll('homeworks use index (id)',array('admin_id'=>$admin_id,'batch_id'=>$batch_id,'date'=>date('Y-m-d')));
		$data['previous_homework']=$this->db_model->countAll('homeworks use index (id)',array('admin_id'=>$admin_id,'batch_id'=>$batch_id,'date <'=>date('Y-m-d')));
		
		$data['total_extra_class']=$this->db_model->countAll('extra_classes use index(id)',array('admin_id'=>$admin_id,'date'=>date('Y-m-d')),'','',array('batch_id',$batch_id_like));
		$data['total_previous_class']=$this->db_model->countAll('extra_classes use index(id)',array('admin_id'=>$admin_id,'date <'=>date('Y-m-d')),'','',array('batch_id',$batch_id_like));
		$data['total_upcoming_class']=$this->db_model->countAll('extra_classes use index(id)',array('admin_id'=>$admin_id,'date >'=>date('Y-m-d')),'','',array('batch_id',$batch_id_like));
		
		$data['total_vacancy']=$this->db_model->countAll('vacancy use index(id)',array('admin_id'=>$admin_id,'last_date >= '=>date('Y-m-d')));
		$data['online_vacancy']=$this->db_model->countAll('vacancy use index(id)',array('admin_id'=>$admin_id,'last_date >= '=>date('Y-m-d'),'mode'=>'Online'));
		$data['offline_vacancy']=$this->db_model->countAll('vacancy use index(id)',array('admin_id'=>$admin_id,'last_date >= '=>date('Y-m-d'),'mode'=>'Offline'));
	    
	    $table_name = 'mock_result';
        $cond1=array("admin_id"=>$this->session->userdata('admin_id'),'type'=>1,'batch_id'=>$this->session->userdata('batch_id'));
        $exam_Data = $this->db_model->select_data('*', 'exams use index (id)',$cond1,'',array('id','asc'));
        
        $dataarray =array();
        if($exam_Data){
            
           foreach($exam_Data as $exams){
               
            $cond['paper_id'] = $exams['id'];  
            $cond['student_id'] =$uid;  
            $result_data = $this->db_model->select_data('*', $table_name.' use index (id)',$cond,'',array('id','desc'));
           
            if(!empty($result_data)){
                $count = "";
                foreach($result_data as $rkey=>$result){
    
                    $attemptedQuestion = json_decode($result['question_answer'],true);
                    if(!empty($result['question_answer'])){
                        $question_ids = implode(',',array_keys($attemptedQuestion));
                        if(!empty($question_ids)){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)','id in ('.$question_ids.')');
                        }else{
                            $right_ansrs = array();
                        }
                        
                        $rightCount = 0;
                        $wrongCount = 0;
                        $c = 0;
                        foreach($attemptedQuestion as $key=>$value){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)',array('id'=>$key));
                            if(($key == $right_ansrs[0]['id']) && ($value == $right_ansrs[0]['answer'])){
                                $rightCount++;
                            }else{
                                $wrongCount++;
                            }
                          
                        }
        
                        $percentage = (($rightCount - ($wrongCount*0.25))*100)/$result['total_question'];
        
                        $time_taken = '';
                        if($result['start_time']!="" || $result['submit_time']!=""){
                            $stime=strtotime($result['start_time']);
                            $etime=strtotime($result['submit_time']);
                            $elapsed = $etime - $stime;
                            $time_taken = gmdate("H:i", $elapsed);
                        }
                     
                        $dataarray[] = array(
                            'id'=>$result['id'],
                            'paper_id'=>$exams['id'],
                            'paper_name'=>$result['paper_name'],
                            'date'=>date('d-m-Y',strtotime($result['date'])),
                            'start_time'=>date('h:i A',strtotime($result['start_time'])),
                            'submit_time'=>date('h:i A',strtotime($result['submit_time'])),
                            'total_question'=>$result['total_question'],
                            'time_duration'=>gmdate("H:i", $result['time_duration']*60),
                            'attempted_question'=>$result['attempted_question'],
                            'question_answer'=>json_encode($attemptedQuestion),
                            'percentage'=>number_format((float)$percentage, 2, '.', ''),
                            'added_on'=>$result['added_on']
                           
                        ); 
                        
                        $count++;
                    }
                }
            }
           }
           }
        $data['mock_result'] =$dataarray;
        $exam = $this->db_model->select_data('id,name','exams  use index (id)',array('batch_id'=>$batch_id,'type'=>1,'mock_sheduled_date <='=>date('Y-m-d')),'1',array('id','desc'));
        if(!empty($exam)){
            $data['top_three'] = $this->db_model->select_data('*','mock_result  use index (id)',array('paper_id'=>$exam[0]['id'],'mock_result.percentage >'=>0),'3',array('mock_result.percentage','desc'),'',array('students','students.id=mock_result.student_id'));
        }else{
            $data['top_three']='';
        }
       
			$month = date('m');
			$year = date('Y');
		
		$like = $year.'-'.$month.'-';
	    $data['month'] = $month;
		$data['year'] = $year;
		
		$data['paper_list'] = $this->db_model->select_data('id,name,time_duration,total_question,mock_sheduled_date,mock_sheduled_time','exams use index (id)',array('type'=>1,'admin_id'=>$this->session->userdata('admin_id'),'status'=>1,'batch_id'=>$this->session->userdata('batch_id'),'mock_sheduled_date >='=>date('Y-m-d')),'1',array('mock_sheduled_time','desc'));
		$this->load->view('common/student_header',$header);
		$this->load->view('student/dashboard',$data);
		$this->load->view('common/student_footer');
    }

    function profile(){
		$header['title'] = $this->lang->line('ltr_profile');
		$this->load->view('common/student_header',$header);
		$this->load->view('teacher/profile');
		$this->load->view('common/student_footer');
    }

    function extra_classes(){
		$header['title']=$this->lang->line('ltr_extra_classes');
		$admin_id = $this->session->userdata('admin_id');
		$batch_id_like = '"'.$this->session->userdata('batch_id').'"';
		$data['todayClass']=$this->db_model->select_data('extra_classes.*,users.name,users.teach_gender','extra_classes use index(id)',array('extra_classes.admin_id'=>$admin_id,'extra_classes.date >= '=>date('Y-m-d')),'',array('date','asc'),array('batch_id',$batch_id_like),array('users','users.id = extra_classes.teacher_id'));
		
		 $like = array('batch_id','"'.$this->session->userdata('batch_id').'"');
		 $data['eclass_data'] = $this->db_model->select_data('batch_id','extra_classes  use index (id)',array('admin_id'=>$admin_id),'',array('id','desc'),$like);

		$this->load->view("common/student_header",$header);
		$this->load->view("student/extra_classes",$data); 
		$this->load->view("common/student_footer");
	}

	function homework(){
		$header['title']=$this->lang->line('ltr_homework');
		$admin_id = $this->session->userdata('admin_id');
		
		$data['todayHW']=$this->db_model->select_data('homeworks.description,users.name,users.teach_gender,subjects.subject_name','homeworks use index(id)',array('homeworks.admin_id'=>$admin_id,'homeworks.date'=>date('Y-m-d'),'homeworks.batch_id'=>$this->session->userdata('batch_id')),'','','',array('multiple',array(array('users','users.id = homeworks.teacher_id'),array('subjects','subjects.id = homeworks.subject_id'))));
		$data['homeworks_data'] = $this->db_model->select_data('*','homeworks  use index (id)',array('admin_id'=>$admin_id,'batch_id'=>$this->session->userdata('batch_id')),'',array('id','desc'));
		$this->load->view("common/student_header",$header);
		$this->load->view("student/homework",$data); 
		$this->load->view("common/student_footer");
    }
    function start_class($id){
        $data =array();
		$livedata =$this->db_model->select_data('*','live_class_setting',array('batch' =>$id));
		$date = date('Y-m-d');
		$time_s = date('h:i:s A');
		$subCon = "batch_id = '$id' AND date ='$date' AND end_time =''";
		$livedata_his =$this->db_model->select_data('*','live_class_history',$subCon,1,array('id','desc'));
	    
	
		if(!empty($livedata) && !empty($livedata_his)){
    		$data['signature'] = $this->generate_signature($livedata[0]['zoom_api_key'], $livedata[0]['zoom_api_secret'],$livedata[0]['meeting_number'],0);
    		$data['api_key']=$livedata[0]['zoom_api_key'];
    		$data['display_name']=$this->session->userdata('name');
    		$data['meeting_number']=$livedata[0]['meeting_number'];
    		$data['password']=$livedata[0]['password'];
    		$this->load->view("student/start_live_class",$data);
        }else{
           
            $header['title']=$this->lang->line('ltr_live_class');
            $this->load->view("common/student_header",$header);
    		$this->load->view("student/no_live_class"); 
    		$this->load->view("common/student_footer");
        }
		
	}
	function generate_signature ( $api_key, $api_sercet, $meeting_number, $role){
		$time = time() * 1000; //time in milliseconds (or close enough)
		$data = base64_encode($api_key . $meeting_number . $time . $role);
		$hash = hash_hmac('sha256', $data, $api_sercet, true);
		$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
		return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
	}
    function video_lecture(){
		$header['title']=$this->lang->line('ltr_video_lectures');
	//	$this->session->userdata('batch_id')
        $data['subject'] = $this->db_model->select_data('subjects.id,subjects.subject_name','subjects use index (id)',array('subjects.admin_id'=>$this->session->userdata('admin_id'),'batch_subjects.batch_id'=>$this->session->userdata('batch_id')),'',array('subjects.id','desc'),'',array('batch_subjects','batch_subjects.subject_id=subjects.id'));
        $like = array('batch','"'.$this->session->userdata('batch_id').'"');
		$data['video_data'] = $this->db_model->select_data('batch','video_lectures  use index (id)',array('admin_id'=>$this->session->userdata('admin_id')),'',array('id','desc'),$like);
		$this->load->view("common/student_header",$header);
		$this->load->view("admin/video_manage",$data); 
		$this->load->view("common/student_footer");
    }
    
    function vacancy(){
		$header['title']= $this->lang->line('ltr_upcoming_exams');
		$this->load->view("common/student_header",$header);
		$data['vacancy_data'] = $this->db_model->select_data('id','vacancy  use index (id)',array('admin_id'=>$this->session->userdata('admin_id')),'',array('id','desc'));
		$this->load->view("admin/vacancy_manage",$data); 
		$this->load->view("common/student_footer");
	}
    
    function notice(){
		$header['title'] = $this->lang->line('ltr_notice');
		$admin_id = $this->session->userdata('admin_id');
		$uid = $this->session->userdata('uid');
		$this->db_model->update_data('notices use index(id)',array('read_status'=>1),array('student_id'=>$this->session->userdata('uid')));
        $subCon = "admin_id = '$admin_id' AND notice_for in ('Both','Student') || student_id ='$uid'";
		$data['notice_data'] =$this->db_model->select_data('*','notices',$subCon,1,array('id','desc'));
		$this->load->view('common/student_header',$header);
		$this->load->view('student/notice',$data);
		$this->load->view('common/student_footer');
	}

	function answer_sheet($paper_type='',$result_id='',$paper_id){
		$header['title']=$this->lang->line('ltr_answer_sheet');
		if($paper_type == 'mock'){
			$type = 1;
			$table = 'mock_result';
		}else{
			$type = 2;
			$table = 'practice_result';
		}
		if($result_id!=0){
		    $data['result_details'] = $this->db_model->select_data("$table.*,exams.question_ids",$table.' use index (id)',array("$table.id"=>$result_id),1,'','',array('exams',"exams.id = $table.paper_id"));
		}else{
		    $data['result_details'] =$this->db_model->select_data("*",'exams use index (id)',array("id"=>$paper_id),1,'','');
		}
		
		$this->load->view("common/student_header",$header);
		$this->load->view("student/answer_sheet",$data); 
		$this->load->view("common/student_footer");
    }
    
    function practice_paper(){
		$header['title']=$this->lang->line('ltr_practice_paper');
		$data['paper_list'] = $this->db_model->select_data('id,name,time_duration,total_question','exams use index (id)',array('type'=>2,'admin_id'=>$this->session->userdata('admin_id'),'status'=>1,'batch_id'=>$this->session->userdata('batch_id')),'',array('id','desc'));
		$data['practice_data'] = $this->db_model->select_data('id','exams  use index (id)',array('admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id'),'type'=>2),'',array('id','desc'));
		$data['paper_type'] = 'practice';
		$this->load->view("common/student_header",$header);
		$this->load->view("student/show_paper",$data); 
		$this->load->view("common/student_footer");
    } 
    
    function mock_paper(){
		$header['title']=$this->lang->line('ltr_mock_paper');
		$data['paper_list'] = $this->db_model->select_data('id,name,time_duration,total_question,mock_sheduled_date,mock_sheduled_time','exams use index (id)',array('type'=>1,'admin_id'=>$this->session->userdata('admin_id'),'status'=>1,'batch_id'=>$this->session->userdata('batch_id'),'mock_sheduled_date >='=>date('Y-m-d')),'',array('id','desc'));
		$data['mock_data'] = $this->db_model->select_data('id','exams  use index (id)',array('admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id'),'type'=>1),'',array('id','desc'));
		$data['paper_type'] = 'mock';
		$this->load->view("common/student_header",$header);
		$this->load->view("student/show_paper",$data); 
		$this->load->view("common/student_footer");
	}
 
	function practice_result(){
		$header['title']=$this->lang->line('ltr_practice_result');
		$data['paperList'] = $this->db_model->select_data('id,name','exams use index (id)',array('type'=>2,'admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id')),'',array('id','desc'));
		$data['papeResult'] = $this->db_model->select_data('id','practice_result use index (id)',array('student_id'=>$this->session->userdata('uid'),'admin_id'=>$this->session->userdata('admin_id')),1,array('id','desc'));
		$this->load->view("common/student_header",$header);
		$this->load->view("student/practice_result",$data); 
		$this->load->view("common/student_footer");
	}

	function mock_result(){
		$header['title']=$this->lang->line('ltr_mock_test_result');
		$data['paperList'] = $this->db_model->select_data('id,name','exams use index (id)',array('type'=>1,'admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id')),'',array('id','desc'));
		$this->load->view("common/student_header",$header);
		$this->load->view("student/mock_result",$data); 
		$this->load->view("common/student_footer");
	}
	
	function apply_leave(){
		$header['title']=$this->lang->line('ltr_apply_leave');
		$data = array();
		$data['leave_data'] = $this->db_model->select_data('id','leave_management use index (id)',array('admin_id'=>$this->session->userdata('admin_id'),'student_id'=>$this->session->userdata('uid')),1);
		$this->load->view("common/student_header",$header);
		$this->load->view("teacher/apply_leave",$data); 
		$this->load->view("common/student_footer");
	}

	function view_progress(){
		$uid = $this->session->userdata('uid');
		if(isset($_POST['filter_performance'])){
			$month = $_POST['month']; 
			$year = $_POST['year'];	
		}else{ 	
			$month = date('m');
			$year = date('Y');
		}
		$header['title']=$this->lang->line('ltr_progress');
		$like = $year.'-'.$month.'-';
		
	
		$table_name = 'practice_result';
		$cond1=array('type'=>2,'batch_id'=>$this->session->userdata('batch_id'));
		$exam_Datap = $this->db_model->select_data('*', 'exams use index (id)',$cond1,'',array('id','asc'));
		
		$dataarray_pre =array();
        if($exam_Datap){
            
           foreach($exam_Datap as $exams){
               
            $cond['paper_id'] = $exams['id'];  
            $cond['student_id'] =$uid;  
            $result_data = $this->db_model->select_data('*', $table_name.' use index (id)',$cond,'',array('id','asc'),array('date',$like));
           
            if(!empty($result_data)){
                $count = "";
                foreach($result_data as $rkey=>$result){
    
                    $attemptedQuestion = json_decode($result['question_answer'],true);
                    if(!empty($result['question_answer'])){
                        $question_ids = implode(',',array_keys($attemptedQuestion));
                        if(!empty($question_ids)){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)','id in ('.$question_ids.')');
                        }else{
                            $right_ansrs = array();
                        }
                        
                        $rightCount = 0;
                        $wrongCount = 0;
                        $c = 0;
                        foreach($attemptedQuestion as $key=>$value){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)',array('id'=>$key));
                            if(($key == $right_ansrs[0]['id']) && ($value == $right_ansrs[0]['answer'])){
                                $rightCount++;
                            }else{
                                $wrongCount++;
                            }
                          
                        }
        
                        $percentage = (($rightCount - ($wrongCount*0.25))*100)/$result['total_question'];
        
                        $time_taken = '';
                        if($result['start_time']!="" || $result['submit_time']!=""){
                            $stime=strtotime($result['start_time']);
                            $etime=strtotime($result['submit_time']);
                            $elapsed = $etime - $stime;
                            $time_taken = gmdate("H:i", $elapsed);
                        }
                     
                        $dataarray_pre[] = array(
                            'id'=>$result['id'],
                            'paper_id'=>$exams['id'],
                            'paper_name'=>$result['paper_name'],
                            'date'=>date('d-m-Y',strtotime($result['date'])),
                            'start_time'=>date('h:i A',strtotime($result['start_time'])),
                            'submit_time'=>date('h:i A',strtotime($result['submit_time'])),
                            'total_question'=>$result['total_question'],
                            'time_duration'=>gmdate("H:i", $result['time_duration']*60),
                            'attempted_question'=>$result['attempted_question'],
                            'question_answer'=>json_encode($attemptedQuestion),
                            'percentage'=>number_format((float)$percentage, 2, '.', ''),
                            'added_on'=>$result['added_on']
                           
                        ); 
                        
                        $count++;
                    }
                }
            }
           }
           }
           
        $data['practice_result'] =$dataarray_pre;
		
		
		
		$table_name = 'mock_result';
        $cond1=array("admin_id"=>$this->session->userdata('admin_id'),'type'=>1,'batch_id'=>$this->session->userdata('batch_id'));
        $exam_Data = $this->db_model->select_data('*', 'exams use index (id)',$cond1,'',array('id','asc'));
        
        $dataarray =array();
        if($exam_Data){
            
           foreach($exam_Data as $exams){
               
            $cond['paper_id'] = $exams['id'];  
            $cond['student_id'] =$uid;  
            $result_data = $this->db_model->select_data('*', $table_name.' use index (id)',$cond,'',array('id','desc'),array('date',$like));
           
            if(!empty($result_data)){
                $count = "";
                foreach($result_data as $rkey=>$result){
    
                    $attemptedQuestion = json_decode($result['question_answer'],true);
                    if(!empty($result['question_answer'])){
                        $question_ids = implode(',',array_keys($attemptedQuestion));
                        if(!empty($question_ids)){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)','id in ('.$question_ids.')');
                        }else{
                            $right_ansrs = array();
                        }
                        
                        $rightCount = 0;
                        $wrongCount = 0;
                        $c = 0;
                        foreach($attemptedQuestion as $key=>$value){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)',array('id'=>$key));
                            if(($key == $right_ansrs[0]['id']) && ($value == $right_ansrs[0]['answer'])){
                                $rightCount++;
                            }else{
                                $wrongCount++;
                            }
                          
                        }
        
                        $percentage = (($rightCount - ($wrongCount*0.25))*100)/$result['total_question'];
        
                        $time_taken = '';
                        if($result['start_time']!="" || $result['submit_time']!=""){
                            $stime=strtotime($result['start_time']);
                            $etime=strtotime($result['submit_time']);
                            $elapsed = $etime - $stime;
                            $time_taken = gmdate("H:i", $elapsed);
                        }
                     
                        $dataarray[] = array(
                            'id'=>$result['id'],
                            'paper_id'=>$exams['id'],
                            'paper_name'=>$result['paper_name'],
                            'date'=>date('d-m-Y',strtotime($result['date'])),
                            'start_time'=>date('h:i A',strtotime($result['start_time'])),
                            'submit_time'=>date('h:i A',strtotime($result['submit_time'])),
                            'total_question'=>$result['total_question'],
                            'time_duration'=>gmdate("H:i", $result['time_duration']*60),
                            'attempted_question'=>$result['attempted_question'],
                            'question_answer'=>json_encode($attemptedQuestion),
                            'percentage'=>number_format((float)$percentage, 2, '.', ''),
                            'added_on'=>$result['added_on']
                           
                        ); 
                        
                        $count++;
                    }
                }
            }
           }
           }

        $data['mock_result'] =$dataarray;
		$data['practice_result_d'] = $this->db_model->select_data('total_question,question_answer,date,paper_name,percentage','practice_result',array('student_id'=>$this->session->userdata('uid'),'admin_id'=>$this->session->userdata('admin_id')),1);
	    $data['mock_result_d'] = $this->db_model->select_data('total_question,question_answer,date,paper_name,percentage','mock_result',array('student_id'=>$this->session->userdata('uid'),'admin_id'=>$this->session->userdata('admin_id')),1);
		
		$data['month'] = $month;
		$data['year'] = $year;
	    
		$this->load->view("common/student_header",$header);
		$this->load->view("student/view_progress",$data); 
		$this->load->view("common/student_footer");
	}
	
	function academic_record(){
		$header['title']=$this->lang->line('ltr_academic_record');	
		if(isset($_POST['filter_performance'])){
			$month = $_POST['month']; 
			$year = $_POST['year'];	
		}else{ 	
			$month = date('m');
			$year = date('Y');
		}
		$data['month'] = $month;
		$data['year'] = $year;
	
		$like = $year.'-'.$month.'-';
		
		$like_batch_id='"'.$this->session->userdata('batch_id').'"';
		$data['extra_class'] = $this->db_model->countAll('extra_class_attendance',array('student_id'=>$this->session->userdata('uid')),'','',array('date',$like));
		
		$data['homework'] = $this->db_model->countAll('homeworks',array('admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id')),'','',array('date',$like));
		
		$data['total_extra_class'] = $this->db_model->countAll('extra_classes','',array('batch_id'=>$like_batch_id),'',array('date',$like));
	    $data['practice_result'] = $this->db_model->custom_slect_query(" COUNT(*) AS `numrows` FROM ( SELECT practice_result.id FROM `practice_result` JOIN `exams` ON `exams`.`id`=`practice_result`.`paper_id` WHERE `practice_result`.`admin_id` = '".$this->session->userdata('admin_id')."' AND `student_id` = '".$this->session->userdata('uid')."' AND date(added_at) LIKE '%".$like."%' ESCAPE '!' GROUP BY `paper_id` ) a")[0]['numrows'];
	
		$data['total_practice_test'] = $this->db_model->countAll('exams',array('admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id'),'type'=>2),'','',array('date(added_at)',$like));
		
		$data['mock_result'] = $this->db_model->countAll('mock_result',array('admin_id'=>$this->session->userdata('admin_id'),'student_id'=>$this->session->userdata('uid')),'','',array('date',$like));
		
		$data['total_mock_test'] = $this->db_model->countAll('exams',array('admin_id'=>$this->session->userdata('admin_id'),'batch_id'=>$this->session->userdata('batch_id'),'type'=>1),'','',array('date(added_at)',$like));
		
		$this->load->view("common/student_header",$header);
		$this->load->view("student/academic_record",$data); 
		$this->load->view("common/student_footer");
	}
	function student_attendance(){
		if(isset($_POST['filter_performance'])){
			$month = $_POST['month']; 
			$year = $_POST['year'];	
		}else{ 	
			$month = date('m');
			$year = date('Y');
		}
			$header['title']="Attendance";	
		$id=$this->session->userdata('uid');
		$data['month'] = $month;
		$data['year'] = $year;
		$data['student_id'] = $id;
		$data['baseurl'] = base_url();
		$data['attendance'] = $this->db_model->select_data('id','attendance',array('student_id'=>$this->session->userdata('uid')),1);
		$this->load->view("common/student_header",$header);
		$this->load->view("student/student_attendance",$data); 
		$this->load->view("common/student_footer");
	}
	function certificate(){
		$header['title']=$this->lang->line('ltr_certificate');
		$id=$this->session->userdata('uid');
		$batch_id = $this->session->userdata('batch_id');
		$data['student_certificate']=$this->db_model->select_data('*','certificate',array('student_id'=>$id,'batch_id'=>$batch_id),1,array('id','desc'));
		$data['certificate_details']=$this->db_model->select_data('*','certificate_setting','',1,array('id','desc'));
		
		$data['batchdata']=$this->db_model->select_data('batch_name','batches',array('id'=>$batch_id),1,array('id','desc'));
		
		$data['baseurl'] = base_url();
		$data['uid'] = $id;
		$data['batch_id'] = $batch_id;
		$this->load->view("common/student_header",$header);
		$this->load->view("student/certificate",$data); 
		$this->load->view("common/student_footer");
		
	}
	
	function doubts_ask($id=''){
	    $header['title']=$this->lang->line('ltr_student_doubts_ask');	
		$id=$this->session->userdata('uid');
		$data['doubts_class_data'] = $this->db_model->select_data('doubt_id','student_doubts_class',array('student_id'=>$id),1);
		$data['id'] = $id;
		$batch_id = $this->session->userdata('batch_id');
		$subBatch = $this->db_model->select_data('subject_id, chapter','batch_subjects use index (id)',array('batch_id '=>$this->session->userdata('batch_id')),'',array('id','desc'));
		$condd ='';
		if(!empty($subBatch)){
			$subId =array();
			foreach($subBatch as $value){
				array_push($subId, $value['subject_id']);
			}
			$sid = implode(',', $subId);
			$condd = "id in ($sid)";
		}
		
		$data['subject'] = $this->db_model->select_data('id,subject_name,no_of_questions','subjects use index (id)',$condd,'',array('id','desc'));
		$this->load->view("common/student_header",$header);
		$this->load->view("student/add_doubts_ask",$data); 
		$this->load->view("common/student_footer");
	}
	
	
}