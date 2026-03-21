<?php
 
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
 
class Api extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('book_model');		
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
    }
	//City list
	public function cityList_get(){
		$result = $this->GlobalModel->selectActiveData('citymaster')->result_array();  
		if($result){
			$data['cities']=$result; 
			return $this->response(array("status" => "200","result"=>$data), 200);
		}
		else{
			return $this->response(array("status" => "400", "message" => "Data not found."), 200);
		}
	}
	
	// Start registration
	public function registration_post()
	{
		$postData = $this->post();		
		if ($postData["name"] != '' && $postData["mobile"] != '' && $postData["password"] !='' && $postData["cityCode"]!="")
		{
			$email=filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL);
			$com_code = md5(uniqid(rand()));
			$cart_code= md5(uniqid(rand()));
			$forgot = md5(uniqid(rand()));
			if($postData["emailId"] != ''){
				$checkEmailData=array("emailId"=>$email,'isDelete!='=>1);
				
				if(!$this->GlobalModel->checkExistAndInsertRecords($checkEmailData,'clientmaster'))
				{
					return $this->response(array("status" => "406","message"=>"E-Mail already exist.Please Signin"), 200);
					exit();
				}
			}
			
			$checkMobileData=array("mobile"=>$postData["mobile"],'isDelete!='=>1);
			
			if(!$this->GlobalModel->checkExistAndInsertRecords($checkMobileData,'clientmaster'))
			{
				return $this->response(array("status" => "406","message"=>"Mobile Number already exist.Please Signin"), 200);
				exit();
			}
			
			$insertArr = array(
				"name"=> $postData["name"], 
				"emailId" => $email,
				"mobile" => $postData["mobile"],
				"password"=>  md5($postData["password"]),
				'cityCode'=> $postData['cityCode'],
				"comCode" => $com_code,
				"cartCode" => $cart_code,
				"forgot" => $forgot,
				"isActive" => 1
			  );
			  
			$insertResult=$this->GlobalModel->addNew($insertArr, 'clientmaster', 'CLNT');
			
			if($insertResult != 'false')
			{
				$data = array(
					"clientCode"=> $insertResult,
					"isActive" => 1
				);
				
				$profileResult=$this->GlobalModel->addWithoutCode($data, 'clientprofile');
				if($profileResult != 'false')
				{
					$condition = array(
						"clientmaster.code" => $insertResult
					);
					
					$resultData = $this->ApiModel->read_user_information($condition);
					$result['userData']=$resultData[0];
					return $this->response(array("status" => "200","result"=>$result,"message"=>"Registration Successfully.."), 200);
				}
				else
				{
					$this->response(array("status" => "400", "message" => "Registration Successfully.. Please Signin.."), 400);
				}
			}
			else
			{
				$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 400);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}// End registration
	
	//login
	public function loginProcess_post()
	{
		$postData = $this->post();
		
		if($postData["type"] != '' && $postData["userId"] != '' && $postData["userPassword"] !='') 
		{
			
			if($postData["type"] == 'mobile')
			{
				$loginData = array(
					"mobile" => $postData["userId"],
					"isActive" => 1,
					"password"=> md5($postData["userPassword"])
				);
				if($this->ApiModel->login($loginData))
				{
					$data = array(
						"clientmaster.mobile" => $postData["userId"]
					);
					$resultData = $this->ApiModel->read_user_information($data);
					$result['userData']=$resultData[0];
					return $this->response(array("status" => "200","message"=>"Login Successfully...","result"=>$result), 200);
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "incorrect Mobile or Password"), 200);
				}
			}
			else
			{
				$email=filter_var($postData["userId"], FILTER_SANITIZE_EMAIL);
				$clientData = $this->GlobalModel->selectDataByField('emailId',$email,'clientmaster')->result_array();  
				
				if(sizeof($clientData) > 0)
				{ 
					$loginData = array(
						"emailId" => $email,
						"isActive" => 1,
						"password"=> md5($postData["userPassword"])
					 );
					if($this->ApiModel->login($loginData))
					{
						$data = array(
							"clientmaster.emailId" => $email
						);
						$resultData = $this->ApiModel->read_user_information($data);
						$result['userData']=$resultData[0];
						return $this->response(array("status" => "200","message"=>"Login Successfully...", "result"=>$result), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "incorrect Email or Password"), 200);
					}
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
				}
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}//end login  Process
	 
	//get Custom added address and area list
	public function getCustomAddressList_post()
	{
		$postData = $this->post();
		if($postData['cityCode']!=""){
			$conditionColumns = array('isActive','cityCode');
			$conditionValues = array(1,$postData['cityCode']);
			$res = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns,$conditionValues,'customaddressmaster');
			if($res)
			{
				$addressList=[];
				foreach($res->result() as $row)
				{
					$data = array(
							'addressCode' => $row->code,
							'place' => $row->place,
							'district' => $row->district,
							'taluka' => $row->taluka,
							'pincode' => $row->pincode,
							'state' => $row->state,
						);
						
						array_push($addressList,$data);
				}
				$result['addressList'] = $addressList;
				return $this->response(array("status" => "200", "message" => " Address List where Services Available","result" => $result), 200);
			}	
			else
			{
				$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
			}
		} else {
			return $this->response(array("status" => "200", "message" => " Address List where Services Available","result" => $result), 200);
		}
	}	
	
	//get slider images
	public function sliderImages_post()
	{
		$postData = $this->post();
		if($postData['cityCode']!=""){
			$columns = array("productslider.*");
			$cond=array('productslider' . ".isActive" => 1,'productslider.productCode'=>$postData['cityCode']);
			$orderBy = array('productslider' . ".id" => 'ASC');
			$images_result = $this->GlobalModel->selectQuery($columns,'productslider',$cond,$orderBy);
			if($images_result){
				$imageArray=array();		
				$images_result = $images_result->result_array();
				for($img=0;$img<sizeof($images_result);$img++)
				{
					if(file_exists('uploads/slider/'.$images_result[$img]['imagePath'])){
						$imgData['imagePath']=sliderBasePath.'uploads/slider/'.$images_result[$img]['imagePath'];
						$imgData['type'] = $images_result[$img]['type'];
						array_push($imageArray,$imgData);
					}
				}			
				$data['sliderImages']=$imageArray;			
				return $this->response(array("status" => "200","message"=>'Data Found',"result"=>$data), 200);
			}
			else{
				$data['sliderImages'] = array();
				return $this->response(array("status" => "200", "message" => "Data not found.",'result'=>$data), 200);
			}
		} else { 
			$data['sliderImages'] = array();
			return $this->response(array("status" => "200", "message" => "Data not found.",'result'=>$data), 200);
		}
	} 
	//end slider images
	
	// Get Category list
	public function categoryList_post()
	{
		$postData = $this->post();
		if ($postData["userId"] != '' && $postData["offset"] !='')
		{
			$category_offset=$postData["offset"];
			$category_limit=10;
			$condition=array('isActive'=>1);
			$totalRecords=sizeof($this->ApiModel->selectData('categorymaster','','',$condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster',$category_limit,$category_offset,$condition)->result_array();  
			if($category_result){
				for($i=0;$i<sizeof($category_result);$i++){
					$category_result[$i]['categoryImage']=base_url().'uploads/category/'.$category_result[$i]['code'].'/'.$category_result[$i]['categoryImage'];
				}
				$data['categories']=$category_result;
				return $this->response(array("status" => "200","totalRecords" => $totalRecords,"result"=>$data), 200);
			}
			else{
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends Get Category list
	
	//category list by limit
	public function categoryAndProduct_post()
	{
		$postData = $this->post();
		
		if ($postData["userId"] != '' && $postData["offset"] !='' && $postData['cityCode']!="")
		{
			$category_offset=$postData["offset"];
			$cityCode = $postData['cityCode'];
			$category_limit="";
			$condition=array('isActive'=>1);
			$totalRecords=sizeof($this->ApiModel->selectData('categorymaster','','',$condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster',$category_limit,$category_offset,$condition)->result_array();  
			if($category_result){
				$category_resulta = [];
				for($i=0;$i<sizeof($category_result);$i++)
				{
					
					$category_resulta[$i]['id']= $category_result[$i]['id'];
					$category_resulta[$i]['code']= $category_result[$i]['code'];
					$category_resulta[$i]['categoryName']= $category_result[$i]['categoryName'];
					$category_resulta[$i]['categorySName']= $category_result[$i]['categorySName'];					
					$category_resulta[$i]['isActive']= $category_result[$i]['isActive'];
					$category_resulta[$i]['categoryImage']=base_url().'uploads/category/'.$category_result[$i]['code'].'/'.$category_result[$i]['categoryImage'];
					
					$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
					$cond=array('productmaster' . ".productCategory" => $category_result[$i]['categorySName'],'productmaster' . ".isActive" =>1,'productratelineentries.cityCode'=>$cityCode);
					$orderBy = array('productmaster' . ".id" => 'ASC');
					$join = array('productratelineentries'=>'productmaster.code=productratelineentries.productCode');
					$joinType=array('productratelineentries'=>'inner');
					$like=array();
					$p_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,$like,$category_limit,$category_offset);
					if($p_result){
						$product_result=$p_result->result_array();
						for($j=0;$j<sizeof($product_result);$j++){
							$condition2=array('productCode'=>$product_result[$j]['code']);
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							$imageArray=array();
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$product_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
							}
							if($product_result[$j]['productUom']=='PC' && $product_result[$j]['minimumSellingQuantity']>1)
							{
								$product_result[$j]['productUom']='PCS';
							}
							$product_result[$j]['images']=$imageArray;
							unset($imageArray);
						}
						$proData['products']=$product_result;
						$category_resulta[$i]['productList']=$proData;
					}
				}
				$data['categories']=$category_resulta;
				return $this->response(array("status" => "200","totalRecords" => $totalRecords,"result"=>$data), 200);
			} else {
				$data['categories']= array();
				return $this->response(array("status" => "400", "message" => "Data not found.","result"=>$data), 400);
			}
		}
		else
		{
			$data['categories']= array();
			$this->response(array("status" => "200", "message" => "Required field(s).","result"=>$data), 200);
		}
	}
	//end category list 
			
	// Get Product list by categorySName 
	public function productByCategory_post() 
	{
		$postData = $this->post();
		
		if ($postData["categorySName"] != '' && $postData["offset"] !='' && $postData['cityCode']!="")
		{
			$categorySName = $postData["categorySName"];
			$product_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$product_limit = 10;
			 
			$condition2=array('productCategory'=>$categorySName,'isActive'=>1);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster','','',$condition2)->result());
			
			if($totalProduct){
				
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
				$cond=array('productmaster' . ".productCategory" => $categorySName,'productmaster' .'.isActive'=>1,'productratelineentries.cityCode'=>$cityCode);
				$orderBy = array('productmaster' . ".id" => 'ASC');
				$join = array('productratelineentries'=>'productmaster.code=productratelineentries.productCode');
				$joinType=array('productratelineentries'=>'inner');
			
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,array(),$product_limit,$product_offset);
			 
				if($resultQuery)
				{
					$limitProduct_result=$resultQuery->result_array();
					for($j=0;$j<sizeof($limitProduct_result);$j++){
					
							$condition2=array('productCode'=>$limitProduct_result[$j]['code']);
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							
							$imageArray=array();
							
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$limitProduct_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
							}
							if($limitProduct_result[$j]['productUom']=='PC' && $limitProduct_result[$j]['minimumSellingQuantity']>1)
							{
								$limitProduct_result[$j]['productUom']='PCS';
							}
							$limitProduct_result[$j]['images']=$imageArray;
							unset($imageArray); 
					}
						  
					$data['products']=$limitProduct_result;
					 
					return $this->response(array("status" => "200","totalRecords" => $totalProduct,"result"=>$data), 200);
				}
				else{
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
		 	}
			else{
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		}else{
				$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		} 
	
	}  //Ends Product list by categorySName
	
	// Get Product by productCode
	public function productById_post()
	{
		$postData = $this->post();
		
		if ($postData["productCode"]!= '' && $postData['cityCode']!="")
		{
			$productCode = $postData["productCode"];
			$cityCode = $postData['cityCode'];
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
			$cond=array('productmaster' . ".code" => $productCode,'productratelineentries.cityCode'=>$cityCode);
			$orderBy = array();
			$join = array('productratelineentries'=>'productmaster.code=productratelineentries.productCode');
			$joinType=array('productratelineentries'=>'inner');
			
			$product_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType);
			if($product_result) {
				$product_result = $product_result->result_array();
				for($j=0;$j<sizeof($product_result);$j++){
					
					$condition2=array('productCode'=>$product_result[$j]['code']); 
					$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
					
					$imageArray=array();
					
					for($img=0;$img<sizeof($images_result);$img++)
					{
						array_push($imageArray, base_url().'uploads/product/'.$product_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
					}
					if($product_result[$j]['productUom']=='PC' && $product_result[$j]['minimumSellingQuantity']>1)
					{ 
						$product_result[$j]['productUom']='PCS';
					}
					
					$product_result[$j]['images']=$imageArray;
					unset($imageArray); 
				}
				$data['products']=$product_result[0];
				return $this->response(array("status" => "200","result"=>$data), 200);
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "Data not found."), 400);
			}
		}else{ 
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	
	} // Ends Get Product by productCode 
	
	// check wishlist 
	public function checkWishList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["productCode"] !='')
		{
			$clientCode=$postData["clientCode"];
			$client_result = $this->GlobalModel->selectDataById($clientCode,'clientmaster')->result_array();  
			if($client_result)
			{
				$clientCode=$client_result[0]['code'];
				  
				$condition2=array("clientCode"=>$postData["clientCode"],"productCode"=>$postData["productCode"],"isActive"=>1);
				$wishlist_result = $this->ApiModel->selectData('clientwishlist','','',$condition2)->result_array();
				if(sizeof($wishlist_result)>0)
				{
					return $this->response(array("status" => "200","message" =>'item already exist in wishlist'), 200);
				}
				else
				{
					return $this->response(array("status" => "400","message"=>'iteam not added to wishlist yet'), 200);
				}
				 
			}
			else
			{
				return $this->response(array("status" => "400","message"=>'user not registerd'), 400);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends check WishList
	
	// Add to Wish list by productCode and clientCode
	public function addToWishlist_post()
	{
		$postData = $this->post();
		
		if ($postData["productCode"] && $postData["clientCode"] !='' )
		{
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			
			$clientData = $this->GlobalModel->selectDataById($clientCode,'clientmaster')->result_array();  
			
			if(sizeof($clientData) > 0)
			{
				
				$condition2=array('productCode'=>$productCode,'clientCode'=>$clientCode,'isActive'=>1);
				$clientWishList = $this->ApiModel->selectData('clientwishlist','','',$condition2)->result_array();
			  
				if(sizeof($clientWishList) > 0)
				{
					$code =$clientWishList[0]['code'];   
					$this->GlobalModel->deleteForever($code,'clientwishlist');
					
					return $this->response(array("status" => "200", "message" => "Product removed from your wishlist successfully."), 200);
				}
				else
				{
					$data = [
						'productCode'=>$productCode,
						'clientCode'=>$clientCode,
						'isActive'=>1
					];
					
					$code = $this->GlobalModel->addNew($data, 'clientwishlist','WISH');
					
					if($code != 'false')
					{
						return $this->response(array("status" => "200", "message" => "Product added to your wishlist successfully."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Product not added. Please try again later."), 400);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user before adding product to your wishlist."), 400);
			}
		}
		else
		{
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	
	} // Ends Add to Wish list by productId and userId
	
	// get WishList by ClientCode
	public function getWishList_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '' && $postData['cityCode']!="")
		{ 
			$clientCode = $postData["clientCode"];
			$cityCode = $postData['cityCode'];
			$tableName="clientwishlist";
			$orderColumns = array("productmaster.*, clientwishlist.productCode, productratelineentries.productStatus, productratelineentries.sellingPrice as productSellingPrice" );
			$cond=array('clientwishlist' . ".clientCode" => $clientCode,'productratelineentries.cityCode'=>$cityCode);
			$orderBy = array('clientwishlist' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientwishlist' . '.productCode=' . 'productmaster' .'.code', 'productratelineentries' => 'productmaster' . '.code=' . 'productratelineentries' . '.productCode');
			$joinType=array('productmaster' =>'inner','productratelineentries' =>'inner');
		
			 
			$clientWishList = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType);
			if($clientWishList)   { 
			    $clientWishList = $clientWishList->result_array();
				for($j=0;$j<sizeof($clientWishList);$j++)
				{ 
					$productCode = $clientWishList[$j]['productCode'];
					$condition2=array('productCode'=>$productCode); 
					$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
					
					$imageArray=array();
					
					for($img=0;$img<sizeof($images_result);$img++)
					{
						array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
					}
					if($clientWishList[$j]['productUom']=='PC' && $clientWishList[$j]['minimumSellingQuantity']>1)
					{ 
						$clientWishList[$j]['productUom']='PCS';
					}
					
					$clientWishList[$j]['images']=$imageArray;
					unset($imageArray);
				}
				
				$data['wishlist'] = $clientWishList;
			 
				return $this->response(array("status" => "200","totalresult"=>sizeof($clientWishList),"result"=>$data), 200);
			} 
			else 
			{
			    return $this->response(array("status" => "300","message"=>"No Data found!"), 200);
			}
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}// End WishList by ClientCode
		
	// Start user profile
	public function userProfile_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '')
		{ 
			$clientCode = $postData["clientCode"];
			$orderColumns = array("clientmaster.*, clientprofile.*,IFNULL(citymaster.cityName,'-') as city");
			$cond=array('clientmaster' . ".code" => $clientCode,'clientprofile.isActive'=>1);
			$orderBy = array('clientmaster' . ".id" => 'ASC','clientprofile.id'=>"DESC");
			$join = array('citymaster'=>'clientmaster.cityCode=citymaster.code','clientprofile' => 'clientmaster' . '.code=' . 'clientprofile' . '.clientCode');
			$joinType=array('citymaster'=>'left','clientprofile' =>'inner');
			$resultData = $this->GlobalModel->selectQuery($orderColumns,'clientmaster',$cond,$orderBy,$join,$joinType);
			if($resultData)
			{
			   	$result['userProfile']=$resultData->result_array()[0];
			    return $this->response(array("status" => "200","result"=>$result), 200);	
			} 
			else 
			{
			  
			    return $this->response(array("status" => "200","msg"=>"No Data Found!"), 200);	
			}
		}
		else
		{
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	// End user profile
		
	// Start update profile
	public function updateProfile_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != ''&& $postData["name"] != '' && $postData["mobile"] != '' && $postData["gender"] != '' && $postData["city"] != '' && $postData["state"] != '' && $postData["area"] != '' && $postData["local"] != '' && $postData["flat"] != '' && $postData["pincode"] !='' && $postData["areaCode"] !='')
		{ 
			if($postData["emailId"]){
			$e_id=filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL);
			}
			else
			{
				$e_id="";
			}
			
			$dataMaster=[
				"code" => $postData["clientCode"],
				"name" => $postData["name"],
				"emailId" => $e_id,
				"mobile" => $postData["mobile"]
			];
			
			$dataProfile=[
				"gender" => $postData["gender"],
				"city" => $postData["city"],
				"local" => $postData["local"],
				"area" => $postData["area"],
				"state" => $postData["state"],
				"flat" => $postData["flat"],
				"pincode" => $postData["pincode"],
				"landMark" => $postData["landMark"],
				"areaCode"=> $postData["areaCode"]
				
			]; 
			
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster')->result_array();
			  
			if(sizeof($resultData) == 1)
			{
				$resultMaster = $this->GlobalModel->doEdit($dataMaster,'clientmaster',$postData["clientCode"]);
				
				if($resultMaster != false)
				{ 
					$resultProfile = $this->GlobalModel->doEditWithField($dataProfile,'clientprofile','clientCode',$postData["clientCode"]);
					
					if($resultProfile != false)
					{
						return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
					}
				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
			}
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update profile
	
	//updateuseraddress
	public function updateProfileAddress_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '' && $postData["city"] != '' && $postData["state"] != '' && $postData["area"] != '' && $postData["local"] != '' && $postData["flat"] != '' && $postData["pincode"] !=''  && $postData["areaCode"] !='')
		{ 
		 
			$dataMaster=[
				"code" => $postData["clientCode"],				 
			];
			
			$dataProfile=[
				"city" => $postData["city"],
				"local" => $postData["local"],
				"area" => $postData["area"],
				"state" => $postData["state"],
				"flat" => $postData["flat"],
				"pincode" => $postData["pincode"],
				"landMark" => $postData["landMark"],
				"areaCode" => $postData["areaCode"],
			]; 
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster')->result_array();
			if(sizeof($resultData) == 1)
			{
				$resultMaster = $this->GlobalModel->doEdit($dataMaster,'clientmaster',$postData["clientCode"]);
				if($resultMaster != false)
				{ 
					$resultProfile = $this->GlobalModel->doEditWithField($dataProfile,'clientprofile','clientCode',$postData["clientCode"]);
					
					if($resultProfile != false)
					{
						return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
					}
				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
			}
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}//end update user address
	
	//user lat long update profile
	public function updateUserLatlong_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '' && $postData["latitude"] !='' && $postData["longitude"] !='')
		{
				$dataProfile=["latitude"=>$postData["latitude"],
								"longitude"=>$postData["longitude"]
							];
							
				$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster')->result_array();
			  
				if(sizeof($resultData) == 1)
				{
					$resultProfile = $this->GlobalModel->doEditWithField($dataProfile,'clientprofile','clientCode',$postData["clientCode"]);
					
					if($resultProfile != false)
					{
						return $this->response(array("status" => "200", "message" => "Your Location has been updated successfully."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => " Failed to update your Location."), 200);
					}
					
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
					
				}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	
	//Add to cart by productCode and clientCode
	public function addToCart_post()
	{
		$postData = $this->post();
		
		if ($postData["productCode"] && $postData["clientCode"] !='' && $postData["quantity"] !='')
		{
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			$quantity = $postData["quantity"];
			
			$clientData = $this->GlobalModel->selectDataById($clientCode,'clientmaster')->result_array();  
			
			if(sizeof($clientData) > 0)
			{
				$condition2=array('productCode'=>$productCode,'clientCode'=>$clientCode,'isActive'=>1);
				$clientCart = $this->ApiModel->selectData('clientcarts','','',$condition2)->result_array();
	
				if(sizeof($clientCart) > 0)
				{
					return $this->response(array("status" => "inCart", "message" => "Product already present in your cart."), 200);
				}
				else
				{
					$data = [
						'clientCode'=>$clientCode,
						'productCode'=>$productCode,
						'quantity'=>$quantity,
						'isActive'=>1
					];
					
					$code = $this->GlobalModel->addNew($data, 'clientcarts','CART');
					
					if($code != 'false')
					{ 
						return $this->response(array("status" => "200", "message" => "Product added to your cart successfully."), 200);
					}
					else
					{ 
						return $this->response(array("status" => "fail", "message" => "Failed to add product to your cart . Please try again later."), 200);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "fail", "message" => "User not registered. Please register user before adding product to your cart."), 200);
			}
		}
		else
		{
			return $this->response(array("status" => "fail", "message" => "Required fields not found."), 400);
		} 
	
	}  //Ends Add to cart by productId and clientId
	
	// start get CartList by ClientCode
	public function getCartList_post()
	{
		$postData = $this->post();		
		if($postData["clientCode"] != '' && $postData['cityCode']!="")
		{
		    $cityCode = $postData['cityCode'];
			$clientCode = $postData["clientCode"];			
			$tableName="clientcarts";
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.code as cartCode" );
			$cond=array('clientcarts' . ".clientCode" => $clientCode,"productratelineentries.cityCode"=>$postData['cityCode'],"productmaster.isActive"=>1);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts.productCode=productmaster.code',"productratelineentries"=>'productmaster.code=productratelineentries.productCode');
			$joinType=array('productmaster' =>'inner','productratelineentries' =>'inner');
			$res=$this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType);
			if($res){
				$clientCartList = $res->result_array();
				if($postData["count"] != '') return $this->response(array("status" => "200","totalRecords"=>sizeof($clientCartList)), 200);
				for($j=0;$j<sizeof($clientCartList);$j++)
				{
					$productCode = $clientCartList[$j]['code'];
					$condition2=array('productCode'=>$productCode); 
					$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
					$imageArray=array();
					for($img=0;$img<sizeof($images_result);$img++)
					{
						array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
					}
					$clientCartList[$j]['images']=$imageArray;
					unset($imageArray);
				}
				$data['products'] = $clientCartList; 
				//$chargesResult = $this->ApiModel->selectData('deliverycharge',1,0)->result_array();
				$chargesResult = $this->GlobalModel->selectQuery('citymaster.minOrder,citymaster.deliveryCharge',"citymaster",array("citymaster.code"=>$cityCode));
				if($chargesResult){
					$minOrder = $chargesResult->result_array()[0]['minOrder'];
					$deliveryCharge = $chargesResult->result_array()[0]['deliveryCharge'];
				} else {
					$minOrder = 1000;
					$deliveryCharge = 15;
				}
				return $this->response(array("status" => "200","totalRecords"=>sizeof($clientCartList),"minimumOrder"=>$minOrder,"deliveryCharge"=>$deliveryCharge,"result"=>$data), 200);
			}
			else
			{
				return $this->response(array("status" => "300","totalRecords"=>0,"message"=>"no records found"), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	// End CartList by ClientCode
	
	//Start Update Cart
	public function updateCart_post()
	{
		$postData = $this->post();
		
		if($postData["cartCode"] != '' && $postData["quantity"] !='')
		{
			
			if($postData["quantity"]==0)
			{
				$result = $this->GlobalModel->deleteForever($postData["cartCode"],'clientcarts');
				if($result != 'false')
				{
					return $this->response(array("status" => "deletetrue", "message" => "delete successfully"), 200);
				}
				else
				{
					return $this->response(array("status" => "deletefalse", "message" => "delete failed"), 200);
				}
			}
			
			
			$data=array(
					"quantity" => $postData["quantity"]
				); 
				
			$result = $this->GlobalModel->doEdit($data,'clientcarts',$postData["cartCode"]);
			
			if($result != 'false')
			{
				return $this->response(array("status" => "200", "message" => "cart updated."), 200);
			}
			else
			{
				return $this->response(array("status" => "400", "message" => " Failed to update."), 200);
				}
		}
		else
		{
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	//update Cart
	
	// Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();
		
		$clientCode=""; 
		$oldPassword="";
		$dbPassword="";
		$newPassword="";
		$cfmPassword=""; 
		
		if ($postData["clientCode"] != '' && $postData["oldPassword"] != '' && $postData["newPassword"] != '')
		{
			$oldPassword=md5($postData["oldPassword"]);  
			
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster')->result_array();
			
			for($j=0;$j<sizeof($resultData);$j++)
			{
				$dbPassword=$resultData[$j]['password'];
			}
			if($dbPassword == $oldPassword)
			{ 
				$passData=[
					"password" => md5($postData["newPassword"])
				];
				
				$passresult = $this->GlobalModel->doEdit($passData,'clientmaster',$postData["clientCode"]);
				
				if($passresult != false)
				{
					return $this->response(array("status" => "200", "message" => "Your password has been updated successfully."), 200);
				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your password."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "You entered wrong current password."), 200);
			} 
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update password
	
	// start statelist
	public function getStateList_get()
	{ 
		$columnName = "state";
		$stateresult = $this->GlobalModel->selectDistinctData($columnName,'addressmaster')->result(); 
		
		if($stateresult)
		{
			return $this->response(array("status" => "200","result"=>$stateresult), 200);			
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	}// End statelist
	
	// start statelist
	public function getAreaList_get()
	{ 
		$tableName="addressmaster";
		$orderColumns = array("addressmaster.place,addressmaster.pincode" );
		$cond=array('addressmaster' . ".district" => 'Kolhapur','addressmaster' . ".state" => 'Maharashtra');
		$orderBy = array('addressmaster' . ".place" => 'ASC');
		$stateresult=$this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy)->result();
		if($stateresult)
		{
			$result["areaList"]=$stateresult;
			return $this->response(array("status" => "200","result"=>$result), 200);			
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	}
	// End statelist
	
	// start  termslist
	public function getTermsList_get()
	{   
		$termsResult = $this->GlobalModel->selectDataExcludeDelete("policy")->result_array(); 
		if($termsResult)
		{
			$result['terms']=$termsResult[0]['terms'];
			return $this->response(array("status" => "200","result"=>$result), 200);
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	}// End termslist
	
	// start FAQ
	public function getFaq_get()
	{   
		$faqResult = $this->GlobalModel->selectDataExcludeDelete("faq")->result_array(); 
		if($faqResult)
		{
			$result['faq']=$faqResult[0]['description'];
			return $this->response(array("status" => "200","result"=>$result), 200);			
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	}// End  FAQ
	
	public function placeOrder_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["paymentMode"] !='' && $postData["areaCode"] !='' && $postData['cityCode']!="")
		{
			$clientCode = $postData["clientCode"];
			$timeStamp=date("Y-m-d h:i:s");
			$totalamount= 0;
			$tableName="clientcarts";
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.code as cartCode");
			$cond=array('clientcarts' . ".clientCode" => $clientCode,'productratelineentries.cityCode'=>$postData['cityCode'],"productmaster.isActive"=>1);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts.productCode=productmaster.code',"productratelineentries"=>'productmaster.code=productratelineentries.productCode');
			$joinType=array('productmaster' =>'inner','productratelineentries' =>'inner');
			$clientCartList = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType)->result_array();
			if(sizeof($clientCartList)>0)
			{
				$insertArr = array(
					"clientCode"=> $postData["clientCode"],
					"cityCode" => $postData['cityCode'],
					"paymentref" => $postData["transactionId"],
					"paymentMode"=> $postData["paymentMode"],
					"paymentStatus" => "PNDG",
					"shippingCharges" => $postData["shippingCharges"],
					"address" => $postData["address"],
					"areaCode"=> $postData["areaCode"],
					"latitude"=>$postData["latitude"],
					"longitude"=>$postData["longitude"],
					"phone" => $postData["phone"],
					"orderStatus" => "PND",
					"isActive" => 1,
					'editDate'=>$timeStamp
				);
				$totalamount=$postData["shippingCharges"];
				  
				  
				$orderCode='ORDER'.rand(99,99999);
				$insertResult=$this->GlobalModel->addWithoutYear($insertArr, 'ordermaster', $orderCode);
				if($insertResult != 'false') 
				{
					for($i=0;$i<sizeof($clientCartList);$i++)
					{
						$amount = ($clientCartList[$i]["productSellingPrice"] * $clientCartList[$i]["quantity"]);
						$totalamount += $amount;
						$data = array(
							"orderCode"=> $insertResult,
							"productCode" => $clientCartList[$i]["code"],
							"weight" => $clientCartList[$i]["minimumSellingQuantity"],
							"productUom" => $clientCartList[$i]["productUom"],
							"productPrice" => $clientCartList[$i]["productSellingPrice"],
							"quantity" => $clientCartList[$i]["quantity"],
							"totalPrice" => ($clientCartList[$i]["productSellingPrice"] * $clientCartList[$i]["quantity"]),
							"isActive" => 1
						);
						
						$orderLineResult=$this->GlobalModel->addWithoutCode($data, 'orderlineentries');
						if($orderLineResult != 'false')
						{
							//stock update
							// $totalQty=($clientCartList[$i]["minimumSellingQuantity"] * $clientCartList[$i]["quantity"]);
							// $updatedStock=$clientCartList[$i]["stock"] - $totalQty;
							// $updateStockData=array(
								// "stock" => $updatedStock
							// );
							// $this->GlobalModel->doEditWithField($updateStockData, "stockinfo", "productCode",$clientCartList[$i]["code"]);
							$this->GlobalModel->deleteForeverFromField("code", $clientCartList[$i]["cartCode"],"clientcarts");
						}
					}
					$updatePriceData = array('totalPrice'=>$totalamount);
					$resultArea = $this->GlobalModel->doEdit($updatePriceData,"ordermaster",$insertResult);
					
					//notification
					
					$resultData = $this->ApiModel->getFirebaseIdsByAddress($postData["areaCode"]);
					$resultArea = $this->GlobalModel->selectDataById($postData["areaCode"],"customaddressmaster");
					if($resultData->num_rows()>0){
					
						for($j=0; $j<$resultData->num_rows(); $j++){
							if($resultData->result()[$j]->firebase_id==null){
								
							}
							else{
								$title="New Order";
								$message = "New order from ".$resultArea->result()[0]->place;//$insertResult;
								$random=rand(0,999);
								$DeviceIdsArr[] = $resultData->result()[$j]->firebase_id;
								
								$dataArr = array();
								$dataArr['device_id'] = $DeviceIdsArr;
								$dataArr['message'] = $message;//Message which you want to send
								$dataArr['title'] = $title;
								$dataArr['order_id'] = $insertResult;
								$dataArr['random_id']=$random;
								$dataArr['type']='order';
								
								$notification['device_id'] = $DeviceIdsArr;
								$notification['message'] = $message;//Message which you want to send
								$notification['title'] = $title;
								$notification['order_id'] = $insertResult;
								$notification['random_id']=$random;
								$notification['type']='order';
								$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
							}
						}
					}
					//end notification
					return $this->response(array("status" => "200","message"=>"Order Placed Successfully.. Your OrderID is ".$insertResult), 200);
				}
				else
				{
					$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
				}
			}
			else
			{
				$this->response(array("status" => "400", "message"=>'cart is empty'), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	
	// start get OrderList by ClientCode
	public function getOrderList_post()
	{
		$postData = $this->post();		
		if($postData["clientCode"] != '')
		{
			$clientCode = $postData["clientCode"];
			$tableName="ordermaster";
			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode" );
			$cond=array('ordermaster' . ".clientCode" => $clientCode);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' .'.statusSName','paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' .'.statusSName');
			$joinType=array('orderstatusmaster' =>'inner','paymentstatusmaster' =>'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType);	
			if($resultQuery)
			{
				$clientOrderList=$resultQuery->result_array();
				$totalOrders=sizeof($clientOrderList);
				for($i=0;$i<sizeof($clientOrderList);$i++)
				{
					$cityCode = $clientOrderList[$i]['cityCode'];
					$linetableName="orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond=array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries.productCode=productmaster.code');
					$linejoinType=array('productmaster' =>'inner');					
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns,$linetableName,$linecond,$lineorderBy,$linejoin,$linejoinType);
					if($orderProductRes){
						$orderProductList=$orderProductRes->result_array();
						for($j=0;$j<sizeof($orderProductList);$j++)
						{
							$condition2=array('productCode'=>$orderProductList[$j]["productCode"]); 
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							$productCode=$orderProductList[$j]["productCode"];
							$imageArray=array();							
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
							}							
							$orderProductList[$j]['images']=$imageArray;
							unset($imageArray);
						}
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s',$clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate']=$oDt;
					}
				}
				$finalResult['orders']=$clientOrderList;
				return $this->response(array("status" => "200","totalOrders"=>$totalOrders,"result"=>$finalResult), 200);
			}
			else{
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	// End OrdersList by ClientCode

	//reset password
	public function resetpassword_post()
	{
		$postData = $this->post();
		if ($postData['type'] !='' && $postData["username"] != '' )		{
			$checkdata = array();
			if($postData['type']=='mobile'){
				$mobilenumber = $postData['username'];
				$checkdata = array('mobile'=>$mobilenumber);
			}else{
				$email = $postData['username'];
				$checkdata = array('emailId'=>$email);
			}
			if($this->GlobalModel->checkExistAndInsertRecords($checkdata,'clientmaster'))
			{
				return $this->response(array("status" => "406","message"=>"Please Enter Registered mobile number or Email ID!"), 200);
				exit();
			}
			else
			{
				$tableName="clientmaster";
				$orderColumns = array("clientmaster.code");
				$condition=array();
				if($postData['type']=='mobile'){
					$condition=array('clientmaster'.".isActive"=>1,'clientmaster'.".mobile"=>$postData['username']);
				}
				else{
					$condition=array('clientmaster'.".isActive"=>1,'clientmaster'.".emailId"=>$postData['username']);
				}
				$result = $this->GlobalModel->selectQuery($orderColumns,$tableName,$condition)->result();
				if($result){
				    $userCode=$result[0]->code;
				    $exist = $this->GlobalModel->selectQuery("resetpassword.*","resetpassword",array("resetpassword.userCode"=>$userCode));
				    if($exist){
			        	return $this->response(array("status" => "200","message"=>"Password reset request already sent. Please wait  Change your password after login."), 200);
						exit();
				    } else {
    				
    					$insertArr = array(
    						"userCode"=> $userCode, 
    						"isActive" => 1
    					);
    					$insertResult=$this->GlobalModel->onlyinsert($insertArr, 'resetpassword');
    					
    					if($insertResult != 'false') 
    					{
    						return $this->response(array("status" => "200","message"=>" Reset password Request sent. default password is 123456 ... try after admin reset. Change your password after login."), 200);
    						exit();
    					}
    					else
    					{
    						return $this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
    						exit();
    					}
				    }
				}else
				{
					return $this->response(array("status" => "400","message"=>"Please Contact Administrator"), 200);
					exit();
				}
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => "Invalid Parameters"), 400);
		}
	}
	
	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();		
		if ($postData["clientCode"] != ''&& $postData["firebaseId"] != '')
		{
			$dataMaster=[
				"firebaseId" => $postData["firebaseId"]
			];
			$resultMaster = $this->GlobalModel->doEdit($dataMaster,'clientmaster',$postData["clientCode"]);
				
			if($resultMaster != false)
			{ 
				return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
			}
			else
			{
				return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
			}
		}
		else
		{
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 200);
		}
	}  
	// End update firebaseId
		
	// Get Product list by keyword 
	public function searchProductByKeyword_post()
	{
		$postData = $this->post();		
		if ($postData["keyword"] != '' && $postData["offset"] !='' && $postData['cityCode']!="")
		{
			$keyword = $postData["keyword"];
			$product_offset = $postData["offset"];
			$product_limit = 10;			 
			$condition2=array('isActive'=>1);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster','','',$condition2)->result());			
			if($totalProduct){
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive" );
				$cond=array('productmaster' .'.isActive'=>1,"productratelineentries.cityCode"=>$postData['cityCode']);
				$orderBy = array('productmaster' . ".id" => 'ASC');
				$join = array("productratelineentries"=>'productmaster.code=productratelineentries.productCode');
				$joinType=array('productratelineentries' =>'inner');
				$like = array('productmaster.productName' => $keyword . '~both');			
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,$like,$product_limit,$product_offset);
			 
				if($resultQuery)
				{
					$limitProduct_result=$resultQuery->result_array();
					for($j=0;$j<sizeof($limitProduct_result);$j++){
					
							$condition2=array('productCode'=>$limitProduct_result[$j]['code']); 
							$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
							$imageArray=array();
							
							for($img=0;$img<sizeof($images_result);$img++)
							{
								array_push($imageArray, base_url().'uploads/product/'.$limitProduct_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
							}
							if($limitProduct_result[$j]['productUom']=='PC' && $limitProduct_result[$j]['minimumSellingQuantity']>1)
							{
								$limitProduct_result[$j]['productUom']='PCS';
							}
							$limitProduct_result[$j]['images']=$imageArray;
							unset($imageArray); 
					}
						  
					$data['products']=$limitProduct_result;
					 
					return $this->response(array("status" => "200","totalRecords" => $totalProduct,"result"=>$data), 200);
				}
				else{
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
		 	}
			else{
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		}else{
				$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		} 
	
	}  //Ends Product list by keyword
	
	//Start cancel order
	public function cancleOrder_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '' && $postData["orderCode"] != '' )
		{ 
			$userCode = $postData["clientCode"];
			$code=$postData["orderCode"];
			$nowdate=date('Y-m-d h:i:s');
			$Result = $this->GlobalModel->selectDataByPND('code',$code,'ordermaster',$userCode);
			if($Result)
			{
				$data=array('orderStatus'=>"CAN","cancelledTime"=>$nowdate);
				$passresult = $this->GlobalModel->doEditWithField($data,'ordermaster','code',$code);
				if($passresult != false)
				{
					return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
				} 
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
			} 		
		}
		else
		{
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	//End Cancel Order`
	
	//
	function getOffers_post()
	{
		$postData = $this->post();
		if($postData['cityCode']!=""){		
			$tableName="offers";
			$todayDate=date('Y-m-d');;
			$orderColumns = array("offers.*" );
			$cond=array('offers' . ".startDate <=" =>$todayDate,'offers' . ".expireDate >=" =>$todayDate,"offers.isActive" =>1,"offers.cityCode"=>$postData['cityCode']);
			$orderBy = array('offers' . ".id" => 'DESC');
			$join = array();
			$joinType=array();	
			$res=$this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType);
			if($res){
				$result=$res->result_array();
				for($j=0;$j<sizeof($result);$j++){
					$result[$j]['expireDate']=date('d-M-Y',strtotime($result[$j]['expireDate']));
				}
				$data["offers"] = $result;
				return $this->response(array("status" => "200","totalRecords"=>sizeof($result),"result"=>$data), 200);
			}
			else
			{
				return $this->response(array("status" => "300","totalRecords"=>0,"message"=>"no records found"), 200);
			}
		} else {
			return $this->response(array("status" => "200","totalRecords"=>0,"message"=>"No Offers found!"), 200);
		}
	}

	public function checkNoti_get()
	{
		$DeviceIdsArr[] = 'elDHQU1fL3g:APA91bHQlZDyTDZntJP-sJ9TdMGheOucdpeieGSJVeNhGe40ERI17t6Xc5OpY0zAA5Vy9Awp7NyRLkWXTWLLgu1EKFrpbLSHgQaFC8ZukbzNTXzX6iIdEDDrTEZ-e76HADbjRv2Hhg_F';
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = "New order ";//Message which you want to send
		$dataArr['title'] = 'New Order';
		$dataArr['order_id'] = 'order_id';
		$dataArr['random_id']='random_id';
		$dataArr['image']='ic_launcher_round';
		$dataArr['type']='order';		
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] ="New order from ";//Message which you want to send
		$notification['title'] = 'New Order';
		$notification['order_id'] = 'order_id';
		$notification['random_id']='random_id';
		$notification['image']='ic_launcher_round';
		$notification['type']='order';
		$notify = $this->notificationlibv_3->pushNotification($dataArr,$notification);
	}
	
	public function maintenance_get()
	{		
		$resultData = $this->GlobalModel->selectQuery('settings.*','settings',array('settings.settingName'=>'maintenance_mode'));
		$maintenance_mode['maintenance'] = $resultData->result_array()[0]['settingValue'];
		$maintenance_mode['messageTitle'] = $resultData->result_array()[0]['messageTitle'];
		$maintenance_mode['messageDescription'] = $resultData->result_array()[0]['messageDescription'];
		return $this->response(array("status" => "200","result"=>$maintenance_mode), 200);
	}
	
}