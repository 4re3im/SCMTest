<?php  

Loader::model('subject/model', 'cup_content');

class DashboardCupContentSubjectsAddController extends Controller
{
    public function view()
    {
        
    }
    
    public function on_start()
    {
        $this->set('disableThirdLevelNav', true);
    }
    
    public function submit()
    {
        Loader::model('collection_types');
        $val = Loader::helper('validation/form');
        $vat = Loader::helper('validation/token');
        
        $val->setData($this->post());
        $val->addRequired("name", t("Name required."));
        $val->test();
        
        $error = $val->getError();
    
        if (!$vat->validate('create_subject')) {
            $error->add($vat->getErrorMessage());
        }
        
    
        if ($error->has()) {
            $_SESSION['alerts'] = array('error' => $error->getList());
            $this->set('entry', $this->post());
        } else {
            $post = $this->post();
            Loader::helper('tools', 'cup_content');
            
            $subject = new CupContentSubject();
            $subject->id = $post['id'];
            $subject->name = $post['name'];
            $subject->prettyUrl = CupContentToolsHelper::string2prettyURL($subject->name);
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
            
            $entry = $subject->getAssoc();
            
            if ($subject->save()) {
                $_SESSION['alerts'] = array('success' => 'New Subject has been added successfully');
                $this->redirect("/dashboard/cup_content/subjects");
            } else {
                $this->set('entry', $entry);
                $_SESSION['alerts'] = array('error' => $author->errors);
            }
        }
    }
}