<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('event/model', 'cup_competition');
Loader::model('event/list', 'cup_competition');

class DashboardGoDashboardNewSubscriptionsController extends Controller {
	public function view() {
		$list = new CupCompetitionEventList();

		if ($_REQUEST['numResults']) {
			$list->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if ($_GET['keywords'] != '') {
			$list->filterByKeywords($_GET['keywords']);
		}
		
		if(isset($_GET['ajax'])){
			echo Loader::packageElement('event/dashboard_search', 'cup_competition', 
								array('eventList' => $list)
						);
			exit();
		}
		
		$this->set('eventList', $list);		
	}
	
	public function add(){
		$event = new CupCompetitionEvent();
	
		if(count($this->post()) > 0){
			$event->setPost($this->post());
			//print_r($event->getAssoc());
			//exit();
			if($event->save()){
				$_SESSION['alerts'] = array('success' => 'Event has been saved successfully');
					
				$this->redirect("/dashboard/cup_competition/event");
			}else{
				$_SESSION['alerts'] = array('error' => $event->getErrors());
			}
		}
		
		//unset($_POST);
		$this->set('eventObj', $event);
		$this->render('/dashboard/cup_competition/event/add');
	}
	
	public function edit($event_id){
		$event = new CupCompetitionEvent($event_id);
		
		if(count($this->post()) > 0){
			$event->setPost($this->post());
			//print_r($event->getAssoc());
			//exit();
			if($event->save()){
				$_SESSION['alerts'] = array('success' => 'Event has been saved successfully');
					
				$this->redirect("/dashboard/cup_competition/event");
			}else{
				$_SESSION['alerts'] = array('error' => $event->getErrors());
			}
		}
		
		$this->set('eventObj', $event);
		$this->render('/dashboard/cup_competition/event/edit');
	}
	
	public function delete($event_id){
		$result = array('result'=>'failure', 'error'=>'unknown error');
		
		$event = CupCompetitionEvent::fetchByID($event_id);
		if($event->delete() === TRUE){
			$result = array('result'=>'success', 'error'=>'unknown error');
		}else{
			$result = array('result'=>'failure', 'error'=>array_shift($event->errors));
		}
		
		echo json_encode($result);
		exit();
	}
	
	
}