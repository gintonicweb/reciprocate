[![Build Status](https://travis-ci.org/gintonicweb/reciprocate.svg)](https://travis-ci.org/gintonicweb/multiselect)
[![Coverage Status](https://coveralls.io/repos/gintonicweb/reciprocate/badge.svg?branch=master&service=github)](https://coveralls.io/github/gintonicweb/multiselect?branch=master)
[![Packagist](https://img.shields.io/packagist/dt/gintonicweb/reciprocate.svg)]()
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

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
$friends = $this->Users->find('reciprocated', [
    'name' => 'friends', 
    'id' => $this->Auth->user('id')
]);
$friendRequestSent = $this->Users->find('reciprocateSent', [
    'name' => 'friends', 
    'id' => $this->Auth->user('id')
]);
$friendRequestRecieved = $this->Users->find('reciprocateRecieved', [
    'name' => 'friends', 
    'id' => $this->Auth->user('id')
]);
```
