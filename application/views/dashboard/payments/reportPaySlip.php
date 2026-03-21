<div class="page-wrapper">
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-5 align-self-center">
					<h4 class="page-title">Payments</h4>
					<div class="d-flex align-items-center">
					   <nav aria-label="breadcrumb">
						  <ol class="breadcrumb">
							 <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							 <li class="breadcrumb-item active" aria-current="page"> Payslip</li>
						  </ol>
					   </nav>
					</div>
				</div>
			</div>
		</div>
		<div id="load-container" class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="col-md-12">
						<div class="text-right">
							<button id="sendEmail" class="btn btn-danger" type="submit"> Send by Mail </button>
							<button id="btnPrint" class="btn btn-default btn-outline" type="button" onclick="jQuery('#pdfPrint').print();"> <span><i class="fa fa-print"></i> Print</span> </button>
						</div>
						<br/>
					</div>
					<div class="card">
						<div class="card-body "   style="overflow-x: scroll;">
							<form>
								<input type="hidden" id="empCode" value="<?=$employeeData[0]->code_00?>">
								<input type="hidden" id="salDate" value="<?=$salDate?>">
								<input type="hidden" id="fromEmail" value="shubham.mulye@wolfox.in">
								<input type="hidden" id="toEmail" value="<?=$employeeData[0]->email_027?>">
								<input type="hidden" id="SFM" value="<?=$month.' - '.$year?>">
								<input type="hidden" id="empName" value="<?=$employeeData[0]->firstName_01. ' '.$employeeData[0]->middleName_02.' '.$employeeData[0]->lastName_03;?>">
								<input type="hidden" id="deptName" value="<?=$employeeData[0]->departmentName_30?>">
								
								<div id="pdfPrint" >
									<table border="1" class="printableArea">
									
										<tr>
										<td colspan="11"><div align="center"><strong>ROCK TECH  ENGINEERS  H.O.KOLHAPUR</strong></div></td>
										</tr>
										<tr>
										<td colspan="2">Emp. No. : <?=$employeeData[0]->empToken_037?>  &nbsp; &nbsp; Emp. Code. : <?=$employeeData[0]->code_00?></td>
										<td colspan="2">Name : <?php echo $employeeData[0]->firstName_01. ' '.$employeeData[0]->middleName_02.' '.$employeeData[0]->lastName_03;?></td>
										<td colspan="2">Designation : <?=$employeeData[0]->designationName_10?> </td>
										<td colspan="3">Bank A/C No. : <?=$employeeData[0]->empBankAccountNo_048?></td>
										<td width="145">Salary Slip For : </td>
										<td width="136"><?=$month?> - <?=$year?></td>
										</tr>
										<tr>
										<td colspan="2">P.F.No. : <?=$employeeData[0]->empPfAccountNumber_051?></td>
										<td colspan="2">Dept : <?=$employeeData[0]->departmentName_30?></td>
										<td colspan="5">Bank : <?=$employeeData[0]->empBankName_043?>, &nbsp;  Branch :  <?=$employeeData[0]->empBankBranchName_045?></td>
										<td>Gross Salary : </td>
										<td><?=$paymentInfo[0]->grossSalary;?></td>
										</tr>
										<tr>
										<td height="24" colspan="2"><div align="center">WORKING DAY</div></td>
										<td colspan="2"><div align="center">EARNING</div></td>
										<td colspan="2"><div align="center">DEDUCTION</div></td>
										<td width="155"><div align="center">DEDUCTION</div></td>
										<td colspan="2"><div align="center">DEDUCTION</div></td>
										<td colspan="2"><div align="center">SUMMARY</div></td>
										</tr>
										<tr>
										<td colspan="2"><table width="347" height="269">
											<tr>
											  <td width="140">Days</td>
											  <td width="192"><?=$paymentInfo[0]->totalWorkingDays;?></td>
											</tr>
											<tr>
											  <td>CL</td>
											  <td>0.00</td>
											</tr>
											<tr>
											  <td>PL</td>
											  <td>0.00</td>
											</tr>
											<tr>
											  <td>SL</td>
											  <td>0.00</td>
											</tr>
											<tr>
											  <td>LWP</td>
											  <td>0.00</td>
											</tr>
											<tr>
											  <td>Absent</td>
											  <td><?=$paymentInfo[0]->workingMonthDays-$paymentInfo[0]->totalWorkingDays;?></td>
											</tr>
											<tr>
											  <td>H.D.</td>
											  <td><?=$holidaycount;?></td>
											</tr>
											<tr>
											  <td>PH/F.H</td>
											  <td>0.00</td>
											</tr>
											<tr>
											  <td>OD+COOF </td>
											  <td>0.00</td>
											</tr>
										  </table>    </td>
										<td colspan="2"><table width="292" height="269">
										  
										  <tr>
											<td width="165">BASIC</td>
											<td width="115"><?=$paymentInfo[0]->basicSalary;?></td>
										  </tr>
										  <tr>
											<td>HRA/DA</td>
											<td><?=$paymentInfo[0]->houseRentAllowance;?></td>
										  </tr>
										  <tr>
											<td>CONV</td>
											<td><?=$paymentInfo[0]->conveyanceAllowance;?></td>
										  </tr>
										  <tr>
											<td>EDU REMB</td>
											<td><?=$paymentInfo[0]->medicalAllowance;?></td>
										  </tr>
										  <tr>
											<td>MEDICAL</td>
											<td><?=$paymentInfo[0]->eduRembAllowance;?></td>
										  </tr>
										  <tr>
											<td>HSA</td>
											<td><?=$paymentInfo[0]->HazardousSiteAllowance;?></td>
										  </tr>
										  <tr>
											<td>MEAL </td>
											<td><?=$paymentInfo[0]->foodingAllowance;?></td>
										  </tr>
										  <tr>
											<td>Special ALLOW </td>
											<td><?=$paymentInfo[0]->specialAllowance;?></td>
										  </tr>
										  <tr>
											<td height="25">TELE+TRAVEL </td>
											<td><?=$paymentInfo[0]->telephoneTravellingAllowance;?></td>
										  </tr>
										  <tr>
											<td height="25">OTHER ALLOW </td>
											<td><?=$paymentInfo[0]->otherAllowance;?></td>
										  </tr>
										</table></td>
										<td colspan="2"><table width="286" height="269">
										  <tr>
											<td width="160">PF</td>
											<td width="114"><?=$paymentInfo[0]->providentFund;?></td>
										  </tr>
										  <tr>
											<td>P.TAX</td>
											<td><?=$paymentInfo[0]->professionalTax;?></td>
										  </tr>
										  <tr>
											<td>ESI</td>
											<td><?=$paymentInfo[0]->empStateInsurance;?></td>
										  </tr>
										  <tr>
											<td>CANTEEN</td>
											<td><?=$paymentInfo[0]->foodingDeduction;?></td>
										  </tr>
										  <tr>
											<td>ADVN+BANK LOAN</td>
											<td><?=$paymentInfo[0]->advance;?></td>
										  </tr>
										  <tr>
											<td>FINE</td>
											<td><?=$paymentInfo[0]->fineDeduction;?></td>
										  </tr>
										  <tr>
											<td height="25">OTHER DED.</td>
											<td><?=$paymentInfo[0]->otherDeduction;?></td>
										  </tr>
										</table></td>
										<td>&nbsp;</td>
										<td colspan="2">&nbsp;</td>
										<td colspan="2"><table width="283">
										  <tr>
											<td width="149" height="59">TOTAL EARNING </td>
											<td width="122"><?=$paymentInfo[0]->grossSalary?></td>
										  </tr>
										  <tr>
											<td height="57">TOTAL DEDUCTION </td>
											<td><?=$paymentInfo[0]->finalTotalDeduction;?></td>
										  </tr>
										</table></td>
										</tr>
										<tr>
										<td width="143">DAYS : </td>
										<td width="198"><?=$paymentInfo[0]->totalWorkingDays+($paymentInfo[0]->workingMonthDays-$paymentInfo[0]->totalWorkingDays)+$holidaycount;?></td>
										<td width="164">GROSS EARN : </td>
										<td width="123"><?=$paymentInfo[0]->grossSalary;?></td>
										<td width="161">DEDUCTION-1 : </td>
										<td width="121"><?=$paymentInfo[0]->finalTotalDeduction;?></td>
										<td>DEDUCTION-2 : </td>
										<td width="155">GROSS DEDUCTION :  </td>
										<td width="62"><?=$paymentInfo[0]->finalTotalDeduction;?></td>
										<td>NET PAY : </td>
										<td><?=$paymentInfo[0]->finalPayableAmount;?></td>
										</tr>
									</table>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="loadermodal"></div>
		</div>
		 
	</div>
	
	
	<script>
	
		$( document ).ready(function(){
			var container = $("#load-container");
			
			$('#sendEmail').click(function() {
				container.addClass("loading");
				var date=$('#salDate').val();
				var empCode=$('#empCode').val();
				var empName=$('#empName').val();
				var deptName=$('#deptName').val();
				$.ajax({
					url:base_path+"Payments/sendEmail",
					method:"POST",
					data:{
							'empCode':empCode,
							'empName':empName,
							'deptName':deptName,
							'date':date
						},
					datatype:"text",
					success: function(data)
					{
						container.removeClass("loading");
						if(data!='')
						{
						  var obj=JSON.parse(data);
						  if(obj.status)
						  {
							 toastr.success(obj.message, 'Salary Slip Mail', { "progressBar": true });
					   
						  }
						  else
						  {
							  toastr.error(obj.message, 'Salary Slip Mail', { "progressBar": true });
						   
						  }
						}
					}
				});
			}); 
			
			// End Create Pdf
			
		}); // End Ready
		
	</script>
	
	
	
	
	
	
	
	