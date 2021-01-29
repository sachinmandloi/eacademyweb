<?php
if(!defined("BASEPATH")) exit("No Direct Script Access Allowed");

class Ajaxcall extends CI_Controller{
	function __construct(){
        parent:: __construct();
        $timezoneDB = $this->db_model->select_data('timezone','site_details',array('id'=>1));
        if(isset($timezoneDB[0]['timezone']) && !empty($timezoneDB[0]['timezone'])){
            date_default_timezone_set($timezoneDB[0]['timezone']);
        }

        $this->load->helper('language');
        
        // check select language
        $language = $this->general_settings('language_name');
        if($language=="french"){
            $this->lang->load('french_lang', 'french');
        }else if($language=="arabic"){
            $this->lang->load('arabic_lang', 'arabic');
        }else{
            $this->lang->load('english_lang', 'english');
        }
    }

    /********   Course Manage Start   ********/

    function general_settings($key_text=''){
        $data = $this->db_model->select_data('*','general_settings',array('key_text'=>$key_text),1);
        return $data[0]['velue_text'];
    }
    function course_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('course_name',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $course_data = $this->db_model->select_data('*','courses use index (id)',array('admin_id'=>$this->session->userdata('uid')),$limit,array('id','desc'),$like);
    
            if(!empty($course_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($course_data as $course){
                   
                    if($course['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$course['id'].'" data-table ="courses" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$course['id'].'" data-table ="courses" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $action = '<p class="actions_wrap"><a class="edit_course btn_edit" data-id="'.$course['id'].'" data-img="'.$course['image'].'"><i class="fa fa-edit""></i></a>
                    <a class="deleteData btn_delete" data-id="'.$course['id'].'" data-table="courses"><i class="fa fa-trash"></i></a></p>';
                    
                    $descriptionWord =$this->readMoreWord($course['description'], $this->lang->line('ltr_description'));
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$course['id'].'">',
                                $count,
                                '<img src="'.base_url('uploads/courses/'.$course['image']).'" alt="course" class="view_large_image">',
                                $course['course_name'],
                                date('d-m-Y',strtotime($course['start_date'])),
                                date('d-m-Y',strtotime($course['end_date'])),
                                $course['course_duration'],
                                $course['class_size'],
                                $course['time_duration'],
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
                                $statusDrop,
                                $action   
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('courses use index (id)',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    } 

	function blog_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('blog_title',$post['search']['value']);
            }else{
               $like = ''; 
            }
            $blog_data = $this->db_model->select_data('*','blog',array('admin_id'=>$this->session->userdata('uid')),$limit,array('id','desc'),$like);
    
            if(!empty($blog_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($blog_data as $blog){
                   
                    if($blog['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$blog['id'].'" data-table ="courses" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$blog['id'].'" data-table ="courses" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $action = '<p class="actions_wrap"><a class="edit_course btn_edit" data-id="'.$blog['id'].'" data-img="'.$blog['image'].'"><i class="fa fa-edit""></i></a>
                    <a class="deleteData btn_delete" data-id="'.$blog['id'].'" data-table="courses"><i class="fa fa-trash"></i></a>
                    <a class="btn_edit" href="'.base_url('admin/blog-reply/'.$blog['id']).'"><i class="fa fa-reply""></i></a>
                    </p>';
                    
                    $descriptionWord =$this->readMoreWord($blog['description'], $this->lang->line('ltr_description'));
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$blog['id'].'">',
                                $count,
                                '<img src="'.base_url('uploads/blog/'.$blog['image']).'" alt="course" class="view_large_image">',
                                $blog['title'],
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
								date('d-m-Y',strtotime($blog['create_at'])),
                                $statusDrop,
                                $action   
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('blog',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    } 
	function addBlog(){  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('blog_title',TRUE))){
                $data_arr = array();
    
                $config['upload_path'] ='./uploads/blog/';
                $config['allowed_types'] = 'jpeg|jpg|png';
                $config['max_size']    = '0';		
                $this->load->library('upload', $config);
                
                $admin_id = $this->session->userdata('uid');
                if($this->input->post('type',TRUE) == 'edit'){
                    $st_id = $data_arr['blog_id'];
                    $prevRecd = $this->db_model->select_data('*','blog',array('admin_id'=>$admin_id,'id !='=>$st_id,'title'=>$this->input->post('blog_title',TRUE)),1);
    
                    //pic upload
                    if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                        if ($this->upload->do_upload('image')){
                            $uploaddata = $this->upload->data();
                            $pic = $uploaddata['raw_name'];
                            $pic_ext = $uploaddata['file_ext'];
                            $image_name = $pic.'_'.date('ymdHis').$pic_ext;
                            rename('./uploads/blog/'.$pic.$pic_ext,'./uploads/blog/'.$image_name);
                            $data_arr['image'] = $image_name;
                        }else{
                            $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                            die();
                        }
                    }
					$data_arr['title'] = trim($this->input->post('blog_title',TRUE));
					$data_arr['description'] = $this->input->post('description',TRUE);
					$data_arr['admin_id'] = $admin_id;
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('blog',$data_arr,array('id'=>$st_id,'admin_id'=>$admin_id),1);
                    
                        $resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_updated_msg'));
                    
                }else{
                    $prevRecd = $this->db_model->select_data('*','blog',array('admin_id'=>$admin_id,'title'=>$this->input->post('blog_title',TRUE)),1);
                    if(empty($prevRecd)){
                        $data_arr['title'] = trim($this->input->post('blog_title',TRUE));
                        $data_arr['description'] = $this->input->post('description',TRUE);
                        $data_arr['status'] = 1;
						$data_arr['admin_id'] = $admin_id;
    
                        //pic upload
                        if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                            if ($this->upload->do_upload('image')){
                                $uploaddata = $this->upload->data();
                                $pic = $uploaddata['raw_name'];
                                $pic_ext = $uploaddata['file_ext'];
                                $image_name = $pic.'_'.date('ymdHis').$pic_ext;
                                rename('./uploads/blog/'.$pic.$pic_ext,'./uploads/blog/'.$image_name);
                                $data_arr['image'] = $image_name;
                            }else{
                                $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                                die();
                            }
                        }else{
							$data_arr['image']='student_img.png';
                        }
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('blog',$data_arr);
                        
						$resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_added_msg'));
                        
                    }else{
                        $resp = array('status'=>'0', 'msg' => $this->lang->line('ltr_something_msg')); 
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }  
    }
    function addcourse(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('course_name',TRUE))){
             
                $diff = abs(strtotime($this->input->post('end_date',TRUE))-strtotime($this->input->post('start_date',TRUE)));
                $years = floor($diff / (365*60*60*24));  
                $months = floor(($diff - $years * 365*60*60*24)/ (30*60*60*24)); 
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                if($months > 0 and $months <= 1)
                    $mnth = $months.' month';
                else if($months > 1) 
                    $mnth = $months.' months';
                else 
                    $mnth = '';
    
                if($days > 0 and $days <= 1)
                    $dys = ' '.$days.' day';
                else if($days > 1) 
                    $dys = ' '.$days.' days';
                else 
                    $dys = '';
                
                $prevdata =  $this->db_model->select_data('id','courses use index (id)',array('course_name'=>$this->input->post('course_name',TRUE)),1);
                
                if($this->input->post('type',TRUE) == 'edit'){
                    if(empty($prevdata) || ($prevdata[0]['id'] == $this->input->post('course_id',TRUE))){
                        $data_arr = array(
                            'course_name'	=>	$this->input->post('course_name',TRUE),
                            'start_date'	=>	date('Y-m-d',strtotime($this->input->post('start_date',TRUE))),
                            'end_date'	=>	date('Y-m-d',strtotime($this->input->post('end_date',TRUE))),
                            'class_size'	=>	$this->input->post('class_size',TRUE),
                            'time_duration'	=>	$this->input->post('time_duration',TRUE),
                            'description'	=>	$this->input->post('description',TRUE)
                        ); 
                       
                        $data_arr['course_duration'] = trim($mnth.$dys);
                        
                        if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                            $image = $this->upload_media($_FILES,'uploads/courses/','image');
                            if(is_array($image)){
                                $resp = array('status'=>'2', 'msg' => $image['msg']);
                                die();
                            }else{
                                $data_arr['image'] = $image;
                            }
                        }
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->update_data_limit('courses',$data_arr,array('id'=>$this->input->post('course_id',TRUE)),1);
                        if($ins==true){
                            $resp = array('status'=>'1', 'msg' => $this->lang->line('ltr_course_updated_msg'));
                        }else{
                            $resp = array('status'=>'0');
                        }
                    }else{
                         $resp = array('status'=>'2', 'msg' => $this->lang->line('ltr_course_name_already_msg'));
                    }
                }else{
                    if(empty($prevdata)){
                        $data_arr = array(
                            'course_name'	=>	$this->input->post('course_name',TRUE),
                            'status'	=>	1,
                            'start_date'	=>	date('Y-m-d',strtotime($this->input->post('start_date',TRUE))),
                            'end_date'	=>	date('Y-m-d',strtotime($this->input->post('end_date',TRUE))),
                            'admin_id' => $this->session->userdata('uid'), 
                            'class_size'	=>	$this->input->post('class_size',TRUE),
                            'time_duration'	=>	$this->input->post('time_duration',TRUE),
                            'description'	=>	$this->input->post('description',TRUE)
                        ); 
        
                        $data_arr['course_duration'] = trim($mnth.$dys);
                        
                        if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                            $image = $this->upload_media($_FILES,'uploads/courses/','image');
                            if(is_array($image)){
                                $resp = array('status'=>'2', 'msg' => $image['msg']);
                                die();
                            }else{
                                $data_arr['image'] = $image;
                            }
                        }
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('courses',$data_arr);
                        if($ins==true){
                            $resp = array('status'=>'1', 'msg' => $this->lang->line('ltr_course_add_msg'));
                        }else{
                            $resp = array('status'=>'0');
                        }
                    }else{
                         $resp = array('status'=>'2', 'msg' => $this->lang->line('ltr_course_name_already_msg'));
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);   
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function upload_media($files,$path,$file){   
        $config['upload_path'] =$path;
        $config['allowed_types'] = 'jpeg|jpg|png|SVG|svg';
        $config['max_size']    = '0';
        $filename = '';		
        $this->load->library('upload', $config);
        if ($this->upload->do_upload($file)){
            $uploadData = $this->upload->data();
            $filename = $uploadData['file_name'];
            return $filename;
        }else{
            $resp = array('status'=>'2', 'msg' => $this->upload->display_errors());
            return $resp;
        }     
    }

    /********   Course Manage End   ********/

    /********   Batch Manage Start   ********/

    function batch_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('batch_name',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $batch_data = $this->db_model->select_data('*','batches use index (id)',array('admin_id'=>$this->session->userdata('uid')),$limit,array('id','desc'),$like);
    
            if(!empty($batch_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($batch_data as $batch){
                   
                    if($batch['batch_type']==2){
                            $price =$batch['batch_price'].' '.$this->general_settings('currency_decimal_code');
                        if(!empty($batch['batch_offer_price'])){
                            $price ='<s>'.$batch['batch_price'].' '.$this->general_settings('currency_decimal_code').'</s>'.' / '.$batch['batch_offer_price'].' '.$this->general_settings('currency_decimal_code');
                        }
                    }else{
                        $price =$this->lang->line('ltr_free');
                    }
                    if($batch['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$batch['id'].'" data-table ="batches" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$batch['id'].'" data-table ="batches" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $action = '<p class="actions_wrap"><a class="btn_edit" href="'.base_url('admin/add-batch/').$batch['id'].'"><i class="fa fa-edit"></i></a>
                        <a class="deleteData btn_delete" data-id="'.$batch['id'].'" data-table="batches"><i class="fa fa-trash"></i></a></p>';
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$batch['id'].'">',
                                $count,
                                $batch['batch_name'],
                                date('d-m-Y',strtotime($batch['start_date'])),
                                date('d-m-Y',strtotime($batch['end_date'])),
                                date('h:i A',strtotime($batch['start_time'])).' - '. date('h:i A',strtotime($batch['end_time'])),
                                $price,
                                $batch['no_of_student'],
                                $statusDrop,
                                $action   
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('batches use index (id)',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function addbatch(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('batch_name',TRUE))){
                $prevdata =  $this->db_model->select_data('id','batches use index (id)',array('batch_name'=>$this->input->post('batch_name',TRUE)),1);
                if($this->input->post('type',TRUE) == 'edit'){
                    if(empty($prevdata) || ($prevdata[0]['id'] == $this->input->post('batch_id',TRUE))){
                        $data_arr = array(
                            'batch_name'	=>	$this->input->post('batch_name',TRUE),
                            'start_date'	=>	date('Y-m-d',strtotime($this->input->post('start_date',TRUE))),
                            'end_date'	=>	date('Y-m-d',strtotime($this->input->post('end_date',TRUE))),
                            'start_time'	=>	date('H:i:s',strtotime($this->input->post('start_time',TRUE))),
                            'end_time'	=>	date('H:i:s',strtotime($this->input->post('end_time',TRUE))),
                        ); 
						if(!empty($this->input->post('batchType',TRUE))){
							$data_arr['batch_type']=$this->input->post('batchType',TRUE);
						}
						
						if(!empty($this->input->post('batchPrice',TRUE))){
							$data_arr['batch_price']=$this->input->post('batchPrice',TRUE);
						}
						
						if(!empty($this->input->post('batchOfferPrice',TRUE))){
							$data_arr['batch_offer_price']=$this->input->post('batchOfferPrice',TRUE);
						}
                        if(!empty($this->input->post('batch_description',TRUE))){
							$data_arr['description']=$this->input->post('batch_description',TRUE);
						}
						
						 //batch image upload
						if(isset($_FILES['batch_image']) && !empty($_FILES['batch_image']['name'])){
							$config['upload_path'] ='./uploads/batch_image/';
							$config['allowed_types'] = 'jpeg|jpg|png';
							$config['max_size']    = '0';		
							$this->load->library('upload', $config); 
							if ($this->upload->do_upload('batch_image')){
								$uploaddata = $this->upload->data();
								$pic = $uploaddata['raw_name'];
								$pic_ext = $uploaddata['file_ext'];
								$image_name = $pic.'_'.date('ymdHis').$pic_ext;
								rename('./uploads/batch_image/'.$pic.$pic_ext,'./uploads/batch_image/'.$image_name);
								$data_arr['batch_image'] = $image_name;
							}else{
								$resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
								echo json_encode($resp,JSON_UNESCAPED_SLASHES);
								die();
							}
						}
                       
						
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->update_data_limit('batches',$data_arr,array('id'=>$this->input->post('batch_id',TRUE)),1);

                        if($ins==true){

                            $batch_id = $this->input->post('batch_id');
                            $this->db_model->delete_data('batch_subjects',array('batch_id'=>$batch_id));
                            $data = $this->input->post();
                            
                            for($i=0; $i < count($data['batch_subject']); $i++){      
                                $teacher_id = $data['batch_teacher'][$i];                        
                                $subjectData = array(
                                    'batch_id'	=> $batch_id,
                                    'teacher_id' => $teacher_id,
                                    'subject_id' => $data['batch_subject'][$i],
                                    'chapter' => json_encode(json_decode($data['batch_chapter'],true)[$i]),
                                    'sub_start_date' => date('Y-m-d',strtotime($data['sub_start_date'][$i])),
                                    'sub_end_date'	=> date('Y-m-d',strtotime($data['sub_end_date'][$i])),
                                    'sub_start_time'	=> date('H:i:s',strtotime($data['sub_start_time'][$i])),
                                    'sub_end_time'	=> date('H:i:s',strtotime($data['sub_end_time'][$i])),
                                ); 
                               
                                $this->db_model->insert_data('batch_subjects',$subjectData);

                                $teacherData = $this->db_model->select_data('id,teach_batch','users use index (id)',array('id'=>$teacher_id),1);

                                if(!empty($teacherData)){
                                    if(!empty($teacherData[0]['teach_batch'])){
                                        $newBatch = array_unique(array_merge(explode(',',$teacherData[0]['teach_batch']), array($batch_id)));
                                    }else{
                                        $newBatch = array($batch_id);
                                    }
                                    
                                    $this->db_model->update_data_limit('users',array('teach_batch'=>implode(',',$newBatch)),array('id'=>$teacherData[0]['id']),1);
                                }
                            }
							
							// batch fecherd add
							if(!empty($data['batch_speci_heading'])){
								for($i=0; $i < count($data['batch_speci_heading']); $i++){     
									$speci_heading = array(
										'batch_id'	=> $batch_id,
										'batch_specification_heading' => $data['batch_speci_heading'][$i],
										'batch_fecherd' => json_encode(json_decode($data['batch_sub_fecherd'],true)[$i]),
									); 
                                   if(!empty($data['batch_speci_id'][$i])){
                                       $this->db_model->update_data_limit('batch_fecherd',$speci_heading,array('id'=>$data['batch_speci_id'][$i]));
                                   }else{
									$this->db_model->insert_data('batch_fecherd',$speci_heading);
							        }
								}
                            }
							
                            $resp = array('status'=>'1', 'msg' => $this->lang->line('ltr_batch_updated_msg'),'url' => base_url('admin/batch-manage'));
                        }else{
                            $resp = array('status'=>'0');
                        }
                    }else{
                        $resp = array('status'=>'2', 'msg' => $this->lang->line('ltr_batch_name_already_msg'));
                    }
                }else{
                    if(empty($prevdata)){
                        $data_arr = array(
                            'batch_name'	=>	$this->input->post('batch_name',TRUE),
                            'start_date'	=>	date('Y-m-d',strtotime($this->input->post('start_date',TRUE))),
                            'end_date'	=>	date('Y-m-d',strtotime($this->input->post('end_date',TRUE))),
                            'start_time'	=>	date('H:i:s',strtotime($this->input->post('start_time',TRUE))),
                            'end_time'	=>	date('H:i:s',strtotime($this->input->post('end_time',TRUE))),
                            'status'	=>	1,
                            'admin_id' => $this->session->userdata('uid')
                        );
						if(!empty($this->input->post('batchType',TRUE))){
							$data_arr['batch_type']=$this->input->post('batchType',TRUE);
						}
						
						if(!empty($this->input->post('batchPrice',TRUE))){
							$data_arr['batch_price']=$this->input->post('batchPrice',TRUE);
						}
						
						if(!empty($this->input->post('batchOfferPrice',TRUE))){
							$data_arr['batch_offer_price']=$this->input->post('batchOfferPrice',TRUE);
						}
						
						if(!empty($this->input->post('batch_description',TRUE))){
							$data_arr['description']=$this->input->post('batch_description',TRUE);
						}
						
						 //batch image upload
						if(isset($_FILES['batch_image']) && !empty($_FILES['batch_image']['name'])){
							$config['upload_path'] ='./uploads/batch_image/';
							$config['allowed_types'] = 'jpeg|jpg|png';
							$config['max_size']    = '0';		
							$this->load->library('upload', $config); 
							if ($this->upload->do_upload('batch_image')){
								$uploaddata = $this->upload->data();
								$pic = $uploaddata['raw_name'];
								$pic_ext = $uploaddata['file_ext'];
								$image_name = $pic.'_'.date('ymdHis').$pic_ext;
								rename('./uploads/batch_image/'.$pic.$pic_ext,'./uploads/batch_image/'.$image_name);
								$data_arr['batch_image'] = $image_name;
							}else{
								$resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
								echo json_encode($resp,JSON_UNESCAPED_SLASHES);
								die();
							}
						}
                       
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('batches',$data_arr);
                       
                        if($ins){
                            $batch_id = $this->db->insert_id();
                            $data = $this->input->post();
                            for($i=0; $i < count($data['batch_subject']); $i++){      
                                $teacher_id = $data['batch_teacher'][$i];
                                $teacherData = $this->db_model->select_data('id,teach_batch','users use index (id)',array('id'=>$teacher_id),1);
                               
                                if(!empty($teacherData)){
                                    if(!empty($teacherData[0]['teach_batch'])){
                                        $newBatch = array_unique(array_merge(explode(',',$teacherData[0]['teach_batch']), array($batch_id)));
                                    }else{
                                        $newBatch = array($batch_id);
                                    }
                                   
                                    $this->db_model->update_data_limit('users',array('teach_batch'=>implode(',',$newBatch)),array('id'=>$teacherData[0]['id']),1);
                                }

                                $subjectData = array(
                                    'batch_id'	=> $batch_id,
                                    'teacher_id' => $teacher_id,
                                    'subject_id' => $data['batch_subject'][$i],
                                    'chapter' => json_encode(json_decode($data['batch_chapter'],true)[$i]),
                                    'sub_start_date' => date('Y-m-d',strtotime($data['sub_start_date'][$i])),
                                    'sub_end_date'	=> date('Y-m-d',strtotime($data['sub_end_date'][$i])),
                                    'sub_start_time'	=> date('H:i:s',strtotime($data['sub_start_time'][$i])),
                                    'sub_end_time'	=> date('H:i:s',strtotime($data['sub_end_time'][$i])),
                                ); 

                                $this->db_model->insert_data('batch_subjects',$subjectData);
                            }
							// batch fecherd add
							if(!empty($data['batch_speci_heading'])){
								for($i=0; $i < count($data['batch_speci_heading']); $i++){     
									$speci_heading = array(
										'batch_id'	=> $batch_id,
										'batch_specification_heading' => $data['batch_speci_heading'][$i],
										'batch_fecherd' => json_encode(json_decode($data['batch_sub_fecherd'],true)[$i]),
									); 

									$this->db_model->insert_data('batch_fecherd',$speci_heading);
								}
                            }
                            $resp = array('status'=>'1', 'msg' => 'Batch added successfully.','url' => base_url('admin/batch-manage'));
                        }else{
                            $resp = array('status'=>'0');
                        }
                    }else{
                        $resp = array('status'=>'2', 'msg' => $this->lang->line('ltr_batch_name_already_msg'));
                    }
                }
                
            } 
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);  
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }  
    }

    function batchdetails_table($uid){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $join = array('subjects',"subjects.subject_name like '%".$post['search']['value']."%' AND subjects.id = batch_subjects.subject_id");
            }else{
                $join = ''; 
            }
            
            $role = $this->session->userdata('uid');
            $cond = array('batch_subjects.teacher_id'=>$uid);
             
            if(isset($get['batch']) || isset($get['subject'])){
                if($get['batch']!='' && $get['subject']!=''){ 
                    $cond['batch_subjects.subject_id'] = $get['subject'];
                    $cond['batch_subjects.batch_id'] = $get['batch'];
                }else if($get['batch']!=''){
                    $cond['batch_subjects.batch_id'] = $get['batch'];
                }else if($get['subject']!=''){
                    $cond['batch_subjects.subject_id'] = $get['subject'];
                }
            }
            
            
            $batchSubjects = $this->db_model->select_data('batch_subjects.*','batch_subjects use index (id)',$cond,$limit,array('id','desc'),'',$join,'');
     
            if(!empty($batchSubjects)){
                foreach($batchSubjects as $sub){
                    $completedchaptr = json_decode($sub['chapter_status'],true);
                    if(!empty($completedchaptr)){
                        $dataIndx = implode(',',$completedchaptr);
                    }else{
                        $dataIndx = '';
                    }
    
                    $batchdata = $this->db_model->select_data('batch_name,start_time,end_time,status','batches use index (id)',array('id'=>$sub['batch_id']));
                    
                    $subject_name = $this->db_model->select_data('subject_name','subjects use index (id)',array('id'=>$sub['subject_id']));
                    $chapter_ids = implode(',',json_decode($sub['chapter']));
                    $chapterdata = $this->db_model->select_data('id,chapter_name','chapters use index (id)',"id in ($chapter_ids)");
                    
                    $chapters = '';
                    
                    if(!empty($batchdata)){
                        if(!empty($chapterdata)){
                            $i = 0;
                            $completedDate = json_decode($sub['chapter_complt_date'],true);
                            foreach($chapterdata as $chapter){
                                $style = '';
                                $title = '';
                                if(!empty($completedchaptr) && in_array($chapter['id'],$completedchaptr)){
                                    $style = 'style="background-color:#73d872;"';
                                }
                                
                                if(!empty($completedDate) && array_key_exists($chapter['id'],$completedDate)){
                                    
                                    $title = 'Completed on '.$completedDate[$chapter['id']];
                                }
                                
                                $chapters .= '<p class="chapter_wrap" data-id="'.$sub['id'].'" '.$style.' data-chapter="'.$chapter['id'].'" title="'.$title.'"><span>'.$chapter['chapter_name'].'</span></p>';
                                $i++;
                            }
                        }
                        if(!empty($subject_name)){
                            
                            if($batchdata[0]['status']==1){
                                 $batchon = 2;
                            }else{
                                $batchon = 1;
                            }
                            if($role != 1){
                                $action = '<p class="actions_wrap"><a  data-tcsid="'.$sub['subject_id'].'" data-tcbid="'.$sub['batch_id'].'" class="btn_view tc_progress_popup"><i class="fa fa-eye"></i></a>
                                    <a class="edit_batchDetails btn_edit" data-id="'.$sub['id'].'" data-index="'.$dataIndx.'" data-batchon="'.$batchon.'"><i class="fa fa-edit"></i></a></p>';
                            }else{
                                $action = '<p class="actions_wrap"><a  data-tcsid="'.$sub['subject_id'].'" data-tcbid="'.$sub['batch_id'].'" class="btn_view tc_progress_popup"><i class="fa fa-eye"></i></a>';
                            }
                            $complt_date="";
                            if($sub['total_chapter_complt_date'] !='0000-00-00 00:00:00'){
                                $complt_date =date('d-m-Y h:i A',strtotime($sub['total_chapter_complt_date']));
                            }
                            $dataarray[] = array(
                                        $count,
                                        $batchdata[0]['batch_name'],
                                        date('h:i A',strtotime($batchdata[0]['start_time'])).' - '.date('h:i A',strtotime($batchdata[0]['end_time'])),
                                        date('d-m-Y',strtotime($sub['sub_start_date'])).' - '.date('d-m-Y',strtotime($sub['sub_end_date'])),
                                        date('h:i A',strtotime($sub['sub_start_time'])).' - '.date('h:i A',strtotime($sub['sub_end_time'])),
                                        $complt_date,
                                        $subject_name[0]['subject_name'],
                                        $chapters,
                                        $action
                                    ); 
                        }
                        $count++;
                    }
                      
                }
                 
                $recordsTotal = $this->db_model->countAll('batch_subjects use index (id)',$cond,'','','',$join,'');
                     
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function change_chapter_staus(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id'))){
                $prevData = $this->db_model->select_data('chapter_status,chapter_complt_date, chapter','batch_subjects use index (id)',array('id'=>$this->input->post('id')));
                if(!empty($prevData[0]['chapter_status'])){
                    $prevStatus = json_decode($prevData[0]['chapter_status'],true);
                    $newstatus = array_unique(array_merge($prevStatus,array($this->input->post('chapter'))));
                    
                    $prevCmplt = json_decode($prevData[0]['chapter_complt_date'],true);
                   
                    $newcmplt = $prevCmplt + array($this->input->post('chapter') => date('d-m-Y'));
                    $data = array('chapter_status'=>json_encode($newstatus),'chapter_complt_date'=>json_encode($newcmplt));
                }else{
                    $data = array('chapter_status'=>json_encode(array($this->input->post('chapter'))),'chapter_complt_date'=>json_encode(array($this->input->post('chapter') => date('d-m-Y'))));
                }
            
               if((count(json_decode($data['chapter_status'])))==(count(json_decode($prevData[0]['chapter'])))){
                   $data['total_chapter_complt_date']= date('Y-m-d H:i:s');
                   $ins = $this->db_model->update_data_limit('batch_subjects',$this->security->xss_clean($data),array('id'=>$this->input->post('id')));
               }else{
                $ins = $this->db_model->update_data_limit('batch_subjects',$this->security->xss_clean($data),array('id'=>$this->input->post('id')));
               }
                if($ins){
                    $resp = array('status'=>1);
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        } 
    }

    function get_progress(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('teacher_id'))){

                if(!empty($this->input->post('batch')) && !empty($this->input->post('subject'))){
                    $cond = array('batch_id'=>$this->input->post('batch'),'subject_id'=>$this->input->post('subject'),'teacher_id'=>$this->input->post('teacher_id'));
                }else if(!empty($this->input->post('batch'))){
                    $cond = array('batch_id'=>$this->input->post('batch'),'teacher_id'=>$this->input->post('teacher_id'));
                }else if(!empty($this->input->post('subject'))){
                    $cond = array('subject_id'=>$this->input->post('subject'),'teacher_id'=>$this->input->post('teacher_id'));
                }else{
                    $cond = array('teacher_id'=>$this->input->post('teacher_id'));
                }
               
                $chapter_data = $this->db_model->select_data('chapter,chapter_status','batch_subjects use index (id)',$cond);
               
                if(!empty($chapter_data)){
                    $pendingCount = 0;
                    $completeCount = 0;
                    $totalCount = 0;
                    foreach($chapter_data as $chapter){
                        $total = count(json_decode($chapter['chapter'],true));
                        if(!empty($chapter['chapter_status'])){
                            $complete = count(json_decode($chapter['chapter_status'],true));
                        }else{
                            $complete = 0;
                        }
                        
                        if($complete < $total){
                            $pending = ($total - $complete);
                        }else{
                            $pending = 0;
                        }
                        $pendingCount += $pending;
                        $completeCount += $complete;
                        $totalCount += $total;
                    }

                    $completeChapter = ($completeCount/$totalCount)*100;
                    $pendingChapter = ($pendingCount/$totalCount)*100;
                    $resp = array('status'=>1,'complete'=>$completeChapter,'pending'=>$pendingChapter);
                   
                }else{
                    $resp = array('status'=>0,'msg'=> $this->lang->line('ltr_no_data_msg'),'complete'=>0,'pending'=>100);
                }
                
            }else{
                $resp = array('status'=>0,'msg'=>$this->lang->line('ltr_something_msg'));
            }
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        } 
    }

    /********   Batch Manage End   ********/

     /********   Student Manage Start   ********/

    function student_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('name',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            if($this->session->userdata('role')==1){
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else{
                $batch_ids = $this->session->userdata('batch_id');
                $admin_id = $this->session->userdata('admin_id');
        		if(!empty($batch_ids)){
        			$cond = "admin_id = $admin_id AND batch_id in ($batch_ids) AND status = 1";
        		}else{
        			$cond = '';
        		}
            }
            
            if(isset($get['user_status']) || isset($get['lgStatus']) || isset($get['user_batch'])){
                if($get['user_status']!='' && $get['lgStatus']!='' && $get['user_batch']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['status'] = $get['user_status'];   
                        $cond['login_status'] = $get['lgStatus'];   
                        $cond['batch_id'] = $get['user_batch'];
                    }else{
                        $cond .= ' AND status='.$get['user_status'];
                        $cond .= ' AND login_status='.$get['lgStatus'];
                        $cond .= ' AND batch_id='.$get['user_batch'];
                    }
                      
                }else if($get['user_status']!='' && $get['lgStatus']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['status'] = $get['user_status'];   
                        $cond['login_status'] = $get['lgStatus'];
                    }else{
                        $cond .= ' AND status='.$get['user_status'];
                        $cond .= ' AND login_status='.$get['lgStatus'];
                    }
                }else if($get['user_status']!='' && $get['user_batch']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['status'] = $get['user_status'];   
                        $cond['batch_id'] = $get['user_batch'];
                    }else{
                        $cond .= ' AND status='.$get['user_status'];
                        $cond .= ' AND batch_id='.$get['user_batch'];
                    }
                }else if($get['lgStatus']!='' && $get['user_batch']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['login_status'] = $get['lgStatus'];  
                        $cond['batch_id'] = $get['user_batch'];
                    }else{
                        $cond .= ' AND login_status='.$get['lgStatus'];
                        $cond .= ' AND batch_id='.$get['user_batch'];
                    }
                }else if($get['user_status']!='' ){
                    if($this->session->userdata('role')==1){
                        $cond['status'] = $get['user_status'];  
                    }else{
                        $cond .= ' AND status='.$get['user_status'];
                    }
                }else if($get['lgStatus']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['login_status'] = $get['lgStatus'];  
                    }else{
                        $cond .= ' AND login_status='.$get['lgStatus'];
                    }
                }else if($get['user_batch']!=''){
                    if($this->session->userdata('role')==1){
                        $cond['batch_id'] = $get['user_batch'];  
                    }else{
                        $cond .= ' AND batch_id='.$get['user_batch'];
                    }
                }
            }
            
            $student_data = $this->db_model->select_data('*','students use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
            if(($this->session->userdata('role')==3) && empty($this->session->userdata('batch_id'))){
                $student_data = "";
            }
            if(!empty($student_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }else if($role == '3'){
                    $profile = 'teacher';
                }
    
                $batch_array = $this->db_model->select_data('id,batch_name','batches use index (id)',array('admin_id'=>$this->session->userdata('uid')));
    
                foreach($student_data as $student){
                    if (!empty($student['image']))                    { 
                        $image = '<img src="'.base_url().'uploads/students/'.$student['image'].'" title="'.$student['name'].'" class="view_large_image"></a>';
                    }else{
                        $image = '<img src="'.base_url().'assets/images/student_img.png" title="'.$student['name'].'" class="view_large_image"></a>';
                    }
    
                    if($role == '1'){  
                        $batchData = '<select class="form-control changeStudBatch datatableSelectSrch" name="batch"  data-id="'.$student['id'].'">';
                            if(!empty($batch_array)){
                                foreach($batch_array as $batch){
                                  if($student['batch_id'] == $batch['id']){
                                        $select = 'selected';
                                    }else{
                                        $select = '';
                                    }
                                    $batchData .= '<option '.$select.' value="'.$batch['id'].'">'.$batch['batch_name'].'</option>';
                                }
                            }   
                        $batchData .= '</select>';
                    }else if($role == '3'){
                        $batchName = $this->db_model->select_data('batch_name','batches use index (id)',array('id'=>$student['batch_id']));
                        if(!empty($batchName)){
                            $batchData = $batchName[0]['batch_name'];
                        }else{
                            $batchData = '';
                        }
                    }
    
                    
                    if($student['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$student['id'].'" data-table ="students" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$student['id'].'" data-table ="students" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    
                    if($student['payment_status']==1){
						  $payment_status=$this->lang->line('ltr_paid'); 
					   }else{
						  $payment_status=$this->lang->line('ltr_unpaid'); 
					   }
                    if($role == '1'){
                       						
                       
                            $action = '<div class="actions_wrap_dot">
                            <span class="tbl_action_drop" >
                                <svg xmlns="https://www.w3.org/2000/svg" width="15px" height="4px">
                				<path fill-rule="evenodd" fill="rgb(77 74 129)" d="M13.031,4.000 C11.944,4.000 11.062,3.104 11.062,2.000 C11.062,0.895 11.944,-0.000 13.031,-0.000 C14.119,-0.000 15.000,0.895 15.000,2.000 C15.000,3.104 14.119,4.000 13.031,4.000 ZM7.500,4.000 C6.413,4.000 5.531,3.104 5.531,2.000 C5.531,0.895 6.413,-0.000 7.500,-0.000 C8.587,-0.000 9.469,0.895 9.469,2.000 C9.469,3.104 8.587,4.000 7.500,4.000 ZM1.969,4.000 C0.881,4.000 -0.000,3.104 -0.000,2.000 C-0.000,0.895 0.881,-0.000 1.969,-0.000 C3.056,-0.000 3.937,0.895 3.937,2.000 C3.937,3.104 3.056,4.000 1.969,4.000 Z"></path>
                				</svg>
                				<ul class="tbl_action_ul">
                				    <li>
                				        <a href="'.base_url('admin/student-attendance/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-check-circled"></i>
                				            </span>
                				            '.$this->lang->line('ltr_attendance').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url('admin/student-attendance-extra-class/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-tasks-alt"></i>
                				            </span>
                				            '.$this->lang->line('ltr_extra_class_attendance').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url('admin/student-progress/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-paper"></i>
                				            </span>
                				             '.$this->lang->line('ltr_progress').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url('admin/student-academic-record/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-bars"></i>
                				            </span>'.$this->lang->line('ltr_academic_record').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url().$profile.'/student-notice/'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="fas fa-bell"></i>
                				            </span>
                				            '.$this->lang->line('ltr_notice').'
                				        </a>
                				    </li>
									<li>
                				        <a href="'.base_url().$profile.'/doubts-ask/'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-speech-comments"></i>
                				            </span>
                				            '.$this->lang->line('ltr_doubts_ask').' 
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url().$profile.'/add-student/'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="fa fa-edit"></i>
                				            </span>
                				            '.$this->lang->line('ltr_edit').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="javascript:void(0);" class="deleteData" data-id="'.$student['id'].'" data-table="students">
                				            <span class="action_drop_icon">
                				                <i class="fa fa-trash"></i>
                				            </span>
                				            '.$this->lang->line('ltr_delete').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="javascript:void(0);" class="changePassModal" data-id="'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-gear"></i>
                				            </span>
                				            '.$this->lang->line('ltr_change_password').'
                				        </a>
                				    </li>
									<li>
                				        <a href="javascript:void(0);" class="paymentStatus" data-id="'.$student['id'].'">
                				            <span class="action_drop_icon">
                				               <i class="icofont-mail"></i>
                				            </span>
                				            '.$payment_status.'
                				        </a>
                				    </li>
                				</ul>
                            </span>
                         </div>';
						 $user_name =$this->readMoreWord($student['name'], 'Student Name',15);
                        $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$student['id'].'">',
                                $count,
                                $image.$user_name,
                                '<p class="email">'.$student['email'].'</p>',
                                $student['contact_no'],
                                $student['enrollment_id'],
                                $batchData,
                                date('d-m-Y',strtotime($student['admission_date'])),
                                $statusDrop,
                                $action
                        ); 
                    }else if($role == '3'){
                        $action = '<div class="actions_wrap_dot">
                            <span class="tbl_action_drop" >
                                <svg xmlns="https://www.w3.org/2000/svg" width="15px" height="4px">
                				<path fill-rule="evenodd" fill="rgb(77 74 129)" d="M13.031,4.000 C11.944,4.000 11.062,3.104 11.062,2.000 C11.062,0.895 11.944,-0.000 13.031,-0.000 C14.119,-0.000 15.000,0.895 15.000,2.000 C15.000,3.104 14.119,4.000 13.031,4.000 ZM7.500,4.000 C6.413,4.000 5.531,3.104 5.531,2.000 C5.531,0.895 6.413,-0.000 7.500,-0.000 C8.587,-0.000 9.469,0.895 9.469,2.000 C9.469,3.104 8.587,4.000 7.500,4.000 ZM1.969,4.000 C0.881,4.000 -0.000,3.104 -0.000,2.000 C-0.000,0.895 0.881,-0.000 1.969,-0.000 C3.056,-0.000 3.937,0.895 3.937,2.000 C3.937,3.104 3.056,4.000 1.969,4.000 Z"></path>
                				</svg>
                				<ul class="tbl_action_ul">
                				    <li>
                				        <a data-toggle="tooltip" data-placement="top" title="Attendance" href="'.base_url('teacher/student-attendance/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-check-circled"></i>
                				            </span>
                				            '.$this->lang->line('ltr_attendance').'
                				        </a>
                				    </li>
                				    <li>
                				        <a data-toggle="tooltip" data-placement="top" title="Extra Class Attendance" href="'.base_url('teacher/student-attendance-extra-class/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-tasks-alt"></i>
                				            </span>
                				            '.$this->lang->line('ltr_extra_class_attendance').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="'.base_url('teacher/student-progress/').$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-paper"></i>
                				            </span>
                				             '.$this->lang->line('ltr_progress').'
                				        </a>
                				    </li>
                				    <li>
                				         <a  href="'.base_url('teacher/student-academic-record/').$student['id'].'">
                				                <i class="icofont-bars"></i>
                				            </span>'.$this->lang->line('ltr_academic_record').'
                				        </a>
                				    </li>
                				    <li>
                				         <a href="'.base_url().$profile.'/student-notice/'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="fas fa-bell"></i>
                				            </span>
                				            '.$this->lang->line('ltr_notice').'
                				        </a>
                				    </li>
									<li>
                				        <a href="'.base_url().$profile.'/doubts-ask/'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-speech-comments"></i>
                				            </span>
                				           '.$this->lang->line('ltr_doubts_ask').'
                				        </a>
                				    </li>
                				    <li>
                				        <a href="javascript:void(0);" class="changePassModal" data-id="'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-gear"></i>
                				            </span>
                				            '.$this->lang->line('ltr_change_password').'
                				        </a>
                				    </li>
									<li>
                				        <a href="javascript:void(0);" class="paymentStatus" data-id="'.$student['id'].'">
                				            <span class="action_drop_icon">
                				                <i class="icofont-mail"></i>
                				            </span>
                				            '.$payment_status.'
                				        </a>
                				    </li>
                				</ul>
                            </span>
                         </div>';
						 $user_name =$this->readMoreWord($student['name'], 'Student Name',15);
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$student['id'].'">',
                                $count,
                                $image.$user_name,
                                '<p class="email">'.$student['email'].'</p>',
                                $student['contact_no'],
                                $student['enrollment_id'],
                                $batchData,
                                date('d-m-Y',strtotime($student['admission_date'])),
                                $action
                        ); 
                    }
                    
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('students use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function addNewStudent(){  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('email',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
    
                $config['upload_path'] ='./uploads/students/';
                $config['allowed_types'] = 'jpeg|jpg|png';
                $config['max_size']    = '0';		
                $this->load->library('upload', $config);
                
                $admin_id = $this->session->userdata('uid');
                if($this->input->post('type',TRUE) == 'edit'){
                    unset($data_arr['type']);
                    $st_id = $data_arr['student_id'];
                    unset($data_arr['student_id']);
                    $data_arr['dob'] = date('Y-m-d',strtotime($data_arr['dob']));
                    $prevRecd = $this->db_model->select_data('batch_id','students use index (id)',array('admin_id'=>$admin_id,'id'=>$st_id),1);
    
                    //pic upload
                    if(isset($_FILES['stu_pic']) && !empty($_FILES['stu_pic']['name'])){
                        if ($this->upload->do_upload('stu_pic')){
                            $uploaddata = $this->upload->data();
                            $pic = $uploaddata['raw_name'];
                            $pic_ext = $uploaddata['file_ext'];
                            $image_name = $pic.'_'.date('ymdHis').$pic_ext;
                            rename('./uploads/students/'.$pic.$pic_ext,'./uploads/students/'.$image_name);
                            $data_arr['image'] = $image_name;
                        }else{
                            $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                            die();
                        }
                    }
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('students',$data_arr,array('id'=>$st_id,'admin_id'=>$admin_id),1);
                    if($ins){
                        if($prevRecd[0]['batch_id']!=$this->input->post('batch_id',TRUE)){
                            $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$this->input->post('batch_id',TRUE)),'plus',1);
                            $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$prevRecd[0]['batch_id']),'minus',1);
                        }
                        $resp = array('status'=>'1', 'msg' => $this->input->post('name',TRUE).$this->lang->line('ltr_student_details_updated_msg'),'enroll_id'=>'', 'password'=>'');
                    }
                }else{
                    $prevRecd = $this->db_model->select_data('id','students use index (id)',array('admin_id'=>$admin_id,'email'=>$this->input->post('email',TRUE)),1);
                    $prevRecdTeacher = $this->db_model->select_data('id','users use index (id)',array('email'=>$this->input->post('email',TRUE)),1);
                    
                    if(empty($prevRecd) && empty($prevRecdTeacher)){
                        $siteData = array();
                        $siteData['word_for_enroll'] = $this->common->enrollWord;
                        unset($data_arr['type']);
                        $data_arr['admin_id'] = $admin_id;            
                        $data_arr['login_status'] = 0;
                        $lastrecord = $this->db_model->select_data('id','students use index (id)',array('admin_id'=>$admin_id),1,array('id','desc'));             
                        if(!empty($lastrecord)){
                            $last_id = $lastrecord[0]['id'];
                        }else{
                            $last_id = 0;
                        }
                        
                        $password = $siteData['word_for_enroll'].$admin_id.$last_id.rand(1000,5000);
                        $enrolid = $siteData['word_for_enroll'].$admin_id.$last_id.rand(10,100);
                        $data_arr['enrollment_id'] = $enrolid;
                        $data_arr['password'] = md5($password);
                        $data_arr['admission_date'] = date('Y-m-d');
                        $data_arr['dob'] = date('Y-m-d',strtotime($data_arr['dob']));
                        $data_arr['status'] = 1;
    
                        //pic upload
                        if(isset($_FILES['stu_pic']) && !empty($_FILES['stu_pic']['name'])){
                            if ($this->upload->do_upload('stu_pic')){
                                $uploaddata = $this->upload->data();
                                $pic = $uploaddata['raw_name'];
                                $pic_ext = $uploaddata['file_ext'];
                                $image_name = $pic.'_'.date('ymdHis').$pic_ext;
                                rename('./uploads/students/'.$pic.$pic_ext,'./uploads/students/'.$image_name);
                                $data_arr['image'] = $image_name;
                            }else{
                                $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                                die();
                            }
                        }else{
                        $data_arr['image']='student_img.png';
                        }
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('students',$data_arr);
                        if($ins){
                            $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$this->input->post('batch_id',TRUE)),'plus',1);
                            $resp = array('status'=>'1', 'msg' => $this->input->post('name',TRUE).$this->lang->line('ltr_added_msg'),'enroll_id'=>$enrolid, 'password'=>$password);
                            //send email
                            $title = $this->db_model->select_data('site_title','site_details','',1,array('id','desc'))[0]['site_title'];
                            $subj = $title.'- '.$this->lang->line('ltr_credentials');
                            $em_msg = $this->lang->line('ltr_hey').' '.ucwords($this->input->post('name',TRUE)).', '.$this->lang->line('ltr_congratulation').' <br/><br/>'.$this->lang->line('ltr_successfully_enrolled').'<br/><br/>'.$this->lang->line('ltr_login_details').'<br/><br/> '.$this->lang->line('ltr_enrolment_id').' : '.$enrolid.'<br/><br/>'.$this->lang->line('ltr_password').' : '.$password.'';
                            $to_male =$this->input->post('email',TRUE);
                            $this->SendMail($to_male,$subj,$em_msg);
                        }
                    }else{
                        $resp = array('status'=>'0', 'msg' => $this->lang->line('ltr_email_already_msg')); 
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }  
    }
    
    function change_student_batch(){  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('batch_id',TRUE))){          
                $admin_id = $this->session->userdata('uid');
                $prevRecd = $this->db_model->select_data('batch_id','students use index (id)',array('admin_id'=>$admin_id,'id'=>$this->input->post('id',TRUE)),1);
                $updt = $this->db_model->update_data_limit('students',$this->security->xss_clean(array('batch_id'=>$this->input->post('batch_id',TRUE))),array('id'=>$this->input->post('id',TRUE)),1);
                if($updt){
                    if($prevRecd[0]['batch_id']!=$this->input->post('batch_id',TRUE)){
                        $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$this->input->post('batch_id',TRUE)),'plus',1);
                        $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$prevRecd[0]['batch_id']),'minus',1);
                    }
                    $resp = array('status'=>'1'); 
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function update_student_pass(){  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('password',TRUE))){
                $updt = $this->db_model->update_data_limit('students',$this->security->xss_clean(array('password'=>md5($this->input->post('password',TRUE)))),array('id'=>$this->input->post('id',TRUE)),1);
                if($updt){
                    $resp = array('status'=>'1'); 
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    

    /********   Student Manage End   ********/
    /********   subject Manage Start   ********/

    function subject_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);

            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('subject_name',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $subject_data = $this->db_model->select_data('*','subjects use index (id)',array('admin_id'=>$this->session->userdata('uid')),$limit,array('id','desc'),$like);
    
            if(!empty($subject_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($subject_data as $subject){
                    $chapterData = $this->db_model->select_data('id,chapter_name','chapters use index (id)',array('subject_id'=>$subject['id']),'',array('id','desc'));           
                    $allchapters = '';
                    foreach($chapterData as $chaptr){
                        $chapter_nameWord = $this->readMoreWord($chaptr['chapter_name'], 'Chapter name','15');
                        
                        $allchapters .= '<p class="chapter_wrap"><span>'.$chapter_nameWord.'</span><span class="ChapterEditDltWrap" data-word="'.$chaptr['chapter_name'].'" data-id="'.$chaptr['id'].'" data-sid="'.$subject['id'].'"><span class="editChapterName"><i class="icofont-edit"></i></span><span class="deleteChapterName"><i class="fa fa-times"></i></span></span></p>';
                    }
    
                    
                    if($subject['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton"  data-id="'.$subject['id'].'" data-table ="subjects" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$subject['id'].'" data-table ="subjects" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $action = '<p class="actions_wrap"><a class="edit_SbjectChaptr btn_edit" data-id="'.$subject['id'].'"><i class="fa fa-edit"></i></a>
                    <a class="deleteSubject btn_delete" data-id="'.$subject['id'].'"><i class="fa fa-trash"></i></a>
                    <a class="addChapers" data-id="'.$subject['id'].'" title="add chapter"><i class="fa fa-plus""></i></a></p>';
    
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$subject['id'].'">',
                                $count,
                                $subject['subject_name'],
                                $allchapters,
                                $statusDrop,
                                $action   
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('subjects use index (id)',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function add_subject(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $prevdata =  $this->db_model->select_data('id','subjects use index (id)',array('subject_name'=>$this->input->post('name',TRUE)),1);
                if(!empty($this->input->post('id',TRUE))){
                    if(empty($prevdata) || ($prevdata[0]['id'] == $this->input->post('id',TRUE))){
                        $data_arr = array(
                            'subject_name'	=>	trim($this->input->post('name',TRUE))
                        );
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->update_data_limit('subjects',$data_arr,array('id'=>$this->input->post('id',TRUE)),1);
                        if($ins==true){
                            $resp = array('status'=>'1'); 
                        }else{
                            $resp = array('status'=>'0'); 
                        }
                    }else{
                        $resp = array('status'=>'2'); 
                    }
                }else{
                    if(empty($prevdata)){
                        $data_arr = array(
                            'subject_name'	=>	trim($this->input->post('name',TRUE)),
                            'status'	=>	1,
                            'admin_id' => $this->session->userdata('uid')
                        ); 
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('subjects',$data_arr);
                        if($ins==true){
                            $resp = array('status'=>'1'); 
                        }else{
                            $resp = array('status'=>'0'); 
                        }
                    }else{
                        $resp = array('status'=>'2'); 
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function deleteSubjects(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $res = $this->db_model->delete_data('subjects',array('id'=>$this->input->post('id',TRUE)));
                if($res){    
                    $this->db_model->delete_data('chapters',array('subject_id'=>$this->input->post('id',TRUE)));        
                    $this->db_model->delete_data('questions',array('subject_id'=>$this->input->post('id',TRUE)));        
                    $resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_char_qu_msg'));
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
    
    function add_chapter(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $chapters = explode(',',$this->input->post('name',TRUE));
                foreach($chapters as $chpt){
                    if(!empty($chpt)){
                        $prevData = $this->db_model->select_data('id','chapters use index (id)',array('subject_id'=>$this->input->post('sid',TRUE),'chapter_name'=>trim($chpt)));
                        if(empty($prevData)){
                            $this->db_model->insert_data('chapters',$this->security->xss_clean(array('chapter_name'=>trim($chpt),'subject_id'=>$this->input->post('sid',TRUE),'status'=>1,'no_of_questions'=>0)));
                        }else{
                            
                            $resp = array('status'=>'2','char'=>trim($chpt));
                            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                            die();
                        }
                    }
                }
                $resp = array('status'=>'1');
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function edit_chapter(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $prevchapt =  $this->db_model->select_data('id','chapters use index (id)',array('chapter_name'=>$this->input->post('name',TRUE),'subject_id'=>$this->input->post('sid',TRUE)),1);
                if(empty($prevchapt) || ($prevchapt[0]['id'] == $this->input->post('id',TRUE))){
                    $prevData = $this->db_model->select_data('id','chapters use index (id)',array('subject_id'=>$this->input->post('sid',TRUE),'id'=>$this->input->post('id',TRUE)),1);
                    if(!empty($prevData)){
                        $res = $this->db_model->update_data_limit('chapters',$this->security->xss_clean(array('chapter_name'=>trim($this->input->post('name',TRUE)))),array('subject_id'=>$this->input->post('sid',TRUE),'id'=>$this->input->post('id',TRUE)),1);
                        if($res)
                            $resp = array('status'=>'1');
                        else
                            $resp = array('status'=>'0');
                    }else{
                        $resp = array('status'=>'0');
                    }
                }else{
                    $resp = array('status'=>'2');
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function deleteChapter(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $res = $this->db_model->delete_data('chapters',array('id'=>$this->input->post('id',TRUE),'subject_id'=>$this->input->post('sid',TRUE)));
                if($res){        
                    $this->db_model->delete_data('questions',array('subject_id'=>$this->input->post('sid',TRUE),'chapter_id'=>$this->input->post('id',TRUE)));        
                    $resp = array('status'=>'1', 'msg' => $this->lang->line('ltr_char_qus_msg'));
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_char_qus_msg');
        }
    }

    /********   subject Manage End   ********/
    /********   Question Manage Start   ********/

    function question_table($page){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('question',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $role = $this->session->userdata('role');
            if($role == '1'){  
                $profile = 'admin';
            }
            if($role == '3'){  
                $profile = 'teacher';
            }
    
            $cond = '';
            if($role == '1'){
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else{
                $cond = array('admin_id'=>$this->session->userdata('admin_id'),'added_by'=>$this->session->userdata('uid'));
            }
    
            if($page == 'exam'){
                if($role == '1'){
                    $cond = array('admin_id'=>$this->session->userdata('uid'));
                }else{
                    $cond = array('admin_id'=>$this->session->userdata('admin_id'));
                }
            }else{
                if($role == '1'){
                    $cond = array('admin_id'=>$this->session->userdata('uid'));
                }else{
                    $cond = array('admin_id'=>$this->session->userdata('admin_id'),'added_by'=>$this->session->userdata('uid'));
                }
            }
            
            if(isset($get['subject']) || isset($get['chapter'])){
                if($get['subject']!='' && $get['chapter']!=''){   
                    $cond['subject_id'] = $get['subject'];   
                    $cond['chapter_id'] = $get['chapter'];  
                }else if($get['subject']!=''){
                    $cond['subject_id'] = $get['subject'];   
                }
            }
    
            if(isset($get['word'])){
                if($get['word']!=''){
                    $like = array('question',$get['word']);  
                }
            }
    
            $question_data = $this->db_model->select_data('*','questions use index (id)',$cond,$limit,array('id','desc'),$like);
    
            if(!empty($question_data)){
                
                foreach($question_data as $question){
    
                    $subject = $this->db_model->select_data('subject_name','subjects use index (id)',array('id'=>$question['subject_id']),1)[0]['subject_name'];
                    $chapter_data = $this->db_model->select_data('chapter_name','chapters use index (id)',array('id'=>$question['chapter_id']),1);
                    if(!empty($chapter_data)){
                        $chapter=$chapter_data[0]['chapter_name'];
                    }else{
                        $chapter="";

                    }
                    $added_by = $this->db_model->select_data('name','users use index (id)',array('id'=>$question['added_by']),1)[0]['name'];
                    
                    $optionArr = json_decode($question['options'],true);
                   //  echo $question['options'];
                   // print_r($optionArr);

                    $i = 'A';
                    $cnt = 1;
                    $options = '';
                    foreach($optionArr as $op){
                        $options .= '<p>'.$i.'. &nbsp;&nbsp; <span class="option_'.$cnt.'">'.$op.'</span></p>';
                        $i++;
                        $cnt++;
                    }
    
            
                    if($question['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$question['id'].'" data-table ="questions" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$question['id'].'" data-table ="questions" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $catDrop = '<select data-id="'.$question['id'].'" data-table ="questions" class="form-control changeCategory datatableSelect">
                        <option value="0" '.(($question['category'] == 0) ? 'selected':'').'>'.$this->lang->line('ltr_none').'</option>
                        <option value="1" '.(($question['category'] == 1) ? 'selected':'').'>'.$this->lang->line('ltr_imp').'</option>
                        <option value="2" '.(($question['category'] == 2) ? 'selected':'').'>'.$this->lang->line('ltr_vimp').'</option>
                    </select> ';
                    $action = '<p class="actions_wrap"><a  href="'.base_url().$profile.'/add-question/'.$question['id'].'" class="btn_edit"><i class="fa fa-edit"></i></a>
                    <a class="deleteData btn_delete" data-id="'.$question['id'].'" data-table="questions"><i class="fa fa-trash"></i></a></p>';
                    
                    $questionWord =$this->readMoreWord($question['question'], 'Question');
                    if($page == 'exam'){
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$question['id'].'">',
                            $count,
                            '<p class="descParaCls">'.$questionWord.'</p>',
                            $options,
                            $question['answer'],
                            $subject,
                            $chapter
                        ); 
                    }else{
                        if($this->session->userdata('role')==1){
                            $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$question['id'].'">',
                                $count,
                                '<p class="descParaCls">'.htmlspecialchars_decode($question['question']).'</p>',
                                $options,
                                $question['answer'],
                                $subject,
                                $chapter,
                                $statusDrop,
                                $catDrop,
                                $added_by,
                                date('d-m-Y h:i A',strtotime($question['added_on'])),
                                $action 
                            ); 
                        }else{
                            
                            $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$question['id'].'">',
                                $count,
                                '<p class="descParaCls">'.$questionWord.'</p>',
                                $options,
                                $question['answer'],
                                $subject,
                                $chapter,
                                $statusDrop,
                                $catDrop,
                                date('d-m-Y h:i A',strtotime($question['added_on'])),
                                $action 
                            ); 
                        }
                    }
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('questions use index (id)',$cond,'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function get_chapter(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('subject',TRUE))){
                $chapterData = $this->db_model->select_data('id,chapter_name,no_of_questions','chapters use index (id)',array('subject_id'=>$this->input->post('subject',TRUE)),'',array('id','desc'));
                $html = '<option value="">Select Chapter</option>';
                if(!empty($chapterData)){
                    foreach($chapterData as $chaptr){
                        if($chaptr['no_of_questions'] != 0){
                            $chcount = ' - ('.$chaptr['no_of_questions'].')';
                        }else{
                            $chcount = '';
                        }
                        
                            $html .= '<option value="'.$chaptr['id'].'">'.$chaptr['chapter_name'].'</option>';
                        
                    }
                }
                $teacherHtml = '';
                if(!empty($this->input->post('teacher',TRUE))){
                    $subject = $this->input->post('subject',TRUE);
                    $like = array('teach_subject','"'.$subject.'"');
                    $teacherData = $this->db_model->select_data('id,name','users use index (id)','','',array('id','desc'),$like);
                    //$teacherData);
                    $teacherHtml = '<option value="">'.$this->lang->line('ltr_select_teacher').'</option>';
                    if(!empty($teacherData)){
                        foreach($teacherData as $teachr){                          
                            $teacherHtml .= '<option value="'.$teachr['id'].'">'.$teachr['name'].'</option>';
                        }
                    }
                }
                $resp = array('status'=>1,'html'=>$html,'teacherHtml'=>$teacherHtml);
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function get_chapter_tech(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('subject',TRUE))){
				
				$batch_id = $this->session->userdata('batch_id');
				$subData = $this->db_model->select_data('id,teacher_id,chapter','batch_subjects use index (id)',array('subject_id'=>$this->input->post('subject',TRUE),'batch_id'=>$batch_id),'',array('id','desc'));
				if(empty($subData)){
					$resp = array('status'=>0,'html'=>'sdsd');
				}else{
					$chid = implode(',', json_decode($subData[0]['chapter']));
					$conn ="id in ($chid)";
					$chapterData = $this->db_model->select_data('id,chapter_name','chapters use index (id)',$conn,'',array('id','desc'));
					$html = '<option value="">'.$this->lang->line('ltr_select_chapter').'</option>';
					if(!empty($chapterData)){
						foreach($chapterData as $chaptr){
							$html .= '<option value="'.$chaptr['id'].'">'.$chaptr['chapter_name'].'</option>';
						}
					}
					$teacherHtml = '';
					if(!empty($subData)){
						
						$teacherData = $this->db_model->select_data('id,name','users use index (id)',array('id'=>$subData[0]['teacher_id']),'',array('id','desc'));
						//$teacherData);
						$teacherHtml = '<option value="">'.$this->lang->line('ltr_select_teacher').'</option>';
						if(!empty($teacherData)){
							foreach($teacherData as $teachr){                          
								$teacherHtml .= '<option value="'.$teachr['id'].'">'.$teachr['name'].'</option>';
							}
						}
					}
					$resp = array('status'=>1,'html'=>$html,'teacherHtml'=>$teacherHtml);
				}
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_question(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if($this->session->userdata('role') == 1){
                    $profile = 'admin';
                }else{
                    $profile = 'teacher';
                }
            if(!empty($this->input->post('question',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                unset($data_arr['option1']);
                unset($data_arr['option2']);
                unset($data_arr['option3']);
                unset($data_arr['option4']);
                //echo($this->input->post('options',TRUE));
                $data_arr['options'] = json_encode(array($this->input->post('option1',TRUE),$this->input->post('option2',TRUE),$this->input->post('option3',TRUE),$this->input->post('option4',TRUE)),JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                $prevQuestion = $this->db_model->select_data('id','questions use index (id)',array('question'=>$this->input->post('question',TRUE),'subject_id'=>$this->input->post('subject_id',TRUE),'chapter_id'=>$this->input->post('chapter_id',TRUE)),1);
                if(!empty($this->input->post('question_id',TRUE))){
                    if(empty($prevQuestion) || ($prevQuestion[0]['id'] == $this->input->post('question_id',TRUE))){
                        $prevData = $this->db_model->select_data('id,subject_id,chapter_id','questions use index (id)',array('id'=>$this->input->post('question_id',TRUE)),1);
                        unset($data_arr['question_id']);
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->update_data_limit('questions',$data_arr,array('id'=>$this->input->post('question_id',TRUE)),1);
                        if($ins==true){
                            if($prevData[0]['subject_id'] != $this->input->post('subject_id',TRUE)){
                                $this->db_model->update_with_increment('subjects','no_of_questions',array('id'=>$prevData[0]['subject_id']),'minus',1);
                                $this->db_model->update_with_increment('subjects','no_of_questions',array('id'=>$this->input->post('subject_id',TRUE)),'plus',1);
                            }
                            if($prevData[0]['chapter_id'] != $this->input->post('chapter_id',TRUE)){
                                $this->db_model->update_with_increment('chapters','no_of_questions',array('id'=>$prevData[0]['chapter_id']),'minus',1);
                                $this->db_model->update_with_increment('chapters','no_of_questions',array('id'=>$this->input->post('chapter_id',TRUE)),'plus',1);
                            }
        
                            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_question_updated_msg'),'url'=>base_url($profile.'/question-manage'));
                        }else{
                            $resp = array('status'=>0);
                        }
                    }else{
                        $resp = array('status'=>2,'msg'=>$this->lang->line('ltr_question_exists_msg'));
                    }
                }else{
                    if(empty($prevQuestion)){ 
                        unset($data_arr['question_id']);
                        if($this->session->userdata('role') == 1){
                            $data_arr['admin_id'] = $this->session->userdata('uid');
                        }else{
                            $data_arr['admin_id'] = $this->session->userdata('admin_id');
                        }
                        
                        $data_arr['added_by'] = $this->session->userdata('uid');
                        $data_arr['status'] = 1;
                       // $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('questions',$data_arr);
                        if($ins==true){
                            $this->db_model->update_with_increment('chapters','no_of_questions',array('id'=>$this->input->post('chapter_id',TRUE)),'plus',1);
                            $this->db_model->update_with_increment('subjects','no_of_questions',array('id'=>$this->input->post('subject_id',TRUE)),'plus',1);
                            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_question_added_msg'),'url'=>base_url($profile.'/question-manage'));
                        }else{
                            $resp = array('status'=>0);
                        }
                    }else{
                        $resp = array('status'=>2,'msg'=>$this->lang->line('ltr_question_exists_msg'));
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    /********   Question Manage End   ********/
    /********   Notice Manage Start   ********/

    function notice_table($type){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            $admin_id = $this->session->userdata('uid');
            if($type == 'common'){
                $cond = array('admin_id'=>$admin_id,'notice_for'=>'Both');
            }else if($type == 'student'){
                $cond = "admin_id = $admin_id AND (notice_for = 'Student' OR student_id != 0)";
            }else if($type == 'teacher'){
                $cond = "admin_id = $admin_id AND (notice_for = 'Teacher' OR teacher_id != 0)";
            }
            
            $notices = $this->db_model->select_data('*','notices use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($notices)){
                
                foreach($notices as $not){
                    
                    $descriptionWord =$this->readMoreWord($not['description'], $this->lang->line('ltr_description'));    
                    $action = '<p class="actions_wrap"><a class="deleteDataNotice btn_delete" data-id="'.$not['id'].'" data-table="notices"><i class="fa fa-trash"></i></a></p>';
                    if($not['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$not['id'].'" data-table ="notices" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$not['id'].'" data-table ="notices" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    
                    $userName = '';
                    if(empty($not['notice_for'])){
                        if($not['student_id'] != 0){
                            $userData = $this->db_model->select_data('name','students use index (id)',array('id'=>$not['student_id']),1);
                            if(!empty($userData))
                                $userName = $userData[0]['name'];
                        }else if($not['teacher_id'] != 0){
                            $userData = $this->db_model->select_data('name','users use index (id)',array('id'=>$not['teacher_id']),1);
                            if(!empty($userData))
                                $userName = $userData[0]['name'];
                        }
                    }

                    $dataarray[] = array(
                                $count,
                                $not['title'],
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
                                (empty($not['notice_for'])?$userName:(($not['notice_for']!='Both')?'All '.$not['notice_for']:$not['notice_for'])),
                                $statusDrop,
                                date('d-m-Y',strtotime($not['date'])),
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('notices use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function student_notice_table($id){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post();
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            if($this->session->userdata('role') == 1){
                $cond = array('admin_id'=>$this->session->userdata('uid'),'student_id'=> $id,'teacher_id'=>0,'notice_for'=>'');
            }else if($this->session->userdata('role') == 3){
               $cond = array('admin_id'=>$this->session->userdata('admin_id'),'student_id'=> $id,'added_by' =>$this->session->userdata('uid'),'teacher_id'=>0,'notice_for'=>''); 
            }
    
            $notices = $this->db_model->select_data('*','notices use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($notices)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($notices as $not){
                    
                    $descriptionWord =$this->readMoreWord($not['description'], $this->lang->line('ltr_description'));
                    $action = '<p class="actions_wrap"><a class="deleteData btn_delete" data-id="'.$not['id'].'" data-table="notices"><i class="fa fa-trash"></i></a></p>';
    
                   if($not['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$not['id'].'" data-table ="notices" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$not['id'].'" data-table ="notices" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    
                    $added_by = $this->db_model->select_data('name','users use index (id)',array('id'=>$not['added_by']),1);
                    $dataarray[] = array(
                                $count,
                                $not['title'],
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
                                date('d-m-Y',strtotime($not['date'])),
                                (!empty($added_by)?$added_by[0]['name']:''),
                                $statusDrop,
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('notices use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function teacher_notice_table($id){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post();
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            $cond = array('admin_id'=>$this->session->userdata('uid'),'student_id'=> 0,'teacher_id'=>$id,'notice_for'=>'');
           
            $notices = $this->db_model->select_data('*','notices use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($notices)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($notices as $not){
                    $action = '<p class="actions_wrap"><a class="deleteData btn_delete" data-id="'.$not['id'].'" data-table="notices"><i class="fa fa-trash"></i></a></p>';
    
                    $statusDrop = '<select data-id="'.$not['id'].'" data-table ="notices" class="form-control changeStatus datatableSelect">
                        <option value="1" '.(($not['status'] == 1) ? 'selected':'').'>'.$this->lang->line('ltr_active').'</option>
                        <option value="0" '.(($not['status'] == 0) ? 'selected':'').'>'.$this->lang->line('ltr_inactive').'</option>
                    </select> ';
                   
                    $dataarray[] = array(
                                $count,
                                $not['title'],
                                '<p class="descParaCls">'.$not['description'].'</p>',
                                date('d-m-Y',strtotime($not['date'])),
                                $statusDrop,
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('notices use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_notice(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                $data_arr['date'] = date('Y-m-d');
                $data_arr['status'] = 1;
                $data_arr['added_at'] = date('Y-m-d H:i:s');
                $data_arr['admin_id'] = $this->session->userdata('uid');
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('notices',$data_arr);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>'Notice added sucessfully.');
                    $notice_for = $this->input->post('notice_for',TRUE);
                    if(($notice_for=='Student') || ($notice_for=='Both')){
                        $title ="New notice";
                        $where ="notice_common";
                        $batch_id='';
                        if(!empty($where)){
                            $this->push_notification_android($batch_id='',$title,$where);
                        }
                    }
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
   
    function add_personal_notice(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title'))){
                $data_arr = $this->input->post();
                $data_arr['date'] = date('Y-m-d');
                $data_arr['status'] = 1;
                $data_arr['read_status'] = 0;
                if($this->session->userdata('role') == 1){
                    $data_arr['admin_id'] = $this->session->userdata('uid');
                }else{
                    $data_arr['admin_id'] = $this->session->userdata('admin_id');
                }
                $data_arr['added_by'] = $this->session->userdata('uid');
                $data_arr['added_at'] = date('Y-m-d H:i:s');
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('notices',$data_arr);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_notice_added_msg'));
                    
                    $title ="New notice";
                    $where ="notice_personal";
                    $batch_id='';
                    $student_id =$this->input->post('student_id');
                    if(!empty($student_id)){
                        $this->push_notification_android($batch_id='',$title,$where,$student_id);
                    }
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    /********   Notice Manage End   ********/
    /********   Vacancy Manage Start   ********/

    function vacancy_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
    
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = array(array('description',$post['search']['value']));
            }else{
            $like = ''; 
            $or_like = ''; 
            }
    
            if($this->session->userdata('role') == 1){
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else{
                $cond = array('admin_id'=>$this->session->userdata('admin_id'));
            }
            
    
            $vacancy = $this->db_model->select_data('*','vacancy use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($vacancy)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($vacancy as $vac){
                    if($vac['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$vac['id'].'" data-table ="vacancy" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$vac['id'].'" data-table ="vacancy" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    
                    $descriptionWord =$this->readMoreWord($vac['description'],$this->lang->line('ltr_description'));
                    if($this->session->userdata('role') == 1){
                        $file ="'".$vac['files']."'";
                        $action = '<p class="actions_wrap"><a class="edit_vacancy btn_edit" data-id="'.$vac['id'].'" data-img='.$file.'><i class="fa fa-edit""></i></a><p class="actions_wrap"><a class="deleteData btn_delete" data-id="'.$vac['id'].'" data-table="vacancy"><i class="fa fa-trash"></i></a>
                        <a class="showinPopData btn_view" data-id="'.$vac['id'].'" data-table="vacancy"><i class="fa fa-eye"></i></a></p>';
    
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$vac['id'].'">',
                            $count,
                            $vac['title'],
                            '<p class="descParaCls">'.$descriptionWord.'</p>',
                            date('d-m-Y',strtotime($vac['start_date'])),
                            date('d-m-Y',strtotime($vac['last_date'])),
                            $vac['mode'],
                            $statusDrop,
                            $action
                        ); 
                    }else{
    
                        $action = '<p class="actions_wrap"><a class="showinPopData btn_view" data-id="'.$vac['id'].'" data-table="vacancy"><i class="fa fa-eye"></i></a></p>';
    
                        $dataarray[] = array(
                            $count,
                            $vac['title'],
                            '<p class="descParaCls">'.$descriptionWord.'</p>',
                            date('d-m-Y',strtotime($vac['start_date'])),
                            date('d-m-Y',strtotime($vac['last_date'])),
                            $vac['mode'],
                            $action
                        ); 
                    }
    
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('vacancy use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_vacancy(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
             
                //file upload
                
                $file_names = array();
                
                if(isset($_FILES) && !empty($_FILES['files']['name'][0])){
                    $fileArray = $_FILES;
                    $count = count($_FILES['files']['name']);
                    unset($_FILES['files']);
                    
                    $arr = [];
                    $totalFiles = [];
                    for($i=0;$i<$count;$i++){
                            
                        $arr['name'] = $fileArray['files']['name'][$i];
                        $arr['type'] = $fileArray['files']['type'][$i];
                        $arr['tmp_name'] = $fileArray['files']['tmp_name'][$i];
                        $arr['error'] = $fileArray['files']['error'][$i];
                        $arr['size'] = $fileArray['files']['size'][$i];
                        
                        $_FILES['newfile_'.$i] = $arr;
                
                        $config['upload_path'] = 'uploads/vacancy'; 
                        $config['allowed_types'] = 'jpg|jpeg|png|docx|doc|pdf';
                        $config['max_size'] = '0';
                
                        $this->load->library('upload',$config); 
                    
                        if($this->upload->do_upload('newfile_'.$i)){
                            $uploadData = $this->upload->data();
                            $filename = $uploadData['file_name'];
                            $totalFiles[] = $filename;
                        }else{
                            $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                            die();
                        }
                    }  
                    $data_arr['files'] = json_encode($totalFiles);
                }
            
                $data_arr['admin_id'] = $this->session->userdata('uid');
                $data_arr['start_date'] = date('Y-m-d',strtotime($data_arr['start_date']));
                $data_arr['last_date'] = date('Y-m-d',strtotime($data_arr['last_date']));
                $data_arr['status'] = 1;
                $data_arr['added_at'] = date('Y-m-d H:i:s');
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('vacancy',$data_arr);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=> $this->lang->line('ltr_vacancy_added_msg'));
                    $title =$this->lang->line('ltr_upcoming_exams');
                    $where ="exam";
                    $batch_id='';
                    if(!empty($where)){
                        $this->push_notification_android($batch_id='',$title,$where);
                    }
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function edit_vacancy(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
             
                //file upload
                
                $file_names = array();
                
                if(isset($_FILES) && !empty($_FILES['files']['name'][0])){
                    $fileArray = $_FILES;
                    $count = count($_FILES['files']['name']);
                    unset($_FILES['files']);
                    
                    $arr = [];
                    $totalFiles = [];
                    for($i=0;$i<$count;$i++){
                            
                        $arr['name'] = $fileArray['files']['name'][$i];
                        $arr['type'] = $fileArray['files']['type'][$i];
                        $arr['tmp_name'] = $fileArray['files']['tmp_name'][$i];
                        $arr['error'] = $fileArray['files']['error'][$i];
                        $arr['size'] = $fileArray['files']['size'][$i];
                        
                        $_FILES['newfile_'.$i] = $arr;
                
                        $config['upload_path'] = 'uploads/vacancy'; 
                        $config['allowed_types'] = 'jpg|jpeg|png|docx|doc|pdf';
                        $config['max_size'] = '0';
                
                        $this->load->library('upload',$config); 
                    
                        if($this->upload->do_upload('newfile_'.$i)){
                            $uploadData = $this->upload->data();
                            $filename = $uploadData['file_name'];
                            $totalFiles[] = $filename;
                        }else{
                            $resp = array('status'=>'0', 'msg' => $this->upload->display_errors());
                            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                            die();
                        }
                    }  
                    $data_arr['files'] = json_encode($totalFiles);
                }
            
                $data_arr['admin_id'] = $this->session->userdata('uid');
                $data_arr['start_date'] = date('Y-m-d',strtotime($data_arr['start_date']));
                $data_arr['last_date'] = date('Y-m-d',strtotime($data_arr['last_date']));
               
                $data_arr['added_at'] = date('Y-m-d H:i:s');
                $data_arr = $this->security->xss_clean($data_arr);
                $id = $data_arr['id'];
                unset($data_arr['id']);
               
                $ins = $this->db_model->update_data_limit('vacancy',$data_arr,array('id'=>$id),1);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_vacancy_updated_msg'));
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function showinPopData(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $fileData = $this->db_model->select_data('*','vacancy use index (id)',array('id'=>$this->input->post('id',TRUE)),1);
                
                if(!empty($fileData)){
                    $html = '<div class="gallery_maindiv">
                        <div class="gallery row">';
                    
                    if(!empty($fileData[0]['files'])){
                        $files = json_decode($fileData[0]['files'],true);
                        foreach($files as $file){
                            $ext = pathinfo(base_url('uploads/vacancy/'.$file))['extension'];
                            if($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg'){
                                $html .= '<div class="col-lg-3 col-md-4 col-sm-3 vacancy_dataShow">
                                            <a href="'.base_url('uploads/vacancy/'.$file).'" target="_blank">
                                                <img src="'.base_url('uploads/vacancy/'.$file).'"  width="100" height="100" alt="" />
                                            </a>
                                        </div>';
                            }else if($ext == 'pdf'){
                                $html .= '<div class="col-lg-3 col-md-4 col-sm-3 vacancy_dataShow">
                                            <a href="'.base_url('uploads/vacancy/'.$file).'" target="_blank">
                                                <img src="'.base_url('assets/images/pdf-icon.png').'"  width="100" height="100" alt="" />
                                            </a>
                                        </div>';
                            }else{
                                $html .= '<div class="col-lg-3 col-md-4 col-sm-3 vacancy_dataShow">
                                            <a href="'.base_url('uploads/vacancy/'.$file).'" target="_blank">
                                                <img src="'.base_url('assets/images/doc_img.png').'"  width="100" height="100" alt="" />
                                            </a>
                                        </div>';
                            }
                        }
                    }
                }
                $html .= '</div></div>';
                $resp = array('status'=>1,'html'=>$html);
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    /********   Vacancy Manage End   ********/
    /********   Video Manage Start   ********/

    function video_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            $role = $this->session->userdata('role');
            if($role =='student'){
            $like = array('batch','"'.$this->session->userdata('batch_id').'"');
            }else{
              $like ='';  
            }
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $or_like = ''; 
            }

            if(isset($get['subject']) || isset($get['chapter'])){
                if($get['subject']!='' && $get['chapter']!=''){   
                    $like = array('topic',urldecode($get['chapter'])); 
                }
            }
    
            if($role == 1){  
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else if($role == 3){
                $cond = array('added_by'=>$this->session->userdata('uid'), 'admin_id'=>$this->session->userdata('admin_id'));
            }else if($role == 'student'){
                $cond = array('admin_id'=>$this->session->userdata('admin_id'),'status'=>1);
            } 
    
            $videos = $this->db_model->select_data('*','video_lectures use index (id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
            
            if(!empty($videos)){
                
                foreach($videos as $vid){
                    $action = '<p class="actions_wrap"><a class="viewVideo btn_view" data-id="'.$vid['id'].'" data-url="'.$vid['url'].'" data-type="'.$vid['video_type'].'"><i class="fa fa-eye"></i></a>
                    <a class="deleteData btn_delete" data-id="'.$vid['id'].'" data-table="video_lectures"><i class="fa fa-trash"></i></a></p>';
    
                    
                    if($vid['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$vid['id'].'" data-table ="video_lectures" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$vid['id'].'" data-table ="video_lectures" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $added_by = $this->db_model->select_data('name','users use index (id)',array('id'=>$vid['added_by']),1)[0]['name'];
                    $batch_id = json_decode($vid['batch']);
                    $bach_name = '';
                    
                    foreach($batch_id as $bid){
                        $batch = $this->db_model->select_data('batch_name','batches use index (id)',array('id'=>$bid),1);
                        $bach_name .= $batch[0]['batch_name'].', ';
                    }
                   
                    $bach_name = rtrim($bach_name,", ");
                    if($role == 1){
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$vid['id'].'">',
                            $count,
                            $vid['title'],
                            (!empty($bach_name)?$bach_name:''),
                            $vid['subject'],
                            $vid['topic'],
                            $statusDrop,
                            $added_by,
                            $action
                        ); 
                    }else if($role == 3){
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$vid['id'].'">',
                            $count,
                            $vid['title'],
                            (!empty($batch)?$batch[0]['batch_name']:''),
                            $vid['subject'],
                            $vid['topic'],
                            $statusDrop,
                            $action
                        ); 
                    }else if($role == 'student'){
                        $action = '<p class="actions_wrap"><a class="viewVideo btn_view" data-id="'.$vid['id'].'" data-url="'.$vid['url'].'" data-type="'.$vid['video_type'].'"><i class="fa fa-eye"></i></a></p>';
                        $dataarray[] = array(
                            $count,
                            $vid['title'],
                            $vid['subject'],
                            $vid['topic'],
                            $action
                        ); 
                    }
                    
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('video_lectures use index (id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_video(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post();
               //print_r($data_arr);
                if($this->session->userdata('role') == 1){
                    $data_arr['admin_id'] = $this->session->userdata('uid');
                }else{
                    $data_arr['admin_id'] = $this->session->userdata('admin_id');
                }
                $data_arr['batch']=json_encode(explode(",", $data_arr['batch']));
                $data_arr['added_by'] = $this->session->userdata('uid');
                $data_arr['status'] = 1; 
                $data_arr['added_at'] = date('Y-m-d H:i:s'); 
               // $data_arr['added_at'] = date('Y-m-d H:i:s'); 
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('video_lectures',$data_arr);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_video_added_msg'));
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    /********   Video Manage End   ********/
    /********   Enquiry Manage Start  ********/

    function enquiry_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('name',$post['search']['value']);
                $or_like = array(array('mobile',$post['search']['value']),array('email',$post['search']['value']));
            }else{
               $like = ''; 
               $or_like = ''; 
            }
    
            $enquiry = $this->db_model->select_data('*','enquiry use index (id)','',$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($enquiry)){
                
                foreach($enquiry as $enq){
                    $messageWord =$this->readMoreWord($enq['message'], 'Message');
                    $action = '<p class="actions_wrap"><a class="deleteData btn_delete" data-id="'.$enq['id'].'" data-table="enquiry"><i class="fa fa-trash"></i></a></p>';
    
                    $dataarray[] = array(
                                $count,
                                $enq['name'],
                                $enq['mobile'],
                                $enq['email'],
                                $enq['subject'],
                                '<p class="descParaCls">'.$messageWord.'</p>',
                                date('d-m-Y',strtotime($enq['date'])),
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('enquiry use index (id)','','','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    /********   Enquiry Manage End   ********/
    /********   Teacher Manage Start  ********/

    function teacher_table(){
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
       
            if($post['search']['value'] != ''){
                $like = array('name',$post['search']['value']);
            }else{
               $like = ''; 
            }
            
            if($this->session->userdata('role') == '1'){
                $cond = array('parent_id'=>$this->session->userdata('uid'),'role'=>3);
            }else{
                $cond = array('parent_id'=>$this->session->userdata('admin_id'),'role'=>3);
            }
    
            $teachers = $this->db_model->select_data('*','users use index (id)',$cond,$limit,array('id','desc'),$like,'','');
             
            if(!empty($teachers)){
                
                foreach($teachers as $teach){
                    
                    $action = '<div class="actions_wrap_dot">
                    <span class="tbl_action_drop" >
                        <svg xmlns="https://www.w3.org/2000/svg" width="15px" height="4px">
        				<path fill-rule="evenodd" fill="rgb(77 74 129)" d="M13.031,4.000 C11.944,4.000 11.062,3.104 11.062,2.000 C11.062,0.895 11.944,-0.000 13.031,-0.000 C14.119,-0.000 15.000,0.895 15.000,2.000 C15.000,3.104 14.119,4.000 13.031,4.000 ZM7.500,4.000 C6.413,4.000 5.531,3.104 5.531,2.000 C5.531,0.895 6.413,-0.000 7.500,-0.000 C8.587,-0.000 9.469,0.895 9.469,2.000 C9.469,3.104 8.587,4.000 7.500,4.000 ZM1.969,4.000 C0.881,4.000 -0.000,3.104 -0.000,2.000 C-0.000,0.895 0.881,-0.000 1.969,-0.000 C3.056,-0.000 3.937,0.895 3.937,2.000 C3.937,3.104 3.056,4.000 1.969,4.000 Z"></path>
        				</svg>
        				<ul class="tbl_action_ul">
        				    
        				    <li>
        				        <a href="'.base_url('admin/teacher-progress/').$teach['id'].'">
        				            <span class="action_drop_icon">
        				                <i class="icofont-paper"></i>
        				            </span>
        				             '.$this->lang->line('ltr_progress').'
        				        </a>
        				    </li>
        				    <li>
        				        <a href="'.base_url('admin/teacher-academic-record/').$teach['id'].'">
        				            <span class="action_drop_icon">
        				                <i class="icofont-bars"></i>
        				            </span>'.$this->lang->line('ltr_academic_record').'
        				        </a>
        				    </li>
        				    <li>
        				        <a href="'.base_url().'admin/teacher-notice/'.$teach['id'].'">
        				            <span class="action_drop_icon">
        				                <i class="fas fa-bell"></i>
        				            </span>
        				            '.$this->lang->line('ltr_notice').'
        				        </a>
        				    </li>
							<li>
        				        <a href="'.base_url().'admin/doubts-class/'.$teach['id'].'">
        				            <span class="action_drop_icon">
        				                <i class="icofont-speech-comments" aria-hidden="true"></i>
        				            </span>
        				            '.$this->lang->line('ltr_doubts_class').'
        				        </a>
        				    </li>
        				    <li>
        				        <a href="javascript:void(0);" class="edit_teacher" data-id="'.$teach['id'].'" data-subject="'.implode(",",json_decode($teach['teach_subject'])).'" data-img="'.$teach['teach_image'].'">
        				            <span class="action_drop_icon">
        				                <i class="fa fa-edit"></i>
        				            </span>
        				            '.$this->lang->line('ltr_edit').'
        				        </a>
        				    </li>
        				    <li>
        				        <a  class="deleteData" data-id="'.$teach['id'].'" data-table="users" href="javascript:void(0);">
        				            <span class="action_drop_icon">
        				                <i class="fa fa-trash"></i>
        				            </span>
        				            '.$this->lang->line('ltr_delete').'
        				        </a>
        				    </li>
        				</ul>
                    </span>
                 </div>';
        
                    $statusDrop = '<select data-id="'.$teach['id'].'" data-table ="users" class="form-control changeStatus datatableSelect">
                        <option value="1" '.(($teach['status'] == 1) ? 'selected':'').'>'.$this->lang->line('ltr_active').'</option>
                        <option value="0" '.(($teach['status'] == 0) ? 'selected':'').'>'.$this->lang->line('ltr_inactive').'</option>
                    </select> ';
                    
                    if($teach['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$teach['id'].'" data-table ="users" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$teach['id'].'" data-table ="users" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    if (!empty($teach['teach_image'])){ 
                        $image = '<img src="'.base_url().'uploads/teachers/'.$teach['teach_image'].'" title="'.$teach['name'].'" class="view_large_image"></a>';
                    }else{
                        $image = '';
                    }
    
                    $newSubj = $newBatch = '';
                    
                    if(!empty($teach['teach_batch'])){
                        $batches =  $this->db_model->select_data('batch_name','batches use index (id)','id in ('.$teach['teach_batch'].')');
                        $batch = [];
                        for($i=0; $i<count($batches); $i++){
                            $batch[$i] = $batches[$i]['batch_name'];
                        }
                        $newBatch = implode(', ',$batch);
                    }
                    
                    if(!empty($teach['teach_subject'])){
                        $teach_subject_string =implode(",",json_decode($teach['teach_subject']));
                        $subjects =  $this->db_model->select_data('subject_name','subjects use index (id)','id in ('.$teach_subject_string.')');
                        $subject = [];
                        for($i=0; $i<count($subjects); $i++){
                            $subject[$i] = $subjects[$i]['subject_name'];
                        }
                        $newSubj = implode(',',$subject);
                    }
                    
                    $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$teach['id'].'">',
                                $count,
                                $image.$teach['name'],
                                '<p class="email">'.$teach['email'].'</p>',
                                $teach['teach_education'],
                                $teach['teach_gender'],
                                $newBatch,
                                $newSubj,
                                $statusDrop,
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('users use index (id)',$cond,'','',$like,'','');
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_teacher(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                if(!empty($this->input->post('teacher_id',TRUE))){
                    if(!empty($data_arr['password'])){
                        $data_arr['password'] = md5($data_arr['password']);
                    }else{
                        unset($data_arr['password']);
                    }
    
                    if(isset($_FILES['teach_image']) && !empty($_FILES['teach_image']['name'])){
                        $image = $this->upload_media($_FILES,'uploads/teachers/','teach_image');
                        if(is_array($image)){
                            $resp = array('status'=>'2', 'msg' => $image['msg']);
                            die();
                        }else{
                            $data_arr['teach_image'] = $image;
                        }
                    }
    
                    $id = $data_arr['teacher_id'];
                    unset($data_arr['teacher_id']);
                    $data_arr['teach_subject'] = json_encode($data_arr['teach_subject']);
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('users',$data_arr,array('id'=>$id),1);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>'Teacher updated sucessfully.');
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    $prevData =  $this->db_model->select_data('id','users use index (id)',array('email'=>$data_arr['email'],'role'=>3,'parent_id'=>$this->session->userdata('uid')),1);
                    $prevDataStu =  $this->db_model->select_data('id','students use index (id)',array('email'=>$data_arr['email'],'admin_id'=>$this->session->userdata('uid')),1);
                    if(empty($prevData) && empty($prevDataStu)){
                        unset($data_arr['teacher_id']);
                        $data_arr['parent_id'] = $this->session->userdata('uid');   
                        $data_arr['password'] = md5($data_arr['password']); 
                        $data_arr['teach_subject'] = json_encode($data_arr['teach_subject']);
                        $data_arr['role'] = 3;
                        $data_arr['status'] = 1;
    
                        if(isset($_FILES['teach_image']) && !empty($_FILES['teach_image']['name'])){
                            $image = $this->upload_media($_FILES,'uploads/teachers/','teach_image');
                            if(is_array($image)){
                                $resp = array('status'=>'2', 'msg' => $image['msg']);
                                die();
                            }else{
                                $data_arr['teach_image'] = $image;
                            }
                        }
                        $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('users',$data_arr);               
                        if($ins==true){
                            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_teacher_added_msg'));
                        }else{
                            $resp = array('status'=>0);
                        }
                    }else{
                        $resp = array('status'=>2,'msg'=> $this->lang->line('ltr_email_already_msg'));
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
 
    function extraclass_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('description',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            if($this->session->userdata('role')==1){
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else{
                $cond = array('admin_id'=>$this->session->userdata('admin_id'),'teacher_id'=>$this->session->userdata('uid'));
            }
    
            if(isset($get['teacher']) || isset($get['status'])){
                if($get['teacher']!='' && $get['status']!=''){
                    $cond['status'] = $get['status'];   
                    $cond['teacher_id'] = $get['teacher'];
                }else if($get['teacher']!=''){
                    $cond['teacher_id'] = $get['teacher'];
                }else if($get['status']!=''){
                    $cond['status'] = $get['status'];
                }
            }
    
            $classes = $this->db_model->select_data('*','extra_classes use index (id)',$cond,$limit,array('id','desc'),$like,'','');
    
            if(!empty($classes)){
                
                foreach($classes as $cls){
                   $descriptionWord =$this->readMoreWord($cls['description'], $this->lang->line('ltr_description'));
                    $teacher =  $this->db_model->select_data('name','users use index (id)', array('id'=>$cls['teacher_id']),1)[0]['name'];
                  $d="'".$cls['batch_id']."'";
                    if($this->session->userdata('role')==1){
                        $action = '<p class="actions_wrap"><a class="edit_extraclass btn_edit" data-id="'.$cls['id'].'" data-teacher="'.$cls['teacher_id'].'" data-batch='.$d.'><i class="fa fa-edit"></i></a>
                            <a class="deleteData btn_delete" data-id="'.$cls['id'].'" data-table="extra_classes"><i class="fa fa-trash"></i></a></p>';
                            if($cls['status']=="Complete"){
                                $complete_date=date('d-m-Y h:i A',strtotime($cls['completed_date_time']));
                            }else{
                                $complete_date="";
                            }
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$cls['id'].'">',
                            $count,
                            date('d-m-Y',strtotime($cls['date'])),
                            date('h:i A',strtotime($cls['start_time'])).' - '.date('h:i A',strtotime($cls['end_time'])),
                            '<p class="descParaCls">'.$descriptionWord.'</p>',
                            $teacher,
                            $cls['status'],
                            $complete_date,
                            $action
                        ); 
                    }else{
                        if($cls['date'] == date('Y-m-d') && $cls['status'] == 'Incomplete'){
                            $statusDrop = '<input type="checkbox" value="Complete" data-id="'.$cls['id'].'" class="extraClsCmplete">Mark as Complete';
                        }else{
                            $statusDrop = '';
                        }
                        if($cls['status']=="Complete"){
                                $complete_date=date('d-m-Y h:i A',strtotime($cls['completed_date_time']));
                            }else{
                                $complete_date="";
                            }
                        $dataarray[] = array(
                            $count,
                            date('d-m-Y',strtotime($cls['date'])),
                            date('h:i A',strtotime($cls['start_time'])).' - '.date('h:i A',strtotime($cls['end_time'])),
                            '<p class="descParaCls">'.$descriptionWord.'</p>',
                            $cls['status'],
                            $complete_date,
                            $statusDrop
                        ); 
                    }
    
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('extra_classes use index (id)',$cond,'','',$like,'','');
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function add_extracls(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('date',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                //print_r($this->input->post(NULL,TRUE));
                if(!empty($this->input->post('edit_id',TRUE))){
                    $id = $data_arr['edit_id'];
                    unset($data_arr['edit_id']);
                    $data_arr['batch_id']=json_encode($data_arr['batch_id']);
                    $data_arr['date'] = date('Y-m-d',strtotime($data_arr['date']));
                    $data_arr['start_time'] = date('H:i:s',strtotime($data_arr['start_time']));
                    $data_arr['end_time'] = date('H:i:s',strtotime($data_arr['end_time']));
                    $data_arr['added_at'] = date('Y-m-d H:i:s'); 
                    
                    $start_time =$data_arr['start_time'];
                    $end_time =$data_arr['end_time'];
                    $date = $data_arr['date'];
                    $idt = $this->input->post('teacher_id');
                    $cond = "(start_time <= CAST('$start_time' AS time) AND end_time >= CAST('end_time' AS time)) AND date = '$date' AND teacher_id = '$idt' AND id !='$id'";
                    $sem_time_teacher = $this->db_model->select_data('id','extra_classes use index (id)', $cond,1);
                    if((strtotime($data_arr['start_time'])) ==(strtotime($data_arr['end_time']))){
                        $resp = array('status'=>3);
                    }else{
                        if(empty($sem_time_teacher)){
                            $data_arr = $this->security->xss_clean($data_arr);
                            $ins = $this->db_model->update_data_limit('extra_classes',$data_arr,array('id'=>$id),1);
                            if($ins==true){
                                $resp = array('status'=>1,'msg'=>'Class updated sucessfully.');
                            }else{
                                $resp = array('status'=>0);
                            }
                        }else{
                            $resp = array('status'=>2);
                        }
                    }
                }else{
                    unset($data_arr['edit_id']);
                    $batch_aray= $this->input->post('batch_id[]');
                    $batch_id=implode(",",$batch_aray);
                    $data_arr['batch_id']=json_encode($data_arr['batch_id']);
                    $data_arr['admin_id'] = $this->session->userdata('uid');
                    $data_arr['date'] = date('Y-m-d',strtotime($data_arr['date']));
                    $data_arr['start_time'] = date('H:i:s',strtotime($data_arr['start_time']));
                    $data_arr['end_time'] = date('H:i:s',strtotime($data_arr['end_time'])); 
                    $data_arr['status'] = 'Incomplete';
                    $data_arr['added_at'] = date('Y-m-d H:i:s');
                    
                    $start_time =$data_arr['start_time'];
                    $end_time =$data_arr['end_time'];
                    $date = $data_arr['date'];
                    $id = $this->input->post('teacher_id');
                    $cond = "(start_time <= CAST('$start_time' AS time) AND end_time >= CAST('end_time' AS time)) AND date = '$date' AND teacher_id = '$id'";
                    $sem_time_teacher = $this->db_model->select_data('id','extra_classes use index (id)', $cond,1);
                    if((strtotime($data_arr['start_time'])) ==(strtotime($data_arr['end_time']))){
                        $resp = array('status'=>3,'data'=> $batch_id);
                    }else{
                        if(empty($sem_time_teacher)){
                            $data_arr = $this->security->xss_clean($data_arr);
                            $ins = $this->db_model->insert_data('extra_classes',$data_arr);  
                            
                            if($ins==true){
                                $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_class_updated_msg'));
                                $title ="View extra class";
                                $where ="extraclasses";
                                if(!empty($batch_id)){
                                    $this->push_notification_android($batch_id,$title,$where);
                                }
                            }else{
                                $resp = array('status'=>0,'data'=> $batch_id);
                            }
                        }else{
                            $resp = array('status'=>2,'data'=> $batch_id);
                        }
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function change_extraCls_status(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $ins = $this->db_model->update_Data_limit('extra_classes',$this->security->xss_clean(array('status'=>'Complete','completed_date_time'=>date('Y-m-d H:i:s'))),array('id'=>$this->input->post('id',TRUE)));
                if($ins){
                    $resp = array('status'=>1);
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    /********   Teacher Manage End   ********/
    /********   Exam Manage Start   ********/

    function exam_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('name',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            if($this->session->userdata('role')==1){
                $cond = array('admin_id'=>$this->session->userdata('uid'));
                $btchcond = array('admin_id'=>$this->session->userdata('uid'));
            }else{
                $admin_id = $this->session->userdata('admin_id');
                $batch_id = $this->session->userdata('batch_id');
                if(!empty($batch_id)){
                    $cond = "admin_id = $admin_id AND batch_id in ($batch_id)";
                }
                $btchcond = "admin_id = $admin_id AND id in ($batch_id)";
            }
    
            if(!empty($cond)){
                $all_exams = $this->db_model->select_data('*','exams use index (id)',$cond,$limit,array('id','desc'),$like,'','');
            }else{
                $all_exams = '';
            }
    
            if(!empty($all_exams)){
                $batch_array = $this->db_model->select_data('id,batch_name','batches use index (id)',$btchcond);
                foreach($all_exams as $exam){
                    
                    if($exam['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$exam['id'].'" data-table ="exams" data-status ="0" href="javascript:;"> Active </a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$exam['id'].'" data-table ="exams" data-status ="1" href="javascript:;"> Inactive </a></div>';
                    }
                    
                    $batchData = '';
                    if(!empty($batch_array)){
                        foreach($batch_array as $batch){
                            if($exam['batch_id'] == $batch['id']){
                                
                                $batchData = $batch['batch_name'];
                            }
                        }
                    }   
    
                    $date = '';
                    if($exam['mock_sheduled_date'] != '0000-00-00'){
                        $date = date('d-m-Y',strtotime($exam['mock_sheduled_date']));
                    }
                    $time = '';
                    if($exam['mock_sheduled_time'] != '00:00:00'){
                        $time = date('h:i A',strtotime($exam['mock_sheduled_time']));
                    }

                    $added_Data = $this->db_model->select_data('name','users use index (id)',array('id'=>$exam['added_by']));

                    if(!empty($added_Data)){
                        $added_by = $added_Data[0]['name'];
                    }else{
                        $added_by = '';
                    }
					$mockkp = $this->lang->line('ltr_mock_test_paper');
					$prakkp = $this->lang->line('ltr_practice_paper');
                    if($this->session->userdata('role')==1){
                        $action = '<p class="actions_wrap"><a href="'.base_url('admin/view-paper/'.$exam['id']).'" target="_blank" class="btn_view"><i class="fa fa-eye"></i></a>
                        <a class="deleteData btn_delete" data-id="'.$exam['id'].'" data-table="exams"><i class="fa fa-trash"></i></a></p>';
                        if($exam['format'] == 1){
                            $format ="Shuffle";
                        }else{
                            $format ="Fix";
                        }
                        
                        $dataarray[] = array(
                            '<input type="checkbox" class="checkOneRow" value="'.$exam['id'].'">',
                            $count,
                            (($exam['type'] == 1)?$mockkp:$prakkp),
                            $exam['name'],
                            $exam['total_question'],
                            $exam['time_duration'],
                            $format,
                            $batchData,
                            $date,
                            $time,
                            $added_by,
                            //$statusDrop,
                            $action
                        ); 
                    }else{
                       
                        if($exam['added_by'] == $this->session->userdata('uid')){
                            $action = '<p class="actions_wrap"><a href="'.base_url('teacher/view-paper/'.$exam['id']).'" target="_blank" class="btn_view"><i class="fa fa-eye"></i></a>
                            <a class="deleteData btn_delete" data-id="'.$exam['id'].'" data-table="exams"><i class="fa fa-trash"></i></a></p>';
                            if($exam['format'] == 1){
                            $format ="Shuffle";
                            }else{
                                $format ="Fix";
                            }
                            $batchData = $batchData;
                            $statusDrop = $statusDrop;
                        }else{
                            $action = '<p class="actions_wrap"><a href="'.base_url('teacher/view-paper/'.$exam['id']).'" target="_blank" class="btn_view"><i class="fa fa-eye"></i></a></p>';

                            if($exam['format'] == 1){
                            $format ="Shuffle";
                            }else{
                                $format ="Fix";
                            }
                            $batchData =$batchData;
							$ac = $this->lang->line('ltr_active');
						    $iac = $this->lang->line('ltr_inactive');
                            $statusDrop = (($exam['status'] == 1) ? $ac:$iac);
                        }
                        
    
                        $dataarray[] = array(
                            $count,
                            (($exam['type'] == 1)? $mockkp :$prakkp),
                            $exam['name'],
                            $exam['total_question'],
                            $exam['time_duration'],
                            $format,
                            $batchData,
                            $date,
                            $time,
                            $added_by,
                            //$statusDrop,
                            $action
                        ); 
                    }
    
                    $count++;
                }
                
                if(!empty($cond))
                    $recordsTotal = $this->db_model->countAll('exams use index (id)',$cond,'','',$like,'','');
                else
                    $recordsTotal = 0;
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function add_exam_paper(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                if($this->session->userdata('role')==1)
                    $data_arr['admin_id'] = $this->session->userdata('uid');
                else
                    $data_arr['admin_id'] = $this->session->userdata('admin_id');

                if($this->input->post('type',TRUE) == 1){
                    $data_arr['mock_sheduled_date'] = date('Y-m-d',strtotime($data_arr['mock_sheduled_date']));
                    $data_arr['mock_sheduled_time'] = date('H:i:s',strtotime($data_arr['mock_sheduled_time']));
                  
                    $admin_id = $this->session->userdata('uid');        
                    
                    $cond = array('type'=>$this->input->post('type',TRUE),'mock_sheduled_date'=>$data_arr['mock_sheduled_date'],'mock_sheduled_time'=>$data_arr['mock_sheduled_time'],'batch_id'=>$this->input->post('batch_id',TRUE),'admin_id'=>$admin_id,'status'=>1);
    
                    $prevData = $this->db_model->select_data('id','exams use index (id)',$cond,1);
                }else{
                    $data_arr['mock_sheduled_date'] = '';
                    $data_arr['mock_sheduled_time'] = '';
                    $prevData = array();
                }
    
                $data_arr['status'] = 1;
                $data_arr['format'] = 1;
                $data_arr['added_at'] = date('Y-m-d H:i:s');
                $data_arr['added_by'] = $this->session->userdata('uid');
                if($this->session->userdata('role') == 1){
                    $profile = 'admin';
                }else{
                    $profile = 'teacher';
                }
                if(empty($prevData)){
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->insert_data('exams',$data_arr);               
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_paper_added_msg'),'url'=>base_url($profile.'/exam-manage'));
                        
                        $batch_id = $this->input->post('batch_id',TRUE);
                        $paper_type =$this->input->post('type',TRUE);
                        if($paper_type==1){
                            $title =$this->lang->line('ltr_view_mock_paper');
                            $where ="mock_test";
                        }else{
                            $title =$this->lang->line('ltr_view_practice_paper');
                            $where ="practice";
                        }
                        if(!empty($batch_id)){
                            $this->push_notification_android($batch_id,$title,$where);
                        }
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    $resp = array('status'=>2,'msg'=>$this->lang->line('ltr_add_paper_already_msg'));
                }
                
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    /********   Exam Manage End   ********/
    /********   Result Manage Start   ********/

    function result_table($type){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
            
            if($type == 'practice'){
                $table_name = 'practice_result';
            }else{
                $table_name = 'mock_result';
            }
        
            if($post['search']['value'] != ''){
                $join_array = array('students',"students.name like '%".$post['search']['value']."%' AND students.id = $table_name.student_id");
            }else{
               $join_array = array('students',"students.id = $table_name.student_id");
            }
            
            $cond = '';
            $role = $this->session->userdata('role');
            if($role == '1'){
                $cond = array("$table_name.admin_id"=>$this->session->userdata('uid'));
            }else{
                $admin_id = $this->session->userdata('admin_id');
                $batch_id = $this->session->userdata('batch_id');
                if(!empty($batch_id)){
                    $cond = $table_name.".admin_id = $admin_id AND students.batch_id in ($batch_id)";
                }else{
                    $cond = '';
                }
            }
            $like = '';
            if(isset($get['month']) || isset($get['year'])){
                if($get['month']!='' && $get['year']!=''){ 
                    $datefiltr = $get['year'].'-'.$get['month'];
                    $like = array('date',$datefiltr);  
                }
            }
    
            if(isset($get['paper'])){
                if($get['paper']!=''){
                    $cond[$table_name.'.paper_id'] = $get['paper'];  
                }
            }
            if(isset($get['batch_id'])){
                if($get['batch_id']!=''){
                    $cond['students.batch_id'] = $get['batch_id'];  
                }
            }
    
            if($role == 1){
                $profile = 'admin';
            }else if($role == 3){
                $profile = 'teacher';
            }
            
            if(!empty($cond)){
                $result_data = $this->db_model->select_data("$table_name.*,students.name,students.image,students.enrollment_id", $table_name.' use index (id)',$cond,$limit,array("$table_name.id",'desc'),$like,$join_array);
            }else{
                $result_data = ''; 
            }
            //echo $this->db->last_query();
            if(!empty($result_data)){
                foreach($result_data as $result){
                   
                    if (!empty($result['image'])){ 
                        $image = '<img src="'.base_url().'uploads/students/'.$result['image'].'" title="'.$result['name'].'" class="view_large_image"></a>';
                    }else{
                        $image = '';
                    }
    
                    $attemptedQuestion = json_decode($result['question_answer'],true);
                    $rightCount = 0;
                    $wrongCount = 0;
                    if(!empty($attemptedQuestion)){
                    foreach($attemptedQuestion as $key=>$value){
                            $right_ansrs = $this->db_model->select_data('id,answer', 'questions use index (id)',array('id'=>$key));
                            if(!empty($right_ansrs)){
                                if(($key == $right_ansrs[0]['id']) && ($value == $right_ansrs[0]['answer'])){
                                    $rightCount++;
                                }else{
                                    $wrongCount++;
                                }
                            }
                    }
                }
                    
                    
                    $percentage = (($rightCount - ($wrongCount*0.25))*100)/$result['total_question'];
    
                    $url = base_url($profile.'/answer-sheet/'.$type.'/'.$result['id']);
                    
                    $action = '<p class="actions_wrap"><a href="'.$url.'" target="_blank" class="btn_view"><i class="fa fa-eye"></i></a>';
    
                    if($role == '1'){
                        $action .= '<a class="deleteData btn_delete" data-id="'.$result['id'].'" data-table="'.$table_name.'"><i class="fa fa-trash"></i></a></p>';
                    }else{
                        $action .= '</p>';
                    }
                    
                    $time_taken = '';
                    if($result['start_time']!="" || $result['submit_time']!=""){
                        $stime=strtotime($result['start_time']);
                        $etime=strtotime($result['submit_time']);
                        $elapsed = $etime - $stime;
                        $time_taken = gmdate("H:i", $elapsed);
                    }
                    if($role == '1'){
                        $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$result['id'].'">',
                        $count,
                        $image.$result['name'],
                        $result['enrollment_id'],
                        $result['paper_name'],
                        date('d-m-Y',strtotime($result['date'])),
                        date('h:i A',strtotime($result['start_time'])),
                        date('h:i A',strtotime($result['submit_time'])),
                        $result['total_question'],
                        $result['attempted_question'],
                        gmdate("H:i", $result['time_duration']*60),
                        $time_taken,
                        $rightCount,
                        number_format((float)$percentage, 2, '.', ''),
                        $action,
                    ); 
                    }else{
                        $dataarray[] = array(
                        
                        $count,
                        $image.$result['name'],
                        $result['enrollment_id'],
                        $result['paper_name'],
                        date('d-m-Y',strtotime($result['date'])),
                        date('h:i A',strtotime($result['start_time'])),
                        date('h:i A',strtotime($result['submit_time'])),
                        $result['total_question'],
                        $result['attempted_question'],
                        gmdate("H:i", $result['time_duration']*60),
                        $time_taken,
                        $rightCount,
                        number_format((float)$percentage, 2, '.', ''),
                        $action,
                    ); 
                    }
                    
                    
                    $count++;
                }
    
                if(!empty($cond))
                    $recordsTotal = $this->db_model->countAll($table_name.' use index (id)',$cond,'','','',$join_array);
                else 
                    $recordsTotal = 0;
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    /********   Result Manage End   ********/
    /********   Facility Manage Start   ********/
    
    function facility_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $facilities = $this->db_model->select_data('*','facilities use index (id)','',$limit,array('id','desc'),$like,'','');
    
            if(!empty($facilities)){
                
                foreach($facilities as $faci){
                    $descriptionWord =$this->readMoreWord($faci['description'], $this->lang->line('ltr_description'));
                    $action = '<p class="actions_wrap"><a class="edit_facility btn_edit" data-id="'.$faci['id'].'"><i class="fa fa-edit"></i></a>
                    <a class="deleteData btn_delete" data-id="'.$faci['id'].'" data-table="facilities"><i class="fa fa-trash"></i></a></p>';
    
                   
                    if($faci['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$faci['id'].'" data-table ="facilities" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$faci['id'].'" data-table ="facilities" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
                    $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$faci['id'].'">',
                                $count,
                                $faci['title'],
                                '<i class="'.$faci['icon'].'"></i>',
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
                                $statusDrop,
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('facilities use index (id)','','','',$like,'','');
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function add_facility(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                if(!empty($this->input->post('edit_id',TRUE))){
                    $id = $data_arr['edit_id'];
                    unset($data_arr['edit_id']);
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('facilities',$data_arr,array('id'=>$id),1);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_facility_updated_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    unset($data_arr['edit_id']);
                    $data_arr['status'] = '1';
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->insert_data('facilities',$data_arr);               
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_facility_added_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }  
    }

    /********   Facility Manage End   ********/
     /********   Homework Manage Start   ********/
    
    function homework_table($date=''){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('description',$post['search']['value']);
            }else{
               $like = ''; 
            }
            $uid = $this->session->userdata('uid');
            $cond = "teacher_id = $uid";
    
            if(isset($get['from_date']) || isset($get['to_date'])){
                if($get['from_date']!='' && $get['to_date']!=''){ 
                    $frm_date = $this->db->escape(date('Y-m-d',strtotime($get['from_date'])));
                    $to_date = $this->db->escape(date('Y-m-d',strtotime($get['to_date'])));
                    $cond .= " AND date >= $frm_date AND date <= $to_date";
                }
            }
           
            $homeworks = $this->db_model->select_data('*','homeworks use index (id)',$cond,$limit,array('id','desc'),$like,'','');
    
            if(!empty($homeworks)){
               
                foreach($homeworks as $home){
                    
                    $descriptionWord =$this->readMoreWord($home['description'], $this->lang->line('ltr_description'));
                    $action = '<p class="actions_wrap"><a class="edit_homework btn_edit" data-id="'.$home['id'].'" data-batch="'.$home['batch_id'].'" data-sub="'.$home['subject_id'].'"><i class="fa fa-edit"></i></a>
                    <a class="deleteData btn_delete" data-id="'.$home['id'].'" data-table="homeworks"><i class="fa fa-trash"></i></a></p>';
    
                    $batch_name = $this->db_model->select_data('batch_name','batches use index (id)',array('id'=>$home['batch_id']));
                    $subject_name = $this->db_model->select_data('subject_name','subjects use index (id)',array('id'=>$home['subject_id']));
                  
                    if(!empty($subject_name) && !empty($batch_name)){
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$home['id'].'">',
                                $count,
                                $batch_name[0]['batch_name'],
                                $subject_name[0]['subject_name'],
                                date('d-m-Y',strtotime($home['date'])),
                                '<p class="descParaCls">'.$descriptionWord.'</p>',
                                $action
                            ); 
                    }
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('homeworks use index (id)',$cond,'','',$like,'','');
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function add_homework(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('description',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                unset($data_arr['ci_csrf_token']);
                if(!empty($this->input->post('edit_id',TRUE))){
                    $id = $data_arr['edit_id'];
                    unset($data_arr['edit_id']);
                    $data_arr['date'] = date('Y-m-d',strtotime($data_arr['date']));
                    $data_arr['added_at'] = date('Y-m-d H:i:s');
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('homeworks',$data_arr,array('id'=>$id,'teacher_id'=>$this->session->userdata('uid'),'admin_id'=>$this->session->userdata('admin_id')),1);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_homework_updated_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    unset($data_arr['edit_id']);
                    $data_arr['date'] = date('Y-m-d',strtotime($data_arr['date']));
                    $data_arr['teacher_id'] = $this->session->userdata('uid');
                    $data_arr['admin_id'] = $this->session->userdata('admin_id');
                    $data_arr['added_at'] = date('Y-m-d H:i:s');
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->insert_data('homeworks',$data_arr);               
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_homework_added_msg'));
                        $batch_id = $this->input->post('batch_id');
                        $title =$this->lang->line('ltr_view_homework');
                        $where ="homework";
                        if(!empty($batch_id)){
                            $this->push_notification_android($batch_id,$title,$where);
                        }
                    }else{
                        $resp = array('status'=>0);
                    }
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    /********   Homework  Manage End   ********/
    /********   Gallery Manage Start   ********/

    function gallery_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
            
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $gallery = $this->db_model->select_data('*','gallery use index (id)','',$limit,array('id','desc'),$like,'','');
    
            if(!empty($gallery)){
                
                foreach($gallery as $gal){
                    if($gal['type'] == 'Video'){
                        $viewIcn = '<p class="actions_wrap"><a class="viewVideo btn_view" data-id="'.$gal['id'].'" data-url="'.$gal['video_url'].'"><i class="fa fa-eye"></i></a>';
                    }else{
                        $viewIcn = '<p class="actions_wrap"><a class="viewImage btn_view" data-id="'.$gal['id'].'" data-img="uploads/gallery/'.$gal['image'].'"><i class="fa fa-eye"></i></a>';
                    }
    
                    $action = $viewIcn.'<a class="deleteData btn_delete" data-id="'.$gal['id'].'" data-table="gallery" data-file="uploads/gallery/'.$gal['image'].'"><i class="fa fa-trash"></i></a></p>';
    
                   
                    
                    if($gal['status'] == 1){
                        $statusDrop = '<div class="admin_tbl_status_wrap"><a class="tbl_status_btn light_sky_bg changeStatusButton" data-id="'.$gal['id'].'" data-table ="gallery" data-status ="0" href="javascript:;">'.$this->lang->line('ltr_active').'</a></div>';
                    }else{
                        $statusDrop = '<div class="admin_tbl_status_wrap">
                    <a class="tbl_status_btn light_red_bg changeStatusButton" data-id="'.$gal['id'].'" data-table ="gallery" data-status ="1" href="javascript:;">'.$this->lang->line('ltr_inactive').'</a></div>';
                    }
    
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$gal['id'].'">',
                                $count,
                                $gal['title'],
                                $gal['type'],
                                $statusDrop,
                                $action
                            ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('gallery use index (id)','','','',$like,'','');
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }

    function add_gallery(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('title',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                $data_arr['status'] = 1;
                if($this->input->post('type',TRUE) == 'Image'){
                    if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                        $image = $this->upload_media($_FILES,'uploads/gallery/','image');
                        if(is_array($image)){
                            $resp = array('status'=>'2', 'msg' => $image['msg']);
                            die();
                        }else{
                            $data_arr['image'] = $image;
                        }
                    }
                    $data_arr['video_url'] = '';
                }else{
                    $data_arr['image'] = '';
                }
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('gallery',$data_arr);               
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_gallery').$this->input->post('type',TRUE).$this->lang->line('ltr_added_msg'));
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

     /********   Gallery Manage End   ********/

     /********   Profile Manage Start   ********/

    function admin_change_password(){  
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('new_pass',TRUE))){
                $res = $this->db_model->update_data_limit('users',$this->security->xss_clean(array('password'=>md5($this->input->post('new_pass',TRUE)))),array('id'=>$this->session->userdata('uid')),1);
                if($res){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_password_changed_msg'));
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }  
    }

    function update_teacher_profile(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('name',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
                $role = $this->session->userdata('role');
                if($role == 'student'){
                    $path = 'uploads/students/';
                    $table = 'students';
                    unset($data_arr['batch_name']);
                }else{
                    $path = 'uploads/teachers/';
                    $table = 'users';
                }
                if(isset($_FILES['image']) && !empty($_FILES['image']['name'])){
                    $image = $this->upload_media($_FILES,$path,'image');
                    if(is_array($image)){
                        $resp = array('status'=>'2', 'msg' => $image['msg']);
                        echo json_encode($resp,JSON_UNESCAPED_SLASHES);
                        die();
                    }else{
                        if($role == 'student'){
                            $data_arr['image'] = $image;
                        }else{
                            $data_arr['teach_image'] = $image;
                        }
                        
                        $this->session->set_userdata('profile_img',$image);
                    }
                }
                if($data_arr['password'] == ''){
                    unset($data_arr['password']);
                }else{
                    $data_arr['password'] = md5($data_arr['password']);
                }
                unset($data_arr['email']);
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->update_data_limit($table,$data_arr,array('id'=>$this->session->userdata('uid')));               
                if($ins==true){
                    $this->session->set_userdata('name',$data_arr['name']);
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_profile_updated_msg'));
                }else{
                    $resp = array('status'=>0,'data'=>$data_arr);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

     /********   Profile Manage End   ********/
     
    /********   Common start   ********/

    function change_status(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $ins = $this->db_model->update_Data_limit($this->input->post('table',TRUE),$this->security->xss_clean(array('status'=>$this->input->post('status',TRUE))),array('id'=>$this->input->post('id',TRUE)));
                if($ins){
                    
                    $resp = array('status'=>1);
                    $id = $this->input->post('id',TRUE);
                    $studData = $this->db_model->select_data('student_id','leave_management use index (id)',array('id'=>$id),1);
                    if(!empty($studData)){
                        if($studData[0]['student_id']){
                            $title ="Leave status";
                            $where ="Leave";
                            $batch_id='';
                            $student_id=$studData[0]['student_id'];
                            if(!empty($where)){
                                $this->push_notification_android($batch_id='',$title,$where,$student_id);
                            }
                        }
                    }
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function change_dropdown_Value(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $ins = $this->db_model->update_Data_limit($this->input->post('table',TRUE),$this->security->xss_clean(array($this->input->post('column',TRUE)=>$this->input->post('value',TRUE))),array('id'=>$this->input->post('id',TRUE)));
                if($ins){
                    $resp = array('status'=>1);
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }

    function deleteData(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $studData = '';
                if($this->input->post('table',TRUE) == 'students'){
                    $studData = $this->db_model->select_data('batch_id','students use index (id)',array('id'=>$this->input->post('id',TRUE)),1);
                }
                
                if($this->input->post('table',TRUE) == 'vacancy'){
                    $vacnData = $this->db_model->select_data('files','vacancy use index (id)',array('id'=>$this->input->post('id',TRUE)),1);
                    
                }
    
                if($this->input->post('table',TRUE) == 'questions'){
                    $questData = $this->db_model->select_data('subject_id,chapter_id','questions use index (id)',array('id'=>$this->input->post('id',TRUE)),1);
                }
                if($this->input->post('table',TRUE) == 'blog_comments'){
                   $res = $this->db_model->delete_data('blog_comments_reply',array('comment_id'=>$this->input->post('id',TRUE)));
                }
                $res = $this->db_model->delete_data($this->input->post('table',TRUE),array('id'=>$this->input->post('id',TRUE)));
                
                if($res){
                    if(!empty($studData) && $studData[0]['batch_id']!=''){
                        $this->db_model->update_with_increment('batches','no_of_student',array('id'=>$studData[0]['batch_id']),'minus',1);
                    }
                    
                    if(!empty($vacnData) && $vacnData[0]['files']!=''){
                        $path = FCPATH.'uploads/vacancy/';
                        $files = json_decode($vacnData[0]['files'],true);
                        foreach($files as $file){
                            if(file_exists($path.$file))
                                unlink($path.$file);
                        }
                    }
                    
                    $file = $this->input->post('file',TRUE);
                    if(isset($file) && ($this->input->post('file',TRUE) != '')){
                        unlink(FCPATH.$this->input->post('file',TRUE));
                    }
    
                    if(!empty($questData) && $questData[0]['subject_id']!=''){
                        $this->db_model->update_with_increment('subjects','no_of_questions',array('id'=>$questData[0]['subject_id']),'minus',1);
                        $this->db_model->update_with_increment('chapters','no_of_questions',array('id'=>$questData[0]['chapter_id']),'minus',1);
                    }

                    if($this->input->post('table',TRUE) == 'batches'){
                       $this->db_model->delete_data('batch_subjects',array('batch_id'=>$this->input->post('id')));
                      
                        $teacherData = $this->db_model->select_data('id,teach_batch','users use index (id)','FIND_IN_SET('.$this->input->post('id').', teach_batch) > 0');
                        
                        if(!empty($teacherData)){
                            foreach($teacherData as $teacher){
                                $newBatch = explode(',',$teacher['teach_batch']);
                                $key = array_search($this->input->post('id'),$newBatch);
                                unset($newBatch[$key]);
                                $this->db_model->update_data('users',array('teach_batch'=>implode(',',$newBatch)),array('id'=>$teacher['id']));
                            }
                        }
                    } 
    
                    $resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_deleted_msg'));
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function leave_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('subject',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }

            $role = $this->session->userdata('role');
            if($role == 1){  
                $cond = array('admin_id'=>$this->session->userdata('uid'));
            }else if($role == 3){
                $cond = array('teacher_id'=>$this->session->userdata('uid'), 'admin_id' => $this->session->userdata('admin_id'));
            }else if($role == 'student'){
                $cond = array('student_id'=>$this->session->userdata('uid'), 'admin_id' => $this->session->userdata('admin_id'));
            } 
    
            $leaves = $this->db_model->select_data('*','leave_management use index (user_id)',$cond,$limit,array('id','desc'),$like,'','',$or_like);
    
            if(!empty($leaves)){
                
                foreach($leaves as $leave){
                    $action = '<p class="actions_wrap"><a class="viewLeave btn_view" data-id="'.$leave['id'].'"><i class="fa fa-eye"></i></a></p>';
                    if($leave['status'] == 1){
                        $statusDrop='<span style="color:green;">'.$this->lang->line('ltr_approved').'</span>';
                    }elseif($leave['status'] == 2){
                         $statusDrop='<span style="color:red;">'.$this->lang->line('ltr_decline').'</span>';
                    }else{
                        $statusDrop='<span style="color:red;">'.$this->lang->line('ltr_pending').'</span>';
                    }
    
                    if($role == 1){
                        $dataarray[] = array(
                            $count,
                            $vid['title'],
                            (!empty($batch)?$batch[0]['batch_name']:''),
                            $vid['subject'],
                            $vid['topic'],
                            $statusDrop,
                            $added_by,
                            $action
                        ); 
                    }else{
                        $dataarray[] = array(
                            $count,
                            $leave['subject'],
                            date('d-m-Y',strtotime($leave['added_at'])),
                            date('d-m-Y', strtotime($leave['from_date'])),
                            date('d-m-Y', strtotime($leave['to_date'])),
                            $leave['total_days'],
                            $statusDrop,
                            $action
                        ); 
                    }
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('leave_management use index (user_id)',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function apply_leave(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('leave_msg',TRUE))){
                $data_arr = $this->input->post();
                $data_arr['from_date'] = date('Y-m-d', strtotime($this->input->post('from_date')));
                $data_arr['to_date'] = date('Y-m-d', strtotime($this->input->post('to_date')));
                $role = $this->session->userdata('role');
                if($role == '3')
                    $data_arr['teacher_id'] = $this->session->userdata('uid');
                else if($role == 'student')
                    $data_arr['student_id'] = $this->session->userdata('uid');
                $data_arr['admin_id'] = $this->session->userdata('admin_id');
                $data_arr['status'] = 0; 
                $Datediff = strtotime($this->input->post('to_date')) - strtotime($this->input->post('from_date'));               
                $data_arr['total_days'] = abs(round($Datediff / 86400)); 
                
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->insert_data('leave_management',$data_arr);
                if($ins==true){
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_leave_apply_msg'));
                }else{
                    $resp = array('status'=>0);
                }
            }else{
                $resp = array('status'=>'0'); 
            } 
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function get_leave_data(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $getLeave = $this->db_model->select_data('leave_msg, subject','leave_management use index (id)',array('id'=>$this->input->post('id')),1);
                if(!empty($getLeave)){
                    $resp = array('status'=>1,'data'=>$getLeave[0]);
                }else{
                    $resp = array('status'=>0);
                }
            }else{
                $resp = array('status'=>'0'); 
            }
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    
    function manage_leaves($type){ // if student (type = 1), if teacher (type = 2)
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        if($type == 1){
                $cond = array('student_id !='=>'0', 'leave_management.admin_id' => $this->session->userdata('uid'));
                $table = 'students';
                $join = array('students', 'leave_management.student_id = students.id');
            }else{
                $cond = array('teacher_id !='=>'0', 'leave_management.admin_id' => $this->session->userdata('uid'));
                $table = 'users';
                $join = array('users', 'leave_management.teacher_id = users.id');
            }
            if($post['search']['value'] != ''){
                $like = array($table.'.name',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            

            if(isset($get['id'])){
                if($get['id']!=''){   
                    if($type == 1){
                        $cond['student_id'] = $get['id'];  
                    }else{
                        $cond['teacher_id'] = $get['id'];  
                    }
                }
            }
    
            $users_leave = $this->db_model->select_data('leave_management.subject,leave_management.leave_msg,leave_management.total_days,leave_management.from_date,leave_management.to_date,leave_management.added_at,leave_management.status,leave_management.id as leave_id,'.$table.'.name','leave_management',$cond,$limit,array('leave_management.id','desc'),$like,$join,'',$or_like);
        
            if(!empty($users_leave)){
                
                foreach($users_leave as $leave){
                    $from_date = $leave['from_date'];
                    $current = strtotime(date("Y-m-d"));
                    $date    = strtotime($from_date);
                    
                    $action = '<p class="actions_wrap "><a class="viewLeave btn_view" data-id="'.$leave['leave_id'].'"><i class="fa fa-eye"></i></a></p>';
                    if(($current>$date) && ($leave['status']!=1)){
                        $statusDrop = ($leave['status'] == 2) ? '<span>'.$this->lang->line('ltr_decline').'</span>' : '<span>'.$this->lang->line('ltr_pending').'</span>';
                    }else{
                    $statusDrop = ($leave['status'] == 1) ? '<span style="color:green;">'.$this->lang->line('ltr_approved').'</span>' : '<select data-id="'.$leave['leave_id'].'" data-table ="leave_management" class="form-control changeStatus datatableSelect">
                        <option value="1" '.(($leave['status'] == 1) ? 'selected':'').'>'.$this->lang->line('ltr_approved').'</option>
                        <option value="2" '.(($leave['status'] == 2) ? 'selected':'').'>'.$this->lang->line('ltr_decline').'</option>
                        <option value="0" '.(($leave['status'] == 0) ? 'selected':'').'>'.$this->lang->line('ltr_pending').'</option>
                    </select>';
                    }  
                    $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$leave['leave_id'].'">',
                        $count,
                        $leave['name'],
                        
                        date('d-m-Y',strtotime($leave['added_at'])),
                        date('d-m-Y',strtotime($leave['from_date'])),
                        date('d-m-Y',strtotime($leave['to_date'])),
                        $leave['total_days'],
                        $statusDrop,
                        $action
                    ); 

                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('leave_management use index (user_id)',$cond,'','',$like,$join,'',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function get_student_name(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->get('q', true))){
                
                $user_name = $this->db_model->select_data('id,name','students',"name LIKE '%".$this->input->post('q')."%'");
                if(!empty($user_name)){
                    $resp = array('status'=>1, 'data'=>$user_name, 'message'=>'');
                }else{
                    $resp = array('status'=>2, 'message'=>$this->lang->line('ltr_no_result'));
                }
            }else{
                $resp = array('status'=>0);
            }
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function get_teacher_name(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->get('q', true))){
               
                $user_name = $this->db_model->select_data('id,name','users',"name LIKE '%".$this->input->post('q')."%' AND role = 3");
                if(!empty($user_name)){
                    $resp = array('status'=>1, 'data'=>$user_name, 'message'=>'');
                }else{
                    $resp = array('status'=>2, 'message'=>$this->lang->line('ltr_no_result'));
                }
            }else{
                $resp = array('status'=>0);
            }
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    function add_live_class_setting(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('batch',TRUE))){
                $setting_data = $this->db_model->select_data('*','live_class_setting',array('batch'=>$this->input->post('batch',TRUE)));
                if(empty($setting_data)){
                     $data_arr['batch'] = $this->input->post('batch',TRUE);
                    $data_arr['zoom_api_key'] = $this->input->post('zoom_api_key',TRUE);
                    $data_arr['zoom_api_secret'] = $this->input->post('zoom_api_secret',TRUE);
                    $data_arr['meeting_number'] = $this->input->post('meeting_number',TRUE);
                    $data_arr['password'] = $this->input->post('password',TRUE);
                    $data_arr['status'] = 1;
                    $data_arr['added_at'] = date('Y-m-d H:i:s');
                    $data_arr['admin_id'] = $this->session->userdata('uid');
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->insert_data('live_class_setting',$data_arr);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_class_added_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    $resp = array('status'=>2,'msg'=>$this->lang->line('ltr_batch_exists_msg'));
                }
                
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function add_live_class_Android(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('android_api_key',TRUE))){
                $setting_data = $this->db_model->select_data('*','zoom_api_credentials');
                if(empty($setting_data)){
                     $data_arr['android_api_key'] = $this->input->post('android_api_key',TRUE);
                    $data_arr['android_api_secret'] = $this->input->post('android_api_secret',TRUE);
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->insert_data('zoom_api_credentials',$data_arr);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_data_added_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                     $data_arr['android_api_key'] = $this->input->post('android_api_key',TRUE);
                    $data_arr['android_api_secret'] = $this->input->post('android_api_secret',TRUE);
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('zoom_api_credentials',$data_arr,array('id'=>1),1);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_data_updated_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }
                
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    function live_class_setting_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('batches.batch_name',$post['search']['value']);
            }else{
               $like = ''; 
            }
    
            $setting_data = $this->db_model->select_data('live_class_setting.*,batches.batch_name','live_class_setting',array('live_class_setting.admin_id'=>$this->session->userdata('uid')),$limit,array('id','desc'),$like,array('batches','batches.id=live_class_setting.batch'));
    
            if(!empty($setting_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($setting_data as $setting){
                    $action = '<p class="actions_wrap"><a class="edit_live_class btn_edit" title="Edit" data-id="'.$setting['id'].'" data-batch="'.$setting['batch'].'"><i class="fa fa-edit"></i></a>
                        <a class="deleteData btn_delete" title="Delete" data-id="'.$setting['id'].'" data-table="live_class_setting"><i class="fa fa-trash"></i></a>
                        <a class="btn_view add_live_class_admin" data-id="'.$setting['id'].'" data-batch="'.$setting['batch'].'"><i class="fa fa-users" aria-hidden="true" ></i></a>

                        </p>';
                    $dataarray[] = array(
                                $count,
                                $setting['batch_name'],
                                $setting['zoom_api_key'],
                                $setting['zoom_api_secret'],
                                $setting['meeting_number'],
                                $setting['password'],
                                $action   
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('batches use index (id)',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function edit_live_class_setting(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('batch',TRUE))){
                $setting_data = $this->db_model->select_data('*','live_class_setting',array('batch'=>$this->input->post('batch',TRUE),'id !='=>$this->input->post('live_class_id',TRUE)));
                if(empty($setting_data)){
                    
                    $data_arr['batch'] = $this->input->post('batch',TRUE);
                    $data_arr['zoom_api_key'] = $this->input->post('zoom_api_key',TRUE);
                    $data_arr['zoom_api_secret'] = $this->input->post('zoom_api_secret',TRUE);
                    $data_arr['meeting_number'] = $this->input->post('meeting_number',TRUE);
                    $data_arr['password'] = $this->input->post('password',TRUE);
                    $data_arr = $this->security->xss_clean($data_arr);
                    $ins = $this->db_model->update_data_limit('live_class_setting',$data_arr,array('id'=>$this->input->post('live_class_id',TRUE)),1);
                    if($ins==true){
                        $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_class_added_msg'));
                    }else{
                        $resp = array('status'=>0);
                    }
                }else{
                    $resp = array('status'=>2,'msg'=>$this->lang->line('ltr_batch_exists_msg'));
                }
                
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            } 
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    function live_class_list_teacher(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
           
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
                 
            }else{
                $limit = '';
                $count = 1;
            }
            if($post['search']['value'] != ''){
                $like = array('batches.batch_name',$post['search']['value']);
                
            }else{
               $like = ''; 
            }
            
            if(!empty($this->session->userdata('batch_id'))){
                 $admin_id = $this->session->userdata('admin_id');
            $batch_ids =$this->session->userdata('batch_id');
            $batCon = "batches.admin_id = $admin_id AND batches.id in ($batch_ids)";
            $setting_data = $this->db_model->select_data('live_class_setting.*,batches.batch_name','live_class_setting',$batCon,$limit,array('id','desc'),$like,array('batches','batches.id=live_class_setting.batch'));
            }else{
                $setting_data='';
            }
           

    
            if(!empty($setting_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
                foreach($setting_data as $setting){
                    $action = '<p class="actions_wrap">
                        <a class="btn_view add_live_class" data-id="'.$setting['id'].'"><i class="fa fa-users" aria-hidden="true"></i></a>
                        </p>';
                    $dataarray[] = array(
                                $count,
                                $setting['batch_name'],
                                $action   
                    ); 
                    $count++;
                }
                $recordsTotal = $this->db_model->countAll('batches use index (id)',array('admin_id'=>$this->session->userdata('uid')),'','',$like);
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function live_class_history_table(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array(array('users.name',$post["search"]["value"]),array('batches.batch_name',$post["search"]["value"]));
            }else{
               $like = ''; 
            }
    
            $setting_data = $this->db_model->select_data('live_class_history.*,users.name,batches.batch_name','live_class_history','',$limit,array('id','desc'),'',array('multiple',array(array('users','users.id=live_class_history.uid'),array('batches','batches.id=live_class_history.batch_id'))),'',$like);
    
            if(!empty($setting_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($setting_data as $setting){
                
                    $dataarray[] = array(
                                '<input type="checkbox" class="checkOneRow" value="'.$setting['id'].'">',
                                $count,
                                $setting['batch_name'],
                                date('d-m-Y',strtotime($setting['date'])),
                                $setting['start_time'].' - '.$setting['end_time'],
                                $setting['name'],
                               
                             
                    ); 
                    $count++;
                }
    
                $recordsTotal = $this->db_model->countAll('live_class_history','','','','',array('multiple',array(array('users','users.id=live_class_history.uid'),array('batches','batches.id=live_class_history.batch_id'))),'',$like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function change_category(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $ins = $this->db_model->update_Data_limit($this->input->post('table',TRUE),$this->security->xss_clean(array('category'=>$this->input->post('category',TRUE))),array('id'=>$this->input->post('id',TRUE)));
                if($ins){
                    $resp = array('status'=>1);
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    /********   Common End   ********/
    function insert_excell(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
	    require_once APPPATH.'third_party/phpexcel/PHPExcel.php';
        $this->excel = new PHPExcel(); 
    	$file_info = pathinfo($_FILES["result_file"]["name"]);
        $file_directory = "uploads/excel/";
        $new_file_name = date("d-m-Y ") . rand(000000, 999999) .".". $file_info["extension"];
        if($file_info["extension"]=='xlsx'){
            if(move_uploaded_file($_FILES["result_file"]["tmp_name"], $file_directory . $new_file_name))
            {   
                $file_type	= PHPExcel_IOFactory::identify($file_directory . $new_file_name);
                $objReader	= PHPExcel_IOFactory::createReader($file_type);
                $objPHPExcel = $objReader->load($file_directory . $new_file_name);
                $sheet_data	= $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
             array_shift($sheet_data);
                
                foreach($sheet_data as $data)
                {
                   
                	$check=$this->db_model->select_data('*','questions',array('question'=>trim($data['A'])));
                	if(!empty($check)){
                		continue;
                	}
                	switch ($data['F']) {
                      case $data['B']:
                        $answer='A';
                        break;
                      case $data['C']:
                        $answer='B';
                        break;
                      case $data['D']:
                        $answer='C';
                        break;
                      case $data['E']:
                        $answer='D';
                        break;
                    }
                		$data_arr=array(
            				'subject_id'=>$_POST['subject_id'],
            				'chapter_id'=>$_POST['chapter_id'],
            				'question'=>trim($data['A']),
            				'options'=>json_encode(array($data['B'],$data['C'],$data['D'],$data['E'])),
            				'answer'=>$answer
            			);
            			if($this->session->userdata('role') == 1){
                            $data_arr['admin_id'] = $this->session->userdata('uid');
                        }else{
                            $data_arr['admin_id'] = $this->session->userdata('admin_id');
                        }
                        
                        $data_arr['added_by'] = $this->session->userdata('uid');
                        $data_arr['status'] = 1;
            		    $data_arr = $this->security->xss_clean($data_arr);
                        $ins = $this->db_model->insert_data('questions',$data_arr);
                       
                            $this->db_model->update_with_increment('chapters','no_of_questions',array('id'=>$this->input->post('chapter_id',TRUE)),'plus',1);
                            $this->db_model->update_with_increment('subjects','no_of_questions',array('id'=>$this->input->post('subject_id',TRUE)),'plus',1);
            	}
            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_question_added_msg'));
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            $resp = array('status'=>0);
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }
        
    }else{
         echo $this->lang->line('ltr_not_allowed_msg');
    }
    }
    
    function add_attendance(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            
            $data= json_decode($this->input->post('ids',TRUE));
            for($i=0;$i<count($data);$i++){
                $data_arr['added_id'] = $this->session->userdata('uid');
                $data_arr['student_id'] = $data[$i];
                $data_arr['date'] = date('Y-m-d');
                $data_arr['time'] = date('h:i:s A');
                $check = $this->db_model->select_data('*','attendance',array('student_id'=>$data[$i],'date'=>date('Y-m-d')),'',array('id','desc'));
                if(empty($check)){
                    $ins = $this->db_model->insert_data('attendance',$data_arr);
                }
                
            }
            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_attendance_added_msg'));
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
    
    function add_attendance_extra(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            
            $data= json_decode($this->input->post('ids',TRUE));
            for($i=0;$i<count($data);$i++){
                $data_arr['added_id'] = $this->session->userdata('uid');
                $data_arr['student_id'] = $data[$i];
                $data_arr['date'] = date('Y-m-d');
                $data_arr['time'] = date('h:i:s A');
                $check = $this->db_model->select_data('*','extra_class_attendance',array('student_id'=>$data[$i],'date'=>date('Y-m-d')),'',array('id','desc'));
                if(empty($check)){
                    $ins = $this->db_model->insert_data('extra_class_attendance',$data_arr);
                }
                
            }
            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_attendance_added_msg'));
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
    function student_attendance($id,$month,$year){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post();
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
        $cond = array('student_id'=> $id);
        $like = array('date',$year.'-'.$month);
            $notices = $this->db_model->select_data('*','attendance',$cond,$limit,array('id','desc'),$like,'','',$or_like);
          //echo   $this->db->last_query();
            if(!empty($notices)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($notices as $not){
                   
                   if($this->session->userdata('role')==1){
                    $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$not['id'].'">',
                                $count,
                                $not['date'],
                                $not['time'],
                                'Present',
                                '<a class="deleteData btn_delete" data-id="'.$not['id'].'" data-table="attendance"><i class="fa fa-trash"></i></a></p>'
                            ); 
                    $count++;
                   }else{
                       $dataarray[] = array(
                           
                                $count,
                                $not['date'],
                                $not['time'],
                                'Present'
                            ); 
                    $count++;
                   }
                }
    
                $recordsTotal = $this->db_model->countAll('attendance',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    
    function student_attendance_extra_class($id,$month,$year){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post();
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
                $like = array('title',$post['search']['value']);
                $or_like = '';
            }else{
               $like = ''; 
               $or_like = ''; 
            }
        $cond = array('student_id'=> $id);
        $like = array('date',$year.'-'.$month);
            $notices = $this->db_model->select_data('*','extra_class_attendance',$cond,$limit,array('id','desc'),$like,'','',$or_like);
          //echo   $this->db->last_query();
            if(!empty($notices)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }
    
                foreach($notices as $not){
                   
                   if($this->session->userdata('role')==1){
                    $dataarray[] = array(
                        '<input type="checkbox" class="checkOneRow" value="'.$not['id'].'">',
                                $count,
                                $not['date'],
                                $not['time'],
                                'Present',
                                '<a class="deleteData btn_delete" data-id="'.$not['id'].'" data-table="attendance"><i class="fa fa-trash"></i></a></p>'
                            ); 
                    $count++;
                   }else{
                       $dataarray[] = array(
                           
                                $count,
                                $not['date'],
                                $not['time'],
                                'Present'
                            ); 
                    $count++;
                   }
                }
    
                $recordsTotal = $this->db_model->countAll('extra_class_attendance',$cond,'','',$like,'','',$or_like);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function multiDelete(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $data= json_decode($this->input->post('ids',TRUE));
            $table= $this->input->post('table_name',TRUE);
            $column= $this->input->post('column',TRUE);
            for($i=0;$i<count($data);$i++){
                $id = $data[$i];
                $this->db_model->delete_data($table,array($column=>$id));
            }
            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_data_delete_msg'));
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
    function generateCertificate(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            
            $data= json_decode($this->input->post('ids',TRUE));
            for($i=0;$i<count($data);$i++){
                $getBatch = $this->db_model->select_data('*','students',array('id'=>$data[$i]));
                $data_arr['added_id'] = $this->session->userdata('uid');
                $data_arr['student_id'] = $data[$i];
                $data_arr['date'] = date('Y-m-d');
                $data_arr['batch_id'] = $getBatch[0]['batch_id'];
                
                $check = $this->db_model->select_data('*','certificate',array('student_id'=>$data[$i],'batch_id'=>$getBatch[0]['batch_id']),'',array('id','desc'));
                if(empty($check)){
                    $ins = $this->db_model->insert_data('certificate',$data_arr);
                    //send email
                    $title = $this->db_model->select_data('site_title','site_details','',1,array('id','desc'))[0]['site_title'];
                    $subj = $title.'- '.$this->lang->line('ltr_new_certificate');
                    $filename = "certificate_".$data[$i]."_".$getBatch[0]['batch_id'].'.pdf';
                    $em_msg = $this->lang->line('ltr_hey').' '.ucwords($this->input->post('name',TRUE)).', '.$this->lang->line('ltr_congratulation').' <br/><br/>'.$this->lang->line('ltr_successfully_enrolled').'<br/><br/>'.$this->lang->line('ltr_earned_certificate').'<br/><br/><a href="'.base_url('uploads/certificate/').$filename.'" > '.$this->lang->line('ltr_link').'</a>';
                    $this->SendMail($getBatch[0]['email'],$subj,$em_msg);
                }
                
            }
            $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_certificate_generated_msg'));
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
    }
    
    function updateCertificateSetting(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('heading',false))){
                $data_arr = html_escape($this->input->post(NULL, false));
              
                if(isset($_FILES['signature_image']) && !empty($_FILES['signature_image']['name'])){
                    $logo = $this->upload_media($_FILES,'./uploads/site_data/','signature_image');
                    if(is_array($logo)){
                        $resp = array('status'=>'2', 'msg' => $logo['msg']);
                        die();
                    }else{
                        $data_arr['signature_image'] = $logo;
                    }
                }
                if(isset($_FILES['certificate_logo']) && !empty($_FILES['certificate_logo']['name'])){
                    $logo = $this->upload_media($_FILES,'./uploads/site_data/','certificate_logo');
                    if(is_array($logo)){
                        $resp = array('status'=>'2', 'msg' => $logo['msg']);
                        die();
                    }else{
                        $data_arr['certificate_logo'] = $logo;
                    }
                }
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->update_data_limit('certificate_setting',$data_arr,array('id'=>1),1);
                if($ins){
                    $resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_certificate_updated_msg'));
                }else{
                    $resp = array('status'=>'0');
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);   
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function updatePrivacyPolicy(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('description',false))){
                $data_arr = html_escape($this->input->post(NULL, false));
              
                $data_arr = $this->security->xss_clean($data_arr);
                $ins = $this->db_model->update_data_limit('privacy_policy_data',$data_arr,array('id'=>1),1);
                //echo $this->db->last_query();
                if($ins){
                    $resp = array('status'=>'1', 'msg' => $this->lang->line('ltr_privacy_updated_msg'));
                }else{
                    $resp = array('status'=>'0');
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);   
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    function get_subject_list(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('batch_id',TRUE))){
                $subjectData = $this->db_model->select_data('subjects.id,subjects.subject_name','subjects use index (id)',array('batch_id'=>$this->input->post('batch_id',TRUE)),'',array('id','desc'),'',array('batch_subjects','batch_subjects.subject_id=subjects.id'));
                $html = '<option value="">'.$this->lang->line('ltr_select_subject').'</option>';
                if(!empty($subjectData)){
                    foreach($subjectData as $subject){
                        
                            $html .= '<option value="'.$subject['id'].'">'.$subject['subject_name'].'</option>';
                        
                    }
                }
                $resp = array('status'=>1,'html'=>$html,);
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    function checkActiveLiveClass(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $batch_id = $this->session->userdata('batch_id');
		    $class_data = $this->db_model->select_data('users.name,users.teach_image AS teachImage,subjects.subject_name as subjectName,chapters.chapter_name as chapterName,live_class_history.end_time as endTime','live_class_history',array('batch_id'=>$batch_id),'1',array('live_class_history.id','desc'),'',array('multiple',array(array('users','users.id = live_class_history.uid'),array('subjects','subjects.id = live_class_history.subject_id'),array('chapters','chapters.id = live_class_history.chapter_id'))));
		   if(!empty($class_data)){
		       if(empty($class_data[0]['endTime'])){
		       $resp = array('status'=>1,'data'=>$class_data[0]);
		       }else{
		           $resp = array('status'=>0);
		       }
		   }else{
		       $resp = array('status'=>0);
		   }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function certificate_pdf_view(){
      
       if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('pdfb')) && !empty($this->input->post('pdfu')) ){
                $id=$this->input->post('pdfu',TRUE);
            	$batch_id = $this->input->post('pdfb',TRUE);
            
            	$data['student_certificate']=$this->db_model->select_data('*','certificate',array('student_id'=>$id,'batch_id'=>$batch_id),1,array('id','desc'));
            	if(!empty($data['student_certificate'])){
            	    $data['certificate_details']=$this->db_model->select_data('*','certificate_setting','',1,array('id','desc'));
                	$data['site_details_logo']=$this->db_model->select_data('site_logo','site_details','',1,array('id','desc'));
                	$data['student_details']=$this->db_model->select_data('name','students',array('id'=>$id),1,array('id','desc'));
                	$data['batchdata']=$this->db_model->select_data('batch_name','batches',array('id'=>$batch_id),1,array('id','desc'));
                	$data['baseurl'] = base_url();
                    $html=	$this->load->view("student/certificate_pdf",$data,true); 
                    $this->load->library('pdf'); // change to pdf_ssl for ssl
                    $filename = "certificate_".$id."_".$batch_id;
                    $result=$this->pdf->create($html);
                    
                    $file_path= explode("application",APPPATH);
                    file_put_contents($file_path[0].'uploads/certificate/'.$filename.'.pdf', $result);
                    $resp = array(
                        'fileName' => $filename.'.pdf',
                        'filesUrl' => base_url('uploads/certificate/'),
                        'status' => 1,
                        'msg' => 'Fetch Successfully.'
                    );
                }else{
                    $resp = array(
                        'status' => 0,
                        'msg' =>$this->lang->line('ltr_no_record_msg')
                    );
                }
            }else{
                $resp = array(
                    'status'=>0,
                    'msg'=>$this->lang->line('ltr_missing_parameters_msg')
                );
            }
            echo json_encode($resp,JSON_UNESCAPED_SLASHES);
       }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        }
        
    }
    
    public function push_notification_android($batch_id='',$title='',$where='',$student_id=''){
        
        if(!empty($batch_id)){
            $batchCon = "status = 1 AND token !='' AND batch_id in ($batch_id)";
	        $get_token = $this->db_model->select_data('token','students',$batchCon,'');
        }else{
            if(!empty($student_id)){
                 $get_token = $this->db_model->select_data('token','students',array('status'=>1,'token !='=>'', 'id'=>$student_id),'');
            }else{
                $get_token = $this->db_model->select_data('token','students',array('status'=>1,'token !='=>''),'');
            }
        }
        if(!empty($get_token)){
            $array_chunk = array_chunk($get_token,999);
            $array_count = count($array_chunk);
            for ($x = 0; $x < $array_count; $x++) {
                $device_id=array();
                foreach($array_chunk[$x] as $get_tokens){
                    if(!empty($get_tokens['token'])){
                        array_push($device_id,$get_tokens['token']);
                    }
                }
           
                   
                $url = 'https://fcm.googleapis.com/fcm/send';
                $api_key = 'AAAAFU0Nyks:APA91bFWu1zpzRasM60cqJjMvfcL5Uc667MP38b5CaYd5O3g-ioRYGtVSvBCdFUt5ea4H8eIDbPKNs98z5W0RxFfRsswy07p1EbSKRRlQkUA1b9sb_fBC2sHvFJZWhpILlZlOqz0_M4u';
                $message = array(
                        'title' => $title,
                        'body' => array(
                            'where'=>$where
                            )
                );
                $fields = array (
                    'registration_ids' =>$device_id,
                    'data' => array (
                    "message" => $message
                    )
                );
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:key='.$api_key
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
               
                if ($result === FALSE) {
                    die('FCM Send Error: ' . curl_error($ch));
                }
                curl_close($ch);
               
            }
             return $result;
        }
   
    }
    
    function readMoreWord($story_desc, $title='',$C_word='') {
        $chars = 90;
        if(!empty($C_word)){
            $chars =$C_word;
        }
        
        $count_word = strlen($story_desc);
        if($count_word>$chars){
            $readMore = '<a class="charaViewPopupModel" data-title="'.$title.'" data-word="'.$story_desc.'"  href="javascript:;">  .... </a>';
    	    $story_desc = substr($story_desc,0,$chars);  
    	    $story_desc = substr($story_desc,0,strrpos($story_desc,' '));  
    	    $story_desc = $story_desc.' '.$readMore;  
    	    return $story_desc;  
    	    
        }else{
            return $story_desc; 
        }
    }
	
	function student_doubts_class($tid) {
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
				
                $like = array('name',$post['search']['value']);
                $or_like = '';
				$userdata = $this->db_model->select_data('id','students','','',array('id','desc'),$like);
				$usersId =array();
				foreach($userdata as $key){
					array_push($usersId,$key['id']);
				}
				$uId = implode(', ', $usersId);
				$condd = "student_id in ($uId) AND teacher_id = $tid";
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            
			$cond = "teacher_id = $tid";
        		
            
         
            if(!empty($condd)){
				if(!empty($uId)){
					$doubts_data = $this->db_model->select_data('*','student_doubts_class',$condd,$limit,array('doubt_id','desc'));
				}else{
					$doubts_data = '';
				}
			}else{
				$doubts_data = $this->db_model->select_data('*','student_doubts_class',$cond,$limit,array('doubt_id','desc'),'','','',$or_like);
            }
            
            if(!empty($doubts_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }else if($role == '3'){
                    $profile = 'teacher';
                }
    
                foreach($doubts_data as $key=>$value){
                    
                    
                    $userName =$this->db_model->select_data('name,image','students',array('id'=>$value['student_id']),1);
                    
                    $batchName = $this->db_model->select_data('batch_name,start_date,end_date','batches',array('id'=>$value['batch_id']),1);
                    
                    if (!empty($userName)){ 
                        $image = '<img src="'.base_url().'uploads/students/'.$userName[0]['image'].'" title="'.$userName[0]['name'].'" class="view_large_image"></a>';
                    }else{
                        $image = '<img src="'.base_url().'assets/images/student_img.png" title="" class="view_large_image"></a>';
                    }
                    if(!empty($userName)){
                        $user_name =$this->readMoreWord($userName[0]['name'], 'Student Name',15);
                    }else{
                        $user_name='';
                    }

                    if(!empty($batchName)){
                        $batch_name =$this->readMoreWord(!empty($batchName[0]['batch_name'])?$batchName[0]['batch_name']:'', 'Batch Name',15);
                    }else{
                        $batch_name='';
                    }
                    $subName =$this->db_model->select_data('subject_name','subjects',array('id'=>$value['subjects_id']),1);
                    
                    if(!empty($subName)){
                        $sub_name =$this->readMoreWord($subName[0]['subject_name'], 'Subject Name',15);
                    }else{
                        $sub_name='';
                    }
                    
                    
                    
                    $chap_id =$value['chapters_id'];
                    $chapterCon = "id in ($chap_id)";
	                $chapterName = $this->db_model->select_data('chapter_name','chapters',$chapterCon,'');
	                
	                $chapter_Name =$this->readMoreWord($chapterName[0]['chapter_name'], 'Subject Name',15);
	                $apru = !empty($value['appointment_time'])?'yes':'no';
					$statusDrop = ($value['status'] == 1) ? '<span class="greentext">'.$this->lang->line('ltr_approved').'</span>' : '<select data-id="'.$value['doubt_id'].'" data-apru="'.$apru.'" data-table ="student_doubts_class" data-userid="'.$value['student_id'].'" class="form-control doubtStatus datatableSelect">
                        <option value="1" '.(($value['status'] == 1) ? 'selected':'').'>'.$this->lang->line('ltr_approve').'</option>
                        <option class="redtext" value="2" '.(($value['status'] == 2) ? 'selected':'').'>'.$this->lang->line('ltr_decline').'</option>
                        <option value="0" '.(($value['status'] == 0) ? 'selected':'').'>'.$this->lang->line('ltr_pending').'</option>
                    </select>';
					
					$doubtDate = ($value['appointment_date']!= '0000-00-00')?date('d-m-Y',strtotime($value['appointment_date'])):'';
					$doubtTime = !empty($value['appointment_time'])?$value['appointment_time']:'';
					$doubtDes = !empty($value['teacher_description'])?$value['teacher_description']:'';
					
					$action = '<p class="actions_wrap"><a class="appointmentDate " data-startDate="'.date('d-m-Y').'" data-endDate="'.date('d-m-Y',strtotime($batchName[0]['end_date'])).'" data-id="'.$value['doubt_id'].'" data-doubtDate="'.$doubtDate.'" data-doubtTime="'.$doubtTime.'" data-doubtDes="'.$doubtDes.'" data-userid="'.$value['student_id'].'"><i class="fa fa-calendar"></i></a> 
					<a class="viewDoubt btn_view" data-id="'.$value['doubt_id'].'"><i class="fa fa-eye"></i></a>
                        <a class="doubtDeleteData btn_delete" data-id="'.$value['doubt_id'].'" data-table="student_doubts_class"><i class="fa fa-trash"></i></a></p>';
					
					$des =$this->readMoreWord($value['users_description'], $this->lang->line('ltr_description'),15);
	                $dataarray[] = array(
                                $count,
                                $image.$user_name,
                                $batch_name,
                                $sub_name,
                                $chapter_Name,
                                $des,
                                $statusDrop,
								$action
                               
                        ); 
                
                $count++;
                }
               
                $recordsTotal = count($doubts_data);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function change_status_doubts(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                $ins = $this->db_model->update_Data_limit($this->input->post('table',TRUE),$this->security->xss_clean(array('status'=>$this->input->post('status',TRUE))),array('doubt_id'=>$this->input->post('id',TRUE)));
                if($ins){
                    $resp = array('status'=>1);
                    $user_id = $this->input->post('user_id',TRUE);
					if(!empty($user_id)){
						$title =$this->lang->line('ltr_doubts_class');
						$where ="doubtsClass";
						$batch_id='';
						$this->push_notification_android($batch_id='',$title,$where,$user_id);         
                    }
                }else{
                    $resp = array('status'=>0);
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function doubtsDeleteData(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('id',TRUE))){
                
                $res = $this->db_model->delete_data($this->input->post('table',TRUE),array('doubt_id'=>$this->input->post('id',TRUE)));
                
                if($res){
                    
                    $resp = array('status'=>'1', 'msg' =>$this->lang->line('ltr_deleted_msg'));
                }else{
                    $resp = array('status'=>'0'); 
                }
                echo json_encode($resp,JSON_UNESCAPED_SLASHES);
            }
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function edit_doubts(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            if(!empty($this->input->post('doubts_date',TRUE))){
                $data_arr = $this->input->post(NULL,TRUE);
            
                $data_up['admin_id'] = $this->session->userdata('uid');
                $data_up['appointment_date'] = date('Y-m-d',strtotime($data_arr['doubts_date']));
                $data_up['appointment_time'] = $data_arr['doubts_time'];
               
                $data_up['teacher_description'] = $data_arr['description'];
                $data_up['status'] = 1;
                $data_up = $this->security->xss_clean($data_up);
                $id = $data_arr['doubt_id'];
                               
                $ins = $this->db_model->update_data_limit('student_doubts_class',$data_up,array('doubt_id'=>$id),1);
                if($ins==true){
                    $user_id = $this->input->post('user_id',TRUE);
                    if(!empty($user_id)){
                        $title ="Doubts Class";
                        $where ="doubtsClass";
                        $batch_id='';
                        $this->push_notification_android($batch_id='',$title,$where,$user_id);         
                    }
                    $resp = array('status'=>1,'msg'=>$this->lang->line('ltr_updated_msg'));
                }else{
                    $resp = array('status'=>0);
                }
                
            }else{
				$student_id = $this->session->userdata('uid');
				$batch_id = $this->session->userdata('batch_id');
				$arrayData=array(
								'student_id'=>$student_id,
								'batch_id'=>$batch_id,
								);
				if(!empty($this->input->post('subject_id',TRUE))){
					$arrayData['subjects_id']= $this->input->post('subject_id',TRUE);
				}
				
				if(!empty($this->input->post('teacher_id',TRUE))){
					$arrayData['teacher_id']= $this->input->post('teacher_id',TRUE);
				}
				
				if(!empty($this->input->post('chapter_id',TRUE))){
					$arrayData['chapters_id']= $this->input->post('chapter_id',TRUE);
				}
				
				if(!empty($this->input->post('description',TRUE))){
					$arrayData['users_description']= $this->input->post('description',TRUE);
				}
				
				$checkusers = $this->db_model->select_data('doubt_id','student_doubts_class',array('teacher_id'=>$_POST['teacher_id'],'batch_id'=>$batch_id,'status'=>0,'student_id'=>$student_id,'subjects_id'=>$_POST['subject_id'],'chapters_id'=>$_POST['chapter_id']),'',array('doubt_id ','desc'));
				if(empty($checkusers)){
					$coundUsers = count($this->db_model->select_data('doubt_id ','student_doubts_class',array('teacher_id'=>$_POST['teacher_id'],'batch_id'=>$batch_id),'',array('doubt_id ','desc')));
					
					if($coundUsers<=10){
						
						$data_arr = $this->security->xss_clean($arrayData);
						$ins = $this->db_model->insert_data('student_doubts_class',$data_arr);
						$resp = array('status'=>1,'msg'=>$this->lang->line('ltr_doubt_request_msg'));
					}else{
						$resp = array('status'=>0,'msg'=>$this->lang->line('ltr_something_msg'));
					}
					
				}else{
					$resp = array('status'=>0,'msg'=> $this->lang->line('ltr_doubt_request_already_msg'));
				}
			} 
			echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function student_doubts_ask($id) {
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
				
                $like = array('name',$post['search']['value']);
                $or_like = '';
				$userdata = $this->db_model->select_data('id','students','','',array('id','desc'),$like);
				$usersId =array();
				foreach($userdata as $key){
					array_push($usersId,$key['id']);
				}
				$uId = implode(', ', $usersId);
				$condd = "student_id in ($uId) AND student_id = $id";
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            
			$cond = "student_id = $id";
        		
            
         
            if(!empty($condd)){
				if(!empty($uId)){
					$doubts_data = $this->db_model->select_data('*','student_doubts_class',$condd,$limit,array('doubt_id','desc'));
				}else{
					$doubts_data = '';
				}
			}else{
				$doubts_data = $this->db_model->select_data('*','student_doubts_class',$cond,$limit,array('doubt_id','desc'),'','','',$or_like);
            }
            
            if(!empty($doubts_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }else if($role == '3'){
                    $profile = 'teacher';
                }
    
                foreach($doubts_data as $key=>$value){
                    
                    
                    $userName =$this->db_model->select_data('name,image','students',array('id'=>$value['student_id']),1);
                    
                    $batchName = $this->db_model->select_data('batch_name,start_date,end_date','batches',array('id'=>$value['batch_id']),1);
                    
                    if (!empty($userName[0]['image'])){ 
                        $image = '<img src="'.base_url().'uploads/students/'.$userName[0]['image'].'" title="'.$userName[0]['name'].'" class="view_large_image"></a>';
                    }else{
                        $image = '<img src="'.base_url().'assets/images/student_img.png" title="'.$userName[0]['name'].'" class="view_large_image"></a>';
                    }
                    $user_name =$this->readMoreWord($userName[0]['name'], 'Student Name',15);
                    
                    $batch_name =$this->readMoreWord(!empty($batchName[0]['batch_name'])?$batchName[0]['batch_name']:'', 'Batch Name',15);
                    
                    $subName =$this->db_model->select_data('subject_name','subjects',array('id'=>$value['subjects_id']),1);
                    
                    $sub_name =$this->readMoreWord($subName[0]['subject_name'], 'Subject Name',15);
                    
                    
                    $chap_id =$value['chapters_id'];
                    $chapterCon = "id in ($chap_id)";
	                $chapterName = $this->db_model->select_data('chapter_name','chapters',$chapterCon,'');
	                
	                $chapter_Name =$this->readMoreWord($chapterName[0]['chapter_name'], 'Subject Name',15);
	                $apru = !empty($value['appointment_time'])?'yes':'no';
					if($value['status'] == 1) { $statusDrop= '<span class="greentext">'.$this->lang->line('ltr_approved').'</span>' ;}else if($value['status'] == 2){ $statusDrop ='<span class="redtext">'.$this->lang->line('ltr_decline').'</span>' ;}else{ $statusDrop =$this->lang->line('ltr_pending') ;}
                        
					$doubtDate = ($value['appointment_date']!= '0000-00-00')?date('d-m-Y',strtotime($value['appointment_date'])):'';
					$doubtTime = !empty($value['appointment_time'])?$value['appointment_time']:'';
					$doubtDes = !empty($value['teacher_description'])?$value['teacher_description']:'';
					
					$action = '<p class="actions_wrap"><a class="appointmentDate hide" data-startDate="'.date('d-m-Y').'" data-endDate="'.date('d-m-Y',strtotime($batchName[0]['end_date'])).'" data-id="'.$value['doubt_id'].'" data-doubtDate="'.$doubtDate.'" data-doubtTime="'.$doubtTime.'" data-doubtDes="'.$doubtDes.'"><i class="fa fa-calendar"></i></a> 
					<a class="viewDoubt btn_view" data-id="'.$value['doubt_id'].'"><i class="fa fa-eye"></i></a>
                        <a class="doubtDeleteData btn_delete" data-id="'.$value['doubt_id'].'" data-table="student_doubts_class"><i class="fa fa-trash"></i></a></p>';
					
					$des =$this->readMoreWord($value['users_description'], $this->lang->line('ltr_description'),15);
	                $dataarray[] = array(
                                $count,
                                $image.$user_name,
                                $batch_name,
                                $sub_name,
                                $chapter_Name,
                                $des,
                                $statusDrop,
								$action
                               
                        ); 
                
                $count++;
                }
               
                $recordsTotal = count($doubts_data);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function studentDoubtsAsk($id) {
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
				
                $like = array('name',$post['search']['value']);
                $or_like = '';
				$userdata = $this->db_model->select_data('id','users','','',array('id','desc'),$like);
				$usersId =array();
				foreach($userdata as $key){
					array_push($usersId,$key['id']);
				}
				$uId = implode(', ', $usersId);
				$condd = "teacher_id in ($uId) AND student_id = $id";
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            
			$cond = "student_id = $id";
        		
            
         
            if(!empty($condd)){
				if(!empty($uId)){
					$doubts_data = $this->db_model->select_data('*','student_doubts_class',$condd,$limit,array('doubt_id','desc'));
				}else{
					$doubts_data = '';
				}
			}else{
				$doubts_data = $this->db_model->select_data('*','student_doubts_class',$cond,$limit,array('doubt_id','desc'),'','','',$or_like);
            }
            
            if(!empty($doubts_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }else if($role == '3'){
                    $profile = 'teacher';
                }
    
                foreach($doubts_data as $key=>$value){
                    
                    
                    $userName =$this->db_model->select_data('name,teach_image','users',array('id'=>$value['teacher_id']),1);
                    
                    $batchName = $this->db_model->select_data('batch_name,start_date,end_date','batches',array('id'=>$value['batch_id']),1);
                    
                    if (!empty($userName[0]['teach_image'])){ 
                        $image = '<img src="'.base_url().'uploads/teachers/'.$userName[0]['teach_image'].'" title="'.$userName[0]['teach_image'].'" class="view_large_image"></a>';
                    }else{
                        $image = '<img src="'.base_url().'assets/images/student_img.png" title="'.$userName[0]['teach_image'].'" class="view_large_image"></a>';
                    }
                    $user_name =$this->readMoreWord($userName[0]['name'], 'Student Name',15);
                    
                    $batch_name =$this->readMoreWord(!empty($batchName[0]['batch_name'])?$batchName[0]['batch_name']:'', 'Batch Name',15);
                    
                    $subName =$this->db_model->select_data('subject_name','subjects',array('id'=>$value['subjects_id']),1);
                    
                    $sub_name =$this->readMoreWord($subName[0]['subject_name'], 'Subject Name',15);
                    
                    
                    $chap_id =$value['chapters_id'];
                    $chapterCon = "id in ($chap_id)";
	                $chapterName = $this->db_model->select_data('chapter_name','chapters',$chapterCon,'');
	                
	                $chapter_Name =$this->readMoreWord($chapterName[0]['chapter_name'], 'Subject Name',15);
	                $apru = !empty($value['appointment_time'])?'yes':'no';
					if($value['status'] == 1){
						$statusDrop='<span class="greentext">'.$this->lang->line('ltr_approve').'</span>';
					}else if($value['status'] == 2){
						$statusDrop='<span class="redtext">'.$this->lang->line('ltr_decline').'</span>';
					}else if($value['status'] == 0){
						$statusDrop=$this->lang->line('ltr_pending');
					}
					
					$doubtDate = ($value['appointment_date']!= '0000-00-00')?date('d-m-Y',strtotime($value['appointment_date'])):'';
					$doubtTime = !empty($value['appointment_time'])?$value['appointment_time']:'';
					$doubtDes = !empty($value['teacher_description'])?$value['teacher_description']:'';
					
					$action = '<p class="actions_wrap">
					<a class="studentViewDoubt btn_view" data-doubtDate="'.$doubtDate.'" data-doubtTime="'.$doubtTime.'" data-doubtDes="'.$doubtDes.'" data-id="'.$value['doubt_id'].'"><i class="fa fa-eye"></i></a>
                     </p>';
					
					$des =$this->readMoreWord($value['users_description'], $this->lang->line('ltr_description'),15);
	                $dataarray[] = array(
                                $count,
                                $image.$user_name,
                                $batch_name,
                                $sub_name,
                                $chapter_Name,
                                $des,
                                $statusDrop,
								$action
                               
                        ); 
                
                $count++;
                }
               
                $recordsTotal = count($doubts_data);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => $dataarray,
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
	
	function payment_history(){
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            $post = $this->input->post(NULL,TRUE);
            $get = $this->input->get(NULL,TRUE);
            if(isset($post['length']) && $post['length']>0){
                if(isset($post['start']) && !empty($post['start'])){
                    $limit = array($post['length'],$post['start']);
                    $count = $post['start']+1;
                }else{ 
                    $limit = array($post['length'],0);
                    $count = 1;
                }
            }else{
                $limit = '';
                $count = 1;
            }
        
            if($post['search']['value'] != ''){
				
                $like = array('name',$post['search']['value']);
                $or_like = '';
				$userdata = $this->db_model->select_data('id','students','','',array('id','desc'),$like);
				$usersId =array();
				foreach($userdata as $key){
					array_push($usersId,$key['id']);
				}
				$uId = implode(', ', $usersId);
				$condd = "student_id in ($uId)";
            }else{
               $like = ''; 
               $or_like = ''; 
            }
            
            
         
            if(!empty($condd)){
				if(!empty($uId)){
					$doubts_data = $this->db_model->select_data('*','student_payment_history',$condd,$limit,array('id','desc'));
				}else{
					$doubts_data = '';
				}
			}else{
				$doubts_data = $this->db_model->select_data('*','student_payment_history','',$limit,array('id','desc'),'','','',$or_like);
            }
            
            if(!empty($doubts_data)){
                $role = $this->session->userdata('role');
                if($role == '1'){  
                    $profile = 'admin';
                }else if($role == '3'){
                    $profile = 'teacher';
                }
    
                foreach($doubts_data as $key=>$value){
                    
                    
                    $userName =$this->db_model->select_data('name,image','students',array('id'=>$value['student_id']),1);
                   if(!empty($userName)){
                    $batchName = $this->db_model->select_data('batch_name,start_date,end_date','batches',array('id'=>$value['batch_id']),1);
                    
                    if (!empty($userName[0]['image'])){ 
                        $image = '<img src="'.base_url().'uploads/students/'.$userName[0]['image'].'" title="'.$userName[0]['image'].'" class="view_large_image"></a>';
                    }else{
                        $image = '<img src="'.base_url().'assets/images/student_img.png" title="'.$userName[0]['image'].'" class="view_large_image"></a>';
                    }
                    $user_name =$this->readMoreWord($userName[0]['name'], 'Student Name',15);
                    
                    $batch_name =$this->readMoreWord(!empty($batchName[0]['batch_name'])?$batchName[0]['batch_name']:'', 'Batch Name',15);
				   
                    
	                $dataarray[] = array(
                                $count,
                                $image.$user_name,
                                $batch_name,
                                $value['transaction_id'],
                                $value['amount'].' '.$this->general_settings('currency_decimal_code'),
                                date('d-m-Y',strtotime($value['create_at'])),
                               
                        ); 
				   }
                $count++;
                }
               
                $recordsTotal = count($doubts_data);
    
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => $recordsTotal,
                    "recordsFiltered" => $recordsTotal,
                    "data" => !empty($dataarray)?$dataarray:'',
                );
    
            }else{
                $output = array(
                    "draw" => $post['draw'],
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                );
            }
            echo json_encode($output,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    public function SendMail($tomail='', $subject='', $msg=''){
            $frommail =$this->general_settings('smtp_mail');
            $frompwd =$this->general_settings('smtp_pwd');
            $title = $this->db_model->select_data('site_title','site_details','',1,array('id','desc'))[0]['site_title'];
            $this->load->library('email');
            
            $config = array();
            $config['protocol'] = $this->general_settings('server_type');
            $config['smtp_host'] = $this->general_settings('smtp_host');
            $config['smtp_port'] = $this->general_settings('smtp_port');
            $config['smtp_user'] = $frommail;
            $config['smtp_pass'] = $frompwd;
            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['smtp_crypto'] = $this->general_settings('smtp_encryption');
            $config['newline'] = "\r\n";
            
            $this->email->initialize($config);
            
            $this->email->from($frommail, $title);
            //$ci->email->bcc('example@gmail.com');
            $this->email->to($tomail);
            $this->email->subject($subject);
            $this->email->message($msg);
            @$this->email->send();
            return true;
        }
        
        function edit_email_setting(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
            
				if(!empty($this->input->post('server_type',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('server_type',TRUE)),array('key_text'=>'server_type'),1);
				}
				
				if(!empty($this->input->post('smtp_host',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('smtp_host',TRUE)),array('key_text'=>'smtp_host'),1);
				}
				
				if(!empty($this->input->post('smtp_username',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('smtp_username',TRUE)),array('key_text'=>'smtp_mail'),1);
				}
				
				if(!empty($this->input->post('smtp_password',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('smtp_password',TRUE)),array('key_text'=>'smtp_pwd'),1);
				}
				
				if(!empty($this->input->post('smtp_port',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('smtp_port',TRUE)),array('key_text'=>'smtp_port'),1);
				}
				
				if(!empty($this->input->post('smtp_encryption',TRUE))){
					$this->db_model->update_data_limit('general_settings',array('velue_text'=>$this->input->post('smtp_encryption',TRUE)),array('key_text'=>'smtp_encryption'),1);
				}
			
				$resp = array('status'=>1,'msg'=>$this->lang->line('ltr_updated_msg'));
					
			    echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
            echo $this->lang->line('ltr_not_allowed_msg');
        } 
    }
    
    function convertCurrency(){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')){
               if(!empty($_POST['amount'])){
                   $amount = $_POST['amount'];
                   $from_currency =$this->general_settings('currency_code');
    			   $payment_type_get =$this->general_settings('payment_type');
    			   if($payment_type_get==2){
    			       $to_currency='USD';
    			   }else{
    			       $to_currency='INR';
    			   }
                  $apikey = $this->general_settings('currency_converter_api');
                  $from_Currency = urlencode($from_currency);
                  $to_Currency = urlencode($to_currency);
                  $query =  "{$from_Currency}_{$to_Currency}";
                
                  // change to the free URL if you're using the free version
                  //$json = file_get_contents("https://free.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}");
                  $url = "https://api.currconv.com/api/v7/convert?q={$query}&compact=ultra&apiKey={$apikey}";
                  $ch = curl_init();
                   curl_setopt ($ch, CURLOPT_URL, $url);
                   curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
                   curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
                   $contents = curl_exec($ch);
                    
                  @$obj = json_decode($contents, true);
                  $val = floatval($obj["$query"]);
                 
                  $total = $val * $amount;
                  $convert = number_format($total, 2, '.', '');
                  $resp = array('status'=>1,'convert'=>$convert);
               }else{
                  $resp = array('status'=>2);
               }		
			  echo json_encode($resp,JSON_UNESCAPED_SLASHES);
        }else{
                echo $this->lang->line('ltr_not_allowed_msg');
            } 
        }
}
