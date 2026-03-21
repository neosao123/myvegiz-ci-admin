<h4 class="card-title">Update App Alert</h4>
<form method="post" id="updateAppAlertForm" enctype="multipart/form-data" novalidate>
<!--action="< ?php echo base_url().'index.php/Vendor/save';?>"-->
	<?php foreach($query->result() as $row){  ?>
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="title">Title : <b style="color:red">*</b></label>
			<input type="text" id="title" value="<?=$row->title?>" name="title" class="form-control"  required>
			<div class="invalid-feedback">
								Required Field!
							</div>
		</div>
	</div>
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="description">Description : <b style="color:red">*</b></label>
			<input type="text" id="description" value="<?=$row->description?>" name="description" class="form-control"  required>
			<div class="invalid-feedback">
								Required Field!
							</div>
		</div>
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
		<button type="button" class="btn btn-success" id="updateAppAlert">Submit</button>
		<button type="button" class="btn btn-reset" id="backToAdd">Add New</button>
	</div>
	<?php } ?>
</form>