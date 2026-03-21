<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Api extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		//$this->load->model('book_model');		
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
	}

	public function getMainCategoryList_get()
	{
		$columns = "maincategorymaster.*";
		$cond = array('maincategorymaster' . ".isActive" => 1);
		$orderBy = array('maincategorymaster' . ".id" => 'ASC');
		$Records = $this->GlobalModel->selectQuery($columns, 'maincategorymaster', $cond, $orderBy);
		//print_r($this->db->last_query());
		if ($Records) {
			$data = array();
			foreach ($Records->result_array() as $r) {
				$path = "";
				if (file_exists('uploads/maincategory/' . $r['categoryPhoto'])) {
					$path = base_url() . 'uploads/maincategory/' . $r['categoryPhoto'];
				}
				$data[] = array("code" => $r['code'], "cateoryPhoto" => $path, "mainCategoryName" => $r['mainCategoryName']);
			}
			$response['mainCategoryList'] = $data;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $response), 200);
		} else {
			$data['mainCategoryList'] = array();
			return $this->response(array("status" => "300", "message" => "Data not found.", 'result' => $data), 200);
		}
	}

	//City list
	public function cityList_get()
	{
		$result = $this->GlobalModel->selectActiveData('citymaster')->result_array();
		if ($result) {
			$data['cities'] = $result;
			return $this->response(array("status" => "200", "result" => $data), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 200);
		}
	}

	// Start registration
	public function registration_post()
	{
		$postData = $this->post();
		if ($postData["name"] != '' && $postData["mobile"] != '' && $postData["password"] != '' && $postData["cityCode"] != "") {
			$email = filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL);
			$com_code = md5(uniqid(rand()));
			$cart_code = md5(uniqid(rand()));
			$forgot = md5(uniqid(rand()));
			if ($postData["emailId"] != '') {
				$checkEmailData = array("emailId" => $email, 'isDelete!=' => 1);

				if (!$this->GlobalModel->checkExistAndInsertRecords($checkEmailData, 'clientmaster')) {
					return $this->response(array("status" => "406", "message" => "E-Mail already exist.Please Signin"), 200);
					exit();
				}
			}

			$checkMobileData = array("mobile" => $postData["mobile"], 'isDelete!=' => 1);

			if (!$this->GlobalModel->checkExistAndInsertRecords($checkMobileData, 'clientmaster')) {
				return $this->response(array("status" => "406", "message" => "Mobile Number already exist.Please Signin"), 200);
				exit();
			}

			$insertArr = array(
				"name" => $postData["name"],
				"emailId" => $email,
				"mobile" => $postData["mobile"],
				"password" =>  md5($postData["password"]),
				'cityCode' => $postData['cityCode'],
				"comCode" => $com_code,
				"cartCode" => $cart_code,
				"forgot" => $forgot,
				"isActive" => 1
			);

			$insertResult = $this->GlobalModel->addNew($insertArr, 'clientmaster', 'CLNT');

			if ($insertResult != 'false') {
				if ((isset($postData['os_version'])) && ($postData['os_version'] != "")) {
					$dataDevice['os_version'] = $postData['os_version'];
					$dataDevice['app_version'] = $postData['app_version'];
					$dataDevice['mobile_model'] = $postData['mobile_model'];
					$dataDevice['clientCode'] = $insertResult;
					$dataDevice['firebaseID'] = $postData['firebaseId'];
					$dataDevice['addID'] = $insertResult;
					$dataDevice['addIP'] = $_SERVER['REMOTE_ADDR'];
					$addDevideDetails = $this->GlobalModel->addNew($dataDevice, 'clientdevicedetails', 'CDD');
				}

				$data = array(
					"clientCode" => $insertResult,
					"isActive" => 1
				);

				$profileResult = $this->GlobalModel->addWithoutCode($data, 'clientprofile');
				if ($profileResult != 'false') {
					$condition = array(
						"clientmaster.code" => $insertResult
					);

					$resultData = $this->ApiModel->read_user_information($condition);
					$result['userData'] = $resultData[0];
					return $this->response(array("status" => "200", "result" => $result, "message" => "Registration Successfully.."), 200);
				} else {
					$this->response(array("status" => "400", "message" => "Registration Successfully.. Please Signin.."), 400);
				}
			} else {
				$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} // End registration

	//login
	public function loginProcess_post()
	{
		$postData = $this->post();

		if ($postData["type"] != '' && $postData["userId"] != '' && $postData["userPassword"] != '') {

			if ($postData["type"] == 'mobile') {
				$loginData = array(
					"mobile" => $postData["userId"],
					"isActive" => 1,
					"password" => md5($postData["userPassword"])
				);
				if ($this->ApiModel->login($loginData)) {
					$data = array(
						"clientmaster.mobile" => $postData["userId"],
						"clientmaster.isDelete!=" => 1,
					);
					$resultData = $this->ApiModel->read_user_information($data);
					$result['userData'] = $resultData[0];
					return $this->response(array("status" => "200", "message" => "Login Successfully...", "result" => $result), 200);
				} else {
					return $this->response(array("status" => "400", "message" => "incorrect Mobile or Password"), 200);
				}
			} else {
				$email = filter_var($postData["userId"], FILTER_SANITIZE_EMAIL);
				$clientData = $this->GlobalModel->selectDataByField('emailId,code', $email, 'clientmaster')->result_array();

				if (sizeof($clientData) > 0) {
					$loginData = array(
						"emailId" => $email,
						"isActive" => 1,
						"password" => md5($postData["userPassword"])
					);
					if ($this->ApiModel->login($loginData)) {
						$data = array(
							"clientmaster.emailId" => $email
						);
						$resultData = $this->ApiModel->read_user_information($data);
						$result['userData'] = $resultData[0];


						if ((isset($postData['os_version'])) && ($postData['os_version'] != "")) {

							$code = $resultData[0]->code;

							$dataDevice['clientCode'] = $code;
							$dataDevice['os_version'] = $postData['os_version'];
							$dataDevice['app_version'] = $postData['app_version'];
							$dataDevice['mobile_model'] = $postData['mobile_model'];
							$dataDevice['firebaseID'] = $postData['firebaseId'];

							$dvCondition['clientdevicedetails.clientCode'] = $code;
							$dvCondition['clientdevicedetails.mobile_model'] = $postData['mobile_model'];
							$dvCondition['clientdevicedetails.app_version'] = $postData['app_version'];
							$devExits = $this->GlobalModel->selectQuery("clientdevicedetails.code", "clientdevicedetails", $dvCondition);
							if ($devExits) {
								$ccode = $devExits->result()[0]->code;
								$dataDevice['editID'] = $code;
								$dataDevice['editIP'] = $_SERVER['REMOTE_ADDR'];
								$dataDevice['editDate'] = date('Y-m-d H:i:s');
								$addDevideDetails = $this->GlobalModel->doEdit($dataDevice, 'clientdevicedetails', $ccode);
							} else {
								$dataDevice['addID'] = $code;
								$dataDevice['addIP'] = $_SERVER['REMOTE_ADDR'];
								$addDevideDetails = $this->GlobalModel->addNew($dataDevice, 'clientdevicedetails', 'CDD');
							}
						}
						return $this->response(array("status" => "200", "message" => "Login Successfully...", "result" => $result), 200);
					} else {
						return $this->response(array("status" => "400", "message" => "incorrect Email or Password"), 200);
					}
				} else {
					return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
				}
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
		}
	} //end login  Process

	//get Custom added address and area list
	public function getCustomAddressList_post()
	{
		$postData = $this->post();
		if ($postData['cityCode'] != "") {
			$conditionColumns = array('isActive', 'cityCode');
			$conditionValues = array(1, $postData['cityCode']);
			$res = $this->GlobalModel->selectActiveDataByMultipleFields($conditionColumns, $conditionValues, 'customaddressmaster');
			if ($res) {
				$addressList = [];
				foreach ($res->result() as $row) {
					$data = array(
						'addressCode' => $row->code,
						'place' => $row->place,
						'district' => $row->district,
						'taluka' => $row->taluka,
						'pincode' => $row->pincode,
						'state' => $row->state,
					);

					array_push($addressList, $data);
				}
				$result['addressList'] = $addressList;
				return $this->response(array("status" => "200", "message" => " Address List where Services Available", "result" => $result), 200);
			} else {
				$this->response(array("status" => "400", "message" => " * are Required field(s)."), 400);
			}
		} else {
			return $this->response(array("status" => "200", "message" => " Address List where Services Available", "result" => $result), 200);
		}
	}

	//home slider
	public function homeSliderImages_get()
	// public function homeSliderImages_post()
	{
		// $postData = $this->post();
		// if ($postData["mainCategoryCode"] != '')
		// {
		// $mainCategoryCode = $postData['mainCategoryCode'];

		$columns = array("homeslider.*");
		// $cond=array('homeslider' . ".isActive" => 1,"homeslider.mainCategoryCode" => $mainCategoryCode);
		$cond = array('homeslider' . ".isActive" => 1);
		$orderBy = array('homeslider' . ".id" => 'ASC');
		$images_result = $this->GlobalModel->selectQuery($columns, 'homeslider', $cond, $orderBy);
		if ($images_result) {
			$imageArray = array();
			$images_result = $images_result->result_array();
			for ($img = 0; $img < sizeof($images_result); $img++) {
				if (file_exists('uploads/homeslider/' . $images_result[$img]['imagePath'])) {
					$imgData['imagePath'] = sliderBasePath . 'uploads/homeslider/' . $images_result[$img]['imagePath'];
					array_push($imageArray, $imgData);
				}
			}
			$data['homesliderImages'] = $imageArray;
			return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $data), 200);
		} else {
			$data['homesliderImages'] = array();
			// return $this->response(array("status" => "200", "message" => "Data not found.",'result'=>$data), 200);
			return $this->response(array("status" => "300", "message" => "Data not found."), 200);
		}
		// }
		// else
		// {
		// $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		// }
	}
	//home slider

	//get slider images
	public function sliderImages_post()
	{
		$postData = $this->post();
		if ($postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData['mainCategoryCode'];

			$columns = array("productslider.*");
			$cond = array('productslider' . ".isActive" => 1, 'productslider.productCode' => $postData['cityCode'], 'productslider.mainCategoryCode' => $mainCategoryCode);
			$orderBy = array('productslider' . ".id" => 'ASC');
			$images_result = $this->GlobalModel->selectQuery($columns, 'productslider', $cond, $orderBy);
			if ($images_result) {
				$imageArray = array();
				$images_result = $images_result->result_array();
				for ($img = 0; $img < sizeof($images_result); $img++) {
					if (file_exists('uploads/slider/' . $images_result[$img]['imagePath'])) {
						$imgData['imagePath'] = sliderBasePath . 'uploads/slider/' . $images_result[$img]['imagePath'];
						$imgData['type'] = $images_result[$img]['type'];
						array_push($imageArray, $imgData);
					}
				}
				$data['sliderImages'] = $imageArray;
				return $this->response(array("status" => "200", "message" => 'Data Found', "result" => $data), 200);
			} else {
				$data['sliderImages'] = array();
				return $this->response(array("status" => "200", "message" => "Data not found.", 'result' => $data), 200);
			}
		} else {
			$data['sliderImages'] = array();
			return $this->response(array("status" => "200", "message" => "Data not found.", 'result' => $data), 200);
		}
	}
	//end slider images

	// Get Category list
	public function categoryList_post()
	{
		$postData = $this->post();
		// if ($postData["userId"] != '' && $postData["offset"] !='')
		if ($postData["userId"] != '' && $postData["offset"] != '' && $postData["mainCategoryCode"] != '') {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$category_offset = $postData["offset"];
			$category_limit = 10;
			$condition = array('isActive' => 1, 'mainCategoryCode' => $mainCategoryCode);
			$totalRecords = sizeof($this->ApiModel->selectData('categorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster', $category_limit, $category_offset, $condition)->result_array();
			if ($category_result) {
				for ($i = 0; $i < sizeof($category_result); $i++) {
					$category_result[$i]['categoryImage'] = base_url() . 'uploads/category/' . $category_result[$i]['code'] . '/' . $category_result[$i]['categoryImage'];
				}
				$data['categories'] = $category_result;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends Get Category list

	//category list by limit
	public function categoryAndProduct_post()
	{
		$postData = $this->post();

		if ($postData["userId"] != '' && $postData["offset"] != '' && $postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$category_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$category_limit = "";
			$condition = array('isActive' => 1, 'mainCategoryCode' => $mainCategoryCode);
			$totalRecords = sizeof($this->ApiModel->selectData('categorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster', $category_limit, $category_offset, $condition)->result_array();
			if ($category_result) {
				$category_resulta = [];
				for ($i = 0; $i < sizeof($category_result); $i++) {

					$category_resulta[$i]['id'] = $category_result[$i]['id'];
					$category_resulta[$i]['code'] = $category_result[$i]['code'];
					$category_resulta[$i]['categoryName'] = $category_result[$i]['categoryName'];
					$category_resulta[$i]['categorySName'] = $category_result[$i]['categorySName'];
					$category_resulta[$i]['isActive'] = $category_result[$i]['isActive'];
					$category_resulta[$i]['categoryImage'] = base_url() . 'uploads/category/' . $category_result[$i]['code'] . '/' . $category_result[$i]['categoryImage'];

					$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
					$cond = array('productmaster' . ".productCategory" => $category_result[$i]['categorySName'], 'productmaster' . ".isActive" => 1, 'productratelineentries.cityCode' => $cityCode);
					$orderBy = array('productmaster' . ".id" => 'ASC');
					$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
					$joinType = array('productratelineentries' => 'inner');
					$like = array();
					$p_result = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, $like, $category_limit, $category_offset);
					if ($p_result) {
						$product_result = $p_result->result_array();
						for ($j = 0; $j < sizeof($product_result); $j++) {
							$condition2 = array('productCode' => $product_result[$j]['code']);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$imageArray = array();
							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $product_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
							}
							if ($product_result[$j]['productUom'] == 'PC' && $product_result[$j]['minimumSellingQuantity'] > 1) {
								$product_result[$j]['productUom'] = 'PCS';
							}
							$product_result[$j]['images'] = $imageArray;
							unset($imageArray);
						}
						$proData['products'] = $product_result;
						$category_resulta[$i]['productList'] = $proData;
					}
				}
				$data['categories'] = $category_resulta;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				$data['categories'] = array();
				return $this->response(array("status" => "400", "message" => "Data not found.", "result" => $data), 400);
			}
		} else {
			$data['categories'] = array();
			$this->response(array("status" => "200", "message" => "Required field(s).", "result" => $data), 200);
		}
	}
	//end category list 

	//get popular products list
	// public function getPopularProductsList_get()
	public function getPopularProductsList_post()
	{
		$postData = $this->post();
		if ($postData['mainCategoryCode'] != '' && $postData['cityCode'] != "") {
			$mainCategoryCode = $postData['mainCategoryCode'];
			$condition1 = array("productmaster.isPopular" => 1, "productmaster.isActive" => 1, 'productratelineentries.cityCode' => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode);
			$join1 = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
			$joinType1 = array('productratelineentries' => 'inner');
			$totalProducta = $this->GlobalModel->selectQuery("productmaster.*", 'productmaster', $condition1, array(), $join1, $joinType1);
			if ($totalProducta) {
				$totalProduct = sizeof($totalProducta->result());
				//$totalProduct = 0;
				if ($totalProduct > 0) {
					$limit = 4;
					$data = array();
					$temp = 0;
					for ($i = 0; $i < $totalProduct; $i += 4) {
						$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
						$cond = array("productmaster.isPopular" => 1, 'productratelineentries.cityCode' => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode); //only kolhpaur data
						$orderBy = array('productmaster' . ".id" => 'ASC');
						$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
						$joinType = array('productratelineentries' => 'inner');
						$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, array(), $limit, $i);
						//$q = $this->db->last_query();echo $q.'<br/>';
						if ($resultQuery) {
							$limitProduct_result = $resultQuery->result_array();
							for ($j = 0; $j < sizeof($limitProduct_result); $j++) {
								$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
								$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();

								$imageArray = array();

								for ($img = 0; $img < sizeof($images_result); $img++) {
									array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
								}
								if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
									$limitProduct_result[$j]['productUom'] = 'PCS';
								}
								$limitProduct_result[$j]['images'] = $imageArray;
								unset($imageArray);
							}
							$data[]['products'] = $limitProduct_result;
						}
					}
					$response['list'] = $data;
					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $response), 200);
				} else {
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 200);
		}
	}
	//end get popular products list

	// Get Product list by categorySName 
	public function productByCategory_post()
	{
		$postData = $this->post();

		// if ($postData["categorySName"] != '' && $postData["offset"] !='' && $postData['cityCode']!="" )
		if ($postData["categorySName"] != '' && $postData["offset"] != '' && $postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$categorySName = $postData["categorySName"];
			$product_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$product_limit = 10;

			$condition2 = array('productCategory' => $categorySName, 'isActive' => 1, 'mainCategoryCode' => $mainCategoryCode);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster', '', '', $condition2)->result());
			// ,'categorymaster.mainCategoryCode'=>$mainCategoryCode 
			if ($totalProduct) {

				$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
				$cond = array('productmaster' . ".productCategory" => $categorySName, 'productmaster' . '.isActive' => 1, 'productratelineentries.cityCode' => $cityCode);
				$orderBy = array('productmaster' . ".id" => 'ASC');
				$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
				$joinType = array('productratelineentries' => 'inner');
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, array(), $product_limit, $product_offset);

				if ($resultQuery) {
					$limitProduct_result = $resultQuery->result_array();
					for ($j = 0; $j < sizeof($limitProduct_result); $j++) {

						$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();

						$imageArray = array();

						for ($img = 0; $img < sizeof($images_result); $img++) {
							array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
						}
						if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
							$limitProduct_result[$j]['productUom'] = 'PCS';
						}
						$limitProduct_result[$j]['images'] = $imageArray;
						unset($imageArray);
					}

					$data['products'] = $limitProduct_result;

					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $data), 200);
				} else {
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}  //Ends Product list by categorySName

	// Get Product by productCode
	public function productById_post()
	{
		$postData = $this->post();

		if ($postData["productCode"] != '' && $postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$productCode = $postData["productCode"];
			$cityCode = $postData['cityCode'];
			// $orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,categorymaster.mainCategoryCode");
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
			// $cond=array('productmaster' . ".code" => $productCode,'productratelineentries.cityCode'=>$cityCode,'categorymaster.mainCategoryCode'=>$mainCategoryCode);
			$cond = array('productmaster' . ".code" => $productCode, 'productratelineentries.cityCode' => $cityCode, 'productmaster.mainCategoryCode' => $mainCategoryCode);
			// mainCategoryCode
			$orderBy = array();
			// $join = array('productratelineentries'=>'productmaster.code=productratelineentries.productCode','categorymaster'=>'productmaster.productCategory=categorymaster.categorySName');
			$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
			$joinType = array('productratelineentries' => 'inner');

			$product_result = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType);
			// print_r($this->db->last_query());
			// exit;
			if ($product_result) {
				$product_result = $product_result->result_array();
				for ($j = 0; $j < sizeof($product_result); $j++) {

					$condition2 = array('productCode' => $product_result[$j]['code']);
					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();

					$imageArray = array();

					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $product_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
					}
					if ($product_result[$j]['productUom'] == 'PC' && $product_result[$j]['minimumSellingQuantity'] > 1) {
						$product_result[$j]['productUom'] = 'PCS';
					}

					$product_result[$j]['images'] = $imageArray;
					unset($imageArray);
				}
				$data['products'] = $product_result[0];
				return $this->response(array("status" => "200", "result" => $data), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // Ends Get Product by productCode 

	// check wishlist 
	public function checkWishList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["productCode"] != '') {
			$clientCode = $postData["clientCode"];
			$client_result = $this->GlobalModel->selectDataById($clientCode, 'clientmaster')->result_array();
			if ($client_result) {
				$clientCode = $client_result[0]['code'];

				$condition2 = array("clientCode" => $postData["clientCode"], "productCode" => $postData["productCode"], "isActive" => 1);
				$wishlist_result = $this->ApiModel->selectData('clientwishlist', '', '', $condition2)->result_array();
				if (sizeof($wishlist_result) > 0) {
					return $this->response(array("status" => "200", "message" => 'item already exist in wishlist'), 200);
				} else {
					return $this->response(array("status" => "400", "message" => 'iteam not added to wishlist yet'), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => 'user not registerd'), 400);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends check WishList

	// Add to Wish list by productCode and clientCode
	public function addToWishlist_post()
	{
		$postData = $this->post();

		if ($postData["productCode"] && $postData["clientCode"] != '') {
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];

			$clientData = $this->GlobalModel->selectDataById($clientCode, 'clientmaster')->result_array();

			if (sizeof($clientData) > 0) {

				$condition2 = array('productCode' => $productCode, 'clientCode' => $clientCode, 'isActive' => 1);
				$clientWishList = $this->ApiModel->selectData('clientwishlist', '', '', $condition2)->result_array();

				if (sizeof($clientWishList) > 0) {
					$code = $clientWishList[0]['code'];
					$this->GlobalModel->deleteForever($code, 'clientwishlist');

					return $this->response(array("status" => "200", "message" => "Product removed from your wishlist successfully."), 200);
				} else {
					$data = [
						'productCode' => $productCode,
						'clientCode' => $clientCode,
						'isActive' => 1
					];

					$code = $this->GlobalModel->addNew($data, 'clientwishlist', 'WISH');

					if ($code != 'false') {
						return $this->response(array("status" => "200", "message" => "Product added to your wishlist successfully."), 200);
					} else {
						return $this->response(array("status" => "400", "message" => "Product not added. Please try again later."), 400);
					}
				}
			} else {
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user before adding product to your wishlist."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // Ends Add to Wish list by productId and userId

	// get WishList by ClientCode
	public function getWishList_post()
	{
		$postData = $this->post();

		if ($postData["clientCode"] != '' && $postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$clientCode = $postData["clientCode"];
			$cityCode = $postData['cityCode'];
			$tableName = "clientwishlist";
			$orderColumns = array("productmaster.*, clientwishlist.productCode, productratelineentries.productStatus, productratelineentries.sellingPrice as productSellingPrice");
			$cond = array('clientwishlist' . ".clientCode" => $clientCode, 'productratelineentries.cityCode' => $cityCode, 'productmaster.mainCategoryCode' => $mainCategoryCode);
			$orderBy = array('clientwishlist' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientwishlist' . '.productCode=' . 'productmaster' . '.code', 'productratelineentries' => 'productmaster' . '.code=' . 'productratelineentries' . '.productCode');
			$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner');


			$clientWishList = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($clientWishList) {
				$clientWishList = $clientWishList->result_array();
				for ($j = 0; $j < sizeof($clientWishList); $j++) {
					$productCode = $clientWishList[$j]['productCode'];
					$condition2 = array('productCode' => $productCode);
					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();

					$imageArray = array();

					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
					}
					if ($clientWishList[$j]['productUom'] == 'PC' && $clientWishList[$j]['minimumSellingQuantity'] > 1) {
						$clientWishList[$j]['productUom'] = 'PCS';
					}

					$clientWishList[$j]['images'] = $imageArray;
					unset($imageArray);
				}

				$data['wishlist'] = $clientWishList;

				return $this->response(array("status" => "200", "totalresult" => sizeof($clientWishList), "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No Data found!"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} // End WishList by ClientCode

	// Start user profile
	public function userProfile_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$orderColumns = array("clientmaster.code,clientmaster.name,clientmaster.emailId,clientmaster.mobile,clientmaster.cityCode,clientmaster.cartCode,clientmaster.isCodEnabled,clientprofile.gender,clientprofile.address,clientprofile.latitude,clientprofile.longitude,clientprofile.areaCode,clientprofile.addressType,clientprofile.area,clientprofile.local,clientprofile.flat,clientprofile.pincode,clientprofile.state,clientprofile.landmark,IFNULL(citymaster.cityName,'-') as city");
			$cond = array('clientmaster' . ".code" => $clientCode, 'clientprofile.isActive' => 1);
			$orderBy = array('clientmaster' . ".id" => 'ASC', 'clientprofile.id' => "DESC");
			$join = array('citymaster' => 'clientmaster.cityCode=citymaster.code', 'clientprofile' => 'clientmaster' . '.code=' . 'clientprofile' . '.clientCode');
			$joinType = array('citymaster' => 'left', 'clientprofile' => 'inner');
			$resultData = $this->GlobalModel->selectQuery($orderColumns, 'clientmaster', $cond, $orderBy, $join, $joinType);

			if ($resultData) {
				$result['userProfile'] = $resultData->result_array()[0];
				return $this->response(array("status" => "200", "result" => $result, "q" => $this->db->last_query()), 200);
			} else {

				return $this->response(array("status" => "300", "msg" => "No Data Found!"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	// End user profile

	// Start update profile
	public function updateProfile_post()
	{
		$postData = $this->post();

		if ($postData["clientCode"] != '' && $postData["name"] != '' && $postData["mobile"] != '') //&& $postData["gender"] != ''
		{
			if ($postData["emailId"]) {
				$e_id = filter_var($postData["emailId"], FILTER_SANITIZE_EMAIL);
			} else {
				$e_id = "";
			}

			$dataMaster = [
				"code" => $postData["clientCode"],
				"name" => $postData["name"],
				"mobile" => $postData["mobile"]
			];

			if ($e_id != "") {
				$dataMaster["emailId"] = $e_id;
			}

			// $dataProfile=[
			// "gender" => $postData["gender"]
			// ]; 

			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();

			if (sizeof($resultData) == 1) {
				$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'clientmaster', $postData["clientCode"]);

				if ($resultMaster != false) {
					// $resultProfile = $this->GlobalModel->doEditWithField($dataProfile,'clientprofile','clientCode',$postData["clientCode"]);

					// if($resultProfile != false)
					// {
					return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
					// }
					// else
					// {
					// return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
					// }
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update profile

	//updateuseraddress
	public function updateProfileAddress_post()
	{
		$postData = $this->post();

		if ($postData["clientCode"] != '' && $postData["city"] != '' && $postData["state"] != '' && $postData["area"] != '' && $postData["local"] != '' && $postData["flat"] != '' && $postData["pincode"] != ''  && $postData["areaCode"] != '') {

			$dataMaster = [
				"code" => $postData["clientCode"],
			];

			$dataProfile = [
				"city" => $postData["city"],
				"local" => $postData["local"],
				"area" => $postData["area"],
				"state" => $postData["state"],
				"flat" => $postData["flat"],
				"pincode" => $postData["pincode"],
				"landMark" => $postData["landMark"],
				"areaCode" => $postData["areaCode"],
			];
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();
			if (sizeof($resultData) == 1) {
				$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'clientmaster', $postData["clientCode"]);
				if ($resultMaster != false) {
					$resultProfile = $this->GlobalModel->doEditWithField($dataProfile, 'clientprofile', 'clientCode', $postData["clientCode"]);

					if ($resultProfile != false) {
						return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
					} else {
						return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
					}
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your profile."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end update user address

	//user lat long update profile
	public function updateUserLatlong_post_old()
	{
		$postData = $this->post();

		if ($postData["clientCode"] != '' && $postData["latitude"] != '' && $postData["longitude"] != '') {
			$dataProfile = array(
				"latitude" => $postData["latitude"],
				"longitude" => $postData["longitude"]
			);

			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();

			if (sizeof($resultData) == 1) {
				$resultProfile = $this->GlobalModel->doEditWithField($dataProfile, 'clientprofile', 'clientCode', $postData["clientCode"]);

				if ($resultProfile != false) {
					return $this->response(array("status" => "200", "message" => "Your Location has been updated successfully."), 200);
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your Location."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	//Add to cart by productCode and clientCode
	public function addToCart_post()
	{
		$postData = $this->post();

		if ($postData["productCode"] && $postData["clientCode"] != '' && $postData["quantity"] != '') {
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			$quantity = $postData["quantity"];

			$clientData = $this->GlobalModel->selectDataById($clientCode, 'clientmaster')->result_array();

			if (sizeof($clientData) > 0) {
				$condition2 = array('productCode' => $productCode, 'clientCode' => $clientCode, 'isActive' => 1);
				$clientCart = $this->ApiModel->selectData('clientcarts', '', '', $condition2)->result_array();

				if (sizeof($clientCart) > 0) {
					return $this->response(array("status" => "inCart", "message" => "Product already present in your cart."), 200);
				} else {
					$data = [
						'clientCode' => $clientCode,
						'productCode' => $productCode,
						'quantity' => $quantity,
						'isActive' => 1
					];

					$code = $this->GlobalModel->addNew($data, 'clientcarts', 'CART');

					if ($code != 'false') {
						return $this->response(array("status" => "200", "message" => "Product added to your cart successfully."), 200);
					} else {
						return $this->response(array("status" => "fail", "message" => "Failed to add product to your cart . Please try again later."), 200);
					}
				}
			} else {
				return $this->response(array("status" => "fail", "message" => "User not registered. Please register user before adding product to your cart."), 200);
			}
		} else {
			return $this->response(array("status" => "fail", "message" => "Required fields not found."), 400);
		}
	}  //Ends Add to cart by productId and clientId

	// start get CartList by ClientCode
	public function getCartList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData['cityCode'] != "") {
			$cityCode = $postData['cityCode'];
			$clientCode = $postData["clientCode"];
			$tableName = "clientcarts";
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.code as cartCode");
			$cond = array('clientcarts' . ".clientCode" => $clientCode, "productratelineentries.cityCode" => $postData['cityCode'], "productmaster.isActive" => 1);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts.productCode=productmaster.code', "productratelineentries" => 'productmaster.code=productratelineentries.productCode');
			$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner');
			$res = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($res) {
				$clientCartList = $res->result_array();
				if ($postData["count"] != '') return $this->response(array("status" => "200", "totalRecords" => sizeof($clientCartList)), 200);
				for ($j = 0; $j < sizeof($clientCartList); $j++) {
					$productCode = $clientCartList[$j]['code'];
					$condition2 = array('productCode' => $productCode);
					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
					$imageArray = array();
					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
					}
					$clientCartList[$j]['images'] = $imageArray;
					unset($imageArray);
				}
				$data['products'] = $clientCartList;
				//$chargesResult = $this->ApiModel->selectData('deliverycharge',1,0)->result_array();
				$chargesResult = $this->GlobalModel->selectQuery('citymaster.minOrder,citymaster.deliveryCharge', "citymaster", array("citymaster.code" => $cityCode));
				if ($chargesResult) {
					$minOrder = $chargesResult->result_array()[0]['minOrder'];
					$deliveryCharge = $chargesResult->result_array()[0]['deliveryCharge'];
				} else {
					$minOrder = 1000;
					$deliveryCharge = 15;
				}
				return $this->response(array("status" => "200", "totalRecords" => sizeof($clientCartList), "minimumOrder" => $minOrder, "deliveryCharge" => $deliveryCharge, "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "totalRecords" => 0, "message" => "no records found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	// End CartList by ClientCode

	//Start Update Cart
	public function updateCart_post()
	{
		$postData = $this->post();

		if ($postData["cartCode"] != '' && $postData["quantity"] != '') {

			if ($postData["quantity"] == 0) {
				$result = $this->GlobalModel->deleteForever($postData["cartCode"], 'clientcarts');
				if ($result != 'false') {
					return $this->response(array("status" => "deletetrue", "message" => "delete successfully"), 200);
				} else {
					return $this->response(array("status" => "deletefalse", "message" => "delete failed"), 200);
				}
			}


			$data = array(
				"quantity" => $postData["quantity"]
			);

			$result = $this->GlobalModel->doEdit($data, 'clientcarts', $postData["cartCode"]);

			if ($result != 'false') {
				return $this->response(array("status" => "200", "message" => "cart updated."), 200);
			} else {
				return $this->response(array("status" => "400", "message" => " Failed to update."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	//update Cart

	// Start update password
	public function updatePassword_post()
	{
		$postData = $this->post();

		$clientCode = "";
		$oldPassword = "";
		$dbPassword = "";
		$newPassword = "";
		$cfmPassword = "";

		if ($postData["clientCode"] != '' && $postData["oldPassword"] != '' && $postData["newPassword"] != '') {
			$oldPassword = md5($postData["oldPassword"]);

			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();

			for ($j = 0; $j < sizeof($resultData); $j++) {
				$dbPassword = $resultData[$j]['password'];
			}
			if ($dbPassword == $oldPassword) {
				$passData = [
					"password" => md5($postData["newPassword"])
				];

				$passresult = $this->GlobalModel->doEdit($passData, 'clientmaster', $postData["clientCode"]);

				if ($passresult != false) {
					return $this->response(array("status" => "200", "message" => "Your password has been updated successfully."), 200);
				} else {
					return $this->response(array("status" => "400", "message" => " Failed to update your password."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "You entered wrong current password."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update password

	// start statelist
	public function getStateList_get()
	{
		$columnName = "state";
		$stateresult = $this->GlobalModel->selectDistinctData($columnName, 'addressmaster')->result();

		if ($stateresult) {
			return $this->response(array("status" => "200", "result" => $stateresult), 200);
		} else {
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // End statelist

	// start statelist
	public function getAreaList_get()
	{
		$tableName = "addressmaster";
		$orderColumns = array("addressmaster.place,addressmaster.pincode");
		$cond = array('addressmaster' . ".district" => 'Kolhapur', 'addressmaster' . ".state" => 'Maharashtra');
		$orderBy = array('addressmaster' . ".place" => 'ASC');
		$stateresult = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy)->result();
		if ($stateresult) {
			$result["areaList"] = $stateresult;
			return $this->response(array("status" => "200", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	}
	// End statelist

	// start  termslist
	public function getTermsList_get()
	{
		$termsResult = $this->GlobalModel->selectDataExcludeDelete("policy")->result_array();
		if ($termsResult) {
			$result['terms'] = $termsResult[0]['terms'];
			return $this->response(array("status" => "200", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // End termslist

	// start FAQ
	public function getFaq_get()
	{
		$faqResult = $this->GlobalModel->selectDataExcludeDelete("faq")->result_array();
		if ($faqResult) {
			$result['faq'] = $faqResult[0]['description'];
			return $this->response(array("status" => "200", "result" => $result), 200);
		} else {
			$this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // End  FAQ

	public function placeOrder_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["paymentMode"] != '' && $postData["areaCode"] != '' && $postData['cityCode'] != "") {
			$clientCode = $postData["clientCode"];
			$timeStamp = date("Y-m-d h:i:s");
			$totalamount = 0;
			$tableName = "clientcarts";
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.code as cartCode");
			$cond = array('clientcarts' . ".clientCode" => $clientCode, 'productratelineentries.cityCode' => $postData['cityCode'], "productmaster.isActive" => 1);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts.productCode=productmaster.code', "productratelineentries" => 'productmaster.code=productratelineentries.productCode');
			$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner');
			$clientCartList = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType)->result_array();
			if (sizeof($clientCartList) > 0) {
				$insertArr = array(
					"clientCode" => $postData["clientCode"],
					"cityCode" => $postData['cityCode'],
					"paymentref" => $postData["transactionId"],
					"paymentMode" => $postData["paymentMode"],
					"paymentStatus" => "PNDG",
					"shippingCharges" => $postData["shippingCharges"],
					"address" => $postData["address"],
					"areaCode" => $postData["areaCode"],
					"latitude" => $postData["latitude"],
					"longitude" => $postData["longitude"],
					"phone" => $postData["phone"],
					"orderStatus" => "PND",
					"isActive" => 1,
					'editDate' => $timeStamp
				);
				$totalamount = $postData["shippingCharges"];


				$orderCode = 'ORDER' . rand(99, 99999);
				$insertResult = $this->GlobalModel->addWithoutYear($insertArr, 'ordermaster', $orderCode);
				if ($insertResult != 'false') {
					for ($i = 0; $i < sizeof($clientCartList); $i++) {
						$amount = ($clientCartList[$i]["productSellingPrice"] * $clientCartList[$i]["quantity"]);
						$totalamount += $amount;
						$data = array(
							"orderCode" => $insertResult,
							"productCode" => $clientCartList[$i]["code"],
							"weight" => $clientCartList[$i]["minimumSellingQuantity"],
							"productUom" => $clientCartList[$i]["productUom"],
							"productPrice" => $clientCartList[$i]["productSellingPrice"],
							"quantity" => $clientCartList[$i]["quantity"],
							"totalPrice" => ($clientCartList[$i]["productSellingPrice"] * $clientCartList[$i]["quantity"]),
							"isActive" => 1
						);

						$orderLineResult = $this->GlobalModel->addWithoutCode($data, 'orderlineentries');
						if ($orderLineResult != 'false') {
							//stock update
							// $totalQty=($clientCartList[$i]["minimumSellingQuantity"] * $clientCartList[$i]["quantity"]);
							// $updatedStock=$clientCartList[$i]["stock"] - $totalQty;
							// $updateStockData=array(
							// "stock" => $updatedStock
							// );
							// $this->GlobalModel->doEditWithField($updateStockData, "stockinfo", "productCode",$clientCartList[$i]["code"]);
							$this->GlobalModel->deleteForeverFromField("code", $clientCartList[$i]["cartCode"], "clientcarts");
						}
					}
					$updatePriceData = array('totalPrice' => $totalamount);
					$resultArea = $this->GlobalModel->doEdit($updatePriceData, "ordermaster", $insertResult);

					//end notification
					return $this->response(array("status" => "200", "message" => "Order Placed Successfully.. Your OrderID is " . $insertResult), 200);
				} else {
					$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
				}
			} else {
				$this->response(array("status" => "400", "message" => 'cart is empty'), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	// start get OrderList by ClientCode
	public function getOrderList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '') {

			$clientCode = $postData["clientCode"];
			$tableName = "ordermaster";
			$orderColumns = array("ordermaster.code as orderCode,ordermaster.shippingCharges as deliveryCharges,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode");
			$cond = array('ordermaster' . ".clientCode" => $clientCode);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$cityCode = $clientOrderList[$i]['cityCode'];
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.productName");
					$linecond = array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries.productCode=productmaster.code');
					$linejoinType = array('productmaster' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
						for ($j = 0; $j < sizeof($orderProductList); $j++) {
							$condition2 = array('productCode' => $orderProductList[$j]["productCode"]);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$productCode = $orderProductList[$j]["productCode"];
							$imageArray = array();
							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
							}
							$orderProductList[$j]['images'] = $imageArray;
							unset($imageArray);
						}
						$clientOrderList[$i]['orderedProduct'] = $orderProductList;
						$dFormat = DateTime::createFromFormat('Y-m-d H:i:s', $clientOrderList[$i]['orderDate']);
						$oDt = $dFormat->format('d-m-Y H:i:s');
						$clientOrderList[$i]['orderDate'] = $oDt;
					}
				}
				$finalResult['orders'] = $clientOrderList;
				return $this->response(array("status" => "200", "totalOrders" => $totalOrders, "result" => $finalResult), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	// End OrdersList by ClientCode

	//reset password
	public function resetpassword_post()
	{
		$today = date('Y-m-d') . ' ' . date("h:i:s");
		$postData = $this->post();
		if ($postData["username"] != '' && $postData['type'] != "") {
			$tableName = "clientmaster";
			$orderColumns = array("clientmaster.*");
			if ($postData["type"] == 'mobile') {
				$cond = array("clientmaster.mobile" => trim($postData["username"]), "clientmaster.isActive" => 1);
			} else {
				$cond = array("clientmaster.emailId" => trim($postData["username"]), 'clientmaster.isActive' => 1);
			}
			$userData = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond);
			if ($userData) {
				$dbResult = $userData->result_array()[0];

				$email = $dbResult['emailId'];

				$code = $dbResult['code'];

				$data['fullName'] =  $dbResult['name'];

				$fullName = $dbResult['name'];

				$token = $this->passwordlib->base64url_encode($email . '/' . $code);

				$subData = array('resetToken' => $token);

				$updateData = $this->GlobalModel->doEdit($subData, 'usermaster', $code);

				$data['sendLink'] = base_url() . 'Resetpassword/cuRestPassword/' . $token;

				$sendLink = base_url() . 'Resetpassword/cuRestPassword/' . $token;

				$to = $email;

				$subject = 'Reset Your Password';

				$message = '<html><body><p>Hi...' . $fullName . ',</p>
					<p>We have received a request to reset password. If you did not make the request just ignore this email. Otherwise, you can reset your password using this link.</p>
					<p><a  href="' . $sendLink . '" target="_blank" style="margin-bottom:8px;background:green; width:50%; padding: 8px 12px; border: 1px solid green;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block; text-align:center;">RESET PASSWORD</a></p></body></html>';

				$result = $this->sendemail->sendMailOnly($to, $subject, $message);
				if ($result) {
					return $this->response(array("status" => "200", "message" => "Reset email send Successfully"), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Problem During Sending Mail"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => " No such user found! Please  provide correct email or mobile number"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * Fields are mandatory!"), 200);
		}
	}

	// Start update profile
	public function updateFirebaseId_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["firebaseId"] != '') {
			$clientCode = $postData["clientCode"];
			if ((isset($postData['os_version'])) && $postData['os_version'] != "") {
				$condition['clientdevicedetails.clientCode'] = $clientCode;
				$condition['clientdevicedetails.mobile_model'] = $postData['mobile_model'];
				$condition['clientdevicedetails.app_version'] = $postData['app_version'];
				$device = $this->GlobalModel->selectQuery("clientdevicedetails.code", "clientdevicedetails", $condition);
				if ($device) {
					$deviceCode = $device->result_array()[0]['code'];
					$dataDevice['firebaseID'] = $postData['firebaseId'];
					$dataDevice['editID'] = $clientCode;
					$dataDevice['editIP'] = $_SERVER['REMOTE_ADDR'];
					$result = $this->GlobalModel->doEdit($dataDevice, 'clientdevicedetails', $deviceCode);
					if ($result != false) {
						return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
					} else {
						return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
					}
				} else {
					$dataDevice['os_version'] = $postData['os_version'];
					$dataDevice['app_version'] = $postData['app_version'];
					$dataDevice['mobile_model'] = $postData['mobile_model'];
					$dataDevice['clientCode'] = $clientCode;
					$dataDevice['firebaseID'] = $postData['firebaseId'];
					$dataDevice['addID'] = $clientCode;
					$dataDevice['addIP'] = $_SERVER['REMOTE_ADDR'];
					$result = $this->GlobalModel->addNew($dataDevice, 'clientdevicedetails', 'CDD');
					if ($result) {
						return $this->response(array("status" => "200", "message" => "Firebase Id Update Successfully"), 200);
					} else {
						return $this->response(array("status" => "300", "message" => " Failed to update Firebase Id."), 200);
					}
				}
			} else {
				return $this->response(array("status" => "300", "message" => " Failed to find device details!"), 200);
			}
		} else {
			$this->response(array("status" => "300", "message" => " * are required field(s)."), 200);
		}
	}
	// End update firebaseId

	// Get Product list by keyword 
	public function searchProductByKeyword_post()
	{
		$postData = $this->post();
		if ($postData["keyword"] != '' && $postData["offset"] != '' && $postData['cityCode'] != "" && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$keyword = $postData["keyword"];
			$product_offset = $postData["offset"];
			$product_limit = 10;
			$condition2 = array('isActive' => 1);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster', '', '', $condition2)->result());
			if ($totalProduct) {
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive");
				$cond = array('productmaster' . '.isActive' => 1, "productratelineentries.cityCode" => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode);
				$orderBy = array('productmaster' . ".id" => 'ASC');
				$join = array("productratelineentries" => 'productmaster.code=productratelineentries.productCode');
				$joinType = array('productratelineentries' => 'inner');
				$like = array('productmaster.productName' => $keyword . '~both');
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, $like, $product_limit, $product_offset);

				if ($resultQuery) {
					$limitProduct_result = $resultQuery->result_array();
					for ($j = 0; $j < sizeof($limitProduct_result); $j++) {

						$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
						$imageArray = array();

						for ($img = 0; $img < sizeof($images_result); $img++) {
							array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
						}
						if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
							$limitProduct_result[$j]['productUom'] = 'PCS';
						}
						$limitProduct_result[$j]['images'] = $imageArray;
						unset($imageArray);
					}

					$data['products'] = $limitProduct_result;

					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $data), 200);
				} else {
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends Product list by keyword

	//Start cancel order
		public function cancelOrder_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData["orderCode"] != '' && $postData["orderType"] != '') {
			$userCode = $postData["clientCode"];
			$code = $postData["orderCode"];
			$orderType = $postData["orderType"];
			$nowdate = date('Y-m-d h:i:s');
			if ($orderType != "food") {
				$Result = $this->GlobalModel->selectDataByPND('code', $code, 'ordermaster', $userCode);
				if ($Result) {
					$deliveryBoyCode = $Result->result_array()[0]['deliveryBoyCode'];
					$data = array('orderStatus' => "CAN", "cancelledTime" => $nowdate);
					$passresult = $this->GlobalModel->doEditWithField($data, 'ordermaster', 'code', $code);
					if ($passresult == 'true') {
						$dataUpCnt['orderCount'] = 0;
						$dataUpCnt['orderCode'] = null;
						$dataUpCnt['orderType'] = null;
						$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
						//calcualtion and notifications
						return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
					} else {
						return $this->response(array("status" => "400", "message" => "Failed to cancel the order! Please Try Again."), 200);
					}
				}
			} else {
				$Result2 = $this->GlobalModel->selectDataByIdWithEmpty($code, 'vendorordermaster');
				
				if ($Result2) {
					//check delivery boy and vendor accept this order
					$orderCode = $Result2->result_array()[0]['code'];
					$orderStatus = $Result2->result_array()[0]['orderStatus']; 
				 
					$DBFlag = 0;
					$restoFlag = 0;
					if ($orderStatus == 'PLC' || $orderStatus == 'PND') {
						$DBFlag = 1;
					} else {
						$restoFlag = 1;
						$DBFlag = 1;
					}

					$DeviceIdsArr[] = array();
					if ($DBFlag == 1) {

						$dbCode = $Result2->result_array()[0]['deliveryBoyCode'];
						if($dbCode!=""){
							$orderColumns = array("usermaster.firebase_id");
							$cond = array('usermaster' . '.isActive' => 1, "usermaster.code" => $dbCode);
							$resultDBoy = $this->GlobalModel->selectQuery($orderColumns, 'usermaster', $cond);
							 
							if ($resultDBoy) {
								//send notification
								$DeviceIdsArr[] = $resultDBoy->result()[0]->firebase_id;

								//remove delivery boy current active order
								$dataUpCnt['orderCount'] = 0;
								$dataUpCnt['orderCode'] = null;
								$dataUpCnt['orderType'] = null;
								
								$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $dbCode);
						 
							}
						}
					}
			 
					if ($restoFlag == 1) {
						$vendCode = $Result2->result_array()[0]['vendorCode'];
						$orderColumns = array("vendor.firebaseId");
						$cond = array('vendor' . '.isActive' => 1, "vendor.code" => $vendCode);
						$resultVendor = $this->GlobalModel->selectQuery($orderColumns, 'usermaster', $cond);
						if ($resultVendor) {
							//send notification
							$DeviceIdsArr[] = $resultVendor->result()[0]->firebaseId;
						}
					}

					$title = "Cancelled Order";
					$message = "Order code " . $orderCode . ' was canceled by customer..'; //$insertResult;
					$random = rand(0, 999);
					$dataArr = array();
					$dataArr['device_id'] = $DeviceIdsArr;
					$dataArr['message'] = $message; //Message which you want to send
					$dataArr['title'] = $title;
					$dataArr['order_id'] = $orderCode;
					$dataArr['random_id'] = $random;
					$dataArr['type'] = 'order';

					$notification['device_id'] = $DeviceIdsArr;
					$notification['message'] = $message; //Message which you want to send
					$notification['title'] = $title;
					$notification['order_id'] = $orderCode;
					$notification['random_id'] = $random;
					$notification['type'] = 'order';
					$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
				
					$data = array('orderStatus' => "CAN", "editDate" => $nowdate,"deliveryBoyCode"=>"", 'editID' => $userCode);
					$passresult = $this->GlobalModel->doEditWithField($data, 'vendorordermaster', 'code', $code);
					if ($passresult == 'true') {
						$dataStatusChangeLine = array(
							"orderCode" => $orderCode,
							"statusPutCode" => $userCode,
							"statusLine" => 'CAN',
							"reason" => 'Canceled By customer',
							"statusTime" => date("Y-m-d h:i:s"),
							"isActive" => 1
						);
						$bookLineResult = $this->GlobalModel->addWithoutYear($dataStatusChangeLine, 'bookorderstatuslineentries', 'BOL');

						return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Failed to cancel the order! Please Try Again." . $this->db->last_query()), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Unsuccessfull Order Cancel Please Try Again."), 200);
				}
			}
		} else {
			$this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}
	//End Cancel Order`

	public function getOffers_post()
	{
		$postData = $this->post();
		if ($postData['cityCode'] != "") {
			$tableName = "offers";
			$todayDate = date('Y-m-d');;
			$orderColumns = array("offers.*");
			$cond = array('offers' . ".startDate <=" => $todayDate, 'offers' . ".expireDate >=" => $todayDate, "offers.isActive" => 1, "offers.cityCode" => $postData['cityCode']);
			$orderBy = array('offers' . ".id" => 'DESC');
			$join = array();
			$joinType = array();
			$res = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($res) {
				$result = $res->result_array();
				for ($j = 0; $j < sizeof($result); $j++) {
					$result[$j]['expireDate'] = date('d-M-Y', strtotime($result[$j]['expireDate']));
				}
				$data["offers"] = $result;
				return $this->response(array("status" => "200", "totalRecords" => sizeof($result), "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "totalRecords" => 0, "message" => "no records found"), 200);
			}
		} else {
			return $this->response(array("status" => "200", "totalRecords" => 0, "message" => "No Offers found!"), 200);
		}
	}

	public function checkNoti_get()
	{
		$DeviceIdsArr[] = 'elDHQU1fL3g:APA91bHQlZDyTDZntJP-sJ9TdMGheOucdpeieGSJVeNhGe40ERI17t6Xc5OpY0zAA5Vy9Awp7NyRLkWXTWLLgu1EKFrpbLSHgQaFC8ZukbzNTXzX6iIdEDDrTEZ-e76HADbjRv2Hhg_F';
		$dataArr = array();
		$dataArr['device_id'] = $DeviceIdsArr;
		$dataArr['message'] = "New order "; //Message which you want to send
		$dataArr['title'] = 'New Order';
		$dataArr['order_id'] = 'order_id';
		$dataArr['random_id'] = 'random_id';
		$dataArr['image'] = 'ic_launcher_round';
		$dataArr['type'] = 'order';
		$notification['device_id'] = $DeviceIdsArr;
		$notification['message'] = "New order from "; //Message which you want to send
		$notification['title'] = 'New Order';
		$notification['order_id'] = 'order_id';
		$notification['random_id'] = 'random_id';
		$notification['image'] = 'ic_launcher_round';
		$notification['type'] = 'order';
		$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);
	}

	public function maintenance_get()
	{
		$resultData = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.settingName' => 'maintenance_mode'));
		$maintenance_mode['maintenance'] = $resultData->result_array()[0]['settingValue'];
		$maintenance_mode['messageTitle'] = $resultData->result_array()[0]['messageTitle'];
		$maintenance_mode['messageDescription'] = $resultData->result_array()[0]['messageDescription'];
		return $this->response(array("status" => "200", "result" => $maintenance_mode), 200);
	}

	// Get Product list by grocerycategorySName 
	public function groceryproductByCategory_post()
	{
		$postData = $this->post();

		if ($postData["grocerycategorySName"] != '' && $postData["offset"] != '' && $postData['cityCode'] != "") {
			$grocerycategorySName = $postData["grocerycategorySName"];
			$product_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$product_limit = 10;

			$condition2 = array('productCategory' => $grocerycategorySName, 'isActive' => 1);
			$totalProduct = sizeof($this->ApiModel->selectData('groceryproductmaster', '', '', $condition2)->result());

			if ($totalProduct) {

				$orderColumns = array("groceryproductmaster.id,groceryproductmaster.code,groceryproductmaster.productName,groceryproductmaster.productDescription,groceryproductmaster.minimumSellingQuantity,groceryproductmaster.productUom,groceryproductmaster.productRegularPrice,groceryproductratelineentries.sellingPrice as productSellingPrice,groceryproductratelineentries.productStatus,groceryproductratelineentries.cityCode,groceryproductmaster.isActive");
				$cond = array('groceryproductmaster' . ".productCategory" => $grocerycategorySName, 'groceryproductmaster' . '.isActive' => 1, 'groceryproductratelineentries.cityCode' => $cityCode);
				$orderBy = array('groceryproductmaster' . ".id" => 'ASC');
				$join = array('groceryproductratelineentries' => 'groceryproductmaster.code=groceryproductratelineentries.productCode');
				$joinType = array('groceryproductratelineentries' => 'inner');

				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'groceryproductmaster', $cond, $orderBy, $join, $joinType, array(), $product_limit, $product_offset);

				if ($resultQuery) {
					$limitProduct_result = $resultQuery->result_array();
					for ($j = 0; $j < sizeof($limitProduct_result); $j++) {

						$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('groceryproductphotos', '', '', $condition2)->result_array();

						$imageArray = array();

						for ($img = 0; $img < sizeof($images_result); $img++) {
							array_push($imageArray, base_url() . 'uploads/groceryproduct/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
						}
						if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
							$limitProduct_result[$j]['productUom'] = 'PCS';
						}
						$limitProduct_result[$j]['images'] = $imageArray;
						unset($imageArray);
					}

					$data['products'] = $limitProduct_result;

					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $data), 200);
				} else {
					return $this->response(array("status" => "400", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}  //Ends Product list by grocerycategorySName

	// Get Product by productCode
	public function groceryproductById_post()
	{
		$postData = $this->post();

		if ($postData["productCode"] != '' && $postData['cityCode'] != "") {
			$productCode = $postData["productCode"];
			$cityCode = $postData['cityCode'];
			$orderColumns = array("groceryproductmaster.id,groceryproductmaster.code,groceryproductmaster.productName,groceryproductmaster.productDescription,groceryproductmaster.minimumSellingQuantity,groceryproductmaster.productUom,groceryproductmaster.productRegularPrice,groceryproductratelineentries.sellingPrice as productSellingPrice,groceryproductratelineentries.productStatus,groceryproductratelineentries.cityCode,groceryproductmaster.isActive");
			$cond = array('groceryproductmaster' . ".code" => $productCode, 'groceryproductratelineentries.cityCode' => $cityCode);
			$orderBy = array();
			$join = array('groceryproductratelineentries' => 'groceryproductmaster.code=groceryproductratelineentries.productCode');
			$joinType = array('groceryproductratelineentries' => 'inner');

			$product_result = $this->GlobalModel->selectQuery($orderColumns, 'groceryproductmaster', $cond, $orderBy, $join, $joinType);
			if ($product_result) {
				$product_result = $product_result->result_array();
				for ($j = 0; $j < sizeof($product_result); $j++) {

					$condition2 = array('productCode' => $product_result[$j]['code']);
					$images_result = $this->ApiModel->selectData('groceryproductphotos', '', '', $condition2)->result_array();

					$imageArray = array();

					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/groceryproduct/' . $product_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
					}
					if ($product_result[$j]['productUom'] == 'PC' && $product_result[$j]['minimumSellingQuantity'] > 1) {
						$product_result[$j]['productUom'] = 'PCS';
					}

					$product_result[$j]['images'] = $imageArray;
					unset($imageArray);
				}
				$data['products'] = $product_result[0];
				return $this->response(array("status" => "200", "result" => $data), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // Ends Get Product by productCode 

	//get popular products list
	public function getPopularGroceryProductsList_get()
	{
		$getData = $this->get();
		$cityCode = 'CTY_1';
		if (isset($getData['cityCode'])) {
			if ($getData['cityCode'] != "") {
				$cityCode = $getData['cityCode'];
			}
		}
		$condition1 = array("groceryproductmaster.isPopular" => 1, 'groceryproductratelineentries.cityCode' => $cityCode, "groceryproductmaster.isActive" => 1);
		$join1 = array('groceryproductratelineentries' => 'groceryproductmaster.code=groceryproductratelineentries.productCode');
		$joinType1 = array('groceryproductratelineentries' => 'inner');
		$totalProducta = $this->GlobalModel->selectQuery("groceryproductmaster.*", 'groceryproductmaster', $condition1, array(), $join1, $joinType1);
		if ($totalProducta) {
			$totalProduct = sizeof($totalProducta->result());
			//$totalProduct = 0;
			if ($totalProduct > 0) {
				$limit = 4;
				$data = array();
				$temp = 0;
				for ($i = 0; $i < $totalProduct; $i += 4) {
					$orderColumns = array("groceryproductmaster.id,groceryproductmaster.code,groceryproductmaster.productName,groceryproductmaster.productDescription,groceryproductmaster.minimumSellingQuantity,groceryproductmaster.productUom,groceryproductmaster.productRegularPrice,groceryproductratelineentries.sellingPrice as productSellingPrice,groceryproductratelineentries.productStatus,groceryproductratelineentries.cityCode,groceryproductmaster.isActive");
					$cond = array("groceryproductmaster.isPopular" => 1, 'groceryproductratelineentries.cityCode' => 'CTY_1'); //only kolhpaur data
					$orderBy = array('groceryproductmaster' . ".id" => 'ASC');
					$join = array('groceryproductratelineentries' => 'groceryproductmaster.code=groceryproductratelineentries.productCode');
					$joinType = array('groceryproductratelineentries' => 'inner');
					$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'groceryproductmaster', $cond, $orderBy, $join, $joinType, array(), $limit, $i);
					//$q = $this->db->last_query();echo $q.'<br/>';
					if ($resultQuery) {
						$limitProduct_result = $resultQuery->result_array();
						for ($j = 0; $j < sizeof($limitProduct_result); $j++) {
							$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
							$images_result = $this->ApiModel->selectData('groceryproductphotos', '', '', $condition2)->result_array();

							$imageArray = array();

							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/groceryproduct/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
							}
							if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
								$limitProduct_result[$j]['productUom'] = 'PCS';
							}
							$limitProduct_result[$j]['images'] = $imageArray;
							unset($imageArray);
						}
						$data[]['products'] = $limitProduct_result;
					}
				}
				$response['list'] = $data;
				return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $response), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 200);
		}
	}
	//end get popular products list

	// Get Category list
	public function grocerycategoryList_post()
	{
		$postData = $this->post();
		if ($postData["userId"] != '' && $postData["offset"] != '') {
			$category_offset = $postData["offset"];
			$category_limit = 10;
			$condition = array('isActive' => 1);
			$totalRecords = sizeof($this->ApiModel->selectData('grocerycategorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('grocerycategorymaster', $category_limit, $category_offset, $condition)->result_array();
			if ($category_result) {
				for ($i = 0; $i < sizeof($category_result); $i++) {
					$category_result[$i]['categoryImage'] = base_url() . 'uploads/grocerycategory/' . $category_result[$i]['code'] . '/' . $category_result[$i]['grocerycategoryImage'];
				}
				$data['categories'] = $category_result;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				return $this->response(array("status" => "400", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends Get Category list

	public function getCityByLatLong_post()
	{
		$postData = $this->post();

		if ($postData["latitude"] != '' && $postData["longitude"] != '') {
			$loc_latitude = $postData["latitude"];
			$loc_longitude = $postData["longitude"];

			$Result = $this->GlobalModel->selectQuery('customaddressmaster.*', 'customaddressmaster', array('customaddressmaster.isActive' => 1));

			if ($Result) {
				foreach ($Result->result_array() as $key) {
					$latitude = $key['latitude'];
					$longitude = $key['longitude'];
					// $radius = $key['radius'];

					$radius = 2;
					$R = 6371;

					$maxLat = $loc_latitude + rad2deg($radius / $R);
					$minLat = $loc_latitude - rad2deg($radius / $R);
					$maxLon = $loc_longitude + rad2deg(asin($radius / $R) / cos(deg2rad($loc_latitude)));
					$minLon = $loc_longitude - rad2deg(asin($radius / $R) / cos(deg2rad($loc_latitude)));

					$res = $this->db->query("Select place,code,cityCode,isService From `customaddressmaster` Where latitude Between " . $minLat . " And " . $maxLat . " And longitude Between " . $minLon . " And " . $maxLon);
					if ($res) {
						// print_r($res->result());
						// exit;
						if (!empty($res->result_array())) {
							$areaCode = $res->result_array()[0]['code'];
							$place = $res->result_array()[0]['place'];
							$cityCode = $res->result_array()[0]['cityCode'];
							$isService = $res->result_array()[0]['isService'];
							$cityName = "";
							$Result = $this->GlobalModel->selectQuery('citymaster.cityName', 'citymaster', array('citymaster.code' => $cityCode));
							if ($Result) {
								// $data['cities']=$Result->result_array()[0]['cityName'];
								$cityName = $Result->result_array()[0]['cityName'];
							}
							$data['address'] = array('cityCode' => $cityCode, 'cityName' => $cityName, 'areaCode' => $areaCode, 'place' => $place, 'isService' => $isService);
							return $this->response(array("status" => "200", "message" => "Address Updated Successfully.", "result" => $data), 200);
						} else {
							return $this->response(array("status" => "300", "message" => "No Data Found."), 200);
						}
					} else {
						return $this->response(array("status" => "300", "message" => "No Data Found."), 200);
					}
				}
			} else {
				return $this->response(array("status" => "300", "message" => "No Data Found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	//add address line client
	// public function addClientLineAddress_post()
	public function updateUserLatlong_post()
	{
		$postData = $this->post();
		// if ($postData["clientCode"] != '' && $postData["city"] != '' && $postData["state"] != '' && $postData["area"] != '' && $postData["local"] != '' && $postData["flat"] != '' && $postData["pincode"] !=''  && $postData["areaCode"] !='')
		if ($postData["clientCode"] != '' && $postData["address"] != '' && $postData["latitude"] != '' && $postData["longitude"] != '' && $postData["addressType"] != '' && $postData["flat"] != '' && $postData["landMark"] != '' && $postData["areaCode"] != '' && $postData["cityCode"] != '') {
			$clientCode = $postData["clientCode"];
			$Result = $this->GlobalModel->selectQuery('clientmaster.*', 'clientmaster', array('clientmaster.code' => $clientCode));

			if ($Result) {
				$ResultUpdateData = $this->GlobalModel->selectQuery('clientprofile.*', 'clientprofile', array('clientprofile.clientCode' => $clientCode));
				if ($ResultUpdateData) {
					$data = array('isSelected' => 0);
					$resUpdate = $this->GlobalModel->doEditWithField($data, 'clientprofile', 'clientCode', $clientCode);
				}

				$dataProfile = [
					"clientCode" => $postData["clientCode"],
					"address" => $postData["address"],
					"latitude" => $postData["latitude"],
					"longitude" => $postData["longitude"],
					"flat" => $postData["flat"],
					"addressType" => $postData["addressType"],
					"landMark" => $postData["landMark"],
					"areaCode" => $postData["areaCode"],
					"cityCode" => $postData["cityCode"],
					"isSelected" => 1,
					"isActive" => 1,
				];

				// $insertResult=$this->GlobalModel->addNew($dataProfile, 'clientprofile', 'CTAL');
				$insertResult = $this->GlobalModel->addWithoutCode($dataProfile, 'clientprofile');
				if ($insertResult != 'false') {
					// $Result = $this->GlobalModel->selectQuery('clientaddresslineentries.*','clientaddresslineentries',array('clientaddresslineentries.code'=>$insertResult));
					$Result = $this->GlobalModel->selectQuery('clientprofile.*', 'clientprofile', array('clientprofile.clientCode' => $clientCode, 'clientprofile.isSelected' => 1));
					if ($Result) {
						$data['addressdata'] = $Result->result_array();
						return $this->response(array("status" => "200", "message" => "Location Updated Successfully.", "result" => $data), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "No data Found."), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Something Went Wrong."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end address line client

	public function getAddressesByclienCode_post()
	{
		$addressList = [];
		$postData = $this->post();
		if ($postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$join["clientmaster"] = "clientprofile.clientCode=clientmaster.code";
			$joinType["clientmaster"] = "inner";
			$Result = $this->GlobalModel->selectQuery('clientprofile.*,clientmaster.cityCode as clientCityCode', 'clientprofile', array('clientprofile.clientCode' => $clientCode, 'clientprofile.isActive' => 1), array(), $join, $joinType);
			if ($Result) {
				foreach ($Result->result() as $row) {
					$cityCode = $row->clientCityCode;
					$areaCode = $row->areaCode;
					if (($cityCode != "" || $cityCode != null) && ($areaCode != "" || $areaCode != null)) {
						$cityName = "";
						$ResultCity = $this->GlobalModel->selectQuery('citymaster.cityName', 'citymaster', array('citymaster.code' => $cityCode));
						if ($ResultCity) {
							$cityName = $ResultCity->result_array()[0]['cityName'];
						}
						$place = "";
						$ResultArea = $this->GlobalModel->selectQuery('customaddressmaster.place', 'customaddressmaster', array('customaddressmaster.code' => $areaCode));
						if ($ResultArea) {
							$place = $ResultArea->result_array()[0]['place'];
						}
						$data = array(
							'city' => $cityName,
							'area' => $place,
							'id' => $row->id,
							'address' => $row->address,
							'latitude' => $row->latitude,
							'longitude' => $row->longitude,
							'cityCode' => $row->cityCode,
							'areaCode' => $row->areaCode,
							'addressType' => $row->addressType == null ? "home" : $row->addressType,
							'flat' => $row->flat,
							'landMark' => $row->landMark,
							'isSelected' => $row->isSelected,
						);
						array_push($addressList, $data);
					}
				}
				$result['addresses'] = $addressList;
				// $data['addresses']=$Result->result_array(); 
				return $this->response(array("status" => "200", "message" => "Addresses List.", "result" => $result, "r" => $this->db->last_query()), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data Found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function updateClientAddress_post()
	{
		$postData = $this->post();
		if ($postData["id"] != '' && $postData["clientCode"] != '' && $postData["address"] != '' && $postData["latitude"] != '' && $postData["longitude"] != '' && $postData["addressType"] != '' && $postData["flat"] != '' && $postData["landMark"] != '' && $postData["areaCode"] != '' && $postData["cityCode"] != '') {
			$clientCode = $postData["clientCode"];
			$id = $postData["id"];

			$Result = $this->GlobalModel->selectQuery('clientmaster.*', 'clientmaster', array('clientmaster.code' => $clientCode));

			if ($Result) {
				$dataProfile = [
					"clientCode" => $postData["clientCode"],
					"address" => $postData["address"],
					"latitude" => $postData["latitude"],
					"longitude" => $postData["longitude"],
					"flat" => $postData["flat"],
					"addressType" => $postData["addressType"],
					"landMark" => $postData["landMark"],
					"areaCode" => $postData["areaCode"],
					"cityCode" => $postData["cityCode"],
				];

				$updateResult = $this->GlobalModel->doEditWithField($dataProfile, 'clientprofile', 'id', $id);

				if ($updateResult != 'false') {
					return $this->response(array("status" => "200", "message" => "Address Updated Successfully."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Something Went Wrong."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end address line client

	public function deleteClientAddress_post()
	{
		$postData = $this->post();
		if ($postData["id"] != '' && $postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$id = $postData["id"];

			$Result = $this->GlobalModel->selectQuery('clientmaster.*', 'clientmaster', array('clientmaster.code' => $clientCode));

			if ($Result) {
				$deleteResult = $this->GlobalModel->deleteWithField('id', $id, 'clientprofile');

				if ($deleteResult != 'false') {
					return $this->response(array("status" => "200", "message" => "Address Deleted Successfully."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Something Went Wrong."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end address line client

	public function setDefaultAddress_post()
	{
		$postData = $this->post();
		if ($postData['clientCode'] != "" && $postData['addressId'] != "") {
			$data['isSelected'] = 0;
			$data['editIP'] = $_SERVER['REMOTE_ADDR'];
			$data['editID'] = $postData['clientCode'];
			$result = $this->GlobalModel->doEditWithField($data, 'clientprofile', 'clientCode', $postData['clientCode']);

			$data1['isSelected'] = 1;
			$data1['editIP'] = $_SERVER['REMOTE_ADDR'];
			$data1['editID'] = $postData['clientCode'];
			$result = $this->GlobalModel->doEditWithField($data1, 'clientprofile', 'id', $postData['addressId']);
			if ($result == 'true') {
				return $this->response(array("status" => "200", "message" => "Address is set to default successfully"), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Failed to set as default address"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => '* fields are required'), 200);
		}
	}

	public function getUserExists_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];

			$Result = $this->GlobalModel->selectQuery('clientmaster.isActive', 'clientmaster', array('clientmaster.code' => $clientCode));

			if ($Result) {
				$userData = $Result->result_array()[0]['isActive'];

				if ($userData == 1) {
					return $this->response(array("status" => "200", "message" => "Active User"), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "You are inactive please contact admin."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Someting went wrong try again..."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end address line client


	// Start Support Number
	public function getSupportContact_get()
	{
		return $this->response(array("status" => "200", "message" => "For Support 9373747055"), 200);
	}
	// End
}