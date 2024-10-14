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
 * SaasAuthGroups Model
 *
 * @property \App\Model\Table\SaasAdminAuthGroupsTable&\Cake\ORM\Association\HasMany $SaasAdminAuthGroups
 * @property \App\Model\Table\SaasAuthGroupPermissionsTable&\Cake\ORM\Association\HasMany $SaasAuthGroupPermissions
 *
 * @method \App\Model\Entity\SaasAuthGroup newEmptyEntity()
 * @method \App\Model\Entity\SaasAuthGroup newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SaasAuthGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\SaasAuthGroup[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class SaasAuthGroupsTable extends Table
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

        $this->setTable('saas_auth_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('SaasAdminAuthGroups', [
            'foreignKey' => 'saas_auth_group_id',
        ]);
        $this->hasMany('SaasAuthGroupPermissions', [
            'foreignKey' => 'saas_auth_group_id',
        ]);
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

    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $this->SaasAdminAuthGroups->deleteAll([
            'saas_admin_id' => $entity->id,
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
            ->scalar('name')
            ->maxLength('name', 225)
            ->allowEmptyString('name');

        $validator
            ->scalar('home')
            ->maxLength('home', 225)
            ->allowEmptyString('home');

        $validator
            ->scalar('remark')
            ->allowEmptyString('remark');

        $validator
            ->scalar('allow_all_action')
            ->notEmptyString('allow_all_action');

        $validator
            ->scalar('advance_permission')
            ->requirePresence('advance_permission', 'create')
            ->notEmptyString('advance_permission');

        return $validator;
    }
}
