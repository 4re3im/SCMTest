<?php
/**
 *
 */
class SubscriptionHelper
{
  private $pModel;
  private $pkgHandle = 'go_provisioning_v2';
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
}
