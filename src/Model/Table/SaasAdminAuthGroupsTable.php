<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SaasAdminAuthGroups Model
 *
 * @property \App\Model\Table\SaasAdminsTable&\Cake\ORM\Association\BelongsTo $SaasAdmins
 * @property \App\Model\Table\SaasAuthGroupsTable&\Cake\ORM\Association\BelongsTo $SaasAuthGroups
 *
 * @method \App\Model\Entity\SaasAdminAuthGroup newEmptyEntity()
 * @method \App\Model\Entity\SaasAdminAuthGroup newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAdminAuthGroup[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SaasAdminAuthGroupsTable extends Table
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

        $this->setTable('saas_admin_auth_groups');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('SaasAdmins', [
            'foreignKey' => 'saas_admin_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SaasAuthGroups', [
            'foreignKey' => 'saas_auth_group_id',
            'joinType' => 'INNER',
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
            ->integer('saas_admin_id')
            ->notEmptyString('saas_admin_id');

        $validator
            ->integer('saas_auth_group_id')
            ->notEmptyString('saas_auth_group_id');

        return $validator;
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
        $rules->add($rules->existsIn('saas_admin_id', 'SaasAdmins'), ['errorField' => 'saas_admin_id']);
        $rules->add($rules->existsIn('saas_auth_group_id', 'SaasAuthGroups'), ['errorField' => 'saas_auth_group_id']);

        return $rules;
    }
}
