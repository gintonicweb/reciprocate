# Reciprocate plugin for CakePHP

Reciprocate Behavior lets you know if two entities have the inverse matching 
relation in the same join table.

## Installation

Using [composer](http://getcomposer.org).

```
composer require gintonicweb/reciprocate:dev-master
```

Load the plugin in ```bootstrap.php``` like:

```
Plugin::load('Reciprocate');
```

## Example

With a join table ```friends``` where the fields ```user_id``` and ```friend_id```
both point to the table ```users```, a single relation from a user to another
means a friend request has been sent. If the relation exists both ways, the
friend request has been accepted.


Add this behavior to your Users table.

```
$this->addBehavior('Reciprocate.Reciprocate', [
    'friends' => [
        'model' => 'Friends',
        'foreignKey' => 'user_id',
        'reciprocatorModel' => 'Users',
        'reciprocatorKey' => 'friend_id',
    ]
]);
```

We can then retrieve the list of friends for a user with the following finder

```
$this->Users->find('reciprocate', [
    'name' => 'friends', 
    'id' => $this->Auth->user('id')
]);
```
