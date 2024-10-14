<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SaasAuthGroupPermissions Model
 *
 * @property \App\Model\Table\SaasAuthGroupsTable&\Cake\ORM\Association\BelongsTo $SaasAuthGroups
 *
 * @method \App\Model\Entity\SaasAuthGroupPermission newEmptyEntity()
 * @method \App\Model\Entity\SaasAuthGroupPermission newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroupPermission[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SaasAuthGroupPermissionsTable extends Table
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

        $this->setTable('saas_auth_group_permissions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->integer('saas_auth_group_id')
            ->notEmptyString('saas_auth_group_id');

        $validator
            ->scalar('action')
            ->maxLength('action', 225)
            ->allowEmptyString('action');

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
        $rules->add($rules->existsIn('saas_auth_group_id', 'SaasAuthGroups'), ['errorField' => 'saas_auth_group_id']);

        return $rules;
    }
}
