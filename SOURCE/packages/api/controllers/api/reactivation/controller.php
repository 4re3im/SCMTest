<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../base_controller.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../restApi_interface.php";

class APIReactivationController extends BaseController implements RestAPI
{
    const UNAVAILABLE_URI = '/api/default/unavailable';

    public function __construct()
    {
        Loader::model('access_code', 'api');
        Loader::model('subscription', 'api');

        header('Content-Type: application/json');

        $request = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if (strpos($request, 'checkAccessCode') !== false) {
            $this->authenticate = false;
        }

        parent::__construct();
    }

    public function create($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function read($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function update($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function delete($id = null)
    {
        $this->redirect(static::UNAVAILABLE_URI);
    }

    public function reactivationAccessCode()
    {
        $promoCode = strtoupper($this->post('promoCode'));
        $reactivationCode = strtoupper($this->post('reactivationCode'));
        $accessCode = new AccessCode();
        $result = $accessCode->processReactivationCode($promoCode, $reactivationCode);

        if ($result) {
            $message = 'Success.';
        } else {
            $message = 'There was an error on processing the codes.';
        }

        echo json_encode(array('message' => $message));
    }

    // ANZGO-3758 added by jbernardez 20180628
    public function getSubscriptions()
    {
        $subscriptions = new Subscription();
        $result = $subscriptions->getSubscriptions();

        echo json_encode($result);
    }

    // ANZGO-3758 added by jbernardez 20180628
    public function saveSubscriptionEdumarTitleId()
    {
        $edumarTitleId = $this->post('edumarTitleId');
        $sid = $this->post('sid');
        $subscriptions = new Subscription();

        // ANZGO-3841 added by jbernardez 20180829
        $sid = json_decode($sid);
        $message = array();
        foreach ($sid as $id) {
            $result = $subscriptions->saveEdumarTitleIdToProduct($edumarTitleId, $id);

            if ($result) {
                $message[] = array('message' => 'Success');
            } else {
                $message[] = $result;
            }
        }

        echo json_encode($message);
    }
}
