<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case TEAM_LEADER = 'team_leader';
    case SURVEYOR = 'surveyor';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::TEAM_LEADER => 'Team Leader',
            self::SURVEYOR => 'Surveyor',
        };
    }
}