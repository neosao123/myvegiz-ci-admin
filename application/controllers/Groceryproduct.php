<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Groceryproduct extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form', 'url', 'html');
		$this->load->library('form_validation');
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->load->model('ApiModel');
		$this->load->model('Testing');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key]['code'])) {
			redirect('Admin/login', 'refresh');
		}
	}

	public function listRecords()
	{
		$data['error'] = $this->session->flashdata('response');
		// $data['productmaster']=$this->GlobalModel->selectData('productmaster');
		// $data['categorymaster']=$this->GlobalModel->selectActiveData('categorymaster');
		$table_name = 'productmaster';
		$orderColumns = array("productmaster.*");
		$cond = array('productmaster' . '.isDelete' => 0, 'productmaster' . '.isActive' => 1, 'productmaster' . '.mainCategoryCode' => 'MCAT_2');
		$data['productmaster'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$table_name = 'categorymaster';
		$orderColumns = array("categorymaster.*");
		$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
		$data['categorymaster'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/groceryproduct/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function getGroceryProductList()
	{
		$productCode = $this->input->post('productCode');
		$search = $this->input->post("search")['value'];
		$productCategory = $this->input->post('categoryCode');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		if ($startDate != '') {
			$startDate = date('Y-m-d', strtotime($startDate));
			$endDate = date('Y-m-d', strtotime($endDate));
			$startDate = $startDate . " 00:00:00";
			$endDate = $endDate . " 23:59:59";
		}
		$tableName = "productmaster";
		$orderColumns = array("productmaster.*,categorymaster.categoryName,categorymaster.categorySName,categorymaster.isActive as catActive,subcategorymaster.subcategoryName");
		$condition = array('productmaster.code' => $productCode, 'productmaster.productCategory' => $productCategory, 'categorymaster.isActive' => 1, 'productmaster.mainCategoryCode' => 'MCAT_2');
		$orderBy = array('productmaster' . '.id' => 'DESC');
		$joinType = array('categorymaster' => 'inner', 'subcategorymaster' => 'left');
		$join = array('categorymaster' => 'categorymaster.categorySName=productmaster.productCategory', 'subcategorymaster' => 'subcategorymaster.code=productmaster.subcategoryCode');
		$groupByColumn = array();
		$limit = $this->input->post("length");
		$offset = $this->input->post("start");
		$extraCondition = " productmaster.isDelete=0 OR productmaster.isDelete IS NULL";
		if ($startDate != "") {
			$extraCondition = "productmaster.addDate between '" . $startDate . "' AND '" . $endDate . "' and (productmaster.isDelete = 0 OR productmaster.isDelete IS NULL)";
		}
		$like = array("productmaster.productName" => $search . "~both", "categorymaster.categoryName" => $search . "~both", "subcategorymaster.subcategoryName" => $search . "~both");
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);

		$srno = $this->input->post('start') + 1;
		if ($Records) {
			foreach ($Records->result() as $row) {
				$code = $row->code;
				$productPhoto = "";

				$tblname = 'productphotos';
				$limit = 1;
				$condData = array('isDelete' => 0, 'productCode' => $code);
				$offset = array();
				$photosData = $this->ApiModel->selectData($tblname, $limit, $offset, $condData);
				$start = '<div class="d-flex align-items-center">';
				$end = ' <h5 class="m-b-0 font-16 font-medium">' . $row->productName . '</h5></div></div>';
				foreach ($photosData->result() as $ph) {
					$path = base_url() . 'uploads/product/' . $ph->productCode . '/' . $ph->productPhoto;
					$productPhoto = '<div class="m-r-10"><img src="' . $path . '?' . time() . '" alt="user" class="circle" width="45"></div>
						<div class="">';
				}
				unset($photosData);
				$productName = $start . $productPhoto . $end;
				if ($row->isActive == 1) {
					$status = "<span class='label label-sm label-success'>Active</span>";
				}
				else {
					$status = "<span class='label label-sm label-warning'>Inactive</span>";
				}

				if ($row->isPopular == 1) {
					$popular = "<span class='label label-sm label-success'>Yes</span>";
				}
				else {
					$popular = "<span class='label label-sm label-warning'>No</span>";
				}

				$actionHtml = '<div class="btn-group">
					<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="ti-settings"></i>
					</button>
					<div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
						<a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code . '" href><i class="ti-eye"></i> Open</a>
						<a class="dropdown-item" href="' . base_url() . 'index.php/Groceryproduct/edit/' . $row->code . '"><i class="ti-pencil-alt"></i> Edit</a>
						<a class="dropdown-item  mywarning" data-seq="' . $row->code . '" id="' . $row->code . '"><i class="ti-trash" href></i> Delete</a>
					</div>
				</div>';
				$DateFormat = DateTime::createFromFormat('Y-m-d', substr($row->addDate, 0, 10));
				$Date = $DateFormat->format('d/m/Y');
				$data[] = array(
					$srno,
					$row->code,
					$productName,
					$row->categoryName,
					$row->subcategoryName,
					$row->minimumSellingQuantity,
					$row->productUom,
					$Date,
					$status,
					$popular,
					$actionHtml
				);
				$srno++;
			}
			$dataCount = sizeof($this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), '', '', '', $extraCondition)->result());
			$output = array(
				"draw" => intval($this->input->post("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
		else {
			$dataCount = 0;
			$data = array();
			$output = array(
				"draw" => intval($this->input->post("draw")),
				"recordsTotal" => $dataCount,
				"recordsFiltered" => $dataCount,
				"data" => $data
			);
			echo json_encode($output);
		}
	}

	public function add()
	{
		$table_name = 'categorymaster';
		$orderColumns = array("categorymaster.*");
		$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
		$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$table_nameForUom = 'uommaster';
		$orderColumnsForUom = array("uommaster.*");
		$condForUom = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
		$data['uommaster'] = $this->GlobalModel->selectQuery($orderColumnsForUom, $table_nameForUom, $condForUom);
		$data['citymaster'] = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/groceryproduct/add', $data);
		$this->load->view('dashboard/footer');
	}

	public function save()
	{
		$productName = trim($this->input->post("productName"));
		$productBenefit = $this->input->post("productBenefit");
		$lineCode = $this->input->post('lineCode');
		$imageData = $_FILES['images']['name'];
		//Activity Track Starts		
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' added new groceryproduct "' . $productName . '" from ' . $ip;
		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends	 
		$result = $this->GlobalModel->checkDuplicateRecord('productName', $productName, 'productmaster');
		// $condition2 = array('productName'=>$productName,'mainCategoryCode'=>'MCAT_2');
		// $result = $this->GlobalModel->checkDuplicateRecordNew($condition2,'productmaster');
		if ($result == true) {
			$data = array('error_message' => 'Duplicate Product Name');
			$table_name = 'categorymaster';
			$orderColumns = array("categorymaster.*");
			$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
			$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$table_nameForUom = 'uommaster';
			$orderColumnsForUom = array("uommaster.*");
			$condForUom = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
			$data['uommaster'] = $this->GlobalModel->selectQuery($orderColumnsForUom, $table_nameForUom, $condForUom);
			$data['citymaster'] = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/groceryproduct/add', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$this->form_validation->set_rules('productName', 'Product Name', 'required');
			$this->form_validation->set_rules('productCategory', 'Product Category Name', 'required');
			if ($this->form_validation->run() == FALSE) {
				$data['error_message'] = '* Fields are Required!';
				$table_name = 'categorymaster';
				$orderColumns = array("categorymaster.*");
				$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
				$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$table_nameForUom = 'uommaster';
				$orderColumnsForUom = array("uommaster.*");
				$condForUom = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
				$data['uommaster'] = $this->GlobalModel->selectQuery($orderColumnsForUom, $table_nameForUom, $condForUom);
				$data['citymaster'] = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
				$table_name = 'tagmaster';
				$orderColumns = array("tagmaster.*");
				$cond = array('tagmaster.isActive' => 1);
				$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
				$this->load->view('dashboard/header');
				$this->load->view('dashboard/product/add', $data);
				$this->load->view('dashboard/footer');
			}
			else {
				$images = "";
				$description = trim($this->input->post("productDescription"));
				if (strpos($description, 'class="table table-bordered" border="1"') !== false) {
					$description = str_replace('class="table table-bordered"', 'class="table table-bordered" border="1"', $description);
				}
				else {
					$description = str_replace('class="table table-bordered"', 'class="table table-bordered" border="1"', $description);
				}
				$data = array(
					'productName' => $productName,
					'productCategory' => $this->input->post("productCategory"),
					'subCategoryCode' => $this->input->post("productSubCategory"),
					'hsnCode' => $this->input->post("hsnCode"),
					'taxPercent' => '0',
					'productDescription' => $description,
					'minimumSellingQuantity' => trim($this->input->post("minimumSellingQuantity")),
					'productUom' => trim($this->input->post("productUom")),
					'productRegularPrice' => trim($this->input->post("productRegularPrice")),
					'mainCategoryCode' => 'MCAT_2',
					'addID' => $addID,
					'addIP' => $ip,
					'isActive' => $this->input->post('isActive') ? 1 : 0,
					'isPopular' => $this->input->post("isPopular")
				);
				if ($this->input->post("tagCode") == 1) {
					$data['tagCode'] = NULL;
				}
				else {
					$data['tagCode'] = $this->input->post("tagCode");
				}
				$code = $this->GlobalModel->addWithoutYear($data, 'productmaster', 'GPRDT');
				if ($code != 'false') {
					if ($imageData[0] != '') {
						if (!file_exists(FCPATH . 'uploads/product/' . $code))
							mkdir(FCPATH . 'uploads/product/' . $code, 0755, false);
						$uploadRootDir = 'uploads/';
						$uploadDir = 'uploads/product/' . $code . '/';
						if (!empty($_FILES['images']['name'])) {
							$filesCount = count($_FILES['images']['name']);
							for ($i = 0; $i < $filesCount; $i++) {
								$_FILES['file']['name'] = $_FILES['images']['name'][$i];
								$_FILES['file']['type'] = $_FILES['images']['type'][$i];
								$_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
								$_FILES['file']['error'] = $_FILES['images']['error'][$i];
								$_FILES['file']['size'] = $_FILES['images']['size'][$i];
								// File upload configuration
								$uploadPath = $uploadDir;
								$config['upload_path'] = $uploadPath;
								$config['allowed_types'] = 'jpg|jpeg|png|gif';
								$config['file_name'] = $i;
								// Load and initialize upload library
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
								// Upload file to server
								if ($this->upload->do_upload('file')) {
									// Uploaded file data
									$fileData = $this->upload->data();
									$uploadData[$i]['file_name'] = $fileData['file_name'];
									$config['image_library'] = 'gd2';
									$config['source_image'] = $uploadDir . $fileData['file_name'];
									$config['create_thumb'] = FALSE;
									$config['maintain_ratio'] = FALSE;
									$config['quality'] = '100%';
									$config['width'] = 400;
									$config['height'] = 400;
									$this->load->library('image_lib', $config);
									$this->image_lib->initialize($config);
									$this->image_lib->resize();
									$images = $fileData['file_name'];
									$subData = array('productCode' => $code, 'productPhoto' => $images);
									$filedoc = $this->GlobalModel->addNew($subData, 'productphotos', 'GPDP');
								}
								else {
									$error = array('error' => $this->upload->display_errors());
									print_r($error);
								}
							}
						}
					}
					// strat to add productBenefit
					if (isset($productBenefit)) {
						$addResultFlag = false;
						for ($j = 0; $j < sizeof($productBenefit); $j++) {
							if ($productBenefit[$j] != '') {
								$addLineTableData = array(
									'productCode' => $code,
									'productBenefit' => $productBenefit[$j]

								);
								$addLineDataResult = $this->GlobalModel->addNew($addLineTableData, 'productbenefits', 'GPRB');
								if ($addLineDataResult)
									$addResultFlag = true;
							}
						}
						$result['AddData'] = $addResultFlag;
					}
					else {
						$result['AddData'] = false;
					}

					$cityCodes = $this->input->post('cityCode');
					$subUnit = $this->input->post('subUnit');
					$quantity = $this->input->post('quantity');
					$sellingPrice = $this->input->post('sellingPrice');
					$regularPrice = $this->input->post('regularPrice');
					$productStatus = $this->input->post('productStatus');
					if (isset($cityCodes)) {
						for ($j = 0; $j < sizeof($cityCodes); $j++) {
							if ($cityCodes[$j] != '') {
								$addLineTableData = array(
									'productCode' => $code,
									'cityCode' => $cityCodes[$j],
									'sellingUnit' => $subUnit[$j],
									'quantity' => $quantity[$j],
									'sellingPrice' => $sellingPrice[$j],
									'regularPrice' => $regularPrice[$j],
									'productStatus' => $productStatus[$j],
									'isActive' => 1,
									'isDelete' => 0,
									'addID' => $addID,
									'addIP' => $ip
								);
								$addLineDataResult = $this->GlobalModel->addNew($addLineTableData, 'productratelineentries', 'GPRL');
								if ($addLineDataResult)
									$addResultFlag = true;
							}
						}
						$this->GlobalModel->set_main_variant($code);
					}
				}
				if ($code != 'false' || $result['AddData']) {
					$response['status'] = true;
					$response['message'] = "Grocery Product Successfully Added." . $images;
					$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
				}
				else {
					$response['status'] = false;
					$response['message'] = "Failed To Add Grocery Product";
				}
				$this->session->set_flashdata('response', json_encode($response));
				redirect(base_url() . 'index.php/Groceryproduct/listRecords', 'refresh');
			}
		}
	}

	public function edit()
	{
		$code = $this->uri->segment(3);
		$table_nameForQuery = 'productmaster';
		$orderColumnsForQuery = array("productmaster.*");
		$condForQuery = array('productmaster' . '.isDelete' => 0, 'productmaster' . '.code' => $code);
		$query = $this->GlobalModel->selectQuery($orderColumnsForQuery, $table_nameForQuery, $condForQuery);
		$data['query'] = $query;
		$categoryCode = '';
		$categoryName = $query->result()[0]->productCategory;
		$category = $this->GlobalModel->selectQuery("categorymaster.code", "categorymaster", array('categorymaster.categorySName' => $categoryName));
		if ($category) {
			$categoryCode = $category->result_array()[0]['code'];
		}
		$table_name = 'categorymaster';
		$orderColumns = array("categorymaster.*");
		$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
		$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);

		$table_name1 = "subcategorymaster";
		$orderColumns1 = array("subcategorymaster.*");
		$cond1 = array('subcategorymaster.categoryCode' => $categoryCode, 'subcategorymaster' . '.isDelete' => 0, 'subcategorymaster' . '.isActive' => 1);
		$data['subcategory'] = $this->GlobalModel->selectQuery($orderColumns1, $table_name1, $cond1);
		$table_nameForUom = 'uommaster';
		$orderColumnsForUom = array("uommaster.*");
		$condForUom = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
		$data['uommaster'] = $this->GlobalModel->selectQuery($orderColumnsForUom, $table_nameForUom, $condForUom);

		//sub units
		$main_unit = $data['query']->result()[0]->productUom;
		$orderColumns1 = "subunit.subunitName,subunit.subunitSName";
		$table1 = "subunit";
		$condition1 = ["uommaster.uomSName" => $main_unit];
		$joinType1 = array('uommaster' => 'inner');
		$join1 = array('uommaster' => 'uommaster.code=subunit.uomCode');
		$extraCondition1 = " (subunit.isDelete=0 or subunit.isDelete is null)";
		$data['subunits'] = $this->GlobalModel->selectQuery($orderColumns1, $table1, $condition1, array(), $join1, $joinType1, array(), "", "", array(), $extraCondition1);

		$data['productPhotos'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
		$data['benefits'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productbenefits');
		$data['prices'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productratelineentries');
		$data['citymaster'] = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/groceryproduct/edit', $data);
		$this->load->view('dashboard/footer');
	}

	public function update()
	{
		$productName = trim($this->input->post("productName"));
		$code = $this->input->post('code');
		$productBenefitAdd = $this->input->post("productBenefitAdd");
		$imageData = $_FILES['images']['name'];
		//Activity Track Starts 
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";

		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$text = $role . " " . $userName . ' updated groceryproduct "' . $productName . '" from ' . $ip;

		$log_text = array(
			'code' => "demo",
			'addID' => $addID,
			'logText' => $text
		);
		//Activity Track Ends 

		$this->form_validation->set_rules('productName', 'Product Name', 'required');
		$this->form_validation->set_rules('productCategory', 'Product Category Name', 'required');
		if ($this->form_validation->run() == FALSE) {
			$data['error_message'] = '* Fields are Required!';
			$table_name = 'categorymaster';
			$orderColumns = array("categorymaster.*");
			$cond = array('categorymaster' . '.isDelete' => 0, 'categorymaster' . '.isActive' => 1, 'categorymaster' . '.mainCategoryCode' => 'MCAT_2');
			$data['category'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);

			$table_nameForUom = 'uommaster';
			$orderColumnsForUom = array("uommaster.*");
			$condForUom = array('uommaster' . '.isDelete' => 0, 'uommaster' . '.isActive' => 1);
			$data['uommaster'] = $this->GlobalModel->selectQuery($orderColumnsForUom, $table_nameForUom, $condForUom);

			$table_nameForQuery = 'productmaster';
			$orderColumnsForQuery = array("productmaster.*");
			$condForQuery = array('productmaster' . '.isDelete' => 0, 'productmaster' . '.code' => $code);
			$data['query'] = $this->GlobalModel->selectQuery($orderColumnsForQuery, $table_nameForQuery, $condForQuery);
			$data['productPhotos'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
			$data['benefits'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productbenefits');
			$data['prices'] = $this->GlobalModel->selectDataByField('productCode', $code, 'productratelineentries');
			$data['citymaster'] = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
			$table_name = 'tagmaster';
			$orderColumns = array("tagmaster.*");
			$cond = array('tagmaster.isActive' => 1);
			$data['tags'] = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/groceryproduct/edit', $data);
			$this->load->view('dashboard/footer');
		}
		else {
			$description = trim($this->input->post("productDescription"));
			if (strpos($description, 'class="table table-bordered" border="1"') !== false) {
				$description = str_replace('class="table table-bordered"', 'class="table table-bordered" border="1"', $description);
			}
			else {
				$description = str_replace('class="table table-bordered"', 'class="table table-bordered" border="1"', $description);
			}
			$data = array(
				'productName' => $productName,
				'productCategory' => $this->input->post("productCategory"),
				'subCategoryCode' => $this->input->post("productSubCategory"),
				'productDescription' => $description,
				'hsnCode' => $this->input->post("hsnCode"),
				'taxPercent' => '0',
				'minimumSellingQuantity' => trim($this->input->post("minimumSellingQuantity")),
				'productUom' => trim($this->input->post("productUom")),
				'productRegularPrice' => trim($this->input->post("productRegularPrice") ?? ""),
				'editID' => $addID,
				'editIP' => $ip,
				'isActive' => $this->input->post('isActive') ? 1 : 0,
				'isPopular' => $this->input->post("isPopular")
			);
			if ($this->input->post("tagCode") == 1) {
				$data['tagCode'] = NULL;
			}
			else {
				$data['tagCode'] = $this->input->post("tagCode");
			}
			$result = $this->GlobalModel->doEdit($data, 'productmaster', $code);
			$photoData = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
			$resultFlag = "0";
			$photoCode = '';
			if ($imageData[0] != '') {
				//echo 'in';
				if (!file_exists(FCPATH . 'uploads/product/' . $code))
					mkdir(FCPATH . 'uploads/product/' . $code, 0755, false);
				$uploadRootDir = 'uploads/';
				$uploadDir = 'uploads/product/' . $code . '/';
				if (!empty($_FILES['images']['name'])) {
					$filesCount = count($_FILES['images']['name']);

					for ($i = 0; $i < $filesCount; $i++) {
						$_FILES['file']['name'] = $_FILES['images']['name'][$i];
						$_FILES['file']['type'] = $_FILES['images']['type'][$i];
						$_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
						$_FILES['file']['error'] = $_FILES['images']['error'][$i];
						$_FILES['file']['size'] = $_FILES['images']['size'][$i];

						// File upload configuration
						$uploadPath = $uploadDir;
						$config['upload_path'] = $uploadPath;
						$config['allowed_types'] = 'jpg|jpeg|png|gif';
						$config['file_name'] = $i;

						// Load and initialize upload library
						$this->load->library('upload', $config);
						$this->upload->initialize($config);

						// Upload file to server
						if ($this->upload->do_upload('file')) {
							// Uploaded file data
							$fileData = $this->upload->data();
							$uploadData[$i]['file_name'] = $fileData['file_name'];
							$config['image_library'] = 'gd2';
							$config['source_image'] = $uploadDir . $fileData['file_name'];
							$config['create_thumb'] = FALSE;
							$config['maintain_ratio'] = FALSE;
							$config['quality'] = '100%';
							$config['width'] = 400;
							$config['height'] = 400;
							$this->load->library('image_lib', $config);
							$this->image_lib->initialize($config);
							$this->image_lib->resize();
							$images = $fileData['file_name'];
							$subData = array('productCode' => $code, 'productPhoto' => $images);
							$filedoc = $this->GlobalModel->addNew($subData, 'productphotos', 'GPDP');
						}
						else {
							$error = array('error' => $this->upload->display_errors());
						}
					}
					$resultFlag = "1";
				}
			}

			$lineCode = $this->input->post("lineCode");
			$productBenefit = $this->input->post("productBenefit");
			if ($productBenefit == "") {
				$productBenefit = array();
			}
			$editResultFlag = false;
			for ($k = 0; $k < sizeof($productBenefit); $k++) {
				$editTableData = array('productBenefit' => $productBenefit[$k]);
				// print_r($editTableData);
				$editLineDataResult = $this->GlobalModel->doEdit($editTableData, 'productbenefits', $lineCode[$k]);
				if ($editLineDataResult == 'true')
					$editResultFlag = true;
			}
			// strat to add productBenefit
			if (isset($productBenefitAdd)) {
				$addResultFlag = false;
				for ($j = 0; $j < sizeof($productBenefitAdd); $j++) {
					if ($productBenefitAdd[$j] != '') {
						$addLineTableData = array('productCode' => $code, 'productBenefit' => $productBenefitAdd[$j]);
						$addLineDataResult = $this->GlobalModel->addNew($addLineTableData, 'productbenefits', 'PRB');
						if ($addLineDataResult)
							$addResultFlag = true;
					}
				}
				$responseRes['AddData'] = $addResultFlag;
			}
			else {
				$responseRes['AddData'] = false;
			}
			$cityCodes = $this->input->post('cityCode');
			$subUnit = $this->input->post('subUnit');
			$quantity = $this->input->post('quantity');
			$sellingPrice = $this->input->post('sellingPrice');
			$regularPrice = $this->input->post('regularPrice');
			$productStatus = $this->input->post('productStatus');
			$lineCodes = $this->input->post('lineCodes');
			if (isset($cityCodes)) {
				for ($j = 0; $j < sizeof($cityCodes); $j++) {
					if ($cityCodes[$j] != '') {
						/*$dataCode = $this->GlobalModel->selectQuery("productratelineentries.code", "productratelineentries", array("productratelineentries.productCode" => $code, "productratelineentries.cityCode" => $cityCodes[$j]), array());
						 if ($dataCode) {
						 $codea = $dataCode->result_array()[0]['code'];
						 $addLineTableData = array(
						 'productCode' 	=> $code,
						 'cityCode' 		=> $cityCodes[$j],
						 'sellingUnit'	=> $subUnit[$j],
						 'quantity'		=> $quantity[$j],
						 'sellingPrice' 	=> $sellingPrice[$j],
						 'regularPrice' 	=> $regularPrice[$j],
						 'productStatus' => $productStatus[$j],
						 'editID' => $addID,
						 'editIP' => $ip
						 );
						 $addLineDataResult = $this->GlobalModel->doEdit($addLineTableData, 'productratelineentries', $codea);
						 } else {
						 $addLineTableData = array(
						 'productCode' 	=> $code,
						 'cityCode' 		=> $cityCodes[$j],
						 'sellingUnit'	=> $subUnit[$j],
						 'quantity'		=> $quantity[$j],
						 'sellingPrice' 	=> $sellingPrice[$j],
						 'regularPrice' 	=> $regularPrice[$j],
						 'productStatus' => $productStatus[$j],
						 'addID' => $addID,
						 'addIP' => $ip
						 );
						 $addLineDataResult = $this->GlobalModel->addNew($addLineTableData, 'productratelineentries', 'GPRL');
						 }
						 if ($addLineDataResult) $addResultFlag = true;*/
						$addLineTableData = array(
							'productCode' => $code,
							'cityCode' => $cityCodes[$j],
							'sellingUnit' => $subUnit[$j],
							'quantity' => $quantity[$j],
							'sellingPrice' => $sellingPrice[$j],
							'regularPrice' => $regularPrice[$j],
							'productStatus' => $productStatus[$j],
							'isActive' => 1,
							'isDelete' => 0
						);
						if ($lineCodes[$j] != "-") {
							$addLineTableData['editIP'] = $ip;
							$addLineTableData['editDate'] = date('Y-m-d H:i:s');
							$addLineTableData['editID'] = $addID;
							$this->GlobalModel->doEdit($addLineTableData, 'productratelineentries', $lineCodes[$j]);
						}
						else {
							$addLineTableData['addIP'] = $ip;
							$addLineTableData['addDate'] = date('Y-m-d H:i:s');
							$addLineTableData['addID'] = $addID;
							$this->GlobalModel->addNew($addLineTableData, 'productratelineentries', 'PRL');
						}
					}
				}
				$this->GlobalModel->set_main_variant($code);
			}
		}
		if ($responseRes != 'false' || $resultFlag == "1") {
			$response['status'] = true;
			$response['message'] = "Product Successfully Updated.";
			$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		}
		else {
			$response['status'] = false;
			$response['message'] = "No change In Product";
		}
		$this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'index.php/groceryproduct/listRecords', 'refresh');
	}

	public function delete()
	{
		$code = $this->input->post('code');

		//Activity Track Starts

		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";

		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$dataQ = $this->GlobalModel->selectDataByField('code', $code, 'productmaster');
		$productName = '';

		foreach ($dataQ->result() as $row) {
			$productName = $row->productName;
		}

		$text = $role . " " . $userName . ' deleted groceryproduct "' . $productName . '" from ' . $ip;

		$log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);

		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');

		$data = array('deleteID' => $addID, 'deleteIP' => $ip);

		$resultData = $this->GlobalModel->doEdit($data, 'productmaster', $code);

		//Activity Track Ends 

		//function doEditWithField($data, $tblname, $field ,$code)
		$dataPhoto = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
		$photoCode = '';

		foreach ($dataPhoto->result() as $row) {
			$photoCode = $row->code;
			$dataDelete = array('isDelete' => '1');
			$this->GlobalModel->doEditWithField($dataDelete, 'productphotos', 'code', $photoCode);
		}

		// $this->GlobalModel->deleteForeverFromField('productCode',$code,'clientcarts');
		echo $this->GlobalModel->delete($code, 'productmaster');

	// redirect(base_url() . 'index.php/currency/listrecords', 'refresh');
	}

	public function view()
	{
		$code = $this->input->post('code') ?? $this->input->get('code');
		//Activity Track Starts		
		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";
		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		//Activity Track Ends

		$table_name = 'tagmaster';
		$orderColumns = array("tagmaster.*");
		$cond = array('tagmaster.isActive' => 1);
		$tags = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
		$prices = $this->GlobalModel->selectDataByField('productCode', $code, 'productratelineentries');
		$productPhotos = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
		$citymaster = $this->GlobalModel->selectQuery("citymaster.*", 'citymaster', array('citymaster.isActive' => 1));
		$tableName = "productmaster";
		$orderColumns = array("productmaster.*,subcategorymaster.subcategoryName,categorymaster.categoryName,categorymaster.categorySName");
		$condition = array("productmaster.code" => $code, "categorymaster.isActive" => 1);
		$orderBy = array('productmaster' . '.id' => 'DESC');
		$joinType = array('categorymaster' => "inner", "subcategorymaster" => "left");
		$join = array("categorymaster" => "categorymaster.categorySName = productmaster.productCategory", "subcategorymaster" => "subcategorymaster.code=productmaster.subCategoryCode");
		$groupByColumn = array();
		$limit = $this->input->post("length") ?? $this->input->get("length");
		$offset = $this->input->post("start") ?? $this->input->get("start");
		$extraCondition = "";
		$like = array();
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, $like, $limit, $offset, $groupByColumn, $extraCondition);
		$modelHtml = '<form>';
		$activeStatus = "";
		$photoData = "";
		$benefitsData = "";
		//echo $this->db->last_query();
		foreach ($Records->result() as $row) {
			$code = $row->code;
			$benefits = $this->GlobalModel->selectDataByField('productCode', $code, 'productbenefits');
			$k = 1;
			foreach ($benefits->result() as $pros) {
				$benefitsData .= '<div class="col-md-12 mb-3"><label>' . $k . '.&nbsp  &nbsp' . $pros->productBenefit . '</label></div>';
				$k++;
			}
			$productPhotos = $this->GlobalModel->selectDataByField('productCode', $code, 'productphotos');
			$i = 1;
			foreach ($productPhotos->result() as $photos) {
				$photoData .= '<div class="col-md-3 mb-3"> 
								<div class="card">
									<div class="el-card-item">
										<div class="el-card-avatar el-overlay-1"> <img src="' . base_url() . 'uploads/product/' . $photos->productCode . '/' . $photos->productPhoto . '" alt="product photo File">
											<div class="el-overlay">
												<ul class="list-style-none el-info">
												<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="' . base_url() . 'uploads/product/' . $photos->productCode . '/' . $photos->productPhoto . '" target="_blank"><i class="icon-magnifier"></i></a></li>	
												</ul>
											</div></div></div></div></div>';
				$i = $i + 1;
			}
			if ($row->isActive == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			}
			else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}

			if ($row->isPopular == "1") {
				$popularStatus = '<span class="label label-sm label-success">Yes</span>';
			}
			else {
				$popularStatus = '<span class="label label-sm label-warning">No</span>';
			}

			$modelHtml .= '<div class="form-row">
				<div class="col-md-6 mb-3"><label> <b> Code:</b></label>
					<input type="text" value="' . $row->code . '" class="form-control-line"  readonly>
				</div>
				<div class="col-md-6 mb-3"><label> <b>Product Name:</b> </label>
					<input type="text" class="form-control-line" value="' . $row->productName . '"  readonly>
				</div> 
			</div>
			<div class="col-md-12 mb-3"><label><b>Product Description:</b> </label>
				<input type="text" class="form-control-line" row="2" cols="50" readonly value="' . strip_tags($row->productDescription) . '"> 
			</div>
			<div class="form-row">
				<div class="col-md-4 mb-3"><label> <b>Product Category : </b></label>

					<input type="text" value="' . $row->categorySName . '" class="form-control-line"  readonly>
				</div> 
				<div class="col-md-4 mb-3"><label> <b>Product Subcategory :</b> </label>
					<input type="text" class="form-control-line" value="' . $row->subcategoryName . '"  readonly>
				</div>
				<div class="col-md-4 mb-3"><label> <b>Product Uom :</b> </label>
					<input type="text" class="form-control-line" value="' . $row->productUom . '"  readonly>
				</div>
			</div>
			<div class="form-row">
						    <div class="col-md-4 mb-3"><label> <b>HSN Code : </b></label>
								<input type="text" value="' . $row->hsnCode . '" class="form-control-line"  readonly>
							</div> 
							<div class="col-md-4 mb-3"><label> <b>Tax percent :</b> </label>
								<input type="text" class="form-control-line" value="' . $row->taxPercent . '"  readonly>
							</div>
			</div>
			<label> <b>Product Benefits :</b> </label>
			<div class="form-row">' . $benefitsData . '</div>
			<div class="form-group"><label> <b>Status :</b> </label> ' . $activeStatus . '</div>
			<div class="form-group"><label> <b>Popular marked :</b> </label> ' . $popularStatus . '</div>
			<div class="form-row">
				<div class="row el-element-overlay">' . $photoData . '</div>
			</div>';

			$modelHtml .= '<div class="form-group">
									<div class="row">
										<div class="col-sm-6 mb-1">
											<label for="testType1"> <b>Product Selling Price:</b></label>	
										</div>
									</div>
				              </div>';
			foreach ($prices->result_array() as $price) {
				$modelHtml .= '<div class="form-row">
						<div class="col-md-4 mb-3"><select class="form-control-line" readonly>';
				if ($citymaster->num_rows() > 0) {
					foreach ($citymaster->result() as $city) {
						$modelHtml .= '<option value="' . $city->code . '" ' . $city->code . ' == ' . $price['cityCode'] . ' ? "selected" : "" readonly>' . $city->cityName . '</option>';
					}
				}
				$modelHtml .= '</select></div> 
					    <div class="col-md-4"> <input class="form-control-line" name="selling_unit" value="' . $price['sellingUnit'] . '" readonly></div>
						<div class="col-md-4"> <input class="form-control-line" name="selling_quantity" value="' . $price['quantity'] . '" readonly></div>
						<div class="col-md-4"><input class="form-control-line" name="selling_quantity" value="' . $price['sellingPrice'] . '" readonly></div>
						<div class="col-md-4"><input class="form-control-line" name="selling_quantity" value="' . $price['regularPrice'] . '" readonly></div>
						<div class="col-md-4 mb-3">
							  <select class="form-control-line" readonly>
							       <option value="AVL" "AVL ==' . $price['productStatus'] . ' ? "selected" : "">Available</option>
								   <option value="OOS" "OOS ==' . $price['productStatus'] . ' ? "selected" : "">Out Off Stock</option> 
							  </select>
						</div>
					</div>';
			}
			if ($tags->num_rows() > 0) {
				$str1 = '';
				if ($row->tagCode == NULL || $row->tagCode == '') {
					$str1 = 'checked';
				}
				$modelHtml .= '<div class="form-group">
				<div class="row">
					<div class="col-sm-2 mb-1">
						<label for="testType1"> <b>Tags:</b></label>	
					</div>
				</div>
				<div class="row" style="margin-left:35px;">
					<div class="col-sm-3 mb-3">
						 <div class="custom-control custom-radio custom-control-inline">
							<input type="radio" class="custom-control-input" id="tagSection"  name="tagCode" value="1" ' . $str1 . '>
							<label class="custom-control-label"  for="tagSection"><b>No Tag</b></label> 
						</div>
					</div>';

				foreach ($tags->result_array() as $tag) {
					$str = '';
					if ($tag['code'] == $row->tagCode) {
						$str = 'checked';
					}
					$modelHtml .= '<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" disabled class="custom-control-input" id="tagSection' . $tag['code'] . '" ' . $str . ' name="tagCode" value="' . $tag['code'] . '">
									<label class="custom-control-label" style="color:"' . $tag['tagColor'] . '" for="tagSection' . $tag['code'] . '"><b>' . $tag['tagTitle'] . '</b></label>
								</div>
							</div>';
				}
				$modelHtml .= '</div>
					</div>';
			}
			//for activity
			$text = $role . " " . $userName . ' viewed product "' . $row->productName . '" from ' . $ip;
			$log_text = array(
				'code' => "demo",
				'addID' => $addID,
				'logText' => $text
			);
			$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		}

		$modelHtml .= '</form>';
		echo $modelHtml;
	}


	public function deleteImage()
	{
		$code = $this->input->post('value');
		$Result = $this->GlobalModel->selectDataByid($code, 'productphotos');
		$productCode = $Result->result()[0]->productCode;
		$productPhoto = $Result->result()[0]->productPhoto;
		unlink('uploads/product/' . $productCode . '/' . $productPhoto);
		echo $deleteData = $this->GlobalModel->deleteForever($code, 'productphotos');
	// echo base_url().'uploads/product/'.$photos->productCode.'/'.$photos->productPhoto;
	//print_r($data->result());

	}

	public function deleteLineRecord()
	{
		$lineCode = $this->input->post('code');
		echo $this->GlobalModel->deleteForever($lineCode, 'productbenefits');
	}
	public function deletePriceRecord()
	{
		$lineCode = $this->input->post('code');
		//Activity Track Starts

		$addID = $this->session->userdata['logged_in' . $this->session_key]['code'];
		$userRole = $this->session->userdata['logged_in' . $this->session_key]['role'];
		$userName = $this->session->userdata['logged_in' . $this->session_key]['username'];
		$role = "";

		switch ($userRole) {
			case "ADM":
				$role = "Admin";
				break;
			case "USR":
				$role = "User";
				break;
		}
		$text = $role . " " . $userName . ' deleted variants "' . $lineCode . '" from ' . $ip;
		$log_text = array('code' => "demo", 'addID' => $addID, 'logText' => $text);
		$this->GlobalModel->activityAdd($log_text, 'activitymaster', 'ACT');
		$data = array('isActive' => 0, 'isDelete' => 1, 'deleteID' => $addID, 'deleteIP' => $ip);
		$resultData = $this->GlobalModel->doEdit($data, 'productratelineentries', $lineCode);
	//Activity Track Ends 

	//echo $this->GlobalModel->deleteForever($lineCode, 'productratelineentries');
	}

	public function getSubCategoryList()
	{
		$productCategory = $this->input->post('productCategory');
		$records = "";
		if ($productCategory != "") {
			$table_name = 'subcategorymaster';
			$orderColumns = array("subcategorymaster.*");
			$cond = array('subcategorymaster.isDelete' => 0, 'subcategorymaster.isActive' => 1, 'categoryCode' => $productCategory);
			$categories = $this->GlobalModel->selectQuery($orderColumns, $table_name, $cond);
			if ($categories) {
				$records = '<option value="" readonly>Select Sub Category</option> ';
				foreach ($categories->result() as $r) {
					$records .= '<option value="' . $r->code . '">' . $r->subcategoryName . '</option> ';
				}
			}
		}
		echo $records;
	}

	public function getSubUnitsold()
	{
		$html = "";
		/** commented on 10-2-23 8:00PM */
		// $mainUnit =  $this->input->get('unit');
		// $orderColumns = "uommaster.subunit,uommaster.subunitSName";
		// $table = "uommaster";
		// $condition = ["uommaster.uomSName" => $mainUnit, "uommaster.isActive" => 1];
		// $orderBy = ["uommaster.subunit" => "DESC"];
		// $Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy);
		// if ($Records) {
		// 	$html = '<option value=""> -- Select Sub Unit -- </option>';
		// 	foreach ($Records->result() as $r) {
		// 		$html .= '<option value="' . $r->subunitSName . '">' . $r->subunit . '</option>';
		// 	}
		// }
		$orderColumns = "uommaster.uomName,uommaster.uomSName";
		$table = "uommaster";
		$condition = ["uommaster.isActive" => 1];
		$orderBy = ["uommaster.uomName" => "DESC"];
		$Records = $this->GlobalModel->selectQuery($orderColumns, $table, $condition, $orderBy);
		if ($Records) {
			$html = '<option value=""> -- Select UOM -- </option>';
			foreach ($Records->result() as $r) {
				$html .= '<option value="' . $r->uomSName . '">' . $r->uomName . '</option>';
			}
		}
		echo $html;
	}

	public function getSubUnits()
	{
		$html = "";
		$mainUnit = $this->input->get('unit');
		$orderColumns = array("subunit.*,uommaster.uomName");
		$tableName = "subunit";
		$condition = ["uommaster.uomSName" => $mainUnit];
		$joinType = array('uommaster' => 'inner');
		$join = array('uommaster' => 'uommaster.code=subunit.uomCode');
		$extraCondition = " (subunit.isDelete=0 or subunit.isDelete is null)";
		$orderBy = ["uommaster.subunit" => "DESC"];
		$Records = $this->GlobalModel->selectQuery($orderColumns, $tableName, $condition, $orderBy, $join, $joinType, array(), "", "", array(), $extraCondition);
		if ($Records) {
			$html = '<option value=""> -- Select Sub Unit -- </option>';
			foreach ($Records->result() as $r) {
				$html .= '<option value="' . $r->subunitSName . '">' . $r->subunitName . '</option>';
			}
		}
		echo $html;
	}

	public function delete_all_rate_entries()
	{
		$productCode = $this->input->get('productCode');
		$result = $this->db->where('productratelineentries.productCode', $productCode)->delete('productratelineentries');
		if ($result) {
			echo true;
		}
		else {
			echo false;
		}
	}
}
