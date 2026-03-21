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
		$this->load->library('cashfreepayment');
		$this->load->library('sendsms');
		$this->load->library('notificationlibv_3');
		$this->load->library('firestore');
		$this->load->library('assignorder');
		$this->load->library('encryption');
	}

	public function getMainCategoryList_get()
	{
		$columns = "maincategorymaster.*";
		$cond = array('maincategorymaster' . ".isActive" => 1);
		$orderBy = array('maincategorymaster' . ".id" => 'ASC');
		$Records = $this->GlobalModel->selectQuery($columns, 'maincategorymaster', $cond, $orderBy);
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

	// Start sendRegister OTP 
	public function sendRegisterOTP_post()
	{
		$postData = $this->post();
		if ($postData['contactNumber'] != "") {
			if (strlen($postData['contactNumber']) == 10) {
				$otpNumber = $this->ApiModel->generateOTPMaster($postData['contactNumber']);
				$result = $this->sendsms->sendOTPMessage($otpNumber, $postData['contactNumber']);
				$result['status'] = true;
				if ($result['status'] == true) {
					return $this->response(array("status" => "200", "message" => "OTP was sent successfully!", "result" => $otpNumber,"smsResponse"=>$result), 200);
				} else {
					//$data = new stdClass();
					return $this->response(array("status" => "300", "message" => "Failed to send  OTP!","result" => "", "smsResponse"=>$result), 200);
				}
			} else {
				$data = new stdClass();
				return $this->response(array("status" => "300", "result" => $data, "message" => "Contact Number should be exactly 10 digit number only!"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are mandatory!"), 200);
		}
	}

	public function decryptBody($encrypted_data)
	{
		$cipher = "aes-256-cbc";
		$encryption_key = "myvegizfooddeliv";
		$iv_size = openssl_cipher_iv_length($cipher);
		$iv = "myvegizfooddeliv";
		$encrypted_data = base64_decode($encrypted_data);   
		return openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
	}

	public function enc($text)
	{
		$textToEncrypt = $text;
		$encryptionMethod = "AES-256-CBC";
		$key = "encryptionhash";
		$iv = "sample9863254111";
		$encryptedText = openssl_encrypt(json_encode($textToEncrypt, true), $encryptionMethod, $key, $options = OPENSSL_RAW_DATA, $iv);
		$body = base64_encode($encryptedText);
		return $body;
	}

	public function dec($encryptedText)
	{
		$encryptionMethod = "AES-256-CBC";
		$key = "encryptionhash";
		$iv = "sample9863254111";
		$decryptedText = openssl_decrypt(base64_decode($encryptedText), $encryptionMethod, $key, $options = OPENSSL_RAW_DATA, $iv);
		return json_decode($decryptedText,true);
	}

	public function sendOTP_post()
	{
		$postData = $this->post();
		$encryptedData = $this->enc(["contactNumber" => 8983185204]);
		print_r($encryptedData);
		echo "<br>";
		$decryptedData = $this->dec($encryptedData);
		print_r($decryptedData);

		die;

		if ($postData['contactNumber'] != "") {
			//$number=$this->encryption->encrypt($postData['contactNumber']);			
			if (strlen($decryContactNumber) == 10) {
				$otpNumber = $this->ApiModel->generateOTPMaster($decryContactNumber);
				$result = $this->sendsms->sendOTPMessage($otpNumber, $decryContactNumber);
				$result['status'] = true;
				if ($result['status'] == true) {
					return $this->response(array("status" => "200", "message" => "OTP was sent successfully!", "result" => $otpNumber), 200);
				} else {
					$data = new stdClass();
					return $this->response(array("status" => "300", "result" => $data, "message" => "Failed to send  OTP!"), 200);
				}
			} else {
				$data = new stdClass();
				return $this->response(array("status" => "300", "result" => $data, "message" => "Contact Number should be exactly 10 digit number only!"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * fields are mandatory!"), 200);
		}
	}

	public function verifyRegisterOTP_post()
	{
		$postData = $this->post();
		if ($postData['otp'] != "" && $postData['contactNumber'] != "") {
			$result = $this->ApiModel->checkRegisterOTP($postData['otp'], $postData['contactNumber']);
			if ($result) {
				$condition = array(
					"clientmaster.mobile" => $postData['contactNumber'],
					"clientmaster.isActive" => 1
				);
				$res = $this->ApiModel->read_user_information($condition);
				if ($res != false) {
					$usdata['userData'] = $res[0];
					return $this->response(array("status" => "200", "message" => "OTP Verified and Logged in successfully", "accountExists" => 1, "result" => $usdata), 200);
				} else {
					$usdata = new stdClass();
					return $this->response(array("status" => "300", "message" => "OTP Verified but User does not exists", "accountExists" => 0), 200);
				}
			} else {
				$data = new stdClass();
				return $this->response(array("status" => "300", "result" => $data, "message" => "Invalid data entered"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Please enter OTP you have received to your contact number!"), 200);
		}
	}


	//login
	public function registration_post()
	{
		$postData = $this->post();
		if (isset($postData["contactNumber"]) && $postData["contactNumber"] != '' && isset($postData["cityCode"]) && $postData["cityCode"]  != '' && isset($postData["name"]) && $postData["name"]  != '') {
			if (strlen($postData['contactNumber']) == 10) {
				$checkIfCityValid = $this->GlobalModel->selectQuery("citymaster.code", "citymaster", array("citymaster.code" => $postData['cityCode']));
				if ($checkIfCityValid != false && $checkIfCityValid->num_rows() > 0) {
					$checkUserexists =  $this->GlobalModel->selectQuery("clientmaster.code", 'clientmaster', array("clientmaster.mobile" => $postData['contactNumber'], "clientmaster.isActive" => 1));
					if ($checkUserexists != false && $checkUserexists->num_rows() > 0) {
						$condition = array(
							"clientmaster.code" => $checkUserexists->result_array()[0]['code']

						);
						$resultData = $this->ApiModel->read_user_information($condition);
						$result['userData'] = $resultData[0];
						$this->response(array("status" => "300", "message" => "User Already Exists", "result" => $result), 200);
					} else {
						$insertArr = array(
							"mobile" => $postData["contactNumber"],
							'cityCode' => $postData['cityCode'],
							'name' => $postData['name'],
							"isActive" => 1
						);
						if (isset($postData['emailId']) && $postData['emailId'] != "") {
							$insertArr['emailId'] = $postData['emailId'];
						}
						$insertResult = $this->GlobalModel->addNew($insertArr, 'clientmaster', 'CLNT');
						if ($insertResult != 'false') {
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
								return $this->response(array("status" => "200", "message" => "Registration Successful..", "result" => $result), 200);
							} else {
								return $this->response(array("status" => "300", "message" => "Registration Successful.. Please Signin..", "result" => $result), 200);
							}
						} else {
							$this->response(array("status" => "300", "message" => " Opps...! Something went wrong please try again."), 200);
						}
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Invalid City"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Contact Number should be exactly 10 digit number only!"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end registration  Process

	//get Custom added address and area list
	public function getCustomAddressList_post()
	{
		$postData = $this->post();
		if (isset($postData['cityCode']) && $postData['cityCode'] != "") {
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
				$this->response(array("status" => "300", "message" => " No data Found"), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "  * are Required field(s)."), 400);
		}
	}

	//home slider
	public function homeSliderImages_get()
	{
		$columns = array("homeslider.*");
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

			return $this->response(array("status" => "300", "message" => "Data not found.", "result" => $data), 200);
		}
	}
	//home slider

	//get slider images
	public function sliderImages_post()
	{
		$postData = $this->post();
		if (isset($postData['cityCode']) && $postData['cityCode'] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
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
				return $this->response(array("status" => "300", "message" => "Data not found.", 'result' => $data), 200);
			}
		} else {
			$data['sliderImages'] = array();
			return $this->response(array("status" => "400", "message" => "* fields are required"), 400);
		}
	}
	//end slider images

	// Get Category list
	public function categoryList_post()
	{
		$postData = $this->post();
		if (isset($postData["offset"]) && $postData["offset"] != '' && isset($postData["mainCategoryCode"])  && $postData["mainCategoryCode"] != '') {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$category_offset = $postData["offset"];
			$category_limit = 10;
			$condition = array('isActive' => 1, 'mainCategoryCode' => $mainCategoryCode);
			$totalRecords = sizeof($this->ApiModel->selectData('categorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster', "", "", $condition)->result_array();
			if ($category_result) {
				for ($i = 0; $i < sizeof($category_result); $i++) {
					$category_result[$i]['categoryImage'] = base_url() . 'uploads/category/' . $category_result[$i]['code'] . '/' . $category_result[$i]['categoryImage'];
				}
				$data['categories'] = $category_result;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
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
		if (isset($postData["offset"]) && $postData["offset"] != '' && isset($postData['cityCode']) && $postData['cityCode'] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$category_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$category_limit = "5";
			$condition = array('isActive' => 1, 'mainCategoryCode' => $mainCategoryCode);
			$totalRecords = sizeof($this->ApiModel->selectData('categorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster', $category_limit, $category_offset, $condition)->result_array();
			if ($category_result) {
				$category_resulta = [];
				$art = [];
				for ($i = 0; $i < sizeof($category_result); $i++) {     

					$category_resulta[$i]['id'] = $category_result[$i]['id'];
					$category_resulta[$i]['code'] = $category_result[$i]['code'];
					$category_resulta[$i]['categoryName'] = $category_result[$i]['categoryName'];
					$category_resulta[$i]['categorySName'] = $category_result[$i]['categorySName'];
					$category_resulta[$i]['isActive'] = $category_result[$i]['isActive'];
					$category_resulta[$i]['categoryImage'] = base_url() . 'uploads/category/' . $category_result[$i]['code'] . '/' . $category_result[$i]['categoryImage'];

					$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingPrice as sellingPrice,productratelineentries.productStatus,productratelineentries.sellingUnit,productratelineentries.cityCode,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode,productratelineentries.quantity");
					$cond = array('productmaster' . ".productCategory" => $category_result[$i]['categorySName'], 'productmaster' . ".isActive" => 1, 'productratelineentries.cityCode' => $cityCode, "productratelineentries.isMainVariant" => 1,"productratelineentries.isActive" =>1,"productratelineentries.isDelete" =>0);
					$orderBy = array('productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC', 'productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
					$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
					$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left');
					$like = array();
					$groupBy = array();
					//$groupBy = ["productmaster.tagCode"];
					$p_result = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, $like, $category_limit, "", $groupBy);
					if ($p_result) {
						$product_result = $p_result->result_array();

						for ($j = 0; $j < sizeof($product_result); $j++) {
							$product_result[$j]['isInCart'] = false;
							$product_result[$j]['cartQuantity'] = 0;
							$product_result[$j]['cartCode'] = "";
							$product_result[$j]['isInWishlist'] = false;

							$productCode = $product_result[$j]['code'];
                            $product_result[$j]['quantity'] = $product_result[$j]['quantity'];
							//$product_result[$j]['quantity'] = number_format($product_result[$j]['quantity'], 0, '.', '');
							if ($clientCode != "") {

								$tableName1 = "clientwishlist";
								$orderColumns1 = array("clientwishlist.*");
								$cond1 = array('clientwishlist' . ".productCode" => $product_result[$j]['code'], 'clientwishlist' . ".clientCode" => $clientCode);
								$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
								$join1 = array();
								$joinType1 = array();
								$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
								if ($clientWishList) {
									$product_result[$j]['isInWishlist'] = true;
									$product_result[$j]['wishlistCode'] = $clientWishList->result_array()[0]['code'];
								}
							}
							$condition2 = array('productCode' => $product_result[$j]['code']);
							$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
							$imageArray = array();
							for ($img = 0; $img < sizeof($images_result); $img++) {
								array_push($imageArray, base_url() . 'uploads/product/' . $product_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
							}

							$product_result[$j]['images'] = $imageArray;
							unset($imageArray);

							if ($product_result[$j]['productUom'] == 'PC' && $product_result[$j]['minimumSellingQuantity'] > 1) {
								$product_result[$j]['productUom'] = 'PCS';
							}

							$taxCalulate = round($product_result[$j]['sellingPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);
							$sellingPrice_with_tax = number_format($product_result[$j]['sellingPrice'] + $taxCalulate, 2, '.', '');
							$product_result[$j]['sellingPrice'] = $sellingPrice_with_tax;

							//product rates 
							$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
							$tbl = 'productratelineentries';
							$cndt = ['productratelineentries.productCode' => $productCode, 'cityCode' => $cityCode,'isDelete'=>0,'isActive'=>1];
							$ordby = ['productratelineentries.productCode' => 'DESC'];
							$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);

							if ($rate_result) {
								$rates = [];
								foreach ($rate_result->result_array() as $rs) {
									$product =  $productCode . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
									//$product =  $productCode . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . number_format($rs['quantity'],3, '.', '');
									//echo $product;
									$art[] = $product;
									//in cart list 
									$isInCart1 = false;
									$cartQuantity1 = 0;
									$cartCode1 = "";
									$sellingPrice_tax=0;
									$regularPrice_tax=0;
									$discount = 0;
									if ($clientCode != "") {
										$tableName2 = "clientcarts";
										$orderColumns2 = array("clientcarts.*");
										$cond2 = array('clientcarts' . ".productCode" => $product_result[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
										$orderBy2 = array('clientcarts' . ".id" => 'DESC');
										$join2 = array();
										$joinType2 = array();
										$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);
										//echo $this->db->last_query();
										if ($clientcarts) {
											$isInCart1 = true;
											$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
											$cartCode1 = $clientcarts->result_array()[0]['code'];

											$product_result[$j]['variantsCode'] = $rs['code'];
											$product_result[$j]['quantity'] = $rs['quantity'];
											$product_result[$j]['regularPrice'] = $rs['regularPrice'];
											$product_result[$j]['sellingPrice'] = $rs['sellingPrice'];
											$product_result[$j]['sellingUnit'] = $rs['sellingUnit'];
											$product_result[$j]['isInCart'] = true;
											$product_result[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
											$product_result[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
										}

										$taxCalulate = round($rs['sellingPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);
										$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
										$taxCalulateRegular = round($rs['regularPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);
										$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
										$discount = '';
										if ($rs['regularPrice'] != null) {
											$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
										}
									}

									$rates[] = [
										'variantsCode' => $rs['code'],
										'cityCode' => $rs['cityCode'],
										'sellingUnit' => $rs['sellingUnit'],
										'quantity' => number_format($rs['quantity'], 0, '.', '') ?? "0",
										'productStatus' => $rs['productStatus'],
										'sellingPrice' => $sellingPrice_tax,
										//'sellingActualPrice'=>$rs['sellingPrice'],
										'regularPrice' => $regularPrice_tax,
										//'regularActualPrice'=>$rs['regularPrice'], 
										'productDiscount' => $discount,
										'isMainVariant' => $rs['isMainVariant'],
										'isInCart' => $isInCart1,
										'cartQuantity' => $cartQuantity1,
										'cartCode' => $cartCode1
									];
								}
								$rateArray = $rates;
							}
							$product_result[$j]['rate_variants'] = $rateArray;
						}
						$proData['products'] = $product_result;
						$category_resulta[$i]['productList'] = $proData;
					}
				}
				$data['categories'] = $category_resulta;
				return $this->response(array("status" => "200", "sqr" => $art, "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				$data['categories'] = array();
				return $this->response(array("status" => "300", "message" => "Data not found."), 400);
			}
		} else {
			$this->response(array("status" => "400", "message" => "* fields are required"), 200);
		}
	}

	public function getPopularProductsList_post()
	{
		$postData = $this->post();
		if (isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != '' && isset($postData['cityCode']) && $postData['cityCode'] != "") {
			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$mainCategoryCode = $postData['mainCategoryCode'];
			$cityCode = $postData['cityCode'];
			$condition1 = array("productmaster.isPopular" => 1, "productmaster.isActive" => 1, 'productratelineentries.cityCode' => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode,'productratelineentries.isDelete'=>0,'productratelineentries.isActive'=>1,'productratelineentries.isMainVariant'=>1);
			$join1 = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode');
			$joinType1 = array('productratelineentries' => 'inner');
			$totalProducta = $this->GlobalModel->selectQuery("productmaster.*,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingPrice as sellingPrice,productratelineentries.productStatus,productratelineentries.sellingUnit,productratelineentries.cityCode,productratelineentries.quantity", 'productmaster', $condition1, array('productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC'), $join1, $joinType1);
			if ($totalProducta) {
				$totalProduct = sizeof($totalProducta->result());
				//$totalProduct = 0;
				if ($totalProduct > 0) {
					$limit = 4;
					$data = array();
					$temp = 0;
					for ($i = 0; $i < $totalProduct; $i += 4) {
						$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.cityCode,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode");
						$cond = array("productmaster.isPopular" => 1, 'productratelineentries.cityCode' => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode, "productratelineentries.isMainVariant" => 1); //only kolhpaur data
						$orderBy = array('productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC', 'productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
						$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
						$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left');
						$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, array(), $limit, $i);
						//$q = $this->db->last_query();echo $q.'<br/>';
						if ($resultQuery) {
							$limitProduct_result = $resultQuery->result_array();
							for ($j = 0; $j < sizeof($limitProduct_result); $j++) {
								$limitProduct_result[$j]['isInCart'] = false;
								$limitProduct_result[$j]['cartQuantity'] = 0;
								$limitProduct_result[$j]['cartCode'] = "";
								$limitProduct_result[$j]['isInWishlist'] = false;
								$limitProduct_result[$j]['quantity'] = number_format($limitProduct_result[$j]['quantity'], 0, '.', '');
								if ($clientCode != "") {
									$tableName1 = "clientwishlist";
									$orderColumns1 = array("clientwishlist.*");
									$cond1 = array('clientwishlist' . ".productCode" => $limitProduct_result[$j]['code'], 'clientwishlist' . ".clientCode" => $clientCode);
									$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
									$join1 = array();
									$joinType1 = array();
									$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
									if ($clientWishList) {
										$limitProduct_result[$j]['isInWishlist'] = true;
										$limitProduct_result[$j]['wishlistCode'] = $clientWishList->result_array()[0]['code'];
									}
								}
								$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
								$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();

								$imageArray = array();

								for ($img = 0; $img < sizeof($images_result); $img++) {
									array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
								}
								if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
									$limitProduct_result[$j]['productUom'] = 'PCS';
								}

								$taxCalulate = round($limitProduct_result[$j]['productSellingPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);

								$sellingPrice_with_tax = number_format($limitProduct_result[$j]['productSellingPrice'] + $taxCalulate, 2, '.', '');
								$limitProduct_result[$j]['sellingPrice'] = $sellingPrice_with_tax;

								/*$discount='';
								$taxCalulateRegular = round($limitProduct_result[$j]['regularPrice'] * ($limitProduct_result[$j]['taxPercent']/100),2);
								$regularPrice_with_tax = number_format($limitProduct_result[$j]['regularPrice'] + $taxCalulateRegular,2,'.','');
								$limitProduct_result[$j]['regularPrice'] = $regularPrice_with_tax;
								if($limitProduct_result[$j]['regularPrice']!=0){
									$discount = round(((($limitProduct_result[$j]['regularPrice'] - $limitProduct_result[$j]['sellingPrice']) / ($limitProduct_result[$j]['regularPrice'])) * 100)).' %';
								}
								$limitProduct_result[$j]['productDiscount'] = $discount;*/


								$limitProduct_result[$j]['images'] = $imageArray;
								unset($imageArray);

								//product rates 
								$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
								$tbl = 'productratelineentries';
								$cndt = ['productratelineentries.productCode' => $limitProduct_result[$j]['code'], 'cityCode' => $cityCode,'isDelete'=>0,'isActive'=>1];
								$ordby = ['productratelineentries.code' => 'DESC'];
								$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
								if ($rate_result) {
									$rates = [];
									foreach ($rate_result->result_array() as $rs) {

										$product =  $limitProduct_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
										//in cart list 

										$isInCart1 = false;
										$cartQuantity1 = 0;
										$cartCode1 = "";
										if ($clientCode != "") {
											$tableName2 = "clientcarts";
											$orderColumns2 = array("clientcarts.*");
											$cond2 = array('clientcarts' . ".productCode" => $limitProduct_result[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
											$orderBy2 = array('clientcarts' . ".id" => 'DESC');
											$join2 = array();
											$joinType2 = array();
											$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

											if ($clientcarts) {
												$isInCart1 = true;
												$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
												$cartCode1 = $clientcarts->result_array()[0]['code'];

												$limitProduct_result[$j]['variantsCode'] = $rs['code'];
												$limitProduct_result[$j]['quantity'] = $rs['quantity'];
												$limitProduct_result[$j]['regularPrice'] = $rs['regularPrice'];
												$limitProduct_result[$j]['sellingPrice'] = $rs['sellingPrice'];
												$limitProduct_result[$j]['sellingUnit'] = $rs['sellingUnit'];

												$limitProduct_result[$j]['isInCart'] = true;
												$limitProduct_result[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
												$limitProduct_result[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
											}
										}

										$taxCalulate = round($rs['sellingPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
										$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
										$taxCalulateRegular = round($rs['regularPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
										$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
										$discount = '';
										if ($rs['regularPrice'] != null) {
											$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
										}
										$rates[] = [
											'variantsCode' => $rs['code'],
											'cityCode' => $rs['cityCode'],
											'sellingUnit' => $rs['sellingUnit'],
											'quantity' => number_format($rs['quantity'], 0, '.', ''),
											'productStatus' => $rs['productStatus'],
											'sellingPrice' => $sellingPrice_tax,
											//'sellingActualPrice'=>$rs['sellingPrice'],
											'regularPrice' => $regularPrice_tax,
											//'regularActualPrice'=>$rs['regularPrice'], 
											'productDiscount' => $discount,
											'isMainVariant' => $rs['isMainVariant'],
											'isInCart' => $isInCart1,
											'cartQuantity' => $cartQuantity1,
											'cartCode' => $cartCode1
										];
									}
									$rateArray = $rates;
								}
								$limitProduct_result[$j]['rate_variants'] = $rateArray;
							}
							$data[]['products'] = $limitProduct_result;
						}
					}
					$response['list'] = $data;
					//$data['products'] = $limitProduct_result;
					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $response), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
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
		if (isset($postData["categorySName"])  && $postData["categorySName"] != '' && isset($postData["offset"]) && $postData["offset"] != '' && isset($postData['cityCode']) && $postData["cityCode"] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$categorySName = $postData["categorySName"];
			$product_offset = $postData["offset"];
			$cityCode = $postData['cityCode'];
			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$product_limit = 10;
			$condition2 = array('productCategory' => $categorySName, 'isActive' => 1);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster', '', '', $condition2)->result());
			if ($totalProduct) {
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingUnit,productratelineentries.quantity,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode,productratelineentries.isMainVariant");
				$cond = array('productmaster' . ".productCategory" => $categorySName, 'productmaster' . '.isActive' => 1, 'productratelineentries.cityCode' => $cityCode, 'productratelineentries.isMainVariant' => 1,'productmaster' . '.isDelete' => 0,'productratelineentries.isDelete'=>0,'productratelineentries.isActive'=>1);
				$orderBy = array('productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC', 'productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
				$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
				$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left');
				$groupBy = array("productmaster.tagCode");
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, array(), $product_limit, $product_offset);
				//echo $this->db->last_query();
				if ($resultQuery) {
					$limitProduct_result = $resultQuery->result_array();
					for ($j = 0; $j < sizeof($limitProduct_result); $j++) {

						//is in cart & wishlist
						$limitProduct_result[$j]['isInCart'] = false;
						$limitProduct_result[$j]['cartQuantity'] = 0;
						$limitProduct_result[$j]['cartCode'] = "";
						$limitProduct_result[$j]['isInWishlist'] = false;
						$limitProduct_result[$j]['quantity'] = $limitProduct_result[$j]['quantity'];
						//$limitProduct_result[$j]['quantity'] = number_format($limitProduct_result[$j]['quantity'], 0, '.', '');

						if ($clientCode != "") {
							$tableName1 = "clientwishlist";
							$orderColumns1 = array("clientwishlist.*");
							$cond1 = array('clientwishlist' . ".productCode" => $limitProduct_result[$j]['code'], 'clientwishlist' . ".clientCode" => $clientCode);
							$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
							$join1 = array();
							$joinType1 = array();
							$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
							if ($clientWishList) {
								$limitProduct_result[$j]['isInWishlist'] = true;
								$limitProduct_result[$j]['wishlistCode'] = $clientWishList->result_array()[0]['code'];
							}
						}
						$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
						$imageArray = array();
						for ($img = 0; $img < sizeof($images_result); $img++) {
							array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
						}
						if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
							$limitProduct_result[$j]['productUom'] = 'PCS';
						}

						$taxCalulate = round($limitProduct_result[$j]['productSellingPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);

						$sellingPrice_with_tax = number_format($limitProduct_result[$j]['productSellingPrice'] + $taxCalulate, 2, '.', '');
						$limitProduct_result[$j]['sellingPrice'] = $sellingPrice_with_tax;

						/*$discount='';
							$taxCalulateRegular = round($limitProduct_result[$j]['regularPrice'] * ($limitProduct_result[$j]['taxPercent']/100),2);
							$regularPrice_with_tax = number_format($limitProduct_result[$j]['regularPrice'] + $taxCalulateRegular,2,'.','');
							$limitProduct_result[$j]['regularPrice'] = $regularPrice_with_tax;
							if($limitProduct_result[$j]['regularPrice']!=0){
								$discount = round(((($limitProduct_result[$j]['regularPrice'] - $limitProduct_result[$j]['sellingPrice']) / ($limitProduct_result[$j]['regularPrice'])) * 100)).' %';
							}
							$limitProduct_result[$j]['productDiscount'] = $discount;*/


						$limitProduct_result[$j]['images'] = $imageArray;
						unset($imageArray);
						//product rates 
						$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
						$tbl = 'productratelineentries';
						$cndt = ['productratelineentries.productCode' => $limitProduct_result[$j]['code'], 'cityCode' => $cityCode,'isDelete'=>0,'isActive'=>1];
						$ordby = ['productratelineentries.productCode' => 'DESC'];
						$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
						if ($rate_result) {
							$rates = [];
							foreach ($rate_result->result_array() as $rs) {

								$product =  $limitProduct_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' .$rs['quantity'];
								//$product =  $limitProduct_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . number_format($rs['quantity'],3, '.', '');
								//in cart list 

								$isInCart1 = false;
								$cartQuantity1 = 0;
								$cartCode1 = "";
								if ($clientCode != "") {
									$tableName2 = "clientcarts";
									$orderColumns2 = array("clientcarts.*");
									$cond2 = array('clientcarts' . ".productCode" => $limitProduct_result[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
									$orderBy2 = array('clientcarts' . ".id" => 'DESC');
									$join2 = array();
									$joinType2 = array();
									$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

									if ($clientcarts) {
										$isInCart1 = true;
										$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
										$cartCode1 = $clientcarts->result_array()[0]['code'];

										$limitProduct_result[$j]['variantsCode'] = $rs['code'];
										$limitProduct_result[$j]['quantity'] = $rs['quantity'];
										$limitProduct_result[$j]['regularPrice'] = $rs['regularPrice'];
										$limitProduct_result[$j]['sellingPrice'] = $rs['sellingPrice'];
										$limitProduct_result[$j]['sellingUnit'] = $rs['sellingUnit'];

										$limitProduct_result[$j]['isInCart'] = true;
										$limitProduct_result[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
										$limitProduct_result[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
									}
								}
								$taxCalulate = round($rs['sellingPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
								$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
								$taxCalulateRegular = round($rs['regularPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
								$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
								$discount = '';
								if ($rs['regularPrice'] != null) {
									$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
								}
								$rates[] = [
									'variantsCode' => $rs['code'],
									'cityCode' => $rs['cityCode'],
									'sellingUnit' => $rs['sellingUnit'],
									'quantity' => number_format($rs['quantity'], 0, '.', ''),
									'productStatus' => $rs['productStatus'],
									'sellingPrice' => $sellingPrice_tax,
									//'sellingActualPrice'=>$rs['sellingPrice'],
									'regularPrice' => $regularPrice_tax,
									//'regularActualPrice'=>$rs['regularPrice'],
									'productDiscount' => $discount,
									'isMainVariant' => $rs['isMainVariant'],
									'isInCart' => $isInCart1,
									'cartQuantity' => $cartQuantity1,
									'cartCode' => $cartCode1  
								];
							}
							$rateArray = $rates;
						}
						$limitProduct_result[$j]['rate_variants'] = $rateArray;
					}
					$data['products'] = $limitProduct_result;
					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $data), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "* fields are required"), 400);
		}
	}  //Ends Product list by categorySName

	// Get Product by productCode
	public function productById_post()
	{
		$postData = $this->post();

		if (isset($postData["productCode"]) && $postData["productCode"] != '' && isset($postData['cityCode']) && $postData['cityCode'] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$productCode = $postData["productCode"];
			$cityCode = $postData['cityCode'];

			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingUnit,productratelineentries.quantity,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode,productratelineentries.isMainVariant");
			$cond = array('productmaster' . ".code" => $productCode, 'productratelineentries.cityCode' => $cityCode, 'productmaster.mainCategoryCode' => $mainCategoryCode,"productratelineentries.isMainVariant" => 1,'productratelineentries.isDelete'=>0,'productratelineentries.isActive'=>1);
			$orderBy = array('productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC','productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
			$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
			$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left');
			$product_result = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType);
			//echo $this->db->last_query();
			if ($product_result) {
				$product_result = $product_result->result_array();
				for ($j = 0; $j < sizeof($product_result); $j++) {
					$product_result[$j]['quantity'] = number_format($product_result[$j]['quantity'], 0, '.', '');
					$product_result[$j]['isInCart'] = false;
					$product_result[$j]['cartQuantity'] = 0;
					$product_result[$j]['cartCode'] = "";
					$product_result[$j]['isInWishlist'] = false;
					if ($clientCode != "") {
						$tableName1 = "clientwishlist";
						$orderColumns1 = array("clientwishlist.*");
						$cond1 = array('clientwishlist' . ".productCode" => $product_result[$j]['code'], 'clientwishlist' . ".clientCode" => $clientCode);
						$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
						$join1 = array();
						$joinType1 = array();
						$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
						if ($clientWishList) {
							$product_result[$j]['isInWishlist'] = true;
							$product_result[$j]['wishlistCode'] = $clientWishList->result_array()[0]['code'];
						}
					}
					$condition2 = array('productCode' => $product_result[$j]['code']);
					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
					$imageArray = array();
					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $product_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
					}
					if ($product_result[$j]['productUom'] == 'PC' && $product_result[$j]['minimumSellingQuantity'] > 1) {
						$product_result[$j]['productUom'] = 'PCS';
					}

					$taxCalulate = round($product_result[$j]['productSellingPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);

					$sellingPrice_with_tax = number_format($product_result[$j]['productSellingPrice'] + $taxCalulate, 2, '.', '');
					$product_result[$j]['sellingPrice'] = $sellingPrice_with_tax;

					/*$discount='';
					$taxCalulateRegular = round($product_result[$j]['regularPrice'] * ($product_result[$j]['taxPercent']/100),2);
					$regularPrice_with_tax = number_format($product_result[$j]['regularPrice'] + $taxCalulateRegular,2,'.','');
					$product_result[$j]['regularPrice'] = $regularPrice_with_tax;
					if($product_result[$j]['regularPrice']!=0){
						$discount = round(((($product_result[$j]['regularPrice'] - $product_result[$j]['sellingPrice']) / ($product_result[$j]['regularPrice'])) * 100)).' %';
					}
					$product_result[$j]['productDiscount'] = $discount;*/


					$product_result[$j]['images'] = $imageArray;
					unset($imageArray);
					//product rates 
					$clms = "productmaster.code as productCode,productmaster.productName,productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
					$tbl = 'productratelineentries';
					$cndt = ['productratelineentries.productCode' => $product_result[$j]['code'], 'cityCode' => $cityCode,'productratelineentries.isDelete'=>0,'productratelineentries.isActive'=>1];
					$ordby = ['productratelineentries.productCode' => 'DESC'];
					$join3 = array('productmaster' => 'productmaster.code=productratelineentries.productCode');
					$joinType3 = array('productmaster' => 'inner');
					$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby, $join3, $joinType3);
					//$rateQuery = $this->db->last_query();
					$rateArray = [];
					if ($rate_result) {
						$rates = [];
						foreach ($rate_result->result_array() as $rs) {
							//$product =  $product_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . number_format($rs['quantity'],3,'.','');
							$product =  $product_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
							//in cart list 

							$isInCart1 = false;
							$cartQuantity1 = 0;
							$cartCode1 = "";
							if ($clientCode != "") {
								$tableName2 = "clientcarts";
								$orderColumns2 = array("clientcarts.*");
								$cond2 = array('clientcarts' . ".productCode" => $product_result[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
								$orderBy2 = array('clientcarts' . ".id" => 'DESC');
								$join2 = array();
								$joinType2 = array();
								$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

								if ($clientcarts) {
									$isInCart1 = true;
									$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
									$cartCode1 = $clientcarts->result_array()[0]['code'];

									$product_result[$j]['variantsCode'] = $rs['code'];
									$product_result[$j]['quantity'] = $rs['quantity'];
									$product_result[$j]['regularPrice'] = $rs['regularPrice'];
									$product_result[$j]['sellingPrice'] = $rs['sellingPrice'];
									$product_result[$j]['sellingUnit'] = $rs['sellingUnit'];

									$product_result[$j]['isInCart'] = true;
									$product_result[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
									$product_result[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
								}
							}
							$taxCalulate = round($rs['sellingPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);
							$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
							$taxCalulateRegular = round($rs['regularPrice'] * ($product_result[$j]['taxPercent'] / 100), 2);
							$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
							$discount = '';
							if ($rs['regularPrice'] != null) {
								$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
							}
							$rates[] = [
								'code' => $rs['productCode'],
								'productName' => $rs['productName'],
								'variantsCode' => $rs['code'],
								'cityCode' => $rs['cityCode'],
								'sellingUnit' => $rs['sellingUnit'],
								'quantity' => number_format($rs['quantity'], 0, '.', ''),
								'productStatus' => $rs['productStatus'],
								'sellingPrice' => $sellingPrice_tax,
								//'sellingActualPrice'=>$rs['sellingPrice'],
								'regularPrice' => $regularPrice_tax,
								//'regularActualPrice'=>$rs['regularPrice'],
								'productDiscount' => $discount,
								'isMainVariant' => $rs['isMainVariant'],
								'isInCart' => $isInCart1,
								'cartQuantity' => $cartQuantity1,
								'cartCode' => $cartCode1
							];
						}
						$rateArray = $rates;
					}
					$product_result[$j]['rate_variants'] = $rateArray;
				}
				$data['products'] = $product_result[0];
				return $this->response(array("status" => "200", "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Data not found."), 400);
		}
	} // Ends Get Product by productCode 

	// check wishlist 
	public function checkWishList_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["productCode"]) && $postData["productCode"] != '') {
			$clientCode = $postData["clientCode"];
			$client_result = $this->GlobalModel->selectDataById($clientCode, 'clientmaster')->result_array();
			if ($client_result) {
				$clientCode = $client_result[0]['code'];
				$condition2 = array("clientCode" => $postData["clientCode"], "productCode" => $postData["productCode"], "isActive" => 1);
				$wishlist_result = $this->ApiModel->selectData('clientwishlist', '', '', $condition2)->result_array();
				if (sizeof($wishlist_result) > 0) {
					return $this->response(array("status" => "200", "message" => 'item already exist in wishlist'), 200);
				} else {
					return $this->response(array("status" => "300", "message" => 'iteam not added to wishlist yet'), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => 'user not registerd'), 400);
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

		if (isset($postData["productCode"]) && $postData["productCode"] != "" && isset($postData["clientCode"]) && $postData["clientCode"] != '') {
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
						'isActive' => 1,
						'addIP' => $_SERVER['REMOTE_ADDR'],
						'addID' => $clientCode
					];

					$code = $this->GlobalModel->addNew($data, 'clientwishlist', 'WISH');

					if ($code != 'false') {
						return $this->response(array("status" => "200", "message" => "Product added to your wishlist successfully."), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Product not added. Please try again later."), 400);
					}
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user before adding product to your wishlist."), 400);
			}
		} else {
			return $this->response(array("status" => "300", "message" => "Data not found."), 400);
		}
	} // Ends Add to Wish list by productId and userId

	// get WishList by ClientCode
	public function getWishList_post()
	{
		$postData = $this->post();

		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData['cityCode']) && $postData['cityCode'] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$clientCode = $postData["clientCode"];
			$cityCode = $postData['cityCode'];
			$tableName = "clientwishlist";
			$orderColumns = array("productmaster.*,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,clientwishlist.productCode, productratelineentries.productStatus, productratelineentries.sellingPrice as productSellingPrice,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.code as variantsCode,productratelineentries.isMainVariant");
			$cond = array('clientwishlist' . ".clientCode" => $clientCode, 'productratelineentries.cityCode' => $cityCode, 'productmaster.mainCategoryCode' => $mainCategoryCode,"productratelineentries.isMainVariant" => 1);
			$orderBy = array('clientwishlist' . ".id" => 'DESC');
			$groupBy = array();
			$join = array('productmaster' => 'clientwishlist' . '.productCode=' . 'productmaster' . '.code', 'productratelineentries' => 'productmaster' . '.code=' . 'productratelineentries' . '.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
			$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner', 'tagmaster' => 'left');
			$clientWishList = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType,array(),'','',$groupBy);
			//echo $this->db->last_query();
			if ($clientWishList) {				
				$clientWishList = $clientWishList->result_array();
				for ($j = 0; $j < sizeof($clientWishList); $j++) {
					$clientWishList[$j]['isInCart'] = false;
					$clientWishList[$j]['isInWishlist'] = false;
					$clientWishList[$j]['cartQuantity'] = 0;
					$clientWishList[$j]['cartCode'] = "";
					
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
					if ($clientCode != "") {
						$tableName1 = "clientwishlist";
						$orderColumns1 = array("clientwishlist.*");
						$cond1 = array('clientwishlist' . ".productCode" => $productCode, 'clientwishlist' . ".clientCode" => $clientCode);
						$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
						$join1 = array();
						$joinType1 = array();
						$clientWish = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
						if ($clientWish) {
							$clientWishList[$j]['isInWishlist'] = true;
							$clientWishList[$j]['wishlistCode'] = $clientWish->result_array()[0]['code'];
						}
					}
                    $rateArray=[];
					
					//product rates 
					$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
					$tbl = 'productratelineentries';
					$cndt = ['productratelineentries.productCode' => $clientWishList[$j]['code'], 'cityCode' => $cityCode,'productratelineentries.isDelete'=>0,'productratelineentries.isActive'=>1];
					$ordby = ['productratelineentries.code' => 'DESC'];
					$join3 = array('productmaster' => 'productmaster.code=productratelineentries.productCode');
					$joinType3 = array('productmaster' => 'inner');
					$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby, $join3, $joinType3);
					if ($rate_result) {
						$rates = [];
						foreach ($rate_result->result_array() as $rs) {
							//$product =  $clientWishList[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . number_format($rs['quantity'],3,'.','');
							$product =  $clientWishList[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
							$isInCart1 = false;
							$cartQuantity1 = 0;
							$cartCode1 = "";
							if ($clientCode != "") {						
					
								$tableName2 = "clientcarts";
								$orderColumns2 = array("clientcarts.*");
								//$cond2 = array('clientcarts' . ".productCode" => $clientWishList[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode);
								
								$cond2 = array('clientcarts' . ".productCode" => $clientWishList[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
								$orderBy2 = array('clientcarts' . ".id" => 'DESC');
								$join2 = array();
								$joinType2 = array();
								$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);
                                //echo $this->db->last_query();
								if ($clientcarts) {
									$isInCart1 = true;
									$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
									$cartCode1 = $clientcarts->result_array()[0]['code'];

									$clientWishList[$j]['variantsCode'] = $rs['code'];
									$clientWishList[$j]['quantity'] = $rs['quantity'];
									$clientWishList[$j]['regularPrice'] = $rs['regularPrice'];
									$clientWishList[$j]['sellingPrice'] = $rs['sellingPrice'];
									$clientWishList[$j]['sellingUnit'] = $rs['sellingUnit'];

									$clientWishList[$j]['isInCart'] = true;
									$clientWishList[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
									$clientWishList[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
								}
							}
							$taxCalulate = round($rs['sellingPrice'] * ($clientWishList[$j]['taxPercent'] / 100), 2);
							$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
							$taxCalulateRegular = round($rs['regularPrice'] * ($clientWishList[$j]['taxPercent'] / 100), 2);
							$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
							$discount = '';
							if ($rs['regularPrice'] != null) {
								$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
							}
							$rates[] = [
								'variantsCode' => $rs['code'],
								'cityCode' => $rs['cityCode'],
								'sellingUnit' => $rs['sellingUnit'],
								'quantity' => number_format($rs['quantity'], 0, '.', ''),
								'productStatus' => $rs['productStatus'],
								'sellingPrice' => $sellingPrice_tax,
								//'sellingActualPrice'=>$rs['sellingPrice'],
								'regularPrice' => $regularPrice_tax,
								//'regularActualPrice'=>$rs['regularPrice'],
								'productDiscount' => $discount,
								'isMainVariant' => $rs['isMainVariant'],
								'isInCart' => $isInCart1,
								'cartQuantity' => $cartQuantity1,
								'cartCode' => $cartCode1
							];
						}
						$rateArray = $rates;
					}
					$clientWishList[$j]['rate_variants'] = $rateArray;
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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$orderColumns = array("clientmaster.code,clientmaster.name,clientmaster.emailId,clientmaster.mobile,clientmaster.cityCode,clientmaster.cartCode,clientmaster.isCodEnabled,clientprofile.gender,clientprofile.address,clientprofile.latitude,clientprofile.longitude,clientprofile.areaCode,clientprofile.addressType,clientprofile.area,clientprofile.local,clientprofile.flat,clientprofile.pincode,clientprofile.state,clientprofile.landmark,IFNULL(citymaster.cityName,'-') as city");
			$cond = array('clientmaster' . ".code" => $clientCode, 'clientprofile.isActive' => 1);
			$orderBy = array('clientmaster' . ".id" => 'ASC', 'clientprofile.id' => "DESC");
			$join = array('citymaster' => 'clientmaster.cityCode=citymaster.code', 'clientprofile' => 'clientmaster' . '.code=' . 'clientprofile' . '.clientCode');
			$joinType = array('citymaster' => 'left', 'clientprofile' => 'inner');
			$resultData = $this->GlobalModel->selectQuery($orderColumns, 'clientmaster', $cond, $orderBy, $join, $joinType);

			if ($resultData) {
				$result['userProfile'] = $resultData->result_array()[0];
				return $this->response(array("status" => "200", "result" => $result), 200);
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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["name"]) && $postData["name"] != '') {
			$emailID = '';
			if (isset($postData["emailId"]) && $postData["emailId"] != "") {
				if (filter_var($postData["emailId"], FILTER_VALIDATE_EMAIL)) {
					$emailID = $postData["emailId"];
				} else {
					return $this->response(array("status" => "300", "message" => "Invalid Email ID"), 200);
				}
			}
			$dataMaster = [
				"name" => $postData["name"],
			];
			if ($emailID != "") {
				$dataMaster["emailId"] = $emailID;
			}
			if (isset($postData["cityCode"]) && $postData["cityCode"] != "") {
				$dataMaster["cityCode"] = $postData['cityCode'];
			}
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();
			if (sizeof($resultData) == 1) {
				$resultMaster = $this->GlobalModel->doEdit($dataMaster, 'clientmaster', $postData["clientCode"]);
				if ($resultMaster != false) {
					return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => " Failed to update your profile."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}  // End update profile

	//updateuseraddress
	public function updateProfileAddress_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["city"]) && $postData["city"] != '' && isset($postData["state"]) && $postData["state"] != '' && isset($postData["area"]) && $postData["area"] != '' && isset($postData["local"]) && $postData["local"] != '' && isset($postData["flat"]) && $postData["flat"] != '' && isset($postData["pincode"]) && $postData["pincode"] != ''  && isset($postData["areaCode"]) && $postData["areaCode"] != '') {
			$dataProfile = [
				"city" => $postData["city"],
				"local" => $postData["local"],
				"area" => $postData["area"],
				"state" => $postData["state"],
				"flat" => $postData["flat"],
				"pincode" => $postData["pincode"],
				"areaCode" => $postData["areaCode"],
			];
			if (isset($postData["landMark"]) && $postData["landMark"] != "") {
				$dataProfile["landMark"] = $postData["landMark"];
			}
			$resultData = $this->GlobalModel->selectDataById($postData["clientCode"], 'clientmaster')->result_array();
			if (sizeof($resultData) == 1) {
				$resultProfile = $this->GlobalModel->doEditWithField($dataProfile, 'clientprofile', 'clientCode', $postData["clientCode"]);
				if ($resultProfile != false) {
					return $this->response(array("status" => "200", "message" => "Your profile has been updated successfully."), 200);
				} else {
					return $this->response(array("status" => "300", "message" => " Failed to update your profile."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user."), 400);
			}
		} else {
			return $this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	} //end update user address


	//Add to cart by productCode and clientCode
	public function addToCart_post()
	{
		$postData = $this->post();
		if (isset($postData["productCode"]) && $postData["productCode"] != '' &&  isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["quantity"]) && $postData["quantity"] != '' && isset($postData["productName"]) && $postData['productName'] != "" && isset($postData["unit"]) && $postData['unit'] != "" && isset($postData["sellingQuantity"]) && $postData['sellingQuantity'] != "" && isset($postData["price"]) && $postData['price'] != "" && isset($postData["unitId"]) && $postData['unitId'] != "") {
			$productCode = $postData["productCode"];
			$clientCode = $postData["clientCode"];
			$quantity = $postData["quantity"];
			$productName = $postData["productName"];
			$unit = $postData["unit"];
			$unitId = $postData["unitId"];
			$sellingQuantity = $postData["sellingQuantity"];
			$price = $postData["price"];
			// product string
			$product = $productCode . '##' . $postData['unit'] . '##' . $postData['unitId'] . '##' . $sellingQuantity;
			log_message("error", $product);
			//$product = $productCode . '##' . $postData['unit'] . '##' . $postData['unitId'] . '##' . number_format($sellingQuantity,3,'.','');
			$clientData = $this->GlobalModel->selectDataById($clientCode, 'clientmaster')->result_array();
			if (sizeof($clientData) > 0) {
				$condition2 = array('productCode' => $productCode, 'product' => $product, 'clientCode' => $clientCode, 'isActive' => 1);
				$clientCart = $this->ApiModel->selectData('clientcarts', '', '', $condition2)->result_array();
				//$clientCart = $this->GlobalModel->selectQuery("clientcarts.*", "clientcarts", $condition2,array(), array(), array(), array(), "", "", array(), "")->result_array();
				if ($clientCart != FALSE && sizeof($clientCart) > 0) {
					return $this->response(array("status" => "300", "message" => "Product already present in your cart."), 200);
				} else {
					$data = [
						'clientCode' => $clientCode,
						'productCode' => $productCode,
						'product' => $product,
						'quantity' => $quantity,
						'price' => $price,
						'sellingUnit' => $postData['unit'],
						'rateQuantity' => $sellingQuantity,
						'isActive' => 1,
						'addIP' => $_SERVER['REMOTE_ADDR'],
						'addID' => $clientCode
					];
					$code = $this->GlobalModel->addNew($data, 'clientcarts', 'CART');
					if ($code != 'false') {
						return $this->response(array("status" => "200", "message" => "Product added to your cart successfully.", "cartCode" => $code), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Failed to add product to your cart . Please try again later."), 200);
					}
				}
			} else {
				return $this->response(array("status" => "300", "message" => "User not registered. Please register user before adding product to your cart."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "* are required Fields"), 400);
		}
	}  //Ends Add to cart by productId and clientId

	public function getGroceryProductCartCount_post()
	{
		$postData = $this->post();
		
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			/*$cartData = $this->GlobalModel->selectQuery("GROUP_CONCAT(CONCAT('''', productCode, '''' )) as cartproducts,ifnull(count(id),0) as productCount","clientcarts",array("clientcarts.clientCode"=>$postData['clientCode']));
			if($cartData!=false && $cartData->num_rows()>0){
				$cartproducts = $cartData->result_array()[0]['cartproducts'];
				$productCount = $cartData->result_array()[0]['productCount'];
				if($cartproducts!=''){
					$checkProduct = $this->db->query("select code from productmaster where productmaster.code in (".$cartproducts.")");
					if($checkProduct){
						$count = count($checkProduct->result_array());
						
					}
				}
				$data['groceryCartCount'] = $count;
				return $this->response(array("status" => "200", "message"=>"Grocery Product Cart Count", "result" => $data), 200); 
			}else{
				return $this->response(array("status" => "300", "message" => "No data found"), 200);
			}*/
			
			$cityCode="";
			
			$cityData = $this->GlobalModel->selectQuery("clientmaster.*", "clientmaster", array("clientmaster.code" => $postData['clientCode']));
			if($cityData !=false && $cityData->num_rows()>0){
				   foreach($cityData->result_array() as $items){
					   $cityCode=$items["cityCode"];
				   }
			}
			//echo $this->db->last_query();
			$count = 0;
		    $cartAmount = 0;  
			$cartData = $this->GlobalModel->selectQuery("clientcarts.*", "clientcarts", array("clientcarts.clientCode" => $postData['clientCode']));
			if ($cartData != false && $cartData->num_rows() > 0) {
				foreach ($cartData->result_array() as $item) {
                    
					$checkProduct = $this->db->query("select productmaster.code,productratelineentries.* from productmaster inner join productratelineentries on productmaster.code=productratelineentries.productCode where productmaster.code = '" . $item['productCode'] . "' 
                                    AND cityCode = '" . $cityCode . "'");
					
					if ($checkProduct != false && $checkProduct->num_rows() > 0) {
						
						$cartAmount = $cartAmount + ($item['price'] * $item['quantity']);
					   
					}
				}
				$data['cartAmount'] = number_format($cartAmount, 2, '.', '');
				$data['groceryCartCount'] = $cartData->num_rows();
				return $this->response(array("status" => "200", "message" => "Grocery Product Cart Count", "result" => $data), 200);
			} else {
				$data['cartAmount'] = number_format($cartAmount, 2, '.', '');
				$data['groceryCartCount'] = 0;
				return $this->response(array("status" => "200", "message" => "No data found", "result" => $data), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "* are required fields"), 200);
		}
	}

	// start get CartList by ClientCode
	public function getCartList_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			$clientCode = $postData["clientCode"];
			$cityCode = '';
			$cartAmount = 0;
			$productTax = 0;
			$amountWithTax = 0;
			$rateArray = [];
			$addressLatitudeData = $this->GlobalModel->selectQuery("clientprofile.latitude,clientprofile.longitude,clientprofile.cityCode", "clientprofile", array("clientprofile.isActive" => "1", "clientprofile.isSelected" => "1", "clientprofile.clientCode" => $clientCode));
			if ($addressLatitudeData != false) {
				$data = $addressLatitudeData->result_array()[0];
				$cityCode = $data['cityCode'];				
			}			
			if($cityCode==""){
				$clientData = $this->GlobalModel->selectQuery("clientmaster.cityCode", "clientmaster", array("clientmaster.isActive" => "1","clientmaster.code" => $clientCode));
				$getdata = $clientData->result_array()[0];
				$cityCode = $getdata['cityCode'];
			}
            
			$deliverySlotsList = $this->db->query("select code,slotTitle,startTime,endTime,ifnull(deliveryCharge,0) as deliveryCharge from deliveryChargesSlots where isActive=1 and (code='DSLT_1' OR ((startTime > '" . date('H:i:s') . "') or ('" . date('H:i:s') . "' between startTime and endTime))) order by startTime");
			$tableName = "clientcarts";
			$orderColumns = array("productmaster.id,productmaster.hsnCode,productmaster.taxPercent,productmaster.code,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingUnit,productratelineentries.productStatus,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.product,clientcarts.code as cartCode,productratelineentries.code as variantsCode");
			$cond = array("clientcarts.clientCode" => $clientCode, "productratelineentries.cityCode" => $cityCode, "productmaster.isActive" => 1);
			$orderBy = array('clientcarts' . ".id" => 'DESC');
			$join = array('productmaster' => 'clientcarts.productCode=productmaster.code', "productratelineentries" => 'productmaster.code=productratelineentries.productCode AND clientcarts.rateQuantity=productratelineentries.quantity');
			$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner');
			$res = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
		    //echo $this->db->last_query();
		    $totalRec=0;
			if ($res) {
				$clientCartList = $res->result_array();
				for ($j = 0; $j < sizeof($clientCartList); $j++) {
					$productCode = $clientCartList[$j]['code'];
					$cartproduct = $clientCartList[$j]['product'];
					$cartQty = $clientCartList[$j]['quantity'];
                    $totalRec=$totalRec+1;
					
					$cartProductArray = explode("##", $cartproduct);

					$condition2 = array('productCode' => $productCode);
					$clientCartList[$j]['isInCart'] = true;
					$clientCartList[$j]['isInWishlist'] = false;
					$clientCartList[$j]['cartQuantity'] = $cartQty;
					$clientCartList[$j]['cartCode'] = $clientCartList[$j]['cartCode'];
					$clientCartList[$j]['variantsCode'] = $cartProductArray[2];
					$clientCartList[$j]['quantity'] = $cartProductArray[3];
					$clientCartList[$j]['totalPrice'] = number_format($clientCartList[$j]['sellingPrice'] * $cartQty, 2);
					

				// 	$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
				// 	$tbl = 'productratelineentries';
				// 	$cndt = ['productratelineentries.productCode' => $productCode, "productratelineentries.code" => $cartProductArray[2]];
				// 	$ordby = ['productratelineentries.code' => 'DESC'];
				// 	$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby, [], [], [], 1);

				// 	if ($rate_result) {
				// 		$rates = [];
				// 		foreach ($rate_result->result_array() as $rs) {
				// 			$clientCartList[$j]["regularPrice"] = $rs['regularPrice'];
				// 			$clientCartList[$j]["sellingPrice"] = $rs['sellingPrice'];
				// 		}
				// 	}

					if ($clientCode != "") {
				      $tableName2 = "clientcarts";
				// 		$orderColumns2 = array("clientcarts.*");
				// 		$cond2 = array('clientcarts' . ".productCode" => $productCode, 'clientcarts' . ".clientCode" => $clientCode);
				// 		$orderBy2 = array('clientcarts' . ".id" => 'DESC');
				// 		$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2);
				// 		if ($clientcarts) {
				// 			$clientCartList[$j]['isInCart'] = true;
				// 		//	$clientCartList[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
				// 		//	$clientCartList[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
				// 		}

						$tableName1 = "clientwishlist";
						$orderColumns1 = array("clientwishlist.*");
						$cond1 = array('clientwishlist' . ".productCode" => $productCode, 'clientwishlist' . ".clientCode" => $clientCode);
						$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
						$join1 = array();
						$joinType1 = array();
						$clientWish = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
						if ($clientWish) {
							$clientCartList[$j]['isInWishlist'] = true;
							$clientCartList[$j]['wishlistCode'] = $clientWish->result_array()[0]['code'];
						}
					}

					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
					$imageArray = array();
					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $productCode . '/' . $images_result[$img]['productPhoto']);
					}
					$clientCartList[$j]['images'] = $imageArray;
					$productTax = ($clientCartList[$j]['sellingPrice'] * ($clientCartList[$j]['taxPercent'] / 100));
					$amountWithTax = $productTax + $clientCartList[$j]['sellingPrice'];

					$cartAmount = $cartAmount + ($amountWithTax * $cartQty);
					unset($imageArray);

					//product rates 
				/*	$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
					$tbl = 'productratelineentries';
					$cndt = ['productratelineentries.productCode' => $productCode, "productratelineentries.code" => $cartProductArray[3]];
					$ordby = ['productratelineentries.code' => 'DESC'];
					$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
					//$query[] = $this->db->last_query();
					if ($rate_result) {
						$rates = [];
						foreach ($rate_result->result_array() as $rs) {
							$product =  $clientCartList[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
							//in cart list 
							$isInCart1 = false;
							$cartQuantity1 = 0;
							$cartCode1 = "";
							if ($clientCode != "") {
								$tableName2 = "clientcarts";
								$orderColumns2 = array("clientcarts.*");
								$cond2 = array('clientcarts' . ".productCode" => $clientCartList[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
								$orderBy2 = array('clientcarts' . ".id" => 'DESC');
								$join2 = array();
								$joinType2 = array();
								$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

								if ($clientcarts) {
									$isInCart1 = true;
									$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
									$cartCode1 = $clientcarts->result_array()[0]['code'];
								}
							}

							$taxCalulate = round($rs['sellingPrice'] * ($clientCartList[$j]['taxPercent'] / 100), 2);
							$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
							$taxCalulateRegular = round($rs['regularPrice'] * ($clientCartList[$j]['taxPercent'] / 100), 2);
							$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
							$discount = '';
							if ($rs['regularPrice'] != null) {
								$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
							}
							$rates[] = [
								'variantsCode' => $rs['code'],
								'cityCode' => $rs['cityCode'],
								'sellingUnit' => $rs['sellingUnit'],
								'quantity' => number_format($rs['quantity'], 0, '.', ''),
								'productStatus' => $rs['productStatus'],
								'sellingPrice' => $sellingPrice_tax,
								//'sellingActualPrice'=>$rs['sellingPrice'],
								'regularPrice' => $regularPrice_tax,
								//'regularActualPrice'=>$rs['regularPrice'],
								'productDiscount' => $discount,
								'isMainVariant' => $rs['isMainVariant'],
								'isInCart' => $isInCart1,
								'cartQuantity' => $cartQuantity1,
								'cartCode' => $cartCode1
							];
						}
						$rateArray = $rates;
					}*/
				//	$clientCartList[$j]['rate_variants'] = [];
				}
				$data['products'] = $clientCartList;
				$deliveryListArray = [];
				$deliveryCharge = 0;
				$minOrder = 0;
				$shortestDistance = 0;
				$gst = 0;

				//runtime//isset($postData['deliverySlotCode']) && $postData['deliverySlotCode'] == 'DSLT_1'
				if (true) { //always slot zero set

					if ($cityCode != '') {

						//	$getVegiStoreLocation = $this->GlobalModel->selectQuery("vegitablestorelocation.latitude,vegitablestorelocation.longitude", "vegitablestorelocation", array("vegitablestorelocation.cityCode" => $cityCode));
						$getVegiStoreLocation = $this->GlobalModel->selectQuery("citymaster.latitude,citymaster.longitude", "citymaster", array("citymaster.code" => $cityCode));

						if ($getVegiStoreLocation != false) {
							$resultData = $getVegiStoreLocation->result_array()[0];
							$storeLatitude = $resultData['latitude'];
							$storeLongitude = $resultData['longitude'];
							if ($storeLatitude != "" && $storeLongitude != "") {
								$result = $this->calculateDistanceDeliveryCharges($storeLatitude, $storeLongitude, $clientCode, $cartAmount);
								//	print_r($result);
								$deliveryCharge = $result['charges'];
								$shortestDistance = $result['shortestdistance'];
							}
						}
					}
				}

				if ($deliverySlotsList != false) {
					foreach ($deliverySlotsList->result_array() as $dls) {
						$deliveryList = $dls;
						$deliveryList['isSelected'] = 0;
						$deliveryList['startTime'] = $deliveryList['endTime'] = '';
						if ($dls['startTime'] != '' && $dls['startTime'] != NULL) {
							$deliveryList['startTime'] = date('h:i A', strtotime($dls['startTime']));
						}
						if ($dls['endTime'] != '' && $dls['endTime'] != NULL) {
							$deliveryList['endTime'] = date('h:i A', strtotime($dls['endTime']));
						}
						if (isset($postData['deliverySlotCode']) && $postData['deliverySlotCode'] != '') {
							if ($dls['code'] == $postData['deliverySlotCode']) {
								$deliveryList['isSelected'] = 1;
								if ($postData['deliverySlotCode'] == 'DSLT_1') {
									$deliveryList['deliveryCharge'] = $deliveryCharge;
								} else {
									$deliveryCharge = $dls['deliveryCharge'];
								}
							}
						}
						$deliveryListArray[] = $deliveryList;
					}
				}
				if (isset($postData['couponCode']) && $postData['couponCode'] != "") {
					$amountDetails = $this->getCouponDetails($postData['couponCode'], $postData['clientCode'], $cartAmount);
				} else {
					$amountDetails = $this->getCouponDetails('', $postData['clientCode'], $cartAmount);
				}

				$discount = $amountDetails['discountApplied'];
				$subTotal = $cartAmount - $discount;
				$gstPer = $gstAmount = 0;
				$getTaxDetails = $this->GlobalModel->selectQuery("settings.settingValue", "settings", array("settings.code" => 'SET_11'));
				if ($getTaxDetails != false) {
					$taxData = $getTaxDetails->result_array()[0];
					$gstPer = $taxData['settingValue'];
					if ($gstPer != "") {
						$gstAmount = round(($subTotal * $gstPer) / 100, 2);
					}
				}
				$packagingCharges = 0;
				$getPackagingCharges = $this->GlobalModel->selectQuery("ifnull(settings.settingValue,0) as charges", "settings", array("settings.code" => 'SET_12'));
				if ($getPackagingCharges != false) {
					$packagingData = $getPackagingCharges->result_array()[0];
					$packagingCharges = $packagingData['charges'];
				}
				$data['slotList'] = $deliveryListArray;
				//$deliveryCharge=0;
				//$packagingCharges=0;  
				$finalOrderAmount = $subTotal + $gstAmount + $deliveryCharge + $packagingCharges;
				return $this->response(array("status" => "200", "totalRecords" => $totalRec, "itemTotal" => number_format($cartAmount, 2, '.', ''), "discount" => number_format($discount, 2, '.', ''), "subTotal" => number_format($subTotal, 2, '.', ''), "minimumOrder" => $minOrder, "shortestDistance" => $shortestDistance, "deliveryCharge" => number_format($deliveryCharge, 2, '.', ''), "gstPer" => number_format($gstPer, 2, '.', ''), "gstAmount" => number_format($gstAmount, 2, '.', ''), "packagingCharges" => $packagingCharges, "finalOrderAmount" => number_format($finalOrderAmount, 2, '.', ''), "result" => $data, "couponDetails" => $amountDetails), 200);
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
			$cartCode = $postData["cartCode"];
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
				return $this->response(array("status" => "200", "message" => "cart updated.", "cartCode" => $cartCode), 200);
			} else {
				return $this->response(array("status" => "300", "message" => " Failed to update."), 200);
			}
		} else {
			return $this->response(array("status" => "300", "message" => " * are required field(s)."), 400);
		}
	}

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
		if (
			isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["paymentMode"]) && $postData["paymentMode"] != '' && isset($postData["areaCode"]) && $postData["areaCode"] != '' && isset($postData['cityCode']) && $postData['cityCode'] != ""
			&& isset($postData['discount'])  && $postData['discount'] != "" && isset($postData['subTotal'])  && $postData['subTotal'] != "" && isset($postData['deliverySlotCode'])  && $postData['deliverySlotCode'] != "" && isset($postData['shippingCharges'])  && $postData['shippingCharges'] != ""
			&& isset($postData['gstAmount'])  && $postData['gstAmount'] != "" && isset($postData['gstPer'])  && $postData['gstPer'] != "" && isset($postData['packagingCharges'])  && $postData['packagingCharges'] != ""
		) {
			$clientCode = $postData["clientCode"];
			$timeStamp = date("Y-m-d h:i:s");
			$totalamount = 0;
			if (isset($postData['couponCode'])) {
				$couponCode = $postData["couponCode"];
			} else {
				$couponCode = "";
			}
			$isOnlinePayment = false;
			$paymentStatus = 'PNDG';
			if ($postData['paymentMode'] == 'COD') {
				$orderStatus = "PND";
			} else {
				$isOnlinePayment = true;
				$orderStatus = "PND";
			}
			$transactionId = "";
			if (isset($postData['transactionId']) && $postData['transactionId'] != "") $transactionId = $postData['transactionId'];
			$totalamount = ($postData['subTotal'] - $postData['discount']) + $postData['gstAmount'] + $postData['shippingCharges'] + $postData['packagingCharges'];
			$vendorCode = "myvegiz";
			$getClientDetails = $this->GlobalModel->selectQuery("clientmaster.name,clientmaster.emailId,clientmaster.mobile,clientmaster.code", "clientmaster", array("clientmaster.code" => $postData['clientCode']));
			if ($getClientDetails != false) {
				$clientData = $getClientDetails->result_array()[0];
				$name = $clientData['name'];
				$email = $clientData['emailId'] ?? mailId;
				$mobile = $clientData['mobile'];
				$clientCode = $clientData['code'];
				if ($name != "" && $mobile != "" && $clientCode != "") {
					$tableName = "clientcarts";
					$orderColumns = array("productmaster.id,productmaster.code,productmaster.taxPercent,productmaster.hsnCode,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productmaster.isActive,clientcarts.quantity,clientcarts.code as cartCode,clientcarts.product,clientcarts.price,clientcarts.sellingUnit,clientcarts.rateQuantity,productratelineentries.isMainVariant");
					$cond = array('clientcarts' . ".clientCode" => $clientCode, 'productratelineentries.cityCode' => $postData['cityCode'], "productmaster.isActive" => 1, "productratelineentries.isMainVariant" => 1);
					$orderBy = array('clientcarts' . ".id" => 'DESC');
					$join = array('productmaster' => 'clientcarts.productCode=productmaster.code', "productratelineentries" => 'productmaster.code=productratelineentries.productCode');
					$joinType = array('productmaster' => 'inner', 'productratelineentries' => 'inner');
					$clientCartList = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
					if ($clientCartList != false) {
						
					$flat="";
                    $landmark="";					
					$addressData = $this->GlobalModel->selectQuery("clientprofile.*", "clientprofile", array("clientprofile.isActive" => "1", "clientprofile.isSelected" => "1", "clientprofile.clientCode" => $clientCode));
					if ($addressData != false) {
						$addData = $addressData->result_array()[0];
						$flat = $addData['flat'];
                        $landmark=$addData['landMark'];						
					}				
						
						$insertArr = array(
							"clientCode" => $postData["clientCode"],
							"cityCode" => $postData['cityCode'],
							"paymentMode" => $postData["paymentMode"],
							"paymentStatus" => "PNDG",
							"couponCode" => $couponCode,
							"discount" => $postData["discount"],
							"subTotal" => $postData["subTotal"],
							"gstPer" => $postData['gstPer'],
							"gst" => $postData['gstAmount'],
							"shippingCharges" => $postData["shippingCharges"],
							"address" => $flat.",".$landmark.",".$postData["address"],
							"areaCode" => $postData["areaCode"],
							"phone" => $postData["phone"],
							"orderStatus" => $orderStatus,
							"totalPrice" => $totalamount,
							"deliverySlotCode" => $postData['deliverySlotCode'],
							"packagingCharges" => $postData['packagingCharges'],
							"isActive" => 1,
							'editDate' => $timeStamp
						);
						if (isset($postData['latitude']) && $postData['latitude'] != "") $insertArr['latitude'] = $postData['latitude'];
						if (isset($postData['longitude']) && $postData['longitude'] != "") $insertArr['longitude'] = $postData['longitude'];
						//$orderCode = 'ORDER' . rand(99, 99999);
						$orderCode = 'ORDERVG';
						$insertResult = $this->GlobalModel->addWithoutYear($insertArr, 'ordermaster', $orderCode);
						//echo $this->db->last_query();
						if ($insertResult != 'false') {
							//update status at firestore
							$this->firestore->update_order_status($insertResult, $orderStatus);
							$orderMasterArray = array();
							$ordData = array();
							if ($isOnlinePayment) {
								$orderid = $insertResult;  
								$cashfreeResult = $this->cashfreepayment->payment($orderid, $totalamount, "INR", $clientCode, $email, $mobile, $name);
								log_message("error", " cashfree response => " . trim(stripslashes(json_encode($cashfreeResult))));
								if (array_key_exists("message", $cashfreeResult)) {
									return $this->response(array("status" => "300", "message" => "Payment for this order is not generated. Please try again", "submessage" => $cashfreeResult['message']), 200);
								} else {
									log_message("error", " cashfree response => " . trim(stripslashes(json_encode($cashfreeResult))));
									$orderMasterArray['paymentLink'] = $cashfreeResult['payments']['url'];
									$orderMasterArray['notify_url'] = CASHFREE_WEBHOOK_URL;
									//$orderMasterArray['secretKey'] = CASHFREE_CLIENT_SECRET; 
     
									//$orderMasterArray['paymentOrderId'] = $cashfreeResult['order_id'];
									$orderMasterArray['paymentOrderId'] = $cashfreeResult['cf_order_id'];
									$orderMasterArray['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
									$orderMasterArray['finalAmount'] = $totalamount;

									$ordData['paymentOrderId'] = $cashfreeResult['cf_order_id'];  
									$ordData['paymentOrderToken'] = $cashfreeResult['payment_session_id'];
								}
							}
							$orderMasterArray['orderCode'] = $insertResult;
							if ($couponCode != "") {
								if ((isset($postData['vendorOfferCode'])) && $postData['vendorOfferCode'] != "") {
									if ((isset($postData['decidedExisitingLimit'])) && $postData['decidedExisitingLimit'] > 1) {
										$this->use_User_Coupon("update", $postData['vendorOfferCode'], $couponCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
									} else {
										$this->use_User_Coupon("add", $postData['vendorOfferCode'], $couponCode, $clientCode, $postData['decidedExisitingLimit'], $vendorCode);
									}
								}
							}
							sleep(1);
							$clientCartList = $clientCartList->result_array();
							for ($i = 0; $i < sizeof($clientCartList); $i++) {
								$product = $clientCartList[$i]['product'];

								$split_product = explode("##", $product);
								$productCode = $split_product[0];
								$productUnit = $split_product[1];
								$productUnitID = $split_product[2];
								$productSellingQty = $split_product[3];
								$qty = $clientCartList[$i]["quantity"];
                                $originalPrice=$clientCartList[$i]['price'];
								//$originalPrice = $clientCartList[$i]["productSellingPrice"];
								$taxPercent = $clientCartList[$i]["taxPercent"];
								$taxAmount = round($originalPrice * ($taxPercent / 100), 2);
								$price = ($clientCartList[$i]['price'] + $taxAmount); //$cart_items[$i]["price"];
								$amount = ($price * $qty);
								$totalamount += $amount;

								//$amount = ($clientCartList[$i]["productSellingPrice"] * $clientCartList[$i]["quantity"]);
								$data = array(
									"orderCode" => $insertResult,
									"productCode" => $productCode,
									"weight" => $productSellingQty,
									"productUom" => $productUnit,
									"productUomId" => $productUnitID,
									"productPrice" => $price,
									"quantity" => $qty,
									"totalPrice" => number_format($amount, 2, '.', ''),
									"taxPercent" => $taxPercent,
									'taxAmount' => $taxAmount,
									"isActive" => 1
								);

								$orderLineResult = $this->GlobalModel->addWithoutCode($data, 'orderlineentries');
								if ($orderLineResult != 'false' && $isOnlinePayment == false) {
									$this->GlobalModel->deleteForeverFromField("code", $clientCartList[$i]["cartCode"], "clientcarts");
								}
								if (!empty($ordData)) {
									$this->GlobalModel->doEdit($ordData, 'ordermaster', $insertResult);
								}
							}
							if ($isOnlinePayment) {
								$msg = "Order (" . $insertResult . ") created successfully. Please continue to make the payment...";
							} else {
								$msg = "Order (" . $insertResult . ") Placed Successfully";
							}

							return $this->response(array("status" => "200", "message" => $msg, "result" => $orderMasterArray), 200);
						} else {
							$this->response(array("status" => "400", "message" => " Opps...! Something went wrong please try again."), 200);
						}
					} else {
						$this->response(array("status" => "400", "message" => 'cart is empty'), 200);
					}
				} else {
					$this->response(array("status" => "300", "message" => "Please complete your profile first"), 200);
				}
			} else {
				$this->response(array("status" => "300", "message" => "Invalid Client Code"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function use_User_Coupon($actionType, $vendorOfferCode, $couponCode, $clientCode, $decidedExisitingLimit, $vendorCode)
	{
		if ($actionType == "add") {
			$data['vendorofferCode'] = $vendorOfferCode;
			$data['couponCode'] = $couponCode;
			$data['clientCode'] = $clientCode;
			$data['decidedExisitingLimit'] = $decidedExisitingLimit;
			$data['vendorCode'] = $vendorCode;
			$data['addID'] = $clientCode;
			$data['addIP'] = $_SERVER['REMOTE_ADDR'];
			$data['usedDate'] = date('Y-m-d H:i:s');
			$data['isActive'] = 1;
			$insert = $this->GlobalModel->addWithoutYear($data, 'couponusesdetail', 'CUDS');
			if ($insert != 'false') {
				return true;
			} else {
				return false;
			}
		} else {
			$data['decidedExisitingLimit'] = $decidedExisitingLimit;
			$data['editID'] = $clientCode;
			$data['editIP'] = $_SERVER['REMOTE_ADDR'];
			$data['usedDate'] = date('Y-m-d H:i:s');

			$where['clientCode'] = $clientCode;
			$where['vendorCode'] = $vendorCode;
			$where['couponCode'] = $couponCode;
			$where['vendorofferCode'] = $vendorOfferCode;

			$this->db->where($where);
			$this->db->update('couponusesdetail', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			} else {
				return false;
			}
		}
	}

	// start get OrderList by ClientCode
	public function getOrderList_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '') {

			$clientCode = $postData["clientCode"];   
			$tableName = "ordermaster";
			$orderColumns = array("ordermaster.code as orderCode,vegitablestorelocation.latitude as sourceLat,vegitablestorelocation.longitude as sourceLong,ordermaster.deliveryBoyCode as deliveryBoyCode,usermaster.name as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong,ordermaster.shippingCharges as deliveryCharges,ordermaster.latitude as destLat,ordermaster.longitude as destLong,ordermaster.paymentmode,ordermaster.address,ordermaster.phone,ordermaster.totalPrice as orderTotalPrice,ordermaster.addDate as orderDate, orderstatusmaster.statusName as orderStatus, paymentstatusmaster.statusName as paymentStatus,ordermaster.cityCode,ordermaster.couponCode,ordermaster.discount,ordermaster.subTotal,ordermaster.gst,ifnull(ordermaster.gstPer,0) as gstPer,ifnull(ordermaster.packagingCharges,0) as packagingCharges");
			$cond = array('ordermaster' . ".clientCode" => $clientCode, 'ordermaster.isActive' => 1,'ordermaster.isDelete' => 0);
			$orderBy = array('ordermaster' . ".id" => 'DESC');
			$join = array('vegitablestorelocation' => 'ordermaster' . '.cityCode=' . 'vegitablestorelocation' . '.cityCode', 'usermaster' => 'ordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code', 'orderstatusmaster' => 'ordermaster' . '.orderStatus=' . 'orderstatusmaster' . '.statusSName', 'paymentstatusmaster' => 'ordermaster' . '.paymentStatus=' . 'paymentstatusmaster' . '.statusSName');
			$joinType = array('vegitablestorelocation' => 'left', 'usermaster' => 'left', 'orderstatusmaster' => 'inner', 'paymentstatusmaster' => 'inner');
			$resultQuery = $this->GlobalModel->selectQuery($orderColumns, $tableName, $cond, $orderBy, $join, $joinType);
			if ($resultQuery) {
				$clientOrderList = $resultQuery->result_array();
				$totalOrders = sizeof($clientOrderList);
				for ($i = 0; $i < sizeof($clientOrderList); $i++) {
					$cityCode = $clientOrderList[$i]['cityCode'];
					$linetableName = "orderlineentries";
					$lineorderColumns = array("orderlineentries.productCode,orderlineentries.weight,orderlineentries.productUom,orderlineentries.productUomId,orderlineentries.productPrice ,orderlineentries.quantity,orderlineentries.totalPrice as productTotalPrice,productmaster.code as pcode,productmaster.productName,orderlineentries.taxPercent,orderlineentries.taxAmount,orderlineentries.productUomId"); //,productratelineentries.code as variantsCode"); 
					$linecond = array("orderlineentries.orderCode" => $clientOrderList[$i]['orderCode']);
					$lineorderBy = array('orderlineentries' . ".id" => 'ASC');
					$linejoin = array('productmaster' => 'orderlineentries.productCode=productmaster.code'); //,'productratelineentries' => 'productmaster.code=productratelineentries.productCode');
					$linejoinType = array('productmaster' => 'inner'); //,'productratelineentries' => 'inner');
					$orderProductRes = $this->GlobalModel->selectQuery($lineorderColumns, $linetableName, $linecond, $lineorderBy, $linejoin, $linejoinType);
					if ($orderProductRes) {
						$orderProductList = $orderProductRes->result_array();
						//echo sizeof($orderProductList);
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
							//product rates 
							$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
							$tbl = 'productratelineentries';
							$cndt = ['productratelineentries.code' => $orderProductList[$j]["productUomId"]];
							$ordby = ['productratelineentries.id' => 'ASC'];
							$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
							if ($rate_result) {
								$rates = [];
								foreach ($rate_result->result_array() as $rs) {

									$product =  $orderProductList[$j]['pcode'] . '##' .  $orderProductList[$j]['productName'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
									//in cart list 

									$isInCart1 = false;
									$cartQuantity1 = 0;
									$cartCode1 = "";
									if ($clientCode != "") {
										$tableName2 = "clientcarts";
										$orderColumns2 = array("clientcarts.*");
										$cond2 = array('clientcarts' . ".productCode" => $orderProductList[$j]['pcode'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
										$orderBy2 = array('clientcarts' . ".id" => 'DESC');
										$join2 = array();
										$joinType2 = array();
										$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

										if ($clientcarts) {
											$isInCart1 = true;
											$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
											$cartCode1 = $clientcarts->result_array()[0]['code'];
										}
									}

									$taxCalulate = round($rs['sellingPrice'] * ($orderProductList[$j]['taxPercent'] / 100), 2);
									$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
									$taxCalulateRegular = round($rs['regularPrice'] * ($orderProductList[$j]['taxPercent'] / 100), 2);
									$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
									$discount = '';
									if ($rs['regularPrice'] != null) {
										$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
									}
									$rates[] = [
										'variantsCode' => $rs['code'],
										'cityCode' => $rs['cityCode'],
										'sellingUnit' => $rs['sellingUnit'],
										'quantity' => number_format($rs['quantity'], 0, '.', ''),
										'productStatus' => $rs['productStatus'],
										'sellingPrice' => $sellingPrice_tax,
										//'sellingActualPrice'=>$rs['sellingPrice'],
										'regularPrice' => $regularPrice_tax,
										//'regularActualPrice'=>$rs['regularPrice'],
										'productDiscount' => $discount,
										'isMainVariant' => $rs['isMainVariant'],
										'isInCart' => $isInCart1,
										'cartQuantity' => $cartQuantity1,
										'cartCode' => $cartCode1
									];
								}
								$rateArray = $rates;
							}
							$orderProductList[$j]['rate_variants'] = $rateArray;
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

	public function updateFirebaseId_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["firebaseId"]) && $postData["firebaseId"] != '') {
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
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}
	// End update firebaseId

	// Get Product list by keyword 
	public function searchProductByKeyword_post()
	{
		$postData = $this->post();
		if (isset($postData["keyword"]) && $postData["keyword"] != '' && isset($postData["offset"]) && $postData["offset"] != '' && isset($postData['cityCode']) && $postData["cityCode"] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "") {
			$mainCategoryCode = $postData["mainCategoryCode"];
			$keyword = $postData["keyword"];
			$product_offset = $postData["offset"];
			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$product_limit = 10;
			$condition2 = array('isActive' => 1);
			$totalProduct = sizeof($this->ApiModel->selectData('productmaster', '', '', $condition2)->result());

			if ($totalProduct) {
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,ifnull(productratelineentries.regularPrice,0) as regularPrice,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode");
				$cond = array('productmaster' . '.isActive' => 1, "productratelineentries.cityCode" => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode,"productratelineentries.isActive" =>1,"productratelineentries.isDelete" =>0,"productratelineentries.isMainVariant" =>1);
				$orderBy = array('productmaster.tagCode' => 'DESC', 'productmaster.id' => 'ASC');
				$join = array("productratelineentries" => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode');
				$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left');
				$like = array('productmaster.productName' => $keyword . '~both');
				$groupBy = array("productmaster.code");
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, $like, $product_limit, $product_offset, $groupBy);
				//echo $this->db->last_query();
				if ($resultQuery) {
					$limitProduct_result = $resultQuery->result_array();
					for ($j = 0; $j < sizeof($limitProduct_result); $j++) {

						//is In Cart
						$limitProduct_result[$j]['isInCart'] = false;
						$limitProduct_result[$j]['cartQuantity'] = 0;
						$limitProduct_result[$j]['isInWishlist'] = false;
						if ($clientCode != "") {
							$tableName1 = "clientwishlist";
							$orderColumns1 = array("clientwishlist.*");
							$cond1 = array('clientwishlist' . ".productCode" => $limitProduct_result[$j]['code'], 'clientwishlist' . ".clientCode" => $clientCode);
							$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
							$join1 = array();
							$joinType1 = array();
							$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
							if ($clientWishList) {
								$limitProduct_result[$j]['isInWishlist'] = true;
								$limitProduct_result[$j]['wishlistCode'] = $clientWishList->result_array()[0]['code'];
							}
						}

						$condition2 = array('productCode' => $limitProduct_result[$j]['code']);
						$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
						$imageArray = array();

						for ($img = 0; $img < sizeof($images_result); $img++) {
							array_push($imageArray, base_url() . 'uploads/product/' . $limitProduct_result[$j]['code'] . '/' . $images_result[$img]['productPhoto']);
						}
						$limitProduct_result[$j]['images'] = $imageArray;
						unset($imageArray);

						if ($limitProduct_result[$j]['productUom'] == 'PC' && $limitProduct_result[$j]['minimumSellingQuantity'] > 1) {
							$limitProduct_result[$j]['productUom'] = 'PCS';
						}

						//product rates 
						$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.regularPrice,productratelineentries.productStatus,productratelineentries.regularPrice";
						$tbl = 'productratelineentries';
						$cndt = ['productratelineentries.productCode' => $limitProduct_result[$j]['code'],'cityCode' => $postData['cityCode'],'isDelete'=>0,'isActive'=>1];
						$ordby = ['productratelineentries.code' => 'DESC'];
						$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
						if ($rate_result) {
							$rates = [];
							foreach ($rate_result->result_array() as $rs) {
								//$product =  $limitProduct_result[$j]['code'] . '##' .  $limitProduct_result[$j]['productName'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
								$product =  $limitProduct_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
								
								//in cart list 
								$isInCart1 = false;
								$cartQuantity1 = 0;
								$cartCode1 = "";
								if ($clientCode != "") {
									$tableName2 = "clientcarts";
									$orderColumns2 = array("clientcarts.*");
									$cond2 = array('clientcarts' . ".productCode" => $limitProduct_result[$j]['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
									$orderBy2 = array('clientcarts' . ".id" => 'DESC');
									$join2 = array();
									$joinType2 = array();
									$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);
                                     
									if ($clientcarts) {
										$isInCart1 = true;
										$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
										$cartCode1 = $clientcarts->result_array()[0]['code'];

										$limitProduct_result[$j]['variantsCode'] = $rs['code'];
										$limitProduct_result[$j]['quantity'] = $rs['quantity'];
										$limitProduct_result[$j]['regularPrice'] = $rs['regularPrice'];
										$limitProduct_result[$j]['sellingPrice'] = $rs['sellingPrice'];
										$limitProduct_result[$j]['sellingUnit'] = $rs['sellingUnit'];
										$limitProduct_result[$j]['isInCart'] = true;
										$limitProduct_result[$j]['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
										$limitProduct_result[$j]['cartCode'] = $clientcarts->result_array()[0]['code'];
									}
								}
								$taxCalulate = round($rs['sellingPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
								$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
								$taxCalulateRegular = round($rs['regularPrice'] * ($limitProduct_result[$j]['taxPercent'] / 100), 2);
								$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
								$discount = '';
								if ($rs['regularPrice'] != null) {
									$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
								}
								$rates[] = [
									'variantsCode' => $rs['code'],
									'cityCode' => $rs['cityCode'],
									'sellingUnit' => $rs['sellingUnit'],
									'quantity' => number_format($rs['quantity'], 0, '.', ''),
									'productStatus' => $rs['productStatus'],
									'sellingPrice' => $sellingPrice_tax,
									'regularPrice' => $regularPrice_tax,
									'productDiscount' => $discount,
									'isInCart' => $isInCart1,
									'cartQuantity' => $cartQuantity1,
									'cartCode' => $cartCode1
								];
							}
							$rateArray = $rates;
						}
						$limitProduct_result[$j]['rate_variants'] = $rateArray;
					}

					$data['products'] = $limitProduct_result;

					return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $data), 200);
				} else {
					return $this->response(array("status" => "300", "message" => "Data not found."), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["orderCode"]) && $postData["orderCode"] != '' && isset($postData["orderType"]) && $postData["orderType"] != '') {
			$userCode = $postData["clientCode"];
			$orderCode = $code = $postData["orderCode"];
			$orderType = $postData["orderType"];
			$nowdate = date('Y-m-d h:i:s');
			if ($orderType != "food") {
				$Result = $this->GlobalModel->selectDataByPND('code', $code, 'ordermaster', $userCode);
				if ($Result) {
					$deliveryBoyCode = $Result->result_array()[0]['deliveryBoyCode'];
					$grandTotal = $Result->result_array()[0]['totalPrice'];
					$data = array('orderStatus' => "CAN", "cancelledTime" => $nowdate);
					$passresult = $this->GlobalModel->doEditWithField($data, 'ordermaster', 'code', $code);
					if ($passresult == 'true') {
						//update status at firestore
						$this->firestore->update_order_status($code, "CAN");

						$dataUpCnt['orderCount'] = 0;
						$dataUpCnt['orderCode'] = null;
						$dataUpCnt['orderType'] = null;
						$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $deliveryBoyCode);
						//calcualtion and notifications
						$settingData = $this->GlobalModel->selectQuery("settings.settingValue", "settings", array("settings.code" => "SET_5"));
						if ($settingData) {
							$dbTouchPoint = $settingData->result_array()[0]['settingValue'];
							$this->GlobalModel->deleteForeverFromField('orderCode', $orderCode, 'deliveryboyearncommission');
							log_message("error", "food touchpoint added on cancel order by customer=>" . "Cumission " . $dbTouchPoint . " is added to delivery boy code : " . $deliveryBoyCode . " and order code : " . $code);

							$dbcAdd['commissionAmount'] = $dbTouchPoint;
							$dbcAdd['orderAmount'] = $grandTotal;
							$dbcAdd['commissionType'] = 'regular';
							$dbcAdd['orderCode'] = $code;
							$dbcAdd['deliveryBoyCode'] = $deliveryBoyCode;
							$dbcAdd['orderType'] = 'vegetable';
							$dbcAdd['isActive'] = 1;
							$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
						}

						return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
					} else {
						return $this->response(array("status" => "400", "message" => "Failed to cancel the order! Please Try Again."), 200);
					}
				} else {
				    return $this->response(array("status" => "400", "message" => "Unable to find or match the order"), 200);
				}
			} else {
				$Result2 = $this->GlobalModel->selectDataByIdWithEmpty($code, 'vendorordermaster');

				if ($Result2) {
					//check delivery boy and vendor accept this order
					$orderCode = $Result2->result_array()[0]['code'];
					$orderStatus = $Result2->result_array()[0]['orderStatus'];
					$grandTotal = $Result2->result_array()[0]['grandTotal'];
					$deliveryBoyCode = $Result2->result_array()[0]['deliveryBoyCode'];

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
						if ($dbCode != "") {
							$orderColumns = array("usermaster.firebase_id");
							$cond = array('usermaster' . '.isActive' => 1, "usermaster.code" => $dbCode);
							$resultDBoy = $this->GlobalModel->selectQuery($orderColumns, 'usermaster', $cond);

							if ($resultDBoy) {
								//send notification
								$DeviceIdsArr[] = $resultDBoy->result()[0]->firebase_id;
							}
						}
					}

					if ($restoFlag == 1) {

						$settingData = $this->GlobalModel->selectQuery("settings.settingValue", "settings", array("settings.code" => "SET_5"));
						if ($settingData) {
							$dbTouchPoint = $settingData->result_array()[0]['settingValue'];
							$this->GlobalModel->deleteForeverFromField('orderCode', $orderCode, 'deliveryboyearncommission');
							log_message("error", "food touchpoint added on cancel order by customer=>" . "Cumission " . $dbTouchPoint . " is added to delivery boy code : " . $deliveryBoyCode . " and order code : " . $code);

							$dbcAdd['commissionAmount'] = $dbTouchPoint;
							$dbcAdd['orderAmount'] = $grandTotal;
							$dbcAdd['commissionType'] = 'regular';
							$dbcAdd['orderCode'] = $code;
							$dbcAdd['deliveryBoyCode'] = $deliveryBoyCode;
							$dbcAdd['orderType'] = 'food';
							$dbcAdd['isActive'] = 1;
							$delboyCommission = $this->GlobalModel->addNew($dbcAdd, 'deliveryboyearncommission', 'DBEC');
						}

						$vendCode = $Result2->result_array()[0]['vendorCode'];
						$orderColumns = array("vendor.firebaseId");
						$cond = array('vendor' . '.isActive' => 1, "vendor.code" => $vendCode);
						$resultVendor = $this->GlobalModel->selectQuery($orderColumns, 'vendor', $cond);
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

					$data = array('orderStatus' => "CAN", "editDate" => $nowdate, "deliveryBoyCode" => "", 'editID' => $userCode);
					$passresult = $this->GlobalModel->doEditWithField($data, 'vendorordermaster', 'code', $code);
					if ($passresult == 'true') {
						//update status at firestore
						$this->firestore->update_order_status($code, "CAN");
						$order_status = $this->GlobalModel->selectQuery("vendororderstatusmaster.*", "vendororderstatusmaster", array("vendororderstatusmaster.statusSName" => "CAN"));
						if ($order_status && count($order_status->result_array()) > 0) {
							$order_status_record = $order_status->result()[0];
							$statusTitle = $order_status_record->messageTitle;
							#replace $ template in title 
							$statusDescription = $order_status_record->messageDescription;
							$statusDescription = str_replace("$", $orderCode, $statusDescription);
							$dataStatusChangeLine = array(
								"orderCode" => $orderCode,
								"statusPutCode" => $userCode,
								"statusLine" => 'CAN',
								"reason" => 'Canceled By customer',
								"statusTime" => date("Y-m-d h:i:s"),
								"statusTitle" => $statusTitle,
								"statusDescription" => $statusDescription,
								"isActive" => 1
							);
							$bookLineResult = $this->GlobalModel->addWithoutYear($dataStatusChangeLine, 'bookorderstatuslineentries', 'BOL');
						}
						$dbCode = $Result2->result_array()[0]['deliveryBoyCode'];
						//remove delivery boy current active order
						$dataUpCnt['orderCount'] = 0;
						$dataUpCnt['orderCode'] = null;
						$dataUpCnt['orderType'] = null;

						$delbActiveOrder = $this->GlobalModel->doEditWithField($dataUpCnt, 'deliveryBoyActiveOrder', 'deliveryBoyCode', $dbCode);

						return $this->response(array("status" => "200", "message" => "Order Cancelled Successfully"), 200);
					} else {
						return $this->response(array("status" => "300", "message" => "Failed to cancel the order! Please Try Again."), 200);
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


	public function maintenance_get()
	{
		$resultData = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.settingName' => 'maintenance_mode'));
		$maintenance_mode['maintenance'] = $resultData->result_array()[0]['settingValue'];
		$maintenance_mode['messageTitle'] = $resultData->result_array()[0]['messageTitle'];
		$maintenance_mode['messageDescription'] = $resultData->result_array()[0]['messageDescription'];
		return $this->response(array("status" => "200", "result" => $maintenance_mode), 200);
	}


	// Get Category list
	public function grocerycategoryList_post()
	{
		$postData = $this->post();
		if (isset($postData["offset"]) && $postData["offset"] != '') {
			$category_offset = $postData["offset"];
			$category_limit = 10;
			$condition = array('mainCategoryCode' => 'MCAT_2', 'isActive' => 1);
			$totalRecords = sizeof($this->ApiModel->selectData('categorymaster', '', '', $condition)->result());
			$category_result = $this->ApiModel->selectData('categorymaster', $category_limit, $category_offset, $condition)->result_array();
			if ($category_result) {
				for ($i = 0; $i < sizeof($category_result); $i++) {
					$category_result[$i]['categoryImage'] = base_url() . 'uploads/category/' . $category_result[$i]['code'] . '/' . $category_result[$i]['categoryImage'];
				}
				$data['categories'] = $category_result;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 400);
		}
	}
	//Ends Get Category list

	public function getCityByLatLong_post()
	{
		$postData = $this->post();

		if (isset($postData["latitude"]) && $postData["latitude"] != '' && isset($postData["longitude"]) && $postData["longitude"] != '') {
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
							return $this->response(array("status" => "300", "message" => "Area is not serviceable."), 200);
						}
					} else {
						return $this->response(array("status" => "300", "message" => "Area is not serviceable."), 200);
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
				$insertResult = $this->GlobalModel->addWithoutCode($dataProfile, 'clientprofile');
				if ($insertResult != 'false') {
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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
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
							'city' => $cityName??"",
							'area' => $place??"",
							'id' => $row->id,
							'address' => $row->address??"",
							'latitude' => $row->latitude,
							'longitude' => $row->longitude,
							'cityCode' => $row->cityCode,
							'areaCode' => $row->areaCode,
							'addressType' => $row->addressType == null ? "home" : $row->addressType,
							'flat' => $row->flat??"",
							'directionToReach'=>$row->directionToReach??"",
							'landMark' => $row->landMark??"",
							'isSelected' => $row->isSelected,
							'state'=>$row->state??"",
							'pincode'=>$row->pincode??"",
							'country'=>'India',
						);
						array_push($addressList, $data);
					}
				}
				$result['addresses'] = $addressList;
				// $data['addresses']=$Result->result_array(); 
				return $this->response(array("status" => "200", "message" => "Addresses List.", "result" => $result), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data Found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 400);
		}
	}

	public function addClientAddress_post()
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
				$insertResult = $this->GlobalModel->addWithoutCode($dataProfile, 'clientprofile');
				if ($insertResult != 'false') {
					$Result = $this->GlobalModel->selectQuery('clientprofile.*', 'clientprofile', array('clientprofile.clientCode' => $clientCode, 'clientprofile.isSelected' => 1));
					if ($Result) {
						$data['addressdata'] = $Result->result_array();
						return $this->response(array("status" => "200", "message" => "Location added Successfully.", "result" => $data), 200);
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
	}

	public function updateClientAddress_post()
	{
		$postData = $this->post();
		if (isset($postData["id"]) && $postData["id"] != '' && isset($postData["clientCode"]) && $postData["clientCode"] != '' && isset($postData["address"]) && $postData["address"] != '' && isset($postData["latitude"]) && $postData["latitude"] != '' && isset($postData["longitude"]) && $postData["longitude"] != '' && isset($postData["addressType"]) && $postData["addressType"] != '' && isset($postData["flat"]) && $postData["flat"] != '' && isset($postData["landMark"]) && $postData["landMark"] != '' && isset($postData["areaCode"]) && $postData["areaCode"] != '' && isset($postData["cityCode"]) && $postData["cityCode"] != '') {
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
					"directionToReach"=>$postData["directionToReach"],
				];

				$updateResult = $this->GlobalModel->doEditWithField($dataProfile, 'clientprofile', 'id', $id);
                //echo $this->db->last_query();
				if ($updateResult) { 
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
		if (isset($postData["id"]) && $postData["id"] != '' && isset($postData["clientCode"]) && $postData["clientCode"] != '') {
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
		if (isset($postData['clientCode']) && $postData['clientCode'] != "" && isset($postData['addressId']) && $postData['addressId'] != "") {
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
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
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

	//Application first alert
	public function getAppAlertMessage_get()
	{
		$Result = $this->GlobalModel->selectQuery('appalert.title,appalert.description', 'appalert', array('appalert.isActive' => 1), array(), array(), array(), array(), 1);
		if ($Result) {
			$responseRsult["appAlert"] = $Result->result_array();
			return $this->response(array("status" => "200", "message" => "Alert Message", "result" => $responseRsult), 200);
		} else {
			return $this->response(array("status" => "300", "message" => "Alert not available"), 200);
		}
	} //end alert


	//vegee coupon list
	public function getVegeeCouponList_get()
	{
		$today = date('Y-m-d');
		$condition = array('vegitableoroffer.isActive' => 1);
		$extraCondition = " ('" . $today . "' between vegitableoroffer.startDate and vegitableoroffer.endDate)";
		$Result = $this->GlobalModel->selectQuery('vegitableoroffer.coupanCode as couponCode,vegitableoroffer.offerType,vegitableoroffer.discount,vegitableoroffer.minimumAmount,vegitableoroffer.perUserLimit,vegitableoroffer.termsAndConditions,vegitableoroffer.capLimit,vegitableoroffer.flatAmount,vegitableoroffer.startDate,vegitableoroffer.endDate', 'vegitableoroffer', $condition, array(), array(), array(), array(), "", "", array(), $extraCondition);
		if ($Result) {
			$recordsCount = sizeof($Result->result_array());
			$data = array();
			foreach ($Result->result_array() as $r) {
				$data[] = $r;
			}
			$res['offersList'] = $data;
			return $this->response(array("status" => "200", "totalRecords" => $recordsCount, "message" => "Coupon List", "result" => $res), 200);
		}
		return $this->response(array("status" => "300", "msg" => "No Data found", "query" => $this->db->last_query()), 200);
	}

	public function getVegeeCoupanDetails_post()
	{
		$postData = $this->post();
		if (isset($postData['clientCode']) && $postData['clientCode'] != "" && isset($postData["coupanCode"]) && $postData['coupanCode'] != "" && isset($postData['cartAmount']) && $postData['cartAmount'] != "") {
			$couponCode = $postData["coupanCode"];
			$cartAmount = $postData['cartAmount'];
			$clientCode = $postData['clientCode'];
			$vendorCode = 'myvegiz';
			$today = date('Y-m-d');
			$limit = 0;

			$condition = array('vegitableoroffer.coupanCode' => $couponCode, 'vegitableoroffer.isActive' => 1, "vegitableoroffer.perUserLimit>=" => 1);
			$extraCondition = "( '" . $today . "' between vegitableoroffer.startDate and vegitableoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer', $condition, array(), array(), array(), array(), "", "", array(), $extraCondition);
			if ($Result) {
				$r = $Result->result_array()[0];
				$vendorOfferCode = $r['code'];
				$minimumAmount = $r['minimumAmount'];
				$offerType = $r['offerType'];
				$couponCode = $r['coupanCode'];
				if ($r['offerType'] == 'flat') {
					$discount = $r['flatAmount'];
				} else {
					$discount = $r['discount'];
				}
				$capLimit = $r['capLimit'];
				$termsAndConditions = $r['termsAndConditions'];
				$perUserLimit = $r['perUserLimit'];

				$ar['vendorOfferCode'] = $vendorOfferCode;
				$ar['minimumAmount'] = $minimumAmount;
				$ar['offerType'] = $offerType;
				$ar['couponCode'] = $couponCode;
				$ar['discount'] = $discount;
				$ar['capLimit'] = $capLimit;
				$ar['termsAndConditions'] = $termsAndConditions;
				$ar['perUserLimit'] = $perUserLimit;

				$data['couponDetails'] = $ar;

				if ($minimumAmount <= $cartAmount) {
					//check used or not 
					$condition1 = array(
						'couponusesdetail.vendorOfferCode' => $vendorOfferCode,
						'couponusesdetail.couponCode' => $couponCode,
						'couponusesdetail.clientCode' => $clientCode,
						"couponusesdetail.vendorCode" => $vendorCode
					);
					$clientUseCouponResult = $this->GlobalModel->selectQuery('couponusesdetail.*', 'couponusesdetail', $condition1);
					if ($clientUseCouponResult) {
						$useCoupon = $clientUseCouponResult->result_array()[0];
						$userLimit = $useCoupon['decidedExisitingLimit'];
						if ($userLimit < $perUserLimit) {
							$nextLimit = $userLimit + 1;
							return $this->response(array("status" => "200", "nextLimit" => $nextLimit, "message" => $couponCode . " and save every time you order", "result" => $data), 200);
						} else {
							return $this->response(array("status" => "300", "message" => "Coupon Already Used"), 200);
						}
					} else {
						//not single use return coupon
						return $this->response(array("status" => "200", "nextLimit" => 1, "message" => $couponCode . " and save every time you order", "result" => $data), 200);
					}
				} else {
					return $this->response(array("status" => "300", "message" => "Amount should be greater than " . $minimumAmount), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Invalid Coupon Code."), 200);
			}
		} else {
			return $this->response(array("status" => "400", "msg" => " * are required field(s)."), 400);
		}
	}

	// Start Support Number
	public function getSupportContact_get()
	{
		$settingResult = $this->GlobalModel->selectQuery('settings.*', 'settings', array('settings.isActive' => 1));
		if ($settingResult) {
			$foodContact = $settingResult->result_array()[5]['settingValue'];
			$vegeeContact = $settingResult->result_array()[6]['settingValue'];
			return $this->response(array("status" => "200", "message" => "For Support", "foodContact" => $foodContact, "vegeeContact" => $vegeeContact), 200);
		} else {
			return $this->response(array("status" => "200", "message" => "For Support", "foodContact" => "9373747055", "vegeeContact" => "9373747055"), 200);
		}
	}
	// End

	public function calculateDistanceDeliveryCharges($storeLatitude, $storeLongitude, $clientCode, $cartAmount)
	{
		$shortestdistance = 0;
		$charges = 0;
		$cityCode = "";
		$clientLatitude = $clientLongitude = '';
		$addressLatitudeData = $this->GlobalModel->selectQuery("clientprofile.latitude,clientprofile.longitude,clientprofile.cityCode", "clientprofile", array("clientprofile.isActive" => "1", "clientprofile.isSelected" => "1", "clientprofile.clientCode" => $clientCode));
		if ($addressLatitudeData != false) {
			$data = $addressLatitudeData->result_array()[0];
			$clientLatitude = $data['latitude'];
			$clientLongitude = $data['longitude'];
			$cityCode = $data['cityCode'];
		}

		//	echo $storeLatitude ."  ".$storeLongitude." Customer -> ".$clientLatitude." ".$clientLongitude;
		// 		$client = $this->GlobalModel->selectQuery("clientmaster.cityCode", "clientmaster", array("clientmaster.code" => $clientCode));
		// 		if ($client) $cityCode = $client->result()[0]->cityCode;
		$deliveryCharge = $perKmCharges = $minimumKmForFixedCharges = $minimumChargesForFixedKm  = 0;
		$deliveryChargeCurrency = 'INR';
		$chargesResult = $this->GlobalModel->selectQuery('deliverycomissionandcharges.*', 'deliverycomissionandcharges', array('deliverycomissionandcharges.cityCode' => $cityCode, 'deliverycomissionandcharges.forWhichService' => 'customer_vegee_grocery'));
		$fixedDeliveryFlag = 0;
		if ($chargesResult) {
			$charge = $chargesResult->result()[0];

			if ($charge->isFixedChargesFlag == 1) {
				if ($cartAmount > $charge->minOrderAmountForFixedCharge || $cartAmount == 0) {
					$deliveryCharge = 0;
				} else {
					$deliveryCharge = $charge->fixedChargesOrCommission;
				}
				$fixedDeliveryFlag = 1;
			} else {
				$minimumKmForFixedCharges  = $charge->minimumKmForFixedCharges;
				$minimumChargesForFixedKm = $charge->minimumChargesForFixedKm;
				$perKmCharges = $charge->perKmCharges;
			}
		}

		if ($clientLatitude != "" && $clientLongitude != "") {
			$arrayDist = array();
			$url = "https://maps.googleapis.com/maps/api/directions/json?destination=$storeLatitude,$storeLongitude&mode=driving&origin=$clientLatitude,$clientLongitude&key=" . PLACE_API_KEY;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response = curl_exec($ch);
			curl_close($ch);
			$response_all = json_decode($response, TRUE);
			if (!empty($response_all['routes'])) {
				foreach ($response_all['routes'] as $res1 => $val) {
					foreach ($val['legs'] as $keys => $value) {
						$distance = round($value['distance']['value'] / 1000);
						array_push($arrayDist, $distance);
					}
				}
				$mindistance = min($arrayDist);
				if ($fixedDeliveryFlag == 1) {
					$shortestdistance = $mindistance;
					$charges = $deliveryCharge;
				} else {
					if ($mindistance > $minimumKmForFixedCharges) {
						$finalDistance = $mindistance - $minimumKmForFixedCharges;
						$shortestdistance = $mindistance;
						$shippingCharges1 = $minimumChargesForFixedKm;
						$shippingCharges1 = $shippingCharges1 + ($finalDistance * $perKmCharges);
						$charges = $shippingCharges1;
					} else {
						$shortestdistance = $mindistance;
						$charges = $minimumChargesForFixedKm;
					}
				}
			}
		}
		$result['shortestdistance'] = $shortestdistance . ' Kms';
		$result['charges'] = $charges;
		return $result;
	}

	public function getCouponDetails($couponCode, $clientCode, $cartAmount)
	{
		$today = date('Y-m-d');
		$ar = array();
		if ($couponCode != "") {
			$condition = array('vegitableoroffer.coupanCode' => $couponCode, 'vegitableoroffer.isActive' => 1, "vegitableoroffer.perUserLimit>=" => 1);
			$extraCondition = "( '" . $today . "' between vegitableoroffer.startDate and vegitableoroffer.endDate)";
			$Result = $this->GlobalModel->selectQuery('vegitableoroffer.*', 'vegitableoroffer', $condition, array(), array(), array(), array(), "", "", array(), $extraCondition);
			//	echo $this->db->last_query();
			if ($Result != false) {
				$r = $Result->result_array()[0];
				$ar['OfferCode'] = $r['code'];
				$ar['minimumAmount'] =  $r['minimumAmount'];
				$ar['offerType'] = $r['offerType'];
				$ar['couponCode'] = $r['coupanCode'];
				if ($r['offerType'] == 'flat') {
					$ar['discount'] = $r['flatAmount'];
				} else {
					$ar['discount'] = $r['discount'];
				}
				$ar['capLimit'] = $r['capLimit'];
				$ar['termsAndConditions'] = $r['termsAndConditions'];
				$ar['perUserLimit'] = $r['perUserLimit'];
			} else {
				$ar = array();
				$returnArr['nextLimit'] = "";
				$returnArr['message'] = "Invalid Coupon Code";
				$returnArr['submessage'] = "";
			}
		} else {
			$ar =  new StdClass;
		}

		$saved_rs = 0;
		if ($couponCode != "" && $cartAmount > 0 && !empty($ar)) {
			if ($cartAmount >= $r['minimumAmount']) {
				$condition1 = array(
					'couponusesdetail.couponCode' => $couponCode,
					'couponusesdetail.clientCode' => $clientCode,
				);
				$clientUseCouponResult = $this->GlobalModel->selectQuery('couponusesdetail.*', 'couponusesdetail', $condition1);
				if ($r['offerType'] == 'flat') {
					$saved_rs = $r['flatAmount'];
				} else {
					$saved_rs = round(($cartAmount * $r['discount']) / 100, 2);
					if ($saved_rs > $r['capLimit']) {
						$saved_rs = round($r['capLimit']);
					}
				}


				if ($clientUseCouponResult) {
					$useCoupon = $clientUseCouponResult->result_array()[0];
					$userLimit = $useCoupon['decidedExisitingLimit'];
					if ($userLimit < $r['perUserLimit']) {
						$nextLimit = $userLimit + 1;
						$returnArr['nextLimit'] = $nextLimit;
						$returnArr['message'] = $ar['couponCode'] . " coupon applied..";
						$returnArr['submessage'] = "You saved ₹ " . $saved_rs;
					} else {
						$returnArr['nextLimit'] = "";
						$returnArr['message'] = "Coupon Already Used";
						$returnArr['submessage'] = "";
					}
				} else {
					$nextLimit = 1;
					$returnArr['nextLimit'] = $nextLimit;
					$returnArr['message'] = $ar['couponCode'] . " coupon applied..";
					$returnArr['submessage'] = "You saved ₹ " . $saved_rs;
				}
			} else {
				$returnArr['nextLimit'] = "";
				$returnArr['message'] = "Amount should be greater than or equal to minimum amount";
				$returnArr['submessage'] = "";
			}
		} else {
			$returnArr['nextLimit'] = "";
			$returnArr['message'] = "";
			$returnArr['submessage'] = "";
		}
		$returnArr['totalAmount'] = number_format($cartAmount, 2, '.', '');
		$returnArr['discountApplied'] = number_format($saved_rs, 2, '.', '');
		$returnArr['offerDetails'] = $ar;
		return $returnArr;
	}

	public function verifyPayment_post()
	{
		$postData = $this->post();
		$random = rand(0, 999);
		$random = date('his') . $random;
		if (isset($postData['clientCode']) && $postData['clientCode'] != "" && isset($postData['paymentOrderId']) && $postData['paymentOrderId'] != "" && isset($postData['paymentOrderToken']) && $postData['paymentOrderToken'] != "" && isset($postData['orderCode']) && $postData['orderCode'] != "") {
			//get table record
			$table = "ordermaster";
			$clms = "ordermaster.paymentOrderId,ordermaster.paymentOrderToken,ordermaster.paymentStatus";
			$cond = array("ordermaster.code" => $postData['orderCode']);
			$result = $this->GlobalModel->selectQuery($clms, $table, $cond);
			if ($result != false) {
				$payment = $result->result_array()[0];
				if ($payment['paymentOrderId'] == $postData['paymentOrderId']) {

					if ($payment['paymentStatus'] == "PID") {
						$message = "Payment successfull";
						return $this->response(array("status" => 200, "message" => $message, "paymentId" => $postData['paymentOrderId'], "payment_status" => "SUCCESS"), 200);
					}
					$cashFreeResult = $this->cashfreepayment->getOrderStatus($postData['paymentOrderId']);
					log_message("error", " payments response => " . trim(stripslashes(json_encode($cashFreeResult))));
					if (array_key_exists("payments", $cashFreeResult) && !empty($cashFreeResult["payments"])) {
						$payments = $cashFreeResult['payments'];
						$payments = $payments[0];
						$txtStatus = $payments['payment_status'];
						if ($payments['is_captured'] == true  && $txtStatus = "SUCCESS") {
							$this->db->query("delete from clientcarts where clientCode='" . $postData['clientCode'] . "'");
							$data['orderStatus'] = 'PND';
							$data['paymentStatus'] = "PID";
							$data['isActive'] = 1;
							$dataNoti = array('title' => 'Payment Successful', 'message' => $postData['orderCode'] . ' Order placed successfully.', 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
							//$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
							$errorCode = '200';
							$message = "Payment successfull";
						} else {
							$data['orderStatus'] = 'CAN';
							$data['paymentStatus'] = "RJCT";
							$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled', 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
							$message = "Your payment has been marked as failed. Cannot place your order";
							$errorCode = '300';
						}
						$this->GlobalModel->doEdit($data, 'ordermaster', $postData['orderCode']);
						if ($payments['is_captured'] == true  && $txtStatus = "SUCCESS") {
							$this->assignorder->allocate_delivery_boy_to_order($postData['orderCode']);
						}
						$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
						return $this->response(array("status" => $errorCode, "message" => $message, "paymentId" => $postData['paymentOrderId'], "payment_status" => $payments['payment_status']), 200);
					} else if (array_key_exists('exception', $cashFreeResult)) {
						$data['orderStatus'] = 'CAN';
						$data['paymentStatus'] = "RJCT";
						$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled', 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
						$result1 = $this->GlobalModel->doEdit($data, 'ordermaster', $postData['orderCode']);
						//$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
						return $this->response(array("status" => "300", "message" => "b Your payment has been marked as failed. Cannot place your order due to " . $cashFreeResult['exception'], "paymentId" => $postData['paymentOrderId'], "payment_status" => "FAILED"), 200);
					} else {
						$data['orderStatus'] = 'CAN';
						$data['paymentStatus'] = "RJCT";
						$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled', 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
						$result1 = $this->GlobalModel->doEdit($data, 'ordermaster', $postData['orderCode']);
						//$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
						return $this->response(array("status" => "300", "message" => "c Your payment has been marked as failed. Cannot place your order", "paymentId" => $postData['paymentOrderId'], "payment_status" => "FAILED"), 200);
					}
				} else {
					$data['orderStatus'] = 'CAN';
					$data['paymentStatus'] = "FAIL";
					$result = $this->GlobalModel->doEdit($data, 'ordermaster', $postData['orderCode']);
					$dataNoti = array('title' => 'Payment Failed', 'message' => $postData['orderCode'] . ' Order Cancelled', 'unique_id' => $postData['orderCode'], 'random_id' => $random, 'type' => 'VendorOrder');
					//$this->sendCustomerNotification($postData['clientCode'], $dataNoti);
					return $this->response(array("status" => "300",  "message" => "a Your payment has been marked as failed. Cannot place your order. Please tray again after some time", "paymentId" => $postData['paymentOrderId'], "payment_status" => "FAILED"), 200);
				}
			} else {
				return $this->response(array("status" => "300", "message" => "Unable to process payment"), 200);
			}
		}
		return $this->response(array("status" => "400", "message" => "* are required fields"), 200);
	}

	public function deleteCartItem_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			$this->db->query("DELETE FROM clientcarts where clientCode='" . $postData['clientCode'] . "'");
			return $this->response(array("status" => "200", "message" => "Cart items removed successfully"), 200);
		} else {
			return $this->response(array("status" => "400", "message" => "* are required fields"), 200);
		}
	}

	public function getmostPurchasedProducts_post()
	{
		$postData = $this->post();
		if (isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != '' && isset($postData['cityCode']) && $postData['cityCode'] != "" && isset($postData['mainCategoryCode']) && $postData['mainCategoryCode'] != "" && isset($postData['offset']) && $postData['offset'] != "") {
			$clientCode = isset($postData['clientCode']) ? $postData['clientCode'] : "";
			$mainCategoryCode = $postData['mainCategoryCode'];
			$orderColumns = array("productmaster.id,productmaster.code,productmaster.taxPercent,productmaster.hsnCode,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,ifnull(productratelineentries.regularPrice,0) as regularPrice,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,count(orderlineentries.id) as productCount,productratelineentries.code as variantsCode,productratelineentries.isMainVariant");
			$condition1 = array("ordermaster.orderStatus" => 'DEL', "productmaster.isActive" => 1, 'productratelineentries.cityCode' => $postData['cityCode'], 'productmaster.mainCategoryCode' => $mainCategoryCode,"productratelineentries.isActive" =>1,"productratelineentries.isDelete" =>0);
			$orderBy = array('count(orderlineentries.id)' => 'DESC', 'productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC', 'productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
			$join1 = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode', 'orderlineentries' => 'orderlineentries.productCode=productratelineentries.productCode', 'ordermaster' => 'ordermaster.code=orderlineentries.orderCode');
			$joinType1 = array('productratelineentries' => 'inner', 'tagmaster' => 'inner', 'orderlineentries' => 'inner', 'ordermaster' => 'inner');
			$groupByColumn1 = array('orderlineentries.productCode');
			$totalProducta = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $condition1, $orderBy, $join1, $joinType1, array(), '', '', $groupByColumn1, '');
			$limit = 100;
			$data = array();
			$offset = $this->input->post('offset');
			if ($totalProducta) {
				$totalProduct = sizeof($totalProducta->result_array());
				//echo $totalProduct;
				foreach ($totalProducta->result_array() as $total) {
					$totalArr = $total;
					$totalArr['isInCart'] = false;
					$totalArr['cartQuantity'] = 0;
					$totalArr['cartCode'] = "";
					$totalArr['isInWishlist'] = false;
					if ($clientCode != "") {
						$tableName2 = "clientcarts";
						$orderColumns2 = array("clientcarts.*");
						$cond2 = array('clientcarts' . ".productCode" => $total['code'], 'clientcarts' . ".clientCode" => $clientCode);
						$orderBy2 = array('clientcarts' . ".id" => 'DESC');
						$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2);
						if ($clientcarts) {
							$totalArr['isInCart'] = true;
							$totalArr['cartQuantity'] = $clientcarts->result_array()[0]['quantity'];
							$totalArr['cartCode'] = $clientcarts->result_array()[0]['code'];
						}

						$tableName1 = "clientwishlist";
						$orderColumns1 = array("clientwishlist.*");
						$cond1 = array('clientwishlist' . ".productCode" => $totalArr['code'], 'clientwishlist' . ".clientCode" => $clientCode);
						$orderBy1 = array('clientwishlist' . ".id" => 'DESC');
						$join1 = array();
						$joinType1 = array();
						$clientWishList = $this->GlobalModel->selectQuery($orderColumns1, $tableName1, $cond1, $orderBy1, $join1, $joinType1);
						if ($clientWishList) {
							$totalArr['isInWishlist'] = true;
							$totalArr['wishlistCode'] = $clientWishList->result_array()[0]['code'];
						}
					}
					$condition2 = array('productCode' => $total['code']);
					$images_result = $this->ApiModel->selectData('productphotos', '', '', $condition2)->result_array();
					$imageArray = array();
					for ($img = 0; $img < sizeof($images_result); $img++) {
						array_push($imageArray, base_url() . 'uploads/product/' . $total['code'] . '/' . $images_result[$img]['productPhoto']);
					}
					if ($totalArr['productUom'] == 'PC' && $totalArr['minimumSellingQuantity'] > 1) {
						$totalArr['productUom'] = 'PCS';
					}


					/*$taxCalulate = round($totalArr['productSellingPrice'] * ($totalArr['taxPercent']/100),2);
					
					$sellingPrice_with_tax = number_format($totalArr['productSellingPrice'] + $taxCalulate,2,'.','');
					$totalArr['sellingPrice'] = $sellingPrice_with_tax;
		
					$discount='';
					$taxCalulateRegular = round($totalArr[$j]['regularPrice'] * ($totalArr[$j]['taxPercent']/100),2);
					$regularPrice_with_tax = number_format($totalArr[$j]['regularPrice'] + $taxCalulateRegular,2,'.','');
					$totalArr[$j]['regularPrice'] = $regularPrice_with_tax;
					if($totalArr[$j]['regularPrice']!=0){
						$discount = round(((($totalArr[$j]['regularPrice'] - $totalArr[$j]['sellingPrice']) / ($totalArr[$j]['regularPrice'])) * 100)).' %';
					}
					$totalArr[$j]['productDiscount'] = $discount;*/


					//product rates 
					$clms = "productratelineentries.code,productratelineentries.cityCode,productratelineentries.sellingUnit,productratelineentries.quantity,productratelineentries.sellingPrice,productratelineentries.productStatus,productratelineentries.regularPrice,productratelineentries.isMainVariant";
					$tbl = 'productratelineentries';
					$cndt = ['productratelineentries.productCode' => $totalArr['code'],'isDelete'=>0,'isActive'=>1];
					$ordby = ['productratelineentries.code' => 'DESC'];
					$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
					if ($rate_result) {
						$rates = [];
						foreach ($rate_result->result_array() as $rs) {

							$product =  $totalArr['code'] . '##' .  $totalArr['productName'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
							//in cart list 

							$isInCart1 = false;
							$cartQuantity1 = 0;
							$cartCode1 = "";
							if ($clientCode != "") {
								$tableName2 = "clientcarts";
								$orderColumns2 = array("clientcarts.*");
								$cond2 = array('clientcarts' . ".productCode" => $totalArr['code'], 'clientcarts' . ".clientCode" => $clientCode, 'clientcarts' . ".product" => $product);
								$orderBy2 = array('clientcarts' . ".id" => 'DESC');
								$join2 = array();
								$joinType2 = array();
								$clientcarts = $this->GlobalModel->selectQuery($orderColumns2, $tableName2, $cond2, $orderBy2, $join2, $joinType2);

								if ($clientcarts) {
									$isInCart1 = true;
									$cartQuantity1 = $clientcarts->result_array()[0]['quantity'];
									$cartCode1 = $clientcarts->result_array()[0]['code'];
								}
							}
							$taxCalulate = round($rs['sellingPrice'] * ($totalArr['taxPercent'] / 100), 2);
							$sellingPrice_tax = number_format($rs['sellingPrice'] + $taxCalulate, 2, '.', '');
							$taxCalulateRegular = round($rs['regularPrice'] * ($totalArr['taxPercent'] / 100), 2);
							$regularPrice_tax = number_format($rs['regularPrice'] + $taxCalulateRegular, 2, '.', '');
							$discount = '';
							if ($rs['regularPrice'] != null) {
								$discount = round((($regularPrice_tax - $sellingPrice_tax) / $regularPrice_tax) * 100, 2) . ' %';
							}
							$rates[] = [
								'variantsCode' => $rs['code'],
								'cityCode' => $rs['cityCode'],
								'sellingUnit' => $rs['sellingUnit'],
								'quantity' => number_format($rs['quantity'], 0, '.', ''),
								'productStatus' => $rs['productStatus'],
								'sellingPrice' => $sellingPrice_tax,
								//'sellingActualPrice'=>$rs['sellingPrice'],
								'regularPrice' => $regularPrice_tax,
								//'regularActualPrice'=>$rs['regularPrice'], 
								'productDiscount' => $discount,
								'isInCart' => $isInCart1,
								'cartQuantity' => $cartQuantity1,
								'cartCode' => $cartCode1
							];
						}
						$rateArray = $rates;
					}
					$totalArr['rate_variants'] = $rateArray;
					$totalArr['images'] = $imageArray;
					unset($imageArray);
					$data['products'][] = $totalArr;
				}
				$response = $data;
				return $this->response(array("status" => "200", "totalRecords" => $totalProduct, "result" => $response), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "Data not found."), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => "Required field(s)."), 200);
		}
	}

	public function getActiveOrderTrackingDetails_post()
	{
		$postData = $this->post();
		if (isset($postData["clientCode"]) && $postData["clientCode"] != '') {
			$orderBy = array("vendorordermaster.id" => "ASC");
			$extraCondition =  " (vendorordermaster.orderStatus NOT IN ('CAN','RJT','DEL')) and (deliveryBoyCode!='' or deliveryBoyCode IS NOT NULL) AND (trackingPort!='' or trackingPort IS NOT NULL)";
			$Records = $this->GlobalModel->selectQuery('vendorordermaster.code,vendorordermaster.deliveryBoyCode,vendorordermaster.trackingPort', 'vendorordermaster', array('vendorordermaster.isActive' => 1), $orderBy, array(), array(), array(), '', '', array(), $extraCondition);
			$ar = [];
			if ($Records) {
				foreach ($Records->result_array() as $s) {
					$orderCode = $s['code'];
					$deliveryBoyCode = $s['deliveryBoyCode'];
					$res['orderCode'] = $orderCode;
					$res['deliveryBoyCode'] = $deliveryBoyCode;
					$res['url'] = "https://myvegiz.com";
					$res['port'] = $s['trackingPort'];
					$dbRecords = $this->GlobalModel->selectQuery('usermaster.*', 'usermaster', array('usermaster.code' => $deliveryBoyCode, 'usermaster.isActive' => 1));
					if ($dbRecords) {
						foreach ($dbRecords->result_array() as $db) {
							$res['deliveryBoyDetails'] = $db;
						}
					}
					$res['trackingDetails'] = [];
					if (file_exists('assets/order_tracking/' . $orderCode . '.json')) {
						$jsonString = file_get_contents('assets/order_tracking/' . $orderCode . '.json');
						$fileData = json_decode($jsonString, true);
						if (!empty($fileData[0])) {
							$res['trackingDetails'] = $fileData;
						}
					}
					$array[] = $res;
				}
				return $this->response(array("status" => "200", "message" => "Data found.", "result" => $array), 200);
			} else {
				return $this->response(array("status" => "300", "message" => "No data found",), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
		}
	}

	public function getOrdersForTracking_post()
	{
		$post = $this->post();
		if (array_key_exists('clientCode', $post) && $post['clientCode'] != "") {
			$orders = [];
			$sel = "vendorordermaster.code,vendorordermaster.latitude as destinationLat,vendorordermaster.longitude as destinationLng,vendor.latitude as sourceLat,vendorordermaster.code,vendorordermaster.latitude as destinationLat,vendorordermaster.longitude as destinationLng,vendor.latitude as sourceLat,vendor.longitude as sourceLng,vendorordermaster.deliveryBoyCode,usermaster.username as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong";
			$tbl = 'vendorordermaster';
			$cond = ["vendorordermaster.clientCode" => $post['clientCode']];
			$ord = ["vendorordermaster.id" => "DESC"];
			$join = ['vendor' => 'vendor.code=vendorordermaster.vendorCode', 'usermaster' => 'vendorordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code'];
			$jType = ['vendor' => 'inner', 'usermaster' => 'left'];
			$groupBy = $like = [];
			$limit = $offset = "";
			$extraCondition = " ((vendorordermaster.orderStatus IN ( 'PRE', 'RFP', 'PUP' )) or (vendorordermaster.reachStatus IN ('RCH') AND vendorordermaster.orderStatus  NOT IN ('DEL')))";
			$foodOrders =  $this->GlobalModel->selectQuery($sel, $tbl, $cond, $ord, $join, $jType, $like, $limit, $offset, $groupBy, $extraCondition);
			if ($foodOrders) {
				$foodOrders = $foodOrders->result();
				foreach ($foodOrders as $f) {
					$ary = [
						"ordeCode"	=>	$f->code,
						"orderType"	=> 	"food",
						"srcLat"	=>	$f->sourceLat,
						"srcLng"	=> 	$f->sourceLng,
						"desLat"	=>	$f->destinationLat,
						"deslng"	=> 	$f->destinationLng,
						"deliveryBoyCode" => $f->deliveryBoyCode,
						"dBoyName" => $f->dBoyName,
						"dBoyLat" => $f->dBoyLat,
						"dBoyLong" => $f->dBoyLong
					];
					array_push($orders, $ary);
				}
			}

			$srcLat = "16.691307";
			$srcLng = "74.244865";
			$sel = "ordermaster.deliveryBoyCode as deliveryBoyCode,usermaster.username as dBoyName,usermaster.latitude as dBoyLat,usermaster.longitude as dBoyLong,ordermaster.code,ordermaster.cityCode,ordermaster.latitude as destinationLat,ordermaster.longitude as destinationLng,citymaster.latitude as srcLat,citymaster.longitude as srcLng";
			$tbl = 'ordermaster';
			$cond = ["ordermaster.clientCode" => $post['clientCode']];
			$ord = ["ordermaster.id" => "DESC"];
			$join = ['citymaster' => 'citymaster.code=ordermaster.cityCode', 'usermaster' => 'ordermaster' . '.deliveryBoyCode=' . 'usermaster' . '.code'];
			$jType = ['citymaster' => 'inner', 'usermaster' => 'left'];
			$groupBy = $like = [];
			$limit = $offset = "";
			//$extraCondition = " ((ordermaster.orderStatus IN ( 'PRE', 'RFP', 'PUP' )) or (ordermaster.reachStatus = 'RCH' AND ordermaster.orderStatus != 'DEL'))";
			$extraCondition = " ((ordermaster.orderStatus IN ( 'PRE', 'RFP', 'PUP' )) or (ordermaster.reachStatus IN ('RCH') AND ordermaster.orderStatus  NOT IN ('DEL')))";
			$veggrcOrders =  $this->GlobalModel->selectQuery($sel, $tbl, $cond, $ord, $join, $jType, $like, $limit, $offset, $groupBy, $extraCondition);
			$q = $this->db->last_query();
			//print_r($q);
			if ($veggrcOrders) {
				$veggrcOrders = $veggrcOrders->result();
				foreach ($veggrcOrders as $v) {
					$ary = [
						"ordeCode"	=>	$v->code,
						"orderType"	=> 	"vegetable",
						"srcLat"	=>	$v->srcLat ?? $srcLat,
						"srcLng"	=> 	$v->srcLng ?? $srcLng,
						"desLat"	=>	$v->destinationLat,
						"deslng"	=> 	$v->destinationLng,
						"deliveryBoyCode" => $v->deliveryBoyCode,
						"dBoyName" => $v->dBoyName,
						"dBoyLat" => $v->dBoyLat,
						"dBoyLong" => $v->dBoyLong
					];
					array_push($orders, $ary);
				}
			}

			if (!empty($orders))
				return $this->response(array("status" => "200", "message" => "Data found", "data" => $orders), 200); //, "query" => $q
			else
				return $this->response(array("status" => "300", "message" => "No data found", "data" => $orders), 200); //, "query" => $q
		}
		return $this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
	}

	public function trackOrderById_post()
	{
		$postData = $this->post();
		if ($postData["clientCode"] != '' && $postData['orderCode'] != "" && $postData['orderType'] != "") {
			$orderCode = $postData['orderCode'];
			$track = [];

			$ar = $this->orderCheckStatus($orderCode, "PND", "Pending");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "PLC", "Placed");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "PRE", "Preparing");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "PRO", "Processing");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "RFP", "Ready For Pickup");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "PUP", "On the way");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "RCH", "Reached");
			$track[] = $ar;

			$ar = $this->orderCheckStatus($orderCode, "DEL", "Delivered");
			$track[] = $ar;


			if ($postData['orderType'] == "food") {
				$sel = "vendorordermaster.code,vendorordermaster.latitude as destinationLat,vendorordermaster.longitude as destinationLng,vendor.latitude as sourceLat,vendor.longitude as sourceLng";
				$tbl = 'vendorordermaster';
				$cond = ["vendorordermaster.clientCode" => $postData['clientCode'], "vendorordermaster.code" => $postData['orderCode']];
				$ord = ["vendorordermaster.id" => "DESC"];
				$join = ['vendor' => 'vendor.code=vendorordermaster.vendorCode'];
				$jType = ['vendor' => 'inner'];
				$groupBy = $like = [];
				$limit = 1;
				$offset = "";
				$extraCondition = "";
				$foodOrders =  $this->GlobalModel->selectQuery($sel, $tbl, $cond, $ord, $join, $jType, $like, $limit, $offset, $groupBy, $extraCondition);
				if ($foodOrders) {
					$f = $foodOrders->result()[0];
					$data['orderData'] = [
						"ordeCode"		=>	$f->code,
						"orderType"		=> 	"food",
						"srcLat"		=>	$f->sourceLat,
						"srcLng"		=> 	$f->sourceLng,
						"desLat"		=>	$f->destinationLat,
						"deslng"		=> 	$f->destinationLng,
						"trackingData"	=> 	$track
					];
					return $this->response(array("status" => "200", "message" => "Data found.", "result" => $data), 200);
				}
				return $this->response(array("status" => "300", "message" => "Either order has been delivered or no order found"), 200);
			} else {
				$srcLat = "16.691307";
				$srcLng = "74.244865";

				$sel = "ordermaster.code,ordermaster.cityCode,ordermaster.latitude as destinationLat,ordermaster.longitude as destinationLng,citymaster.latitude as srcLat,citymaster.longitude as srcLng";
				$tbl = 'ordermaster';
				$cond = ["ordermaster.clientCode" => $postData['clientCode'], "ordermaster.code" => $postData['orderCode']];
				$ord = ["ordermaster.id" => "DESC"];
				$join = ['citymaster' => 'citymaster.code=ordermaster.cityCode'];
				$jType = ['citymaster' => 'inner'];
				$groupBy = $like = [];
				$limit = 1;
				$offset = "";
				$extraCondition = "";
				$veggrcOrders =  $this->GlobalModel->selectQuery($sel, $tbl, $cond, $ord, $join, $jType, $like, $limit, $offset, $groupBy, $extraCondition);
				if ($veggrcOrders) {
					$v = $veggrcOrders->result()[0];
					$data['orderData'] = [
						"ordeCode"		=>	$v->code,
						"orderType"		=> 	"vegetable",
						"srcLat"		=>	$v->srcLat ?? $srcLat,
						"srcLng"		=> 	$v->srcLng ?? $srcLng,
						"desLat"		=>	$v->destinationLat,
						"deslng"		=> 	$v->destinationLng,
						"trackingData"	=> 	$track
					];
					return $this->response(array("status" => "200", "message" => "Data found.", "result" => $data), 200);
				}
				return $this->response(array("status" => "300", "message" => "Either order has been delivered or no order found"), 200);
			}
		} else {
			$this->response(array("status" => "400", "message" => " * are required field(s)."), 200);
		}
	}

	public function orderCheckStatus($orderCode, $status, $fullStatus)
	{
		$orderColumns = "bookorderstatuslineentries.*";
		$table = "bookorderstatuslineentries";
		$orderBy = array("bookorderstatuslineentries.id" => "ASC");
		$extraCondition = '';
		if ($status == "CAN") {
			$condition = array("bookorderstatuslineentries.orderCode" => $orderCode);
			$extraCondition =  " (bookorderstatuslineentries.statusLine IN ('CAN','RJT'))";
		} else {
			$condition = array("bookorderstatuslineentries.statusLine" => $status, "bookorderstatuslineentries.orderCode" => $orderCode);
		}
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy, array(), array(), array(), '', '', array(), $extraCondition);
		$ar = [];
		if ($Records) {
			$statusLines = $Records->result_array();
			foreach ($statusLines as $s) {
				$ar['status'] = $status;
				$ar['statusTitle'] = $fullStatus;
				$ar['statusDescription'] = $s['statusDescription'];
				$ar['statusDate'] = date('D, d M Y', strtotime($s['statusTime']));
				$ar['statusTime'] = date('h:i A', strtotime($s['statusTime']));
				$ar['statusDateTime'] = date('D, d M Y h:i A', strtotime($s['statusTime']));
				break;
			}
		} else {
			$ar['status'] = $status;
			$ar['statusTitle'] = $fullStatus;
			$ar['statusDescription'] = "";
			$ar['statusDate'] = "";
			$ar['statusTime'] = "";
			$ar['statusDateTime'] = "";
		}
		return $ar;
	}

	public function deletionurl_post()
	{
		$getData = $this->post();
		if (isset($getData['userCode']) && $getData['userCode'] != "") {
			$condition = array('usermaster.code' => $getData['userCode'], 'usermaster.isActive' => 1, "usermaster.isDelete" => 0);
			$extraCondition = "";
			$Result = $this->GlobalModel->selectQuery('usermaster.*', 'usermaster', $condition, array(), array(), array(), array(), "", "", array(), $extraCondition);

			if ($Result) {
				$data = array(
					'isActive' => 0,
					'isDelete' => 1, 
					'deleteDate' => date('Y-m-d H:i:s')
				);

				$resultData = $this->GlobalModel->doEdit($data, 'usermaster', $getData['userCode']);
				if ($resultData == true) {
					return $this->response(array("status" => "200", "message" => "User deleted successfully."), 200);
				}
				return $this->response(array("status" => "300", "msg" => "Something went to wrong."), 200);
			} else {
				return $this->response(array("status" => "300", "msg" => "No Data found"), 200);
			}
		} else {
			return $this->response(array("status" => "400", "message" => "Required fields not found."), 400);
		}             
	}
}
