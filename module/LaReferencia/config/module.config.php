<?php
namespace LaReferencia\Module\Configuration;

$config = array(
    'controllers' => array(
        'invokables' => array(
            'clickstats' => 'LaReferencia\Controller\ClickStatsController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'clickstats' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/ClickStats',
                    'defaults' => array(
                        'controller' => 'ClickStats',
                        'action'     => 'Home',
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                   
                ),
            ),
        ),
    ),
);

return $config;