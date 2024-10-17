<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SyncRecords Model
 *
 * @property \App\Model\Table\SaasAdminsTable&\Cake\ORM\Association\BelongsTo $SaasAdmins
 *
 * @method \App\Model\Entity\SyncRecord newEmptyEntity()
 * @method \App\Model\Entity\SyncRecord newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SyncRecord[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SyncRecord get($primaryKey, $options = [])
 * @method \App\Model\Entity\SyncRecord findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SyncRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SyncRecord[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SyncRecord|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SyncRecord saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SyncRecord[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncRecord[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncRecord[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SyncRecord[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SyncRecordsTable extends Table
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

        $this->setTable('sync_records');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SaasAdmins', [
            'foreignKey' => 'saas_admin_id',
        ]);

        $this->hasMany('SyncLogs', [
            'foreignKey' => 'sync_record_id', // 根據你的資料表結構調整
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
            ->allowEmptyString('saas_admin_id');

        $validator
            ->scalar('ip_address_ip')
            ->allowEmptyString('ip_address_ip');

        $validator
            ->scalar('status')
            ->allowEmptyString('status');

        $validator
            ->nonNegativeInteger('user_total')
            ->notEmptyString('user_total');

        $validator
            ->nonNegativeInteger('user_update')
            ->allowEmptyString('user_update');

        $validator
            ->nonNegativeInteger('user_threshold')
            ->allowEmptyString('user_threshold');

        $validator
            ->nonNegativeInteger('department_total')
            ->notEmptyString('department_total');

        $validator
            ->nonNegativeInteger('department_update')
            ->notEmptyString('department_update');

        $validator
            ->nonNegativeInteger('department_threshold')
            ->notEmptyString('department_threshold');

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

        return $rules;
    }


    public function getClientIP() {
        if (env('HTTP_X_FORWARDED_FOR') != null) {
            $ipaddr = preg_replace('/(?:,.*)/', '', env('HTTP_X_FORWARDED_FOR'));
        } else {
            if (env('HTTP_CLIENT_IP') != null) {
                $ipaddr = env('HTTP_CLIENT_IP');
            } else {
                $ipaddr = env('REMOTE_ADDR');
            }
        }

        if (env('HTTP_CLIENTADDRESS') != null) {
            $tmpipaddr = env('HTTP_CLIENTADDRESS');

            if (!empty($tmpipaddr)) {
                $ipaddr = preg_replace('/(?:,.*)/', '', $tmpipaddr);
            }
        }


        return trim($ipaddr);
    }
}
