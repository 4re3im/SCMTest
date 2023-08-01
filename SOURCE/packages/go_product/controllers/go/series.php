<?php
/**
 * Search and display product subjects
 *
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 25, 2015
 */

class GoSeriesController extends Controller
{

    protected $pkgHandle = 'go_product';
    // ANZGO-3691 added by jbernardez 20180418
    const PAGE_BROWSE = 'Browse';
    const ACTION_VIEW_SERIES = 'View Series';
    const SERIES_PAGE = 'Series Page';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        $html = Loader::helper('html');

        // ANZGO-3493 Removed by John Renzo Sunico, Aug 17, 2017
        // This is added globally on themes; Causing conflicts to subject page;
        // $this->addHeaderItem($html->javascript('product.js', $this->pkgHandle));
        $this->addHeaderItem($html->css('style.css', $this->pkgHandle));
    }

    // SB-347 modified by jbernardez 20190929
    public function view($prettyUrl = null, $region = null, $yearLevel = null)
    {
        // SB-342 added by jbernardez 20190918
        // Added htmlspecialchars() to make sure that no query injection should enter
        $common = Loader::helper('common', $this->pkgHandle);
        $prettyUrl = htmlspecialchars($prettyUrl);
        $region = $common->checkRegions($region);
        $yearLevel = $common->checkYearLevels($yearLevel);

        $SubjectHelper = Loader::helper('subject', $this->pkgHandle);
        $breadcrumbHelper = Loader::helper('breadcrumb', $this->pkgHandle);
        $series = CupContentSeries::fetchByPrettyUrl($prettyUrl);

        // ANZGO-3691 added by jbernardez 20180417
        // check if there if an isbn13 that was retrieved from fetchDetailByPrettyUrl() method
        $noSeries = false;
        if (!$series) {
            $noSeries = true;
            $this->set('noSeries', $noSeries);

            CupGoLogs::trackUser(static::PAGE_BROWSE, static::ACTION_VIEW_SERIES, static::SERIES_PAGE);

            header('Location: ' . GO_BASE_URL . '/subjects/');
            exit;
        } else {
            header('Location: ' . GO_BASE_URL . '/subjects/' . $series->subject_pretty_url . '/' . $series->prettyUrl);
            exit;

            $CupContentSearch = new CupContentSearch();
            $CupContentSearch->filterByTitleSeries($series->name, $region, $yearLevel);

            //If there is only one title in the series then it should go straight to the title page (ANZGO-1756)
            $this->countSeries($CupContentSearch->getResults());

            $searchList = $SubjectHelper->formatSearchList($CupContentSearch);

            // $subjectList = CupContentSubject::fetchPrimarySubjectList();
            // Modified by Paul Balila, 2016-04-14
            // For ticket ANZUAT-119
            // default: $subjectList = CupContentSubject::fetchPrimarySubjectList();
            $tempSubj = CupContentSubject::fetchPrimarySubjectList();
            $subjectList = array("None" => "Select Subject");

            foreach ($tempSubj as $sKey => $sVal) {
                $subjectList[$sKey] = $sVal;
            }

            $breadcrumb = $breadcrumbHelper->buildFromSeries($series, $prettyUrl);
            $this->set('search_list', $searchList);
            $this->set('subject_list', $subjectList); // This is needed to build the subject display.
            $this->set('title', $series->name);
            $this->set('region', $region);
            $this->set('year_level', $yearLevel);
            $this->set('pretty_url', $prettyUrl);
            $this->set('series_page', '/go/series');
            $this->set('subject_page', '/go/subject');
            $this->set('curent_page', '/go/subject');
            $this->set('current_series', str_replace(" ", "-", $series->name));
            $this->set('breadcrumb', $breadcrumb);
            $this->set('current_subject', $breadcrumbHelper->getSubjPrettyUrl());

            // ANZGO-3013
            CupGoLogs::trackUser(static::PAGE_BROWSE, static::ACTION_VIEW_SERIES, $series->name);
        }
    }

    private function countSeries($series)
    {
        if (count($series) == 1) {
            $this->redirect('/go/titles/', $series[0]['prettyURL']);
        }

        return null;
    }
}
