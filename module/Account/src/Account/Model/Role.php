<?php

namespace Account\Model;

class Role
{
    const NOT_ACTIVATED = 0;
    const USER          = 1;
    const MEMBER        = 1 << 1; //2
    const ELDER         = 1 << 2; //4
    const CO            = 1 << 3; //8
    const LEADER        = 1 << 4; //16
    const ADMIN         = 1 << 5; //32

    public static function convertToRole(int $int)
    {
        switch ($int) {
            case Role::USER:
                return 'User';
            case Role::MEMBER:
                return 'Member';
            case Role::ELDER:
                return 'Elder';
            case Role::CO:
                return 'Co-Leader';
            case Role::LEADER:
                return 'Leader';
            default:
                return 'Elder';
        }
    }

    public static function getAllRoles()
    {
        return [
            self::USER,
            self::MEMBER,
            self::ELDER,
            self::CO,
            self::LEADER,
        ];
    }

    public static function getRoleArray()
    {
        return [
            self::USER   => self::convertToRole(self::USER),
            self::MEMBER => self::convertToRole(self::MEMBER),
            self::ELDER  => self::convertToRole(self::ELDER),
            self::CO     => self::convertToRole(self::CO),
            self::LEADER => self::convertToRole(self::LEADER),
        ];
    }
}
