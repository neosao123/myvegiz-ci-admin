<aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
						<?php 
						$module_data = json_decode($module_data,true);
	                        foreach($module_data['ModulesData'] as $module) {
	                            if($module['type']==1){
                        ?>
							<li class="sidebar-item"><a href="<?php echo base_url(); ?><?=$module['routeUrl']; ?>" class="sidebar-link"><i class="mdi <?=$module['moduleIcon']; ?>"></i><span class="hide-menu"><?=$module['moduleName']; ?></span></a></li>

							<?php
                            }
                            else{
                        ?>
							<li class="sidebar-item">
		                        <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi <?=$module['moduleIcon']; ?>"></i> <span><?=$module['moduleName']; ?></span></a>
		                        <ul aria-expanded="false" class="collapse first-level"> 
		                        	<?php
		                            if($module['subStatus']){
		                            foreach($module['subModules'] as $submodule) 
		                            {?>
		                           <li class="sidebar-item"><a href="<?php echo base_url(); ?><?=$submodule['routeUrl']; ?>" class="sidebar-link"><i class="mdi <?=$submodule['subModuleIcon']; ?>"></i> <span class="hide-menu"><?=$submodule['subModuleName']; ?></span></a></li>
		                           <?php }
		                            }
		                            ?>
		                        </ul>
		                    </li>
							 <?php
                        }
                        }
                        ?>
					</ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>