<?php

App::uses('APIComponent', 'Controller/Component');
App::uses('Model', 'Model');

class UserComponent extends APIComponent {    
    
    public $post_actions    = [];
    public $put_actions     = [];
    public $delete_actions  = [];
    
    public function login(){}
}