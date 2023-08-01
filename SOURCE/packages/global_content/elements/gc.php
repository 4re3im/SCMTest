<?php

/**
 * Description of global_content_helper
 *
 * @author paulbalila
 */
class GlobalContentHelper {
    
    public function formatContentDisplay($content) {
        $html = '<form class="form-stacked inline-form-fix">';
        $html .= '<div class="clearfix">';
        $html .= '<label>Global Content</label>';
        $html .= '</div>';
        $html .= '</form>';
        return $html;
    }
    
}
