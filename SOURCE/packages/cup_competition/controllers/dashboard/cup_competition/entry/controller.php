<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('event/model', 'cup_competition');
Loader::model('event/list', 'cup_competition');

Loader::model('event_entry/model', 'cup_competition');
Loader::model('event_entry/list', 'cup_competition');

class DashboardCupCompetitionEntryController extends Controller {

	public function view() {
		$list = new CupCompetitionEventList();

		if ($_REQUEST['numResults']) {
			$list->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if ($_GET['keywords'] != '') {
			$list->filterByKeywords($_GET['keywords']);
		}
		
		if(isset($_GET['ajax'])){
			echo Loader::packageElement('event/dashboard_search_entry', 'cup_competition', 
								array('eventList' => $list)
						);
			exit();
		}
		
		$this->set('eventList', $list);		
	}
	
	public function viewByEvent($eventID){
		$list = new CupCompetitionEventEntryList();
		$list->filterByEventID($eventID);
		
		if ($_REQUEST['numResults']) {
			$list->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if(isset($_GET['ajax'])){
			echo Loader::packageElement('event_entry/dashboard_search', 'cup_competition', 
								array('entryList' => $list)
						);
			exit();
		}
		
		$this->set('eventID', $eventID);
		$this->set('entryList', $list);	
		$this->render('/dashboard/cup_competition/entry/view_by_event');
	}
	
	public function exportByEvent($eventID){
		$list = new CupCompetitionEventEntryList();
		$list->filterByEventID($eventID);

		$eventObj = new CupCompetitionEvent($eventID);
		
		$results = $list->get(9999, 0);
		
		loader::library('ExcelWriterXML/ExcelWriterXML', 'cup_competition');
		
		$filename = "cup_competition_export_".date('Y-m-d-H-i-s').".xml";
		$xml = new ExcelWriterXML($filename);
		$xml->docAuthor('CUP');

		
		$format = $xml->addStyle('StyleHeader');
		$format->fontBold();
		
		$format = $xml->addStyle('StyleBody');
		
		
		$format = $xml->addStyle('pendingStatus');
		$format->fontColor('blue');
		
		$format = $xml->addStyle('approvedStatus');
		$format->fontColor('green');
		
		$format = $xml->addStyle('rejectedStatus');
		$format->fontColor('red');
		
		$sheet = $xml->addSheet($eventObj->name);

		$sheet->writeString(1,1,'Entry ID','StyleHeader');
		$sheet->writeString(1,2,'First Name','StyleHeader');
		$sheet->writeString(1,3,'Last Name','StyleHeader');
		$sheet->writeString(1,4,'Email','StyleHeader');
		
		$column_idx = 5;
		if(is_array($eventObj->form_config)){
			foreach($eventObj->form_config as $field){
				$sheet->writeString(1, $column_idx, $field['field_name'], 'StyleHeader');
				
				$column_idx++;
			}
		}
		
		if(is_array($eventObj->qa_question)){
			foreach($eventObj->qa_question as $field){
				$sheet->writeString(1, $column_idx, $field['field_name'], 'StyleHeader');
				
				$column_idx++;
			}
		}
		
		$sheet->writeString(1, $column_idx, 'Status', 'StyleHeader');
		$column_idx++;
		
		$sheet->writeString(1, $column_idx, 'Created At', 'StyleHeader');
		$column_idx++;
		
		$sheet->writeString(1, $column_idx, 'Note', 'StyleHeader');
		
		
		$row_idx = 2;
		foreach($results as $entry){
			$entry = $entry->getAssoc();
			$sheet->writeString($row_idx, 1, $entry['id'], 'StyleBody');
			$sheet->writeString($row_idx, 2, $entry['first_name'], 'StyleBody');
			$sheet->writeString($row_idx, 3, $entry['last_name'], 'StyleBody');
			$sheet->writeString($row_idx, 4, $entry['email'], 'StyleBody');
			
			$column_idx = 5;
			if(is_array($eventObj->form_config)){
				foreach($eventObj->form_config as $field){
					$field_value = $entry['question_data'][$field['field_name']];
					if(is_array($field_value)){
						$field_value = implode(", ", $field_value);
					}
					$sheet->writeString($row_idx, $column_idx, $field_value, 'StyleBody');
					$column_idx++;
				}
			}
			
			if(is_array($eventObj->qa_question)){
				foreach($eventObj->qa_question as $field){
					$field_value = $entry['qa_answer'][$field['field_name']];
					if(is_array($field_value)){
						$field_value = implode(", ", $field_value);
					}
					$sheet->writeString($row_idx, $column_idx, $field_value, 'StyleBody');
					$column_idx++;
				}
			}
			
			$sheet->writeString($row_idx, $column_idx, $entry['status'], $entry['status'].'Status');
			$column_idx++;
			
			$sheet->writeString($row_idx, $column_idx, $entry['createdAt'], 'StyleBody');
			$column_idx++;
			
			$sheet->writeString($row_idx, $column_idx, $entry['note'], 'StyleBody');
		
			$row_idx++;
		}
		
		
		$xml->sendHeaders();
		$xml->writeData();
		exit();
		
	}
	
	public function delete($entryID = false){
		$result = array('result'=>'failure', 'error'=>'unknown error');
		
		$entry = CupCompetitionEventEntry::fetchByID($entryID);
		if($entry->delete() === TRUE){
			$result = array('result'=>'success', 'error'=>'unknown error');
		}else{
			$result = array('result'=>'failure', 'error'=>array_shift($entry->errors));
		}
		
		echo json_encode($result);
		exit();
	}
	
	public function viewEntry($entryID){
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('competition_dashboard.css', 'cup_competition')); 
	
		$entry = new CupCompetitionEventEntry($entryID);
		
		if(count($this->post())>0){
			$old_status = $entry->status;
			$post = $this->post();
			$entry->status = $post['status'];
			$entry->note = $post['note'];
			if($entry->save()){
				if(strcmp($old_status, 'pending') == 0 && strcmp($post['status'], 'approved') == 0){
					$this->on_entry_approval($entry);
				}if(strcmp($old_status, 'pending') == 0 && strcmp($post['status'], 'rejected') == 0){
					$this->on_entry_rejection($entry);
				}
				
				$_SESSION['alerts'] = array('success' => 'Entry has been saved.');
				$url = '/dashboard/cup_competition/entry/viewByEvent/'.$entry->eventID;
				$this->redirect($url);
			}else{
				$_SESSION['alerts'] = array('error' => $entry->getErrors());
			}
		}
		
		
		$this->set('entryObj', $entry);	
		$this->render('/dashboard/cup_competition/entry/view_entry');
	}
	
	protected function on_entry_approval($entry){
		$pkg  = Package::getByHandle('cup_competition');
		$email_from = false;
		
		$eventAssoc = $entry->getEventObject()->getAssoc();
		$category = $eventAssoc['category'];
		
		if(strcmp(strtoupper($category), 'HSC') == 0 && $pkg->config('HSC_NOTIFICATION_EMAIL')){
			$email_from = $pkg->config('HSC_NOTIFICATION_EMAIL');
		}elseif(strcmp(strtoupper($category), 'VCE') == 0 && $pkg->config('VCE_NOTIFICATION_EMAIL')){
			$email_from = $pkg->config('VCE_NOTIFICATION_EMAIL');
		}


		$mh = Loader::helper('mail');
		$mh->addParameter('entryObj', $entry);
		$mh->to($entry->email);
		$mh->load('notification_on_approval','cup_competition');
		$mh->from($email_from);
		@$mh->sendMail();
	}
	
	protected function on_entry_rejection($entry){
		$pkg  = Package::getByHandle('cup_competition');
		$email_from = false;
		
		$eventAssoc = $entry->getEventObject()->getAssoc();
		$category = $eventAssoc['category'];
		
		if(strcmp(strtoupper($category), 'HSC') == 0 && $pkg->config('HSC_NOTIFICATION_EMAIL')){
			$email_from = $pkg->config('HSC_NOTIFICATION_EMAIL');
		}elseif(strcmp(strtoupper($category), 'VCE') == 0 && $pkg->config('VCE_NOTIFICATION_EMAIL')){
			$email_from = $pkg->config('VCE_NOTIFICATION_EMAIL');
		}


		$mh = Loader::helper('mail');
		$mh->addParameter('entryObj', $entry);
		$mh->to($entry->email);
		$mh->load('notification_on_rejection','cup_competition');
		$mh->from($email_from);
		@$mh->sendMail();
	}
	
}
