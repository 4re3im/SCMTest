<?php
/**
 * Trial Block Model
 * GCAP-1286 Added by mabrigos 20210428
 */
Loader::library('hub-sdk/autoload');
Loader::library('gigya/GigyaAccount');

use HubEntitlement\Models\Activation;
use HubEntitlement\Models\Permission;
use HubEntitlement\Models\Entitlement;

class TrialsModel
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function getActivationsByEntitlementId($entitlementIds, $filters, $products) 
    {
        $csvData = array();
        $durationFilter = isset($filters['daysRemaining']);
        $rangeFilter = isset($filters['start']) && isset($filters['end']);

        foreach ($entitlementIds as $entitlementId) {
            $permissions = Permission::where([
                            'entitlement_id' => $entitlementId,
                            'created_from' => isset($filters['start']) ? $filters['start'] : null,
                            'created_to'   => isset($filters['end']) ? $filters['end'] : null,
                            'is_paginated' => 0,
                        ]);
            foreach ($permissions as $permission) {
                $activations = Activation::where([
                                    'permission_id' => $permission->id,
                                    'is_paginated'  => 0,
                                ]);
                
                foreach ($activations as $activation) {
                    $start = $activation->activated_at->format(static::DATE_FORMAT);
                    $end = $activation->ended_at->format(static::DATE_FORMAT);
                    $daysRemaining = ceil((strtotime($end) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    $daysRemainingCheck = $durationFilter ? $filters['daysRemaining'] >= $daysRemaining : true;

                    if ($daysRemainingCheck) {
                        $tempData['name'] = $products[$entitlementId];
                        $tempData['activatedAt'] =  $start;
                        $tempData['userId'] = $activation->user_id;
                        $tempData['entId'] = $entitlementId;
                        $tempData['daysRemaining'] = $daysRemaining;
                    } else {
                        continue;
                    }
                    $csvData[] = $tempData;
                }
            }
        }

        return  array_filter($csvData);
    }

    public function mergeGigyaDetailsWithActivations($activations, $country = null)
    {
        $gAccount = new GigyaAccount();
        $users = $gAccount->fetchUsersWithCountryByUid(array_column($activations, 'userId'));
        $sortOrder = array(
                    'userId', 'email', 'firstName',
                    'lastName', 'institution', 'country',
                    'entId', 'name', 'activatedAt'
                );

        foreach ($activations as $activation) {
            foreach ($users as $user) {
                if ($activation['userId'] === $user['UID']) {
                    $activation['email'] = $user['email'];
                    $activation['firstName'] = $user['firstName'];
                    $activation['lastName'] = $user['lastName'];
                    $activation['institution'] = implode(', ', array_map(function($e) {
                        return $e->institute;
                    }, array_filter($user['institution'], function ($institution) {
                        return property_exists($institution, 'institute');
                    })));
                    $activation['country'] = $user['country'];
                }
            }
            $activation = array_merge(array_flip($sortOrder), $activation);
            $mergedArray[] = $activation;
        }

        if ($country) {
            $mergedArray = array_filter($mergedArray, function ($entry) use ($country) {
                return $entry['country'] == $country;
            });
        }

        return $mergedArray;
    }

}
