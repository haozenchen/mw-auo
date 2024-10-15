<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SaasSettings Model
 *
 * @method \App\Model\Entity\SaasSetting newEmptyEntity()
 * @method \App\Model\Entity\SaasSetting newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasSetting[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasSetting get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasSetting findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasSetting patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasSetting[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasSetting|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasSetting saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasSetting[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasSetting[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasSetting[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasSetting[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SaasSettingsTable extends Table
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

        $this->setTable('saas_settings');
        $this->setDisplayField('key');
        $this->setPrimaryKey('id');
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
            ->scalar('key')
            ->maxLength('key', 100)
            ->requirePresence('key', 'create')
            ->notEmptyString('key');

        $validator
            ->scalar('value')
            ->allowEmptyString('value');

        $validator
            ->scalar('type')
            ->allowEmptyString('type');

        return $validator;
    }

    public function getSys($key) {
        return $this->_doGet($key, array('type' => 'S'));
    }

    public function _doGet($key, $params = null) {
        $cond = array('`key`' => $key);
        $condkey = '';
        $cache = true;
        if (isset($params['type'])) {
            $type = $params['type'];
        } else {
            $type = 'S';
        }

        $data = $this->find()
                ->where($cond)
                ->first();
        if (!empty($data)) {
            $return = $data['value'];

            return $return;
        } else {
            return null;
        }
    }
}
