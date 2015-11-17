<?php
namespace Reciprocate\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Reciprocate\Model\Behavior\ReciprocateBehavior;

/**
 * Multiselect\Model\Behavior\MultiselectBehavior Test Case
 */
class ReciprocateBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.Reciprocate.Users',
        'plugin.Reciprocate.Friendships',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Users = TableRegistry::get('Reciprocate.Users');
        $this->Users->addBehavior('Reciprocate.Reciprocate', [
            'friends' => [
                'model' => 'Friendships',
                'foreignKey' => 'user_id',
                'reciprocatorModel' => 'Users',
                'reciprocatorKey' => 'friend_id',
            ]
        ]);
        $this->Behavior = $this->Users->behaviors()->ReciprocateMultiselect;
    }

    public function tearDown()
    {
        $this->Articles->removeBehavior('Multiselect');
        parent::tearDown();
    }

    public function testFrienships()
    {
        $friendshipsTable = TableRegistry::get('Friendships');
        $friendship = $friendshipsTable->newEntity(['user_id' => 1, 'friend_id' => 2]);
        $friendshipsTable->save($friendship);
        $friendship = $friendshipsTable->newEntity(['user_id' => 2, 'friend_id' => 1]);
        $friendshipsTable->save($friendship);

        $result = $this->Users
            ->find()
            ->find('reciprocate', ['name' => 'friends'])
            ->toarray();

        $this->markTestIncomplete('Not implemented yet.');
    }
}
