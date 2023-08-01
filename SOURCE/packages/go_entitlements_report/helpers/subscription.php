<?php
/**
 *
 */
class SubscriptionHelper
{
  private $pModel;
  private $pkgHandle = 'go_provisioning';
  function __construct()
  {
    Loader::model('provisioning', $this->pkgHandle);
    $this->pModel = new ProvisioningModel();
  }

  public function addUserSubscriptions($subsIds,$subsAvailIds,$users)
  {
    foreach ($users as $user) {
      // Check for user subscription.

      // Add user subscription.

    }
  }

    /**
     * SB-611 Added by mtanada 2020-07-20
     * Removal of GO prefix based from Gigya UID
     *
     * @param $userId string
     * @return bool|string
     */
    public function removeGoPrefix($userId)
    {
        $prefix = 'go';
        if (substr($userId, 0, strlen($prefix)) == $prefix) {
            return substr($userId, strlen($prefix));
        } else {
            return false;
        }
    }
}
