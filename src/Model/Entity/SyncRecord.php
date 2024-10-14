<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SyncRecord Entity
 *
 * @property int $id
 * @property int|null $saas_admin_id
 * @property string|null $ip_address_ip
 * @property string|null $status
 * @property int $user_total
 * @property int|null $user_update
 * @property int|null $user_threshold
 * @property int $department_total
 * @property int $department_update
 * @property int $department_threshold
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\SaasAdmin $saas_admin
 */
class SyncRecord extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'saas_admin_id' => true,
        'ip_address_ip' => true,
        'status' => true,
        'user_total' => true,
        'user_update' => true,
        'user_threshold' => true,
        'department_total' => true,
        'department_update' => true,
        'department_threshold' => true,
        'created' => true,
        'modified' => true,
        'saas_admin' => true,
    ];
}
