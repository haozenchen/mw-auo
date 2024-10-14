<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SaasAuthGroupPermissionsFixture
 */
class SaasAuthGroupPermissionsFixture extends TestFixture
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
                'saas_auth_group_id' => 1,
                'action' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
