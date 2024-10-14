<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Event\EventInterface;
use Cake\Http\Session;
use ArrayObject;

/**
 * SaasAdmins Model
 *
 * @property \App\Model\Table\MfaBackupCodesTable&\Cake\ORM\Association\HasMany $MfaBackupCodes
 * @property \App\Model\Table\SaasAdminAuthGroupsTable&\Cake\ORM\Association\HasMany $SaasAdminAuthGroups
 * @property \App\Model\Table\SaasLoginRecordsTable&\Cake\ORM\Association\HasMany $SaasLoginRecords
 * @property \App\Model\Table\SyncRecordsTable&\Cake\ORM\Association\HasMany $SyncRecords
 *
 * @method \App\Model\Entity\SaasAdmin newEmptyEntity()
 * @method \App\Model\Entity\SaasAdmin newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdmin[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdmin get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasAdmin findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasAdmin patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdmin[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdmin|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAdmin saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAdmin[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdmin[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdmin[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdmin[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SaasAdminsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('saas_admins');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('MfaBackupCodes', [
            'foreignKey' => 'saas_admin_id',
        ]);
        $this->hasMany('SaasAdminAuthGroups', [
            'foreignKey' => 'saas_admin_id',
        ]);
        $this->hasMany('SaasLoginRecords', [
            'foreignKey' => 'saas_admin_id',
        ]);
        $this->hasMany('SyncRecords', [
            'foreignKey' => 'saas_admin_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('username')
            ->maxLength('username', 50)
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        $validator
            ->scalar('passwd')
            ->maxLength('passwd', 84)
            ->notEmptyString('passwd');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmptyString('active');

        $validator
            ->boolean('is_km_edit')
            ->notEmptyString('is_km_edit');

        $validator
            ->boolean('is_km_del')
            ->notEmptyString('is_km_del');

        $validator
            ->boolean('is_km_eff')
            ->notEmptyString('is_km_eff');

        $validator
            ->dateTime('last_visit')
            ->allowEmptyDateTime('last_visit');

        $validator
            ->scalar('last_visit_from')
            ->maxLength('last_visit_from', 50)
            ->allowEmptyString('last_visit_from');

        $validator
            ->scalar('dashboard_setting')
            ->allowEmptyString('dashboard_setting');

        $validator
            ->scalar('sys_auth')
            ->allowEmptyString('sys_auth');

        $validator
            ->boolean('is_mfa')
            ->notEmptyString('is_mfa');

        $validator
            ->scalar('mfa_key')
            ->maxLength('mfa_key', 30)
            ->allowEmptyString('mfa_key');

        return $validator;
    }

    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary)
    {
        $session = new Session();
        if($session->check('EmmaApp.UserInfo')){
            $userInfo = unserialize($session->read('EmmaApp.UserInfo'));
            if($userInfo['Uid'] != 1){
                $query->where(['id not IN(1)']);
            }
        }
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['username']), ['errorField' => 'username']);

        return $rules;
    }
}
