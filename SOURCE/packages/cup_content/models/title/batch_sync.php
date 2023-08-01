<?php 
ini_set("memory_limit","256M");
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('title/model', 'cup_content');
Loader::model('series/model', 'cup_content');


Loader::library('PHPExcel/PHPExcel', 'cup_content');
Loader::library('PHPExcel/PHPExcel/IOFactory', 'cup_content');
Loader::library('PHPExcel/PHPExcel/ReferenceHelper', 'cup_content');

define('CUP_CONTENT_SYNC_FOLDER', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
				'sync'.DIRECTORY_SEPARATOR);

class CupContentTitleBatchSync extends Object {
	protected $errors = array();
	protected $filename = false;
	protected $raw_table = array();
	protected $static_fields_map = array();
	protected $varible_fields_map = array();
	
	protected $data = array();
	
	protected $static_column_alias = array(
										'isbn13' => array('isbn 13'),
										'isbn10' => array('isbn 10'),
										'aus_price' => array('aus price'),
										'nz_price' => array('nz price'),
										'aus_available_stock' => array('aus available stock', 'aus stock'),
										'nz_available_stock' => array('nz available stock', 'nz stock'),
										'Availability' => array(),
										
								);
								
	protected $varible_fields = array(
								
								);
	
	function __construct($filename = false) {
		$this->filename = $filename;
	}
	
	public function getErrors(){
		return $this->errors;
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
				
				if(strcmp($field, 'isbn13') == 0){
					$row_entry[$field] = str_pad($cell_value, 13, "0", STR_PAD_LEFT);
				}else if(strcmp($field, 'isbn10') == 0){
					$row_entry[$field] = str_pad($cell_value, 10, "0", STR_PAD_LEFT);
				}else{
					$row_entry[$field] = $cell_value;
				}
			}
			
			$this->data[] = $row_entry;
		}
	}
	
	
	
	
	public function process($type = 'csv', $sheetname = false, $excel_type = 'Excel5'){
		$this->errors = array();
		
		//$this->filename = $filename;
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
		
		foreach($this->data as $idx => $each){	
			$titleObject = CupContentTitle::fetchByISBN13($each['isbn13']);
			
			if($titleObject === FALSE){
				$titleObject = CupContentTitle::fetchByISBN10($each['isbn10']);
			}
			
			if($titleObject){
				if(strlen($each['aus_price']) > 0 && floatval($each['aus_price']) > 0.01){
					if($titleObject->updateProduct('AU', $each['aus_price'], $each['aus_available_stock'])){
					
					}else{
						$this->errors[] = $each['isbn13'].":\t	Failed to update AUS product";
					}
				}
				
				if(strlen($each['nz_price']) > 0  && floatval($each['nz_price']) > 0.01){
					if($titleObject->updateProduct('NZ', $each['nz_price'], $each['nz_available_stock'])){
					
					}else{
						$this->errors[] = $each['isbn13'].":\t	Failed to update NZ product";
					}
				}
				
				if(isset($each['Availability']) && strlen($each['Availability']) > 0){
					$titleObject->availability = $each['Availability'];
					if($titleObject->save()){
					
					}else{
						$this->errors[] = $each['isbn13'].":\t	Failed to Availability";
						print_r($titleObject->errors);
						exit();
					}
				}
			}
			
		}
		
		if($this->errors && count($this->errors) > 0){
			print_r($this->errors);
		}
		
	}
	
	
}