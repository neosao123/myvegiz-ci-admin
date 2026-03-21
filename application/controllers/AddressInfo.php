<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AddressInfo extends CI_Controller {
  	public function __construct()
    {
        parent::__construct();
        $this->load->model('GlobalModel');
    }	

	public function getAddressFromPlace() {
        $place = $this->input->get('place');
        $this->load->model('GlobalModel');
        $dataQuery  = $this->GlobalModel->getAllDataFromField('addressmaster','place',$place);
        
        $country = '';
        $state = '';
        $district = '';
        $taluka = '';
        $pinCode = '';

        foreach($dataQuery->result() as $row){   

          	$country = $row->country;
	        $state = $row->state;
	        $district = $row->district;
	        $taluka = $row->taluka;
            $pinCode = $row->pincode;
        }

        $data= ["country"=> $country,"state"=>$state, "district"=>$district,"taluka"=>$taluka,"pinCode"=>$pinCode];

 		echo json_encode($data);
    }

    public function getAllData()
    {
        $place = $this->input->get('place');
        $dataPlace = $this->GlobalModel->similarResultFind('addressmaster','place',$place);

        $place = '';
        foreach($dataPlace->result() as $pin){   

            $place .='<option value="' . $pin->place . '">';
        }
        echo $place;
    }
	
	 public function getAllDataTaluka()
    {
        $Taluka= $this->input->get('taluka');
        $dataTaluka= $this->GlobalModel->similarResultFind('addressmaster','taluka',$Taluka);

        $Taluka = '';
        foreach($dataTaluka->result() as $tal){   

            $Taluka .='<option value="' . $tal->taluka . '">';
        }
        echo $Taluka;
    }
	
	 public function getAllDataDistrict()
    {
        $District= $this->input->get('district');
        $dataDistrict= $this->GlobalModel->similarResultFind('addressmaster','district',$District);

        $District = '';
        foreach($dataDistrict->result() as $dist){   

            $District .='<option value="' . $dist->district . '">';
        }
        echo $District;
    }
	
	 public function getAllDataState()
    {
        $State= $this->input->get('state');
        $dataState= $this->GlobalModel->similarResultFind('addressmaster','state',$State);

        $State = '';
        foreach($dataState->result() as $sta){   

            $State .='<option value="' . $sta->state . '">';
        }
        echo $State;
    }
	 public function getempCode()
    {
        $empcode= $this->input->get('empcode');
        $datacode= $this->GlobalModel->similarResultFind('employeemaster','code',$empcode);

        $code = '';
        foreach($datacode->result() as $sta){   

            $code .='<option value="' . $sta->code . '">';
        }
        echo $code;
    }
	
	public function getemployeeFromCode()
	{
        $empCode = $this->input->get('empCode');
		
        $this->load->model('GlobalModel');
        $dataQuery  = $this->GlobalModel->selectDataByFieldWithEmpty('code',$empCode,'employeemaster');
        
        $empToken= '';
        $firstName = '';
        $middleName = '';
        $lastName = '';
		
       

        foreach($dataQuery->result() as $row){   

          	$empToken = $row->empToken;
	        $firstName = $row->firstName;
	        $middleName = $row->middleName;
	        $lastName = $row->lastName;
			
          
        }
		$name= $firstName." ".$middleName." ".$lastName;

       // $data= ["empToken"=> $empToken,"firstName"=>$firstName, "middleName"=>$middleName,"lastName"=>$lastName];
     $data=array($empToken,$name);
 		echo json_encode($data);
    }
	
	public function getAssetFromCode()
	{
        $assetCode = $this->input->get('assetCode');
		
        $this->load->model('GlobalModel');
        $dataQuery  = $this->GlobalModel->selectDataByFieldWithEmpty('code',$assetCode,'assetmaster');
        
        $servicePeriod= '';
        $serviceEnd= '';
       
		
       

        foreach($dataQuery->result() as $row){   

          	$servicePeriod = $row->servicePeriod;
	        $serviceEnd = $row->serviceEnd;
	     
          
        }

       // $data= ["empToken"=> $empToken,"firstName"=>$firstName, "middleName"=>$middleName,"lastName"=>$lastName];
     $data=array($servicePeriod,$serviceEnd);
 		echo json_encode($data);
    }
	
	
	
}
?>