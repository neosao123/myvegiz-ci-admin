    
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
                    <h4 class="page-title">Office</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                
                                <li class="breadcrumb-item">Create Office</li>
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
         <h4 class="card-title">Custom Office Information</h4>
         <h6 class="card-subtitle"></h6>
           <form method="post" action="<?php echo base_url().'index.php/repeat/save';?>" class="tab-wizard wizard-circle">
             <!-- Step 1 -->
             <h6>Office Info</h6>
                <section>
                
                      <!-- <span class="section"> -->
                         
                         <h3>Office  Information </h3>
                      <!-- </span> -->
                    
                      <div class="form-row">
                         <div class="col-md-6 mb-3">
                            <label for="officeName">Office Name: </label>
                            <div class="controls">
                               <input type="text" id="officeName" name="officeName" class="form-control" placeholder="Enter text here" >
                            </div>
                         </div>
                         <div class="col-md-6 mb-3">
                            <label for="entityName">Entity Name: </label>
                            <div class="clearfix">
                               <select class="form-control form-control col-xs-12" name="entityName" id="entity"  >
                                  <option value="" selected>Select Entity</option>
                                  <?php
                                     foreach($entities->result() as $entity){
                                     echo '<option value="' . $entity->code . '">' . $entity->entityName . '</option>';
                                     }
                                     ?>
                               </select>
                            </div>
                         </div>
                      </div>
                      
                     <!--  <div class="repeater-default m-t-30">
                         <div data-repeater-list="">
                             <div data-repeater-item=""> -->
                               <span> 
                                  <h4>Office Contact </h4>
                               </span>
                                  <div class="form-row" >
                                     <div class="col-md-6 mb-3">
                                        <label for="contactNo">Contact No: </label>
                                        <input type="text" id="contactNo" name="contactNo[]" class="form-control"  placeholder="Enter text here" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                     </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="website">Website: </label>
                                        <div class="controls">
                                           <input type="text" id="website" name="website[]" class="form-control"  placeholder="Enter text here">
                                        </div>
                                     </div>
                                  </div>
                                  <div class="form-row">
                                     <div class="col-md-6 mb-3">
                                        <label for="facebook">Facebook: </label>
                                        <input type="text" id="facebook" name="facebook[]" class="form-control"  placeholder="Enter text here">
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                     </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="googlePlus">Google Plus: </label>
                                        <div class="controls">
                                           <input type="text" id="googlePlus" name="googlePlus[]" class="form-control"  placeholder="Enter text here">
                                        </div>
                                     </div>
                                  </div>
                                  <div class="form-row">
                                     <div class="col-md-6 mb-3">
                                        <label for="twitter">Twitter: </label>
                                        <div class="controls">
                                           <input type="text" id="twitter" name="twitter[]" class="form-control"  placeholder="Enter text here" >
                                        </div>
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                     </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="linkedIn">Linked In: </label>
                                        <div class="controls">
                                           <input type="text" id="linkedIn" name="linkedIn[]" class="form-control"  placeholder="Enter text here" >
                                        </div>
                                     </div>
                                  </div>
                                  <div class="form-row">
                                     <div class="col-md-6 mb-3">
                                        <label for="instagaram">Instagram: </label>
                                        <input type="text" id="instagaram" name="instagaram[]" class="form-control"  placeholder="Enter text here" >
                                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                     </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="email">Email: </label>
                                        <div class="controls">
                                           <input type="text" id="email" name="email[]" class="form-control"  placeholder="Enter text here" >
                                        </div>
                                     </div>
                                  </div>
                                  <div class="form-row">
                                     <div class="col-md-10"></div>
                                  <button class="btn btn-success" type="button" onclick="office_fields();">Add</button>
                               </div>
                                <div class="row">
                                  <div id="office_fields" class="col-md-12 m-t-20">
                                  </div>
                               </div>
                             </section>
                             <!--   </div>
                            </div>
                            
                            <button data-repeater-create="" class="btn btn-info waves-effect waves-light" type="button">Add
                            </button>
                      </div> -->
                     
                      <!-- rowOffice -->
            <!-- Step 2 -->
            <h6>Person Contact</h6>
              <section> 
                <!-- <div class="repeater-default m-t-30">
                  <div data-repeater-list="">
                   <div data-repeater-item=""> -->
                    
                      <span><h4>Contact Person </h4></span>
                       <div class="form-row">
                          <div class="col-md-6 mb-3">
                                <label for="personName">Person Name: </label>
                                <input type="text" id="personName" name="personName[]" class="form-control"  placeholder="Enter text here">
                                <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                          </div>
                              <div class="col-md-6 mb-3">
                                <label for="personDesignation">Person Designation: </label>
                                <div class="controls">
                                <input type="text" id="personDesignation" name="personDesignation[]" class="form-control"  placeholder="Enter text here" >
                              </div>
                          </div>    
                      </div>
                      <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="personDepartment">Person Department: </label>
                                <input type="text" id="personDepartment" name="personDepartment[]" class="form-control"  placeholder="Enter text here" >
                            <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="personContact">Person Contact: </label>
                                <div class="controls">
                                    <input type="text" id="personContact" name="personContact[]" class="form-control" placeholder="Enter text here" >
                                </div>
                            </div>    
                      </div>
                      <div class="form-row">
                                <div class="col-md-6 mb-3">
                                        <label for="personContact2">Person Contact 2: </label>
                                      <div class="controls">
                                          <input type="text" id="personContact2" name="personContact2[]" class="form-control" placeholder="Enter text here" >
                                      </div>
                                      <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                                </div>
                                <div class="col-md-6 mb-3">
                                       <label for="personEmail"> Person Email: </label>
                                    <div class="controls">
                                        <input type="text" id="personEmail" name="personEmail[]" class="form-control" placeholder="Enter text here" >
                                    </div>
                                </div>    
                      </div>
                      <div class="form-row">
                            <div class="col-md-6 mb-3">
                            <label for="personPhoto">Person Photo: </label>
                            <input type="file" id="personPhoto" name="personPhoto[]" class="form-control" >
                            <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                            </div>
                      </div>
                                <div class="form-row">
                                     <div class="col-md-10"></div>
                                  <button class="btn btn-success" type="button" onclick="person_fields();">Add</button>
                               </div>
                               <div class="row">
                                  <div id="person_fields" class="col-md-12 m-t-20">
                                  </div>
                               </div>
                             </section>
                      <!--  <div class="form-row">
                                     <div class="col-md-10"></div>
                                <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light m-l-10" type="button">Delete Form
                         </button>
                      </div> -->
                   <!--  </div>
                   </div>
                            
                       <button data-repeater-create="" type="button" class="btn btn-info waves-effect waves-light">Add
                       </button>
                 </div> -->
                <!-- </section> -->
       <!-- Step 3 -->
            <h6>Address</h6>
          <section>
      <!-- <div class="repeater-default m-t-30">
        <div data-repeater-list="">
         <div data-repeater-item=""> -->
            
            <span><h4>Address </h4></span>
            <div class="form-row">
                    <div class="col-md-12 mb-3">
                    <label for="address">Address: </label>
                          <input type="text" id="address" name="address[]" class="form-control" placeholder="Enter text here" >
                    <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                    </div>
            </div>
            <div class="form-row">
                    <div class="col-md-4 mb-3">
                              <label for="pinCode">Pin Code: </label>
                          <div class="controls">
                              <input type="text" id="pinCode" name="pinCode[]" class="form-control" placeholder="Enter text here" >
                          </div>
                    </div>    
                    <div class="col-md-4 mb-3">
                        <label for="place">Place: </label>
                            <input type="text" id="place" name="place[]" class="form-control"  placeholder="Enter text here">
                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                    </div>
                    <div class="col-md-4 mb-3">
                          <label for="taluka">Taluka: </label>
                              <input type="text" id="taluka" name="taluka[]" class="form-control" placeholder="Enter text here" >
                          <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                    </div>
            </div>
            <div class="form-row">
                      <div class="col-md-4 mb-3">
                          <label for="district">District: </label>
                            <div class="controls">
                                <input type="text" id="district" name="district[]" class="form-control"  placeholder="Enter text here">
                            </div>
                      </div>    
                      <div class="col-md-4 mb-3">
                          <label for="state">State: </label>
                              <div class="controls">
                                  <input type="text" id="state" name="state[]" class="form-control" placeholder="Enter text here" >
                              </div>
                          <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                      </div>
                      <div class="col-md-4 mb-3">
                          <label for="country"> Country: </label>
                            <div class="controls">
                              <input type="text" id="country" name="country[]" class="form-control" placeholder="Enter text here" >
                            </div>
                      </div>    
            </div>
            <!--  <div class="form-row">
                   <div class="col-md-10"></div>
                        <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light m-l-10" type="button">Delete Form
                   </button>
            </div> -->
                  <div class="form-row">
                       <div class="col-md-10"></div>
                       <button class="btn btn-success" type="button" onclick="address_fields();">Add</button>
                 </div>
                  <div class="row">
                    <div id="address_fields" class="col-md-12 m-t-20">
                    </div>
                 </div>
              </section>
         <!--  </div>
        </div>
                <button data-repeater-create="" type="button" class="btn btn-info waves-effect waves-light">Add
                </button>
      </div> -->
   
            <!-- Step 4 -->
    <h2>Bank Documentaion</h2>
      <section>
        <!-- <div class="repeater-default m-t-30">
        <div data-repeater-list="">
         <div data-repeater-item=""> -->
            <span ><h4>Bank Document </h4></span>
            <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="bankAccountNumber">Bank Account Number: </label>
                            <input type="text" id="bankAccountNumber" name="bankAccountNumber[]" class="form-control" placeholder="Enter text here" >
                        <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bankIFSCCode">Bank IFSC Code: </label>
                          <div class="controls">
                            <input type="text" id="bankIFSCCode" name="bankIFSCCode[]" class="form-control" placeholder="Enter text here" >
                          </div>
                    </div>    
            </div>
            <div class="form-row">
                  <div class="col-md-6 mb-3">
                      <label for="bankPassbookFile">Bank Passbook File: </label>
                          <input type="file" id="bankPassbookFile" name="bankPassbookFile[]" class="form-control" >
                      <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                  </div>
                  <div class="col-md-6 mb-3">
                      <label for="bankAccountHolderName">Bank Account Holder Name: </label>
                          <div class="controls">
                            <input type="text" id="bankAccountHolderName" name="bankAccountHolderName[]" class="form-control" placeholder="Enter text here" >
                          </div>
                  </div>    
            </div>
            <div class="form-row">
                  <div class="col-md-6 mb-3">
                      <label for="bankBranchName">Bank Branch Name: </label>
                          <div class="controls">
                            <input type="text" id="bankBranchName" name="bankBranchName[]" class="form-control" placeholder="Enter text here" >
                      </div>
                      <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> -->
                  </div>
                  <div class="col-md-6 mb-3">
                      <label for="bankMICRCode"> Bank MICR Code: </label>
                          <div class="controls">
                            <input type="text" id="bankMICRCode" name="bankMICRCode[]" class="form-control" placeholder="Enter text here" >
                          </div>
                  </div>    
            </div>
             <div class="form-row">
                   <div class="col-md-10"></div>
                <button class="btn btn-success" type="button" onclick="bank_fields();">Add</button>
             </div>
             <div class="row">
                <div id="bank_fields" class="col-md-12 m-t-20">
                </div>
             </div>
             <!-- <div class="form-row">
                    <div class="col-md-10"></div>
                      <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light m-l-10" type="button">Delete Form
                      </button>
              </div> -->
        <!-- </div>
      </div>
         <button data-repeater-create="" type="button" class="btn btn-info waves-effect waves-light">Add
                </button>
     </div>  -->
        <div class="form-group row">
           <div class="col-md-4"></div>
            <div class="custom-control custom-checkbox mr-sm-2">
                <div class="custom-control custom-checkbox ">

                    <input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">
                    <label class="custom-control-label" for="isActive"><strong>Active</strong></label>
                    <button class="btn btn-success" type="submit" >Submit Form</submit>
                </div>
            </div>
        </div>   
       </section>
     
            <!-- ============================================================== -->
     </form>
    </div>
   </div>
  </div>
   <!--    </div>
   </div> -->
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
   <!-- Start Page Content -->

 </div> <!-- ============================================================== -->

