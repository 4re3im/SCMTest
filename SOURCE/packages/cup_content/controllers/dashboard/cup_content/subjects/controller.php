<?php  
Loader::model('subject/list', 'cup_content');
Loader::model('subject/model', 'cup_content');

class DashboardCupContentSubjectsController extends Controller
{
    public function view()
    {
        $this->redirect('/dashboard/cup_content/subjects/search');
    }

    public function edit($subjectID = false)
    {
        $subject = CupContentSubject::fetchByID($subjectID);
        if ($subject === false) {
            $_SESSION['alerts'] = array('failure' => 'Invalid Subject ID');
            $this->redirect("/dashboard/cup_content/subjects");
        }
        
        // SAVE
        if (count($this->post()) > 0) {
            Loader::model('collection_types');
            $val = Loader::helper('validation/form');
            $vat = Loader::helper('validation/token');
        
            $val->setData($this->post());
            $val->addRequired("name", t("name required."));
            $val->test();
            
            $error = $val->getError();
        
            if (!$vat->validate('edit_author')) {
                $error->add($vat->getErrorMessage());
            }
            
            if ($error->has()) {
                $_SESSION['alerts'] = array('error' => $error->getList());
            } else {
                $post = $this->post();

                $subject->name = $post['name'];
                $subject->description = $post['description'];
                $subject->region = $post['region'];
                
                if (isset($post['isPrimary'])) {
                    $subject->isPrimary = true;
                } else {
                    $subject->isPrimary = false; 
                }
                
                if (isset($post['isSecondary'])) {
                    $subject->isSecondary = true;
                } else {
                    $subject->isSecondary = false; 
                }
                
                // SB-399 added by jbernardez 20191112
                if (isset($post['isEnabled'])) {
                    $subject->isEnabled = true;
                } else {
                    $subject->isEnabled = false; 
                }
                
                if ($subject->save()) {
                    $_SESSION['alerts'] = array('success' => 'Subject has been saved successfully');
                    $this->redirect("/dashboard/cup_content/subjects");
                } else {
                    $this->set('entry', $entry);
                    $_SESSION['alerts'] = array('error' => $subject->errors);
                }
            }
        }
        
        $this->set('entry', $subject->getAssoc());
        $this->render('/dashboard/cup_content/subjects/edit');
    }
    
    function delete($subjectID = false)
    {
        $result = array('result'=>'failure', 'error'=>'unknown error');
        
        $subject = CupContentSubject::fetchByID($subjectID);
        if ($subject->delete() === true) {
            $result = array('result'=>'success', 'error'=>'unknown error');
        } else {
            $result = array('result'=>'failure', 'error'=>array_shift($subject->errors));
        }
        
        echo json_encode($result);
        exit();
    }
    
    public function testImport()
    {
        Loader::helper('tools', 'cup_content');
        
        $data = array(
            array(
                'name' => 'Australian Curriculum',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Arts',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Business, Economics, and Legal',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'English',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'English Shakespeare',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Food',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Geography',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Health & PE',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'History',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Homework',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Humanities',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'International Education',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'IWB Software',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => false
            ),
            array(
                'name' => 'Latin and other Languages',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Literacy',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Mathematics',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Philosophy and Critical Thinking',
                'description' => '',
                'isPrimary' => true,
                'isSecondary' => true
            ),
            array(
                'name' => 'Religion',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Sciences',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Special Needs',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Study Guides',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'IT and other Technology',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            ),
            array(
                'name' => 'Vocational',
                'description' => '',
                'isPrimary' => false,
                'isSecondary' => true
            )
        );
        
        foreach ($data as $idx => $entry) {
            $subject = new CupContentSubject();
            $subject->id = '';
            $subject->name = $entry['name'];
            $subject->prettyUrl = CupContentToolsHelper::string2prettyURL($format->name);
            $subject->description = $entry['description'];
            
            if ($entry['isPrimary']){
                $subject->isPrimary = true;
            }
            
            if ($entry['isSecondary']){
                $subject->isSecondary = true;
            }
            
            $subject->save();
        }
        
        $this->redirect("/dashboard/cup_content/subjects");
    }
}