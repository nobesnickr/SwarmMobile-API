<?php

return [
    'oauth'          => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdev',
        'password'   => 'dev2DaMax',
        'database'   => 'oauth',
        'prefix'     => '',
    ],
    'ee'             => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdev',
        'password'   => 'dev2DaMax',
        'database'   => 'ee_int',
        'prefix'     => '',
    ],
    'pos'            => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdev',
        'password'   => 'dev2DaMax',
        'database'   => 'pos_int',
        'prefix'     => '',
    ],
    'swarmdata'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdev',
        'password'   => 'dev2DaMax',
        'database'   => 'swarmdata_int',
        'prefix'     => '',
    ],
    'backstage'      => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-int.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarmdev',
        'password'   => 'dev2DaMax',
        'database'   => 'swarm_backstage',
        'prefix'     => '',
    ],
    'portal'         => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-device-data.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarm_admin',
        'password'   => '+uPaSeQeru5a',
        'database'   => 'portal',
        'prefix'     => '',
    ],
    'rollups'        => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'swarm-rollups.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => '',
        'password'   => '',
        'database'   => 'rollups',
        'prefix'     => '',
    ],
    'pingAsPresence' => [
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host'       => 'ping-as-presence.cdmer9ay9s4r.us-west-1.rds.amazonaws.com',
        'login'      => 'swarm_admin',
        'password'   => '3mUYJuA8',
        'database'   => 'ping_as_presence',
        'prefix'     => '',
    ],
];
