<?php
namespace ProspectOne\UserModule;

use ProspectOne\UserModule\Factory\BcryptFactory;
use ProspectOne\UserModule\Form\Factory\UserFormFactory;
use ProspectOne\UserModule\Form\UserForm;
use ProspectOne\UserModule\Service\Factory\CurrentUserFactory;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use \Zend\Authentication\AuthenticationService;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'logout',
                    ],
                ],
            ],
            'reset-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/reset-password',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'resetPassword',
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AuthController::class => Controller\Factory\AuthControllerFactory::class,
            Controller\UserController::class => Controller\Factory\UserControllerFactory::class,
            Controller\ConsoleController::class => Controller\Factory\ConsoleControllerFactory::class
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            Controller\UserController::class => [
                // Give access to "resetPassword", "message" and "setPassword" actions
                // to anyone.
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                // Give access to "index", "add", "edit", "view", "changePassword" actions to authorized users only.
                ['actions' => ['index', 'add', 'edit', 'view', 'changePassword'], 'allow' => ['admin']]
            ],
            Controller\ConsoleController::class => [
                ['actions' => ['regenerateTokens'], 'allow' => '*'],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            'ProspectOne\UserModule\Bcrypt' => BcryptFactory::class,
            'ProspectOne\UserModule\CurrentUser' => CurrentUserFactory::class,
            UserForm::class => UserFormFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'prospect-one/user-module/auth/login' => __DIR__ . '/../view/user/auth/login.phtml',
            'prospect-one/user-module/user/index' => __DIR__ . '/../view/user/user/index.phtml',
            'prospect-one/user-module/user/add' => __DIR__ . '/../view/user/user/add.phtml',
            'prospect-one/user-module/user/change-password' => __DIR__ . '/../view/user/user/change-password.phtml',
            'prospect-one/user-module/user/edit' => __DIR__ . '/../view/user/user/edit.phtml',
            'prospect-one/user-module/user/message' => __DIR__ . '/../view/user/user/message.phtml',
            'prospect-one/user-module/user/reset-password' => __DIR__ . '/../view/user/user/reset-password.phtml',
            'prospect-one/user-module/user/view' => __DIR__ . '/../view/user/user/index.view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'ProspectOne\UserModule' => [
        'bcrypt' => [
            'cost' => 14
        ],
        'auth' => [
            'header' => true,
            'header_name' => "xxx-user-module-auth",
        ]
    ],
    'console' => [
        'router' => [
            'routes' => [
                'regenerate-user-tokens' => [
                    'options' => [
                        'route' => 'user-module regenerate tokens',
                        'defaults' => [
                            'controller' => Controller\ConsoleController::class,
                            'action' => "regenerate-tokens",
                        ],
                    ],
                ],
            ],
        ],
    ],
];

