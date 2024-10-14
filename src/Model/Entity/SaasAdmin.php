<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SaasAdmin Entity
 *
 * @property int $id
 * @property string $username
 * @property string $passwd
 * @property string $name
 * @property bool $active
 * @property bool $is_km_edit
 * @property bool $is_km_del
 * @property bool $is_km_eff
 * @property \Cake\I18n\FrozenTime|null $last_visit
 * @property string|null $last_visit_from
 * @property string|null $dashboard_setting
 * @property string|null $sys_auth
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property bool $is_mfa
 * @property string|null $mfa_key
 *
 * @property \App\Model\Entity\MfaBackupCode[] $mfa_backup_codes
 * @property \App\Model\Entity\SaasAdminAuthGroup[] $saas_admin_auth_groups
 * @property \App\Model\Entity\SaasLoginRecord[] $saas_login_records
 * @property \App\Model\Entity\SyncRecord[] $sync_records
 */
class SaasAdmin extends Entity
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
        'username' => true,
        'passwd' => true,
        'name' => true,
        'active' => true,
        'is_km_edit' => true,
        'is_km_del' => true,
        'is_km_eff' => true,
        'last_visit' => true,
        'last_visit_from' => true,
        'dashboard_setting' => true,
        'sys_auth' => true,
        'created' => true,
        'modified' => true,
        'is_mfa' => true,
        'mfa_key' => true,
        'mfa_backup_codes' => true,
        'saas_admin_auth_groups' => true,
        'saas_login_records' => true,
        'sync_records' => true,
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
