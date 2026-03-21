
 <div class="page-wrapper">
 	<div class="page-breadcrumb">
 		<div class="row">
 			<div class="col-12 align-self-center">
 				<h4 class="page-title">Order Details</h4>
 				<div class="d-flex align-items-center">
 					<nav aria-label="breadcrumb">
 						<ol class="breadcrumb">
 							<li class="breadcrumb-item"><a href="#">Home</a></li>
 							<li class="breadcrumb-item active" aria-current="page">View</li>
 						</ol>
 					</nav>
 				</div>
 			</div>
 		</div>
 	</div>
 	<div class="container-fluid col-md-12">
 		<div class="col-12">
 			<div class="card">
 				<div class="card-body">
 					<h4 class="card-title">Tracking Details </h4>
 					<hr />
					<?php 
					if($query){
						foreach($query->result() as $row) { ?>
 					<div class="form-row">
 						<div class="col-md-4 mb-3">
 							<label for="orderCode"> Order Code : </label>
 							<input type="text" id="orderCode" name="orderCode" class="form-control-line" value="<?= $row->code ?>" readonly>
						</div>
 						<div class="col-md-3 mb-3">
 							<label for="deliveryBoyCode"> Delivery Boy: </label>
 							<input type="text" id="deliveryBoyCode" name="deliveryBoyCode" value="<?= $row->deliveryBoyCode ?>" class="form-control-line" readonly>
 						</div>
 						<div class="col-md-5 mb-3">
 							<label for="mobile"> Mobile: </label>
 							<input type="text" id="mobile" name="mobile" value="<?= $row->mobile ?>" class="form-control-line" disabled>
 						</div>
 					</div>
						<?php }
					}?>
 					<div class="form-row">
 						<?php 
						 echo $latitude.'#'.$longitude;
						?>
 					</div>
				</div>
 			</div>
		</div>
 	</div>
 </div>
 <script>
 </script>