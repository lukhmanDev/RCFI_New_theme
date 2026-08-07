<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('role.super_admin', function (User $user) {
    return $user->isSuperAdmin();
});

Broadcast::channel('role.coo', function (User $user) {
    return $user->isCoo();
});

Broadcast::channel('role.project_manager', function (User $user) {
    return $user->isPm();
});

Broadcast::channel('role.hod', function (User $user) {
    return $user->isHod();
});

Broadcast::channel('role.hod.{departmentId}', function (User $user, $departmentId) {
    return $user->isHod() && (int) ($user->department_id ?? 0) === (int) $departmentId;
});

Broadcast::channel('role.social_aid', function (User $user) {
    return $user->isSocialAid();
});

Broadcast::channel('role.engineer', function (User $user) {
    return $user->isEngineer();
});

Broadcast::channel('role.reception', function (User $user) {
    return $user->isReception();
});

Broadcast::channel('role.employee', function (User $user) {
    return $user->isEmployee();
});

Broadcast::channel('role.others', function (User $user) {
    return $user->isOthers();
});
