<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Session $session
 * @property GlobalModel $GlobalModel
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 */
class Userrights extends CI_Controller
{
	private $rights_file_path = rights_file_path;
	private $rights_file_name = rights_file_name;
	var $controller = "userrights";
	public function __construct()
	{
		parent::__construct();
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if (!isset($this->session->userdata['logged_in' . $this->session_key])) {
			redirect(base_url('Login'), 'refresh');
		}
		if (!file_exists($this->rights_file_path)) {
			$myfile = fopen($this->rights_file_path, "w") or die("Unable to open file!");
			fclose($myfile);
		}
	}

	public function add_menu_and_sections()
	{
		$menu = $this->input->post('menuName') ?? $this->input->get('menuName');
		$sections = $this->input->post('sections') ?? $this->input->get('sections');
		if ($menu != "" && $sections != "") {
			$splitsections = explode(',', $sections);
			if (!empty($splitsections)) {
				$secarray = array();
				foreach ($splitsections as $s) {
					$secarray[$s] = 0;
				}

				$adminsecarray = array();
				foreach ($splitsections as $s) {
					$adminsecarray[$s] = 1;
				}

				$aray_to_merge_admin[$menu] = array("menurights" => $adminsecarray);
				$aray_to_merge[$menu] = array("menurights" => $secarray);

				$data = file_get_contents($this->rights_file_path);
				if ($data != "" || $data != null) {
					$unset_role = "";
					$json_data = json_decode($data, true);
					$merged_final = array();
					$roleData = array();
					foreach ($json_data as $d => $val) {
						$roleData = $val;
						if ($d == "ADM") {
							$dataa = array_merge($roleData, $aray_to_merge_admin);
						}
						else {
							$dataa = array_merge($roleData, $aray_to_merge);
						}
						$merged_final[$d] = $dataa;
					}

					$this->emptyMyFiles($this->rights_file_path);
					$convertedJsonData = json_encode($merged_final);
					$file = fopen($this->rights_file_path, "w");
					fwrite($file, $convertedJsonData);
					fclose($file);
				}
			}
		}
	}

