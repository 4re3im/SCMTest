<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class RedirectController extends Controller {
    
    public function on_start() {

        // check if there are old query string url
        // if new redirect link, it will go to view method
        $url = $this->request();
        
        if ($url['id'] !== null) {
            $result = Redirector::redirectUrlByID($url['id']);
            $this->redirectToUrl($result['url']);
        }
        
    }
    
    public function view($data) {

        $occurence = stripos($data, '=');

        if ($occurence !== false) {
            $urlArray = explode("=", $data);
            $data = $urlArray[1];
        }
        
        $result = Redirector::redirectUrlByID($data);
        $this->redirectToUrl($result['url']);

    }

    private function redirectToUrl($url) {

        // search for no http value on url
        $occurence = stripos($url, "http");

        // if no occurence of http, concatenate http
        if ($occurence === false) {
            $url = "http://".$url;
        }

        if ($url !== null) {
            Header( "Location: " . $url );
            die;
        } else {
            Header( "Location: /go" );
        }
    }
    
}