<?php

namespace Members\Tests;

class MembersCest
{
    public function seeMembers(\AcceptanceTester $I)
    {
        $I->am('guest user');
        $I->amGoingTo('Load the members section');
        $I->amOnPage('/');
        $I->click('Members');
        $I->seeInCurrentUrl('/members');
        $I->see('Name');
        $I->see('Role');
    }
}
