<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAdminPasswdTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAdminPasswdTable Test Case
 */
class SaasAdminPasswdTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAdminPasswdTable
     */
    protected $SaasAdminPasswd;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAdminPasswd',
        'app.SaasAdmins',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SaasAdminPasswd') ? [] : ['className' => SaasAdminPasswdTable::class];
        $this->SaasAdminPasswd = $this->getTableLocator()->get('SaasAdminPasswd', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAdminPasswd);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminPasswdTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminPasswdTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
