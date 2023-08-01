<?php

/**
 * ANZGO-3430 Created by Shane Camus 06/09/2017
 * ANZGO-3575, Modfied by John Renzo S. Sunico, 1/24/2018 (Clean up)
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsController extends Controller
{
    const FIELD_RESULT = 'result';
    const FIELD_SUCCESS = 'success';
    const FIELD_MONTH = 'month';
    const FIELD_YEAR = 'year';
    const FIELD_TITLES = 'titles';
    const FIELD_SERIES_ID = 'seriesID';
    const FIELD_COUNT = 'count';
    const FIELD_BATCH = 'batch';
    const FIELD_BATCHES = 'batches';

    protected $pkgHandle = 'go_analytics';

    public function on_start()
    {
        parent::on_start();

        $this->set('useJSON', true);

        Loader::library('Epub/api');
        Loader::model('analytics_users', $this->pkgHandle);
        Loader::model('analytics_usage', $this->pkgHandle);
        Loader::model('analytics_reactivation', $this->pkgHandle);
        Loader::model('analytics_activation', $this->pkgHandle);
        Loader::model('analytics_support', $this->pkgHandle);
        Loader::model('analytics_subscription', $this->pkgHandle);
        Loader::model('analytics_session_info', $this->pkgHandle);
        Loader::model('analytics_download_info', $this->pkgHandle); // ANZGO-3529
        Loader::model('analytics_title', $this->pkgHandle);
        Loader::model('analytics_epub', $this->pkgHandle);
        Loader::model('title/cup_content_title');
        Loader::model('analytics_frontend', $this->pkgHandle);
        Loader::model('analytics_provisioning', $this->pkgHandle); // ANZGO-3473
        Loader::model('analytics_bookseller_report', $this->pkgHandle); // ANZGO-3596
        Loader::helper('analytics_authentication', $this->pkgHandle);
        Loader::helper('epub_activity', $this->pkgHandle);

        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle($this->pkgHandle));

        AnalyticsAuthenticationHelper::postRequestOnly();
        AnalyticsAuthenticationHelper::authenticate();
    }

    public function view()
    {
        $this->set(static::FIELD_RESULT, json_encode(array(static::FIELD_SUCCESS => true)));
    }

    // ANZGO-3430 Added by Shane Camus 06/09/2017
    public function goUsers()
    {
        $result = array(
            'totalStudentCount' => AnalyticsUsers::getTotalStudentUsersCount(),
            'totalTeacherCount' => AnalyticsUsers::getTotalTeacherUsersCount(),
            'totalUserCount' => AnalyticsUsers::getTotalUsersCount(),
            'totalUserCountPerMonth' => AnalyticsUsers::getTotalUsersCountByMonth(),
            'studentCountPerMonth' => AnalyticsUsers::getStudentCountByMonth(),
            'teacherCountPerMonth' => AnalyticsUsers::getTeacherCountByMonth()
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goPeakTimeUserSessionPerMonth()
    {
        $form = $this->post();
        $result = array(
            'peakTime' => AnalyticsUsage::getPeakUsageTimeByMonthYear(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR],
                $form['timezone'] | 0
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goPeakDayUserSessionPerMonth()
    {
        $form = $this->post();
        $result = array(
            'peakDay' => AnalyticsUsage::getPeakUsageDayByMonthYear(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR],
                $form['timezone'] | 0
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goAverageSubscriptionsPerAccount()
    {
        $result = array(
            'averageSubscriptionsPerAccount' => AnalyticsActivation::getAverageSubscriptionsPerAccount(),
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goTotalSubsPerGOLife()
    {
        $result = array(
            'totalSubscriptionPerGOLife' => AnalyticsActivation::getTotalSubscriptionPerGOLife(),
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3432, added by James Bernardez, 2017-07-13
    public function goTotalNewSubscriptionsISBNTitle($startYear, $startMonth)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $results = array();

        $subscriptions = AnalyticsActivation::getAllSubscriptions();

        foreach ($subscriptions as $subscription) {
            $endMonth = 12;
            $isbn = $subscription['ISBN_13'];
            $name = $subscription['CMS_Name'];

            for ($counterYear = $startYear; $counterYear <= $currentYear; $counterYear++) {
                if ($counterYear == $currentYear) {
                    $endMonth = $currentMonth;
                }

                $results[$isbn]['name'] = $name;

                for ($counterMonth = $startMonth; $counterMonth <= $endMonth; $counterMonth++) {
                    $index = $counterYear . str_pad($counterMonth, 2, "0", STR_PAD_LEFT);
                    $countPerMonth = AnalyticsActivation::getTotalNewSubscriptionsISBNTitle(
                        $counterYear,
                        $counterMonth,
                        $isbn
                    );
                    $results[$isbn][$index] = $countPerMonth;
                }
            }
        }

        $result = array(
            'totalNewSubscriptionsISBNTitle' => $results,
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goTotalNewSubscriptionsISBNTitlePerMonth($year, $month)
    {
        $results = array();
        $subscriptions = AnalyticsActivation::getAllSubscriptions();

        foreach ($subscriptions as $subscription) {
            $isbn = $subscription['ISBN_13'];
            $name = $subscription['CMS_Name'];
            $results[$isbn]['name'] = $name;

            $index = $year . str_pad($month, 2, '0', STR_PAD_LEFT);
            $countPerMonth = AnalyticsActivation::getTotalNewSubscriptionsISBNTitle($year, $month, $isbn);
            $results[$isbn][$index] = $countPerMonth;
        }

        $result = array(
            'totalNewSubscriptionsISBNTitlePerMonth' => $results,
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goMonthlyITBNewSubscription($startYear, $startMonth)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $results = array();
        $endMonth = 12;

        for ($counterYear = $startYear; $counterYear <= $currentYear; $counterYear++) {
            if ($counterYear == $currentYear) {
                $endMonth = $currentMonth;
            }

            for ($counterMonth = $startMonth; $counterMonth <= $endMonth; $counterMonth++) {
                $index = $counterYear . str_pad($counterMonth, 2, "0", STR_PAD_LEFT);
                $countPerMonth = AnalyticsActivation::getMonthlyITBNewSubscriptions($counterYear, $counterMonth);
                $results[$index] = $countPerMonth;
            }
        }

        $result = array(
            'monthlyITBNewSubscription' => $results,
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goMonthlyTotalNewSubscriptions($startYear, $startMonth)
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        $results = array();
        $endMonth = 12;

        for ($counterYear = $startYear; $counterYear <= $currentYear; $counterYear++) {
            if ($counterYear == $currentYear) {
                $endMonth = $currentMonth;
            }

            for ($counterMonth = $startMonth; $counterMonth <= $endMonth; $counterMonth++) {
                $index = $counterYear . str_pad($counterMonth, 2, "0", STR_PAD_LEFT);
                $countPerMonth = AnalyticsActivation::getMonthlyTotalNewSubscriptions($counterYear, $counterMonth);
                $results[$index] = $countPerMonth;
            }
        }

        $result = array(
            'monthlyTotalNewSubscriptions' => $results,
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3433 Added by Shane Camus 07/11/17
    public function goTotalReactivationCount()
    {
        $result = array(
            'totalReactivationCount' => AnalyticsReactivation::getTotalReactivationCount()
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goReactivationTitles()
    {
        $result = array(
            static::FIELD_TITLES => AnalyticsReactivation::getAllTitles()
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goReactivationCountPerMonth()
    {
        $form = $this->post();
        $result = array(
            'reactivationCountPerMonth' => AnalyticsReactivation::getReactivationCountByMonth(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3451 Added by Shane Camus 07/26/17
    public function goEnquiryCountPerMonth()
    {
        $form = $this->post();
        $result = array(
            'enquiryCountPerMonth' => AnalyticsSupport::getEnquiryCountByMonth(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goSupportPageVisitPerMonth()
    {
        $form = $this->post();
        $result = array(
            'supportPageVisitCountPerMonth' => AnalyticsSupport::getSupportPageVisitCountByMonth(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    public function goSupportTabClickCountPerMonth()
    {
        $form = $this->post();
        $result = array(
            'supportTabClickCountPerMonth' => AnalyticsSupport::getSupportTabClickCountByMonth(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3505 Added by John Renzo Sunico, October 5, 2017
     * Returns list of epub asset clicks per month
     */
    public function getEPubRichMediaUsagePerMonthYear()
    {
        $form = $this->post();
        $logs = EpubApi::getRichMediaLogCountPerMonthYear($form[static::FIELD_MONTH], $form[static::FIELD_YEAR]);

        $results = array();
        if (!isset($logs[static::FIELD_SUCCESS]) || !$logs[static::FIELD_SUCCESS]) {
            return $results;
        }

        foreach ($logs['data'] as $asset) {

            $titleID = AnalyticsTitle::getTitleIDByPrivateTabKeyword($asset['epubTitle']);

            if (!$titleID) {
                continue;
            }

            $titleInfo = AnalyticsTitle::getShortAssocDescription($titleID);
            $results[] = array_merge($asset, $titleInfo);
        }

        $this->set(static::FIELD_RESULT, json_encode($results));
    }

    /**
     * ANZGO-3481 Added by John Renzo S. Sunico, October 10, 2017
     *
     * Steps to retrieve all the data
     * 1. Pass month, year and user group to API.
     * 2. EPUB API handler returns list of dir, user_id.
     * 3. EPUB API to remove items not belonging to a group
     *
     * Did this rather than sending bulk of userIDs to EPUB
     * for direct filtering in SQL.
     */
    public function getEpubBookmarkAndAnnotationCountPerMonthYear()
    {
        $form = $this->post();
        $studentBookmarks = EpubApi::getEpubBookmarksOrAnnotationCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR],
            EpubApi::TABLE_BOOKMARKS,
            UserModel::GROUP_STUDENTS
        );
        $studentAnnotations = EpubApi::getEpubBookmarksOrAnnotationCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR],
            EpubApi::TABLE_ANNOTATOR,
            UserModel::GROUP_STUDENTS
        );
        $teacherBookmarks = EpubApi::getEpubBookmarksOrAnnotationCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR],
            EpubApi::TABLE_BOOKMARKS,
            UserModel::GROUP_TEACHERS
        );
        $teacherAnnotations = EpubApi::getEpubBookmarksOrAnnotationCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR],
            EpubApi::TABLE_ANNOTATOR,
            UserModel::GROUP_TEACHERS
        );
        $result = array(
            'student' => array('bookmarks' => $studentBookmarks, 'annotations' => $studentAnnotations),
            'teachers' => array('bookmarks' => $teacherBookmarks, 'annotations' => $teacherAnnotations),
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3487 Added by John Renzo S. Sunico - Aug 18, 2017
     * Return all titles from subscription. You could also post with
     * exclude ids to filter the items.
     * Update: Aug. 24, 2017 Change function names
     */
    public function getAllTitlesFromSubscriptions()
    {
        $form = $this->post();
        $result = array(
            static::FIELD_TITLES => AnalyticsSubscription::getTitlesFromSubscription($form['exclude_ids'])
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3487 Added by John Renzo S. Sunico - Aug 18, 2017
     * Return access codes created for subscription per month and year;
     * This would be used just in case.
     * Update: Aug. 24, 2017 Change function names
     */
    public function getSubscriptionAccessCodeCountPerMonth()
    {
        $form = $this->post();
        $result = AnalyticsSubscription::getSubscriptionAccessCodePerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3487 Added by John Renzo S. Sunico - Aug 18, 2017
     * Return activation from subscription per month.
     * Update: Aug. 24, 2017 Change function names
     */
    public function getSubscriptionActivationPerMonth()
    {
        $form = $this->post();
        $result = AnalyticsActivation::getSubscriptionActivationPerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3487 Added by John Renzo S. Sunico - Aug 18, 2017
     * Return expired subscription per month.
     * Update: Aug. 24, 2017 Change function names
     */
    public function getExpiredSubscriptionCountPerMonth()
    {
        $form = $this->post();
        $result = AnalyticsActivation::getExpiredSubscriptionPerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3487 Added by John Renzo S. Sunico - Aug 18, 2017
     * Return access code, activation, expired subscription count per month
     * Update: Aug. 24, 2017 Change function names
     */
    public function getCodesActivationExpiredSubscriptionCountPerMonth()
    {
        $form = $this->post();
        $codes = AnalyticsSubscription::getSubscriptionAccessCodePerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );
        $activations = AnalyticsActivation::getSubscriptionActivationPerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );
        $expired = AnalyticsActivation::getExpiredSubscriptionPerMonth(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );

        $result = array(
            "codes" => $codes,
            "activations" => $activations,
            "expired" => $expired
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488 added by James Bernardez 09/08/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function getEPubAndITBTitles()
    {
        $result = array(
            static::FIELD_TITLES => AnalyticsTitle::getEPubAndITBTitles()
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3536 Added by Shane Camus 11/09/17
    public function getEPubTitles()
    {
        $result = array(
            static::FIELD_TITLES => EPubActivityHelper::ePubDetails()
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3655 Added by Shane Camus 03/13/2018
    public function getEPubTitleByPrivateKeyword()
    {
        $form = $this->post();
        $titleID = AnalyticsTitle::getTitleIDByPrivateTabKeyword($form['code']);
        $this->set(static::FIELD_RESULT, json_encode(AnalyticsTitle::getShortAssocDescription($titleID)));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goUniqueActiveTeacher()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getUniqueActiveTeacher($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goUniqueActiveStudent()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getUniqueActiveStudent($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goRepeatActiveTeacher()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getRepeatActiveTeacher($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goRepeatActiveStudent()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getRepeatActiveStudent($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goIndividualSessionsTeacher()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getIndividualSessionsTeacher($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    // ANZGO-3488, added by James Bernardez, 08/04/17
    // ANZGO-3526 Modified by Shane Camus 10/05/17
    public function goIndividualSessionsStudent()
    {
        $form = $this->post();
        $batch = $form[static::FIELD_BATCH];
        $batches = $form[static::FIELD_BATCHES];

        $titles = AnalyticsActivation::getBatchTitles($batch, $batches);
        $result = AnalyticsSessionInfo::getIndividualSessionsStudent($form, $titles);

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3529 Added by Jeszy Tanada - Oct. 09, 2017
     * to return Downloaded resource and create new Download
     * sheet separated by MM/YYYY
     */
    public function getDownloadCountPerTitle()
    {
        $form = $this->post();
        $result = array(
            "downloadedPdfPerTitle" => AnalyticsDownloadInfo::countPdfPerTitle(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            ),
            "downloadedWordActivityPerTitle" => AnalyticsDownloadInfo::countWordActivityPerTitle(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            ),
            "weblinkClickPerTitle" => AnalyticsDownloadInfo::countWeblinkClickPerTitle(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            ),
            "downloadedTeacherPackagePerTitle" => AnalyticsDownloadInfo::countTeacherPackagePerTitle(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR]
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3472 Added by John Renzo S. Sunico, October 17, 2017
     * Returns student and teachers unique session count per Month and Year
     * Active users per month
     */
    public function getUniqueSessionPerMonthYearPerUserGroup()
    {
        $form = $this->post();
        $result = array(
            AnalyticsUsers::GROUP_STUDENTS => AnalyticsSessionInfo::getUniqueSessionCountPerMonthAndGroupName(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR],
                AnalyticsUsers::GROUP_STUDENTS
            ),
            AnalyticsUsers::GROUP_TEACHERS => AnalyticsSessionInfo::getUniqueSessionCountPerMonthAndGroupName(
                $form[static::FIELD_MONTH],
                $form[static::FIELD_YEAR],
                AnalyticsUsers::GROUP_TEACHERS
            )
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3452 Added by John Renzo Sunico, 11/08/2017
     *
     * Returns analytics of:
     * login button
     * login popup buttons
     * teacher signup link
     * student signup link
     * forgot password link
     */
    public function getGoButtonClickCountPerMonthYear()
    {
        $form = $this->post();
        $result = AnalyticsFrontend::getGoButtonClickCountPerMonthYear(
            $form[static::FIELD_MONTH],
            $form[static::FIELD_YEAR]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3511 Added by John Renzo Sunico 11/21/2017
     * Return titles that are related to ITB
     */
    public function getITBTitles()
    {
        $result = AnalyticsTitle::getITBTitles();
        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3480 Added by Jeszy Tanada - Nov. 20, 2017
     * to return ISBN, Title & Platform (GO/Edjin/Elevate)
     */
    public function getTitlePlatform()
    {
        $platform = '';
        $result = array();
        $titles = AnalyticsTitle::getEnabledTitles();
        foreach ($titles as $title) {
            $id = $title['id'];
            $isbn = $title['isbn13'];
            $titleName = $title['name'];
            $smallTitleName = strtolower($titleName);
            if (strpos($smallTitleName, 'elevate') !== false) {
                $platform = 'Elevate';
            } elseif (strpos($smallTitleName, 'hotmaths') !== false) {
                $platform = 'Edjin';
            } else {
                $tabs = AnalyticsTitle::getTabDetails($id);
                foreach ($tabs as $tab) {
                    $smallTabName = strtolower($tab['TabName']);
                    if ($tab['ElevateProduct'] === 'Y') {
                        $platform = 'Elevate';
                        break;
                    } elseif ($tab['HMProduct'] === 'Y' ||
                        strpos($smallTabName, 'hotmaths') !== false || $smallTabName === 'online resource') {
                        $platform = 'Edjin';
                        break;
                    } else {
                        $platform = 'GO';
                    }
                }
            }
            array_push($result, array(
                    'ISBN' => $isbn,
                    'TitleName' => $titleName,
                    'Platform' => $platform
                )
            );
        }

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3473 aaded by jbernardez 20171120
     * @param int $month
     * @param int $year
     */
    public function getProvisionCountPerMonthYear($month = 0, $year = 0)
    {
        $form = $this->post();

        if ($form) {
            $month = $form[static::FIELD_MONTH];
            $year = $form[static::FIELD_YEAR];
        }

        $dataStudent = AnalyticsProvisioning::getProvisionCountPerMonthYear(
            $month,
            $year,
            'student'
        );

        $dataTeacher = AnalyticsProvisioning::getProvisionCountPerMonthYear(
            $month,
            $year,
            'teacher'
        );

        $result = array(
            'month' => $month,
            'year' => $year,
            'studentCount' => $dataStudent[static::FIELD_COUNT],
            'teacherCount' => $dataTeacher[static::FIELD_COUNT]
        );

        $this->set(static::FIELD_RESULT, json_encode($result));
    }

    /**
     * ANZGO-3596 Added by Maryjes Tanada - Feb. 21, 2018
     * to return Booksellers (Student/Teacher) Provisioned/CMS subscriptions count
     * separated by MM/YYYY
     * @params month, year and (int) userGroup
     * @return month, year and user count (either student/teacher depends on params passed)
     */
    public function getBookSellersProvisionedCmsCount($userGroup = 0)
    {
        $form = $this->post();

        if ($form) {
            $month = $form[static::FIELD_MONTH];
            $year = $form[static::FIELD_YEAR];
            $userGroup = $form['userGroup'];
        }
        $userCount = AnalyticsBookSellerReport::countBookSellerProvisionedCmsSubs(
            $month,
            $year,
            $userGroup
        );

        $result = array(
            'month' => $month,
            'year' => $year,
            'userCount' => $userCount
        );
        $this->set(static::FIELD_RESULT, json_encode($result));
    }
}
