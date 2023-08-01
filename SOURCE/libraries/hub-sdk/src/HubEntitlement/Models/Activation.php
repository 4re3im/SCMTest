<?php

/**
 * Activation Model
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Configuration\ConfigurationManager as Config;
use HubEntitlement\Authentication\JwtClient;
use HubEntitlement\Helpers\Utils;

/**
 * Class Activation
 * @property int $id
 * @property int $permission_id
 * @property int $user_id
 * @property \DateTime $ended_at
 * @property \DateTime $activated_at
 * @property array $metadata
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 * @property \HubEntitlement\Models\Permission $permission
 */
class Activation extends Model
{
    const REQUEST_PATH = '/v2/activations/';

    public $fields = [
        'id',
        'permission_id',
        'user_id',
        'institution_id',
        'ended_at',
        'metadata',
        'activated_at',
        'created_at',
        'updated_at'
    ];

    public $foreignFields = [
        'permission'
    ];

    public function permission()
    {
        return $this->hasOne('HubEntitlement\Models\Permission');
    }

    public function getCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            return Utils::toDateTime($createdAt);
        }

        return $createdAt;
    }

    public function getUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            return Utils::toDateTime($updatedAt);
        }

        return $updatedAt;
    }

    public function getEndedAt($endedAt)
    {
        if (is_string($endedAt)) {
            return Utils::toDateTime($endedAt);
        }

        return $endedAt;
    }

    public function getActivatedAt($activatedAt)
    {
        if (is_string($activatedAt)) {
            return Utils::toDateTime($activatedAt);
        }

        return $activatedAt;
    }

    public function computedIsActive()
    {
        return $this->ended_at > (new \DateTime());
    }

    public function computedDaysRemaining()
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $endedAt = $this->ended_at;
        $endedAt->setTime(0, 0, 0);
        $days = (int)$now->diff($endedAt)->format('%r%a');

        return $days > 0 ? $days : 0;
    }
}
