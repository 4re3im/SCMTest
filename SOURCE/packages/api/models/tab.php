<?php

/**
 * ANZGO-3649 , Added by John Renzo S. Sunico, 03/06/2018
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'base_model.php';

class Tab extends BaseModel
{
    public $_table = 'CupGoTabs';

    public function findByPublicTabText($name)
    {
        return $this->Load('Public_TabText LIKE ?', [$name]);
    }
}
