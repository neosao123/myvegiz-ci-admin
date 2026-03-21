
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>

<!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Invoice</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Library</li>
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
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
				
                    <div class="col-md-12">
					<div class="col-md-12">
								<div class="text-right">
									<button id="print" class="btn btn-default btn-outline" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
								</div>
							</div>
                        <div class="card card-body printableArea mt-3" style = "-webkit-print-color-adjust: exact;">
						
                            <h3><center>Employee Profile </center> <span class=""></span></h3>
							<hr>
							<div class="col-md-12">
							<?php 
					foreach($query->result() as $row)
	//print_r( $data['query']->result());
                            {?>
	<p class="m-t-30"><b>Joining Date :</b> <?=$row->joiningDate?>
 <table >
    <tr>
      <th height="29" colspan="6" style= "background: lightgray;" scope="row"><center> Personal Details</center></th>
												
    </tr>
    <tr>
      <th height="37" colspan="2" scope="row"><label><b> Employee Name :</b></label></th>
      <td colspan="2"><?=$row->firstName.' '.$row->middleName.' '.$row->lastName?> </td>
      <th width="131"><label><b> Date of Birth : </b> </label></th>
	  <?php
		$dobDateFormat = DateTime::createFromFormat('Y-m-d',$row->dob);
		$dob = $dobDateFormat->format('d/m/Y');
		?>
      <td width="223"><?= $dob?></td>
    </tr>
    <tr>
      <th  scope="row"><label><b> Gender</b> </label> 
        : </th> 
	 <?php
		
		$gender=$row->gender;
		if($gender=="m")
		{
		  $gender="male";
		}
		  else{
			  $gender="female";
		  }
		?>
		 <td  scope="row"><?= $gender;?></td>
      <th scope="row"><label><b> Marital Status </b> </label></th>
	  <?php
		
		$status=$row->maritalStatus;
		if($status=="s")
		{
		  $status="single";
		}
		  else{
			  $status="married";
		  }
		?>
      <td  scope="row"><?= $status?></td>
      <th  scope="row"><label><b> Blood Group :</label> </b> </th>
      <td  scope="row"><?= $row->bloodGroup?></td>
    </tr>
    <tr>
      <th height="33" colspan="6" scope="row" style= "background: lightgray;"><center><b> Address Details</b>  </center></th>
    </tr>
    <tr>
      <th height="65" colspan="2" scope="row"><label><b> Current Address :</b> </label> </th>
      <td colspan="4" scope="row"> <?=$row->currentAddress.'-'.$row->currentPinCode.','.$row->currentLandmark.',
                                              '.$row->currentPlace.','.$row->currentTaluka.',
                                                '.$row->currentDistrict.','.$row->currentState.','.$row->currentCountry?></td>
    </tr>
    <tr>
      <th height="76" colspan="2" scope="row"><label><b> Permanent Address :</b> </label> </th>
      <td colspan="4" scope="row"><?= $row->permanentAddress.'-'.$row->permanentPinCode.','.$row->permanentLandmark.',
                                              '.$row->permanentPlace.','.$row->permanentTaluka.',
                                                '.$row->permanentDistrict.','.$row->permanentState.','.$row->permanentCountry?> </td>
    </tr>
    <tr>
      <th  colspan="6" scope="row" style= "background: lightgray;"><center><b> Contact Information </b> </center> </th>
    </tr>
    <tr>
      <th  height="34" scope="row"><label><b> Contact Number:</b> </label> </th>
      <td  colspan="2" scope="row"><?= $row->contact1?></td>
      <th scope="row"><label><b> Alternate Contact Number:</b> </label> </th>
      <td colspan="2" scope="row"><?= $row->contact2?></td>
    </tr>
    <tr>
      <th  scope="row"><label><b> Email:</b> </label></th>
      <td  colspan="2" scope="row"><?= $row->email?></td>
      <th  scope="row"><label><b>Facebook Link :</b></label> </th>
      <td colspan="2" scope="row"><?= $row->fbLink?></td>
    </tr>
    <tr>
      <th  scope="row"><label><b> Linked In : </b> </label></th>
      <td  colspan="2" scope="row"><?= $row->linkedInLink?> </td>
      <th  scope="row"><label><b> Google Plus:</b> </label></th>
      <td  colspan="2" scope="row"><?= $row->gPlusLink?></td>
    </tr>
    <tr>
      <th  colspan="6" scope="row" style= "background: lightgray;"><center><b> Employement Information</b> </center> </th>
    </tr>
	<?php 
	foreach($employee->result() as $emp)
	// print_r($employee->result());
	 {?>

                           
   
    <tr>
      <th  scope="row"><label><b> Department Name :</b> </label> </th>
      <td  colspan="2" scope="row"><?=$emp->departmentName_40?></td>
	  <th scope="row"><label><b> Employment Status :</b> </label> </th>
      <td  colspan="2" scope="row"><?=$row->employmentStatus?></td>
    
    </tr>
    <tr>
     
      
    </tr>
    <tr>
      <th  scope="row"><label><b> Employee Token : </b> </label></th>
      <td  colspan="2" scope="row"><?=$row->empToken?></td>
      <th  scope="row"><label><b> Job Type : </b> </label></th>
      <td  colspan="2" scope="row"><?=$emp->jobTypeName_10?></td>
    </tr>
    <tr>
	
      <th  scope="row"> <label><b> Salary Grade :</b> </label> </th>
      <td  colspan="2" scope="row"><?=$emp->salaryGradeName_20?></td>
      <th  scope="row"><label><b> Designation :</b> </label> </th>
      <td colspan="3" scope="row"><?=$emp->designationName_30?></td>
    </tr>
	   <?php }?>
    <tr>
      <th  colspan="6" scope="row" style= "background: lightgray;"><center><b> Bank Account Details</b>  </center></th>
    </tr>
    <tr>
      <th  scope="row"><label><b> Bank Name:</b> </label> </th>
      <td  colspan="2" scope="row"><?=$row->empBankName?> </td>
      <th  scope="row"><label><b> Bank Account Holder Name </b> </label> </th>
      <td  colspan="2" scope="row"><?=$row->empBankAccountHolderName?> </td>
    </tr>
    <tr>
      <th  scope="row"><label><b> Bank branch Name:</b> </label> </th>
      <td width="164"  scope="row"><?=$row->empBankBranchName?></td>
      <th   scope="row"><label><b> IFSC Code: </b> </label></th>
      <td  scope="row"><?=$row->empBankIfscCode?></td>
      <th width="32" scope="row"><label><b> MICR Code:</b> </label> </th>
      <td width="32" scope="row"><?=$row->empBankMicrCode?></td>
    </tr>
    <tr>
      <th  colspan="6" scope="row" style= "background: lightgray;"><center><b> Document For Submission :</b> </center> </th>
    </tr>
    <tr>
      <th  colspan="4" scope="row">Document</th>
      <th  scope="row">Yes</th>
	  <th  scope="row">No</th>

	  
    </tr>
    <tr>
      <th  colspan="4" scope="row"><label><b> Copy of Identity Proof(AADHAR Card)</b> </label></th>
      <th  scope="row"><label>
	  <?php
	  $aadhar="";
	  $aadhar=$row->empAdharFile;
	  if($aadhar=""){
		   echo '<input type="checkbox" name="checkbox"  disabled value="checkbox" />';
	  }else{
		  echo '<input type="checkbox" name="checkbox" disabled value="checkbox" checked/>';
	  }
	  ?>
       
      </label></th>
	  <th>
	  
		   <input type="checkbox" name="checkbox"   value="checkbox" />
	  
       
</th>
    </tr>
    <tr>
      <th  colspan="4" scope="row"><label><b> Copy of Bank Pasbook</b> </label> </th>
      <th  scope="row"><label>
        <?php
	  $passbook="";
	  $passbook=$row->empBankPassbookFile;
	  if($passbook==""){
		   echo '<input type="checkbox" name="checkbox" disabled value="checkbox" />';
	  }else{
		  echo '<input type="checkbox" name="checkbox" disabled value="checkbox" checked/>';
	  }
	  ?>
      </label></th>
	  <th>
	  
		   <input type="checkbox" name="checkbox"   value="checkbox" />
	  
       
</th>
    </tr>
    <tr>
      <th  colspan="4" scope="row"><label><b> Copy of Provident Fund File</b> </label> </th>
      <th  scope="row"><label>
       <?php
	  $pfaccount="";
	  $pfaccount=$row->empPfAccountFile;
	  if($pfaccount==""){
		   echo '<input type="checkbox" name="checkbox" disabled value="checkbox" />';
	  }else{
		  echo '<input type="checkbox" name="checkbox" disabled value="checkbox" checked/>';
	  }
	  ?>
      </label></th>
	  <th>
	  
		   <input type="checkbox" name="checkbox"   value="checkbox" />
	  
       
</th>
    </tr>
    <tr>
      <th  colspan="4" scope="row"><label><b> Copy of Identity Proof(PAN Card or Driving License)</b> </label></th>
      <th  scope="row"><label>
        <?php
	  $panfile="";
	  $panfile=$row->empPanFile;
	  if($panfile==""){
		   echo '<input type="checkbox" name="checkbox"disabled value="checkbox" />';
	  }else{
		  echo '<input type="checkbox" name="checkbox" disabled value="checkbox" checked/>';
	  }
	  ?>
      </label></th>
	  <th>
	  
		   <input type="checkbox" name="checkbox"   value="checkbox" />
	  
       
</th>
    </tr>
    <tr>
      <td  colspan="6" scope="row"><p>I herby declare that all the information furnished above is true to the best of my knowledge and belief i will do all my duties to the best of my ability while following all code of conduct of the company and maintaining required level of discipline by the company.</p>      </td>
    </tr>
    <tr>
      <td  scope="row"><b> Date : </b> </td>
      <td  colspan="3" scope="row">11-8-2018</td>
      <td  scope="row"><b> Place : </b> </td>
      <td  scope="row"><?=$row->permanentPlace?></td>
    </tr>
    <tr>
      <td  scope="row"><label><b>Reporting To :</b> </label>
        &nbsp;:</td>
      <td  colspan="5" scope="row"><?=$row->firstName.' '.$row->middleName.' '.$row->lastName?> </td>
    </tr>
	<?php }?>
  </table>
							
						
						</div>
				</div>
								
								
								
                              
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
            
			
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
			
			
			<script>
			//for print
		
			//Calculate total and total with tax in bottom of the screen
			$( document ).ready(function(){
			$(function () {
							$('input').blur();
						});
				 $(function() {
		  $("#print").click(function() {
			 
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = {
                mode: mode,
                popClose: close
				
            };
			
            $("div.printableArea").printArea(options);
		
         });
		});

		// $('#sendEmail').click(function() {
			// pdfId=Math.floor(Math.random() * 90 + 10);
			// alert(pdfId);
			// var pdf = new jsPDF('p', 'pt', 'letter');
			// pdf.addHTML($("div.printableArea"), function() {
				// pdf.save("employee"+pdfId+".pdf");
			
			// });
			// window.open('<?= base_url('index.php/Employee/listRecords');?>');
		// }); 
		
	});	

		
			</script>
			<style type="text/css"> 
  
  table {
		padding:20px;
		border:2px;
		
	 
    }
	label{
		Bold ;
		font-size: 15px;
		text-align: center;
	}
	 h4 {
    font-size: 18px;
	margin-bottom: 2px;
}

</style>