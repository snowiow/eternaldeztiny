<?php

namespace Application\Tests;

class HomeCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->am('guest user');
        $I->amGoingTo('Load the homepage');
        $I->amOnPage('/');
        $I->seeInCurrentUrl('/');
    }

    public function seeHomepage(\AcceptanceTester $I)
    {
        $I->wantTo('See the homepage');
        $I->lookForwardTo('See the homepage correctly');
        $I->see('Welcome to Eternal Deztiny');
        $I->see('About us');
        $I->see('Recruiting Criteria');
        $I->see('Clan Dynamics');
        $I->see('What are you waiting for? Apply!');
    }

    public function seeNavBar(\AcceptanceTester $I)
    {
        $I->wantTo('See the navbar links');
        $I->seeLink('News');
        $I->seeLink('Members');
        $I->seeLink('Warlog');
        $I->seeLink('Media');
        $I->seeLink('Live');
        $I->seeLink('Apply Now');
        $I->seeLink('Login');
        $I->seeLink('Register');
    }
}
