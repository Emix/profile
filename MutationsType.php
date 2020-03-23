<?php

namespace app\schema\v2;


use app\components\Bx;
use app\schema\v2\args\AttachedFileArgumentType;
use app\schema\v2\args\CommentArgumentType;
use app\schema\v2\args\EventArgumentType;
use app\schema\v2\entities\CommentType;
use app\schema\v2\entities\EventResponseType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class MutationsType
 * @package app\schema\v2
 */
class MutationsType
{
    /**
     * @return ObjectType
     */
    public static function mutation() {
        return new ObjectType([
            'name' => 'Update',
            'fields' => [
                'updatePasswordProfile' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'oldPassword' => ['type' => Type::string()],
                        'newPassword' => ['type' => Type::string()],
                    ],
                    'resolve' => function ($root, $args) {
                        return Bx::changePassword($args['oldPassword'] , $args['newPassword']);
                    },
                ],

                'createEventRequest' => [
                    'type' => EventResponseType::instance(),
                    'args' => [
                        'event' => EventArgumentType::instance(),
                    ],
                    'resolve' => function ($root, $args) {
                        return Bx::createEvent($args['event']);
                    },
                ],

                'postComment' => [
                    'type' =>Type::listOf(CommentType::instance()),
                    'args' => [
                        'eventId' => ['type' => Type::int()],
                        'comment' => ['type' => CommentArgumentType::instance()],
                    ],
                    'resolve' => function($root, $args) {
                        return Bx::createComment($args['eventId'], $args['comment']);
                    }
                ],

                'editProfile' => [
                    'type' =>Type::boolean(),
                    'args' => [
                        'getPushNotification' => ['type' => Type::boolean()],
                        'userAppToken'        => ['type' => Type::string()],
                        'avatar'              => ['type' => AttachedFileArgumentType::instance()],
                    ],
                    'resolve' => function($root, $args) {
                        return Bx::editProfile($args);
                    }
                ],

                'resetNewPassword' => [
                    'type' =>Type::boolean(),
                    'args' => [
                        'login' => ['type' => Type::string()],
                        'email' => ['type' => Type::string()],
                    ],
                    'resolve' => function($root, $args) {
                        print_r($args['login']);
                        print_r($args['email']);
                        die;

                        return true;
                    }
                ],

                'updateVisitStatus' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'notificationId' => ['type' => Type::int()],
                        'isVisited'      => ['type' => Type::boolean()],
                    ],
                    'resolve' => function($root, $args) {
                        return Bx::updateVisitStatus($args);
                    }
                ],
            ],
        ]);
    }
}