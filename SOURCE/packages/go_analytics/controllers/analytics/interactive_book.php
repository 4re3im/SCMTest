<?php

/**
 * ANZGO-3511 , Added by John Renzo S. Sunico, 11/22/2017
 * ITB related analytics should be included here.
 * ANZGO-3575, Modfied by John Renzo S. Sunico, 1/24/2018 (Clean up)
 */

class AnalyticsInteractiveBookController extends Controller
{
    const MONTH = 'month';
    const YEAR = 'year';
    const FIELD_RESULT = 'result';

    protected $pkgHandle = 'go_analytics';

    public function on_start()
    {
        parent::on_start();

        Loader::helper('analytics_authentication', $this->pkgHandle);
        Loader::library('InteractiveTextBook/api');

        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle($this->pkgHandle));

        $this->set('useJSON', true);

        AnalyticsAuthenticationHelper::postRequestOnly();
        AnalyticsAuthenticationHelper::authenticate();
    }

    /**
     * ANZGO-3511 Added by John Renzo Sunico 11/21/2017
     * Returns time spent for each Epub Title
     */
    public function getTimeSpentOnReaderPerMonthYear()
    {
        $form = $this->post();
        $result = json_encode(
            [
                UserModel::GROUP_STUDENTS => InteractiveTextBookAPI::getTimeSpentOnBookPerMonthYear(
                    $form[static::MONTH],
                    $form[static::YEAR],
                    UserModel::GROUP_STUDENTS
                ),
                UserModel::GROUP_TEACHERS => InteractiveTextBookAPI::getTimeSpentOnBookPerMonthYear(
                    $form[static::MONTH],
                    $form[static::YEAR],
                    UserModel::GROUP_TEACHERS
                )
            ]
        );

        $this->set(static::FIELD_RESULT, $result);
    }
}
