<?php  
Loader::model('format/list', 'cup_content');
Loader::model('format/model', 'cup_content');

class DashboardCupContentFormatsController extends Controller {

	public function on_start(){
		//echo "hello world";
		//exit();
	}

	public function view() {
		//$this->redirect('/dashboard/cup_content/formats/search');
		$formatList = new CupContentFormatList();
		$formatList->setItemsPerPage(999999);
		if ($_REQUEST['numResults']) {
			$formatList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		$formatList->sortBy('name', 'asc');
		
		$this->set('formatList', $formatList);		
		$this->set('formats', $formatList->getPage());		
		$this->set('pagination', $formatList->getPagination());
	}

	public function edit($format_id = false) {
		$format = CupContentFormat::fetchByID($format_id);
		if($format === FALSE){
			$this->redirect("/dashboard/cup_content/formats");
		}
		
		if(count($this->post()) > 0){	//save
			Loader::model('collection_types');
			$val = Loader::helper('validation/form');
			$vat = Loader::helper('validation/token');
		
			$val->setData($this->post());
			$val->addRequired("name", t("Format name required."));
			$val->addRequired("shortDescription", t("Short Description required."));
			$val->addRequired("longDescription", t("Long Description required."));
			$val->test();
			
			$error = $val->getError();
		
			if (!$vat->validate('edit_format')) {
				$error->add($vat->getErrorMessage());
			}
			
			if ($error->has()) {
				$this->set('error', $error);
			}else{
				
				$post = $this->post();
				
				Loader::helper('tools', 'cup_content');
				
				$format->name = $post['name'];
				$format->prettyUrl = CupContentToolsHelper::string2prettyURL($format->name);
				$format->shortDescription = $post['shortDescription'];
				$format->longDescription = $post['longDescription'];
				$format->isDigital = $post['isDigital'];
				
				if($format->save()){
					$_SESSION['alerts'] = array('success' => 'Format has been saved successfully');
					
					if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
						$format->saveImage($_FILES['image']['tmp_name']);
					}
					
					if(isset($_FILES['image_small']) && $_FILES['image_small']['error'] == 0){
						$format->saveSmallImage($_FILES['image_small']['tmp_name']);
					}
					
					$this->redirect("/dashboard/cup_content/formats");
					//$this->set('entry', $entry);
				}else{
					$this->set('entry', $entry);
					//$_SESSION['alerts'] = array('error' => $format->errors);
					$this->set('error', $format->errors);
				}
			}
		}
		
		$this->set('format', $format);
		$this->set('entry', $format->getAssoc());
		$this->render('/dashboard/cup_content/formats/edit');
	}
	
