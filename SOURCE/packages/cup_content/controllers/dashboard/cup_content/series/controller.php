<?php  
Loader::model('series/list', 'cup_content');
Loader::model('series/model', 'cup_content');

class DashboardCupContentSeriesController extends Controller
{
    private $pkgHandle = 'cup_content';

    public function on_start()
    {
        // GCAP-1272 Added by Shane Camus 04/08/2021
        $html = Loader::helper('html');
        $jsPath = (string)$html->javascript('series-script.js', $this->pkgHandle)->file . "?v=1";
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');
    }

    public function view()
    {
        $this->redirect('/dashboard/cup_content/series/search');
    }

    public function edit($seriesId)
    {
        $series = CupContentSeries::fetchByID($seriesId);
        if ($series === false) {
            $_SESSION['alerts'] = array('failure' => 'Invalid Series ID');
            $this->redirect('/dashboard/cup_content/series');
        }
        
        // save
        if (count($this->post()) > 0) {
            $post = CupContentSeries::convertPost($this->post());
            
            Loader::model('collection_types');
            $val = Loader::helper('validation/form');
            $vat = Loader::helper('validation/token');
            
            $val->setData($post);
            $val->addRequired('name', t('Name required.'));
            $val->test();
            
            $error = $val->getError();
        
            if (!$vat->validate('edit_series')) {
                $error->add($vat->getErrorMessage());
            }
        
            if ($error->has()) {
                $_SESSION['alerts'] = array('error' => $error->getList());
                $this->set('entry', $post);
            } else {
                $post = CupContentSeries::convertPost($this->post());
                Loader::helper('tools', 'cup_content');
                
                $series->name               = $post['name'];
                $series->seriesID           = $post['seriesID'];
                $series->trialID            = $post['trialID'];
                $series->prettyUrl          = CupContentToolsHelper::string2prettyURL($series->name);
                $series->shortDescription   = $post['shortDescription'];
                $series->longDescription    = $post['longDescription'];
                $series->yearLevels         = $post['yearLevels'];
                $series->formats            = $post['formats'];
                $series->subjects           = $post['subjects'];
                $series->divisions          = $post['divisions'];
                $series->regions            = $post['regions'];
                $series->compGoUrl          = $post['compGoUrl'];
                $series->compHotUrl         = $post['compHotUrl'];
                $series->compSiteUrl        = $post['compSiteUrl'];
                $series->partnerSiteName    = $post['partnerSiteName'];
                $series->partnerSiteUrl     = $post['partnerSiteUrl'];
                $series->tagline            = $post['tagline'];
                $series->reviews            = $post['reviews'];
                $series->isEnabled          = $post['isEnabled'];
                $series->search_priority    = $post['search_priority'];
                
                $entry = $series->getAssoc();
                
                if ($series->save()) {
                    $_SESSION['alerts'] = array('success' => 'Series has been saved successfully');
                    
                    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                        $filename = $_FILES['image']['tmp_name'];
                        $series->saveImage($filename);
                        $globalGoFilename = $series->saveGlobalGoImage($filename);
                        $series->saveThumbnailURL($globalGoFilename);
                    }
                
                    $this->redirect('/dashboard/cup_content/series');
                } else {
                    $this->set('entry', $entry);
                    $_SESSION['alerts'] = array('error' => $series->errors);
                }
            }
        }
        
