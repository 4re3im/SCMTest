<?php  
Loader::model('subject/list', 'cup_content');
Loader::model('subject/model', 'cup_content');

class DashboardCupContentSubjectsSearchController extends Controller
{
    public function view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 
        $this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
        
        $subjectList = new CupContentSubjectList();
        if ($_REQUEST['numResults']) {
            $subjectList->setItemsPerPage($_REQUEST['numResults']);
        }
        
        if ($_GET['keywords'] != '') {
            $subjectList->filterByKeywords($_GET['keywords']);
        }   
        
        if(isset($_GET['ajax'])){
            echo Loader::packageElement(
                'subject/dashboard_search', 'cup_content', 
                array('subjects' => $subjectList->getPage(), 
                    'subjectList' => $subjectList, 
                    'pagination' => $subjectList->getPagination()
                )
            );
            exit();
        }
        
        $this->set('subjects', $subjectList->getPage());
        $this->set('subjectList', $subjectList);
        $this->set('pagination', $subjectList->getPagination());
    }
}