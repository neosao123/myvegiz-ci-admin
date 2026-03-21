<h4 class="card-title">Update Cuisine</h4>
<form method="post" id="updateForm" enctype="multipart/form-data" novalidate>
	<?php 
	$path ="";
	foreach($query->result() as $row)
	{  
		$image = $row->cuisinePhoto; 
		if($image)
		{
			$path = base_url('uploads/cuisinemaster/'.$image);
		} 
		if($image)
		{ 
	?>
			<div class="row el-element-overlay">
				<div class="col-md-6 text-center">
					<div class="card">
						<div class="el-card-item">
							<div class="el-card-avatar el-overlay-1"> <img src="<?=$path?>" alt="user">
								<div class="el-overlay">
									<ul class="list-style-none el-info">
										<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?=$path?>"><i class="icon-magnifier"></i></a></li>
										<li class="el-item"><a class="btn default btn-outline el-link" href="javascript:void(0);"><i class="icon-link"></i></a></li>
									</ul>
								</div>
							</div> 
						</div>
					</div>
				</div>
			</div>
	<?php
		}
	?>	
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="cuisineName">Cuisine Name <b class="text-danger">*</b></label>
			<input type="text" id="cuisineName" name="cuisineName" class="form-control" value="<?= $row->cuisineName ?>"> 
		</div> 
		<div class="col-md-12 mb-3">
			<label for="imagePath">Cuisine Image :</label>
			<input type="file" id="imagePath" name="imagePath" class="form-control">
		</div> 
	</div> 
	<div class="form-row">
	    <span class="text-danger" id="err"></span>
	</div>
	<div class="form-group">
		<div class="custom-control custom-checkbox mr-sm-2">
			<div class="custom-control custom-checkbox">
				<?php 
					if($row->isActive == "1"){
						echo "<input type='checkbox'   value='1' class='custom-control-input' id='isActive' name='isActive' checked>
						<label class='custom-control-label' for='isActive'>Active</label>";
					}else{ 
						echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive'>
						<label class='custom-control-label' for='isActive'>Active</label>";
					}
				?>
			</div>
		</div>
	</div>
	<input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly>
	<div class="text-xs-right">	
		<button type="button" class="btn btn-success" id="updateCuisine">Submit</button>
		<button type="button" class="btn btn-reset" id="backToAdd">Back</button>
	</div>
	<?php } ?>
</form>