<?php
	$config = array(
		'interval' => 4,
		'slider_data' => array(
				array(
					'image'=> 'http://cdn.motinetwork.net/motifake.com/image/demotivational-poster/1207/higher-education-the-same-and-different-demotivational-posters-1342867787.jpg',
					'title'=>	'Graduation',
					'description'=> 'An event where the commencement spearker tells thousands of student dressed in indentical caps ..',
					'link'=> '',
				),
				array(
					'image'=> 'http://static7.depositphotos.com/1278120/774/i/950/depositphotos_7748254-Education-poster.jpg',
					'title'=>	'BOOK Dictionary',
					'description'=> 'something something something something something ... ...',
					'link'=> '',
				),
				array(
					'image'=> 'http://1.bp.blogspot.com/-QCrdcvkIU4U/TZXgQm9zv7I/AAAAAAAAADg/0jhY3_NLCGI/s1600/EducationPoster2.jpg',
					'title'=>	'Education For All',
					'description'=> 'blah blah blah blah blah ... ...',
					'link'=> '',
				)
			)
	);
	
	echo json_encode($config);
?>