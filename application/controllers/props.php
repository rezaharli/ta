<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Props extends Private_Controller {
	function __construct() {
		parent::__construct();
		$this->load->model('pengajuan_proposal_himpunan_model');
		$this->load->model('logbook_proposal_himpunan_model');
		$this->load->model('proposal_himpunan_model');
		$this->load->model('himpunan_model');
		$this->load->model('staff_model');
		$this->load->model('user_model');
	}

	function logbook(){
		$user = $this->user_model->get_user_dan_role_by_id($this->session->userdata('id'));
		
		if ($user->role == 'staff') {
			$proposals	= $this->logbook_proposal_himpunan_model->get_all();
		}else {
			$himpunan 	= $this->himpunan_model->get_by(array('id_penanggungjawab' => $user->roled_data->nim));
			$proposals 	= $this->logbook_proposal_himpunan_model->get_many_by(array('pengaju' => $himpunan->id));
		}
		
		$data['proposals'] = array();
		foreach ($proposals as $proposal) {
			$pengaju 		= $this->himpunan_model->get_by(array('id' => $proposal->pengaju));
			$get_id_staff 	= $this->staff_model->get_by(array('nip' => $proposal->penanggungjawab));
			$get_nama_user 	= $this->user_model->get($get_id_staff->id_user);

			array_push($data['proposals'], array(
				'id'						=> $proposal->id,
				'pengaju' 					=> $pengaju->nama,
				'tanggal_pengajuan' 		=> $proposal->tanggal_pengajuan,
				'judul' 					=> $proposal->judul,
				'tanggal_proposal_terakhir' => $proposal->tanggal_proposal_terakhir,
				'status_approve' 			=> $proposal->status_approve,
				'penanggungjawab'			=> $get_nama_user->nama
			));
		}

		if ($user->role == 'staff') {
			$this->load_page('page/private/staff/logbook_proposal_himpunan', $data);
		}else{
			$this->load_page('page/private/himpunan/logbook_proposal', $data);
		}
		
	}

	function detail(){
		$id_pengajuan = $this->input->get('id');

		$proposals = $this->proposal_himpunan_model->get_many_by(array('id_pengajuan_proposal' => $id_pengajuan));

		$data['proposals'] = array();
		foreach ($proposals as $proposal) {

			array_push($data['proposals'], array(
				'judul'				=> $proposal->judul,
				'tanggal_upload'	=> $proposal->waktu_upload,
				'status'			=> $proposal->status_approve
			));
			
		}
		// $results = array();
		// foreach ($proposals as $proposal) {
		// 	$result = array(
		// 		'judul'				=> $proposal->judul,
		// 		'tanggal_upload'	=> $proposal->waktu_upload,
		// 		'status'			=> $proposal->status_approve
		// 	);
		// 	array_push($results, $result);
		// }
		// json_encode($results);
		$this->load_page('page/private/staff/logbook_proposal_himpunan_detail', $data);
	}
}