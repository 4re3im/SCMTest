<?php
/**
 * Batch
 * @package HubEntitlement\Models
 *
 * @author jsunico@cambridge.org
 */

namespace HubEntitlement\Models;

use HubEntitlement\Helpers\Utils;

/**
 * Class Batch
 * @property int $id
 * @property int $entitlement_id
 * @property int $proof_pattern_id
 * @property int $admin_id
 * @property string $name
 * @property string $notes
 * @property int $total_codes
 * @property \DateTime $expired_at
 * @property \DateTime $created_at
 * @property \DateTime $generated_at
 * @property int $activations_count
 * @property int $limit
 * @property int $is_active
 * @property int $StaffID
 * @package HubEntitlement\Models
 */
class Batch extends Model
{
    const REQUEST_PATH = '/v2/batches/';

    public $fields = [
        'id',
        'entitlement_id',
        'proof_pattern_id',
        'admin_id',
        'name',
        'notes',
        'total_codes',
        'expired_at',
        'created_at',
        'generated_at',
        'activations_count',
        'limit',
        'is_active'
    ];

    public $foreignFields = [
        'entitlement',
        'proofPattern'
    ];

    public function entitlement()
    {
        return $this->hasOne('HubEntitlement\Models\Entitlement');
    }

    /**
     * Proxy for admin_id.
     */
    public function getStaffID()
    {
        return $this->admin_id;
    }

    /**
     * Proxy for admin_id.
     *
     * @param $value
     */
    public function setStaffID($value)
    {
        $this->admin_id = $value;
    }

    /**
     * Getter for expired_at property.
     *
     * @param $expiredAt
     * @return \DateTime
     */
    public function getExpiredAt($expiredAt)
    {
        if (is_string($expiredAt)) {
            return Utils::toDateTime($expiredAt);
        }

        return $expiredAt;
    }

    /**
     * Getter for created_at property.
     *
     * @param $createdAt
     * @return \DateTime
     */
    public function getCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            return Utils::toDateTime($createdAt);
        }

        return $createdAt;
    }

    public function getBatchName() {
        return $this->name;
    }

    public function getEOL() {
        return $this->expired_at;
    }

    public function getNotes() {
        return $this->attributes['notes'];
    }
}
