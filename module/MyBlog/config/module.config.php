<?php

namespace MyBlog;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;

return [

    'service_manager' => [
        'aliases' => [
            Model\PostRepositoryInterface::class => Model\LaminasDbSqlRepository::class,
            Model\PostCommandInterface::class => Model\LaminasDbSqlCommand::class,
        ],
        'factories' => [
            Model\PostRepository::class => InvokableFactory::class,
            Model\LaminasDbSqlRepository::class => Factory\LaminasDbSqlRepositoryFactory::class,
            Model\PostCommand::class => InvokableFactory::class,
            Model\LaminasDbSqlCommand::class => Factory\LaminasDbSqlCommandFactory::class,
            Model\CommentTable::class => function($container) {
                $tableGateway = $container->get(Model\CommentTableGateway::class);
                return new Model\CommentTable($tableGateway);
            },
            Model\CommentTableGateway::class => function ($container) {
                $dbAdapter = $container->get(AdapterInterface::class);
                $resultSetPrototype = new ResultSet();
                $resultSetPrototype->setArrayObjectPrototype(new Model\Comment());
                return new TableGateway('comments', $dbAdapter, null, $resultSetPrototype);
            },
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\ListController::class => Factory\ListControllerFactory::class,
            Controller\WriteController::class => Factory\WriteControllerFactory::class,
            Controller\DeleteController::class => Factory\DeleteControllerFactory::class,
            Controller\CommentController::class => function($container) {
                return new Controller\CommentController(
                    $container->get(Model\CommentTable::class)
                );
            },
        ],
    ],
    
    'router' => [
        'routes' => [
            'myblog' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/myblog',
                    'defaults' => [
                        'controller' => Controller\ListController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/:id',
                            'defaults' => [
                                'action' => 'detail',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action'     => 'add',
                            ],
                        ],
                    ],

                    'edit' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/edit/:id',
                            'defaults' => [
                                'controller' => Controller\WriteController::class,
                                'action'     => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],

                    'delete' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/delete/:id',
                            'defaults' => [
                                'controller' => Controller\DeleteController::class,
                                'action'     => 'delete',
                            ],
                            'constraints' => [
                                'id' => '[1-9]\d*',
                            ],
                        ],
                    ],

                    'comments' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/comments',
                            'defaults' => [
                                'controller' => Controller\CommentController::class,
                                'action'     => 'comment',
                            ],
                        ],
                    ],

                    'addcomment' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route' => '/addComment',
                            'defaults' => [
                                'controller' => Controller\CommentController::class,
                                'action'     => 'addComment',
                            ],
                        ],
                    ],
                ],
            ],
            
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];