<?php

Loader::model('series/model', 'cup_content');

class DashboardCupContentSeriesAddController extends Controller
{
    private $pkgHandle = 'cup_content';

    public function view()
    {

    }

    public function on_start()
    {
        // GCAP-1272 Added by Shane Camus 04/08/2021
        $html = Loader::helper('html');

        $jsPath = (string)$html->javascript('series-script.js', $this->pkgHandle)->file . "?v=1";
        $this->addFooterItem('<script type="text/javascript" src="' . $jsPath . '"></script>');

        $this->set('disableThirdLevelNav', true);
    }

    public function submit()
    {
        $post = CupContentSeries::convertPost($this->post());

        Loader::model('collection_types');
        $val = Loader::helper('validation/form');
        $vat = Loader::helper('validation/token');

        $val->setData($this->post());
        //$val->addRequired("name", t("Name required."));
        $val->test();

        $error = $val->getError();

        if (!$vat->validate('create_series')) {
            $error->add($vat->getErrorMessage());
        }

        if ($error->has()) {
            $_SESSION['alerts'] = array('error' => $error->getList());
            $this->set('entry', $post);
        } else {
            $post = CupContentSeries::convertPost($this->post());

            Loader::helper('tools', 'cup_content');

            $series = new CupContentSeries();
            /*
            $series->id = $post['id'];
            $series->name = $post['name'];
            $series->prettyUrl = CupContentToolsHelper::string2prettyURL($author->name);
            $series->biography = $post['biography'];
            */
            $series->id = $post['id'];
            $series->seriesID = $post['seriesID'];
            $series->trialID = $post['trialID'];
            $series->name = $post['name'];
            $series->prettyUrl = CupContentToolsHelper::string2prettyURL($series->name);
            $series->shortDescription = $post['shortDescription'];
            $series->longDescription = $post['longDescription'];
            $series->yearLevels = $post['yearLevels'];
            $series->formats = $post['formats'];
            $series->subjects = $post['subjects'];
            $series->divisions = $post['divisions'];
            $series->regions = $post['regions'];
            $series->compGoUrl = $post['compGoUrl'];
            $series->compHotUrl = $post['compHotUrl'];
            $series->compSiteUrl = $post['compSiteUrl'];
            $series->partnerSiteName = $post['partnerSiteName'];
            $series->partnerSiteUrl = $post['partnerSiteUrl'];
            $series->tagline = $post['tagline'];
            $series->reviews = $post['reviews'];

            $entry = $series->getAssoc();

            if ($series->save()) {
                $_SESSION['alerts'] = array('success' => 'New Series has been added successfully');

                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0 && strlen($_FILES['image']['tmp_name']) > 0) {
                    $filename = $_FILES['image']['tmp_name'];
                    $series->saveImage($filename);
                    $globalGoFilename = $series->saveGlobalGoImage($filename);
                    $series->saveThumbnailURL($globalGoFilename);
                }

                $this->redirect("/dashboard/cup_content/series");
                //$this->set('entry', $entry);
            } else {
                $this->set('entry', $entry);
                $_SESSION['alerts'] = array('error' => $series->errors);

                //$this->set('error', $author->errors);
            }

        }
    }


}