<!-- This Page CSS -->

    <link href="<?php echo base_url().'assets/admin/assets/libs/summernote/dist/summernote-bs4.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/libs/dropzone/dist/min/dropzone.min.css';?>" rel="stylesheet">
	
	
<!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Email App Part -->
            <!-- ============================================================== -->
            <div class="email-app">
                <!-- ============================================================== -->
                <!-- Left Part -->
                <!-- ==============================================================
                <div class="left-part">
                    <a class="ti-menu ti-close btn btn-success show-left-part d-block d-md-none" href="javascript:void(0)"></a>
                    <div class="scrollable" style="height:100%;">
                        <div class="p-15">
                            <a id="compose_mail" class="waves-effect waves-light btn btn-danger d-block" href="javascript: void(0)">Compose</a>
                        </div>
                        <div class="divider"></div>
                        <ul class="list-group">
                            <li>
                                <small class="p-15 grey-text text-lighten-1 db">Folders</small>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="active list-group-item-action"><i class="mdi mdi-inbox"></i> Inbox <span class="label label-success float-right">6</span></a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"> <i class="mdi mdi-star"></i> Starred </a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"> <i class="mdi mdi-send"></i> Draft <span class="label label-danger float-right">3</span></a></li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"> <i class="mdi mdi-email"></i> Sent Mail </a>
                            </li>
                            <li class="list-group-item">
                                <hr>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"> <i class="mdi mdi-block-helper"></i> Spam </a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"> <i class="mdi mdi-delete"></i> Trash </a>
                            </li>
                            <li class="list-group-item">
                                <hr>
                            </li>
                            <li>
                                <small class="p-15 grey-text text-lighten-1 db">Labels</small>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"><i class="text-danger mdi mdi-checkbox-blank-circle"></i> Work </a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"><i class="text-cyan mdi mdi-checkbox-blank-circle"></i> Business </a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"><i class="text-warning mdi mdi-checkbox-blank-circle"></i> Family </a>
                            </li>
                            <li class="list-group-item">
                                <a href="javascript:void(0)" class="list-group-item-action"><i class="text-info mdi mdi-checkbox-blank-circle"></i> Friends </a>
                            </li>
                        </ul>
                    </div>
                </div>-->
                <!-- ============================================================== -->
                <!-- Right Part  Mail Compose -->
                <!-- ============================================================== -->
                <div class="mail-compose bg-white">
                    <div class="p-20 border-bottom">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4>Compose</h4>
                                <span>Create new message</span>
						    </div>
                            <div class="ml-auto">
                                <button id="cancel_compose" class="btn btn-dark" onclick="backPage();">Back</button>
                            </div>
							
                        </div>
                    </div>
					
		            <!-- Action part -->
                    <!-- Button group part -->
                    <div class="card-body">
                        <form action="<?= base_url('index.php/MailSend/sendEmail');?>"  method="post" id="my-form">
						<!--<form>-->
							<div class="form-group">
                                <input type="hidden" id="category_type" name="category_type" value="<?= $_GET['category_type'];?>" class="form-control" placeholder="From" readonly >
                            </div>
							<div class="form-group">
                                <input type="text" id="example_from" name="example_from" value="<?= $_GET['from'];?>" class="form-control" placeholder="From" readonly required>
                            </div>
							<div class="form-group">
                                <input type="text" id="example_to" name="example_to" value="<?= $_GET['to'];?>" class="form-control" placeholder="To" required >
                            </div>
								
							<div class="form-group">
                                <input type="text" id="example_cc" name="example_cc" class="form-control" placeholder="Cc">
                            </div>
                            <div class="form-group">
                                <input type="text" id="example_subject" name="example_subject" class="form-control" placeholder="Subject">
                            </div>
							<div class="form-group">
                                <input type="hidden" id="upload_path" name="upload_path" value="" class="form-control">
                            </div>
                           <!-- <div id="summernote" name="summernote"></div> -->
						   
						   <?php
								if($_GET['category_type']=='PAYSLIP')
								{
									echo '<textarea id="summernote" name="summernote">
												<p>ROCK TECH ENGINEERS</p>
												<p>Name : '.$_GET['Name'].'</p>
												<p>Salary For Month : '.$_GET['SFM'].'</p>
											</textarea>';
								}
								else
								{
									echo '<textarea id="summernote" name="summernote"></textarea>';
								}
						   ?>
						   
                            <h4>Attachment</h4>
							
							<div class="dropzone">
								
							</div>
							
                            <button  type="submit" id="btnSendEmail" class="btn btn-success m-t-20"><i class="far fa-envelope"></i> Send</button>
                            <button type="button" class="btn btn-dark m-t-20" onclick="backPage();">Discard</button>
							
                        </form>
						
						<?php if($error=$this->session->flashdata('msg'))
						{
							echo '<script>alert("Mail Not Send Successfully...!");</script>';
						}
						?>
						
						<!-- Action part -->
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
<!-- This Page JS -->
        
	<script>
		$('document').ready(function(){
			
		}); 
		
		function backPage()
		{
			window.close();
		}
		
		var str='';
		var categoryType=$('#category_type').val();
		/* if(categoryType=='PAYSLIP')
		{
			$('#summernote').attr('disabled','disabled');
		} */
		var path=base_path+"MailSend/upload_file/"+categoryType;
		
		$(".dropzone").dropzone({
			
			url:path,
			paramName:'file',
			
            success : function(file, response) {
				
				if(response!='')
				{
					str+=response+',';
					$('#upload_path').val(str);
				}
				else
				{
					alert('File Not Upload Successfully...');
				}
            }
        });
		
	</script>
	
