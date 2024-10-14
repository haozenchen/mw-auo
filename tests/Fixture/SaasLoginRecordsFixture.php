<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SaasLoginRecordsFixture
 */
class SaasLoginRecordsFixture extends TestFixture
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
                'ip' => 'Lorem ipsum dolor sit amet',
                'success' => 'Lorem ipsum dolor sit amet',
                'created' => '2024-10-02 05:42:23',
            ],
        ];
        parent::init();
    }
}
