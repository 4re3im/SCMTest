<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of notification_table_nav
 *
 * @author paulbalila
 */
class NotificationTableNavHelper {
    private $limit;
    private $count;
    private $interval;
    
    private $page;

    private $widget;
    
    public function __construct() {
        $this->widget = '<ul>';
    }


    private function build_widget($count,$limit,$page) {
        $this->count = $count;
        $this->limit = $limit;
        $this->interval = ceil($this->count / $this->limit);
        $this->page = $page;
        
        // Build previous button
        if($page > 1) {
            $prev_btn = '<li class = "prev"><a class = "ltgray" href = "#">« Previous</a></li>';
        } elseif ($page == 1) {
            $prev_btn = '<li class = "prev disabled"><a class = "ltgray" href = "#">« Previous</a></li>';
        } else {
            $prev_btn = '<li class = "prev disabled"><a class = "ltgray" href = "#">« Previous</a></li>';
        }
        $this->widget .= $prev_btn;
        
        // Build pagination
        $page_btn = '';
        if(!$this->page) {
            $this->page = 1;
        }
        for ($index = 1; $index <= $this->interval; $index++) {
            // Set current pagination
            if($index == $this->page) {
                $page_btn .= '<li class = "currentPage active numbers disabled"><a href = "#">' . $index . '</a></li>';
            } else {
                $page_btn .= '<li class = "numbers"><a href = "">' . $index . '</a></li>';
            }
        }
        $this->widget .= $page_btn;
        
        // Build next button
        // Check if we are in the last page...
        if($this->page == $this->interval) { //... if we are, disable button.
            $next_btn = '<li class="next disabled"><a class="ltgray" href="#">Next »</a></li>';
        } else {
            $next_btn .= '<li class = "next"><a class = "" href = "#">Next »</a></li>';
        }
        $this->widget .= $next_btn;
        
        // Close widget tag
        $this->widget .= '</ul>';
        return $this->widget;
    }

    public function render($count,$limit = FALSE,$page = FALSE) {
        return $this->build_widget($count,$limit,$page);
    }
    
    public function refresh_table($notif) {
        $html = "";
        $v = new View();
        
        if($notif) {
            foreach ($notif as $a) {
                $nStatus = ($a['nStatus']) ? 'Active' : 'Hidden';
                $linkedTitles = ($a['linkedTitles'] == "0") ? 'General' : 'Linked to Titles';
                $html .= "<tr>";
                $html .= "<td><input type='checkbox' value=" .  $a['nID'] . " class='tick-notif' name='tick-notifs[]'/></td>";
                $html .= '<td><a href="' . $v->url('/dashboard/notification/edit/' . $a['nID']) . '">' . date('M d, Y', strtotime($a['nDate'])) . '</a></td>';
                $html .= '<td>' . $a['nTitle'] . '</td>';
                $html .= '<td>' . date('M d, Y H:i',  strtotime($a['dateCreated'])) . '</td>';
                $html .= '<td>' . $nStatus . '</td>';
                $html .= '<td>' . $linkedTitles . '</td>';
                $html .= '<td>' . date('M d, Y H:i',  strtotime($a['dateModified'])) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= "<tr>";
            $html .= "<td colspan='7'>No notifications... :|</td>";
            $html .= "</tr>";
        }
        return $html;
    }
}
