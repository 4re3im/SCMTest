<?php  defined('C5_EXECUTE') or die("Access Denied.");

/**
 * HTML formatting of title/product page
 * @author Ariel Tabag <atabag@cambridge.org>
 * April 10, 2015
*/

class CommonHelper extends Object
{
    /**
     * Format product page
     * ANZGO-3468 Modified by Maryjes Tañada 02/26/2018
     * As per request, 'print and interactive textbook' and 'print and interactive textbook powered by HOTmaths'
     * is removed from display
     *
     * @param $name
     * @return str_replace($searchArray, $replaceArray, $name);
     */
    public static function formatProductDisplayName($name)
    {
        $searchArray =  array(
            ': print & digital',
            ': Print & Digital ',
            ': print and digital',
            ': Print and Digital ',
            '(print and digital package)',
            '(Print and Digital Package)',
            '(Print & Digital Package)',
            '(print & digital Package)',
            'print and digital package',
            'Print and Digital Package',
            'Print & Digital Package',
            '(print and digital pack)',
            '(Print and Digital pack)',
            '(Print & Digital Pack)',
            '(print & digital Pack)',
            'print and digital pack',
            'Print and Digital pack',
            'Print and Digital Pack',
            '(print and interactive textbook)',
            '(Print and interactive textbook)',
            '(print & interactive textbook)',
            '(print and interactive textbook powered by HOTmaths)',
            '(print and interactive textbook powered by HOTmat',
            '(print and interactive textbook powered by Cambridge HOTmaths)',
            'print and interactive textbook',
            'Print and interactive textbook',
            'print & interactive textbook',
            'print and interactive textbook powered by HOTmaths',
            'print and interactive textbook powered by HOTmat',
            'print and interactive textbook powered by Cambridge HOTmaths',
            'Electronic Workbooks',
            'Electronic Workbook',
            '(print and digital)',
            '(Print and Digital)',
            '(Print & Digital)',
            '(print & digital)',
            'print and digital',
            'Print and Digital',
            'Print & Digital',
            '(1 Activation)',
            'Workbooks',
            'Workbook',
            'Option 1:',
            'Option 1',
            'Option',
            '(digital)',
            '(Digital)',
            '(print)',
            '(Print)',
            '- print',
            '- Print',
            '- digital',
            '- Digital',
            'print',
            'Print',
            'digital',
            'Digital',
            'Pack',
            'pack'

        );

        $replaceArray = array(
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', ''
        );

        return str_replace($searchArray, $replaceArray, $name);

    }

    // SB-342 added by jbernardez 20190918
    // As both regions and year levels are hard coded and we are expecting this
    // from the search, we search through this array and if the value is included
    // proceed with search, but if not, return null
    public function checkRegions($region) {
        $regions = array(
            '0'                     => 'Select Region',
            'Australia'             => 'Australia',
            'New South Wales'       => 'New South Wales',
            'New Zealand'           => 'New Zealand',
            'Northern Territory'    => 'Northern Territory',
            'Queensland'            => 'Queensland',
            'South Australia'       => 'South Australia',
            'Tasmania'              => 'Tasmania',
            'Victoria'              => 'Victoria',
            'Western Australia'     => 'Western Australia',
        );

        if (in_array($region, $regions)) {
            return $region;
        }

        return null;
    }

    // SB-342 added by jbernardez 20190918
    public function checkYearLevels($yearLevel) {
        $yearLevels = array(
            '0'     => 'Select Year',
            '7'     => 'Year 7',
            '8'     => 'Year 8',
            '9'     => 'Year 9',
            '10'    => 'Year 10',
            '11'    => 'Year 11',
            '12'    => 'Year 12'
        );

        if (in_array($yearLevel, $yearLevels)) {
            return $yearLevel;
        }

        return null;
    }
}
