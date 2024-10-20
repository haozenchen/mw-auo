<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SaasAdminPasswdFixture
 */
class SaasAdminPasswdFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'saas_admin_passwd';
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
                'created' => '2024-10-19 05:51:24',
            ],
        ];
        parent::init();
    }
}
