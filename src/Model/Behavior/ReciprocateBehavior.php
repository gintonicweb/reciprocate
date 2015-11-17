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

            $this->_table->hasMany($config['model'],[
                'foreignKey' => $config['foreignKey']
            ]);

            $joinTable = TableRegistry::get($config['model']);
            $joinTable->belongsTo($this->_table->alias());

            $reciprocatorAlias = ucfirst($name) . 'Reciprocator';
            $joinTable->belongsTo($reciprocatorAlias, [
                'className' => $config['reciprocatorModel'],
                'foreignKey' => $config['reciprocatorKey'],
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

        $table = TableRegistry::get($config['model'])->table();
        $alias = TableRegistry::get($config['model'])->alias();

        $reciprocatorAlias = ucfirst($options['name']) . 'Reciprocator';

        $cond1 = $alias . '.' . $config['foreignKey'] . ' = ' . 'r.' . $config['reciprocatorKey'];
        $cond2 = $alias . '.' . $config['reciprocatorKey'] . ' = r.' . $config['foreignKey'];

        return $query
            ->contain([
                $alias => function ($q) use ($table, $cond1, $cond2, $reciprocatorAlias) {
                    return $q
                        ->innerJoin(
                            ['r' => $table],
                            [$cond1, $cond2]
                        )
                        ->contain([$reciprocatorAlias]);
                },
            ]);
    }
}
