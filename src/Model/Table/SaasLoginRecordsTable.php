<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SaasLoginRecords Model
 *
 * @property \App\Model\Table\SaasAdminsTable&\Cake\ORM\Association\BelongsTo $SaasAdmins
 *
 * @method \App\Model\Entity\SaasLoginRecord newEmptyEntity()
 * @method \App\Model\Entity\SaasLoginRecord newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasLoginRecord get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasLoginRecord|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasLoginRecord[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SaasLoginRecordsTable extends Table
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

        $this->setTable('saas_login_records');
        $this->setDisplayField('success');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SaasAdmins', [
            'foreignKey' => 'saas_admin_id',
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
            ->scalar('ip')
            ->maxLength('ip', 120)
            ->allowEmptyString('ip');

        $validator
            ->scalar('success')
            ->notEmptyString('success');

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
