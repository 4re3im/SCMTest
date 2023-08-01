<?php

/**
 * Go Analytics EPub Controller
 * ANZGO-3506 Created by Shane Camus 11/21/2017
 */

/*
 * Listed below are functions that should be in this controller:
 * getEPubRichMediaUsagePerMonthYear
 * getEpubBookmarkAndAnnotationCountPerMonthYear
 * goEpubTesthubCheckAnswerClicks
 * ANZGO-3575, Modfied by John Renzo S. Sunico, 1/24/2018 (Clean up)
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsEPubController extends Controller
{
    protected $pkgHandle = 'go_analytics';

    const FIELD_DATA = 'data';
    const FIELD_RESULT = 'result';
    const FIELD_MONTH = 'month';
    const FIELD_YEAR = 'year';
    const COUNT = 'count';
    const EPUB_TITLE = 'epubTitle';
    const ASSET_TYPE = 'assetType';
    const DETAIL = 'detail';

    public function on_start()
    {
        parent::on_start();

        Loader::helper('analytics_authentication', $this->pkgHandle);
        Loader::helper('epub_activity', $this->pkgHandle);
        Loader::library('Epub/api');
        Loader::library('Testhub/api');
        Loader::model('user/user_model');

        $this->set('useJSON', true);

        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle($this->pkgHandle));

        AnalyticsAuthenticationHelper::postRequestOnly();
        AnalyticsAuthenticationHelper::authenticate();
    }

    public function view()
    {
        $this->set(static::FIELD_RESULT, json_encode(array('success' => true)));
    }

    /**
     * ANZGO-3509 Added by Shane Camus 10/16/17
     * Returns functionality count from epub per month and year
     */
    public function getFunctionalityCountPerMonthYear()
    {
        $form = $this->post();

        $titles = EPubActivityHelper::ePubDetails();

        $result = array();

        foreach ($titles as $title) {
            $data = EpubApi::getFunctionalityCountPerMonthYear(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR],
                $title['code'],
                $form['functionality']
            );

            if (!$data['success'] || $data['data'][static::COUNT] == 0) {
                continue;
            }

            $result[] = array(
                'id' => $title['id'],
                'isbn' => $title['isbn'],
                'name' => $title['name'],
                static::COUNT => $data['data'][static::COUNT]
            );
        }

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3536 Added by Shane Camus 11/8/17
     * Returns general asset click count from epub per month and year
     */
    public function getAssetClickCountPerMonthYear()
    {
        $form = $this->post();

        $data = EpubApi::getAssetLogCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR],
            $form[static::EPUB_TITLE],
            $form[static::ASSET_TYPE],
            $form[static::DETAIL]
        );

        $result = array(
            static::EPUB_TITLE => $form[static::EPUB_TITLE],
            static::FIELD_MONTH => $form[static::FIELD_MONTH],
            static::FIELD_YEAR => $form[static::FIELD_YEAR],
            static::ASSET_TYPE => $form[static::ASSET_TYPE],
            static::DETAIL => $form[static::DETAIL],
            static::COUNT => $data['data'][static::COUNT]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3511 Added by John Renzo Sunico 11/22/2017
     * Returns time spent in the book per user type [student, teacher]
     */
    public function getTimeSpentOnReaderPerMonthYear()
    {
        $form = $this->post();
        $result = json_encode(
            [
                UserModel::GROUP_STUDENTS => EpubApi::getTimeSpentInBookPerMonth(
                    $form[static::FIELD_MONTH],
                    $form[static::FIELD_YEAR],
                    UserModel::GROUP_STUDENTS
                ),
                UserModel::GROUP_TEACHERS => EpubApi::getTimeSpentInBookPerMonth(
                    $form[static::FIELD_MONTH],
                    $form[static::FIELD_YEAR],
                    UserModel::GROUP_TEACHERS
                )
            ]
        );

        $this->set(static::FIELD_RESULT, $result);
    }

    // ANZGO-3655 Added by Shane Camus 03/12/2018
    public function getTestHubCheckAnswerClicks()
    {
        $form = $this->post();
        $result = TesthubAPI::getCheckAnswersClick($form[static::FIELD_MONTH], $form[static::FIELD_YEAR]);
        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3655 Added by Shane Camus 03/12/2018
    public function getEPubTestHubAssetIDs()
    {
        $form = $this->post();
        $result = EpubApi::getTesthubAssetIDPerSeriesID($form['seriesID']);
        $this->set(static::FIELD_RESULT, json_encode($result));
    }
}
