<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SyncRecordsFixture
 */
class SyncRecordsFixture extends TestFixture
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
                'ip_address_ip' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'status' => 'Lorem ipsum dolor sit amet',
                'user_total' => 1,
                'user_update' => 1,
                'user_threshold' => 1,
                'department_total' => 1,
                'department_update' => 1,
                'department_threshold' => 1,
                'created' => '2024-10-02 08:52:53',
                'modified' => '2024-10-02 08:52:53',
            ],
        ];
        parent::init();
    }
}
