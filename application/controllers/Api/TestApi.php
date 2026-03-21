<?php
 
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;
 
class TestApi extends REST_Controller {
    
    public function __construct()
    {
        parent::__construct();

        //$this->load->model('book_model');
		
		$this->load->helper('form','url','html');
   		$this->load->library('form_validation');
        $this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlib');
		
    }
	
	//category list by limit
	function categoryAndProduct_post()
	{
		$postData = $this->post();
		
		if ($postData["userId"] != '' && $postData["offset"] !='')
		{
			$category_offset=$postData["offset"];
			$category_limit=5;
			
			$totalRecords=sizeof($this->ApiModel->selectData('categorymaster','','')->result());
			
			$category_result = $this->ApiModel->selectData('categorymaster',$category_limit,$category_offset)->result_array();  
			
			if($category_result){
				
				for($i=0;$i<sizeof($category_result);$i++){
					
					$orderColumns = array("productmaster.*, stockinfo.stock");
					$cond=array('productmaster' . ".productCategory" => $category_result[$i]['categorySName']);
					$orderBy = array('productmaster' . ".id" => 'ASC');
					$join = array('stockinfo' => 'productmaster' . '.code=' . 'stockinfo' . '.productCode');
					$joinType=array('stockinfo' =>'inner');
					
					$product_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,'',$category_limit,$category_offset)->result_array();
					
					for($j=0;$j<sizeof($product_result);$j++){
						
						$condition2=array('productCode'=>$product_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
						$imageArray=array();
						
						for($img=0;$img<sizeof($images_result);$img++)
						{
							array_push($imageArray, base_url().'uploads/product/'.$product_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
						}
						
						$product_result[$j]['images']=$imageArray;
						unset($imageArray);
					}
					
					$proData['products']=$product_result;
					$category_result[$i]['productList']=$proData;
					$data['categories']=$category_result;
				}
				return $this->response(array("status" => "200","totalRecords" => $totalRecords,"result"=>$data), 200);
			}
			else{
				return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
			}
		}
		else
		{
			$this->response(array("status" => "400", "msg" => "Required field(s)."), 400);
		}
	}   //end category list
	
	
	
	// Get Product list by categorySName 
	public function productByCategory_post()
	{
		$postData = $this->post();
		
		if ($postData["categorySName"] != '' && $postData["offset"] !='')
		{  
			$categorySName = $postData["categorySName"];
			$product_offset = $postData["offset"];
			$product_limit = 10;
			 
			$condition2=array('productCategory'=>$categorySName);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster','','',$condition2)->result());
			
			if($totalProduct){
				
				$orderColumns = array("productmaster.*, stockinfo.stock" );
				$cond=array('productmaster' . ".productCategory" => $categorySName);
				$orderBy = array('productmaster' . ".id" => 'ASC');
				$join = array('stockinfo' => 'productmaster' . '.code=' . 'stockinfo' . '.productCode');
				$joinType=array('stockinfo' =>'inner');
			
				$limitProduct_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,'',$product_limit,$product_offset)->result_array();
			 
				for($j=0;$j<sizeof($limitProduct_result);$j++){
				
						$condition2=array('productCode'=>$limitProduct_result[$j]['code']); 
						$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
						
						$imageArray=array();
						
						for($img=0;$img<sizeof($images_result);$img++)
						{
							array_push($imageArray, base_url().'uploads/product/'.$limitProduct_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
						}
						
						$limitProduct_result[$j]['images']=$imageArray;
						unset($imageArray); 
				}
					  
				$data['products']=$limitProduct_result;
				 
				return $this->response(array("status" => "200","totalRecords" => $totalProduct,"result"=>$data), 200);
		 	}
			else{
				return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
			}
		}else{
				return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
		} 
	
	}  //Ends Product list by categorySName
	
	 
	// Get Product list by productId
	public function productById_post()
	{
		$postData = $this->post();
		
		if ($postData["productId"])
		{
			$productId = $postData["productId"];
			
			$orderColumns = array("productmaster.*, stockinfo.stock" );
			$cond=array('productmaster' . ".id" => $productId);
			$orderBy = array('productmaster' . ".id" => 'ASC');
			$join = array('stockinfo' => 'productmaster' . '.code=' . 'stockinfo' . '.productCode');
			$joinType=array('stockinfo' =>'inner');
			
			$Product_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,'','','')->result_array();
			
			for($j=0;$j<sizeof($Product_result);$j++){
				
				$condition2=array('productCode'=>$Product_result[$j]['code']); 
				$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
				
				$imageArray=array();
				
				for($img=0;$img<sizeof($images_result);$img++)
				{
					array_push($imageArray, base_url().'uploads/product/'.$Product_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
				}
				
				$Product_result[$j]['images']=$imageArray;
				unset($imageArray); 
			}
			
			$data['products']=$Product_result;  
		
			return $this->response(array("status" => "200","result"=>$data), 200);
			
		}else{
			return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
		}  
	
	} // Ends Get Product list by productId
	
	// Add to Wish list by productCode and clientCode
	public function addToWishlist_post()
	{
		$postData = $this->post();
		
		if ($postData["productCode"] && $postData["clientCode"] !='' )
		{
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			 
			$userData = $this->GlobalModel->selectDataById($clientCode,'clientmaster');  
			$clientEmail='';
			 
			
			if(sizeof($userData) > 0)
			{
				foreach ($userData->result() as $row) 
				{	
					$clientEmail = $row->emailId; 
				}
			  
				if($clientEmail != '')
				{ 
					$condition2=array('productCode'=>$productCode,'clientCode'=>$clientCode);
					$clientWishList = $this->ApiModel->selectData('clientwishlist','','',$condition2)->result();
				  
					if(sizeof($clientWishList) > 0)
					{
						foreach ($clientWishList as $row) 
						{	
							$code = $row->code;   
							$this->GlobalModel->delete($code,'clientwishlist');
						}
						
						return $this->response(array("status" => "400", "msg" => "Product removed from your wishlist successfully."), 400);
					}
					else
					{
						$data = [
							'productCode'=>$productCode,
							'clientCode'=>$clientCode,
							'emailId'=>$clientEmail
							
						];
						
						$code = $this->GlobalModel->addNew($data, 'clientwishlist','WISH');
						
						if($code != 'false')
						{
							// /* return $this->response(array("status" => "200","result"=>$data), 200); */
							return $this->response(array("status" => "200", "msg" => "Product added to your wishlist successfully."), 200);
						}
						else
						{
							return $this->response(array("status" => "400", "msg" => "Product not added. Please try again later."), 400);
						}
					}
					
				}
				else
				{
					return $this->response(array("status" => "400", "msg" => "Please verify your e-mail before adding product to your wishlist."), 400);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "msg" => "User not registered. Please register user before adding product to your wishlist."), 400);
			}
			
		}
		else
		{
			return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
		}  
	
	} // Ends Add to Wish list by productId and userId
	 
	
	//Add to cart by productCode and clientCode
	public function addToCart_post()
	{
		$postData = $this->post();
		
		if ($postData["productCode"] && $postData["clientCode"] !='' && $postData["weight"] !='' && $postData["uom"] !='' && $postData["quantity"] !='' && $postData["unitPrice"] !='' && $postData["totalPrice"] !='' )
		{
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			$quantity = $postData["quantity"];
			
			$clientData = $this->GlobalModel->selectDataById($clientCode,'clientmaster')->result_array();  
			
			if(sizeof($clientData) > 0)
			{
				$cartCode=$clientData[0]['cartCode'];
				 
				$condition2=array('productCode'=>$productCode,'clientCode'=>$clientCode,'isActive'=>1);
				$clientCart = $this->ApiModel->selectData('clientcarts','','',$condition2)->result_array();
	
				if(sizeof($clientCart) > 0)
				{ 
					return $this->response(array("status" => "400", "message" => "Product already present in your cart."), 400);
				}
				else
				{
					
					$data = [  
						'cartCode'=>$cartCode,
						'clientCode'=>$clientCode,
						'productCode'=>$productCode,
						'weight'=>$postData["weight"],
						'productUom'=>$postData["uom"],
						'quantity'=>$quantity,
						'unitPrice'=>$postData["unitPrice"],
						'totalPrice'=>$postData["totalPrice"],
						'isActive'=>1
					];
					
					$code = $this->GlobalModel->addNew($data, 'clientcarts','CART');
					
					if($code != 'false')
					{ 
						return $this->response(array("status" => "200", "message" => "Product added to your cart successfully."), 200);
					}
					else
					{ 
						return $this->response(array("status" => "400", "message" => "Failed to add product to your cart . Please try again later."), 400);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user before adding product to your cart."), 400);
			}
		}
		else
		{
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
	
	}  //Ends Add to cart by productId and clientId
	
	
	//Update cart by productCode and clientCode,quantity
	public function updateCart_post()
	{
		$postData = $this->post();
		$clientCartCode = 0;
		
		if ($postData["productCode"] && $postData["clientCode"] !='' && $postData["quantity"] !='' )
		{
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			$quantity = $postData["quantity"];
			
			$clientData = $this->GlobalModel->selectDataById($clientCode,'clientmaster')->result_array();  
			
			if(sizeof($clientData) > 0)
			{
				/*  $status=$clientData[0]['status'];  
				$cartCode=$clientData[0]['cartCode'];
				   if($status=='ACTIVE')
				{  */ 
					$condition2=array('productCode'=>$productCode,'clientCode'=>$clientCode,'isActive'=>1);
					$clientCart = $this->ApiModel->selectData('clientcarts','','',$condition2)->result_array();
				   
				  
					if(sizeof($clientCart) > 0)
					{
						if($quantity == 0)
						{
							$clientCartCode = $clientCart[0]['code'];   
							$this->GlobalModel->deleteForever($code,'clientcarts');
							
							return $this->response(array("status" => "400", "message" => "Product removed from your cart successfully."), 400);
						}
						else if($quantity == $clientCart[0]['quantity'])
						{
							return $this->response(array("status" => "400", "message" => "No change in your cart."), 400);
						}
						else
						{
							$data = [   
								'quantity'=>$quantity
							];
							
							$result = $this->GlobalModel->doEdit($data,'clientcarts',$clientCartCode);
							
							if($result != 'false')
							{ 
								return $this->response(array("status" => "200", "message" => "Your cart upadated successfully."), 200);
							}
							else
							{ 
								return $this->response(array("status" => "400", "message" => "No change in your cart. Please try again later."), 400);
							}
						}
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Your cart is empty."), 400);
					}
			   /* 	  }
				else
				{
					return $this->response(array("status" => "400", "message" => "Please verify your e-mail before adding product to your cart."), 400);
				} */
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user before adding product to your cart."), 400);
			}
		}
		else
		{
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}  
		
	}   //Ends update cart by productCode and clientCode,quantity
	
	
	// Get Product list by productName
	public function productByName_post()
	{
		$postData = $this->post();
		
		if ($postData["key"] && $postData["offset"] != '')
		{
			$productKey = $postData["key"];
			$product_offset = $postData["offset"];
			$product_limit = 10;
			
			$orderBy = array('productmaster' . ".id" => 'ASC'); 
			$like = array('productmaster' . ".productName" => $productKey);
			
			/* $limitProduct_result = $this->GlobalModel->selectQuery($orderColumns,'productmaster',$cond,$orderBy,$join,$joinType,'',$product_limit,$product_offset)->result_array();*/
			$Product_result = $this->GlobalModel->selectQuery('','productmaster','',$orderBy,'','',$like,$product_limit,$product_offset)->result_array();
			$Product_resultSize = sizeof($this->GlobalModel->selectQuery('','productmaster','',$orderBy,'','',$like)->result_array());
			
			for($j=0;$j<sizeof($Product_result);$j++){
				
				$condition2=array('productCode'=>$Product_result[$j]['code']); 
				$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
				
				$imageArray=array();
				
				for($img=0;$img<sizeof($images_result);$img++)
				{
					array_push($imageArray, base_url().'uploads/product/'.$Product_result[$j]['code'].'/'.$images_result[$img]['productPhoto']);
				}
				
				$Product_result[$j]['images']=$imageArray;
				unset($imageArray); 
			}
			
			$data['products']=$Product_result;  
		
			return $this->response(array("status" => "200","totalresult"=>$Product_resultSize,"result"=>$data), 200);
			
		}else{
			return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
		}  
	
	} // Ends Get Product list by productName
 
	
	// Get cart items by clientCode and cartCode
	public function cartItems_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] && $postData["cartCode"] != '')
		{
			$clientCode = $postData["clientCode"];
			$cartCode = $postData["cartCode"];
			
			$orderColumns = array("clientcarts.*");
			$cond=array('clientcarts' . ".clientCode" => $clientCode,'cartCode'=>$cartCode);
			$orderBy = array('clientcarts' . ".id" => 'ASC');
			  
			$cart_result = $this->GlobalModel->selectQuery($orderColumns,'clientcarts',$cond,$orderBy)->result_array();
			  
			for($j=0;$j<sizeof($cart_result);$j++)
			{ 
				$productCode = $cart_result[$j]['productCode'];
				$condition2=array('productCode'=>$productCode); 
				$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
				
				$imageArray=array();
				
				for($img=0;$img<sizeof($images_result);$img++)
				{
					array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
				}
				
				$cart_result[$j]['images']=$imageArray;
				unset($imageArray);
				 
			}
			
			$data['CartItems']=$cart_result;  
		
			return $this->response(array("status" => "200","totalresult"=>sizeof($cart_result),"result"=>$data), 200);
			
		}else{
			return $this->response(array("status" => "400", "msg" => "Data not found."), 400);
		}  
	
	}// Ends Get Cart items by clientCode and cartCode

	
	
