<?php
/**
 * Search and display product subjects
 *
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 25, 2015
 */
class GoSubjectController extends Controller
{
    protected $pkgHandle = 'go_product';
    // SB-346 added by jbernardez 20190919
    const PAGE_BROWSE = 'Browse';
    const ACTION_VIEW_SERIES = 'View Subjects';
    const SERIES_PAGE = 'Subject Page';

    public function on_start()
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        $html = Loader::helper('html');

        // ANZGO-3493 Removed by John Renzo Sunico, Aug 17, 2017
        // This is added globally on themes; Causing conflicts to subject page;
        // $this->addHeaderItem(Loader::helper('html')->javascript('product.js', $this->pkgHandle));
        $this->addHeaderItem(Loader::helper('html')->css('style.css', $this->pkgHandle));
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
        $subject = CupContentSubject::fetchByPrettyUrl($prettyUrl);
            
        // SB-346 added by jbernardez 20190919
        if (!$subject) {
            $this->set('noSubject', true);

            CupGoLogs::trackUser(static::PAGE_BROWSE, static::ACTION_VIEW_SERIES, static::SERIES_PAGE);
        } else {
            $CupContentSearch = new CupContentSearch();
            $CupContentSearch->filterBySubject($subject->name, $region, $yearLevel);
            $searchList = $SubjectHelper->formatSearchList($CupContentSearch);
            
            // Modified by Paul Balila, 2016-04-14
            // For ticket ANZUAT-119
            // default: $subjectList = CupContentSubject::fetchPrimarySubjectList();
            $tempSubj = CupContentSubject::fetchPrimarySubjectList();
            $subjectList = array("None" => "Select Subject");

            foreach ($tempSubj as $sKey => $sVal) {
                $subjectList[$sKey] = $sVal;
            }
            
            $this->set('search_list', $searchList);
            $this->set('subject_list', $subjectList);
            $this->set('title', $subject->name);
            $this->set('region', $region);
            $this->set('year_level', $yearLevel);
            $this->set('subject_pretty_url', $prettyUrl);
            $this->set('curent_page', '/go/subject');
            // Added by Paul Balila, 2016-04-13
            // For ticket ANZUAT-119
            $this->set('current_subject', $prettyUrl);
            
            $_SESSION['subject_pretty_url'] = $prettyUrl;
            $_SESSION['subject_name'] = array(
                                            'title' => $subject->name,
                                            'url' => '/go/subject/' . $prettyUrl
                                        );
            $_SESSION['series_name'] = null;
            $_SESSION['title_name'] = null;

            // ANZGO-3013
            CupGoLogs::trackUser("Browse","View Subject", $subject->name);
        }
    }
}
