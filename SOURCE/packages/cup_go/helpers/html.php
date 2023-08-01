<?php 
 
defined('C5_EXECUTE') or die("Access Denied.");
 
class HtmlHelper extends Concrete5_Helper_Html {
    public function css($file, $pkgHandle = null, $uniqueItemHandle = array(),$cbEnable = false) {
        //Get the css object output by the parent
        $css = parent::css($file, $pkgHandle, $uniqueItemHandle);
 
        //Add new cachebusting param
        if ($cbEnable) {
            $css->file = $this->bustCache($css->file);
        }
 
        return $css;
    }
 
    public function javascript($file, $pkgHandle = null, $uniqueItemHandle = array(),$cbEnable = false) {
        //Get the js object output by the parent
        $js = parent::javascript($file, $pkgHandle, $uniqueItemHandle);
 
        //Add the new cachebusting param
        if ($cbEnable) {
            $js->file = $this->bustCache($js->file);
        }
 
        return $js;
    }
 
    public function bustCache($file) {
        //Get the clean filename to run filemtime on
        //badFilePath is the part of the path to remove which c5 adds when linking a file in your theme
        $badFilePath = '/index.php/tools/css/';
        $fileParts = explode('?',$file);
        if(strpos($fileParts[0], $badFilePath) !== false) {
            $cleanFile = substr($fileParts[0],strlen($badFilePath));
        } else {
            $cleanFile = ltrim($fileParts[0],'/');
        }
 
        //Create new param based on filemtime
        if (is_file($cleanFile)) {
            $f = md5(filemtime($cleanFile));
        } else {
            Log::AddEntry($cleanFile.' is not a file','custom_html_helper_error');
        }
 
        //Get the old cache busting var
        $vars = explode('?',$file);
        $varArray = explode('&',$vars[1]);
 
 
        foreach($varArray as $key=>$var) {
            if(substr($var,0,2) == 'v=') {
                //swap out with the new var
                $var = 'v='.$f;
            }
 
            $varArray[$key] = $var;
        }
 
        //Build and return the new cachebusted file
        return $vars[0] . '?' . implode('',$varArray);
    }
 
}
class HeaderOutputObject extends Concrete5_Helper_Html_HeaderOutputObject {}
class JavaScriptOutputObject extends Concrete5_Helper_Html_JavascriptOutputObject {}
class InlineScriptOutputObject extends Concrete5_Helper_Html_InlinescriptOutputObject {}
class CSSOutputObject extends Concrete5_Helper_Html_CSSOutputObject {}
 