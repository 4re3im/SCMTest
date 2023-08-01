<?php

class NotificationHelper
{
    const GIGYA_ERRORS = [
        400022 => "Teacher code has already been used"
    ];

    public static $INFORMATION_GENERAL = 100000;

    public static $SUCCESS_GENERAL = 200000;

    public static $ERROR_GENERAL = 400000;
    public static $ERROR_MISSING_DATA = 400001;
    public static $ERROR_S3_BUCKET = 400002;
    public static $ERROR_DATAFLOW = 400003;
    public static $ERROR_SUBSCRIPTION_MISSING_INFORMATION = 400004;
    public static $ERROR_SUBSCRIPTION_NO_ACTION = 400005;
    public static $ERROR_SUBSCRIPTION_NOT_FOUND = 400006;

    const INFORMATION_MESSAGES = [
        100000 => 'Process running'
    ];

    const SUCCESS_MESSAGES = [
        200000 => 'Process done successfully.'
    ];

    const ERROR_MESSAGES = [
        400000 => 'An error occurred',
        400001 => 'Required data not found',
        400002 => 'There was an error exporting the file.',
        400003 => 'There was an error configuring the dataflow.',
        400004 => 'Missing information for Subscription change.',
        400005 => 'There was an error updating the subscriptions.',
        400006 => 'Sorry, Your query returned empty result, please try again.'
    ];

    public function setNotification($type, $message)
    {
        if (is_array($message)) {
            foreach ($message as $m) {
                $_SESSION['alerts'][$type][] = $m;
            }
        } else {
            $_SESSION['alerts'][$type] = $message;
        }
    }

    public function setGigyaNotification($type, $errors)
    {
        $messages = [];
        foreach ($errors as $error) {
            if (array_key_exists($error['errorCode'], static::GIGYA_ERRORS)) {
                $messages[] = static::GIGYA_ERRORS[$error['errorCode']];
            } else {
                $msg = $error['message'];

                if (isset($error['fieldName'])) {
                    $msg .= ' for field ' . $error['fieldName'];
                }
                $messages[] = $msg;
            }
        }

        $this->setNotification($type, $messages);
    }

    public function getNotification($type, $id, $parameters = [])
    {
        $notification = [
            'status' => $id,
            'parameters' => $parameters
        ];

        switch ($type) {
            case 'success':
                $notification['message'] = static::SUCCESS_MESSAGES[$id];
                $notification['success'] = true;
                break;
            case 'error':
                $notification['message'] = static::ERROR_MESSAGES[$id];
                $notification['success'] = false;
                break;
            default:
                $notification['message'] = static::INFORMATION_MESSAGES[$id];
                $notification['success'] = true;
                break;
        }
        return json_encode($notification);
    }
}
