<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MfaBackupCode Entity
 *
 * @property int $id
 * @property int|null $saas_admin_id
 * @property string|null $passwd
 * @property int|null $used
 * @property int|null $creator
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\SaasAdmin $saas_admin
 */
class MfaBackupCode extends Entity
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
        'passwd' => true,
        'used' => true,
        'creator' => true,
        'created' => true,
        'modified' => true,
        'saas_admin' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected $_hidden = [
        'passwd',
    ];
}
