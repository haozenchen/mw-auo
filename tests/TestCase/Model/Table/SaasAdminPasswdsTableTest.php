<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SaasAdminPasswdsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SaasAdminPasswdsTable Test Case
 */
class SaasAdminPasswdsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SaasAdminPasswdsTable
     */
    protected $SaasAdminPasswds;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.SaasAdminPasswds',
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
        $config = $this->getTableLocator()->exists('SaasAdminPasswds') ? [] : ['className' => SaasAdminPasswdsTable::class];
        $this->SaasAdminPasswds = $this->getTableLocator()->get('SaasAdminPasswds', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SaasAdminPasswds);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminPasswdsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\SaasAdminPasswdsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
