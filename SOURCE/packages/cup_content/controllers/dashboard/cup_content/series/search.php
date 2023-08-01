<?php
Loader::model('series/list', 'cup_content');
Loader::model('series/model', 'cup_content');

class DashboardCupContentSeriesSearchController extends Controller
{
    public function view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content'));
        $this->addHeaderItem($html->css('cup_content.css', 'cup_content'));
        $this->addHeaderItem($html->css('wform.css', 'cup_content'));

        $list = new CupContentSeriesList();

        if ($_REQUEST['numResults']) {
            $list->setItemsPerPage($_REQUEST['numResults']);
        }

        // SB-435 modified by jbernardez 20200110
        if (isset($_GET['format'])) {
            $list->filterByFormat($_GET['format'], true);
        }

        if ($_GET['keywords'] !== '') {
            $list->filterByNameKeywords($_GET['keywords']);
        }

        if (isset($_GET['ajax'])) {
            echo Loader::packageElement(
                'series/dashboard_search',
                'cup_content',
                array(
                    'series' => $list->getPage(),
                    'seriesList' => $list,
                    'pagination' => $list->getPagination()
                )
            );
            exit();
        }

        $this->set('seriesList', $list);
        $this->set('series', $list->getPage());
        $this->set('pagination', $list->getPagination());
    }
}