<?php

/**
 * ANZGO-3649 Added by John Renzo S. Sunico, 03/06/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../base_controller.php";
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../restApi_interface.php";

class APIContentTitleController extends BaseController implements RestAPI
{
    private $title;

    public function on_start()
    {
        parent::on_start();
        Loader::model('content/title', $this->pkgHandle);
    }

    public function create($id = null)
    {
    }

    public function read($id = null)
    {
    }

    public function update($id = null)
    {
    }

    public function delete($id = null)
    {
    }

    public function isSample($bookName)
    {
        $resourceType = $this->post('resourceType');

        if (!$resourceType) {
            $this->set('result', [
                'success' => false,
                'message' => 'Resource type might not be specified.'
            ]);
            return false;
        }

        $this->title = new ContentTitle();
        $isSample = $this->title->isSample($bookName, strtoupper($resourceType));
        $this->set('result', [
            'success' => true,
            'isSample' => $isSample
        ]);
    }

    // SB-538 added by jbernardez 20200414
    public function previewButtonLink($isbn)
    {
        $this->title = new ContentTitle();
        $publicTabText = $this->title->previewLinkByISBN($isbn);

        if (!$publicTabText) {
            echo json_encode(
                array(
                    'success' => false
                )
            );
            die;
        }

        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        preg_match_all("/$regexp/siU", $publicTabText, $matches);

        $link = $matches[2][0];

        if (is_null($link)) {
            echo json_encode(
                array(
                    'success' => false
                )
            );
            die;
        } 

        echo json_encode(
            array(
                'success' => true,
                'link' => $link, 
                'ptt' => $publicTabText
            )
        );
        die;
    }
}