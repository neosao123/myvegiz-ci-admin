<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Testing extends CI_Model{
 
 function _construct() {
        parent::_construct();
		
    }
	
	
	function getProcessedAttendanceDataByCondition($empCode,$month,$year)
	{
		$query = $this->db->query("SELECT * FROM `attendancepreprocessed` where `empCode`='".$empCode."' AND MONTH(`attendanceDate`) = ".$month." AND YEAR(`attendanceDate`) = ".$year);
		
		if ($this->db->affected_rows() > 0) {
           $res = $query;
        } else {
            $res = false;
        }
		return $res;
	}
	function getProcessedAttendanceDataByConditionWithStatus($empCode,$month,$year)//nitin
	{
		$query = $this->db->query("SELECT * FROM `attendancepreprocessed` where `empCode`='".$empCode."' AND MONTH(`attendanceDate`) = ".$month." AND actionTaken=1 AND (afterActionOTStatus='HD' OR afterActionOTStatus='FD') AND YEAR(`attendanceDate`) = ".$year);
		
		if ($this->db->affected_rows() > 0) {
           $res = $query;
        } else {
            $res = false;
        }
		return $res;
	}
	
	function execute($query)//nitin
	{
		$query = $this->db->query($query);
		return $query;
	}
	
	function getPendingActonData($month,$year,$contractCode)
	{
		$condition="";
		$conditionInternal="";
		if($contractCode!='')
		{
			$condition.=" and am.contractCode='".$contractCode."'";
			$conditionInternal.=" and contractCode='".$contractCode."'";
		}
		$query= $this->db->query("SELECT am.code,am.firstName,am.middleName,am.lastName,am.empToken,am.contractCode,am.designation,dm.designationName,cm.contractName, (select count(actionTaken) from attendancepreprocessed WHERE MONTH(`attendanceDate`)=".$month." AND YEAR(`attendanceDate`)=".$year." and actionTaken=0 ".$conditionInternal." and empCode=ap.empCode) as pendingActionCount, ap.attendanceDate FROM `attendancepreprocessed` ap INNER JOIN employeemaster am on ap.empCode=am.code INNER JOIN designationmaster dm ON am.designation=dm.code INNER JOIN contractmaster cm ON am.contractCode=cm.code where MONTH(`attendanceDate`)=".$month." AND YEAR(`attendanceDate`)=".$year." ".$condition." group by ap.empCode");
		if ($this->db->affected_rows() > 0) {
           $res = $query;
        } else {
            $res = false;
        }
		return $res;
	}
	function getActionDataForEmpStructure($year,$contractCode)//nitin
	{
		$query= $this->db->query("SELECT em.firstName,em.middleName,em.lastName,em.employmentStatus,em.jobType,jm.jobTypeName,em.code as empCode,cm.contractName,(select code from salarystructure  where `contractCode`= '".$contractCode."' AND YEAR(`salaryYear`)=".$year." AND `empCode`=em.code LIMIT 1) as actionCount From employeemaster em INNER JOIN jobtypemaster jm ON em.jobType=jm.code INNER JOIN contractmaster cm ON em.contractCode=cm.code WHERE em.contractCode='".$contractCode."' AND em.isActive='1' ");
		
		if ($this->db->affected_rows() > 1) {
           $res = $query;
        } else {
            $res = false;
        }
		return $res;
	}
	
	function getOtData($month,$year,$contractCode)
	{
			$query= $this->db->query("SELECT ap.code,ap.afterActionOTStatus,ap.machineCode,ap.empCode,em.firstName,em.middleName,em.lastName,em.contractCode,em.designation,cm.contractName,dm.designationName FROM attendancepreprocessed ap INNER JOIN employeemaster em ON ap.empCode=em.code INNER JOIN designationmaster dm ON em.designation=dm.code INNER JOIN contractmaster cm ON em.contractCode=cm.code WHERE MONTH(ap.`attendanceDate`)=".$month." AND YEAR(ap.`attendanceDate`)=".$year." AND em.contractCode='".$contractCode."' AND ap.actionTaken=1 AND (ap.afterActionOTStatus='HD' OR ap.afterActionOTStatus='FD') ");
		if ($this->db->affected_rows() > 0) {
           $res = $query;
        } else {
            $res = false;
        }
		return $res;
	}
	
	// Start
	
	
	function getManualAttendance()
	{
		$query=$this->db->query("SELECT COUNT(attendanceManual.contractCode) AS totalcontractCode,attendanceManual.contractCode,attendanceManual.siteCode,year(attendanceManual.monthYear) AS Year,month(attendanceManual.monthYear) AS Month,contractmaster.contractName FROM (attendanceManual INNER JOIN contractmaster ON attendanceManual.contractCode=contractmaster.code) GROUP BY attendanceManual.contractCode,attendanceManual.monthYear");
		return $query;
	}
	
	/* function getManualAttendanceDetails($contractCode,$Year,$Month)
	{
		$query=$this->db->query("SELECT attendanceManual.empCode,CONCAT( employeemaster.firstName,  ' ', employeemaster.middleName, ' ', employeemaster.lastName) AS empName,attendanceManual.workingDays FROM (attendanceManual INNER JOIN employeemaster ON attendanceManual.empCode=employeemaster.code) WHERE attendanceManual.contractCode='$contractCode' AND year(attendanceManual.monthYear)='$Year' AND month(attendanceManual.monthYear)='$Month'");
		return $query;
	} */
	
	function getManualAttendanceDetails($contractCode,$Year,$Month)
	{
		$query=$this->db->query("SELECT attendanceManual.code, attendanceManual.empCode,CONCAT( employeemaster.firstName,  ' ', employeemaster.middleName, ' ', employeemaster.lastName) AS empName,attendanceManual.workingDays,contractmaster.contractName FROM ((attendanceManual INNER JOIN employeemaster ON attendanceManual.empCode=employeemaster.code) INNER JOIN contractmaster ON attendanceManual.contractCode=contractmaster.code) WHERE attendanceManual.contractCode='$contractCode' AND year(attendanceManual.monthYear)='$Year' AND month(attendanceManual.monthYear)='$Month'");
		return $query;
	}
	
	function getEmployeeLeaveDetails($code)
	{
		$query=$this->db->query("SELECT empleaves.code,empleaves.leaveType,empleaves.leaveDays,empleaves.leaveDates,empleaves.leaveRemark,empleaves.releaseStatus,empleaves.afterActionLeaveDates,empleaves.afterActionDays,empleaves.afterActionDays,empleaves.currentContactAddress,empleaves.empCode,empleaves.releaseStatus,empleaves.contractCode,empleaves.siteCode,empleaves.afterActionRemark,CONCAT( employeemaster.firstName, ' ', employeemaster.middleName, ' ', employeemaster.lastName) AS empName,employeemaster.permanentAddress,employeemaster.empToken,designationmaster.designationName,departmentmaster.departmentName,contractmaster.contractName,sitemaster.siteName FROM (((((empleaves INNER JOIN employeemaster ON empleaves.empCode=employeemaster.code) INNER JOIN designationmaster ON employeemaster.designation=designationmaster.code) INNER JOIN departmentmaster ON employeemaster.deptCode=departmentmaster.code) INNER JOIN contractmaster ON empleaves.contractCode=contractmaster.code) INNER JOIN sitemaster ON empleaves.siteCode=sitemaster.code) WHERE empleaves.code='$code'");
		return $query;
	}
	
	function getHolidayInRange($siteCode,$fromDate,$toDate)
	{
		$query=$this->db->query("SELECT holidaymaster.holidayTitle,holidaymaster.holidayDate FROM (holidaymaster INNER JOIN holidaysitemaster ON holidaymaster.code=holidaysitemaster.holidayCode) WHERE holidaysitemaster.siteCode='$siteCode' AND (holidaymaster.holidayDate BETWEEN  '$fromDate' AND '$toDate') ");
		
		return $query;
	}
	
	
	function totalMail($mailCategory)
	{
		if($mailCategory!="ALL")
		{
			$query=$this->db->get_where('sentmails',['mailStatus'=>1,'mailCategory'=>$mailCategory]);
		}
		else
		{
			$query=$this->db->get_where('sentmails',['mailStatus'=>1]);
		}
		return $query->num_rows();
	}
	
	function getSentMail($limit,$offset)
	{
		$query=$this->db->select('*')
						->limit($limit,$offset)
						->get_where('sentmails',['mailStatus'=>1]);
		return $query;
	}
	
	function getSentMailByField($limit,$offset,$mailCategory)
	{
		$query=$this->db->select('*')
						->limit($limit,$offset)
						->get_where('sentmails',['mailStatus'=>1,'mailCategory'=>$mailCategory]);
		return $query;
	}
	
	function getWeeklyOff($siteCode,$month,$year)
	{
		$query=$this->db->select('holidaymaster.*,holidaysitemaster.*')
						->join('holidaysitemaster','holidaymaster.code=holidaysitemaster.holidayCode')
						->get_where('holidaymaster',['holidaymaster.type'=>'WO','month(holidaymaster.holidayDate)'=>$month,'year(holidaymaster.holidayDate)'=>$year,'holidaysitemaster.siteCode'=>$siteCode]);
		//$query=$this->db->query('SELECT * FROM holidaymaster WHERE month(holidayDate)='.$month.' AND year(holidayDate)='.$year.'');
		return $query;
	}
	
	function getHolidayInRangeWeeklyOff($siteCode,$holidayDate)
	{
		$query=$this->db->query("SELECT holidaymaster.holidayTitle,holidaymaster.holidayDate FROM (holidaymaster INNER JOIN holidaysitemaster ON holidaymaster.code=holidaysitemaster.holidayCode) WHERE holidaysitemaster.siteCode='$siteCode' AND holidaymaster.holidayDate='$holidayDate' AND holidaymaster.type='WO' ");
		
		return $query->num_rows();
	}
	
	function selectUserAccessActiveDataSequence($tblname) {
        $query = $this->db->query("SELECT * FROM `".$tblname."` WHERE `isActive` = '1' ORDER BY sequence ASC");
        return $query;
    }
	
	function selectUserAccessActiveDataByFieldSequence($field, $value, $tblname) {
        $query = $this->db->query("SELECT * FROM `".$tblname."` WHERE `".$field."` = '".$value."' AND `isActive`='1' ORDER BY sequence ASC" );
        
		return $query;
    }
	
	// End
	
	
	function getAttendanceInProcessedTable($month,$year,$contractCode)
	{ 
		date_default_timezone_set('Asia/Kolkata');
		$nowdate=date('Y-m-d H:i:s');
		$date              = $year . '-' . $month . '-00';
		//$query=$this->db->query("SELECT `empCode`,machineCode,count(`afterActionDayStatus`) as fullDayCount FROM `attendancepreprocessed` where `afterActionDayStatus`='FD'  AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' GROUP BY empCode");
		$query=$this->db->query("SELECT `empCode`,machineCode,(SELECT count(`afterActionDayStatus`) FROM `attendancepreprocessed` where `afterActionDayStatus`='FD' AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode=ap.empCode) as fullDayCount ,(SELECT count(`afterActionOTStatus`) FROM `attendancepreprocessed` where `afterActionOTStatus`='FD' AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode=ap.empCode) as fullOTDayCount FROM `attendancepreprocessed` ap where MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' GROUP by empCode");
		if ($this->db->affected_rows() > 0) 
		{
           
			foreach($query->result() as $row)
			{
				$totalDays=$row->fullDayCount;
				$totalOTDays=$row->fullOTDayCount;
				
				//$query2=$this->db->query("SELECT `empCode`,machineCode,count(`afterActionDayStatus`) as halfDayCount FROM `attendancepreprocessed` where `afterActionDayStatus`='HD'  AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode='".$row->empCode."'");
				$query2=$this->db->query("SELECT `empCode`,machineCode,(SELECT count(`afterActionDayStatus`) FROM `attendancepreprocessed` where `afterActionDayStatus`='HD' AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode='".$row->empCode."') as halfDayCount ,(SELECT count(`afterActionOTStatus`) FROM `attendancepreprocessed` where `afterActionOTStatus`='HD' AND MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode='".$row->empCode."') as halfOTDayCount FROM `attendancepreprocessed` ap where MONTH(attendanceDate)=".$month." AND YEAR(attendanceDate)=".$year." AND contractCode='".$contractCode."' AND empCode='".$row->empCode."' GROUP by empCode");
				if ($this->db->affected_rows() > 0) 
				{
					$totalHalfDay=0;
					$totalHalfOTDay=0;
					foreach($query2->result() as $rowhd)
					{
						$totalHalfDay=$rowhd->halfDayCount;
						$totalHalfOTDay=$rowhd->halfOTDayCount;
					}
				
					$totalDays=$totalDays+($totalHalfDay/2);
					$totalOTDays=$totalOTDays+($totalHalfOTDay/2);
				}
				
				
				$data=array(
					'empCode'=>$row->empCode,
					'empToken'=>$row->machineCode,
					'contractCode'=>$contractCode,
					'attendanceMonthYear'=>$date,
					'workedDays'=>$totalDays,
					'otWorkedDays'=>$totalOTDays,
					'addDate'=>$nowdate
				);
				
				$this->db->insert('attendanceprocessed', $data);
				
			}
		   $res = true;
        }
		else {
            $res = false;
        }
		return $res; 
	}
	
	function getAttendanceInManualTable($month,$year,$contractCode)
	{
		
		date_default_timezone_set('Asia/Kolkata');
		$nowdate=date('Y-m-d H:i:s');
		$date              = $year . '-' . $month . '-00';
		$query=$this->db->query("SELECT `empCode`,`empToken`, `contractCode`, `siteCode`, `monthYear`, `workingDays` FROM `attendanceManual` where MONTH(`monthYear`)=".$month." AND YEAR(`monthYear`)=".$year." AND contractCode='".$contractCode."'");
		if ($this->db->affected_rows() > 0) 
		{
			foreach($query->result() as $row)
			{
				$data=array(
					'empCode'=>$row->empCode,
					'empToken'=>$row->empToken,
					'contractCode'=>$contractCode,
					'attendanceMonthYear'=>$date,
					'workedDays'=>$row->workingDays,
					'addDate'=>$nowdate
				);
				$this->db->insert('attendanceprocessed', $data);
			}
			 $res = true;
		}
		else {
            $res = false;
        }
		return $res;
	}
	
	
	function createMonthlySalary($month,$year,$contractCode)
	{
		$insertStatus=false;
		date_default_timezone_set('Asia/Kolkata');
		$nowdate=date('Y-m-d H:i:s');
		$date              = $year . '-' . $month . '-00';
		$query1=$this->db->query("SELECT em.`code`,em.`firstName`,em.`middleName`, em.`lastName`, em.siteCode, ss.`code` as paySlipCode, ss.`empCode`, ss.`contractCode`, ss.`salaryYear`, ss.`basicSalary`, ss.`totalEarning`, ss.`houseRentAllowance`, ss.`conveyanceAllowance`, ss.`medicalAllowance`, ss.`eduRembAllowance`, ss.`telephoneTravellingAllowance`, ss.`otherAllowance`, ss.`HazardousSiteAllowance`, ss.`foodingAllowance`, ss.`specialAllowance`, ss.`providentFund`, ss.`empStateInsurance`, ss.`professionalTax`, ss.`foodingDeduction`, ss.`fineDeduction`, ss.`otherDeduction`, ss.`finalGrossSalary`, ss.`finalNetpayableSalary`, ss.`finalTotalDeduction`, ss.`finalTotalAllowance` FROM `employeemaster` em INNER JOIN `salarystructure` ss ON em.code=ss.empCode where YEAR(ss.`salaryYear`)=".$year." AND ss.contractCode='".$contractCode."'");
		if ($this->db->affected_rows() > 0) 
		{
			foreach($query1->result() as $query1_row)
			{
				$salOfMonth=0.0;
				$holidydayQuery=$this->db->query("SELECT hm.*, `hsm`.`siteCode`, `hsm`.`holidayCode` FROM `holidaysitemaster` hsm INNER JOIN holidaymaster hm ON hm.code=hsm.holidayCode WHERE `siteCode`='".$query1_row->siteCode."' AND MONTH(hm.`holidayDate`) =".$month." AND YEAR(hm.`holidayDate`) =".$year."");
				$holidayCount=sizeof($holidydayQuery->result());
				$daysInMonth=cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$query1_row->code;
				$workingDayCount=$daysInMonth-$holidayCount;
				$query2=$this->db->query("SELECT `id`, `empCode`, `empToken`, `contractCode`, `attendanceMonthYear`, `workedDays`, `totalHrs`, `status`, `addDate` FROM `attendanceprocessed` WHERE `empCode`='".$query1_row->code."' AND MONTH(`attendanceMonthYear`)=".$month." AND YEAR(`attendanceMonthYear`)=".$year." AND `contractCode`= '".$contractCode."'");
				if ($this->db->affected_rows() > 0) 
				{
					foreach($query2->result() as $query2_row)
					{
						$query2_row->workedDays;
						$query1_row->providentFund;
						//list($fullDay, $halfDay) = explode('.',$query2_row->workedDays);
						$fullDay=$query2_row->workedDays;
						$query1_row->basicSalary;
						$b_sal=round(($query1_row->basicSalary/$workingDayCount) * $fullDay, 2);
						
						//allowance
						$hra=round(($query1_row->houseRentAllowance/$workingDayCount) * $fullDay, 2);
						
						$ca=round(($query1_row->conveyanceAllowance/$workingDayCount) * $fullDay, 2);
						
						$ma=round(($query1_row->medicalAllowance/$workingDayCount) * $fullDay, 2);
						
						$edua=round(($query1_row->eduRembAllowance/$workingDayCount) * $fullDay, 2);
						
						$tta=round(($query1_row->telephoneTravellingAllowance/$workingDayCount) * $fullDay, 2);
						
						$otha=round(($query1_row->otherAllowance/$workingDayCount) * $fullDay, 2);
						
						$hsa=round(($query1_row->HazardousSiteAllowance/$workingDayCount) * $fullDay, 2);
						
						$fooda=round(($query1_row->foodingAllowance/$workingDayCount) * $fullDay, 2);
						
						$spea=round(($query1_row->specialAllowance/$workingDayCount) * $fullDay, 2);
				
						//deductions
						$pf_d=round(($query1_row->providentFund/$workingDayCount) * $fullDay, 2);
						
						$esi_d=round(($query1_row->empStateInsurance/$workingDayCount) * $fullDay, 2);
						
						$food_d=round(($query1_row->foodingDeduction/$workingDayCount) * $fullDay, 2);
						
						$fine_d=round(($query1_row->fineDeduction/$workingDayCount) * $fullDay, 2);
						
						$oth_d=round(($query1_row->otherDeduction/$workingDayCount) * $fullDay, 2);
						
						//calculation
						$total_allowance=round($hra+$ca+$ma+$edua+$tta+$otha+$hsa+$fooda+$spea, 2);
						
						$grossSalery=$b_sal+$total_allowance;
						
						//pf calculation
						$pt_d=0.0;
						if($grossSalery<=7500)
						{
							$pt_d=0.0;
						}
						else if($grossSalery>7500 && $grossSalery<=10000)
						{
							$pt_d=175;
							
						}
						else if($grossSalery>10000)
						{
							if($month==2)
							{
								$pt_d=300;
							}
							else
							{
								$pt_d=200;
							}
						}
						
						//total deductions
						$total_deduction=round($pf_d+$esi_d+$food_d+$fine_d+$oth_d+$pt_d, 2);
						
						$emi=0.0;
						$net_payable_salary=($grossSalery-$total_deduction);
						
						$queryAdvance=$this->db->query("SELECT * FROM `advancesalary` where `empCode`='".$query1_row->code."' AND status='1'");
						if ($this->db->affected_rows() > 0) 
						{
							foreach($queryAdvance->result() as $adv_row)
							{
								$nextEmiCount=$adv_row->nextEmiCount;
								$remEmiCount=$adv_row->remainingEmiCount;
								$emiAmt=$adv_row->emi;
								$code=$adv_row->code;
								
								$remainingAmount=$adv_row->remainingAmount; // Pradip
								
								if($adv_row->nextEmiCount==1)
								{
									
									if($net_payable_salary<$adv_row->emi)
									{
										//when emi is greater than salary
										if($remEmiCount>$nextEmiCount)
										{
											$pendingEmiDetails='Date : '.date("Y-m-d"). ', EMI Amount : ' .$emiAmt. ', Reason : EMI is greater than salary';
											
											$nextEmiCount+=1;
											
											$dataAdvanceSalary=[
												'nextEmiCount'      => $nextEmiCount,
												'pendingEmiStatus'  => 1,
												'pendingEmiDetails' => $pendingEmiDetails
											
											];
											
											// Update advancesalary Table 
											
											$this->db->where('code',$code)
													 ->update('advancesalary',$dataAdvanceSalary);
										}
									}
									else
									{	
										$emi=$adv_row->emi;
										$net_payable_salary-=$emi;
										$total_deduction+=$emi;
										
										
										$emidata=array(
											'empCode'=>$query1_row->code,
											'advanceCode'=>$adv_row->code,
											'emiAmount'=>$emi,
											'emiCount'=>$adv_row->nextEmiCount,
											'isActive'=>1,
											'emiDate'=>$date,
											'addDate'=>$nowdate
										);
										
											
										if($this->db->insert('advanceemitransaction', $emidata))
										{
											$up_remainingEmiCount=(((int)$adv_row->remainingEmiCount)-((int)$adv_row->nextEmiCount));
											$up_nextEmiCount=1;
											$up_remainingAmount=($adv_row->remainingAmount-$emi);
											if($up_remainingEmiCount==0)
											{
												$up_status=0;
												$advanceUpdateData=array(
													'remainingAmount'=>$up_remainingAmount,
													
													'payableAmount'=>$up_remainingAmount, // Pradip
													
													'status'=>0,
													'nextEmiCount'=>$up_nextEmiCount,
													'remainingEmiCount'=>$up_remainingEmiCount,
													'nilDate'=>$nowdate,
													'pendingEmiStatus' => 0 
												);
											}
											else
											{
												$advanceUpdateData=array(
													'remainingAmount'=>$up_remainingAmount,
													
													'payableAmount'=>$up_remainingAmount, // Pradip
													
													'nextEmiCount'=>$up_nextEmiCount,
													'remainingEmiCount'=>$up_remainingEmiCount,
													'pendingEmiStatus' => 0
												);
											}
											
											// Update advancesalary Table 
										
											$this->db->where('code',$code)
													 ->update('advancesalary',$advanceUpdateData);
										
										} // Insert
									
									} 
									
								} // End if condition ($adv_row->nextEmiCount==1)
								
								else
								{
									
									//when emi count is greater than 1
									$chkFlag=0;
									$emiAmounts=0;
									$j=0;
									
									for($i=$nextEmiCount;$i>0;$i--)
									{
										$emiAmounts=$adv_row->emi * $i;
										
										if($net_payable_salary<($emiAmounts))
										{
											//when emi is greater than salary
											
											$chkFlag=1;
										}
										else
										{	
											$j=$i;
											$chkFlag=0;
											break;
										}
										
									} // End for Loop
									
									if($chkFlag!=0)
									{
										if($remEmiCount>$nextEmiCount)
										{
											$pendingEmiDetails='Date : '.date("Y-m-d"). ', EMI Amount : ' .$emiAmounts. ', Reason : EMI is greater than salary';
											
											$nextEmiCount+=1;
											
											$dataAdvanceSalary=[
												'nextEmiCount'     => $nextEmiCount,
												'pendingEmiStatus' => 1,
												'pendingEmiDetails' => $pendingEmiDetails
												
											];
											
											// Update advancesalary Table 
											
											$this->db->where('code',$code)
													 ->update('advancesalary',$dataAdvanceSalary);
										}
									}
									else
									{
										$emi=((int)$adv_row->emi) * $j;
										
										$net_payable_salary-=$emi;
										$total_deduction+=$emi;
										
										
										$emidata=array(
											'empCode'=>$query1_row->code,
											'advanceCode'=>$adv_row->code,
											'emiAmount'=>$emi,
											'emiCount'=>$adv_row->nextEmiCount,
											'isActive'=>1,
											'emiDate'=>$date,
											'addDate'=>$nowdate
										);
										
											
										if($this->db->insert('advanceemitransaction', $emidata))
										{
											//$up_remainingEmiCount=(((int)$adv_row->remainingEmiCount)-((int)$adv_row->nextEmiCount));
											
											$up_remainingEmiCount=((int)$adv_row->remainingEmiCount)-($j);
											
											//$up_nextEmiCount=1;
											
											$up_nextEmiCount= $nextEmiCount-$j ;
											
											/* if($up_nextEmiCount==0)
											{
												$up_nextEmiCount=1
												
											} */  // Pradip
											
											$up_remainingAmount=($adv_row->remainingAmount-$emi);
											
											
											if($up_remainingEmiCount==0)
											{
												$up_status=0;
												$advanceUpdateData=array(
													'remainingAmount'=>$up_remainingAmount,
													
													'payableAmount'=>$up_remainingAmount, // Pradip
													
													'status'=>0,
													//'nextEmiCount'=>$up_nextEmiCount,
													'nextEmiCount'=>1,
													'remainingEmiCount'=>$up_remainingEmiCount,
													'nilDate'=>$nowdate
												);
											}
											else
											{
												$advanceUpdateData=array(
													'remainingAmount'=>$up_remainingAmount,
													
													'payableAmount'=>$up_remainingAmount, // Pradip
													
													'nextEmiCount'=>$up_nextEmiCount,
													'remainingEmiCount'=>$up_remainingEmiCount
												);
											}
											
											// Update advancesalary Table 
										
											$this->db->where('code',$code)
													 ->update('advancesalary',$advanceUpdateData);
											
										}
										
										// Insert
									} 
									
								} // End else
								
							} // End foreach Loop
						}
						
						
						$empToken=$query1_row->empToken;
						if($empToken=='')
						{
							$empToken=0;
						}
						$data=array(
							'empCode' => $query1_row->code,
							'empToken' => $empToken,
							'contractCode' => $contractCode,
							'salaryMonthYear' => $date,
							'totalWorkingDays' => $fullDay,
							'workingMonthDays' => $workingDayCount,
							'basicSalary' => $b_sal,
							'houseRentAllowance' => $hra,
							'conveyanceAllowance' => $ca,
							'medicalAllowance' => $ma,
							'eduRembAllowance' => $edua,
							'telephoneTravellingAllowance' => $tta,
							'otherAllowance' => $otha,
							'HazardousSiteAllowance' => $hsa,
							'foodingAllowance' => $fooda,
							'specialAllowance' => $spea,
							'providentFund' => $pf_d,
							'empStateInsurance' => $esi_d,
							'professionalTax' => $pt_d,
							'foodingDeduction' => $food_d,
							'fineDeduction' => $fine_d,
							'otherDeduction' => $oth_d,
							'advance' => $emi,
							'grossSalary' => $grossSalery,
							'totalDeduction' => $total_deduction,
							'netSalary' => $net_payable_salary,
							'totalAllowance' => $total_allowance,
							'finalTotalDeduction' => $total_deduction,
							'finalPayableAmount' => $net_payable_salary,
							'addDate' =>$nowdate
						); 
						
						if($this->db->insert('employeemonthlypayment', $data))
						{
							$insertStatus=true;
						} 
						
					}//end query2 for
				}//end query2 if 
			}//end query1 for
		}//end query1 if 
		
		return $insertStatus;
	}
	
	
	function createOTSalary($month,$year,$contractCode)
	{
		$insertStatus=false;
		date_default_timezone_set('Asia/Kolkata');
		$nowdate=date('Y-m-d H:i:s');
		$date= $year . '-' . $month . '-00';
		
		$query1=$this->db->query("SELECT em.`code`,em.`firstName`,em.`middleName`, em.`lastName`, em.siteCode, ss.`code` as paySlipCode, ss.`empCode`, ss.`contractCode`, ss.`salaryYear`, ss.`basicSalary`, ss.`totalEarning`, ss.`houseRentAllowance`, ss.`conveyanceAllowance`, ss.`medicalAllowance`, ss.`eduRembAllowance`, ss.`telephoneTravellingAllowance`, ss.`otherAllowance`, ss.`HazardousSiteAllowance`, ss.`foodingAllowance`, ss.`specialAllowance`, ss.`providentFund`, ss.`empStateInsurance`, ss.`professionalTax`, ss.`foodingDeduction`, ss.`fineDeduction`, ss.`otherDeduction`, ss.`finalGrossSalary`, ss.`finalNetpayableSalary`, ss.`finalTotalDeduction`, ss.`finalTotalAllowance` FROM `employeemaster` em INNER JOIN `salarystructure` ss ON em.code=ss.empCode where YEAR(ss.`salaryYear`)=".$year." AND ss.contractCode='".$contractCode."'");
		
		if ($this->db->affected_rows() > 0) 
		{
			foreach($query1->result() as $query1_row)
			{
				$holidydayQuery=$this->db->query("SELECT hm.*, `hsm`.`siteCode`, `hsm`.`holidayCode` FROM `holidaysitemaster` hsm INNER JOIN holidaymaster hm ON hm.code=hsm.holidayCode WHERE `siteCode`='".$query1_row->siteCode."' AND MONTH(hm.`holidayDate`) =".$month." AND YEAR(hm.`holidayDate`) =".$year."");
				$holidayCount=sizeof($holidydayQuery->result());
				$daysInMonth=cal_days_in_month(CAL_GREGORIAN, $month, $year);
				
				$workingDayCount=$daysInMonth-$holidayCount;
				
				$query2=$this->db->query("SELECT `id`, `empCode`, `empToken`, `contractCode`, `attendanceMonthYear`, `otWorkedDays`, `totalHrs`, `status`, `addDate` FROM `attendanceprocessed` WHERE `empCode`='".$query1_row->code."' AND MONTH(`attendanceMonthYear`)=".$month." AND YEAR(`attendanceMonthYear`)=".$year." AND `contractCode`= '".$contractCode."' AND otWorkedDays!=0.0");
				
				if ($this->db->affected_rows() > 0) 
				{
					foreach($query2->result() as $query2_row)
					{
						
						$fullDay=$query2_row->otWorkedDays;
						
						$b_sal=round(($query1_row->basicSalary/$workingDayCount) * $fullDay, 2);
						
						$empToken=$query2_row->empToken;
						
						if($empToken=='')
						{
							$empToken=0;
						}
						
						$data=array(
							'empCode' => $query1_row->code,
							'empToken' => $empToken,
							'contractCode' => $contractCode,
							'salaryMonthYear' => $date,
							'totalWorkingDays' => $fullDay,
							'basicSalary' => $b_sal,
							'addDate' =>$nowdate
						); 
						
						
						
						if($this->db->insert('employeemonthlyotpayment', $data))
						{
							$insertStatus=true;
						} 
						
					} // End query2 foreach Loop
					
				} // End query2 if Condition
				
			} // End query1 foreach Loop
			
		} // End query1 if Condition
		
		return $insertStatus;
		
	}
	
	function ph() {//advanceemitransaction employeemonthlypayment employeemonthlyotpayment
        $query=$this->db->query("truncate employeemonthlypayment");
    }
	
		
}

?>