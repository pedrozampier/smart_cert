<?php

namespace Tests\Acceptance\Events;

use App\Models\Event;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class EventSearchAjaxCest extends BaseAcceptanceCest
{
    // Teste 1: Busca de eventos via Ajax retorna resultados corretos
    public function buscaDeEventosViaAjaxRetornaResultados(AcceptanceTester $page): void
    {
        $criador = $this->criarCriador();

        $page->login($criador->email, '123456');

        $event1 = $this->criarEvento($criador->id, 'Workshop de PHP', 'workshop');
        $event2 = $this->criarEvento($criador->id, 'Curso de Laravel', 'curso');
        $event3 = $this->criarEvento($criador->id, 'Palestra sobre Python', 'palestra');

        $page->amOnPage('/events');
        $page->wait(1);

        $page->seeElement('#event-search-input');

        $page->fillField('#event-search-input', 'PHP');
        $page->wait(2);

        $page->see('Workshop de PHP');
        $page->dontSee('Curso de Laravel');
        $page->dontSee('Palestra sobre Python');

        $page->fillField('#event-search-input', 'Curso');
        $page->wait(2);

        $page->see('Curso de Laravel');
        $page->dontSee('Workshop de PHP');
        $page->dontSee('Palestra sobre Python');
    }

    // Teste 2: Busca de eventos via Ajax sem resultados
    public function buscaDeEventosViaAjaxSemResultados(AcceptanceTester $page): void
    {
        $criador = $this->criarCriador();
        $page->login($criador->email, '123456');

        $this->criarEvento($criador->id, 'Workshop de PHP', 'workshop');

        $page->amOnPage('/events');
        $page->wait(1);

        $page->see('Workshop de PHP');

        $page->fillField('#event-search-input', 'JavaScript');
        $page->wait(1);

        $page->see('Nenhum evento encontrado');
        $page->dontSee('Workshop de PHP');

        $page->fillField('#event-search-input', ' ');
        $page->wait(1);

        $page->see('Workshop de PHP');
    }

    private function criarCriador(): User
    {
        $criador = new User([
            'name' => 'Criador de Eventos',
            'email' => 'criador@smartcert.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $criador->is_admin = true;
        $criador->save();

        return $criador;
    }

    private function criarEvento(int $criadorId, string $nome, string $tipo): Event
    {
        $event = new Event([
            'name' => $nome,
            'description' => "Descrição do evento {$nome}",
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+1 day')),
            'event_location' => 'São Paulo',
            'workload_hours' => 8,
            'event_type' => $tipo,
            'creator_id' => $criadorId,
            'owner_id' => $criadorId,
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $saved = $event->save();

        return $event;
    }
}
