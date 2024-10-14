<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SaasLoginRecord Entity
 *
 * @property int $id
 * @property int $saas_admin_id
 * @property string|null $ip
 * @property string $success
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\SaasAdmin $saas_admin
 */
class SaasLoginRecord extends Entity
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
        'ip' => true,
        'success' => true,
        'created' => true,
        'saas_admin' => true,
    ];


}
