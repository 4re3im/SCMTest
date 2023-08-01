<?php
Loader::model('title/list', 'cup_content');
Loader::model('title/model', 'cup_content');

class DashboardCupContentTitlesSearchController extends Controller
{
    public function view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content'));
        $this->addHeaderItem($html->css('cup_content.css', 'cup_content'));
        $this->addHeaderItem($html->css('wform.css', 'cup_content'));

        $list = new CupContentTitleList();
        if ($_REQUEST['numResults']) {
            $list->setItemsPerPage($_REQUEST['numResults']);
        }

        if ($_GET['format'] != '') {
            $list->filterByFormat($_GET['format'], true);
        }

        if ($_GET['keywords'] != '') {
            $list->filterByNameKeywords($_GET['keywords']);
        }

        if (isset($_GET['isbn']) && strlen($_GET['isbn']) > 0) {
            $list->filterByISBN($_GET['isbn']);
        }
        //show only go product
        //https://jira.cambridge.org/browse/ANZUAT-88
        $list->filterByGoTitle();

        if (isset($_GET['ajax'])) {
            echo Loader::packageElement(
                'title/dashboard_search',
                'cup_content',
                array(
                    'titles' => $list->getPage(),
                    'titleList' => $list,
                    'pagination' => $list->getPagination()
                )
            );
            exit();
        }

        $this->set('titleList', $list);
        $this->set('titles', $list->getPage());
        $this->set('pagination', $list->getPagination());
    }
}
