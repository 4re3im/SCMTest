<?php

/**
 * Search and display product subjects
 *
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 25, 2015
 */

class GoSearchController extends Controller
{
    protected $pkgHandle = 'go_product';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        $html = Loader::helper('html');

        // Modified for ANZGO-3196 by Paul Balila, 2017-01-16
        $this->addFooterItem($html->javascript('product.js', $this->pkgHandle));
        $this->addHeaderItem($html->css('style.css', $this->pkgHandle));
    }

    public function view()
    {
        // Modified for ANZGO-3196 by Paul Balila, 2017-01-16
        $subjectHelper = Loader::helper('subject', $this->pkgHandle);
        $subject = CupContentSubject::fetchSubjectByGoSubject();
        $subjects = $subjectHelper->formatSubjectList(array_chunk($subject, 4));
        $this->set('subjects', $subjects);
    }

    // ANZGO-3726 Modified by Shane Camus 05/17/18
    public function searchByKeyWord()
    {
        $params = $this->post();

        if (!$params['keyword']) {
            $this->redirect('/go');
        }

        $cupContentSearch = new CupContentSearch();
        $subjectHelper = Loader::helper('subject', $this->pkgHandle);
        $cupContentSearch->filterByKeyWord($params['keyword']);
        echo $subjectHelper->formatSearchList($cupContentSearch);
        exit();
    }
}
