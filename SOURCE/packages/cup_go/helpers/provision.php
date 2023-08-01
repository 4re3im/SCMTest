<?php  defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Data manipulation
 */

class ProvisionHelper {
    
    /**
     * Format Provsions items to its html format
     * @param type $provisions
     */
    public function formatProvision($provisions, $user_ids = array()){
        
        $provision_lists = ''; $summary = '';

        $html = $provisions;
        
        if(!$provisions['warning']){

            foreach($provisions as $provision){
                //sanitize parameters
                
                $access_code_class = '';
                
                $state = $provision['State'];
                
                $post_code = $provision['Postcode'];

                $user_id = $provision['UserID'];
                
                $product = $provision['ProductName'];

                $us_product = $provision['USProductName'];

                $login = $provision['EmailAddress'];

                $last_name = stripslashes($provision['LastName']);

                $first_name = stripslashes($provision['FirstName']);

                $user_exists = $provision['UserExists'];

                $access_code = $provision['ProAccessCode'];

                $access_code_usable = $provision['AccessCodeUsable'];

                $user_class = $user_exists == 'Y' ? 'account-exists' : 'account-created';
                
                //$access_code_class = $access_code_usable == 'Y' ? '' : 'access-code-used';

                $user_note = $user_exists == 'Y' ? 'account already exists' : 'account created';

                $us_product_names = (strpos($us_product,'|') !== false) ? explode('|', $us_product) : $us_product;

                $access_code_note = "$product added from code $access_code";

                $checkbox_state = in_array($user_id, $user_ids) ? 'checked="checked"' : '';
                
                //access code class, defaulted to ''
                if(empty($product)){
                    $access_code_class = 'access-code-dont-exist';
                    $access_code_note = "$access_code does not exist";
                    
                }elseif($access_code_usable=='N'){
                    $access_code_class = 'access-code-used';
                    $access_code_note = "$access_code for $product has already been used";
                }
                
                //format html output
                $provision_lists .= "<tr>
                    <td><input type='checkbox' name='check_$user_id' id='$user_id' class='provision_checkbox' $checkbox_state /></td>
                    <td class='$user_class'>$login</td>
                    <td class='$user_class'>$first_name</td>
                    <td class='$user_class'>$last_name</td>
                    <td width='300px'>
                        <table>";

                             //check if array
                            $provision_lists .= "<tr>
                                <td style='border-left: none;' width='100px' class='$access_code_class'>
                                    <div class='td_accesscode'>$access_code</div>
                                </td>
                                <td width='200px'>$product</td>
                            </tr>";    

                            if($us_product!=''){

                                if(!(is_array($us_product_names))){

                                    $provision_lists .= "<tr>
                                        <td style='border-left: none;' width='100px'>
                                            <div class='td_accesscode'></div>
                                        </td>
                                        <td width='200px'>$us_product_names</td>
                                    </tr>";  

                                }else{

                                    foreach($us_product_names as $key => $product){

                                        $provision_lists .= "<tr>
                                            <td style='border-left: none;' width='100px'>
                                                <div class='td_accesscode'></div>
                                            </td>
                                            <td width='200px'>$product</td>
                                        </tr>";  

                                    }
                                    //echo $provision_lists; exit;
                                }

                            }

                    $provision_lists .= "</table>
                        </td>
                        <td>$state</td>
                        <td>$post_code</td>
                    </tr>";

                $summary .= "<p>$first_name $last_name ($login) $user_note, $access_code_note</p>";

            }

            $html = array('provision_lists'=> $provision_lists, 'summary'=>$summary);
            
        }
        
        return $html;

    }
    
    /**
     * For product list
     * @param type $products
     */
    public function formatSearchProduct($product_name, $products){
        
        $html = '<ul>';
        
        if($products){
            
            foreach($products as $product){

                $s_id = $product['s_id'];
                
                $sa_id = $product['sa_id'];
                
                $search_id = $product['search_id'];
                
                $search_result = $product['search_result'];
                
                
                $html .= '<li id="'.$search_id.'">'.$search_result.'</li>';

            }
            
        }else{
            
            $html .= '<li>No results found for '.$product_name.'</li>';
            
        }
        
        $html .= '</ul>';
        
        return $html;
        
    }
    
    public function formatProvisionWithDupes($subscriptions) {
        $html = array();
        $alert_string = 'Duplicates found for: ';
        $html = $this->formatProvision($subscriptions['provision'],$subscriptions['user_ids']);
        foreach ($subscriptions['dupes'] as $d) {
            foreach ($subscriptions['provision'] as $p) {
                if($p['UserID'] == $d) {
                    $alert_string .= stripslashes($p['FirstName']) . " " . stripslashes($p['LastName']) . ", "; 
                }
            }
        }
        $html['duplicates'] = $alert_string;
        return $html;
    }
    
}