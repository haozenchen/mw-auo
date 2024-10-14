<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SaasAdminAuthGroupsFixture
 */
class SaasAdminAuthGroupsFixture extends TestFixture
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
                'saas_auth_group_id' => 1,
            ],
        ];
        parent::init();
    }
}
