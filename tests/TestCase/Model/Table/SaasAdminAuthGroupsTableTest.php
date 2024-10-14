<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAdminAuthGroupsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAdminAuthGroupsTable Test Case
 */
class SaasAdminAuthGroupsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAdminAuthGroupsTable
     */
    protected $SaasAdminAuthGroups;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAdminAuthGroups',
        'app.SaasAdmins',
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
        $config = $this->getTableLocator()->exists('SaasAdminAuthGroups') ? [] : ['className' => SaasAdminAuthGroupsTable::class];
        $this->SaasAdminAuthGroups = $this->getTableLocator()->get('SaasAdminAuthGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAdminAuthGroups);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminAuthGroupsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminAuthGroupsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
