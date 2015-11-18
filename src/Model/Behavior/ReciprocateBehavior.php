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
     * Find all reciprocated relations
     *
     * @param \Cake\ORM\Query $query Query
     * @param array  $options the 'id' key must define the targetted user
     * @return \Cake\ORM\Query
     */
    public function findReciprocate(Query $query, array $options)
    {
        $config = $this->config($options['name']);

        $sent = $this->_table->find()
            ->matching($config['model'], function ($q) use ($config, $options) {
                return $q->where([$config['foreignKey'] => $options['id']]);
            })
            ->distinct()
            ->select(['id' => $config['reciprocatorKey']]);

        $recieved = $this->_table->find()
            ->matching($config['model'], function ($q) use ($config, $options) {
                return $q->where([$config['reciprocatorKey'] => $options['id']]);
            })
            ->distinct()
            ->select(['id' => $config['foreignKey']]);

        return $query->where([
            'id IN' => $sent,
            'id IN' => $recieved
        ]);
    }
}
