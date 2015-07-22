<?php

namespace Account\Model;

interface Role
{
    const NOT_ACTIVATED = 0;
    const USER          = 1;
    const MEMBER        = 1 << 1; //2
    const ELDER         = 1 << 2; //4
    const CO            = 1 << 3; //8
    const LEADER        = 1 << 4; //16
    const ADMIN         = 1 << 5; //32
}
