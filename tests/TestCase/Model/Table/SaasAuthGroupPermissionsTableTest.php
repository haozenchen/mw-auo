<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAuthGroupPermissionsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAuthGroupPermissionsTable Test Case
 */
class SaasAuthGroupPermissionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAuthGroupPermissionsTable
     */
    protected $SaasAuthGroupPermissions;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAuthGroupPermissions',
        'app.SaasAuthGroups',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SaasAuthGroupPermissions') ? [] : ['className' => SaasAuthGroupPermissionsTable::class];
        $this->SaasAuthGroupPermissions = $this->getTableLocator()->get('SaasAuthGroupPermissions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAuthGroupPermissions);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAuthGroupPermissionsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasAuthGroupPermissionsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
