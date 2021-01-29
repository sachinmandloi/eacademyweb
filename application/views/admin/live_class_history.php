<section class="edu_admin_content">
	<div class="edu_admin_right sectionHolder edu_batch_manager">
	    <div class="edu_btn_wrapper sectionHolder padderBottom30 text-right">
		   
		</div>
		<div class="createDivWrapper edu_add_question create_ppr_popup hide">
    		<div class="edu_admin_informationdiv sectionHolder">
    		    <div class="ppr_popup_inner">
        			<div class="row align-items-center text-center">
        			    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
					<button class="multiDelete btn_delete btn btn-primary" data-toggle="tooltip" data-placement="top" title="Delete" data-table="live_class_history" data-column="id"><?php echo html_escape($this->common->languageTranslator('ltr_delete')); ?></button>
        		</div>
					</div>
        		</div>
    		</div>
		</div>
		<?php 
		if(!empty($live_data) && $live_data>=1){
		?>
		<div class="edu_main_wrapper edu_table_wrapper">
			<div class="edu_admin_informationdiv sectionHolder">
				<div class="tableFullWrapper">
				    
    				<table class="server_datatable datatable table table-striped table-hover dt-responsive" cellspacing="0" width="100%" data-url="ajaxcall/live_class_history_table">
    					<thead>
    						<tr>
    						    <th><input type="checkbox" class="checkAllAttendance"></th>
    							<th>#</th>
    							<th><?php echo html_escape($this->common->languageTranslator('ltr_batch_name')); ?></th>
    							<th><?php echo html_escape($this->common->languageTranslator('ltr_date')); ?></th>
    							<th><?php echo html_escape($this->common->languageTranslator('ltr_time')); ?></th>
    							<th><?php echo html_escape($this->common->languageTranslator('ltr_class_by')); ?></th>
    						</tr>
    					</thead>
    					<tbody>
    					</tbody>
    				</table>
    			</div>
			</div>
		</div>
		<?php 
		}else{ 
		    echo '<section class="edu_admin_content">
                        <div class="edu_admin_right sectionHolder edu_add_users">
                            <div class="edu_admin_informationdiv edu_main_wrapper">
                                <div class="eac_text eac_page_re">'.html_escape($this->common->languageTranslator('ltr_live_his_no_data')).'</div>
                            </div>
                        </div>
                    </section>';
		} ?>
	</div>
</section>

