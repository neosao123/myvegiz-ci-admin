<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CommonReport extends CI_Controller
{
	var $session_key;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('GlobalModel');
		$this->load->model('GlobalModel1');
		$this->session_key = $this->session->userdata('key' . SESS_KEY);
		if(!isset($this->session->userdata['logged_in' . $this->session_key]['code'])){
			redirect('Admin/login','refresh');
		}
	}

	public function add()
	{
		// $Data = $this->GlobalModel->selectData('commoninlinereports'); 
		// print_r($Data->result());

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commonreport/add');
		$this->load->view('dashboard/footer');
	}




	//To Save submitted PR in DB

	public function save()
	{

		//$reportName=$this->input->post("reportName");
		$reportSName = strtoupper($this->input->post("reportSName"));
		// echo $reportSName;
		// exit();
		$result = $this->GlobalModel->checkDuplicateRecord('reportSName', $reportSName, 'commoninreports');

		// $result1 = $this->GlobalModel->checkDuplicateRecord('reportName',$reportName,'commoninreports');
		if ($result != FALSE) {
			$data = array(
				'errormessage' => ' Duplicate report Short Name'
			);
			$this->load->view('dashboard/header');
			$this->load->view('dashboard/commonreport/add', $data);
			$this->load->view('dashboard/footer');
		} else {

			$data = array(
				'reportName' => strtoupper($this->input->post("reportName")),
				'reportSName' => strtoupper($this->input->post("reportSName")),
				'isActive' => $this->input->post("isActive")
			);
			//	print_r($data);

			$resultData = $this->GlobalModel->addWithoutYear($data, 'commoninreports', 'COMRP');
			//print_r($resultData);

			$result['coInformation'] = $resultData;

			// Lines Data 
			$reportPara = $this->input->post('reportPara');


			if ($resultData != 'false') {

				if ($reportPara[0] != '') {

					$addResultFlag = false;
					for ($i = 0; $i < sizeof($reportPara); $i++) {


						$tabledata = array(
							'commonCode' => $resultData,
							'isActive' => $this->input->post("isActive"),
							'reportPara' => $reportPara[$i]
						);
						//print_r($tabledata);

						$addLineDataResult = $this->GlobalModel->addWithoutYear($tabledata, 'commoninlinereports', 'COMRL');
						//print_r($addLineDataResult);
						if ($addLineDataResult) {
							$addResultFlag = true;
						}
					}
					$result['AddData'] = $addResultFlag;
				} else {
					$result['AddData'] = false;
				}
			}


			if ($result['coInformation'] != 'false' || $result['AddData'] != 'false') {
				$response['status'] = true;
				$response['message'] = "Common Report Successfully Added.";
			} else {
				$response['status'] = false;
				$response['message'] = "Failed To Add Common Report";
			}


			$this->session->set_flashdata('response', json_encode($response));
			redirect(base_url() . 'index.php/CommonReport/listRecords', 'refresh');
		}
	}



	public function edit()
	{
		$code = $this->uri->segment(3);

		$data['query'] = $this->GlobalModel->selectDataById($code, 'commoninreports');
		$data['lines'] = $this->GlobalModel->selectDataByField('commonCode', $code, 'commoninlinereports');
		//print_r($data1->result());

		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commonreport/edit', $data);
		$this->load->view('dashboard/footer');
	}

	//To update pr in db
	public function update()
	{
		$code =  $this->input->post('code');


		$data = array(
			'reportName' => strtoupper($this->input->post("reportName")),
			'reportSName' => strtoupper($this->input->post("reportSName")),
			'isActive' => $this->input->post("isActive")
		);

		$resultData = $this->GlobalModel->doEdit($data, 'commoninreports', $code);
		// print_r( $resultData );

		$result['noticeResult'] = $resultData;

		$reportPara = $this->input->post('reportPara');



		if ($reportPara[0] != '') {

			$editResultFlag = false;
			for ($i = 0; $i < sizeof($reportPara); $i++) {
				//$single= $showLineCode[$i];
				//print_r($single);

				$editTableData = array(
					'commonCode' => $code,
					'isActive' => $this->input->post("isActive"),
					'reportPara' => $reportPara[$i]
				);
				//print_r($editTableData);


				$addLineDataResult = $this->GlobalModel->doEdit($editTableData, 'commoninlinereports', $code);
				//print_r($addLineDataResult);
				if ($editLineDataResult == 'true') {
					$editResultFlag = true;
				}
			}
		}


		$result['editData'] = $editResultFlag;


		$reportParaAdd = $this->input->post('reportParaAdd');



		if ($reportParaAdd[0] != '') {
			$addResultFlag = false;
			for ($j = 0; $j < sizeof($reportParaAdd); $j++) {

				if ($reportParaAdd[$j] != '') {

					$addLineTableData = array(

						'commonCode' => $code,
						'isActive' => $this->input->post("isActive"),
						'reportPara' => $reportParaAdd[$j]

					);
					// print_r($addLineTableData);
					$addLineDataResult = $this->GlobalModel->addWithoutYear($addLineTableData, 'commoninlinereports', 'COMRL');
					if ($addLineDataResult) {
						$addResultFlag = true;
					}
				}
			}
			$result['AddData'] = $addResultFlag;
		} else {
			$result['AddData'] = false;
		}


		if ($result['noticeResult'] != 'false' || $result['editData'] != 'false' ||  $result['AddData'] != 'false') {
			$response['status'] = true;
			$response['message'] = "Common Reports Successfully Updated .";
		} else {
			$response['status'] = false;
			$response['message'] = "Failed To  Updated Common Reports .";
		}
		print_r($response);
		echo $this->session->set_flashdata('response', json_encode($response));
		redirect(base_url() . 'index.php/CommonReport/listRecords', 'refresh');
	}


	public function listRecords()
	{
		$data['error'] = $this->session->flashdata('response');
		$this->load->view('dashboard/header');
		$this->load->view('dashboard/commonreport/list', $data);
		$this->load->view('dashboard/footer');
	}

	public function getCommonList()
	{
		$tables = array('commoninreports');

		$requiredColumns = array(
			array('code', 'reportName', 'isActive')
		);

		$conditions = array();


		$extraConditionColumnNames = array();

		$extraConditions = array();


		$Records = $this->GlobalModel1->make_datatables($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		// print_r($Records->result());
		$srno = $_GET['start'] + 1;
		$data = array();
		foreach ($Records->result() as $row) {

			if ($row->isActive_02 == "1") {
				$status = " <span class='label label-sm label-success'>Active</span>";
			} else {
				$status = " <span class='label label-sm label-warning'>Inactive</span>";
			}



			$actionHtml = '<div class="btn-group">
                                                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="ti-settings"></i>
                                                        </button>
                                                        <div class="dropdown-menu animated slideInUp" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 35px, 0px);">
                                                            <a class="dropdown-item  blue" data-toggle="modal" data-target="#responsive-modal" data-seq="' . $row->code_00 . '"  href="' . $row->code_00 . '"><i class="ti-eye"></i> Open</a>
                                                            <a class="dropdown-item" href="' . base_url() . 'index.php/CommonReport/edit/' . $row->code_00 . '"><i class="ti-pencil-alt"></i> Edit</a>
                                                            <a class="dropdown-item  mywarning" data-seq="' . $row->code_00 . '" id="' . $row->code_00 . '"><i class="ti-trash"></i> Delete</a>
                                                            
                                                        </div>
                                                    </div>';

			$data[] = array(
				$srno,
				$row->code_00,
				$row->reportName_01,
				$status,
				$actionHtml
			);


			$srno++;
		}
		$dataCount = $this->GlobalModel1->get_all_data($tables, $requiredColumns, $conditions, $extraConditionColumnNames, $extraConditions);
		$output = array(
			"draw"                    =>     intval($_GET["draw"]),
			"recordsTotal"          =>      $dataCount,
			"recordsFiltered"     =>     $dataCount,
			"data"                    =>     $data
		);
		echo json_encode($output);
	}


	public function view()
	{
		$code = $this->input->get('code');

		$data['query'] = $this->GlobalModel->selectDataById($code, 'commoninreports');
		foreach ($data['query']->result() as $row) {

			$activeStatus = "";
			if ($row->isActive == "1") {
				$activeStatus = '<span class="label label-sm label-success">Active</span>';
			} else {
				$activeStatus = '<span class="label label-sm label-warning">Inactive</span>';
			}

			$modelHtmlcontract = "<div>";

			$modelHtmlcontract .= '
						<div class="form-row">
						<div class="col-md-4 mb-3"><label> Common Code: </label>
						<input type="text" class="form-control-line" value="' . $row->code . '"  readonly/></div>
						<div class="col-md-4 mb-3"><label>Report Name: </label>
						<input type="text" class="form-control-line" value="' . $row->reportName . '"  readonly></div>
						<div class="col-md-4 mb-3"><label>Report Short Name: </label>
						<input type="text" class="form-control-line" value="' . $row->reportSName . '"  readonly/></div></div>';

			$modelHtmlStatus = '';
			$modelHtmlStatus .= '<div class="form-group">' . $activeStatus . '</div>';
		}

		$data['lines'] = $this->GlobalModel->selectDataByFieldWithOrder('commonCode', $code, 'commoninlinereports');
		$modelHtmltable = "";
		$modelHtmltable .= ' <div class="table-responsive" >
			             <center><font size="5">Text Para</font></center>
				</div>';

		$modelHtml = '';

		foreach ($data['lines']->result() as $li) {


			$modelHtml .= '<font size="3"><p>' . $li->reportPara . '</p></font>';
		}

		$modelHtmlcontract .= $modelHtmltable .= $modelHtml .= $tbend .= $modelHtmlStatus;
		// $modelHtml.='</form>';
		echo $modelHtmlcontract;
	}



	public function deleteLineRecord()
	{
		$code =  $this->input->post('code');
		echo $this->GlobalModel->deleteForever($code, 'commoninlinereports');
	}
	public function delete()
	{
		$code = $this->input->post('code');

		echo $this->GlobalModel->delete($code, 'commoninreports');
		//	$this->session->set_flashdata('responsePR', json_encode($response));
		redirect(base_url() . 'index.php/CommonReport/listRecords', 'refresh');
	}
}
?>