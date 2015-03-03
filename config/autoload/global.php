<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    /* View Helpers */
    'cdn'                   => array(
        'default'           => 'courseocean.b0.upaiyun.com',
    ),
    /* Services */
    'db'                    => array(
        'driver'            => 'Pdo',
        'dsn'               => 'mysql:dbname=courseocean;host=localhost',
        'driver_options'    => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'mail'                  => array(
        'connection_class'  => 'plain',
        'connection_config' => array(
            'ssl'       => 'tls'
        ),
    ),
    'service_manager'       => array(
        'factories'         => array(
            'Zend\Db\Adapter\Adapter'=> 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
