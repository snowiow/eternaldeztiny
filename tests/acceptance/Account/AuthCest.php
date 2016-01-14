<?php

namespace Account\Tests;

class AuthCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->am('guest user');
    }

    /*
     * @before register
     */
    public function visitRegisterPage(\Acceptancetester $I)
    {
        $I->amGoingTo('visit register page');
        $I->amOnPage('/');
        $I->seeLink('Register');
        $I->click('Register');
        $I->seeInCurrentUrl('/auth/register');
        $I->see('Username');
        $I->see('Password');
        $I->see('E-Mail Adress');
    }

    public function register(\AcceptanceTester $I)
    {
        $I->amGoingTo('register');
        $I->amOnPage('/auth/register');
        $I->fillField('name', 'codeception');
        $I->fillField('password', 'acceptance');
        $I->fillField('email', 'marcel.patzwahl+cept@gmail.com');
        $I->click('submit');
        $I->see('Registered successfully!');
        $I->seeLink('Go back to Home');
        $I->click('Go back to Home');
        $I->seeInCurrentUrl('/');
    }
}
