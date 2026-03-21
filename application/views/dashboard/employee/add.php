  
  <!--============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 align-self-center">
                    <h4 class="page-title">Employee</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                
                                <li class="breadcrumb-item">Create Employee</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid col-md-12">
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->

                <div class="col-12">
                    <div class="card">
                        <div class="card-body wizard-content">
                            <h4 class="card-title">Create Employee</h4>
                            <h6 class="card-subtitle"></h6>
                            <form class="validation-wizard wizard-circle" id="myForm" method="post" enctype="multipart/form-data" id="wizard"> 
                                <!-- Step 1 -->
                                <h6>Personal Info</h6>
                                <section>
                                    <div class="row">
										<div class="col-md-3">
                                            <div class="form-group">
                                                <label for="title">Title : </label>
                                                <select class="custom-select form-control" id="title" name="title">
                                                    <option value="" readonly>Select Option</option>
                                                    <option value="mr">Mr.</option>
                                                    <option value="ms">Ms.</option>
													<option value="mrs">Mrs.</option>
												</select>
											</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="firstName">First Name :<b style="color:red">*</b></label>
                                                <input type="text" class="form-control required" id="firstName" name="firstName" autofocus> </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="middleName">Middle Name :<b style="color:red">*</b></label>
                                                <input type="text" class="form-control required" id="middleName" name="middleName"> </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="lastName">Last Name :<b style="color:red">*</b></label>
                                                <input type="text" class="form-control required" id="lastName" name="lastName"> </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                          <label for="dob">Date of Birth :<b style="color:red">*</b></label>
                                          <div class="controls">
                                              <input type="text" class="form-control date-inputmask required" id="dob" name="dob">
                                              
                                          </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" align="center">
                                                <label for="gender">Gender :<b style="color:red">*</b></label>
                                                <div class="c-inputs-stacked">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="male" name="gender" value="m" class="custom-control-input" required>
                                                        <label class="custom-control-label" for="male">Male</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="female" name="gender" value="f" class="custom-control-input" required>
                                                        <label class="custom-control-label" for="female">Female</label>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" align="center">
                                                <label for="maritalStatus">Marital Status :</label>
                                                <div class="c-inputs-stacked">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="single" name="maritalStatus" value="s" class="custom-control-input">
                                                        <label class="custom-control-label" for="single">Single</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input type="radio" id="married" name="maritalStatus" value="m" class="custom-control-input">
                                                        <label class="custom-control-label" for="married">Married</label>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="location1">Blood Group :</label>
                                                <select class="custom-select form-control" id="bloodGroup" name="bloodGroup">
                                                    <option value="" readonly>Select Option</option>
                                                    <option value="a+">A+</option>
                                                    <option value="a-">A-</option>
                                                    <option value="b+">B+</option>
                                                    <option value="b-">B-</option>
                                                    <option value="ab+">AB+</option>
                                                    <option value="ab-">AB-</option>
                                                    <option value="o+">O+</option>
                                                    <option value="o-">O-</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </section>
			         		  <!-- Step 2 -->
								  <h6>Address Info</h6>
								  <section>
									 <h4>Current Address:</h4>
									 <div class="form-row">
										<div class="col-md-8 mb-3">
										   <label for="currentAddress">Address :</label>
										   <input type="text" id="currentAddress" name="currentAddress" class="form-control">
										</div>
										<div class="col-md-4 mb-3">
										   <label for="currentLandmark">Landmark : </label>
										   <div class="controls">
											  <input type="text" id="currentLandmark" name="currentLandmark" class="form-control">
										   </div>
										</div>
									 </div>
									 <div class="form-row">
										<div class="col-md-4 mb-3">
										   <label for="currentPlace">Place : </label>
										   <div class="input-group">
											  <input type="text" id="currentPlace"  name="currentPlace" list="currentPlaceList" class="form-control">
											  <div class="input-group-append">
												 <span class="input-group-text" id="currentPlaceSearch" style="display:none;position:RELATIVE;"> <i class="fa fa-spinner fa-spin loader" ></i></span>
											  </div>
										   </div>
										   <datalist id="currentPlaceList">
										   </datalist>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="currentPinCode">PinCode : </label>
										   <div class="controls">
											  <input type="number" list="pinCodeList" id="currentPinCode" name="currentPinCode" class="form-control" min="0">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="currentTaluka">Taluka : </label>
										   <input type="text" id="currentTaluka"  list="talukaList" name="currentTaluka" class="form-control">
										</div>
									 </div>
									 <div class="form-row">
										<div class="col-md-4 mb-3">
										   <label for="currentDistrict">District :</label>
										   <div class="controls">
											  <input type="text" id="currentDistrict" list="districtList" name="currentDistrict" class="form-control">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="currentState">State : </label>
										   <div class="controls">
											  <input type="text" id="currentState" list="stateList" name="currentState" class="form-control">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="currentCountry"> Country : </label>
										   <div class="controls">
											  <input type="text" id="currentCountry" name="currentCountry" class="form-control">
										   </div>
										</div>
									 </div>
									 <hr/>
									 <div class="form-row">
										<div class="col-sm-7 mb-3">
										   <h4>Permanent Address :</h4>
										</div>
										<div class="col-sm-5 mb-3">
										   <div class="custom-control custom-checkbox">
											  <input type="checkbox" class="custom-control-input" id="isPermanentAddressSame" value="1">
											  <label class="custom-control-label" for="isPermanentAddressSame">Check if Permanent address is same to Current address</label>
										   </div>
										</div>
									 </div>
									 <div class="form-row">
										<div class="col-md-8 mb-3">
										   <label for="permanentAddress">Address :</label>
										   <input type="text" id="permanentAddress" name="permanentAddress" class="form-control">
										</div>
										<div class="col-md-4 mb-3">
										   <label for="permanentLandmark">Landmark :</label>
										   <div class="controls">
											  <input type="text" id="permanentLandmark" name="permanentLandmark" class="form-control">
										   </div>
										</div>
									 </div>
									 <div class="form-row">
										<div class="col-md-4 mb-3">
										   <label for="permanentPlace">Place: </label>
										   <div class="input-group">
											  <input type="text" id="permanentPlace" list="permanentPlaceList" name="permanentPlace" class="form-control">
											  <div class="input-group-append">
												 <span class="input-group-text" id="permanentPlaceSearch" style="display:none;position:RELATIVE;"> <i class="fa fa-spinner fa-spin loader" ></i></span>
											  </div>
										   </div>
										   <datalist id="permanentPlaceList">
										   </datalist>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="permanentPinCode">PinCode :</label>
										   <div class="controls">
											  <input type="number" list="pinCodeList2" id="permanentPinCode" name="permanentPinCode" class="form-control" min="0">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="permanentTaluka">Taluka :</label>
										   <input type="text" id="permanentTaluka" list="talukaList2" name="permanentTaluka" class="form-control">
										</div>
									 </div>
									 <div class="form-row">
										<div class="col-md-4 mb-3">
										   <label for="permanentDistrict">District :/label>
										   <div class="controls">
											  <input type="text" id="permanentDistrict" list="districtList2" name="permanentDistrict" class="form-control">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="permanentState">State :</label>
										   <div class="controls">
											  <input type="text" id="permanentState" list="stateList2" name="permanentState" class="form-control">
										   </div>
										</div>
										<div class="col-md-4 mb-3">
										   <label for="permanentCountry"> Country :</label>
										   <div class="controls">
											  <input type="text" id="permanentCountry" name="permanentCountry" class="form-control">
										   </div>
										</div>
									 </div>
								  </section>
                                <!-- Step 3 -->
                                <h6>Contact Info</h6>
                                <section>
                                    <div class="form-row">
                                       <div class="col-md-4 mb-3">
                                          <label for="contact1">Contact Number: </label>
                                          <div class="input-group">
                                             <input type="text" onkeypress="return validateFloatKeyPress(this, event, 9, -1);" id="contact1" name="contact1" class="form-control" required>
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-phone"></i></span>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-4 mb-3">
                                          <label for="contact2">Alternative Contact Number: </label>
                                          <div class="input-group">
                                             <input type="text" id="contact2" name="contact2" onkeypress="return validateFloatKeyPress(this, event, 9, -1);" class="form-control">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-phone"></i></span>
                                             </div>
                                          </div>
                                          <!-- <div class="form-control-feedback"><small>Add <code>required</code> attribute to field for required validation.</small></div> -->
                                       </div>
                                       <div class="col-md-4 mb-3">
                                          <label for="email">Email: </label>
                                          <div class="input-group">
                                            <input type="email" id="email" name="email" class="form-control ">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-envelope-open"></i></span>
                                            </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="form-row">
                                       <div class="col-md-4 mb-3">
                                          <label for="fbLink">Facebook Link: </label>
                                          <div class="input-group">
                                             <input type="url" id="fbLink" name="fbLink" class="form-control">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-social-facebook"></i></span>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-4 mb-3">
                                          <label for="linkedInLink">LinkedIn Link: </label>
                                          <div class="input-group">
                                             <input type="url" id="linkedInLink" name="linkedInLink" class="form-control">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-social-linkedin"></i></span>
                                             </div>
                                          </div>
                                          <!-- <div class="form-control-feedback"><small>Add <code>required</code> attribute to field for required validation.</small></div> -->
                                       </div>
                                       <div class="col-md-4 mb-3">
                                          <label for="gPlusLink">GooglePlus Link: </label>
                                          <div class="input-group">
                                             <input type="url" id="gPlusLink" name="gPlusLink" class="form-control">
                                             <div class="input-group-append">
                                                <span class="input-group-text"><i class="icon-social-gplus"></i></span>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                </section>
                                <h6>Employment Information</h6>
                                <section>
                                    <div class="row">
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="deptCode">Department Name:<b style="color:red">*</b></label>
                                                <select class="custom-select form-control" id="deptCode" name="deptCode" required>
                                                     <option value="" readonly>Select Option</option>
                                                  <?php foreach($queryDept->result() as $dept)
                                                  {
                                                    echo "<option value='".$dept->code."'>".$dept->departmentName."</option>"; 
                                                  }?>
                                                </select>
                                            </div>
                                        </div>
										<div class="col-md-4">
                                         <div class="form-group">
                                            <label for="employmentStatus">Employment Status:<b style="color:red">*</b></label>
                                            <select class="custom-select form-control" id="employmentStatus" name="employmentStatus" required>
                                                <option value="" readonly>Select Option</option>
                                                <?php foreach($queryEmploymentStatus->result() as $empStatus)
                                                {
                                                  echo "<option value='".$empStatus->employmentStatusSName."'>".$empStatus->employmentStatusName."</option>"; 
                                                }?>
                                            </select>
                                        </div>
                                       </div>
									   <div class="col-md-4">
                                          <label for="empToken">Employee Token: </label>
                                          <div class="controls">
                                             <input type="text" id="empToken" name="empToken" class="form-control">
                                          </div>
                                       </div>
                                    </div>
                                   
                                    <div class="row">
                                      
                                      <div class="col-md-4">
                                          <label for="joiningDate">Joining Date:<b style="color:red">*</b></label>
                                          <div class="input-group">
                                              <input type="text" class="form-control date-inputmask " id="joiningDate" name="joiningDate" placeholder="dd/mm/yyyy" required>
                                              <div class="input-group-append">
                                                  <span class="input-group-text"><i class="icon-calender "></i></span>
                                              </div>
                                          </div>
                                      </div>
									  <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="jobType">Job type:<b style="color:red">*</b></label>
                                                <select class="custom-select form-control" id="jobType" name="jobType" required>
                                                    <option value="" readonly>Select Option</option>
                                                   <?php foreach($queryJobType->result() as $jobType)
                                                    {
                                                      echo "<option value='".$jobType->code."'>".$jobType->jobTypeName."</option>"; 
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
										
                                       <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="salaryGrade">Salary Grade:<b style="color:red">*</b></label>
                                                <select class="custom-select form-control" id="salaryGrade" name="salaryGrade" required>
                                                    <option value="" readonly>Select Option</option>
                                                     <?php foreach($querySalaryGrade->result() as $salGrade)
                                                    {
                                                      echo "<option value='".$salGrade->code."'>".$salGrade->salaryGradeName."</option>"; 
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
									   
                                      
                                    </div>
                                    <div class="row">
										
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="designation">Designation:<b style="color:red">*</b></label>
                                                <select class="custom-select form-control" id="designation" name="designation" required>
                                                    <option value="" readonly>Select Option</option>
                                                   <?php foreach($queryDesignation->result() as $designation)
                                                    {
                                                      echo "<option value='".$designation->code."'>".$designation->designationName."</option>"; 
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="reportingTo">Reporting To:</label>
												  <input type="text" class="custom-select form-control" list="employeeList" id="reportingTo" name="reportingTo">
                                                   <datalist id="employeeList">
												    
													<?php foreach($queryemployee->result() as $employee)
                                                    {
                                                      echo "<option value='".$employee->code."'>".$employee->firstName." ".$employee->middleName." ".$employee->lastName."</option>"; 
                                                    }?>
												   </datalist>
											</div>
										</div>
                                        
                                    </div>
									
										
                                </section>
                                <!-- Step 3 -->
                                <h6>Bank Account Information</h6>
                                <section>
                                    <div class="form-row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empBankName">Bank Name: </label>
                                        <div class="controls">
                                        <input type="text" id="empBankName" name="empBankName" class="form-control" >
                                        </div>
                                      </div> 
                                      <div class="col-md-6 mb-3">
                                        <label for="empBankAccountHolderName">Bank Account Holder Name: </label>
                                        <div class="controls">
                                        <input type="text" id="empBankAccountHolderName" name="empBankAccountHolderName" class="form-control" >
                                        </div>
                                      </div>    
                                    </div>
                                    <div class="form-row">
                                      <div class="col-md-4 mb-3">
                                        <label for="empBankBranchName">Bank Branch Name: </label>
                                        <div class="controls">
                                        <input type="text" id="empBankBranchName" name="empBankBranchName" class="form-control" >
                                        </div>
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-4 mb-3">
                                        <label for="empBankIfscCode">Bank IFSC Code: </label>
                                        <div class="controls">
                                        <input type="text" id="empBankIfscCode" name="empBankIfscCode" class="form-control" >
                                        </div>
                                      </div>  
                                      <div class="col-md-4 mb-3">
                                        <label for="empBankMICRCode"> Bank MICR Code: </label>
                                        <div class="controls">
                                        <input type="text" id="empBankMicrCode" name="empBankMicrCode" class="form-control" >
                                        </div>
                                      </div>    
                                    </div>
                                </section>
                                <!-- Step 4 -->
                                <h6>Documents Information</h6>
                                <section>
                                    <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empBankAccountNo">Bank Account Number: </label>
                                        <input type="number" id="empBankAccountNo" name="empBankAccountNo" class="form-control" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <label for="empBankPassbookFile">Bank Passbook File: </label>
                                        <input type="file" id="empBankPassbookFile" name="empBankPassbookFile" class="form-control" accept="image/*">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                    </div>
                                     <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empAdharNumber">Adhar Card Number: </label>
                                        <input type="aadhar" id="empAdharNumber" name="empAdharNumber" class="form-control" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <label for="empAdharFile">Adhar Card File: </label>
                                        <input type="file" id="empAdharFile" name="empAdharFile" class="form-control" accept="image/*">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                    </div>
                                     <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empPanNumber">PAN Card Number: </label>
                                        <input type="pan" id="empPanNumber" name="empPanNumber" class="form-control" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <label for="empPanFile">PAN Card File: </label>
                                        <input type="file" id="empPanFile" name="empPanFile" class="form-control" accept="image/*">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                    </div>
                                     <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empPfAccountNumber">P.F. Account Number: </label>
                                        <input type="text" id="empPfAccountNumber" name="empPfAccountNumber" class="form-control" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <label for="empPfAccountFile">P.F. Account File: </label>
                                        <input type="file" id="empPfAccountFile" name="empPfAccountFile" class="form-control" accept="image/*">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                    </div>
                                     <div class="row">
                                      <div class="col-md-6 mb-3">
                                        <label for="empEsiAccountNumber">E.S.I. Account Number: </label>
                                        <input type="text" id="empEsiAccountNumber" name="empEsiAccountNumber" class="form-control" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                      <div class="col-md-6 mb-3">
                                        <label for="empEsiAccountFile">E.S.I. Account File: </label>
                                        <input type="file" id="empEsiAccountFile" name="empEsiAccountFile" class="form-control" accept="image/*">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                      </div>
                                    </div>
									
									<div class="row">
										<div class="custom-control custom-checkbox mr-sm-2">
											<input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">
											<label class="custom-control-label" for="isActive">Active</label>
										</div>
									</div>    
                                </section>
                            </form>
                        </div>
                    </div>
                </div>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Right sidebar -->
            <!-- ============================================================== -->
            <!-- .right-sidebar -->
            <!-- ============================================================== -->
            <!-- End Right sidebar -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ==============================================================-->  
        <footer class="footer text-center">
                  All Rights Reserved by Rocktech. Designed and Developed by <a href="https://wolfox.in/">Wolfox Services Private Limited</a>.
            </footer>
       
         </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Main Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- customizer Panel -->
    <!-- ============================================================== -->
    <aside class="customizer">
        <a href="javascript:void(0)" class="service-panel-toggle"><i class="fa fa-spin fa-cog"></i></a>
        <div class="customizer-body">
            <ul class="nav customizer-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true"><i class="mdi mdi-wrench font-20"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#chat" role="tab" aria-controls="chat" aria-selected="false"><i class="mdi mdi-message-reply font-20"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false"><i class="mdi mdi-star-circle font-20"></i></a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <!-- Tab 1 -->
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="p-15 border-bottom">
                        <!-- Sidebar -->
                        <h5 class="font-medium m-b-10 m-t-10">Layout Settings</h5>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="theme-view" id="theme-view">
                            <label class="custom-control-label" for="theme-view">Dark Theme</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input sidebartoggler" name="collapssidebar" id="collapssidebar">
                            <label class="custom-control-label" for="collapssidebar">Collapse Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="sidebar-position" id="sidebar-position">
                            <label class="custom-control-label" for="sidebar-position">Fixed Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="header-position" id="header-position">
                            <label class="custom-control-label" for="header-position">Fixed Header</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="boxed-layout" id="boxed-layout">
                            <label class="custom-control-label" for="boxed-layout">Boxed Layout</label>
                        </div>
                    </div>
                    <div class="p-15 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium m-b-10 m-t-10">Logo Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin6"></a></li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                    <div class="p-15 border-bottom">
                        <!-- Navbar BG -->
                        <h5 class="font-medium m-b-10 m-t-10">Navbar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin6"></a></li>
                        </ul>
                        <!-- Navbar BG -->
                    </div>
                    <div class="p-15 border-bottom">
                        <!-- Logo BG -->
                        <h5 class="font-medium m-b-10 m-t-10">Sidebar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin6"></a></li>
                        </ul>
                        <!-- Logo BG -->
                    </div>
                </div>
                <!-- End Tab 1 -->
                <!-- Tab 2 -->
                <div class="tab-pane fade" id="chat" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <ul class="mailbox list-style-none m-t-20">
                        <li>
                            <div class="message-center chat-scroll">
                                <a href="javascript:void(0)" class="message-item" id='chat_user_1' data-user-id='1'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/1.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status online pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:30 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_2' data-user-id='2'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/2.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status busy pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_3' data-user-id='3'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/3.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status away pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span> <span class="time">9:08 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_4' data-user-id='4'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/4.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Nirav Joshi</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_5' data-user-id='5'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/5.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sunil Joshi</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_6' data-user-id='6'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/6.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Akshay Kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_7' data-user-id='7'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/7.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                <!-- Message -->
                                <!-- Message -->
                                <a href="javascript:void(0)" class="message-item" id='chat_user_8' data-user-id='8'>
                                    <span class="user-img"> <img src="<?php echo base_url().'assets/admin/assets/images/users/8.jpg';?>" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Varun Dhavan</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                <!-- Message -->
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- End Tab 2 -->
                <!-- Tab 3 -->
                <div class="tab-pane fade p-15" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <h6 class="m-t-20 m-b-20">Activity Timeline</h6>
                    <div class="steamline">
                        <div class="sl-item">
                            <div class="sl-left bg-success"> <i class="ti-user"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today <span class="sl-date"> 5pm</span></div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info"><i class="fas fa-image"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="<?php echo base_url().'assets/admin/assets/images/users/2.jpg';?>"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="<?php echo base_url().'assets/admin/assets/images/users/1.jpg';?>"> </div>
                            <div class="sl-right">
                                <div><a href="javascript:void(0)">Stephen</a> <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-primary"> <i class="ti-user"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today <span class="sl-date"> 5pm</span></div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info"><i class="fas fa-image"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="<?php echo base_url().'assets/admin/assets/images/users/4.jpg';?>"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="<?php echo base_url().'assets/admin/assets/images/users/6.jpg';?>"> </div>
                            <div class="sl-right">
                                <div><a href="javascript:void(0)">Stephen</a> <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Tab 3 -->
            </div>
        </div>
    </aside>
     <div class="chat-windows"></div>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?php echo base_url().'assets/admin/assets/libs/popper.js/dist/umd/popper.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/libs/bootstrap/dist/js/bootstrap.min.js';?>"></script>
    <!-- apps -->
    <script src="<?php echo base_url().'assets/admin/dist/js/app.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/dist/js/app.init.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/dist/js/app-style-switcher.js';?>"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?php echo base_url().'assets/admin/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/extra-libs/sparkline/sparkline.js';?>"></script>
    <!--Wave Effects -->
    <script src="<?php echo base_url().'assets/admin/dist/js/waves.js';?>"></script>
    <!--Menu sidebar -->
    <script src="<?php echo base_url().'assets/admin/dist/js/sidebarmenu.js';?>"></script>
    <!--Custom JavaScript -->
    <script src="<?php echo base_url().'assets/admin/dist/js/custom.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/extra-libs/jqbootstrapvalidation/validation.js';?>"></script>
    <!-- Form Wizard JS -->
    <script src="<?php echo base_url().'assets/admin/assets/libs/jquery-steps/build/jquery.steps.min.js';?>"></script>
   <!-- Masked JS -->
    <script src="<?php echo base_url().'assets/admin/assets/libs/inputmask/dist/min/jquery.inputmask.bundle.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/dist/js/pages/forms/mask/mask.init.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/libs/sweetalert2/dist/sweet-alert.min.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/libs/jquery-validation/dist/jquery.validate.min.js';?>"></script>
	<script src="<?= base_url()?>assets/js/decimaltext.js"></script>
	
	<script>
	
	
           $( document ).ready(function(){
			   
		
			  
		
		
		$('input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
                 // Disable keyboard scrolling
            $('input[type=number]').on('keydown',function(e) {
                var key = e.charCode || e.keyCode;
             // Disable Up and Down Arrows on Keyboard
                 if(key == 38 || key == 40 ) {
	            e.preventDefault();
                     } 
					 else 
					 {
	                     return;
                     }
          });
			  
	    			
					//Fetch Pincode related to user input pincode
	   $('#currentPlace').keyup (function () {
	   
	   if($(this).val().length > 3)
	   {  
	   $('#currentPlaceSearch').show();
	   var current_place = $(this).val();
	   $.ajax({
		url:'<?php echo site_url('addressInfo/getAllData'); ?>',
		method:"GET",
		data:{place:current_place},
		datatype:"text",
		success: function(data)
		{
		 $('#currentPlaceSearch').hide();
		  $("#currentPlaceList").html(data);
		}
		  });
	   }
	   });
	   //From Pin Code get Address data from DB
	   $('#currentPlace').change(function()
	   {
	   var current_place = $(this).val();
	   $.ajax({
		  url:'<?php echo site_url('addressInfo/getAddressFromPlace'); ?>',
		  method:"GET",
		  data:{place:current_place},
		  datatype:"text",
		  success: function(data)
		  {
		 var res = $.parseJSON(data);
		 $("#currentPinCode").val(res.pinCode);
		 $("#currentTaluka").val(res.taluka);
		 $("#currentDistrict").val(res.district);
		 $("#currentState").val(res.state);
		 $("#currentCountry").val(res.country);
		
		  }
		});
	   });
	   
	   //Fetch Permanent Pincode related to user input pincode
	   $('#permanentPlace').keyup (function () {
	   if($(this).val().length > 3)
	   {
	   var permanent_place = $(this).val();
	   $('#permanentPlaceSearch').show();
	   $.ajax({
		 url:'<?php echo site_url('addressInfo/getAllData'); ?>',
		 method:"GET",
		 data:{place:permanent_place},
		 datatype:"text",
		 success: function(data)
		 {
			$('#permanentPlaceSearch').hide();
		   $("#permanentPlaceList").html(data);
		   //console.log(data);
		 }
	   });
		 }
	   });
	   
	   //From Permanent Pin Code get Address data from DB
	   $('#permanentPlace').change(function(){
	   var permanent_place = $(this).val();
	   $.ajax({
		  url:'<?php echo site_url('addressInfo/getAddressFromPlace'); ?>',
		  method:"GET",
		  data:{place:permanent_place},
		  datatype:"text",
		  success: function(data)
		  {
		 var res = $.parseJSON(data);
		 $("#permanentPinCode").val(res.pinCode);
		 $("#permanentTaluka").val(res.taluka);
		 $("#permanentDistrict").val(res.district);
		 $("#permanentState").val(res.state);
		 $("#permanentCountry").val(res.country);
		
		  }
		   });
		 });
	   
                  
                 

                  $('#isPermanentAddressSame').change(function()
                  {
                    if($(this).prop('checked'))
                    {
                      $('#permanentAddress').attr('readonly', 'readonly');
                      $('#permanentLandmark').attr('readonly', 'readonly');
                      $('#permanentPinCode').attr('readonly', 'readonly');
                      $('#permanentPlace').attr('readonly', 'readonly');
                      $('#permanentTaluka').attr('readonly', 'readonly');
                      $('#permanentDistrict').attr('readonly', 'readonly');
                      $('#permanentState').attr('readonly', 'readonly');
                      $('#permanentCountry').attr('readonly', 'readonly');
                    

                      var currentAddress = $('#currentAddress').val();
                      var currentLandmark = $('#currentLandmark').val();
                      var currentPinCode = $('#currentPinCode').val();
                      var currentPlace = $('#currentPlace').val();
                      var currentTaluka = $('#currentTaluka').val();
                      var currentDistrict = $('#currentDistrict').val();
                      var currentState = $('#currentState').val();
                      var currentCountry = $('#currentCountry').val();

                      $('#permanentAddress').val(currentAddress);
                      $('#permanentLandmark').val(currentLandmark);
                      $('#permanentPinCode').val(currentPinCode);
                      $('#permanentPlace').val(currentPlace);
                      $('#permanentTaluka').val(currentTaluka);
                      $('#permanentDistrict').val(currentDistrict);
                      $('#permanentState').val(currentState);
                      $('#permanentCountry').val(currentCountry);
            
                  }
                  else
                  {
                    $('#permanentAddress').removeAttr('readonly', 'readonly');
                    $('#permanentLandmark').removeAttr('readonly', 'readonly');
                    $('#permanentPinCode').removeAttr('readonly', 'readonly');
                    $('#permanentPlace').removeAttr('readonly', 'readonly');
                    $('#permanentTaluka').removeAttr('readonly', 'readonly');
                    $('#permanentDistrict').removeAttr('readonly', 'readonly');
                    $('#permanentState').removeAttr('readonly', 'readonly');
                    $('#permanentCountry').removeAttr('readonly', 'readonly');
                  }
                });
              

           });
           ! function(window, document, $) {
                "use strict";
                $("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
            }(window, document, jQuery);
         //Mask for date and phone number
            jQuery(function($) {
                $.mask.definitions['~']='[+-]';
                $('.input-mask-date').mask('99-99-9999');
                $('.input-mask-phone').mask('(999) 999-9999');
            });
			
					
	//**********For Validation Step wizard Form******************//
	var form = $(".validation-wizard").show();

    $(".validation-wizard").steps({
        headerTag: "h6",
        bodyTag: "section",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: "Submit"
        },
        onStepChanging: function(event, currentIndex, newIndex) {
            return currentIndex > newIndex || !(3 === newIndex && Number($("#age-2").val()) < 18) && (currentIndex < newIndex && (form.find(".body:eq(" + newIndex + ") label.error").remove(), form.find(".body:eq(" + newIndex + ") .error").removeClass("error")), form.validate().settings.ignore = ":disabled,:hidden", form.valid())
        },
        onFinishing: function(event, currentIndex) {
            return form.validate().settings.ignore = ":disabled", form.valid()
        },
        onFinished: function(event, currentIndex) {
			//get all form data in fd object of formdata
			var fd = new FormData();
				  
			var currentField = null;
					
			//Form data except files
                  var other_data = $('form').serializeArray();
                  $.each(other_data,function(key,input){
                      fd.append(input.name,input.value);

                  });
                      
					  var isPermanentAddressSame = '';
				if($("#isPermanentAddressSame").prop("checked") == true){
					isPermanentAddressSame = 1;
				}  
                  //Form File Data 
                 
                 var empBankPassbookFile = $("#empBankPassbookFile").prop("files")[0];
				 var empAdharFile = $("#empAdharFile").prop("files")[0];
				 var empPanFile = $("#empPanFile").prop("files")[0];
				 var empPfAccountFile = $("#empPfAccountFile").prop("files")[0];
				 var empEsiAccountFile = $("#empEsiAccountFile").prop("files")[0];
			
                  fd.append("empBankPassbookFile", empBankPassbookFile);
				  fd.append("empAdharFile", empAdharFile);
                  fd.append("empPanFile", empPanFile);
				  fd.append("empPfAccountFile", empPfAccountFile);
                  fd.append("empEsiAccountFile", empEsiAccountFile);
				  
                  //Form Data Send to employee controller for save
                  $.ajax({
                    url: '<?php echo site_url('employee/save'); ?>',
                    data: fd,
                    enctype: 'multipart/form-data',
                    contentType: false,
                    processData: false,
                    type: 'POST',
                   success: function(data){
							 var obj=JSON.parse(data);
						if(data !='')
							  { 
							   if(obj.status)
						    {  
								 toastr.success(obj.message, 'Employee', { "progressBar": true });
								 
							}
							else
							  {
							  toastr.error(obj.message, 'Employee', { "progressBar": true });
						   }
					   }
				
						},
						
						complete: function(data){
							// location.reload();
							// $("#form-t-0").get(0).click();
							//$('#form').reset();
							//document.forms["form"].reset();
							setTimeout(location.reload.bind(location),5000);
							 
						}
                });
        }
		
    }), $(".validation-wizard").validate({
        ignore: "input[type=hidden]",
        errorClass: "text-danger",
        successClass: "text-success",
        highlight: function(element, errorClass) {
            $(element).removeClass(errorClass)
        },
        unhighlight: function(element, errorClass) {
            $(element).removeClass(errorClass)
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element)
        },
        rules: {
            email: {
                email: !0
            }
        }
       
          });
		  
		  </script>
		
 		  
    <script src="<?php echo base_url().'assets/admin/assets/libs/toastr/build/toastr.min.js';?>"></script>
       

  </body>

</html>