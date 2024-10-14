<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MfaBackupCodesFixture
 */
class MfaBackupCodesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'saas_admin_id' => 1,
                'passwd' => 'Lorem ipsum dolor sit amet',
                'used' => 1,
                'creator' => 1,
                'created' => '2024-10-01 07:44:37',
                'modified' => '2024-10-01 07:44:37',
            ],
        ];
        parent::init();
    }
}
