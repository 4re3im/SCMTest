<?php  defined('C5_EXECUTE') or die("Access Denied.");

/**
 * HTML formatting
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 26, 2015
*/

class SubjectHelper {

    /**
     * Format subject items to its html format
     * @param type $subjects
     */
    public function formatSubjectList($subjects){

        $html = '';

        $v = View::getInstance();

        foreach($subjects as $_subject){
            $html .= '<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">';

            $html .= '<ul>';

            foreach($_subject as $subject){

                $url = $v->url('/go/subject/'.$subject['prettyUrl']);

                $html .= '<li><a href="'.$url.'">'.CommonHelper::formatProductDisplayName($subject['name']).'</a></li>';
            }

            $html .= '</ul>';

            $html .= '</div>';


        }
        return $html;

    }

    public function formatSearchList($CupContentSearch){

        $html = '';

        $education_url = DIR_REL . "/files/cup_content/images/";

        //$searches = array_chunk($CupContentSearch->getResults(),6);
        $searches = $CupContentSearch->getResults();

        $parent_html  = '<div class="row container-bg-1 row-list row-search"><div class="col-wrapper">';
        $parent_html .= '<div class="col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">';
        $parent_html .= '<ul class="row text-center row-ul">';

        $parent_html_end = '</ul></div></div></div>';

        // ANZGO-3013
        if(!$searches) {
            
            // Add user tracker if no search results
            CupGoLogs::trackUser('Search', 'Perform Search - no results', '');
            return $parent_html.'<div class="col-lg-12"><h1>Whoops, we couldn\'t find any resources. Try again or use the subjects below.</h1></div>'.$parent_html_end;
        }

        $v = View::getInstance();

        $html .= $parent_html;

        // Modified by Paul Balila, 2016-04-04
        // For ticket ANZUAT-83
        // usort($searches, function($v1, $v2) { return (strtolower($v1['name']) == strtolower($v2['name'])) ? strnatcmp($v1['name'], $v2['name']) : strnatcasecmp($v1['name'], $v2['name']); });
        //usort($searches, function($a, $b) { return strnatcasecmp($a['name'], $b['name']); });

        foreach($searches as $_search){
            $new_class = $_search['new_product_flag'] ? 'new' : '';

            //for high res images
            $class_name = $_search['class_name'] == 'CupContentTitle' ? 'titles/' : 'series/';

            if($_search['class_name'] == 'CupContentTitle'){
              // Modified by Paul Balila for ticket ANZGO-2674, 2016-08-25
                // $search_mg = $education_url.$class_name.$_search['isbn13'].'_CS.png';
                $search_mg = $education_url.$class_name.$_search['isbn13'];
            }else{
                //check stand alone titles
                $CupContentSearch = new CupContentSearch();
                $CupContentSearch->filterByTitleSeries($_search['name']);
                $titles = $CupContentSearch->getResults();

                if(count($titles)==1){
                    $search_mg = $education_url.'titles/'.$titles[0]['isbn13'];
                }else{
                    $search_mg = $education_url.$class_name.$_search['isbn13'].'.png';
                }
            }

            //$search_mg = $this->is_file_url_exists($search_mg) ? $search_mg : str_replace('_CS','',str_replace('.png', '_90.jpg', $search_mg));

            $url = $v->url('/go/'.$class_name . $_search['prettyURL']);

            $html .= '<li class="col-lg-2 col-md-3 col-sm-3 col-xs-12 result  '.$new_class.'">';

            $html .= '<a href="'.$url.'">';

            $html .= '<div class="book-wrap"><div class="cover">';

            $html .= '<div class="load">';

            $html .= '<img src="'.$search_mg. '?' . time() . '" class="search-img"/></div>';

            $html .= '</div>';

            $html .= '<div class="undercover" style="border-color: #5c5959;"></div>';

            if($new_class) $html .= '<div class="bookmark"><svg class="icon icon-new" viewBox="0 0 32 32"><use xlink:href="#icon-new"/></svg></div>';

            $html .= '</div></a>';

            $html .= '<p class="title">'.CommonHelper::formatProductDisplayName($_search['name']).'</p>';

            $html .= '</li>';

        }

        $html .= $parent_html_end;

        return $html;

    }

    public function colorPalette($imageFile, $numColors, $granularity = 5){
       $granularity = max(1, abs((int)$granularity));
       $colors = array();
       $size = @getimagesize($imageFile);
       if($size === false)
       {
          user_error("Unable to get image size data");
          return false;
       }

       $img = @imagecreatefromjpeg($imageFile);
       if(!$img)
       {
          user_error("Unable to open image file");
          return false;
       }
       for($x = 0; $x < $size[0]; $x += $granularity)
       {
          for($y = 0; $y < $size[1]; $y += $granularity)
          {
             $thisColor = imagecolorat($img, $x, $y);
             $rgb = imagecolorsforindex($img, $thisColor);
             $red = round(round(($rgb['red'] / 0x33)) * 0x33);
             $green = round(round(($rgb['green'] / 0x33)) * 0x33);
             $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
             $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
             if(array_key_exists($thisRGB, $colors))
             {
                $colors[$thisRGB]++;
             }
             else
             {
                $colors[$thisRGB] = 1;
             }
          }
       }
       arsort($colors);

       return array_slice(array_keys($colors), 0, $numColors);
    }

    private function is_file_url_exists($url) {
        if (@file_get_contents($url, 0, NULL, 0, 1)) {
            return 1;
        }

        return 0;
    }


}