	public function delete($format_id = false) {
		$result = array('result'=>'failure', 'error'=>'unknown error');
		
		$format = CupContentFormat::fetchByID($format_id);
		if($format && $format->delete() === TRUE){
			$result = array('result'=>'success', 'error'=>'unknown error');
		}elseif($format){
			$result = array('result'=>'failure', 'error'=>array_shift($format->errors));
		}else{
			$result = array('result'=>'failure', 'error'=>'Invalid id');
		}
		
		echo json_encode($result);
		exit();
	}
	
	
	public function testImport(){
		Loader::helper('tools', 'cup_content');
		
		$data = array(
					array('name' => 'Print',
							'shortDescription' => 'Print',
							'longDescription' => 'Print'
						),
					array('name' => 'Interactive Textbook (one year)',
							'shortDescription' => 'The Interactive Textbook is an HTML version of the student text',
							'longDescription' => "is an HTML version of the student text that delivers a host of interactive features to enhance the teaching and learning experience of the student text. It is viewed through an internet browser. \n
Access: To access the Interactive Textbook, register for a Cambridge GO account and enter the unique 16-character access code found in the sealed pocket supplied on purchase. This code also provides access to the downloadable PDF Textbook, to ensure that you can view the textbook in digital format without internet connection. \n
Purchase:  The Interactive Textbook is available as a calendar year subscription. A calendar year subscription is defined as follows: if activation occurs between January and August of this year, subscription concludes on the 31st December this year. If activation occurs between September and December of this year, subscription concludes on the 31st December the following year.  \n
The Interactive Textbook is available for purchase separately or in a bundle with other Cambridge resources. Check the included components for each title in this series for more information."

						),
					array('name' => 'Interactive Textbook (two year)',
							'shortDescription' => 'The Interactive Textbook is an HTML version of the student text ',
							'longDescription' => "is an HTML version of the student text that delivers a host of interactive features to enhance the teaching and learning experience of the student text. It is viewed through an internet browser. \n
Access: To access the Interactive Textbook, register for a Cambridge GO account and enter the unique 16-character access code found in the sealed pocket supplied on purchase. This code also provides access to the downloadable PDF Textbook, to ensure that you can view the textbook in digital format without internet connection. \n
Purchase: The Interactive Textbook is available for purchase separately or in a bundle with other Cambridge resources. \n
The Interactive Textbook is available as a two-calendar-year subscription. A two-calendar-year subscription is defined as follows: if activation occurs between January and August of this year, subscription concludes on the 31st December in the following year. If activation occurs between September and December of this year, subscription concludes on the 31st December in the year after the following."

						),
					array('name' => 'PDF Textbook',
							'shortDescription' => 'The PDF Textbook is a downloadable PDF version of the student ',
							'longDescription' => "is a downloadable PDF version of the student text that enables students to take notes and bookmark pages. The PDF Textbook is designed for full functionality using the latest version of Adobe Reader. The markup function is available in selected PDF readers for the iPad and other devices using iOS (Check your PDF reader specifications.) 
Access: To access the PDF Textbook, register for a Cambridge GO account and enter the unique 16-character access code found in the front of the Print Textbook or in the sealed pocket supplied on purchase. This code may also provide access to extra resources on Cambridge GO.\n
Purchase: Access to the PDF Textbook is included with the purchase of any Interactive Textbook and with most Print Textbooks published from 2010. It may also be available for purchase separately in a sealed pocket where an Interactive Textbook is not offered. Check the included components for each title in this series for more information."

						),
					array('name' => 'Student CD-ROM',
							'shortDescription' => 'The Student CD-ROM is provided in the inside back cover of selected Print Textbooks. May include a PDF of the textbook',
							'longDescription' => 'is provided in the inside back cover of selected Print Textbooks. The Student CD-ROM may include a PDF of the textbook and extra resources.'
						),
					array('name' => 'Print Workbook',
							'shortDescription' => 'The Print Workbook is a print version of the workbook',
							'longDescription' => 'is a print version of the workbook.'
						),
					array('name' => 'Electronic Workbook or Electronic Version',
							'shortDescription' => 'The Electronic Workbook or Electronic Version is a PDF version of the workbook.',
							'longDescription' => "is a PDF version of the student workbook which enables students to input answers, take notes, bookmark pages and print. This product is designed for full functionality using the latest version of Adobe Reader. The markup function is available in selected PDF readers for the iPad and other devices using iOS. (PDF Expert is one viable option. Check your PDF reader specifications.) \n
Access: To access the Electronic Workbook, register for a Cambridge GO account and enter the unique 16-character access code found in the sealed pocket supplied on purchase. \n
Purchase: The Electronic Workbook is available for purchase separately and may also be available for purchase in a bundle with other Cambridge resources. Check the included components for each title in this series for more information."
						),
					array('name' => 'Print Toolkit',
							'shortDescription' => 'The Print Toolkit is a print version of the Toolkit',
							'longDescription' => "is a print version of the Toolkit. \n
Purchase: The Toolkit may be included with purchase of the Print Textbook or it may be available for purchase separately or in a bundle with other Cambridge resources. Check the included components for each title in this series for more information."

						),
					array('name' => 'Digital Toolkit',
							'shortDescription' => 'a PDF version of the Toolkit that enables students to take notes and bookmark pages',
							'longDescription' => "is a PDF version of the Toolkit that enables students to take notes and bookmark pages. The PDF Textbook is designed for full functionality using the latest version of Adobe Reader. The markup function is available in selected PDF readers for the iPad and other devices using iOS (Check your PDF reader specifications.)\n
Access: To access the Digital Toolkit, register for a Cambridge GO account and enter the unique 16-character access code found in the front of the printed textbook or in the sealed pocket supplied on purchase. \n
Purchase: The Digital Toolkit may be included with purchase of the student text in print or digital format. It may also be available for purchase separately or in a bundle with other Cambridge resources. Check the included components for each title in this series for more information."
						),
					array('name' => 'Web App',
							'shortDescription' => 'a mobile app designed to run in a web browser on a mobile device, or any Mac, PC or tablet device',
							'longDescription' => "is a mobile app designed to run in a web browser on an iPhone or iPod Touch, Android mobile device, Windows Phone 7 or any Mac, PC or tablet device. \n
Access: To access the Web App on your smart phone, launch the mobile web browser and type in the Web App address. You can then save the Web App address to your home screen and access the app simply by tapping the icon. You can also use the app in the web browser of your Mac, PC or tablet device. \n
Purchase: The Web App is included with purchase of selected Cambridge products. It is not currently available for purchase separately."

						),
					array('name' => 'Vodcast',
							'shortDescription' => 'a short animated video podcast that can be used by students at home for revision or displayed in the classroom',
							'longDescription' => "is a short animated video podcast that can be used by students at home for revision or displayed in the classroom as a pre-topic visual demonstration. \n
Purchase: The Vodcast may be purchased from this website individually or as a complete set. Once purchased, the Vodcast(s) can be downloaded directly to your device.  Please contact us to discuss network licence options for your school. "

						),
					array('name' => 'Audio CD',
							'shortDescription' => 'offers audio-only materials such as voice or music recordings',
							'longDescription' => 'offers audio-only materials such as voice or music recordings. It may be played in a CD-player or through the CD drive of a personal computer. '
						),
					array('name' => 'Cambridge HOTmaths',
							'shortDescription' => 'Cambridge HOTmaths is an interactive online maths learning, teaching and assessment resource.',
							'longDescription' => "offers the best in interactive online maths learning, teaching and assessment resources for Australian students and teachers. The rich curriculum-based content is integrated with our Australian Curriculum maths textbooks to provide a state-of-the-art and comprehensive teaching and learning program for the new national curriculum.\n
Access: Students in classes set up to use the Cambridge HOTmaths learning management system will be provided with a username and password by their teacher. Log on to the HOTmaths website using this username and password, then enter the unique 16-character validation code found in the sealed pocket supplied on purchase of a product bundled with HOTmaths. You cannot access HOTmaths using a bundled validation code if your whole class does not subscribe to HOTmaths. If you are unsure, check with your teacher or contact us for more information.\n
Purchase: HOTmaths is available on subscription, and is valid from the date the validation code is activated until 28 February in the following year.  A HOTmaths validation code can be purchased in a bundle with a Cambridge textbook, for use only by students in classes that have subscribed to the HOTmaths learning management system (LMS).   Teachers may purchase a teacher subscription by contacting HOTmaths via the HOTmaths website. Individual students may purchase a HOTmaths subscription directly from the HOTmaths website.   www.cambridge.edu.au/hotmaths"

						),
					array('name' => 'Teacher Resource Package',
							'shortDescription' => 'The Teacher Resource Package is online time-saving planning, classroom and assessment support resource.',
							'longDescription' => "offers valuable time-saving planning, classroom and assessment support resources for teachers. Most of these resources are available as downloadable materials that can be modified as required. \n
Access: To access the Teacher Resource Package, register for a Cambridge GO teacher account. Once your teacher account has been verified, enter the unique 16-character code found in the sealed pocket supplied on purchase or inside selected Print Teacher Resources.\n
Downloaded materials may be placed on your school network for use by other teachers at your school. If the Teacher Resource Package includes non-downloadable resources, the sealed pocket will include additional codes for activation by colleagues in their own verified Cambridge GO teacher accounts.\n
Purchase: The Teacher Resource Package is available for purchase separately. Note: The Teacher Resource Package does not include access to the PDF Textbook. To access the PDF Textbook, activate the unique16-character code found in your teacher copy of the Print Textbook (or sealed pocket if you have purchased a digital-only teacher copy) in your Cambridge GO teacher account."

						),
					array('name' => 'Teacher CD-ROM / DVD-ROM',
							'shortDescription' => 'The Teacher CD or DVD –ROM is a time-saving planning, classroom and assessment support resource.',
							'longDescription' => "offers valuable time-saving planning, classroom and assessment support resources for teachers. This resource may include photocopiable materials and/or resources that can be displayed in class using a data projector or interactive white board.\n
Purchase: The Teacher CD-ROM or DVD-ROM is usually available for purchase separately."
						),
					array('name' => 'Print Teacher Resource',
							'shortDescription' => 'The Print Teacher Resource offers valuable teacher support materials in print format.',
							'longDescription' => "offers valuable teacher support materials in print format. This resource may include some photocopiable materials. It may also include access to additional materials on Cambridge GO using a unique 16-character code printed in the front of the printed book. Refer to specific title details for more information on each Print Teacher Resource.\n
Purchase: The Print Teacher Resource is available for purchase separately.  "

						)
					
				);
				
		foreach($data as $idx => $entry){
			$format = new CupContentFormat();
			$format->id = '';
			$format->name = $entry['name'];
			$format->prettyUrl = CupContentToolsHelper::string2prettyURL($format->name);
			$format->shortDescription = $entry['shortDescription'];
			$format->longDescription = $entry['longDescription'];
			$format->save();
		}
		
		$this->redirect("/dashboard/cup_content/formats");
	}
}