<?php

/**
 * ANZGO-3575, Added by John Renzo S. Sunico, 1/24/2018
 * Optimizations
 */

defined('C5_EXECUTE') || die(_('Access Denied.'));

class AnalyticsDefaultsController extends Controller
{
    const MESSAGE = 'message';
    const STATUS = 'status';
    const RESULT = 'result';

    private $response;
    private $responseCode = 200;
    public $pkgHandle = 'go_analytics';

    public function on_start()
    {
        parent::on_start();

        $this->set('useJSON', true);

        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle($this->pkgHandle));
    }

    public function on_before_render()
    {
        http_response_code($this->responseCode);
        $this->set(static::RESULT, json_encode($this->response));
    }

    public function methodNotAllowed()
    {
        $this->responseCode = 405;
        $this->response = [
            static::MESSAGE => 'Method not allowed.',
            static::STATUS => 405
        ];
    }

    public function forbidden()
    {
        $this->responseCode = 403;
        $this->response = [
            static::MESSAGE => 'Permission denied',
            static::STATUS => 403
        ];
    }
}
