 <?php if($result) { 
		$row = $result; 
	?>
	<div class="form-row">
		<div class="col-md-12 mb-3"><label><b> Code:</b> </label>
			<input type="text" value="<?= $row['code'] ?>" class="form-control-line"  readonly>
		</div>
		<div class="col-md-12 mb-3"><label> <b>City Name:</b> </label>
			<input type="text" class="form-control-line" value="<?= $row['cityName'] ?>"  readonly>
		</div> 
		<?php $imagePath=$row['imagePath']?>
		<?php 
			if($row['type']=='image') {
			if($imagePath!="")
			{
				$imagePath = base_url().'uploads/slider/'.$imagePath.'?'.time();
			} else {
				$imagePath =  base_url().'assets/admin/assets/images/users/1.jpg';
			}
		?>		
		<div class="col-md-12 mb-3">
			<label><b>Grocery Slider Image:</b> </label>
			<img src ="<?= $imagePath?>" alt="Profile Photo" class="img-fluid"/>						 
		</div>
		<?php } else { ?>
			<div class="col-md-12 mb-3">
				<video width="100%" height="240" controls>
					<source src="<?= base_url().'uploads/slider/'.$imagePath ?>">										 
					Your browser does not support the video tag.
				</video>
			</div>
		<?php
		}
		$activeStatus = $row['isActive']==1 ? '<label class="label label-success">Active</label>' :'<label class="label label-danger">IN-Active</label>' ; 
		?>
		<div class="form-group"><?= $activeStatus ?></div>
	</div>
	<?php 
	}
	?>