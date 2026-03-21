<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key'.SESS_KEY);
		if(!isset($this->session->userdata['logged_in' . $this->session_key]['code'])){
			redirect('Admin/login','refresh');
		}
    }
	public function getAllcode()
	{
		$itemCode = $this->input->get('itemCode');
		$dataitemCode = $this->GlobalModel->similarResultFind('itemmaster','code',$itemCode);
		$itemCode = '';
		foreach($dataitemCode->result() as $em)
		{   

			$itemCode.='<option value="' . $em->code . '">';
		}
		echo $itemCode;
	} 
	public function getAllname()
	{
		$itemName = $this->input->get('itemName');
		$dataitemName = $this->GlobalModel->similarResultFind('itemmaster','name',$itemName);
		
	   // $itemName = '';
		foreach( $dataitemName->result() as $em)
		{   

			$itemName.='<option value="' . $em->name . '">';
		}
		echo $itemName;
	} 
	public function getAllInformationByCode()
	{
		$itemCode = $this->input->get('itemCode');
		$dataQuery  = $this->GlobalModel->getAllDataFromField('itemmaster','code',$itemCode);
		
			$itemName = '';
			$itemUom = '';
			$storageName = '';
			$storageSection = '';
			$itemPrice = '';
			$tax = '';

		foreach($dataQuery->result() as $row)
		{   
			$itemName = $row->name;	
			$itemUom = $row->uom;
			$storageName = $row->storageName;
			$storageSection = $row->storageSection;
			$itemPrice = $row->itemPrice;
			$tax = $row->gstPer;
		}

		//TO Fetch Vendor Data from another table
		//table array
		$tables=array('itemvendormaster','entities');
		
		//required column in each table
		$requiredColumns=array
		(
			array('id', 'code', 'vendorCode', 'isActive', 'isDelete', 'addID', 'addIP', 'addDate', 'editIP', 'editID', 'editDate', 'deleteID', 'deleteIP', 'deleteDate'),
			array('entityName')
		);
		
		//join condition 
		$conditions=array(
			array('vendorCode','code')
		);
		
		//filter table column names
		$extraConditionColumnNames=array(
			array("code")
		);
		
		//filter values 
		$extraConditions=array(
			array($itemCode)
		);
		
        $vendorRecords = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions);
		//print_r($contractRecords->result());
		$vendorData = '<option value="" selected>Select Option</option>';
		foreach($vendorRecords->result() as $ven)
		{   
			$vendorData .= "<option value='".$ven->vendorCode_02."'>".$ven->entityName_10."</option>";
		}
		
		$vendor = ["vendorData"=>$vendorData];
		$data= ["name"=>$itemName, "uom"=>$itemUom,"storageName"=>$storageName,"storageSection"=>$storageSection,"itemPrice"=>$itemPrice,"tax"=>$tax, $vendor];

		echo json_encode($data);
	
	}
	public function getAllInformationByName()
	{

	$itemName = $this->input->get('itemName');
		
	  $dataQuery  = $this->GlobalModel->getAllDataFromField('itemmaster','name',$itemName);
		
			$itemCode = '';
			$itemUom = '';
			$storageName = '';
			$storageSection = '';
			$itemPrice = '';
			$tax = '';
			
		foreach($dataQuery->result() as $row)
		{    
			$itemCode = $row->code;
			$itemUom = $row->uom;
			$storageName = $row->storageName;
			$storageSection = $row->storageSection;
			$itemPrice = $row->itemPrice;
			$tax = $row->gstPer;
		}
		//TO Fetch Vendor Data from another table
		
		$vendorRecords  = $this->GlobalModel->selectCombineResult('itemmaster','code','name','itemvendormaster','itemCode','vendor','vendorCode','entities','code','entity','entityName',$itemName);

		$vendorData = '<option value="" selected>Select Option</option>';
		foreach($vendorRecords->result() as $ven)
		{   
			$vendorData .= "<option value='".$ven->vendorCode."'>".$ven->entityName."</option>";
		}
		
		$vendor = ["vendorData"=>$vendorData];

		$data= ["code" =>$itemCode, "uom"=> $itemUom, "storageName"=>$storageName,"storageSection"=> $storageSection,"itemPrice"=>$itemPrice,"tax"=>$tax,$vendor];

		echo json_encode($data);
	
	
	}
	
	
	//################FROM ASSET GET DATA OF PARENTS ######################//
	/*
	1.Table name, where we have data for search.
	2.Common field name of both, mentioned in first table 
	3.Value from which we want result.
	4.Resultant Table name.
	*/
	public function getDataFromAsset() {
        $asset = $this->input->get('assetCode');
		//echo $asset;
        $locationData  = $this->GlobalModel->dependantResult('assetmaster','assetLocation',$asset,'locationmaster');
        //print_r($locationData);
        $workLocationCode = '';
		$workLocationName = '';
        foreach($locationData->result() as $location){   

           $workLocationCode = $location->code;
			$workLocationName = $location->locationName;
        }
		
		$siteCode = '';
		$siteName = '';
		$siteData  = $this->GlobalModel->dependantResult('locationmaster','siteCode',$workLocationCode,'sitemaster');
		 //print_r($siteData);
	     foreach($siteData->result() as $site){   

          	$siteCode = $site->code;
			$siteName = $site->siteName;
        }
		
        $mineCode = '';
		$mineName = '';
		$mineData  = $this->GlobalModel->dependantResult('sitemaster','contractCode',$siteCode,'contractmaster');
		foreach($mineData->result() as $mine){   

          	$contractCode = $mine->code;
			$mineName = $mine->mineName;
        }
		
       // $data= ["country"=> $country,"state"=>$state, "district"=>$district,"taluka"=>$taluka,"place" =>$place];
          $data= ["workLocationCode"=> $workLocationCode,"workLocationName"=>$workLocationName, "siteCode"=>$siteCode,"siteName"=>$siteName,"contractCode" =>$contractCode,"mineName"=>$mineName];
 		echo json_encode($data);
    }
	
	
	 public function getStorageSectionByStorageCode()
	 {
		 $storageCode = $this->input->get('storageCode');
		 
		  $storageSectionCode="";
		  
                    $selectStorageSectionCode  = $this->GlobalModel->selectDataByField('storageCode',$storageCode,'storagesectionmaster');
					 
					foreach($selectStorageSectionCode ->result() as $str)
						{
							
					       $storageSectionCode .= "<option value='".$str->code."'>".$str->storageSectionName."</option>";
							
						}
			
		
		
		 $data=array($storageSectionCode );
		 echo json_encode($data);
		 
	 }
	  public function getRemainingStock(){
		  $storageSectionCode=$this->input->get('storageSectionCode');
		  $itemCode=$this->input->get('itemCode');
		 
		   $RemainingStock= $this->GlobalModel->getAvailableStock($itemCode,$storageSectionCode);
			foreach($RemainingStock->result() as $row)
			{
				$stock=$row->stock;
				//echo $stock;
			}
		  
		  $data=array($stock);
		 
		  echo json_encode($data);
	 }
	 
	 public function getStorageNameBySiteCode()
	 {
		 $siteCode = $this->input->get('siteCode');
		   $storageCode.="";
		 
		 $storageCode.=".<option value=''>Select Option</option>.";
					$selectStorageCode  = $this->GlobalModel->selectDataByField('siteCode',$siteCode,'storagemaster');
					foreach($selectStorageCode ->result() as $str)
					// $storageCode.="<option value="">Select Option</option>";
						{
						  
					       $storageCode .= "<option value='".$str->code."'>".$str->storageName."</option>";
							
						}
		$data=["storageCode"=>$storageCode];
		echo json_encode($data);
		 
	 }
	  public function getStorageCodeBySiteCode()
	 {
		   $siteCode = $this->input->get('siteCode');
		   $storageCode.="";
		 
		 $storageCode.=".<option value=''>Select Option</option>.";
					$selectStorageCode  = $this->GlobalModel->selectDataByField('siteCode',$siteCode,'storagemaster');
					foreach($selectStorageCode ->result() as $str)
					// $storageCode.="<option value="">Select Option</option>";
						{
						 $storageCode .= "<option value='".$str->code."'>".$str->storageName."</option>";
						}
				$data=["storageCode"=>$storageCode];
				echo json_encode($data);
	 }
	  public function getSiteCodeBycontractCode()
	 {
		 $contractCode = $this->input->get('contractCode');
		 // echo $contractCode;
		   $siteCode.="";
		 
		 $siteCode.=".<option value=''>Select Option</option>.";
					$selectSiteCode  = $this->GlobalModel->selectDataByField('contractCode',$contractCode,'sitemaster');
					foreach($selectSiteCode ->result() as $str)
					// $storageCode.="<option value="">Select Option</option>";
						{
						  
					       $siteCode .= "<option value='".$str->code."'>".$str->siteName."</option>";
							
						}
		$data=["siteCode"=>$siteCode];
		echo json_encode($data);
		 
	 }
	 public function getRemainingStockByBatchCode(){
		   $itemCode=$this->input->get('itemCode');
		   $storageSectionCode=$this->input->get('storageSectionCode');
		   $batchCode=$this->input->get('batchCode');
      
	  $tables=array('stocklinesinfo');
		
		$requiredColumns=array
		(
			array('itemCode','storageSection', 'itemQuantity','activityCode')
			
		);
		
		$conditions=array(
			
		);
		
		
			
		$extraConditionColumnNames=array(
		array("activityCode","itemCode","storageSection"),
		
		);
		
		$extraConditions=array(
		array($batchCode,$itemCode,$storageSectionCode),
		
		);
		//filterbyDate
		$extraDateConditionColumnNames=array(
			//array("consumeDate")
		);

		$extraDateConditions=array(
			//array($consumDateStart.'~'.$consumDateEnd)
		);
		
         $Records = $this->GlobalModel1->make_datatables($tables,$requiredColumns,$conditions,$extraConditionColumnNames,$extraConditions,$extraDateConditionColumnNames,$extraDateConditions);
		
		 echo $stock=$Records->result()[0]->itemQuantity_02;
		 
		
	 }
}
?>