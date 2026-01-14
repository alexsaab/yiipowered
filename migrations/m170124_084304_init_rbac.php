<?php

use app\components\UserPermissions;
use yii\db\Migration;

class m170124_084304_init_rbac extends Migration
{
    public function up()
    {
        /** @var \yii\rbac\ManagerInterface $auth */
        $auth = Yii::$app->authManager;

        $manageProjects = $auth->getPermission(UserPermissions::MANAGE_PROJECTS);
        if ($manageProjects === null) {
            $manageProjects = $auth->createPermission(UserPermissions::MANAGE_PROJECTS);
            $manageProjects->description = 'Manage projects';
            $auth->add($manageProjects);
        }

        $manageUsers = $auth->getPermission(UserPermissions::MANAGE_USERS);
        if ($manageUsers === null) {
            $manageUsers = $auth->createPermission(UserPermissions::MANAGE_USERS);
            $manageUsers->description = 'Manage users';
            $auth->add($manageUsers);
        }

        $moderator = $auth->getRole('moderator');
        if ($moderator === null) {
            $moderator = $auth->createRole('moderator');
            $moderator->description = 'Moderator';
            $auth->add($moderator);
        }

        if (!$auth->hasChild($moderator, $manageProjects)) {
            $auth->addChild($moderator, $manageProjects);
        }

        $admin = $auth->getRole('admin');
        if ($admin === null) {
            $admin = $auth->createRole('admin');
            $admin->description = 'Administrator';
            $auth->add($admin);
        }

        if (!$auth->hasChild($admin, $manageProjects)) {
            $auth->addChild($admin, $manageProjects);
        }
        if (!$auth->hasChild($admin, $manageUsers)) {
            $auth->addChild($admin, $manageUsers);
        }
    }

    public function down()
    {
        /** @var \yii\rbac\ManagerInterface $auth */
        $auth = Yii::$app->authManager;

        $manageProjects = $auth->getPermission(UserPermissions::MANAGE_PROJECTS);
        $manageUsers = $auth->getPermission(UserPermissions::MANAGE_USERS);
        $moderator = $auth->getRole('moderator');
        $admin = $auth->getRole('admin');

        if ($admin) {
            if ($manageProjects) {
                $auth->removeChild($admin, $manageProjects);
            }
            if ($manageUsers) {
                $auth->removeChild($admin, $manageUsers);
            }
            $auth->remove($admin);
        }

        if ($moderator) {
            if ($manageProjects) {
                $auth->removeChild($moderator, $manageProjects);
            }
            $auth->remove($moderator);
        }

        if ($manageUsers) {
            $auth->remove($manageUsers);
        }

        if ($manageProjects) {
            $auth->remove($manageProjects);
        }
    }
}