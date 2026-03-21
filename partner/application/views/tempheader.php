<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
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
	} 
	else {
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
    <link rel="shortcut icon" href="<?php echo base_url().'assets/images/header/favicon/favicon-16x16.png';?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url().'assets/images/header/favicon/favicon-32x32.png';?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url().'assets/images/header/favicon/favicon-96x96.png';?>" type="image/png">
    <title>Admin - My Vegiz</title>
    <!-- Custom CSS -->
    <link href="<?php echo base_url().'assets/admin/assets/libs/chartist/dist/chartist.min.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/c3/c3.min.css';?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url().'assets/admin/dist/css/style.min.css';?>" rel="stylesheet">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url().'assets/admin/assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css';?>">
     <!-- Datatable plugin CSS -->
    <link href="<?php echo base_url().'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css';?>" rel="stylesheet">
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
	<!-- <link href="<?php //echo base_url().'assets/admin/assets/bootstrap-confirm-delete.css';?>" rel="stylesheet">-->
    <!--css for calendar-->
    <link href="<?php echo base_url().'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'assets/admin/assets/extra-libs/calendar/calendar.css';?>" rel="stylesheet">
	<link href="<?php echo base_url().'assets/admin/assets/extra-libs/loader/loader.css';?>" rel="stylesheet">
    <script src="<?php echo base_url().'assets/admin/assets/libs/jquery/dist/jquery.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/jQuery.print.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/libs/moment/moment.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/libs/dropzone/dist/min/dropzone.min.js';?>"></script>	
    <script src="<?php echo base_url().'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/extra-libs/taskboard/js/jquery-ui.min.js';?>"></script>
    <!-- base_url -->
    <script>
     var base_path =  "<?= base_url()?>";
     // alert(base_path);
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
    <div id="main-wrapper">
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
                    <a class="navbar-brand" href="#">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <!-- <img src="<?php //echo base_url().'assets/admin/assets/images/logo-icon.png';?>" alt="homepage" class="dark-logo" />-->
                            <!--<img src="<?php //echo base_url().'assets/admin/assets/images/admin-small-logo.jpg';?>" class="light-logo" alt="Rocktech Engineers" />-->
							<!-- Light Logo icon -->
                            <img src="<?php echo base_url().'assets/images/MYVEGIZ LOGO 1st -01-cut.png';?>" alt="homepage" style="height:50px"> 
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text" style="margin-top: 10px;">
                             <!-- dark Logo text -->
                             <!-- <img src="<?php //echo base_url().'assets/admin/assets/images/logo-text.png" alt="homepage';?>" class="dark-logo" />-->
                            <!-- Light Logo text -->    
                             <!--<img src="<?php echo base_url().'assets/images/MYVEGIZ LOGO 3rd-01.png';?>" class="light-logo" alt="Myvegiz" width="100%"/> -->
							 
						  <b style="color:#2d5c2a">MYVEGIZ</b>
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
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo base_url().'assets/admin/assets/images/users/1.jpg';?>" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
								 
								  <div class="">
									<img src="<?php echo $profilePhoto != "" ? base_url().'uploads/profilePhoto/'.$profilePhoto : base_url().'assets/admin/assets/images/users/1.jpg' ?>" alt="user" class="img-circle" width="60">
								  </div>
									<div class="m-l-10">
									<?php
									  
                                      echo'<h4 class="m-b-0">'.$userFname." ".$userLname.'</h4>';
									  
									  switch($role)
									  {
											case "ADM" : echo '<p class="m-b-0">Admin</p>';break;
											case "USR" : echo '<p class="m-b-0">Employee</p>';break;
									  }?>
                                        <p class=" m-b-0"><?php echo $email?></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="<?php echo base_url().'index.php/usermaster/edit/'.$code;?>"><i class="ti-user m-r-5 m-l-5"></i> My Profile</a>
								<?php if($role!="ADM" && $role!="USR"){?>
                                <a class="dropdown-item blueWalletBal"  data-toggle="modal" data-target="#modal" data-seq="<?php echo $code;?>"  href="<?php echo $code;?>"><i class="ti-wallet m-r-5 m-l-5" ></i> My Balance</a>
								<?php }?>
								<a class="dropdown-item" href="<?php echo base_url() . 'index.php/authentication/logout'?>"><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                                <div class="dropdown-divider"></div>
                                <div class="p-l-30 p-10"><a class="btn btn-sm btn-success btn-rounded userData" data-toggle="modal" data-target="#user-modal" data-seq="<?php echo $code;?>"  href="<?php echo $code;?>">View Profile</a></div>
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
			<!-- /.modal -->
			
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
						
					<!--	<li class="sidebar-item"><a href="<?php echo base_url().'index.php/admin';?>" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li> -->

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Configuration </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/uom/listrecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> UOM List </span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/currency/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Currency List </span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Usermaster/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> User List </span></a></li>
                                
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Category/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Product Category List</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'index.php/Storage/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Storage List</span></a></li>
							   <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Product/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Product List </span></a></li>
                                
                                 <li class="sidebar-item"><a href="<?php echo base_url().'index.php/DeliveryCharge/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Delivery Charge</span></a></li>
                                
                            </ul>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Inward </span></a>
							<ul aria-expanded="false" class="collapse  first-level">
									<li class="sidebar-item"><a href="<?php echo base_url().'index.php/Vendor/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Vendor List</span></a></li>
									<li class="sidebar-item"><a href="<?php echo base_url().'index.php/Inward/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Inward List</span></a></li>                               
							</ul>
						</li>
                       <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Employee </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/employee/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Employee List </span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/jobtype/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Job Type List </span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/designation/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Designation List </span></a></li>
                                <li class="sidebar-item"><a href="<?php echo base_url().'index.php/salaryGrade/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Salary Grade List </span></a></li>
                            </ul>
                        </li>
						
						  <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Other </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                             <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Slider/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Slider List </span></a></li>  
                             <li class="sidebar-item"><a href="<?php echo base_url().'index.php/AndroidUsers/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Android Users </span></a></li>  
							</ul>
                        </li>		
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Order </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                             <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Order/pendingListRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Pending List </span></a></li>  
                            <li class="sidebar-item"><a href="<?php echo base_url().'index.php/Order/placedListRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Placed List </span></a></li>  
							</ul>
                        </li>	
						<li class="sidebar-item"><a href="<?php echo base_url().'index.php/resetpassword/listRecords';?>" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Reset Password</span></a></li>
						
						<li class="sidebar-item"><a href="<?php echo base_url().'index.php/Notification/listRecords';?>" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Push Notification</span></a></li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ==============================================================headder -->
        <!-- ============================================================== -->
     <script>
	 $( document ).ready(function() {
		  $(':input[type="text"]').each( function() { $(this).attr('autocomplete', 'off'); });
		 $(".userData").click(function()
 
		 {
			var code=$(this).data('seq');
			// alert(code);
			   $.ajax({
				url: base_path+"Usermaster/view",
				method:"GET",
				data:{code:code},
				datatype:"text",
				success: function(data)
				{
				   //	console.log(data);
					$(".userModal-body").html(data);
					
				}
			});
						 
		 });
	 });
 </script>