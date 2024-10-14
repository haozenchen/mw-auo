<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAdminsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAdminsTable Test Case
 */
class SaasAdminsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAdminsTable
     */
    protected $SaasAdmins;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAdmins',
        'app.MfaBackupCodes',
        'app.SaasAdminAuthGroups',
        'app.SaasLoginRecords',
        'app.SyncRecords',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SaasAdmins') ? [] : ['className' => SaasAdminsTable::class];
        $this->SaasAdmins = $this->getTableLocator()->get('SaasAdmins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAdmins);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
