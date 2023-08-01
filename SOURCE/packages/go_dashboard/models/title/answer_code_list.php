<?php

$csv_answercode_filepath = dirname(__FILE__).DIRECTORY_SEPARATOR.'answercodes.csv';

$answer_codes = array();

if (($handle = fopen($csv_answercode_filepath, "r")) !== FALSE) {
	while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
		$line = array();
		$line['code'] = trim($data[0]);
		$line['name'] = trim($data[1]);
		$line['note'] = trim($data[2]);
		if(isset($data[3])){
			$line['custom_name'] = trim($data[3]);
		}
		$answer_codes[] = $line;
	}
	fclose($handle);
}

print_r($answer_codes);