	public function create()
	{
		$usrdata = $this->GlobalModel->selectQuery("userroles.*", "userroles", array("userroles.isActive" => 1, "userroles.role!=" => 'ADM'), array("userroles.id" => 'ASC'));

		$menuArAdmin = array(
			"dashboard" => array(
				"menurights" => array("list" => 1)
			),
			"chat" => array(
				"menurights" => array("list" => 1)
			),
			"conversations" => array(
				"menurights" => array("list" => 1)
			),
			"customer" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"customerrequest" => array(
				"menurights" => array("list" => 1, "accept" => 1, "reject" => 1, "revoke" => 1, "view" => 1)
			),
			"employee" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"payment" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"project" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"tutor" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"tutorrequest" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1, "accept" => 1, "reject" => 1, "revoke" => 1)
			),
			"tutorskills" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"user" => array(
				"menurights" => array("list" => 1, "add" => 1, "edit" => 1, "delete" => 1, "view" => 1)
			),
			"userrights" => array(
				"menurights" => array("list" => 1, 'edit' => 1)
			)
		);

		$menuAr = array(
			"dashboard" => array(
				"menurights" => array("list" => 0)
			),
			"chat" => array(
				"menurights" => array("list" => 0)
			),
			"conversations" => array(
				"menurights" => array("list" => 0)
			),
			"customer" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"customerrequest" => array(
				"menurights" => array("list" => 0, "accept" => 0, "reject" => 0, "revoke" => 0, "view" => 0)
			),
			"employee" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"payment" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"project" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"tutor" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"tutorrequest" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0, "accept" => 0, "reject" => 0, "revoke" => 0)
			),
			"tutorskills" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"user" => array(
				"menurights" => array("list" => 0, "add" => 0, "edit" => 0, "delete" => 0, "view" => 0)
			),
			"userrights" => array(
				"menurights" => array("list" => 0, 'edit' => 0)
			)
		);

		$data = array();
		if ($usrdata) {
			foreach ($usrdata->result_array() as $r) {
				$role = $r['role'];
				if ($role == "ADM")
					$data[$role] = $menuArAdmin;
				else
					$data[$role] = $menuAr;
			}
		}
		//echo '<pre>';
		//print_r($data);	
		$this->emptyMyFiles($this->rights_file_path);
		$convertedJsonData = json_encode($data);
		$file = fopen($this->rights_file_path, "w");
		fwrite($file, $convertedJsonData);
		fclose($file);
	}

	public function emptyMyFiles($filePath)
	{
		$filename = $filePath;
		if (file_exists($filename)) {
			$handle = fopen($filename, 'r+');
			ftruncate($handle, rand(1, filesize($filename)));
			rewind($handle);
			if (filesize($filename) > 0) {
				fread($handle, filesize($filename));
				fclose($handle);
			}
		}
	}

	public function listRecords()
	{
		$role = ($this->session->userdata['logged_in' . $this->session_key]['role']);
		$rights = $this->checkrights->check_Rights_Role_Menu_Page($role, $this->controller, 'list');
		if ($rights) {
			$data['roles'] = $this->GlobalModel->selectQuery("userroles.*", "userroles", array("userroles.isActive" => 1, "userroles.role!=" => "ADM"), array("userroles.id" => 'ASC'));
			$data['css'] = "";
			$data['jscripts'] = "";
			$this->load->view('admin/userrights/list', $data);
		}
		else {
			$this->load->view('errors/norights.php');
		}
	}

	public function update()
	{
		$role = ($this->session->userdata['logged_in' . $this->session_key]['role']);
		$rights = $this->checkrights->check_Rights_Role_Menu_Page($role, $this->controller, 'edit');
		if ($rights) {
			sleep(3);
			$role = $this->input->post('role');
			$menus = $this->input->post('menu');
			if (isset($menus)) {
				$cnt = sizeof($menus);
				if (file_exists($this->rights_file_path)) {
					$data = file_get_contents($this->rights_file_path);
					if ($data != "" || $data != null) {
						$unset_role = "";
						$json_data = json_decode($data, true);
						$roleData = array();
						foreach ($json_data as $d => $val) {
							if ($d == $role) {
								$roleData = $val;
								unset($json_data->d);
							}
						}

						$dataNew = $json_data;

						$this->emptyMyFiles($this->rights_file_path);
						$convertedJsonData = json_encode($dataNew);
						$file = fopen($this->rights_file_path, "w");
						fwrite($file, $convertedJsonData);
						fclose($file);
					}

					$data = file_get_contents($this->rights_file_path);
					if ($data != "" || $data != null) {
						$unset_role = "";
						$json_data = json_decode($data, true);

						$dataNew = $json_data;

						$menuAr = array();

						for ($i = 0; $i < $cnt; $i++) {
							$menuName = $menus[$i];
							$rightsData = array();
							if (isset($roleData[$menuName])) {
								$rightsData = $roleData[$menuName]['menurights'];
								//print_r($rightsData);
								$menusections = array();
								foreach ($rightsData as $r => $valm) {
									$section = $r;

									$value = $this->input->post($menuName . '_right_' . $section);

									$menusections[$section] = $value == "1" ? 1 : 0;

								}
								$menuAr[$menuName] = array("menurights" => $menusections);
								$dataNew[$role] = $menuAr;
							}
						}

						$this->emptyMyFiles($this->rights_file_path);
						$convertedJsonData = json_encode($dataNew);
						$file = fopen($this->rights_file_path, "w");
						fwrite($file, $convertedJsonData);
						fclose($file);

						$res['status'] = true;
						$res['message'] = "User rights updated successfully.";
						echo json_encode($res);
					}
					else {
						$res['status'] = false;
						$res['message'] = "Failed to update the rights!";
						echo json_encode($res);
					}
				}
				else {
					$res['status'] = false;
					$res['message'] = "Failed to update the rights!";
					echo json_encode($res);
				}
			}
			else {
				$res['status'] = false;
				$res['message'] = "No Rights Found! Failed to update the rights.";
				echo json_encode($res);
			}
		}
		else {
			$this->load->view('errors/norights.php');
		}
	}

	public function update_old()
	{
		$role = $this->input->post('role');
		$menus = $this->input->post('menu');
		if (isset($menus)) {
			$cnt = sizeof($menus);
			if (file_exists($this->rights_file_path)) {
				$data = file_get_contents($this->rights_file_path);
				if ($data != "" || $data != null) {
					$unset_role = "";
					$json_data = json_decode($data, true);
					foreach ($json_data as $d => $val) {
						if ($d == $role) {
							$roleData = $val;
							unset($json_data->d);
						}
					}

					$dataNew = $json_data;
					//print_r($dataNew);

					$menuAr = array();

					for ($i = 0; $i < $cnt; $i++) {
						$menuName = $menus[$i];

						$list = $this->input->post($menuName . '_right_list');
						$listVal = $list == "1" ? 1 : 0;

						$add = $this->input->post($menuName . '_right_add');
						$addVal = $add == "1" ? 1 : 0;

						$save = $this->input->post($menuName . '_right_save');
						$saveVal = $save == "1" ? 1 : 0;

						$edit = $this->input->post($menuName . '_right_edit');
						$editVal = $edit == "1" ? 1 : 0;

						$update = $this->input->post($menuName . '_right_update');
						$updateVal = $update == "1" ? 1 : 0;

						$delete = $this->input->post($menuName . '_right_delete');
						$deleteVal = $delete == "1" ? 1 : 0;

						$view = $this->input->post($menuName . '_right_view');
						$viewVal = $view == "1" ? 1 : 0;

						$menusections = array(
							"list" => $listVal,
							"add" => $addVal,
							"save" => $saveVal,
							"edit" => $editVal,
							"update" => $updateVal,
							"delete" => $deleteVal,
							"view" => $viewVal
						);
						$menuAr[$menuName] = array("menurights" => $menusections);
					}
					$dataNew[$role] = $menuAr;

					//print_r($dataNew);

					$this->emptyMyFiles($this->rights_file_path);
					$convertedJsonData = json_encode($dataNew);
					$file = fopen($this->rights_file_path, "w");
					fwrite($file, $convertedJsonData);
					fclose($file);

					$res['status'] = true;
					$res['message'] = "User rights updated successfully.";
					echo json_encode($res);
				}
				else {
					$res['status'] = true;
					$res['message'] = "User rights updated successfully.";
					echo json_encode($res);
				}
			}
			else {
				$res['status'] = false;
				$res['message'] = "Failed to update the rights!";
				echo json_encode($res);
			}
		}
		else {
			$res['status'] = false;
			$res['message'] = "No Rights Found! Failed to update the rights.";
			echo json_encode($res);
		}
	}

	public function getRightsForRole()
	{

		$role = ($this->session->userdata['logged_in' . $this->session_key]['role']);
		$rights = $this->checkrights->check_Rights_Role_Menu_Page($role, $this->controller, 'edit');
		if ($rights) {
			$html = "";
			$role = $this->input->post('role') ?? $this->input->get('role');
			if (file_exists($this->rights_file_path)) {
				$data = file_get_contents($this->rights_file_path);
				if ($data != "" || $data != null) {
					$json_data = json_decode($data);
					foreach ($json_data as $d => $val) {
						if (isset($d) && $d == $role) {
							$html = '<form id="updateRights" method="post">';
							$html .= '<div class="row"><div class="col-sm-12">';
							$html .= '<input name="role" readonly id="role" value="' . $role . '" type="hidden"><table class="table table-borderd"><thead><tr><th>Menu</th><th>Rights</th></tr></thead><tbody>';
							foreach ($val as $v => $opt) {
								$html .= '<tr><th>' . ucwords($v) . '<input name="menu[]" value="' . $v . '" readonly type="hidden"></th><td>';
								$menurights = $opt->menurights;

								foreach ($menurights as $m => $va) {
									$checked = '';

									if ($va == 1)
										$checked = 'checked';
									else
										$checked = '';
									$html .= '<label class="col-4"><input type="checkbox" name="' . $v . '_right_' . $m . '" ' . $checked . ' value="1"> ' . $m . '</label>';
								}
								$html .= '</td></tr>';
							}
							$html .= '</tbody></table></div>';
							$html .= '<div class="col-sm-12 mt-3"><button type="button" id="updateSubmit" class="btn btn-primary">Update Rights</button></div>';
							$html .= '</div></form>';

							$res['status'] = true;
							$res['message'] = "Records found";
							$res['html'] = $html;
							echo json_encode($res);
						}
					}
				}
				else {
					$res['status'] = false;
					$res['message'] = "No Records found";
					echo json_encode($res);
				}
			}
			else {
				$res['status'] = false;
				$res['message'] = "No Records found";
				echo json_encode($res);
			}
		}
		else {
			$this->load->view('errors/norights.php');
		}
	}

	public function check()
	{
		$role = $this->input->post('role') ?? $this->input->get('role');
		$menuName = $this->input->post('menuName') ?? $this->input->get('menuName');
		$viewName = $this->input->post('viewName') ?? $this->input->get('viewName');

		$rights = $this->checkrights->check_Rights_Role_Menu_Page($role, $menuName, $viewName);
		if ($rights) {
			echo 'Access Granted';
		}
		else {
			echo 'Access Rejected';
		}
		header("HTTP/1.1 200 OK");
	}



}
