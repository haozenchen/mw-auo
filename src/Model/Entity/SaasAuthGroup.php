<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SaasAuthGroup Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $home
 * @property string|null $describe
 * @property string $allow_all_action
 * @property string $advance_permission
 *
 * @property \App\Model\Entity\SaasAdminAuthGroup[] $saas_admin_auth_groups
 * @property \App\Model\Entity\SaasAuthGroupPermission[] $saas_auth_group_permissions
 */
class SaasAuthGroup extends Entity
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
        'name' => true,
        'home' => true,
        'remark' => true,
        'allow_all_action' => true,
        'advance_permission' => true,
        'saas_admin_auth_groups' => true,
        'saas_auth_group_permissions' => true,
    ];
}
