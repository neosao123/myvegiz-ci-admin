<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
	error_reporting(0);
	$session_key = $this->session->userdata('key'.SESS_KEY);
	$role = ($this->session->userdata['logged_in'.$session_key]['role']);
	if (isset($this->session->userdata['logged_in'.$session_key])) {
		$code = ($this->session->userdata['logged_in'.$session_key]['code']);	
		$username = ($this->session->userdata['logged_in'.$session_key]['username']);
		 $role = ($this->session->userdata['logged_in'.$session_key]['role']);
		 $userFname = ($this->session->userdata['logged_in'.$session_key]['userFname']); 
		 $userMname = ($this->session->userdata['logged_in'.$session_key]['userMname']);
		 $userLname = ($this->session->userdata['logged_in'.$session_key]['userLname']);
		$email = ($this->session->userdata['logged_in'.$session_key]['email']);
		$profilePhoto = ($this->session->userdata['logged_in'.$session_key]['profilePhoto']);
		$filename= 'assets/rights/'.$code.'.json'; 
        $json = file_get_contents($filename);
		$module_data=json_decode($json,true); 
	} else {
		return redirect('admin/login');
	}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="<?php echo base_url().'assets/images/favicon/favicon-16x16.png';?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url().'assets/images/favicon/favicon-32x32.png';?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url().'assets/images/favicon/favicon-96x96.png';?>" type="image/png">
    <title> Admin - My Vegiz</title>
    <!-- Custom CSS -->
    <link href="<?php echo base_url().'assets/admin/assets/libs/chartist/dist/chartist.min.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/c3/c3.min.css';?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url().'assets/admin/dist/css/style.min.css';?>" rel="stylesheet">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/admin/assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';?>">
    <link href="<?php echo base_url().'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css';?>" rel="stylesheet">
	<link href="https://cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css" rel="stylesheet">
     <!-- Wizard Form page CSS -->
    <link href="<?php echo base_url().'assets/admin/assets/libs/jquery-steps/jquery.steps.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/libs/jquery-steps/steps.css';?>" rel="stylesheet">
	<link href="<?php echo base_url().'assets/admin/assets/libs/toastr/build/toastr.min.css'?>" rel="stylesheet">
	<!-- PNotify -->
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.buttons.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.nonblock.css';?>" rel="stylesheet">
	<link href="<?php echo base_url().'assets/admin/assets/libs/sweetalert2/dist/sweet-alert.css';?>" rel="stylesheet">
	<!--For Maxlength Error msg-->
	<link href="<?php echo base_url().'assets/admin/assets/extra-libs/maxlength-master/jquery.maxlength.css';?>" rel="stylesheet">
	<!-- Page CSS For Multiple Select Element-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/admin/assets/libs/select2/dist/css/select2.min.css'?>">
	<!--css for calendar-->
    <link href="<?php echo base_url().'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/calendar/calendar.css';?>" rel="stylesheet">
	<link href="<?php echo base_url().'assets/admin/assets/extra-libs/loader/loader.css';?>" rel="stylesheet">
	 <!--css for summernote-->
    <link href="<?php echo base_url().'assets/admin/assets/libs/summernote/dist/summernote-bs4.css';?>" rel="stylesheet">
	<link href="<?php echo base_url().'assets/admin/assets/extra-libs/loader/loader.css';?>" rel="stylesheet">
	<style>
		.page-item.active .page-link {
			z-index: 1;
			color: #fff;
			background-image: linear-gradient(to right,#588002d1 , #9EC746);
			border-color: #dee2e6;
		}
		.bg-myvegiz{
			background-image: linear-gradient(to right,#588002d1 , #9EC746);
		}
		.bg-dash{
			background-image: linear-gradient(to bottom,#8bc34a , #000000c4);
		}
		.btn-myve{
			background-image: linear-gradient(to right,#588002d1 , #9EC746);
			border-color: #9EC746;
			color: whitesmoke;
		}
		.btn-default{
			background-color:#E6E6E6!important;
		}
		.page-wrapper{
			background-color:#ddf9bc!important;
		}
		.bride {
			padding: 3px 5px 2px;
			position: absolute;
			top: 12px;
			right: 10px;
			display: inline-block;
			min-width: 10px;
			font-size: 10px;
			font-weight: bold;
			color: #ffffff;
			line-height: 1;
			vertical-align: baseline;
			white-space: nowrap;
			text-align: center;
			border-radius: 11px;
		}
		.bride-danger {
			background-color: #db5565;
		}
		.btn-default, .btn-default:hover{border:1px solid grey!important;color:#4e1919!important}
		a,.mywarning,.mywarn,.del {cursor:pointer!important}
		.cust_check::after,.cust_check::before{margin-top: 20px;}
		.topbar .nav-toggler, .topbar .topbartoggler {
            color: #509004;
        }
	</style>  
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?= PLACE_API_KEY?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/libs/jquery/dist/jquery.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/jQuery.print.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/libs/moment/moment.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/libs/dropzone/dist/min/dropzone.min.js';?>"></script>
	<link href="<?php echo base_url().'assets/admin/assets/libs/dropzone/dist/min/dropzone.min.css';?>" rel="stylesheet">
	<script src="<?php echo base_url().'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/taskboard/js/jquery-ui.min.js';?>"></script>
	<!-- base_url -->
    <script>
       var base_path = "<?= base_url() ?>";
	</script>
</head> 
<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" >
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="<?= base_url()?>admin/welcome">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <!-- <img src="<?php //echo base_url().'assets/admin/assets/images/logo-icon.png';?>" alt="homepage" class="dark-logo" />-->
                            <!--<img src="<?php //echo base_url().'assets/admin/assets/images/admin-small-logo.jpg';?>" class="light-logo" alt="Rocktech Engineers" />-->
							<!-- Light Logo icon -->
                            <img src="" alt="homepage" style="height:50px;display:none;" id="collapseImg" > 
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                            <!-- dark Logo text -->
                            <!-- <img src="<?php //echo base_url().'assets/admin/assets/images/logo-text.png" alt="homepage';?>" class="dark-logo" />-->
                            <!-- Light Logo text -->    
                            <img src="<?php echo base_url().'assets/images/MYVEGIZ LOGO 3rd-01-cut.png';?>" class="light-logo" alt="Myvegiz" width="100%" > 
                            <!--<b style="color:#2d5c2a">MYVEGIZ</b>-->
                            <!--<span style="color:#2d5c2a">VEGITABLE DELIVERY</span>-->
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
					
                    <ul class="navbar-nav float-left mr-auto">
                        
						<li class="nav-item d-none d-md-block">
							<a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
								<i class="mdi mdi-menu font-24"></i>
							</a>
						</li>
						<?php if($role=='ADM'){ ?>
						<li class="nav-item">
							<a class="nav-link">
								<div class="form-check form-check-inline">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="maintenanceMode" value="1">
										<label class="custom-control-label cust_check" for="maintenanceMode">Maintenance Mode</label>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
                    </ul>
					
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right"> 
						
						<li class="nav-item">
						    <button class="nav-link text-primary" id="button"><i class="fas fa-volume-up"></i></button>
						</li>
						
						<?php if($role=='ADM'){ ?>
						<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark " id="notifyList" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-bell font-24"></i><span class="bride bride-danger mCount" id="msgCount"></span></a>
                            <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown addData" id="addContractNotifyData">
                               <div id="img_load"  class="text-center d-none"><img src="<?= base_url()?>assets/admin/assets/images/loader.gif"></div>
								<div id="another"></div>
                            </div>
                        </li>
						<?php } ?>
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $profilePhoto != "" ? base_url().'uploads/profilePhoto/'.$profilePhoto : base_url().'assets/admin/assets/images/users/1.jpg';?>" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-myvegiz"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-myvegiz text-white m-b-10">
								 
								  <div class="">
									<img src="<?php echo $profilePhoto != "" ? base_url().'uploads/profilePhoto/'.$profilePhoto : base_url().'assets/admin/assets/images/users/1.jpg' ?>" alt="user" class="img-circle" width="60">
								  </div>
									<div class="m-l-10">
									<?php
                                      echo'<h4 class="m-b-0">'.$userFname." ".$userLname.'</h4>';
									  switch($role)
									  {
											case "ADM" : echo '<p class="m-b-0">Admin</p>';break;
											case "USR" : echo '<p class="m-b-0">User</p>';break;
									  }
									 ?>
                                        <p class=" m-b-0"><?php echo $email?></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="<?php echo base_url().'usermaster/myProfile/'.$code;?>"><i class="ti-user m-r-5 m-l-5"></i> My Profile</a>
								<?php if($role!="ADM" && $role!="USR"){?>
                                <a class="dropdown-item blueWalletBal"  data-toggle="modal" data-target="#modal" data-seq="<?php echo $code;?>"  href="<?php echo $code;?>"><i class="ti-wallet m-r-5 m-l-5" ></i> My Balance</a>
								<?php }?>
								<a class="dropdown-item" href="<?php echo base_url() . 'authentication/logout'?>"><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                                <div class="dropdown-divider"></div>
                               <!-- <div class="p-l-30 p-10"><a class="btn btn-sm btn-myve btn-rounded userData" data-toggle="modal" data-target="#user-modal" data-seq="<?php echo $code;?>"  href="<?php echo $code;?>">View Profile</a></div>-->
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
		
		
		<!-- sample modal content -->
			<div id="user-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog model-lg">
					<div class="modal-content"  style="width:600px;height:600px;padding:20px">
						<div class="modal-header">
							<h4 class="modal-title">View user</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
						</div>
						<div class="userModal-body">
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
			<div id="maintenance-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog model-md">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Maintenance Mode</h4>
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
						</div>
						<div class="modal-body maintenancemodal-body" >
							<p>Maintenance Mode - <b>ON</b></p>
							<span>When Maintenance Mode is kept "On" - all of your application users shall not be able to access the application unless you turn off the maintenace!</span>
							<form>
								<div class="form-row">
									<div class="col-sm-12 mb-2">
										<label for="messageTitle">Message Title</label>									 
										<input class="form-control" id="messageTitle" maxlength="150" required>
									</div>
									<div class="col-sm-12 mb-2">
										<label for="messageDescription">Message Description</label>
										<textarea class="form-control" id="messageDescription" maxlength="255" required></textarea>
									</div>
									<div class="col-sm-12 mb-2">
										<button class="btn btn-primary" type="button" id="updateMaintenance">Submit</button>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default waves-effect" data-dismiss="modal" id="close">Close</button>
						</div>
					</div>
				</div>
			</div>
			<!-- /.modal -->
			
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <?php include 'header_drawer.php';?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ==============================================================headder -->
        <!-- ============================================================== -->
     <script>
		$( document ).ready(function() {
			$(':input[type="text"]').each( function() { $(this).attr('autocomplete', 'off'); });
			$(".userData").click(function(){
				var code=$(this).data('seq');			 
				$.ajax({
					url: base_path+"Usermaster/view",
					method:"GET",
					data:{code:code},
					datatype:"text",
					success: function(data)
					{
						$(".userModal-body").html(data);					
					}
				});
			});
			$.ajax({
				url:base_path+"Settings/getMaintenanceMode",
				method:"GET",
				data:{},
				datatype:"text",
				success: function(data)
				{
					var da = JSON.parse(data);
					if(da['settingValue']==1){
						$("#maintenanceMode").prop("checked",true);
					} else {
						$("#maintenanceMode").prop("checked",false);
					}
					$("#messageTitle").val(da['messageTitle']);
					$("#messageDescription").val(da['messageDescription']);
				},
			}); 
			$.ajax({
				url:base_path+"CustomerNotification/getOrderNotificationCount",
				method:"GET",
				data:{},
				datatype:"text",
				success: function(data)
				{
					$(".mCount").html('<strong>'+data+'</strong>');
				},
			}); 
			$("#notifyList").click(function(){
				$("#img_load").removeClass('d-none').addClass('d-block');
				$("#another").empty();
				setTimeout(function(){   
					$.ajax({
						url: base_path+"CustomerNotification/getOrderNotificationList",
						method:"GET",
						data:{},
						datatype:"text",
						beforeSend: function(){
						$("#loader").show();
						$("#submit").hide();
						},
						success: function(data)
						{
							$("#another").html(data);
							$("#img_load").removeClass('d-block').addClass('d-none');
						},
					}); 
				}, 5000);
			});
			var divMain = $("#main-wrapper").attr('data-sidebartype');
			$('.sidebartoggler').on('click',function(){
				var divMain = $("#main-wrapper").attr('data-sidebartype');
				if(divMain == 'mini-sidebar'){
					$('#collapseImg').hide();
					$('.light-logo').show();
				}
				else if(divMain == 'full')
				{				 
					$('#collapseImg').show();
					var imgUrl = "<?php echo base_url().'assets/images/MYVEGIZ LOGO 1st -01-cut.png';?>";
					$('#collapseImg').attr('src',imgUrl);
					$('.light-logo').hide();				
				}
			});
			$("#maintenanceMode").change(function(){
				if($(this).is(":checked")){
					$("#maintenance-modal").modal('show');	
					$("#close").click(function(){
						// alert('close');
						$.ajax({
							url:base_path+"Settings/getMaintenanceMode",
							method:"GET",
							data:{},
							datatype:"text",
							success: function(data)
							{
								var da = JSON.parse(data);
								if(da['settingValue']==1){
									$("#maintenanceMode").prop("checked",true);
								} else {
									$("#maintenanceMode").prop("checked",false);
								}
								$("#messageTitle").val(da['messageTitle']);
								$("#messageDescription").val(da['messageDescription']);
							},
						}); 
					});
				} else {
					var settingValue= 0;
					$.ajax({					
						url: base_path+"Settings/updateMaintenanceOff",
						method:"get",
						data:{"settingValue":settingValue},
						datatype:"text",
						success: function(data)
						{
							if(data){
								alert('Maintenance mode is `OFF` now!');
							} else {
								alert('Failed to update maintenance mode!');
							} 
						}
					});
				}
				
				
			});
			$("#updateMaintenance").click(function(){
				var settingValue = 1;
				var messageTitle = $("#messageTitle").val().trim();
				var messageDescription = $("#messageDescription").val().trim();
				if(messageTitle.length>=5 && messageDescription.length>=5){
					$.ajax({
						url: base_path+"Settings/updateMaintenanceOn",
						method:"post",
						data:{"settingValue":settingValue,'messageTitle':messageTitle,'messageDescription':messageDescription},
						success: function(data)
						{
							if(data){
								alert('Maintenance mode is `On` now!');
								$("#maintenanceMode").prop('checked',true);
							} else {
								alert('Failed to update maintenance mode!');
								$("#maintenanceMode").prop('checked',false);
							}
							$("#maintenance-modal").modal('hide');
						}
					});
				}
			});
		});
	</script>
	