	//start login  Process
	public function loginProcess_post()
	{
		$postData = $this->post();
		
		if($postData["type"] != '' && $postData["userId"] != '' && $postData["userPassword"] !='') 
		{
			
			if($postData["type"] == 'mobile')
			{  
				$data = array(
					"mobile" => $postData["type"] 
				);
				
				$userId = $postData["userId"];
				$userPassword = $postData["userPassword"];
				
				$resultData = $this->ApiModel->read_user_information($data);
			 	
				return $this->response(array("status" => "200","totalresult"=>sizeof($resultData),"result"=>$resultData), 200);
				
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
							"emailId" => $email
						);
						$resultData = $this->ApiModel->read_user_information($data);
				
						return $this->response(array("status" => "200","result"=>$resultData), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "incorrect Email or Password"), 400);
					} 
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
				}
				  
			}
			 
		}
		else
		{
			$this->response(array("status" => "400", "msg" => " * are Required field(s)."), 400);
		}
	}//end login  Process 
	
	 
	// Start registration
	public function registration_post()
	{
		$postData = $this->post();
		
		if ($postData["name"] != '' && $postData["emailId"] != '' && $postData["mobile"] != '' && $postData["password"] !='')
		{
			$email=filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL);
			$com_code = md5(uniqid(rand()));
			$cart_code= md5(uniqid(rand()));  
			$forgot = md5(uniqid(rand()));
			$checkEmailData=array("emailId"=>$email);
			
			if(!$this->GlobalModel->checkExistAndInsertRecords($checkEmailData,'clientmaster'))
			{
				return $this->response(array("status" => "406","message"=>"E-Mail already exist.Please Signin"), 200);
				exit();
			}
			
			$checkMobileData=array("mobile"=>$postData["mobile"]);
			
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
				"comCode" => $com_code,
				"cartCode" => $cart_code,
				"forgot" => $forgot
			  );
			  
			$insertResult=$this->GlobalModel->addNew($insertArr, 'clientmaster', 'CLNT');
			
			if($insertResult != 'false') 
			{
				$data = array(
					"clientCode"=> $insertResult 
				);
				
				$profileResult=$this->GlobalModel->addNew($data, 'clientprofile', 'CLPROF');
				  
			}
		}
		else
		{
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}// End registration
	
	
	//Start user profile
	public function userProfile_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '')
		{ 
			$clientCode = $postData["clientCode"];
			
			$orderColumns = array("clientmaster.*, clientprofile.*");
			$cond=array('clientmaster' . ".code" => $clientCode);
			$orderBy = array('clientmaster' . ".id" => 'ASC');
			$join = array('clientprofile' => 'clientmaster' . '.code=' . 'clientprofile' . '.clientCode');
			$joinType=array('clientprofile' =>'inner');
			
			$resultData = $this->GlobalModel->selectQuery($orderColumns,'clientmaster',$cond,$orderBy,$join,$joinType,'','','')->result_array();
			
			return $this->response(array("status" => "200","result"=>$resultData), 200);			
		}	 
		else
		{
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}// End user profile
	 
	// Start update profile
	public function updateProfile_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != ''&& $postData["name"] != '' && $postData["emailId"] != '' && $postData["mobile"] != '' && $postData["password"] !='' && $postData["gender"] != ''&& $postData["city"] != '' && $postData["local"] != '' && $postData["flat"] != '' && $postData["pincode"] !='' && $postData["state"] != '' && $postData["landMark"] !='')
		{ 
			$dataMaster=[
				"code" => $postData["clientCode"],
				"name" => $postData["name"],
				"emailId" => filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL),
				"mobile" => $postData["mobile"],
				"password" => md5($postData["password"]),
			];
			
			$dataProfile=[
				"gender" => $postData["gender"],
				"city" => $postData["city"],
				"local" => $postData["local"],
				"flat" => $postData["flat"],
				"pincode" => $postData["pincode"],
				"state" => $postData["state"],
				"landMark" => $postData["landMark"]
			];
			
			 /*   // $orderColumns = array("clientmaster.*, clientprofile.*");
			$cond=array('clientmaster' . ".code" => $postData["clientCode"]);
			$orderBy = array('clientmaster' . ".id" => 'ASC');
			$join = array('clientprofile' => 'clientmaster' . '.code=' . 'clientprofile' . '.clientCode');
			$joinType=array('clientprofile' =>'inner');
			
			$resultData = $this->GlobalModel->selectQuery($orderColumns,'clientmaster',$cond,$orderBy,$join,$joinType,'','','')->result_array();
			  */
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster');
			  
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
						return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 400);
					}
				}
				else
				{
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 400);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
			}
		}	 
		else
		{
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}  // End update profile
	
	// Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();
		
		$clientCode=""; 
		$oldPassword="";
		$dbPassword="";
		$newPassword="";
		$cfmPassword=""; 
		
		if ($postData["clientCode"] != '' && $postData["oldPassword"] != '' && $postData["newPassword"] != '' && $postData["cfmPassword"] != '')
		{
			$oldPassword=md5($postData["oldPassword"]);  
			
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"],'clientmaster')->result_array();
			
			for($j=0;$j<sizeof($resultData);$j++)
			{
				$dbPassword=$resultData[$j]['password'];
			}
			if($dbPassword == $oldPassword)
			{ 
				if($postData["newPassword"] == $postData["cfmPassword"])
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
						return $this->response(array("status" => "400", "message" => " Failed to update your password."), 400);
					}
				
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "You entered wrong confirm password."), 400);
				} 
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "You entered wrong current password."), 400);
			} 
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update password
	
	 
	
	 
	// get WishList by ClientCode
	public function getWishList_post()
	{
		$postData = $this->post();
		
		if ($postData["clientCode"] != '')
		{ 
			$clientCode = $postData["clientCode"];
			 
			$tableName="clientwishlist";
			$orderColumns = array("productmaster.*, clientwishlist.productCode, stockinfo.stock" );
			$cond=array();
			$orderBy = array('clientwishlist' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientwishlist' . '.productCode=' . 'productmaster' .'.code', 'stockinfo' => 'productmaster' . '.code=' . 'stockinfo' . '.productCode');
			$joinType=array('productmaster' =>'inner','stockinfo' =>'inner');
		
			 
			$clientWishList = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType)->result_array();
			 
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
					
					$clientWishList[$j]['images']=$imageArray;
					unset($imageArray);
				}
				
				$data['wishlist'] = $clientWishList;
			 
				return $this->response(array("status" => "200","totalresult"=>sizeof($clientWishList),"result"=>$data), 200);
		}	 
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}// End WishList by ClientCode
	
	
	// start get CartList by ClientCode
	public function getCartList_post()
	{
		$postData = $this->post();
		
		if($postData["clientCode"] != '')
		{
			$clientCode = $postData["clientCode"]; 
			$tableName="clientcarts";
			$orderColumns = array("productmaster.*, clientcarts.quantity,clientcarts.code as cartCode, stockinfo.stock" );
			$cond=array('clientcarts' . ".clientCode" => $clientCode);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts' . '.productCode=' . 'productmaster' .'.code', 'stockinfo' => 'productmaster' . '.code=' . 'stockinfo' . '.productCode');
			$joinType=array('productmaster' =>'inner','stockinfo' =>'inner');
		
			
			$clientCartList = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType)->result_array();
			 
			 if($postData["count"] != '')
				 return $this->response(array("status" => "200","totalRecords"=>sizeof($clientCartList)), 200);
				
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
				
				$chargesResult = $this->ApiModel->selectData('deliverycharge',1,0)->result_array();
				return $this->response(array("status" => "200","totalRecords"=>sizeof($clientCartList),"minimumOrder"=>$chargesResult[0]['minOrder'],"deliveryCharge"=>$chargesResult[0]['deliveryCharge'],"result"=>$data), 200);
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}// End CartList by ClientCode
	
	
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
	 
	// start get OrderList by ClientCode
	public function getOrderList_post()
	{
		$postData = $this->post();
		
		if($postData["clientCode"] != '')
		{
			$clientCode = $postData["clientCode"];
			 
			$tableName="ordermaster";
			$orderColumns = array("ordermaster.paymentmode,ordermaster.paymentStatus,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate,ordermaster.code as orderCode,orderlineentries.weight,orderlineentries.productPrice,orderlineentries.quantity,orderlineentries.productCode,orderlineentries.totalPrice as lineTotalPrice" );
			$cond=array('ordermaster' . ".clientCode" => $clientCode);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('orderlineentries' => 'ordermaster' . '.code=' . 'orderlineentries' .'.orderCode');
			$joinType=array('orderlineentries' =>'inner');
		 
			$clientOrderList = $this->GlobalModel->selectQuery($orderColumns,$tableName,$cond,$orderBy,$join,$joinType)->result_array();
			  
			for($i=0;$i<sizeof($clientOrderList);$i++)
			{
				$totalOrders = sizeof($clientOrderList[$i]['orderCode']);
				$orderCode = $clientOrderList[$i]['orderCode'];
				$orderDate = $clientOrderList[$i]['orderDate'];
				$paymentmode = $clientOrderList[$i]['paymentmode'];
				$paymentStatus = $clientOrderList[$i]['paymentStatus'];
				$weight = $clientOrderList[$i]['weight'];
				$quantity = $clientOrderList[$i]['quantity'];
				$lineTotalPrice = $clientOrderList[$i]['lineTotalPrice'];
				$orderTotalPrice = $clientOrderList[$i]['orderTotalPrice'];
				 
				$productCode = $clientOrderList[$i]['productCode'];
				
				$productResult = $this->GlobalModel->selectDataById($productCode,'productmaster')->result_array();
			
				for($j=0;$j<sizeof($productResult);$j++)
				{ 
					$condition2=array('productCode'=>$productCode); 
					$images_result = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
					
					$imageArray=array();
					
					for($img=0;$img<sizeof($images_result);$img++)
					{
						array_push($imageArray, base_url().'uploads/product/'.$productCode.'/'.$images_result[$img]['productPhoto']);
					}
					
					$productResult[$j]['images']=$imageArray;
					unset($imageArray);
				}
				
				$clientOrderList[$i]=$productResult;  
			}
			
			$data['orders'] =[
					"orderCode" => $orderCode,
					"orderDate" => $orderDate,
					"paymentmode" => $paymentmode,
					"paymentStatus" => $paymentStatus,
					"weight" => $weight,
					"quantity" => $quantity,
					"lineTotalPrice" => $lineTotalPrice,
					"orderTotalPrice" => $orderTotalPrice,
					"orderProducts" => $clientOrderList
				];
			 
			 
			return $this->response(array("status" => "200","totalOrders"=>$totalOrders,"result"=>$data), 200);
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}// End OrdersList by ClientCode
	
	public function rshow_get()
	{
		//$productresult=$this->ApiModel->selectData('categorymaster','','')->result();
		//$productresult = $this->GlobalModel->selectData('clientwishlist')->result();
		//$productresult = $this->GlobalModel->selectDataExcludeDelete('faq')->result();
		$productresult = $this->GlobalModel->selectData('clientmaster')->result();
		//$productresult = $this->GlobalModel->selectData('ordermaster')->result();
		//$productresult = $this->GlobalModel->selectData('orderlineentries')->result();
		//$productresult = $this->GlobalModel->selectData('clientprofile')->result();
		//$productresult = $this->GlobalModel->selectData('productmaster')->result();
		//$productresult = $this->GlobalModel->selectData('clientcarts')->result();
		//$productresult = $this->GlobalModel->selectData('stockinfo')->result();
		// function selectDistinctDataByField($field, $value, $requiredColumns, $groupByValue, $tblname) {
		// $requiredColumns = 'state';
		// $requiredColumns=array
		// ( 
			// 'state'
		// );
		// $productresult = $this->GlobalModel->selectDistinctData('state','addressmaster')->result();
		//$productresult = $this->GlobalModel->selectDataExcludeDelete('stockinfo')->result_array();  
		//$productresult = $this->GlobalModel->selectDataByField('productCategory',"TES",'productmaster')->result();  
		
		//$userCode = "CLNT18_2";
		//$clientCode = "CLNT18_2";
		//$productCode = 'PRODT18_43';
		
		//$condition2=array('productCode'=>$a);
		//$condition2=array('productCode'=>$productCode,'clientCode'=>'$userCode');
		//$productresult = $this->ApiModel->selectData('clientwishlist','','',$condition2)->result();
		
		//$productresult = $this->GlobalModel->selectDataByField('productCode',$productCode,'clientwishlist')->result();  
		
		//$coloums=array('clientCode','cartCode');
			
		//$values=array('CLNT18_2','a05fe7f26559d5429082006bf188836f');
		 
		//$productresult=$this->GlobalModel->selectActiveDataByMultipleFields($coloums,$values,'clientwishlist')->result_array();	
		
		
		// $data = [
			// 'productCode'=>"PRODT18_26",
			// 'clientCode'=>"CLNT18_4",
			// 'emailId'=>"shubh@gmail.com",
			
		// ];
		//$code = "WISH18_4";
		//$rs=$this->GlobalModel->deleteForever($code,'clientwishlist');
		
		//$code = $this->GlobalModel->addNew($data, 'clientwishlist','WISH');
		// $cond=array('clientcarts' . ".clientCode" => 'CLNT18_2','cartCode'=>'a05fe7f26559d5429082006bf188836f','isActive'=>1);
		// $productresult = $this->GlobalModel->selectQuery('','clientcarts',$cond,'')->result_array();
		
		//$productresult=$this->GlobalModel->selectActiveDataByMultipleFields($coloums,$values,'clientcarts')->result_array();	
		
		
		// $condition2=array('productCode'=>$productCode); 
		// $productresult = $this->ApiModel->selectData('productphotos','','',$condition2)->result_array();
		
		 // $d=['productCode'=>'PRODT_42'];
		 // $this->GlobalModel->doEditWithField($d,'clientcarts', 'id' ,1);
		 
		// $oldPassword=md5("admin@123");  
		// $dbPassword="";	
		// $resultData = $this->GlobalModel->selectDataById("CLNT18_34",'clientmaster')->result_array();
		
		// for($j=0;$j<sizeof($resultData);$j++)
		// {
			// $dbPassword=$resultData[$j]['password'];
		// }
		// $data=[
			// "dbPassword" => $dbPassword,
			// "oldPassword" => $oldPassword,
		// ]; 
		
		if($productresult){ 
			return $this->response(array("status" => "200", "result" => $productresult ), 200);
		}  
		else{
			return $this->response(array("status" => "400", "message" => "Data Not Found"), 400);
		}
	}

	
	public function confirmOrderPlace_post()
	{
			$postData = $this->post();
		
			if($postData["orderCode"] != '' && $postData['userCode'] !='' && $postData['orderStatus'] !='')
			{
				$orderCode = $postData["orderCode"];
				$timeStamp=date("Y-m-d h:i:s");
				
				if($postData['orderStatus'] == 'PLC')
				{

						$data=array(
						'orderStatus'=>$postData['orderStatus'],
						// 'paymentStatus'=>$paymentStatus,
						'placedTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
					
					
					$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
				  
					$getLineData=$this->GlobalModel->selectDataByField('orderCode',$orderCode,'orderlineentries');
				 
					foreach($getLineData->result() as $line){
					   $productCode=$line->productCode;
					   $stock=($line->quantity*$line->weight);
					   $consumeStock = $this->GlobalModel->stockChange($productCode,$stock,'consume');
					}
						
					//get Data from OrderCode and send push notification
						$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;
						
						$random=rand(0,999);
						$datamsg=array("title"=>'Order Successfully Placed',"message"=>'Order Successfully Placed',"order_id"=>$orderCode,"random_id"=>$random);
						
						
						$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
						
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message'];//Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id']=$datamsg['random_id'];
						
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message'];//Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id']=$datamsg['random_id'];
						
						$notify = $this->notificationlib->pushNotification($dataArr,$notification);
					
					
					if($result!='false')
					{
						
						return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);

						
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Failed To Place Order."), 200);
					}
					
				}
				else
				{
					$data=array(
						'orderStatus'=>$postData['orderStatus'],
						// 'paymentStatus'=>$paymentStatus,
						'placedTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
					
					
					$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
					
					//get Data from OrderCode and send push notification
						$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;
						
						$random=rand(0,999);
						$datamsg=array("title"=>'Order Successfully Placed',"message"=>'Order Successfully Placed',"order_id"=>$orderCode,"random_id"=>$random);
						
						
						$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
						
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message'];//Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id']=$datamsg['random_id'];
						
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message'];//Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id']=$datamsg['random_id'];
						
						$notify = $this->notificationlib->pushNotification($dataArr,$notification);
						
						if($result!='false')
					{
						
						return $this->response(array("status" => "200", "message" => "Order Successfully Rejected."), 200);

						
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Failed To Reject Order."), 200);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
			}
	}
	
	public function confirmOrderDelivered_post()
	{
		$postData = $this->post();
		
			if($postData["orderCode"] != '' && $postData['userCode'] !='' && $postData['orderStatus'] !='')
			{
				$orderCode=$postData["orderCode"];
				$orderStatus=$postData['orderStatus'];
				$timeStamp=date("Y-m-d h:i:s");
				
				$data=array();
			  	switch ($orderStatus) 
				{
					case "SHP":
						$data=array(
						'orderStatus'=>$orderStatus,
						'paymentStatus'=>'PNDG',
						'shippedTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
						
						break;
					case "DEL":
						$data=array(
						'orderStatus'=>$orderStatus,
						'paymentStatus'=>'PID',
						'deliveredTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
						
						break;
					case "RJT":
						$data=array(
						'orderStatus'=>$orderStatus,
						'paymentStatus'=>'RJCT',
						'rejectedTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
						
						break;
				
				}
				
				$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
				
				/*---Notification Code ---11/11/2019*/
				$clnt = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
				$clientCode = $clnt->result()[0]->clientCode;
			
				$random=rand(0,999);
			
			
				switch ($orderStatus) 
				{
					case "SHP":
						$not=array(
						'title'=>'Order is Successfully Shipped',
						'message'=>'Order is Successfully Shipped',
						'order_id'=>$orderCode,
						'random_id'=>$random
						);
						
						break;
					case "DEL":
						$not=array(
						'title'=>'Order is Successfully Deliverd',
						'message'=>'Order is Successfully Deliverd',
						'order_id'=>$orderCode,
						'random_id'=>$random
						);
						break;
					case "RJT":
						$not=array(
							'title'=>'Order is Rejected',
							'message'=>'Order is Deliverd',
							'order_id'=>$orderCode,
							'random_id'=>$random
							);
						break;
					
				}
			  
				$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
				$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
			
				$dataArr = array();
				$dataArr['device_id'] = $DeviceIdsArr;
				$dataArr['message'] = $not['message'];//Message which you want to send
				$dataArr['title'] = $not['title'];
				$dataArr['order_id'] = $not['order_id'];
				$dataArr['random_id']=$not['random_id'];
				
				$notification['device_id'] = $DeviceIdsArr;
				$notification['message'] = $not['message'];//Message which you want to send
				$notification['title'] = $not['title'];
				$notification['order_id'] = $not['order_id'];
				$notification['random_id']=$not['random_id'];
				
				$notify = $this->notificationlib->pushNotification($dataArr,$notification);
				/*if($notify)
				{
					echo $notify;
				}
				else{
					echo "else";
					
				}*/
			
			
				 if($result!='false')
				{
					return $this->response(array("status" => "200", "message" => "Successfully Order Status Changed."), 200);
				 
				}
				else
				{
					return $this->response(array("status" => "400", "message" => "Order Status Failed To Change."), 200);
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
			}
		
	}
	
	
	//get client Delivered order list
	public function getDeliveredOrders_post()
	{
		$postData = $this->post();
		
		if($postData['code'] !='' && $postData['offset']!="")
		{
			//$areaCode=$postData['areaCode'];
					
			$areaLineData = $this->GlobalModel->selectDataByField('userCode',$postData["code"],'useraddresslineentries');
			$response=array();
			$areaCodes="";
			$size=sizeof($areaLineData->result());
			$count=1;
			foreach($areaLineData->result() as $row)
			{
				$comma="";
				if($size!=$count)
				{
					$comma=",";
				}
				$areaCodes.="'".$row->addressCode."'".$comma;
				$count++;
			}
			
			
			
			if($areaCodes !='')
			{
			
					$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,clientmaster.code as clientCode,clientmaster.name" );
					$join = array('clientmaster'=>'clientmaster.code = ordermaster.clientCode','orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' .'.statusSName','paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' .'.statusSName');
					$joinType=array('clientmaster' =>'left','orderstatusmaster' =>'inner','paymentstatusmaster' =>'inner');
					$cond=array();
					$orderBy = array('ordermaster' . ".id" => 'ASC');
					$like=array();
					$limit="10";
					$offset=$postData['offset'];
					$groupByColumn=array();
					$extraCondition="ordermaster.orderStatus IN ('DEL') AND ordermaster.areaCode IN(".$areaCodes.")";
					
					$resultQuery = $this->GlobalModel->selectQuery($orderColumns,'ordermaster',$cond,$orderBy,$join,$joinType,$like,$limit,$offset,$groupByColumn, $extraCondition);
					
					if($resultQuery)
					{
						$clientOrderList=$resultQuery->result_array();
						$totalOrders=sizeof($clientOrderList);
						for($i=0;$i<sizeof($clientOrderList);$i++)
						{
							$linetableName="orderlineentries";
							$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
							$linecond=array('orderlineentries' . ".orderCode" => $clientOrderList[$i]['orderCode']);
							$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
							$linejoin = array('productmaster' => 'orderlineentries' . '.productCode=' . 'productmaster' .'.code');
							$linejoinType=array('productmaster' =>'inner');
							
							$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns,$linetableName,$linecond,$lineorderBy,$linejoin,$linejoinType);
							if($orderProductRes)
							{
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
							}
							 
							
						}
						
						$finalResult['orders']=$clientOrderList;
						$this->response(array("status" => "200", "message" => " Order details","result"=>$finalResult), 200);
					}
					else
					{
							$this->response(array("status" => "400", "message" => " No more Records"), 200);
					}
					
			}
			else
			{
				$this->response(array("status" => "400", "message" => " No more Records"), 200);
			}
		}
		else
		{
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	}
	
	
	
	public function confirmOrderPlace_post()
	{
			$postData = $this->post();
		
			if($postData["orderCode"] != '' && $postData['userCode'] !='' && $postData['orderStatus'] !='')
			{
				$orderCode = $postData["orderCode"];
				$timeStamp=date("Y-m-d h:i:s");
				
				if($postData['orderStatus'] == 'PLC')
				{
						$data=array(
						'orderStatus'=>$postData['orderStatus'],
						'placedTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
					
					$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
				
					
					if($result!='false')
					{
						//get Data from OrderCode and send push notification
						$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;
						
						$random=rand(0,999);
						$datamsg=array("title"=>'Order Successfully Placed',"message"=>'Order Successfully Placed',"order_id"=>$orderCode,"random_id"=>$random);
						
						
						$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
						
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message'];//Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id']=$datamsg['random_id'];
						
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message'];//Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id']=$datamsg['random_id'];
						
						$notify = $this->notificationlib->pushNotification($dataArr,$notification);
						
						return $this->response(array("status" => "200", "message" => "Order Successfully Placed."), 200);
						
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Failed To Place Order."), 200);
					}
				}
				else if($postData['orderStatus'] == 'DEL')
				{
					$data=array(
						'orderStatus'=>$postData['orderStatus'],
						'paymentStatus'=>'PID',
						'deliveredTime'=>$timeStamp,
						'editID'=>$postData['userCode']
						);
						
						$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
						
						if($result != 'false')
						{
							$getLineData=$this->GlobalModel->selectDataByField('orderCode',$orderCode,'orderlineentries');
							foreach($getLineData->result() as $line){
							   $productCode=$line->productCode;
							   $stock=($line->quantity*$line->weight);
							   $consumeStock = $this->GlobalModel->stockChange($productCode,$stock,'consume');
							}
							
							
							/*---Notification Code ---11/11/2019*/
							$clnt = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
							$clientCode = $clnt->result()[0]->clientCode;
						
							$random=rand(0,999);
							$datamsg=array("title"=>'Order Deliverd',"message"=>'Order successfully delivered.',"order_id"=>$orderCode,"random_id"=>$random);
						
							$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
							$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
							
							$dataArr = array();
							$dataArr['device_id'] = $DeviceIdsArr;
							$dataArr['message'] = $datamsg['message'];//Message which you want to send
							$dataArr['title'] = $datamsg['title'];
							$dataArr['order_id'] = $datamsg['order_id'];
							$dataArr['random_id']=$datamsg['random_id'];
							
							$notification['device_id'] = $DeviceIdsArr;
							$notification['message'] = $datamsg['message'];//Message which you want to send
							$notification['title'] = $datamsg['title'];
							$notification['order_id'] = $datamsg['order_id'];
							$notification['random_id']=$datamsg['random_id'];
							
							$notify = $this->notificationlib->pushNotification($dataArr,$notification);
							
							return $this->response(array("status" => "200", "message" => "Order Successfully Delivered."), 200);
						}
						else
						{
							return $this->response(array("status" => "400", "message" => "Failed To delivered Order."), 200);
						}
				}
				else
				{
					$data=array(
						'orderStatus'=>$postData['orderStatus'],
						'placedTime'=>$timeStamp,
						'paymentStatus'=>'RJCT',
						'editID'=>$postData['userCode']
						);
					
					$result=$this->GlobalModel->doEdit($data,'ordermaster',$orderCode);
					
						
					if($result!='false')
					{
					//get Data from OrderCode and send push notification
						$orderData = $this->GlobalModel->selectDataByField('code',$orderCode,'ordermaster');
						$clientCode = $orderData->result()[0]->clientCode;
						
						$random=rand(0,999);
						$datamsg=array("title"=>'Order Rejected',"message"=>'Your Order is rejected,Your order id is '.$orderCode,"order_id"=>$orderCode,"random_id"=>$random);
						
						$checkdevices= $this->GlobalModel->selectDataByField('code',$clientCode,'clientmaster');
						$DeviceIdsArr[] = $checkdevices->result()[0]->firebaseId;
						
						$dataArr = array();
						$dataArr['device_id'] = $DeviceIdsArr;
						$dataArr['message'] = $datamsg['message'];//Message which you want to send
						$dataArr['title'] = $datamsg['title'];
						$dataArr['order_id'] = $datamsg['order_id'];
						$dataArr['random_id']=$datamsg['random_id'];
						
						$notification['device_id'] = $DeviceIdsArr;
						$notification['message'] = $datamsg['message'];//Message which you want to send
						$notification['title'] = $datamsg['title'];
						$notification['order_id'] = $datamsg['order_id'];
						$notification['random_id']=$datamsg['random_id'];
						
						$notify = $this->notificationlib->pushNotification($dataArr,$notification);
						
						return $this->response(array("status" => "200", "message" => "Order Successfully Rejected."), 200);
					}
					else
					{
						return $this->response(array("status" => "400", "message" => "Failed To Reject Order."), 200);
					}
				}
			}
			else
			{
				return $this->response(array("status" => "400", "message" => "* are Required field(s)."), 400);
			}
	}
    
}