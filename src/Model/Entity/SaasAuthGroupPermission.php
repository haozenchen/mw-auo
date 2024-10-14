<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SaasAuthGroupPermission Entity
 *
 * @property int $id
 * @property int $saas_auth_group_id
 * @property string|null $action
 *
 * @property \App\Model\Entity\SaasAuthGroup $saas_auth_group
 */
class SaasAuthGroupPermission extends Entity
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
        'saas_auth_group_id' => true,
        'action' => true,
        'saas_auth_group' => true,
    ];
}
