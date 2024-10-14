<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SyncLogs Model
 *
 * @property \App\Model\Table\SyncRecordsTable&\Cake\ORM\Association\BelongsTo $SyncRecords
 *
 * @method \App\Model\Entity\SyncLog newEmptyEntity()
 * @method \App\Model\Entity\SyncLog newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SyncLog[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SyncLog get($primaryKey, $options = [])
 * @method \App\Model\Entity\SyncLog findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SyncLog patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SyncLog[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SyncLog|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SyncLog saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SyncLog[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncLog[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncLog[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncLog[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SyncLogsTable extends Table
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

        $this->setTable('sync_logs');
        $this->setDisplayField('type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SyncRecords', [
            'foreignKey' => 'sync_records_id',
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
            ->integer('sync_records_id')
            ->notEmptyString('sync_records_id');

        $validator
            ->scalar('type')
            ->notEmptyString('type');

        $validator
            ->scalar('api_host')
            ->notEmptyString('api_host');

        $validator
            ->scalar('action')
            ->allowEmptyString('action');

        $validator
            ->integer('total_count')
            ->allowEmptyString('total_count');

        $validator
            ->integer('success_count')
            ->allowEmptyString('success_count');

        $validator
            ->integer('error_count')
            ->allowEmptyString('error_count');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->scalar('msg')
            ->maxLength('msg', 4294967295)
            ->allowEmptyString('msg');

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
        $rules->add($rules->existsIn('sync_records_id', 'SyncRecords'), ['errorField' => 'sync_records_id']);

        return $rules;
    }
}