        $this->set('series', $series);
        $this->set('entry', $series->getAssoc());
        $this->render('/dashboard/cup_content/series/edit');
    }
    
    function delete($seriesId = false)
    {
        $result = array('result'=>'failure', 'error'=>'unknown error');
        
        $series = CupContentSeries::fetchByID($seriesId);
        if ($series->delete() === true) {
            $result = array('result'=>'success', 'error'=>'unknown error');
        } else {
            $result = array('result'=>'failure', 'error'=>array_shift($series->errors));
        }
        
        echo json_encode($result);
        exit();
    }
    
    public function import()
    {
        if (isset($_FILES['file'])) {
            Loader::model('series/model', 'cup_content');
            Loader::helper('tools', 'cup_content');

            $table = $this->readCSV2Array($_FILES['file']['tmp_name']);
            $fieldMap = $this->importGenerateFieldMap($table);
            
            if ($fieldMap == false) {
                print_r($this->errors);
            }
            
            $fieldMap2 = $this->importGenerateFieldMap2($table);
            
            $table = $this->importGeneratePrepareData($table, $fieldMap, $fieldMap2);
            
            foreach ($table as $row) {
                if (in_array('Australia & New Zealand', $row['regions'])) {
                    $row['regions'] = array_merge(
                        $row['regions'],
                        array(
                            'Australian Capital Territory',
                            'Queensland', 'New South Wales',
                            'Victoria', 'Tasmania', 'Northern Territory',
                            'South Australia', 'Western Australia',
                            'Australia', 'New Zealand'
                        )
                    );
                } elseif (in_array('Australia', $row['regions'])) {
                    $row['regions'] = array_merge(
                        $row['regions'],
                        array(
                            'Australian Capital Territory',
                            'Queensland', 'New South Wales',
                            'Victoria', 'Tasmania', 'Northern Territory',
                            'South Australia', 'Western Australia'
                        )
                    );
                } elseif (in_array('New South Wales', $row['regions']) 
                        || in_array('New South Wales', $row['regions'])
                        || in_array('Northern Territory', $row['regions'])
                        || in_array('Queensland', $row['regions'])
                        || in_array('South Australia', $row['regions'])
                        || in_array('Tasmania', $row['regions'])
                        || in_array('Victoria', $row['regions'])
                        || in_array('Western Australia', $row['regions'])
                        || in_array('Australian Capital Territory', $row['regions'])) {
                    $row['regions'] = array_merge(
                        $row['regions'],
                        array(
                            'Australia'
                        )
                    );
                }
            
                $series = new CupContentSeries();
                $series->seriesID           = $row['series_id'];
                $series->name               = $row['series_name'];
                $series->prettyUrl          = CupContentToolsHelper::string2prettyURL($series->name);
                $series->shortDescription   = $row['short_description'];
                $series->longDescription    = $row['long_description'];
                $series->yearLevels         = $row['yearLevels'];
                $series->formats            = $row['formats'];
                $series->subjects           = $row['subjects'];
                $series->divisions          = $row['divisions'];
                $series->regions            = $row['regions'];
                $series->compGoUrl          = $row['comp_go'];
                $series->compHotUrl         = $row['comp_hot'];
                $series->compSiteUrl        = $row['comp_site'];
                $series->partnerSiteName    = '';
                $series->partnerSiteUrl     = '';
                $series->tagline            = $row['tagline'];
                $series->reviews            = $row['review'];
                
                if (!$series->save()) {
                    echo "series ID: " . $series->seriesID . "  \t name: " . $series->name . "\n";
                    print_r($series->errors);
                }
            }
            
            echo "\n\nimport finished\n\n";
            exit();
        }
        $this->render('/dashboard/cup_content/series/import');
    }
    
    protected function readCSV2Array($filename)
    {
        $table = array();
        
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($data = fgetcsv($handle, 10000, ',')) !== false) {
                $table[] = $data;
            }
            fclose($handle);
            
            return $table;
        } else {
            return false;
        }
    }
    
    protected function importGenerateFieldMap($rawTable)
    {
        $this->errors = array();
        
        $requiredFields = array(
            'series_id',
            'series_name',
            'short_description', 
            'long_description'
        );
        
        $fieldMap = array(
            'series_id'         => false,
            'series_name'       => false,
            'short_description' => false,
            'long_description'  => false,
            'tagline'           => false,
            'comp_go'           => false,
            'comp_hot'          => false,
            'comp_site'         => false,
            'review'            => false
        );
                        
        $fieldAlias = array(
            'series_id'         => array('series id'),
            'series_name'       => array('series name'),
            'short_description' => array('short description', 'short blurb', 'series short blurb'),
            'long_description'  => array('long description', 'long blurb', 'series long blurb'),
            'tagline'           => array('tagline', 'series tagline'),
            'comp_go'           => array('comp go'),
            'comp_hot'          => array('comp hot'),
            'comp_site'         => array('comp site'),
            'review'            => array('review-text', 'review- text', 'review - text')
        );
        
        $header = $rawTable[0];
        
        foreach ($header as $idx => $column_name) {
            $column_name = strtolower(trim($column_name));
            
            $matched = false;
            foreach ($fieldAlias as $field_name => $alias) {
                if (!$matched) {
                    
                    if (strcmp($column_name, $field_name) == 0) {
                        $fieldMap[$field_name] = $idx;
                        $matched = true;
                    } elseif (in_array($column_name, $alias)) {
                        $fieldMap[$field_name] = $idx;
                        $matched = true;
                    } else {
                        // echo "\t $field_name no match\n";
                    }
                }
            }
        }
        
        foreach ($requiredFields as $field_name) {
            if ($fieldMap[$field_name] === false) {
                $this->errors[] = "column name \"{$field_name}\" is required";
            }
        }
        
        if (count($this->errors) > 0) {
            return false;
        }
        return $fieldMap;
    }
    
    protected function importGenerateFieldMap2($rawTable)
    {
        $this->errors = array();
        
        $requiredFields = array(
            'series_id',
            'series_name',
            'short_description', 
            'long_description');

        $subjects = array(
            'Australian Curriculum'             => false,
            'Arts'                              => false,
            'Business, Economics, and Legal'    => false,
            'English'                           => false,
            'English Shakespeare'               => false,
            'Food'                              => false,
            'Geography'                         => false,
            'Health & PE'                       => false,
            'History'                           => false,
            'Homework'                          => false,
            'Humanities'                        => false,
            'International Education'           => false,
            'IWB Software'                      => false,
            'Latin and other Languages'         => false,
            'Literacy'                          => false,
            'Mathematics'                       => false,
            'Philosophy and Critical Thinking'  => false,
            'Religion'                          => false,
            'Sciences'                          => false,
            'Special Needs'                     => false,
            'Study Guides'                      => false,
            'IT and other Technology'           => false,
            'Vocational'                        => false,
        );
        
        $formats = array(
            'Print'                                     => false,
            'Interactive Textbook (one year)'           => false,
            'Interactive Textbook (two year)'           => false,
            'PDF Textbook'                              => false,
            'Student CD-ROM'                            => false,
            'Print Workbook'                            => false,
            'Electronic Workbook or Electronic Version' => false,
            'Print Toolkit'                             => false,
            'Digital Toolkit'                           => false,
            'Web App'                                   => false,
            'Vodcast'                                   => false,
            'Audio CD'                                  => false,
            'Cambridge HOTmaths'                        => false,
            'Teacher Resource Package'                  => false,
            'Teacher CD-ROM / DVD-ROM'                  => false,
            'Print Teacher Resource'                    => false
        );
                    
        $fieldMap = array(
            'primary'   => false,
            'secondary' => false,
            'aus'       => false,
            'nz'        => false,
            'anz'       => false,
            'qld'       => false,
            'nsw'       => false,
            'vic'       => false,
            'sa'        => false,
            'wa'        => false,
            'nt'        => false,
            'tas'       => false,
            'act'       => false,
            'y11-12'    => false,
            'y7-10'     => false,
            'y7-8'      => false,
            'y9-10'     => false,
            'y1-6'      => false,
            'yf-2'      => false,
            'y3-4'      => false,
            'y5-6'      => false,
            'y12'       => false,
            'y11'       => false,
            'y10'       => false,
            'y9'        => false,
            'y8'        => false,
            'y7'        => false,
            'y6'        => false,
            'y5'        => false,
            'y4'        => false,
            'y3'        => false,
            'y2'        => false,
            'y1'        => false,
            'f'         => false
        );
        
        $fieldAlias = array(
            'primary'   => array(),
            'secondary' => array(),
            'aus'       => array(),
            'nz'        => array(),
            'anz'       => array(),
            'qld'       => array(),
            'nsw'       => array(),
            'vic'       => array(),
            'sa'        => array(),
            'wa'        => array(),
            'nt'        => array(),
            'tas'       => array(),
            'act'       => array(),
            'y11-12'    => array(),
            'y7-10'     => array(),
            'y7-8'      => array(),
            'y9-10'     => array(),
            'y1-6'      => array(),
            'yf-2'      => array(),
            'y3-4'      => array(),
            'y5-6'      => array(),
            'y12'       => array(),
            'y11'       => array(),
            'y10'       => array(),
            'y9'        => array(),
            'y8'        => array(),
            'y7'        => array(),
            'y6'        => array(),
            'y5'        => array(),
            'y4'        => array(),
            'y3'        => array(),
            'y2'        => array(),
            'y1'        => array(),
            'f'         => array(),
        );
                        
        foreach ($subjects as $subject => $val) {
            $fieldMap[strtolower($subject)] = false;
            $fieldAlias[strtolower($subject)] = array();
        }
        
        foreach ($formats as $key => $val) {
            $fieldMap[strtolower($key)] = false;
            $fieldAlias[strtolower($key)] = array();
        }
        
        $header = $rawTable[0];
        
        foreach ($header as $idx => $column_name) {
            $column_name = strtolower(trim($column_name));
            
            $matched = false;
            foreach ($fieldAlias as $field_name => $alias) {
                if (!$matched) {
                    $field_name = strtolower(trim($field_name));
                    if (strcmp($column_name, $field_name) == 0) {
                        $fieldMap[$field_name] = $idx;
                        $matched = true;
                    } elseif (in_array($column_name, $alias)) {
                        $fieldMap[$field_name] = $idx;
                        $matched = true;
                    } else {
                        //echo "\t $field_name no match\n";
                    }
                }
            }
        }
        
        if (count($this->errors) > 0) {
            return false;
        }

        return $fieldMap;
    }
    
    protected function importGeneratePrepareData($rawTable, $fieldMap, $fieldMap2)
    {
        $data = array();
        for ($i = 1, $max = count($rawTable); $i < $max; $i++) {
            $row = $rawTable[$i];
            $rowData = array();
            
            foreach ($fieldMap as $field_name => $idx) {
                if ($idx !== false) {
                    $rowData[$field_name] = trim($row[$idx]);
                } else {
                    $rowData[$field_name] = '';
                }
            }
            
            $formats = array(
                'Print'                                     => false,
                'Interactive Textbook (one year)'           => false,
                'Interactive Textbook (two year)'           => false,
                'PDF Textbook'                              => false,
                'Student CD-ROM'                            => false,
                'Print Workbook'                            => false,
                'Electronic Workbook or Electronic Version' => false,
                'Print Toolkit'                             => false,
                'Digital Toolkit'                           => false,
                'Web App'                                   => false,
                'Vodcast'                                   => false,
                'Audio CD'                                  => false,
                'Cambridge HOTmaths'                        => false,
                'Teacher Resource Package'                  => false,
                'Teacher CD-ROM / DVD-ROM'                  => false,
                'Print Teacher Resource'                    => false
            );
            
            $subjects = array(
                'Australian Curriculum'             => false,
                'Arts'                              => false,
                'Business, Economics, and Legal'    => false,
                'English'                           => false,
                'English Shakespeare'               => false,
                'Food'                              => false,
                'Geography'                         => false,
                'Health & PE'                       => false,
                'History'                           => false,
                'Homework'                          => false,
                'Humanities'                        => false,
                'International Education'           => false,
                'IWB Software'                      => false,
                'Latin and other Languages'         => false,
                'Literacy'                          => false,
                'Mathematics'                       => false,
                'Philosophy and Critical Thinking'  => false,
                'Religion'                          => false,
                'Sciences'                          => false,
                'Special Needs'                     => false,
                'Study Guides'                      => false,
                'IT and other Technology'           => false,
                'Vocational'                        => false,
            );
            
            $rowData['divisions'] = array();
            $rowData['regions'] = array();
            $rowData['subjects'] = array();
            $rowData['formats'] = array();
            
            foreach ($fieldMap2 as $field_name => $idx) {
                $field_name = strtolower($field_name);
                
                if ($idx !== false) {
                    $value = trim($row[$idx]);
                    if (strlen($value) > 0 && in_array($field_name, array('primary'))) {
                        $rowData['divisions'][] = 'Primary';
                    } elseif (strlen($value) > 0 && in_array($field_name, array('secondary'))) {
                        $rowData['divisions'][] = 'Secondary';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'anz') == 0) {
                        $rowData['regions'][] = 'Australia & New Zealand';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'aus') == 0) {
                        $rowData['regions'][] = 'Australia';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'nz') == 0) {
                        $rowData['regions'][] = 'New Zealand';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'qld') == 0) {
                        $rowData['regions'][] = 'Queensland';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'nsw') == 0) {
                        $rowData['regions'][] = 'New South Wales';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'vic') == 0) {
                        $rowData['regions'][] = 'Victoria';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'sa') == 0) {
                        $rowData['regions'][] = 'South Australia';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'wa') == 0) {
                        $rowData['regions'][] = 'Western Australia';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'nt') == 0) {
                        $rowData['regions'][] = 'Northern Territory';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'tas') == 0) {
                        $rowData['regions'][] = 'Tasmania';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'act') == 0) {
                        $rowData['regions'][] = 'Australian Capital Territory';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y11-12') == 0) {
                        $rowData['yearLevels'][] = '11-12';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y7-10') == 0) {
                        $rowData['yearLevels'][] = '7-10';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y7-8') == 0) {
                        $rowData['yearLevels'][] = '7-8';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y9-10') == 0) {
                        $rowData['yearLevels'][] = '9-10';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y1-6') == 0) {
                        $rowData['yearLevels'][] = '1-6';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'yf-2') == 0) {
                        $rowData['yearLevels'][] = 'F-2';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y5-6') == 0) {
                        $rowData['yearLevels'][] = '5-6';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y12') == 0) {
                        $rowData['yearLevels'][] = '12';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y11') == 0) {
                        $rowData['yearLevels'][] = '11';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y10') == 0) {
                        $rowData['yearLevels'][] = '10';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y9') == 0) {
                        $rowData['yearLevels'][] = '9';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y8') == 0) {
                        $rowData['yearLevels'][] = '8';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y7') == 0) {
                        $rowData['yearLevels'][] = '7';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y6') == 0) {
                        $rowData['yearLevels'][] = '6';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y5') == 0) {
                        $rowData['yearLevels'][] = '5';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y4') == 0) {
                        $rowData['yearLevels'][] = '4';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y3') == 0) {
                        $rowData['yearLevels'][] = '3';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y2') == 0) {
                        $rowData['yearLevels'][] = '2';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'y1') == 0) {
                        $rowData['yearLevels'][] = '1';
                    } elseif (strlen($value) > 0 && strcmp($field_name, 'f') == 0) {
                        $rowData['yearLevels'][] = 'F';
                    }
                    
                    $found_subject = false;
                    foreach ($subjects as $key => $val) {
                        if (strlen($value) > 0 && strcmp($field_name, strtolower($key)) == 0) {
                            $rowData['subjects'][] = $key;
                            $found_subject = true;
                        }
                    }
                    
                    
                    $found_format = false;
                    foreach ($formats as $key => $val) {
                        if (strlen($value) > 0 && strcmp($field_name, strtolower($key)) == 0) {
                            $rowData['formats'][] = $key;
                            $found_format = true;
                        }
                    }
                }
            }
            
            $data[] = $rowData;
        }
        
        return $data;
    }
    
    
    
    public function fixSeriesImages()
    {
        $folder = DIR_BASE . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'cup_content' . DIRECTORY_SEPARATOR .
            'images' . DIRECTORY_SEPARATOR . 'series' . DIRECTORY_SEPARATOR . 'import';
            
        if ($handle = opendir($folder)) {

            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array('.', '..'))) {
                    $ext = pathinfo ($entry, PATHINFO_EXTENSION);
                    $seriesID = basename($entry, '.'.$ext);
                    echo $seriesID . "\n";
                    $series = CupContentSeries::fetchBySeriesID($seriesID);
                    if ($series) {
                        $series->saveImage($folder . DIRECTORY_SEPARATOR . $entry);
                    }
                }
            }

            closedir($handle);
        }
        
        echo "\nfinished";
        exit();
    }

    // GCAP-1272 Added by Shane Camus 04/08/2021
    public function getTrialId()
    {
        $trialID = $_POST['trialId'];
        $series = CupContentSeries::fetchByTrialId($trialID);
        if (isset($series) && is_array($series) && count($series) > 0) {
            echo json_encode(array('seriesName'=> $series['name']));
            exit;
        } else {
            echo json_encode(array('seriesName'=>'available'));
            exit;
        }
    }
}