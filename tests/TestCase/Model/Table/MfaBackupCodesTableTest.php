<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MfaBackupCodesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MfaBackupCodesTable Test Case
 */
class MfaBackupCodesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MfaBackupCodesTable
     */
    protected $MfaBackupCodes;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.MfaBackupCodes',
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
        $config = $this->getTableLocator()->exists('MfaBackupCodes') ? [] : ['className' => MfaBackupCodesTable::class];
        $this->MfaBackupCodes = $this->getTableLocator()->get('MfaBackupCodes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MfaBackupCodes);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\MfaBackupCodesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\MfaBackupCodesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
