<?php
require(APPPATH . '/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

date_default_timezone_set('Asia/Kolkata');
class TestApi extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('ApiModel');
		$this->load->library('notificationlibv_3');
		$this->load->library('sendemail');
		$this->load->library('passwordlib');
		$this->load->library('firestore');
	}

	public function send_notification_restaurant_get()
	{
		$vendor = $this->db->query("select * from vendor where ownerContact = '9960746060'")->row_array();
		if ($vendor) {

			$random = rand(0, 999);
			$datamsg = array("title" => 'Notification', "message" => ' Your new notifcitio ' . time(), "order_id" => "123", "random_id" => $random);

			$dataArr = array();
			$dataArr['device_id'] = [$vendor['firebaseId']];
			$dataArr['message'] = "Test"; //Message which you want to send
			$dataArr['title'] = $datamsg['title'];
			$dataArr['order_id'] = $datamsg['order_id'];
			$dataArr['random_id'] = $datamsg['random_id'];
			$dataArr['type'] = 'order';

			$notification['device_id'] = [$vendor['firebaseId']];
			$notification['message'] = $datamsg['message']; //Message which you want to send
			$notification['title'] = $datamsg['title'];
			$notification['order_id'] = $datamsg['order_id'];
			$notification['random_id'] = $datamsg['random_id'];
			$notification['type'] = 'order';
			$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);

			return $this->response(array("status" => "200", "message" => "Notification send", "token" => $vendor['firebaseId'], "notify_result" => $notify), 200);
		}

		return $this->response(array("status" => "300", "message" => "Faled to send notification"), 200);
	}

	public function send_notification_admin_get()
	{
		$vendor = $this->db->query("select * from usermaster where id='1'")->row_array();
		if ($vendor) {
			$random = rand(0, 999);
			$datamsg = array("title" => 'Notification', "message" => ' Your new notification ' . time(), "order_id" => "123", "random_id" => $random);

			$dataArr = array();
			$dataArr['device_id'] = [$vendor['websiteFirebaseToken']];
			$dataArr['message'] = "Test"; //Message which you want to send
			$dataArr['title'] = $datamsg['title'];
			$dataArr['order_id'] = $datamsg['order_id'];
			$dataArr['random_id'] = $datamsg['random_id'];
			$dataArr['type'] = 'order';

			$notification['device_id'] = [$vendor['websiteFirebaseToken']];
			$notification['message'] = $datamsg['message']; //Message which you want to send
			$notification['title'] = $datamsg['title'];
			$notification['order_id'] = $datamsg['order_id'];
			$notification['random_id'] = $datamsg['random_id'];
			$notification['type'] = 'order';
			$notify = $this->notificationlibv_3->pushNotification($dataArr, $notification);

			return $this->response(array("status" => "200", "message" => "Notification send", "token" => $vendor['websiteFirebaseToken'], "notify_result" => $notify), 200);
		}

		return $this->response(array("status" => "300", "message" => "Faled to send notification"), 200);
	}

	 

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

					$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingPrice as sellingPrice,productratelineentries.productStatus,productratelineentries.sellingUnit,productratelineentries.cityCode,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode,productratelineentries.quantity,ifnull(subcategorymaster.id,0) as subCategoryId");
					$cond = array('productmaster' . ".productCategory" => $category_result[$i]['categorySName'], 'productmaster' . ".isActive" => 1, 'productratelineentries.cityCode' => $cityCode, "productratelineentries.isMainVariant" => 1, "productratelineentries.isActive" => 1, "productratelineentries.isDelete" => 0);
					//$orderBy = array('productmaster.productCategory' => 'DESC', 'productmaster.subCategoryCode' => 'DESC', 'productmaster.tagCode' => 'DESC', 'productmaster.id' => 'DESC');
					$orderBy = array('productmaster.productCategory' => 'DESC', 'subcategorymaster.id' => 'ASC', 'productmaster.id' => 'DESC');
					$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode', 'subcategorymaster' => 'subcategorymaster.code=productmaster.subCategoryCode');
					$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left', 'subcategorymaster' => 'left');
					$like = array();
					$groupBy = array();
					//$groupBy = ["productmaster.tagCode"];
					$p_result = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, $like, $category_limit, "", $groupBy);
					//echo $this->db->last_query();
					if ($p_result) {
						$product_result = $p_result->result_array();

						function customSort($a, $b) {
							$tagTitleA = !empty($a['tagTitle']);
							$tagTitleB = !empty($b['tagTitle']);
				
							if ($tagTitleA != $tagTitleB) {
								return $tagTitleA ? -1 : 1;
							}
				
							return $a['regularPrice'] <=> $b['regularPrice'];
						}
				
						// Reorder function
						function reorderData($data) {
							$result = [];
							$groupedData = [];
				
							foreach ($data as $item) {
								$subCategoryId = $item['subCategoryId'];
				
								if (!isset($groupedData[$subCategoryId])) {
									$groupedData[$subCategoryId] = [];
								}
				
								$groupedData[$subCategoryId][] = $item;
							}
				
							foreach ($groupedData as $subCategoryId => $products) {
								usort($products, 'customSort');
								$result = array_merge($result, $products);
							}
				
							return $result;
						}
						
						$reorderedData = reorderData($product_result);

						return $this->response(array("status" => "200", "result" => $reorderedData), 200);

						$prd = $products_by_category = array();

						foreach ($product_result as $p) {
							$cat = $p['subCategoryId'];
							if (!isset($products_by_category[$cat])) {
								$products_by_category[$cat] = array();
							}
							$products_by_category[$cat][] = $p;
						}

						foreach ($products_by_category as $k => $v) {
							$prds = $v;

							$tagTitleProducts = [];
							$otherProducts = [];

							foreach ($prds as $product) {
								if (!empty($product['tagTitle'])) {
									$tagTitleProducts[] = $product;
								} else {
									$otherProducts[] = $product;
								}
							}
							// Merge the arrays with tagTitle products at the top
							$sortedProducts = array_merge($tagTitleProducts, $otherProducts);
							$prd = $sortedProducts;
						}

						$proData['products'] = $prd;
						$category_resulta[$i]['productList'] = $proData;
					}
				}
				$data['categories'] = $category_resulta;
				return $this->response(array("status" => "200", "totalRecords" => $totalRecords, "result" => $data), 200);
			} else {
				$data['categories'] = array();
				return $this->response(array("status" => "300", "message" => "Data not found."), 400);
			}
		} else {
			$this->response(array("status" => "400", "message" => "* fields are required"), 200);
		}
	}

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
				$orderColumns = array("productmaster.id,productmaster.code,productmaster.hsnCode,productmaster.taxPercent,productmaster.productName,productmaster.productDescription,productmaster.minimumSellingQuantity,productmaster.productUom,productmaster.productRegularPrice,productratelineentries.sellingPrice as productSellingPrice,productratelineentries.productStatus,productratelineentries.cityCode,ifnull(productratelineentries.regularPrice,0) as regularPrice,productratelineentries.sellingUnit,productratelineentries.quantity,productmaster.isActive,ifnull(productmaster.tagCode,'') as tagCode,ifnull(tagmaster.tagTitle,'') as tagTitle,ifnull(tagmaster.tagColor,'') as tagColor,productratelineentries.code as variantsCode,productratelineentries.isMainVariant,subcategorymaster.id as subCategoryId");
				$cond = array('productmaster' . ".productCategory" => $categorySName, 'productmaster' . '.isActive' => 1, 'productratelineentries.cityCode' => $cityCode, 'productratelineentries.isMainVariant' => 1, 'productmaster' . '.isDelete' => 0, 'productratelineentries.isDelete' => 0, 'productratelineentries.isActive' => 1);
				$orderBy = array('productmaster.productCategory' => 'DESC', 'subcategorymaster.id' => 'ASC', 'productmaster.id' => 'DESC');
				$join = array('productratelineentries' => 'productmaster.code=productratelineentries.productCode', 'tagmaster' => 'tagmaster.code=productmaster.tagCode', 'subcategorymaster' => 'subcategorymaster.code=productmaster.subCategoryCode');
				$joinType = array('productratelineentries' => 'inner', 'tagmaster' => 'left', 'subcategorymaster' => 'left');
				$groupBy = array();
				//$groupBy = array("productmaster.tagCode");
				$resultQuery = $this->GlobalModel->selectQuery($orderColumns, 'productmaster', $cond, $orderBy, $join, $joinType, array(), $product_limit, $product_offset);
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
						$cndt = ['productratelineentries.productCode' => $limitProduct_result[$j]['code'], 'cityCode' => $cityCode, 'isDelete' => 0, 'isActive' => 1];
						$ordby = ['productratelineentries.productCode' => 'DESC'];
						$rate_result = $this->GlobalModel->selectQuery($clms, $tbl, $cndt, $ordby);
						if ($rate_result) {
							$rates = [];
							foreach ($rate_result->result_array() as $rs) {

								$product =  $limitProduct_result[$j]['code'] . '##' . $rs['sellingUnit'] . '##' . $rs['code'] . '##' . $rs['quantity'];
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

					$prd = $products_by_category = array();

					foreach ($limitProduct_result as $p) {
						$cat = $p['subCategoryId'];
						if (!isset($products_by_category[$cat])) {
							$products_by_category[$cat] = array();
						}
						$products_by_category[$cat][] = $p;
					}

					foreach ($products_by_category as $k => $v) {
						$prds = $v;
						$tagTitleProducts = [];
						$otherProducts = [];
						foreach ($prds as $product) {
							if (!empty($product['tagTitle'])) {
								$tagTitleProducts[] = $product;
							} else {
								$otherProducts[] = $product;
							}
						}
						// Merge the arrays with tagTitle products at the top
						$sortedProducts = array_merge($tagTitleProducts, $otherProducts);
						$prd = $sortedProducts;
					}
					$data['products'] = $prd;
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
	}
}
