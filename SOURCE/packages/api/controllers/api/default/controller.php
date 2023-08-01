<?php

class APIDefaultController extends Controller
{
    public function on_start()
    {
        $view = View::getInstance();
        $view->setTheme(PageTheme::getByHandle('json_theme', 'api'));
    }

    public function forbidden()
    {
        $this->set('result', array(
            'success' => false,
            'message' => 'Permission denied.'
        ));
    }

    public function unavailable()
    {
        $this->set('result', array(
            'success' => false,
            'message' => 'This resource is not yet available.'
        ));
    }
}