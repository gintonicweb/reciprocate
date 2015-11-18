<?php
namespace Reciprocate\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * Tooglable behavior
 */
class ReciprocateBehavior extends Behavior
{
    /**
     * Initialize class
     *
     * @param array $config The configuration settings provided to this behavior.
     * @return void
     */
    public function initialize(array $config)
    {
        $configs = $this->config();
        foreach ($configs as $name => $config) {
            $this->_table->hasMany($config['model'], [
                'foreignKey' => $config['foreignKey']
            ]);
        }
    }

    /**
     * Finder Method that returns the Reciprocator object for all reciprocated relations
     *
     * @param \Cake\ORM\Query $query Query
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    public function findReciprocated(Query $query, array $options)
    {
        $config = $this->config($options['name']);
        return $query
            ->where(['id IN' => $this->_getSent($config, $options)])
            ->andWhere(['id IN' => $this->_getRecieved($config, $options)]);
    }

    /**
     * Finder Method that returns the Reciprocator object for all sent relations
     *
     * @param \Cake\ORM\Query $query Query
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    public function findReciprocateSent(Query $query, array $options)
    {
        $config = $this->config($options['name']);
        return $query->where(['id IN' => $this->_getSent($config, $options)]);
    }

    /**
     * Finder Method that returns the Reciprocator object for all recieved relations
     *
     * @param \Cake\ORM\Query $query Query
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    public function findReciprocateRecieved(Query $query, array $options)
    {
        $config = $this->config($options['name']);
        return $query->where(['id IN' => $this->_getRecieved($config, $options)]);
    }

    /**
     * Query that returns the Reciprocator object for all recieved relations
     *
     * @param array $config The configuration settings provided to this behavior.
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    protected function _getRecieved($config, $options)
    {
        return $this->_table->find()
            ->matching($config['model'], function ($q) use ($config, $options) {
                return $q->where([$config['reciprocatorKey'] => $options['id']]);
            })
            ->distinct()
            ->select(['id' => $config['foreignKey']]);
    }

    /**
     * Query that returns the Reciprocator object for all sent relations
     *
     * @param array $config The configuration settings provided to this behavior.
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    protected function _getSent($config, $options)
    {
        return $this->_table->find()
            ->matching($config['model'], function ($q) use ($config, $options) {
                return $q->where([$config['foreignKey'] => $options['id']]);
            })
            ->distinct()
            ->select(['id' => $config['reciprocatorKey']]);
    }
}
