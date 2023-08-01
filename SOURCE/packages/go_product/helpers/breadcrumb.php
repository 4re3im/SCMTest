<?php

/**
 * Builds the breadcrumb display.
 *
 * @author gerardbalila
 */
class BreadcrumbHelper {

    private $subject = FALSE;
    private $subjectUrl = FALSE;
    private $subjPrettyUrl = FALSE;
    private $tempSubj = FALSE;
    private $tempSubjUrl = FALSE;
    private $tempPrettyUrl = FALSE;
    
    private $breadcrumb;

    private function getSubjectComponent($sourceType = 'series', $sourceId) {
        // Regardless of source, series or title, we have the following steps:
        // 1. Extract from DB the subjects from either series or title.
        // 2. Check if there is already a set subject pretty url from the session.
        // 3. If there is, use that and get details from the database.
        // 4. If there are none, get the first subject from the database and use that.
        // 5. Lastly, set the subject pretty url from the database.

        $results = FALSE;
        if ($sourceType == 'series') {
            $results = CupContentSeries::getSeriesSubjects($sourceId);
        } else {
            $results = CupContentTitle::getTitleSubjects($sourceId);
        }
        foreach ($results as $r) {
            if (isset($_SESSION['subject_pretty_url'])) {
                if ($r['prettyUrl'] == $_SESSION['subject_pretty_url']) {
                    $this->subject = $r['subject'];
                    $this->subjectUrl = '/go/subject/' . $r['prettyUrl'];
                    $this->subjPrettyUrl = $r['prettyUrl'];
                } else {
                    if (!$this->tempSubj) {
                        $this->tempSubj = $r['subject'];
                        $this->tempSubjUrl = '/go/subject/' . $r['prettyUrl'];
                        $this->tempPrettyUrl = $r['prettyUrl'];
                    }
                }
            } else {
                $this->subject = $r['subject'];
                $this->subjectUrl = '/go/subject/' . $r['prettyUrl'];
                $this->subjPrettyUrl = $r['prettyUrl'];
                $_SESSION['subject_pretty_url'] = $r['prettyUrl'];
                break;
            }
        }
        if (!$this->subject) {
            $this->subject = $this->tempSubj;
            $this->subjectUrl = $this->tempSubjUrl;
            $this->subjPrettyUrl = $this->tempPrettyUrl;
            $_SESSION['subject_pretty_url'] = $this->tempPrettyUrl;
        }
        $this->breadcrumb['subject'] = array('title' => $this->subject, 'url' => $this->subjectUrl);
    }
    
    private function getSeriesComponent($series) {
        if($prettyUrl) {
            $this->breadcrumb['series'] = array('title' => $series->name, 'url' => '/go/series/' . $prettyUrl);
        } else {
            $this->breadcrumb['series'] = array('title' => $series->name, 'url' => '/go/series/' . $series->prettyUrl);
        }
    }

    public function buildFromSeries($series) {
        $this->getSubjectComponent('series', $series->id);
        return $this->breadcrumb;
    }

    public function buildFromTitle($title) {
        // Check if title belongs to a series.
        $series = CupContentSeries::fetchByName($title->__get('series'));

        if ($series) {
            $this->getSubjectComponent('series', $series->id);
            $this->getSeriesComponent($series);
        } else {
            $this->getSubjectComponent('title', $title->id);
        }
        return $this->breadcrumb;
    }
    
    public function getSubjPrettyUrl() {
        return $this->subjPrettyUrl;
    }

}
