<?php

namespace Tests\Acceptance\home;

use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class HomeIndexCest extends BaseAcceptanceCest
{
    public function verPaginaInicial(AcceptanceTester $page): void
    {
        $page->amOnPage('/');
        $page->seeInTitle('SmartCert - Login');
        $page->see('Email');
        $page->see('Senha');
        $page->seeElement('//button[@type="submit" and contains(text(), "Enter")]');
    }
}
