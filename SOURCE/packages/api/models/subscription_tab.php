<?php

/**
 * ANZGO-3951 , Added by John Renzo S. Sunico, 1/12/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base_model.php';

class SubscriptionTab extends BaseModel
{
    public $_table = 'CupGoSubscriptionTabs';

    public function loadByID($id)
    {
        return $this->Load('ID = ?', [$id]);
    }
}