<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ==============================================================
   ============================================================== -->
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
    <script>
//contract operation rates
var room = 1;

    function office_fields() {
       $.ajax({
           url: "http://sgm-pc/wf-rocktech/index.php/OperationRates/getWorkOrderData",
           type: 'GET',
           success: function(data) {
             var operationhtml="";

              var obj=JSON.parse(data);
            for(i=0;i<obj.operationmaster.length;i++)
            {
                operationhtml+='<option value="'+obj.operationmaster[i].code+'">'+obj.operationmaster[i].operationName+'</option>';
            }

        room++;
        var objTo = document.getElementById('office_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass" + room);
        var rdiv = 'removeclass' + room;
        divtest.innerHTML = '<span> <h4>Office Contact </h4> </span> <div class="form-row" > <div class="col-md-6 mb-3"> <label for="contactNo">Contact No: </label> <input type="text" id="contactNo" name="contactNo" class="form-control"  placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="website">Website: </label> <div class="controls"> <input type="text" id="website" name="website[]" class="form-control"  placeholder="Enter text here"> </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="facebook">Facebook: </label> <input type="text" id="facebook" name="facebook[]" class="form-control"  placeholder="Enter text here"> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="googlePlus">Google Plus: </label> <div class="controls"> <input type="text" id="googlePlus" name="googlePlus[]" class="form-control"  placeholder="Enter text here"> </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="twitter">Twitter: </label> <div class="controls"> <input type="text" id="twitter" name="twitter[]" class="form-control"  placeholder="Enter text here" > </div> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="linkedIn">Linked In: </label> <div class="controls"> <input type="text" id="linkedIn" name="linkedIn[]" class="form-control"  placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="instagaram">Instagram: </label> <input type="text" id="instagaram" name="instagaram[]" class="form-control"  placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="email">Email: </label> <div class="controls"> <input type="text" id="email" name="email[]" class="form-control"  placeholder="Enter text here" > </div> </div><div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_office_fields(' + room + ');"> Delete Form </button> </div>';

        objTo.appendChild(divtest)
       },
               error: function(xhr, ajaxOptions, thrownError) {
               var errorMsg = 'Ajax request failed: ' + xhr.responseText;
               alert(errorMsg);
               console.log("Ajax Request for patient data failed : " + errorMsg);
             }
    });
}

    function remove_office_fields(rid) {
        $('.removeclass' + rid).remove();
    }


//start person fields
    function person_fields() {
       $.ajax({
           url: "http://sgm-pc/wf-rocktech/index.php/OperationRates/getWorkOrderData",
           type: 'GET',
           success: function(data) {
             var operationhtml="";

              var obj=JSON.parse(data);
            for(i=0;i<obj.operationmaster.length;i++)
            {
                operationhtml+='<option value="'+obj.operationmaster[i].code+'">'+obj.operationmaster[i].operationName+'</option>';
            }

        room++;
        var objTo = document.getElementById('person_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass" + room);
        var rdiv = 'removeclass' + room;
        divtest.innerHTML = ' <span><h4>Contact Person </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personName">Person Name: </label> <input type="text" id="personName" name="personName[]" class="form-control"  placeholder="Enter text here"> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="personDesignation">Person Designation: </label> <div class="controls"> <input type="text" id="personDesignation" name="personDesignation[]" class="form-control"  placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personDepartment">Person Department: </label> <input type="text" id="personDepartment" name="personDepartment[]" class="form-control"  placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="personContact">Person Contact: </label> <div class="controls"> <input type="text" id="personContact" name="personContact[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personContact2">Person Contact 2: </label> <div class="controls"> <input type="text" id="personContact2" name="personContact2[]" class="form-control" placeholder="Enter text here" > </div> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="personEmail"> Person Email: </label> <div class="controls"> <input type="text" id="personEmail" name="personEmail[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personPhoto">Person Photo: </label> <input type="file" id="personPhoto" name="personPhoto[]" class="form-control" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> </div><div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_person_fields(' + room + ');"> Delete Form </button> </div>';
        objTo.appendChild(divtest)
       },
               error: function(xhr, ajaxOptions, thrownError) {
               var errorMsg = 'Ajax request failed: ' + xhr.responseText;
               alert(errorMsg);
               console.log("Ajax Request for patient data failed : " + errorMsg);
             }
    });
}

    function remove_person_fields(rid) {
        $('.removeclass' + rid).remove();
    } //end person fields

    //start address fields
    function address_fields() {
       $.ajax({
           url: "http://sgm-pc/wf-rocktech/index.php/OperationRates/getWorkOrderData",
           type: 'GET',
           success: function(data) {
             var operationhtml="";

              var obj=JSON.parse(data);
            for(i=0;i<obj.operationmaster.length;i++)
            {
                operationhtml+='<option value="'+obj.operationmaster[i].code+'">'+obj.operationmaster[i].operationName+'</option>';
            }

        room++;
        var objTo = document.getElementById('address_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass" + room);
        var rdiv = 'removeclass' + room;
        divtest.innerHTML = '<span><h4>Address </h4></span> <div class="form-row"> <div class="col-md-12 mb-3"> <label for="address">Address: </label> <input type="text" id="address" name="address[]" class="form-control" placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="pinCode">Pin Code: </label> <div class="controls"> <input type="text" id="pinCode" name="pinCode[]" class="form-control" placeholder="Enter text here" > </div> </div> <div class="col-md-4 mb-3"> <label for="place">Place: </label> <input type="text" id="place" name="place[]" class="form-control"  placeholder="Enter text here"> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-4 mb-3"> <label for="taluka">Taluka: </label> <input type="text" id="taluka" name="taluka[]" class="form-control" placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="district">District: </label> <div class="controls"> <input type="text" id="district" name="district[]" class="form-control"  placeholder="Enter text here"> </div> </div> <div class="col-md-4 mb-3"> <label for="state">State: </label> <div class="controls"> <input type="text" id="state" name="state[]" class="form-control" placeholder="Enter text here" > </div> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-4 mb-3"> <label for="country"> Country: </label> <div class="controls"> <input type="text" id="country" name="country[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_address_fields(' + room + ');"> Delete Form </button> </div>'; 

        objTo.appendChild(divtest)
       },
               error: function(xhr, ajaxOptions, thrownError) {
               var errorMsg = 'Ajax request failed: ' + xhr.responseText;
               alert(errorMsg);
               console.log("Ajax Request for patient data failed : " + errorMsg);
             }
    });
}

    function remove_address_fields(rid) {
        $('.removeclass' + rid).remove();
    } //end address fields


    //start address fields
    function bank_fields() {
       $.ajax({
           url: "http://sgm-pc/wf-rocktech/index.php/OperationRates/getWorkOrderData",
           type: 'GET',
           success: function(data) {
             var operationhtml="";

              var obj=JSON.parse(data);
            for(i=0;i<obj.operationmaster.length;i++)
            {
                operationhtml+='<option value="'+obj.operationmaster[i].code+'">'+obj.operationmaster[i].operationName+'</option>';
            }

        room++;
        var objTo = document.getElementById('bank_fields')
        var divtest = document.createElement("div");
        divtest.setAttribute("class", "form-group removeclass" + room);
        var rdiv = 'removeclass' + room;
        divtest.innerHTML = '<span ><h4>Bank Document </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankAccountNumber">Bank Account Number: </label> <input type="text" id="bankAccountNumber" name="bankAccountNumber[]" class="form-control" placeholder="Enter text here" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="bankIFSCCode">Bank IFSC Code: </label> <div class="controls"> <input type="text" id="bankIFSCCode" name="bankIFSCCode[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankPassbookFile">Bank Passbook File: </label> <input type="file" id="bankPassbookFile" name="bankPassbookFile[]" class="form-control" > <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="bankAccountHolderName">Bank Account Holder Name: </label> <div class="controls"> <input type="text" id="bankAccountHolderName" name="bankAccountHolderName[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankBranchName">Bank Branch Name: </label> <div class="controls"> <input type="text" id="bankBranchName" name="bankBranchName[]" class="form-control" placeholder="Enter text here" > </div> <!-- <div class="form-control-feedback"><small>Add <code></code> attribute to field for  validation.</small></div> --> </div> <div class="col-md-6 mb-3"> <label for="bankMICRCode"> Bank MICR Code: </label> <div class="controls"> <input type="text" id="bankMICRCode" name="bankMICRCode[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_bank_fields(' + room + ');"> Delete Form </button> </div>';
        objTo.appendChild(divtest)
       },
               error: function(xhr, ajaxOptions, thrownError) {
               var errorMsg = 'Ajax request failed: ' + xhr.responseText;
               alert(errorMsg);
               console.log("Ajax Request for patient data failed : " + errorMsg);
             }
    });
}

    function remove_bank_fields(rid) {
        $('.removeclass' + rid).remove();
    } //end bank fields
  </script>

    <!-- All Jquery -->
    <!-- ============================================================== -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?php echo base_url().'assets/admin/assets/libs/popper.js/dist/umd/popper.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/assets/libs/bootstrap/dist/js/bootstrap.min.js';?>"></script>
    <!-- apps -->
    <script src="<?php echo base_url().'assets/admin/dist/js/app.min.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin/dist/js/app.init.js';?>"></script>
    <script src="<?php echo base_url().'assets/admin./dist/js/app-style-switcher.js';?>"></script>
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
        <script>
           $( document ).ready(function(){
                //Fetch Pincode related to user input pincode
                $('#pinCode').keyup (function () {
                  if($(this).val().length > 3)
                  {
                    var pin_code = $(this).val();
                    $.ajax({
                             url:'<?php echo site_url('addressInfo/getAllData'); ?>',
                             method:"GET",
                             data:{pinCode:pin_code},
                             datatype:"text",
                             success: function(data)
                             {
                               $("#pinCodeList").html(data);
                             }
                    });
                  }
                });
                 //From Pin Code get Address data from DB
                  $('#pinCode').change(function(){
                      var pin_code = $(this).val();
                      $.ajax({
                               url:'<?php echo site_url('addressInfo/getAddressFromPin'); ?>',
                               method:"GET",
                               data:{pinCode:pin_code},
                               datatype:"text",
                               success: function(data)
                               {
                                 var res = $.parseJSON(data);
                                 $("#place").val(res.place);
                                 $("#taluka").val(res.taluka);
                                 $("#district").val(res.district);
                                 $("#state").val(res.state);
                                 $("#country").val(res.country);
                                
                               }
                      });
                  });
            });
                  



                  //Custom design form example
          $(".tab-wizard").steps({
              headerTag: "h6",
              bodyTag: "section",
              transitionEffect: "fade",
              titleTemplate: '<span class="step">#index#</span> #title#',
              labels: {
                  finish: "Submit"
              },
              onFinished: function(event, currentIndex) {
            // swal("Form Submitted!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat eleifend ex semper, lobortis purus sed.");
                              //Serialize form all data
                  var fd = new FormData();

                  //Form data except files
                  var other_data = $('section').serializeArray();
                  $.each(other_data,function(key,input){
                      fd.append(input.name,input.value);

                  });

                  //Form File Data 
                  var personPhoto = $("#personPhoto").prop("files")[0];
                  var bankPassbookFile = $("#bankPassbookFile").prop("files")[0];

                  fd.append("personPhoto", personPhoto);
                  fd.append("bankPassbookFile", bankPassbookFile);

                  //Form Data Send to employee controller for save
                  $.ajax({
                    url: '<?php echo site_url('office/save'); ?>',
                    data: fd,
                    enctype: 'multipart/form-data',
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(data){
                        console.log(data);
                    }
                });

        }
    });
        </script>
        <script src="<?php echo base_url().'assets/admin/assets/libs/jquery.repeater/jquery.repeater.min.js';?>"></script>
        <script src="<?php echo base_url().'assets/admin/assets/extra-libs/jquery.repeater/dff.js';?>"></script>
        <script src="<?php echo base_url().'assets/admin/assets/extra-libs/jquery.repeater/repeater-init.js';?>"></script>


  </body>

</html>


