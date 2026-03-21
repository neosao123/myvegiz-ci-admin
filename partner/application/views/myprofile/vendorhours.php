<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<style>
	.accordion .card {
		border-left: 2px solid #3e943f;
	}
	.form-control-line:disabled, .form-control-line[readonly] {
    background-color: rgb(255 255 255 / 25%);
		opacity: 1;
	}
	.form-group {
    margin-bottom: 0.5rem;
	}
</style>
<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Restaurant Hours</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Restaurant Hours</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-9">
		<?php if ($vendor) foreach ($vendor->result() as $row) {  ?>
			<form>
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Restaurant Info/Details</h4>
						<hr />
						<input type="hidden" id="vendorCode" name="vendorCode" value="<?= $row->code ?>" class="form-control-line" readonly>
						<div class="form-row">
							<div class="col-md-8 mb-3">
								<label for="entityName">Entity Name : </label>
								<input type="text" id="entityName" name="entityName" value="<?= $row->entityName ?>" class="form-control-line" readonly required>
							</div>
							<div class="col-md-4 mb-3">
								<label for="entityContact">Owner Contact : </label>
								<input type="text" id="entityContact" name="entityContact" value="<?= $row->ownerContact ?>" class="form-control-line" readonly>
							</div>
							<div class="col-md-12 mb-3">
								<label for="address">Address : </label>
								<input type="text" id="address" name="address" value="<?= $row->address ?>" class="form-control-line" readonly>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Restaurant Hours</h4>
						<hr />
						<?php
						$i = 0;
						?>
						<div id="accordion2" class="accordion" role="tablist" aria-multiselectable="true">
							<div class="card">
								<div class="card-header" role="tab" id="day_monday">
									<h5 class="mb-0">
										<a data-toggle="collapse" data-parent="#monday" href="#monday" aria-expanded="true" aria-controls="collapseOne">
											Monday
										</a>
									</h5>
								</div>
								<div id="monday" class="collapse show" role="tabpanel" aria-labelledby="day_monday">
									<div class="card-body">
										<div id="monday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'monday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div> 
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div> 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_tuesday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#tuesday" href="#tuesday" aria-expanded="false" aria-controls="collapseTwo">
											Tuesday
										</a>
									</h5>
								</div>
								<div id="tuesday" class="collapse show" role="tabpanel" aria-labelledby="day_tuesday">
									<div class="card-body">
										<div id="tuesday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'tuesday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div> 
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div> 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_wednesday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#wednesday" href="#wednesday" aria-expanded="false" aria-controls="collapseThree">
											Wednesday
										</a>
									</h5>
								</div>
								<div id="wednesday" class="collapse show" role="tabpanel" aria-labelledby="headingThree">
									<div class="card-body">
										<div id="wednesday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'wednesday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div> 
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div>
										 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_thursday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#thursday" href="#thursday" aria-expanded="false" aria-controls="collapseThree">
											Thursday
										</a>
									</h5>
								</div>
								<div id="thursday" class="collapse show" role="tabpanel" aria-labelledby="headingThree">
									<div class="card-body">
										<div id="thursday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'thursday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div> 
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div>
										 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_friday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#friday" href="#friday" aria-expanded="false" aria-controls="collapseThree">
											Friday
										</a>
									</h5>
								</div>
								<div id="friday" class="collapse show" role="tabpanel" aria-labelledby="headingThree">
									<div class="card-body">
										<div id="friday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'friday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div>
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div> 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_saturday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#saturday" href="#saturday" aria-expanded="false" aria-controls="collapseThree">
											Saturday
										</a>
									</h5>
								</div>
								<div id="saturday" class="collapse show" role="tabpanel" aria-labelledby="day_saturday">
									<div class="card-body">
										<div id="saturday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'saturday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div>
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div>
										 
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header" role="tab" id="day_sunday">
									<h5 class="mb-0">
										<a class="collapsed" data-toggle="collapse" data-parent="#sunday" href="#sunday" aria-expanded="false" aria-controls="collapseThree">
											Sunday
										</a>
									</h5>
								</div>
								<div id="sunday" class="collapse show" role="tabpanel" aria-labelledby="day_sunday">
									<div class="card-body">
										<div id="sunday_fields" class="m-b-20">
											<?php
											if ($vendorhours) {
												foreach ($vendorhours->result_array() as $r) {
													$day = $r['weekDay'];
													if ($day == 'sunday') {
														$i++;
														$dayRoom = $day . $i;
											?>
														<div class="form-group removeclass<?= $day . '_' . $i ?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control-line" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control-line pickatime" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>">
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control-line pickatime" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>">
																	</div>
																</div>
															</div>
														</div>
											<?php
													}
												}
											}
											?>
										</div> 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		<?php } ?>
	</div>
</div>
 