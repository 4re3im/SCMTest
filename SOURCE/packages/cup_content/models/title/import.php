<?php 
ini_set("memory_limit","256M");
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('title/model', 'cup_content');
Loader::model('series/model', 'cup_content');


Loader::library('PHPExcel/PHPExcel', 'cup_content');
Loader::library('PHPExcel/PHPExcel/IOFactory', 'cup_content');
Loader::library('PHPExcel/PHPExcel/ReferenceHelper', 'cup_content');


class CupContentTitleImport extends Object {
	protected $errors = array();
	protected $filename = false;
	protected $raw_table = array();
	protected $static_fields_map = array();
	protected $varible_fields_map = array();
	
	protected $data = array();
	
	protected $static_column_alias = array(
										'isbn13' => array('isbn 13'),
										'isbn10' => array('isbn 10'),
										'hasAccessCode' => array('has access code'),
										'hasInspectionCopy' => array('has inspection copy'),
										'isEnabled' => array('is enabled', 'title_enable', 'istitleenabled', 'is title enabled'),
										'hasDownloadableFile' => array('hasDownloadableFile'),
										'title' => array(),
										'custom_title' => array('custom title'),
										'subtitle' => array(),
										'custom_subtitle' => array('custom subtitle'),
										'series_name' => array('series name'),
										'series_id' => array('series id'),
										'short_description' => array('short blurb', 'title short blurb', 'short description'),
										'tagline' => array('title tagline'),
										'feature' => array('title features'),
										'content' => array('contents'),
										'reviews' => array('Review- TEXT', 'Review - TEXT'),
										'goUrl' => array('GO'),
										'previewUrl' => array('preview')
										
								);
								
	protected $varible_fields = array(
									'edition' => array(
											'1ed' => array(),
											'2ed' => array(),
											'3ed' => array(),
											'4ed' => array(),
											'5ed' => array()
										),
									'authors' => array(
											'author 1' => array(),
											'author 2' => array(),
											'author 3' => array(),
											'author 4' => array(),
											'author 5' => array()
										),
									'formats' => array(
											'Print' => array(),
											'Interactive Textbook (one year)' => array('the interactive textbook (oneyear)'),
											'Interactive Textbook (two year)' => array('The Interactive Textbook (two year)'),
											'PDF Textbook' => array('The PDF Textbook'),
											'Student CD-ROM' => array('The Student CD-ROM'),
											'Print Workbook' => array('The Print Workbook'),
											'Electronic Workbook or Electronic Version' => array('The Electronic Workbook or Electronic Version'),
											'Print Toolkit' => array('The Print Toolkit'),
											'Digital Toolkit' => array('The Digital Toolkit'),
											'Web App' => array('The Web App'),
											'Vodcast' => array('The Vodcast'),
											'Audio CD' => array(),
											'Cambridge HOTmaths' => array(),
											'Teacher Resource Package' => array('The Teacher Resource Package'),
											'Teacher CD-ROM / DVD-ROM' => array('The Teacher CD-ROM / DVD-ROM'),
											'Print Teacher Resource' => array()
											
										),
									'yearLevels' => array(
											'11-12' => array('y11-12'),
											'7-10' => array('y7-10'),
											'7-8' => array('y7-8'),
											'9-10' => array('y9-10'),
											'F-6' => array('yF-6'),
											'F-2' => array('yF-2'),
											'3-4' => array('y3-4'),
											'5-6' => array('y5-6'),
											'12' => array('y12'),
											'11' => array('y11'),
											'10' => array('y10'),
											'9' => array('y9'),
											'8' => array('y8'),
											'7' => array('y7'),
											'6' => array('y6'),
											'5' => array('y5'),
											'4' => array('y4'),
											'3' => array('y3'),
											'2' => array('y2'),
											'1' => array('y1'),
											'f' => array('yF'),
										),
									'divisions' => array(
											'Primary' => array(),
											'Secondary' => array()
										),
									'relatedTitles' => array(
											'Also 1' => array(),
											'Also 2' => array(),
											'Also 3' => array(),
											'Also 4' => array(),
											
										),
									'supportingTitles' => array(
											'Supporting Prod1' => array(),
											'Supporting Prod2' => array(),
											'Supporting Prod3' => array(),
											'Supporting Prod4' => array(),
										),
									'type' => array(
											'Stand alone' => array(),
										),
									'regions' => array(
											'Australia' => array('AU', 'AUS'),
											'New Zealand' => array('NZ'),
											'Australia & New Zealand' => array('ANZ'),
											'Queensland' => array('QLD'),
											'New South Wales' => array('NSW'),
											'Victoria' => array('VIC'),
											'South Australia' => array('SA'),
											'Western Australia' => array('WA'),
											'Northern Territory' => array('NT'),
											'Tasmania' => array('TAS'),
											'Australian Capital Territory' => array('ACT'),
										),
									'subjects' => array(
											"Australian Curriculum"=>array(),
											"Arts"=>array(),
											"Business, Economics, and Legal"=>array(),
											"English"=>array(),
											"English Shakespeare"=>array(),
											"Food"=>array(),
											"Geography"=>array(),
											"Health & PE"=>array(),
											"History"=>array(),
											"Homework"=>array(),
											"Humanities"=>array(),
											"International Education"=>array(),
											"IWB Software"=>array(),
											"Latin and other Languages"=>array(),
											"Literacy"=>array(),
											"Mathematics"=>array(),
											"Philosophy and Critical Thinking"=>array(),
											"Religion"=>array(),
											"Sciences"=>array(),
											"Special Needs"=>array(),
											"Study Guides"=>array(),
											"IT and other Technology"=>array(),
											"Vocational"=>array()
										)
								);
	
