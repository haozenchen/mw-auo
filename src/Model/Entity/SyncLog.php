<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SyncLog Entity
 *
 * @property int $id
 * @property int $sync_records_id
 * @property string $type
 * @property string $api_host
 * @property string|null $action
 * @property int|null $total_count
 * @property int|null $success_count
 * @property int|null $error_count
 * @property string|null $status
 * @property string|null $msg
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\SyncRecord $sync_record
 */
class SyncLog extends Entity
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
        'sync_records_id' => true,
        'type' => true,
        'api_host' => true,
        'action' => true,
        'total_count' => true,
        'success_count' => true,
        'error_count' => true,
        'status' => true,
        'msg' => true,
        'created' => true,
        'sync_record' => true,
    ];
}
