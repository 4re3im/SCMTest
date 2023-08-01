-- Revert ProvisioningUsers
RENAME TABLE `tngdb.ProvisioningUsers` TO `tngdb.ProvisioningUsers_bak_GCAP_479`;
RENAME TABLE `tngdb.ProvisioningUsers_GCAP479` TO `tngdb.ProvisioningUsers`;