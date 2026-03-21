<h4 class="card-title">Update Main Category Image</h4>
<form method="post" id="updateForm" enctype="multipart/form-data" novalidate>
<!--action="< ?php echo base_url().'index.php/Vendor/save';?>"-->
	<?php 
	$path ="";
	foreach($query->result() as $row){  
		$image = $row->categoryPhoto;
		
		if($image)
		{
			$path = base_url('uploads/maincategory/'.$image);
		}
	?>
	<div class="form-row">
		<?php
			if($image)
			{
				echo '<div class="col-md-12 mb-3" id="imageDiv"><img class="img-fluid" src="'.$path.'">';
				echo '<button type="button" id="deleteImage" class="btn btn-danger btn-sm d-none" data-id="'.$row->code.'">Delete Image</button></div>';
			}
		?>		
		<div class="col-md-12 mb-3">
			<label for="imagePath">Category Image : <b style="color:red">*</b></label>
			<input type="file" id="imagePath" name="imagePath" class="form-control"  required>
			<span class="text-danger" id="err"></span>
		</div>
	</div>
 
	<input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly>
	<div class="text-xs-right">	
		<button type="button" class="btn btn-success" id="updateMainCategory">Submit</button>
		<button type="button" class="btn btn-reset" id="backToAdd">Back</button>
	</div>
	<?php } ?>
</form>