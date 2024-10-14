<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAuthGroupsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAuthGroupsTable Test Case
 */
class SaasAuthGroupsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAuthGroupsTable
     */
    protected $SaasAuthGroups;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAuthGroups',
        'app.SaasAdminAuthGroups',
        'app.SaasAuthGroupPermissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SaasAuthGroups') ? [] : ['className' => SaasAuthGroupsTable::class];
        $this->SaasAuthGroups = $this->getTableLocator()->get('SaasAuthGroups', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAuthGroups);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAuthGroupsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