	function __construct($filename = false) {
		$this->filename = $filename;
	}
	
	
	public function readCSV2Array($filename){
		$table = array();
		if (($handle = fopen($filename, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
				$table[] = $data;
			}
			fclose($handle);
			
			return $table;
		}else{
			return false;
		}
	}
	
	public function readExcel52Array($filename, $sheetname = false){

		$inputFileType = 'Excel5';
		$inputFileName = $filename;
		
		//$sheetname = 'TITLES in series (2)'; 
		//move_uploaded_file($_FILES['file']['tmp_name']);

		/**  Create a new Reader of the type defined in $inputFileType  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		
		if($sheetname){
			$objReader->setLoadSheetsOnly($sheetname); 
		}
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);
		
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		return array_merge($sheetData); 
	}
	
	public function readExcel20072Array($filename, $sheetname = false){
		Loader::library('PHPExcel/PHPExcel', 'cup_content', $args = null);
		Loader::library('PHPExcel/PHPExcel/IOFactory', 'cup_content', $args = null);

		$inputFileType = 'Excel2007';
		$inputFileName = $filename;
		
		//$sheetname = 'TITLES in series (2)'; 
		//move_uploaded_file($_FILES['file']['tmp_name']);

		/**  Create a new Reader of the type defined in $inputFileType  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		
		if($sheetname){
			$objReader->setLoadSheetsOnly($sheetname); 
		}
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);
		
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		return array_merge($sheetData); 
	}
	
	public function readExcel2Array($filename, $sheetname = false, $inputFileType = 'Excel5'){
		//echo "[readExcel2Array]";
	
		Loader::library('PHPExcel/PHPExcel', 'cup_content', $args = null);
		Loader::library('PHPExcel/PHPExcel/IOFactory', 'cup_content', $args = null);

		$inputFileName = $filename;
		
		//$sheetname = 'TITLES in series (2)'; 
		//move_uploaded_file($_FILES['file']['tmp_name']);

		/**  Create a new Reader of the type defined in $inputFileType  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		
		if($sheetname){
			$objReader->setLoadSheetsOnly($sheetname); 
		}
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($inputFileName);
		
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		return array_merge($sheetData); 
	}
	
	public function generateFieldMaps(){
		if(count($this->raw_table) > 1){
			$header = $this->raw_table[0];
			foreach($header as $idx => $fieldname){
				$found_match = false;
			
				$fieldname = strtolower(trim($fieldname));
				//echo $fieldname.":\n";
				foreach($this->static_column_alias as $key => $alias){
					if(!$found_match && !isset($this->static_fields_map[$key])){
						for($i = 0; $i < count($alias); $i++){
							$alias[$i] = strtolower($alias[$i]);
						}
						
						$key_lower_case = strtolower($key);
						//echo '['.$key_lower_case.']';
						if(strcmp($fieldname, $key_lower_case) == 0){
							$this->static_fields_map[$key] = $idx;
							$found_match = true;
						}else if(in_array($fieldname, $alias)){
							$this->static_fields_map[$key] = $idx;
							$found_match = true;
						}
					}
				}
				//echo "\n";
				
				
				foreach($this->varible_fields as $field_type => $fields){
					if(!$found_match){
						foreach($fields as $key => $alias){
							if(!$found_match && !isset($this->varible_fields_map[$field_type][$key])){
								for($i = 0; $i < count($alias); $i++){
									$alias[$i] = strtolower($alias[$i]);
								}
								
								$key_lower_case = strtolower($key);
								//echo '['.$key.']';
								if(strcmp($fieldname, $key_lower_case) == 0){
									$this->varible_fields_map[$field_type][$key] = $idx;
									$found_match = true;
								}else if(in_array($fieldname, $alias)){
									$this->varible_fields_map[$field_type][$key] = $idx;
									$found_match = true;
								}
							}
						}
					}
				}
				//echo "\n";
			}
		}
	}
	
	public function prepareData(){
		$this->data = array();
		
		for($i = 1; $i < count($this->raw_table); $i++){
			$row_entry = array();
			$row = $this->raw_table[$i];
			
			foreach($this->static_fields_map as $field => $idx){
				$cell_value = trim($row[$idx]);
				
				if(strcmp($field, 'hasAccessCode') == 0){
					if(strlen($cell_value) > 0){
						$row_entry[$field] = 1;
					}else{
						$row_entry[$field] = 0;
					}
				}else if(strcmp($field, 'isEnabled') == 0){
					if(strlen($cell_value) > 0){
						$row_entry[$field] = 1;
					}else{
						$row_entry[$field] = 0;
					}
				}else if(strcmp($field, 'hasDownloadableFile') == 0){
					if(strlen($cell_value) > 0){
						$row_entry[$field] = 1;
					}else{
						$row_entry[$field] = 0;
					}
				}else if(strcmp($field, 'hasInspectionCopy') == 0){
					if(strlen($cell_value) > 0){
						$row_entry[$field] = 1;
					}else{
						$row_entry[$field] = 0;
					}
				}else{
					$row_entry[$field] = $cell_value;
				}
			}
			
			
			$row_entry['authors'] = array();
			$row_entry['formats'] = array();
			$row_entry['yearLevels'] = array();
			$row_entry['regions'] = array();
			$row_entry['divisions'] = array();
			foreach($this->varible_fields_map as $type => $field_detail){
				if(strcmp($type, 'edition') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['edition'] = $field;
						}
					}
				}elseif(strcmp($type, 'authors') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['authors'][] = $cell_value;
						}
					}
				}elseif(strcmp($type, 'formats') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['formats'][] = $field;
						}
					}
				}elseif(strcmp($type, 'yearLevels') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['yearLevels'][] = $field;
						}
					}
				}elseif(strcmp($type, 'regions') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['regions'][] = $field;
						}
					}
				}elseif(strcmp($type, 'subjects') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['subjects'][] = $field;
						}
					}
				}elseif(strcmp($type, 'relatedTitles') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['relatedTitles'][] = $cell_value;
						}
					}
				}elseif(strcmp($type, 'supportingTitles') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['supportingTitles'][] = $cell_value;
						}
					}
				}elseif(strcmp($type, 'divisions') == 0){
					foreach($field_detail as $field => $idx){
						$cell_value = trim($row[$idx]);
						if(strlen($cell_value) > 0){
							$row_entry['divisions'][] = $field;
						}
					}
				}
			}
			
			if(strlen($row_entry['series_name']) > 0){
				$row_entry['type'] = 'part of series';
			}else{
				$row_entry['type'] = 'stand alone';
			}
			
			$this->data[] = $row_entry;
		}
	}
	
	
	
	
	public function process($type = 'csv', $sheetname = false, $excel_type = 'Excel5'){
	
		//echo "Type: $type	\nfilename: $sheetname	\nExcel Type: $excel_type";
		if(strcmp($type, 'excel') == 0){
			$this->raw_table = $this->readExcel2Array($this->filename, $sheetname, $excel_type);
			//exit();
			//$this->raw_table = array_slice($this->raw_table,0,10);
		}else{
			$this->raw_table = $this->readCSV2Array($this->filename);
		}
		
		//print_r($this->raw_table[0]);
		$this->generateFieldMaps();
		//print_r($this->static_fields_map);
		
		//print_r($this->varible_fields_map);
		
		$this->prepareData();
		//print_r($this->static_fields_map);
		//print_r($this->varible_fields_map);
		
		//print_r($this->data);
		//exit();
		//print_r($this->raw_table);
		
		$db = Loader::db();
		
		foreach($this->data as $idx => $each){			
			if(in_array('Australia & New Zealand', $each['regions'])){
				//echo "Region Australia & New Zealand detected \n";
				//print_r($each['regions']);
				$each['regions'] = array_merge($each['regions'], array(
																	'Australian Capital Territory',
																	'Queensland', 'New South Wales',
																	'Victoria', 'Tasmania', 'Northern Territory',
																	'South Australia', 'Western Australia',
																	'Australia', 'New Zealand'
																));
				if(!in_array('Australia', $each['regions'])){
					$each['regions'][] = 'Australia';
				}
				if(!in_array('New Zealand', $each['regions'])){
					$each['regions'][] = 'New Zealand';
				}
			}elseif(in_array('Australia', $each['regions'])){
				$each['regions'] = array_merge($each['regions'], array(
																	'Australian Capital Territory',
																	'Queensland', 'New South Wales',
																	'Victoria', 'Tasmania', 'Northern Territory',
																	'South Australia', 'Western Australia'
																));
			}elseif(in_array('New South Wales', $each['regions']) 
					|| in_array('New South Wales', $each['regions'])
					|| in_array('Northern Territory', $each['regions'])
					|| in_array('Queensland', $each['regions'])
					|| in_array('South Australia', $each['regions'])
					|| in_array('Tasmania', $each['regions'])
					|| in_array('Victoria', $each['regions'])
					|| in_array('Western Australia', $each['regions'])
					|| in_array('Australian Capital Territory', $each['regions'])){
					
				$each['regions'] = array_merge($each['regions'], array(
																	'Australia'
																));
			}
		
			
		
		
			$title = new CupContentTitle();
			
			$each = $title::convertPost($each);
			
			$title->id 					= "";
			$title->isbn13				= str_pad($each['isbn13'], 13, "0", STR_PAD_LEFT);
			$title->isbn10				= str_pad($each['isbn10'], 10, "0", STR_PAD_LEFT);
			$title->hasAccessCode		= $each['hasAccessCode'];
			$title->isEnabled			= $each['isEnabled'];
			$title->hasDownloadableFile	= (isset($each['hasDownloadableFile']) && $each['hasDownloadableFile'] == 1 ? 1: 0);
			
			$title->name				= $each['title'];
			$title->customName			= $each['custom_title'];
			$title->subtitle			= $each['subtitle'];
			$title->customSubtitle		= $each['custom_subtitle'];
			$title->edition				= $each['edition'];
			//$title->prettyUrl			= CupContentToolsHelper::string2prettyURL($title->name);
			$title->shortDescription	= $each['short_description'];
			$title->longDescription		= $each['long_description'];
			$title->feature				= $each['feature'];
			$title->content				= $each['content'];
			$title->yearLevels			= $each['yearLevels'];
			$title->publishDate			= (isset($each['publishDate']) ? $each['publishDate'] : "");
			$title->availability		= (isset($each['availability']) ? $each['availability'] : "");
			$title->goUrl				= $each['goUrl'];
			$title->previewUrl			= $each['previewUrl'];
			$title->type				= $each['type'];		//part of series, stand alone, study guide
			$title->series				= (isset($each['series_name']) ? $each['series_name'] : "");
			$title->descriptionOption 	= 'title short description';
			$title->divisions			= $each['divisions'];
			$title->regions 			= $each['regions'];
			$title->tagline				= $each['tagline'];
			$title->reviews				= $each['reviews'];
			
			
			$title->formats				= $each['formats'];
			$title->subjects			= $each['subjects'];
			$title->authors				= $each['authors'];
			
			
			if(isset($each['series_id'])){
				$sr = CupContentSeries::fetchBySeriesID($each['series_id']);
				if($sr){
					$title->series				= $sr->name;
					$each['series_load_obj']	= $sr;
				}
			}
			
			if($title->save()){
				$this->data[$idx]['saved_id'] = $title->id;
				
				if(isset($each['relatedTitles']) && is_array($each['relatedTitles'])){
					foreach($each['relatedTitles'] as $each_isbn){
						$tmp_query = "INSERT INTO CupContentTitleRelatedTitle (titleID, isbn13) VALUES (?, ?)";
						$tmp_result = $db->Execute($tmp_query, array($title->id, $each_isbn));
						if(!$tmp_result){
							$this->errors[] = "Related Title [ISBN13: {$each_isbn}] could not be added.";
							$is_saved = false;
						}
					}
				}
				
				if(isset($each['supportingTitles']) && is_array($each['supportingTitles'])){
					foreach($each['supportingTitles'] as $each_isbn){
						$tmp_query = "INSERT INTO CupContentTitleSupportingTitle (titleID, isbn13) VALUES (?, ?)";
						$tmp_result = $db->Execute($tmp_query, array($title->id, $each_isbn));
						if(!$tmp_result){
							$this->errors[] = "Supporting Title [ISBN13: {$each_isbn}] could not be added.";
							$is_saved = false;
						}
					}
				}
			}else{
				$tmp_object = CupContentTitle::fetchByISBN13($title->isbn13);
				$title->id = $tmp_object->id;
				
				echo '['.$tmp_object->id.'|'.$title->isbn13.']';
				//print_r($title);
				
				//try update
				if($title->save()){
					echo $title->isbn13." Updated\n";
					$this->data[$idx]['saved_id'] = $title->id;
					
					/*
					if(isset($each['relatedTitles']) && is_array($each['relatedTitles'])){
						foreach($each['relatedTitles'] as $each_isbn){
							$tmp_query = "INSERT INTO CupContentTitleRelatedTitle (titleID, isbn13) VALUES (?, ?)";
							$tmp_result = $db->Execute($tmp_query, array($title->id, $each_isbn));
							if(!$tmp_result){
								$this->errors[] = "Related Title [ISBN13: {$each_isbn}] could not be added.";
								$is_saved = false;
							}
						}
					}
					
					if(isset($each['supportingTitles']) && is_array($each['supportingTitles'])){
						foreach($each['supportingTitles'] as $each_isbn){
							$tmp_query = "INSERT INTO CupContentTitleSupportingTitle (titleID, isbn13) VALUES (?, ?)";
							$tmp_result = $db->Execute($tmp_query, array($title->id, $each_isbn));
							if(!$tmp_result){
								$this->errors[] = "Supporting Title [ISBN13: {$each_isbn}] could not be added.";
								$is_saved = false;
							}
						}
					}
					*/
				}else{
					echo "ISBN: {$each['isbn13']}  \t Title: {$each['title']} \n";
					if(in_array('Name has been used', $title->errors)){
						
					}
					
					if(in_array('Invalid Series', $title->errors)){
						echo "\tSeries ID: {$each['series_id']}  \t Name: {$each['series_name']} \n";
						if(isset($each['series_load_obj'])){
							print_r($each['series_load_obj']);
						}else{
							echo "no series object found\n";
						}
					}
					print_r($title->errors);
					
					echo "\n";
				}
			}
		}
		
		
	}
	
	
}