<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
error_reporting(0);
$usrName = "";
$session_key = $this->session->userdata('partner_key' . SESS_KEY_PARTNER);
$role = ($this->session->userdata['part_logged_in' . $session_key]['role']);

if (isset($this->session->userdata['part_logged_in' . $session_key])) {
	$code = ($this->session->userdata['part_logged_in' . $session_key]['code']);
	$username = ($this->session->userdata['part_logged_in' . $session_key]['username']);
	$role = ($this->session->userdata['part_logged_in' . $session_key]['role']);
	$userFname = ($this->session->userdata['part_logged_in' . $session_key]['userFname']);
	$userMname = ($this->session->userdata['part_logged_in' . $session_key]['userMname']);
	$userLname = ($this->session->userdata['part_logged_in' . $session_key]['userLname']);
	$email = ($this->session->userdata['part_logged_in' . $session_key]['email']);
	$profilePhoto = ($this->session->userdata['part_logged_in' . $session_key]['profilePhoto']);
	$usrName = substr($userFname, 0, 1);
	$usrName .= substr($userLname, 0, 1);
	$usrName = strtoupper($usrName);
} else {
	return redirect('Login');  
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
	<link rel="shortcut icon" href="<?php echo base_url() . 'assets/images/header/favicon/favicon-16x16.png'; ?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url() . 'assets/images/header/favicon/favicon-32x32.png'; ?>" type="image/png">
	<link rel="shortcut icon" href="<?php echo base_url() . 'assets/images/header/favicon/favicon-96x96.png'; ?>" type="image/png">
	<title>Partner - My Vegiz</title>
	<link href="<?php echo base_url() . 'assets/admin/assets/extra-libs/c3/c3.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'; ?>" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="<?php echo base_url() . 'assets/admin/dist/css/style.min.css'; ?>" rel="stylesheet">
	<!-- Datepicker CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css'; ?>">
	<link href="<?php echo base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'; ?>" rel="stylesheet">
	<link href="https://cdn.datatables.net/buttons/1.6.0/css/buttons.dataTables.min.css" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/libs/toastr/build/toastr.min.css' ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/libs/sweetalert2/dist/sweet-alert.css'; ?>" rel="stylesheet">
	<!--For Maxlength Error msg-->
	<link href="<?php echo base_url() . 'assets/admin/assets/extra-libs/maxlength-master/jquery.maxlength.css'; ?>" rel="stylesheet">

	<link href="<?php echo base_url() . 'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/extra-libs/calendar/calendar.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/extra-libs/loader/loader.css'; ?>" rel="stylesheet">
	<!--css for summernote-->
	<link href="<?php echo base_url() . 'assets/admin/assets/libs/summernote/dist/summernote-bs4.css'; ?>" rel="stylesheet">
	<link href="<?php echo base_url() . 'assets/admin/assets/extra-libs/loader/loader.css'; ?>" rel="stylesheet">
	<style>
		.mdi-bell {
			color: #83a637
		}

		.page-item.active .page-link {
			z-index: 1;
			color: #fff;
			background-image: linear-gradient(to right, #588002d1, #9EC746);
			border-color: #dee2e6;
		}

		.bg-myvegiz {
			background-image: linear-gradient(to right, #588002d1, #9EC746);
		}

		.bg-dash {
			background-image: linear-gradient(to bottom, #8bc34a, #000000c4);
		}

		.btn-myve {
			background-image: linear-gradient(to right, #588002d1, #9EC746);
			border-color: #9EC746;
			color: whitesmoke;
		}

		.btn-default {
			background-color: #E6E6E6 !important;
		}

		.page-wrapper {
			background-color: #ddf9bc !important;
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

		.btn-default,
		.btn-default:hover {
			border: 1px solid grey !important;
			color: #4e1919 !important
		}

		a,
		.mywarning,
		.mywarn,
		.del {
			cursor: pointer !important
		} 
		.navbar-dark .navbar-nav .nav-link {
            color: rgba(0,0,0,.5);
        }
		.userdiv div {
			background: #83a637;
			color: #ffffff !important;
			height: 35px;
			border-radius: 50%;
			width: 35px;
			margin: 15px 0px;
			line-height: 35px;
			padding-left: 6px;
		} 
		.fadea { color:black;} 
		.cust_check::after,.cust_check::before{margin-top: 20px;}
		.topbar .nav-toggler, .topbar .topbartoggler {
            color: #509004;
        }
		/* by anirudh 19-3-21*/	
		.nav-item.vendor-name {
    transition: none !important;
    width: 600px;
    display: flex;
    justify-content: center;
    align-content: center;
    align-items: center;
}
.nav-item.vendor-name:hover {
	background:none!important;
}
/* by anirudh 19-3-21*/	
	</style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBjAeJLrBzCPACXrH1tuW3JIQUr35t6QaI"></script>
	<script src="<?php echo base_url() . 'assets/admin/assets/libs/jquery/dist/jquery.min.js'; ?>"></script>
	<script src="<?php echo base_url() . 'assets/admin/jQuery.print.js'; ?>"></script>
	<script src="<?php echo base_url() . 'assets/admin/assets/libs/moment/moment.js'; ?>"></script>
	<script src="<?php echo base_url() . 'assets/admin/assets/libs/fullcalendar/dist/fullcalendar.min.js'; ?>"></script>
	<script src="<?php echo base_url() . 'assets/admin/assets/extra-libs/taskboard/js/jquery-ui.min.js'; ?>"></script>
	<!-- base_url --> 
</head>

<body> 
	<div id="permission_div">
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
					<a class="navbar-brand" href="<?= base_url() ?>Dashboard">
						<!-- Logo icon -->
						<b class="logo-icon">
							<img src="" alt="homepage" style="height:50px;display:none;" id="collapseImg">
						</b>
						<!--End Logo icon -->
						<!-- Logo text -->
						<span class="logo-text">
							<img src="<?php echo base_url() . 'assets/images/MYVEGIZ LOGO 3rd-01-cut.png'; ?>" class="light-logo" alt="Myvegiz" width="100%">
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
						<li class="nav-item">
							<a class="nav-link">
								<div class="form-check form-check-inline">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="maintenanceMode" value="1">
										<label class="custom-control-label cust_check" for="maintenanceMode">Online</label>
									</div>
								</div>
							</a>
						</li>
						<li class="nav-item vendor-name">
						<?php echo '<h4 class="mb-0">' . $userFname . " " . $userLname . '</h4>';?>
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
						<li class="nav-item">
						    <button class="nav-link text-primary" id="button"><i class="fas fa-volume-up"></i></button>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link userdiv dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<div><?= $usrName ?></div>
							</a>
							<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
								<span class="with-arrow"><span class="bg-myvegiz"></span></span>
								<div class="d-flex no-block align-items-center p-15 bg-myvegiz text-white m-b-10">
									<div class="m-l-10">
										<?php
										echo '<h4 class="m-b-0">' . $userFname . " " . $userLname . '</h4>';
										switch ($role) {
											case "ADM":
												echo '<p class="m-b-0">Admin</p>';
												break;
											case "USR":
												echo '<p class="m-b-0">Employee</p>';
												break;
											case "VEN":
												echo '<p class="m-b-0">Vendor</p>';
												break;
										}
										?>
										<p class=" m-b-0"><?php echo $email ?></p>
									</div>
								</div>
								<a class="dropdown-item" href="<?php echo base_url() . 'MyProfile/viewHours/' . $code ?>"><i class="fa fa-clock m-r-5 m-l-5"></i> Working Hours</a>
								<a class="dropdown-item" href="<?php echo base_url() . 'MyProfile/edit/' . $code ?>"><i class="fa fa-user m-r-5 m-l-5"></i> Edit Profile</a>
								<a class="dropdown-item" href="<?php echo base_url() . 'authentication/logout' ?>"><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
								<div class="dropdown-divider"></div>
								
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
				<div class="modal-content" style="width:600px;height:600px;padding:20px;overflow-y:scroll">
					<div class="modal-header">
						<h4 class="modal-title">View Details</h4>
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
						<!-- under vegetable category -->
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Home'; ?>" class="sidebar-link"><i class="mdi mdi-food-fork-drink"></i><span class="hide-menu">Recent Orders</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Dashboard'; ?>" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Vendoritem/listRecords'; ?>" class="sidebar-link"><i class="mdi mdi-food-fork-drink"></i><span class="hide-menu">Vendor Items</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Pendingorders'; ?>" class="sidebar-link"><i class="mdi mdi-pot"></i><span class="hide-menu">Pending Order</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Confirmorder'; ?>" class="sidebar-link"><i class="mdi mdi-pot-mix"></i><span class="hide-menu">Confirm Order</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Serviceavailable'; ?>" class="sidebar-link"><i class="mdi mdi mdi-checkbox-multiple-marked-outline"></i><span class="hide-menu">Item Service Avaliabilty</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Serviceavailable/addonServiceavailable'; ?>" class="sidebar-link"><i class="mdi mdi-playlist-check"></i><span class="hide-menu">Add on Service Avaliabilty</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Offer/listRecords'; ?>" class="sidebar-link"><i class="mdi mdi-tag-plus"></i><span class="hide-menu">Offer</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url() . 'Commission/listRecords'; ?>" class="sidebar-link"><i class="mdi mdi-tag-plus"></i><span class="hide-menu">Commission</span></a></li>
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
			$(document).ready(function() {
				$(':input[type="text"]').each(function() {
					$(this).attr('autocomplete', 'off');
				});
				$(".userData").click(function() {
					var code = $(this).data('seq');
					$.ajax({
						url: base_path + "MyProfile/view",
						method: "GET",
						data: {
							code: code
						},
						datatype: "text",
						success: function(data) {
							$(".userModal-body").html(data);
						}
					});
				});
				var divMain = $("#main-wrapper").attr('data-sidebartype');
				$('.sidebartoggler').on('click', function() {
					var divMain = $("#main-wrapper").attr('data-sidebartype');
					if (divMain == 'mini-sidebar') {
						$('#collapseImg').hide();
						$('.light-logo').show();
					} else if (divMain == 'full') {
						$('#collapseImg').show();
						var imgUrl = "<?php echo base_url() . 'assets/images/MYVEGIZ LOGO 1st -01-cut.png'; ?>";
						$('#collapseImg').attr('src', imgUrl);
						$('.light-logo').hide();
					}
				});
				getRestaurantStatus();
			});
			function getRestaurantStatus(){
			    $.ajax({
					url:base_path+"Home/getRestaurantStatus",
					method:"GET",
					data:{},
					datatype:"text",
					success: function(data)
					{
						var da = JSON.parse(data);
						if(da['settingValue']==1){
							$("#maintenanceMode").prop("checked",true);
							$(".cust_check").text("Offline");
						} else {
							$("#maintenanceMode").prop("checked",false);
						    $(".cust_check").text("Online");
						}  
					},
				});
			}
			$("#maintenanceMode").change(function(){
			    var settingValue= 0;
				if($(this).is(":checked")){
					settingValue= 1;
				}  
				$.ajax({					
					url: base_path+"Home/updateRestaurantStatus",
					method:"post",
					data:{"settingValue":settingValue}, 
					datatype:"text",
					success: function(data)
					{
						 getRestaurantStatus();
					}
				});
			});
		</script>
		<audio src="http://myvegiz.com/ringing.mp3" autoplay="true" id="noti" muted loop="true"></audio>
        