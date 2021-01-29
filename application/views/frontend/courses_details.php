<!----- Courses Single Section ----->
		<section class="edu_courses_single">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-12 padder0">
						<div class="edu_courses_section">
							<div class="row">
            					<div class="col-xl-7 col-lg-5 col-md-12 col-sm-12 col-12 align-self-center">
            						<div class="edu_courses_imgbox">
            						    <img src="<?php if(!empty($singel_batches[0]['batch_image'])) { echo base_url('uploads\batch_image/').$singel_batches[0]['batch_image'] ; }else{ echo base_url('uploads/site_data/'.$site_Details['0']['site_logo']); } ?>" alt="image">
            						</div>
            					</div>
            				    <div class="col-xl-5 col-lg-7 col-md-12 col-sm-12 col-12 align-self-center">
            				        <div class="edu_courses_box">
            						    <div class="edu_courses_cntnbox">
            						    <h2 class="edu_courses_sprice"><?php if($singel_batches[0]['batch_type']==2){ if(!empty($singel_batches[0]['batch_offer_price'])){ echo '<s>'.$currency_decimal.' '.$singel_batches[0]['batch_price'].'</s> / '.$currency_decimal.' '.$singel_batches[0]['batch_offer_price']; }else{ echo $currency_decimal.' '.$singel_batches[0]['batch_price'];} }else{ echo "Free";} ?></h2>
            						    
            							<h2 class="edu_courses_title"><?php echo $singel_batches[0]['batch_name']; ?></h2>
            							<p class="edu_courses_des mb_30"><?php echo $singel_batches[0]['description']; ?></p>
										
										<a href="<?php echo base_url('enroll-now/'.$singel_batches[0]['id']); ?>" class="edu_btn"><?php echo html_escape($this->common->languageTranslator('ltr_enroll_now'));?></a>
            						    </div>
            						</div>
            				    </div>
            				</div>
						</div>
					</div>
				</div>
			</div>
		</section>
<!----- course includes Section ----->
        <?php 
		if(!empty($batch_fecherd)){
			?>
		<section class="edu_incld_course_wra">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-12 padder0">
						 <div class="edu_ic_sec">
					<?php
						 foreach($batch_fecherd as $value){
							 ?>
								<h2 class="edu_ic_ttl"><?php echo $value['batch_specification_heading'];?></h2>
								<ul class="edu_ic_list">
								    <?php $batch_fecherd = json_decode($value['batch_fecherd']); 
									   foreach($batch_fecherd as $kkey){
									?>
									<li> <i class="icofont-rounded-double-right"></i><?php echo $kkey ;?> </li>
									   <?php } ?>
								</ul>
							 <?php
						 }
					 
					?>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php } ?>
		<!----- Recommended Course Section ----->
		<section class="edu_rc_wrap">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-12 text-center">
						<div class="edu_heading_wrapper">
							<h4 class="edu_subTitle"><?php $ttd =html_escape($this->common->languageTranslator('ltr_enhance')); echo !empty($frontend_details[0]['sec_crse_sub_heading'])?$frontend_details[0]['sec_crse_sub_heading']: $ttd;?></h4>
							<h4 class="edu_heading"><?php echo html_escape($this->common->languageTranslator('ltr_recod_course'));?></h4>
							<img src="<?php echo base_url();?>assets/images/border.png" alt=""/>
						</div>
					</div>
				</div>
			</div>
			
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-12 padder0">
						<div class="edu_courses_section">
							<div class="row">
							<?php 
							if(!empty($batches)){
								foreach($batches as $value){
									?>
									<div class="col-lg-4 col-md-4 col-sm-6 col-12">
										<div class="edu_courses_box">
											<div class="edu_courses_imgbox">
												<img src="<?php if(!empty($value['batch_image'])) { echo base_url('uploads\batch_image/').$value['batch_image'] ; }else{ echo base_url('uploads/site_data/'.$site_Details['0']['site_logo']); } ?>" alt="image">
												<a href="<?php echo base_url('enroll-now/'.$value['id']); ?>" class="courses_atc"><?php echo html_escape($this->common->languageTranslator('ltr_enroll_now'));?></a>
												<a href="<?php echo base_url('enroll-now/'.$value['id']); ?>" class="edu_btn courses_price"><?php if($value['batch_type']==2){ if(!empty($value['batch_offer_price'])){ echo '<s>'.$currency_decimal.' '.$value['batch_price'].'</s> / '.$currency_decimal.' '.$value['batch_offer_price']; }else{ echo $currency_decimal.' '.$value['batch_price'];} }else{ echo "Free";} ?></a>
												<?php if(!empty($value['batch_offer_price'])){ ?>
												<span class="edu_courses_flag"><?php echo html_escape($this->common->languageTranslator('ltr_offer')); ?></span>
												<?php } ?>
											</div>
											<div class="edu_courses_cntnbox">
											<h2 class="edu_courses_title"><?php echo $value['batch_name'];?></h2>
											<p class="edu_courses_des"><?php echo $value['description']; ?></p>
											<a href="<?php echo base_url('courses-buy/'.$value['id']); ?>" class="edu_courses_view mt-2"><?php echo html_escape($this->common->languageTranslator('ltr_course_view'));?> <i class="fas fa-long-arrow-alt-right pl-1"></i></a> 
											</div>
										</div>
									</div>
									<?php
								}
							}else{
							?>
            					<div class="col-lg-4 col-md-4 col-sm-6 col-12">
            						<div class="edu_courses_box">
            							<div class="edu_courses_imgbox">
            							    <img src="http://themes91.in/ci/e-academy_test/assets/images/course01.jpg" alt="image">
            							    <a href="#" class="courses_atc"><?php echo html_escape($this->common->languageTranslator('ltr_add_to_cart'));?></a>
            							    <a href="#" class="edu_btn courses_price"><?php echo html_escape($this->common->languageTranslator('ltr_course_price'));?></a>
            							    <span class="edu_courses_flag"><?php echo html_escape($this->common->languageTranslator('ltr_best_seller'));?></span>
            						    </div>
            						    <div class="edu_courses_cntnbox">
            						    <div class="edu_courses_rwrap">
                						    <ul class="edu_courses_rating">
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/star.svg" alt="image"></li>
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/star.svg" alt="image"></li>
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/star.svg" alt="image"></li>
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/blank_star.svg" alt="image"></li>
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/blank_star.svg" alt="image"></li>
                						        <li><img src="http://themes91.in/ci/e-academy_test/assets/images/blank_star.svg" alt="image"></li>
                						    </ul>
                						    <p class="edu_courses_rno"><span><?php echo html_escape($this->common->languageTranslator('ltr_course_rating'));?></span> <?php echo html_escape($this->common->languageTranslator('ltr_course_rati_no'));?></p>
                						</div>
            							<h2 class="edu_courses_title"><?php echo html_escape($this->common->languageTranslator('ltr_course_title'));?></h2>
            							<p class="edu_courses_des"><?php echo html_escape($this->common->languageTranslator('ltr_course_des'));?></p>
            							<a href="" class="edu_courses_view mt-2"><?php echo html_escape($this->common->languageTranslator('ltr_course_view'));?> <i class="fas fa-long-arrow-alt-right pl-1"></i></a> 
            						    </div>
            						</div>
            					</div>
							<?php } ?>
            				</div>
						</div>
					</div>
				</div>
			</div>
	
		</section>