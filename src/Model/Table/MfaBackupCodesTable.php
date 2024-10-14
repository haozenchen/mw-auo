<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MfaBackupCodes Model
 *
 * @property \App\Model\Table\SaasAdminsTable&\Cake\ORM\Association\BelongsTo $SaasAdmins
 *
 * @method \App\Model\Entity\MfaBackupCode newEmptyEntity()
 * @method \App\Model\Entity\MfaBackupCode newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\MfaBackupCode[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MfaBackupCode get($primaryKey, $options = [])
 * @method \App\Model\Entity\MfaBackupCode findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\MfaBackupCode patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\MfaBackupCode[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MfaBackupCode|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MfaBackupCode saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\MfaBackupCode[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MfaBackupCode[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\MfaBackupCode[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\MfaBackupCode[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class MfaBackupCodesTable extends Table
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

        $this->setTable('mfa_backup_codes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SaasAdmins', [
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
            ->integer('saas_admin_id')
            ->allowEmptyString('saas_admin_id');

        $validator
            ->scalar('passwd')
            ->maxLength('passwd', 50)
            ->allowEmptyString('passwd');

        $validator
            ->allowEmptyString('used');

        $validator
            ->integer('creator')
            ->allowEmptyString('creator');

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
}
