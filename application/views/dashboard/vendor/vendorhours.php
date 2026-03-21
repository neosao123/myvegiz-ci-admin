<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<style>
	.accordion .card {
		border-left: 2px solid #3e943f;
	}
	.form-control:disabled, .form-control[readonly] {
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
				<h4 class="page-title">Vendor Hours</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vendor Hours</li>
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
						<h4 class="card-title">Vendor Details</h4>
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
						<h4 class="card-title">Hours Entry</h4>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="dayMonday" name="day[]" placeholder="Day" value="monday">
													<input type="text" readonly class="form-control pickatime" id="fromTimemonday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input type="text" readonly class="form-control pickatime" id="toTimemonday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('monday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="dayTuesdDay" name="day[]" placeholder="Day" value="tuesday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimetuesday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimetuesday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('tuesday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="dayWednesday" name="day[]" placeholder="Day" value="wednesday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimewednesday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimewednesday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('wednesday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime" id="fromTime<?= $dayRoom ?>" data-weekday="<?= $day?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime" id="toTime<?= $dayRoom ?>" data-weekday="<?= $day?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="dayThursday" name="day[]" placeholder="Day" value="thursday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimethursday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimethursday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('thursday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="dayFriday" name="day[]" placeholder="Day" value="friday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimefriday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimefriday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('friday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="daySaturday" name="day[]" placeholder="Day" value="saturday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimesaturday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimesaturday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('saturday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
														<div class="form-group removeclass<?=$r['code']?>">
															<div class="row">
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="fromTime<?= $dayRoom ?>">From Time</label>
																		<input type="hidden" readonly class="form-control" id="day<?= $dayRoom ?>" name="day[]" placeholder="Day" value="<?= $day ?>">
																		<input ttype="text" readonly class="form-control pickatime " data-when="from" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="fromTime<?= $dayRoom ?>" name="fromTime[]" placeholder="From Time" value="<?= $r['fromTime']!='' ? date('h:i A',strtotime($r['fromTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['fromTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label for="toTime<?= $dayRoom ?>">To Time</label>
																		<input ttype="text" readonly class="form-control pickatime " data-when="to" data-weekday="<?= $day?>" data-linecode="<?=$r['code']?>" id="toTime<?= $dayRoom ?>" name="toTime[]" placeholder="To Time" value="<?= $r['toTime']!='' ? date('h:i A',strtotime($r['toTime'])) : '' ?>" data-previous='<?= date('h:i A',strtotime($r['toTime'])) ?>'>
																	</div>
																</div>
																<div class="col-sm-2">
																	<div class="form-group mt-4">
																		<button class="btn btn-danger" onclick="remove_hours_line(<?= $i ?>,'<?= $day ?>','delete','<?= $r['code'] ?>');" type="button"><i class="fa fa-trash"></i></button>
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
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<input type="hidden" readonly class="form-control" id="daySunday" name="day[]" placeholder="Day" value="sunday">
													<input ttype="text" readonly class="form-control pickatime" id="fromTimesunday" name="fromTime[]" placeholder="From Time">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<input ttype="text" readonly class="form-control pickatime" id="toTimesunday" name="toTime[]" placeholder="To Time">
												</div>
											</div>
											<div class="col-sm-2">
												<div class="form-group">
													<button class="btn btn-success" type="button" onclick="day_fields('sunday');"><i class="fa fa-plus"></i></button>
												</div>
											</div>
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
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script> 
	$('.pickatime').timepicker({
		timeFormat: 'h:mm p',
		interval: 30, 
		dynamic: true,
		dropdown: true,
		scrollbar: true,
		change: function() {
			debugger
			var lineCode = $(this).data('linecode');
			var id = $(this).attr("id");
			var vendorCode = $('#vendorCode').val();
			var weekDay = $(this).data("weekday");
			var when = $(this).data('when');
			var previous = $(this).data('previous');
			var time = $(this).val().trim();
			var updateto ="";
			if(when=='from'){
				updateto = 'from';
			} else {
				updateto = 'to';
			} 
			if(lineCode!='' && lineCode!=undefined){
			$.ajax({
				type: "post",
				url: base_path+"Vendor/updateRestaurantHour",
				data: {'lineCode':lineCode,'time':time,'updateTo':updateto,'vendorCode':vendorCode,'weekDay':weekDay},
				success: function (response) {
					debugger 
					var result = JSON.parse(response);
					if(result.status){
						toastr.success(result.message,"Serving Hours",{"progressBar":true});
					}else{
						toastr.error(result.message, "Serving Hours", {
							"progressBar": true
						});
						$('#'+id).val(previous)
					}
				}
			});
			}
		}
	});
	var room = "<?=$i?>";
	room = Number(room); 
	
	function day_fields(day) {
		var fromTime = $("#fromTime" + day).val().trim();
		var toTime = $("#toTime" + day).val().trim();
		var vendorCode = $("#vendorCode").val().trim();
		if (fromTime != "" && toTime != "") {
			$.ajax({
				type: "post",
				url: base_path + "Vendor/saveHours",
				data: {
					"weekDay": day,
					'fromTime': fromTime,
					'toTime': toTime,
					'vendorCode': vendorCode
				},
				success: function(response) {
					var result = JSON.parse(response);
					if (result.status) {
						var hourlineCode = result.lineCode;
						room++;
						var objTo = document.getElementById(day + '_fields')
						var divtest = document.createElement("div");
						divtest.setAttribute("class", "form-group "); 
						divtest.setAttribute("class","removeclass"+hourlineCode);
						var rdiv = 'removeclass' + room;
						divtest.innerHTML = `
								<div class="row"> 
										<div class="col-sm-4">
												<div class="form-group">
													<label for="fromTime${day+room}">From Time</label>
													<input type="hidden" readonly class="form-control" id="day${day+room}" name="day[]" placeholder="Day" value="${day}">
													<input type="text" readonly class="form-control pickatime" data-when="from" data-linecode="${hourlineCode}" id="fromTime${day+room}" name="fromTime[]" placeholder="From Time" value="${fromTime}">
												</div>
										</div>
										<div class="col-sm-4">
												<div class="form-group">
													<label for="toTime${day+room}">To Time</label>
													<input ttype="text" readonly class="form-control pickatime" data-when="to" data-linecode="${hourlineCode}" id="toTime${day+room}" name="toTime[]" placeholder="To Time" value="${toTime}">
												</div>
										</div>
										<div class="col-sm-2">
												<div class="form-group mt-4">
														<button class="btn btn-danger" type="button" onclick="remove_hours_line('${room}','${day}','delete','${hourlineCode}');"> <i class="fa fa-trash"></i></button>
												</div>
										</div>
								</div>
							`;
						objTo.appendChild(divtest);
						$("#fromTime" + day).val("");
						$("#toTime" + day).val("");
						toastr.success("Record Added Successfully", "Serving Hours", {
							"progressBar": true
						});
						$('.pickatime').timepicker({
							timeFormat: 'h:mm p',
							interval: 30, 
							dynamic: true,
							dropdown: true,
							scrollbar: true,
							change: function() {
								var lineCode = $(this).data('linecode');
								var when = $(this).data('when');
								var time = $(this).val().trim();
								var updateto ="";
								if(when=='from'){
									updateto = 'from';
								} else {
									updateto = 'to';
								} 
								$.ajax({
									type: "post",
									url: base_path+"Vendor/updateRestaurantHour",
									data: {'lineCode':lineCode,'time':time,'updateTo':updateto},
									success: function (response) {
										var result = JSON.parse(response);
										if(result.status){
											toastr.success(result.message,"Serving Hours",{"progressBar":true});
										}
									}
								});
							}
						});
					} else {
						$("#fromTime" + day).val("");
						$("#toTime" + day).val("");
						toastr.error(result.message, "Serving Hours", {
							"progressBar": true
						});
						return false;
					}
				}
			}); 
		} else {
			$("#fromTime" + day).val("");
			$("#toTime" + day).val("");
			toastr.error("Please enter From-Time and To-Time", "Serving Hours", {
				"progressBar": true
			});
			false;
		}
	}

	function remove_hours_line(rid, day, flag, code) { 
		if (flag == 'add') {
			$('.removeclass' + day + '_' + rid).remove();
		} else {
			$.ajax({
				type: "post",
				url: base_path + "Vendor/deleteHourLine",
				data: {
					"lineCode": code
				},
				success: function(response) {
					var result = JSON.parse(response);
					if (result.status) {
						$('.removeclass' +code).remove();
						toastr.success("Record Deleted Successfully", "Serving Hours", {
							"progressBar": true
						});
						return false;
					} else {
						toastr.error("Failed to delete the record! Please try later...", "Serving Hours", {
							"progressBar": true
						});
						return false;
					}
				}
			});
		}
	}
</script>