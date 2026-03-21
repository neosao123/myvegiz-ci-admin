<aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
						<li class="sidebar-item"><a href="<?php echo base_url().'admin';?>" class="sidebar-link"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu">Dashboard</span></a></li>  
						
						<!--Configuration-->
						<li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-wrench"></i><span class="hide-menu">Configuration </span></a>
							<ul aria-expanded="false" class="collapse first-level"> 
								<li class="sidebar-item"><a href="<?php echo base_url().'uom/listrecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Unit List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'subunit/listrecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Sub Unit List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'currency/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Currency List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'City/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> City List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Address/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Address List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Usermaster/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> User List </span></a></li>
						        <li class="sidebar-item"><a href="<?php echo base_url().'DeliveryCharge/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Company Info</span></a></li>
						        <li class="sidebar-item"><a href="<?php echo base_url().'Tag/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Tag List</span></a></li>
						        <li class="sidebar-item"><a href="<?php echo base_url().'DeliveryChargesSlots/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Delivery Charges Slot List</span></a></li>
								<!--Settings-->
						        <li class="sidebar-item"><a href="<?php echo base_url().'Settings/listRecords';?>" class="sidebar-link"><i class="mdi mdi-settings"></i><span class="hide-menu">Settings</span></a></li>
						
							</ul>
						</li>	
						
						<!--Main Category and Ho,e slider-->
						<li class="sidebar-item"><a href="<?php echo base_url().'Maincategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-sitemap"></i><span class="hide-menu">Main Category</span></a></li> 
						<li class="sidebar-item"><a href="<?php echo base_url().'Homeslider/listRecords';?>" class="sidebar-link"><i class="mdi mdi-arrange-send-backward"></i><span class="hide-menu">Home Slider</span></a></li> 
						
						<!--Employee-->
						<!--<li class="sidebar-item"> 
						    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-tune-vertical"></i><span class="hide-menu">Employee </span></a>
							<ul aria-expanded="false" class="collapse  first-level">
								<li class="sidebar-item"><a href="<?php echo base_url().'employee/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Employee List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'jobtype/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Job Type List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'designation/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Designation List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'salaryGrade/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Salary Grade List </span></a></li>
							</ul>
						</li>-->
						
						<!--Android users--> 
						<li class="sidebar-item"><a href="<?php echo base_url().'AndroidUsers/listRecords';?>" class="sidebar-link"><i class="mdi mdi-cellphone-settings"></i><span class="hide-menu"> Android Users </span></a></li> 
						
						<!--Reset Password-->
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-lock"></i><span class="hide-menu">Reset Password</span></a>
							<ul aria-expanded="false" class="collapse  first-level">
								<li class="sidebar-item"><a href="<?php echo base_url().'resetpassword/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Android Users </span></a></li>  
								<li class="sidebar-item"><a href="<?php echo base_url().'resetpassword/listDeliveryRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Delivery Boy </span></a></li>  
							</ul>
						</li>
						
						<!--Food/Restaureants-->
						<li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-food-fork-drink"></i><span class="hide-menu">Food/Restaurents</span></a>
							<ul aria-expanded="false" class="collapse first-level"> 
							    <li class="sidebar-item"> 
									<a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-wrench"></i><span class="hide-menu">Configuration </span></a>
									<ul aria-expanded="false" class="collapse  second-level">
        								<li class="sidebar-item"><a href="<?php echo base_url().'Entitycategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Entity Category</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Menucategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Menu Category</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/MenuSubcategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Menu Sub Category</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Foodslider/listRecords';?>" class="sidebar-link"><i class="mdi mdi-arrange-send-backward"></i><span class="hide-menu">Food Slider</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Cuisine/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Cuisine</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Vendor/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Vendoritem/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Items</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Offer/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Coupons</span></a></li>
        								<li class="sidebar-item"><a href="<?php echo base_url().'Food/Vendorconfiguration/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Configuration</span></a></li>
        							</ul>
        							<!--Food Order and Cancelled order-->
        							<li class="sidebar-item"><a href="<?php echo base_url().'foodOrderList/FoodOrderList';?>" class="sidebar-link"><i class="mdi mdi-food"></i><span class="hide-menu">Food Order</span></a></li>
						            <li class="sidebar-item"><a href="<?php echo base_url().'userCancelOrder/UserCancelOrder';?>" class="sidebar-link"><i class="mdi mdi-close-box"></i><span class="hide-menu">User Cancel/Reject Order</span></a></li>
						      </li>
						    </ul>
					    </li>
						
                        <!-- vegetable -->
						<li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-food-apple"></i><span class="hide-menu">Vegetable</span></a>
							<ul aria-expanded="false" class="collapse first-level">
							    <li class="sidebar-item"><a href="<?php echo base_url().'Category/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Product Category List</span></a></li>
							    <li class="sidebar-item"><a href="<?php echo base_url().'Subcategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Product Subcategory List</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Product/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu"> Product List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Slider/listRecords';?>" class="sidebar-link"><i class="mdi mdi-arrange-send-backward"></i><span class="hide-menu"> Slider List </span></a></li>  
							</ul>
						</li>
						
						<!--Grocery-->
						<li class="sidebar-item"> 
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-hexagon-multiple"></i><span class="hide-menu">Grocery</span></a>
							<ul aria-expanded="false" class="collapse first-level">
								<li class="sidebar-item"><a href="<?php echo base_url().'Grocerycategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Grocery Category List</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Grocerysubcategory/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Grocery Subcategory List</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Groceryproduct/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Grocery Product List </span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Groceryslider/listRecords';?>" class="sidebar-link"><i class="mdi mdi-arrange-send-backward"></i><span class="hide-menu">Grocery Slider List </span></a></li>
								
							</ul>
						</li>
						
						<!-- Vegi and Grocery Orders -->
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart"></i><span class="hide-menu">Veggies/Grocery Orders</span></a>
							<ul aria-expanded="false" class="collapse  first-level">
								<li class="sidebar-item"><a href="<?php echo base_url().'Order/pendingListRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Pending List </span></a></li>  
								<li class="sidebar-item"><a href="<?php echo base_url().'Order/placedListRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Placed List </span></a></li>  
								<li class="sidebar-item"><a href="<?php echo base_url().'Order/serviceUnavailableRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Service Unavailable List </span></a></li>
							</ul>
						</li>
						
						<!--Push Notification-->
						<li class="sidebar-item"><a href="<?php echo base_url().'Notification/listRecords';?>" class="sidebar-link"><i class="mdi mdi-bell"></i><span class="hide-menu">Push Notification</span></a></li>
						<li class="sidebar-item"><a href="<?php echo base_url().'CustomerNotification/listRecords';?>" class="sidebar-link"><i class="mdi mdi-bell"></i><span class="hide-menu">Push Customer Notification</span></a></li>
						
						<!-- Others -->
						<li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-library-plus"></i><span class="hide-menu">Other</span></a>
							<ul aria-expanded="false" class="collapse  first-level">
								<li class="sidebar-item"><a href="<?php echo base_url().'Couponoffer/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vegetable Offers</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Reports/orderListRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Order Reports</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Reports/penaltyReport';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Delivery Boy Penalty Report</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Reports/vendorpenaltyReport';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Penalty Report</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Commission/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Delivery Boy Commission</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Vendorordercommission/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Order Commission</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'Vendorcommission/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">Vendor Payment</span></a></li>
								<li class="sidebar-item"><a href="<?php echo base_url().'AppAlert/listRecords';?>" class="sidebar-link"><i class="mdi mdi-adjust"></i><span class="hide-menu">App Alert</span></a></li>
							</ul>
						</li>
						
						
						
						
					</ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>