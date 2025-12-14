<?php

namespace Tests\Acceptance\EventParticipants;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Logo;
use App\Models\User;
use Tests\Acceptance\BaseAcceptanceCest;
use Tests\Support\AcceptanceTester;

class EventParticipantCest extends BaseAcceptanceCest
{
    // Teste 1: Adicionar participante a um evento
    public function adicionarParticipanteAoEvento(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678901');
        $participant = $this->criarUsuario('Participante', 'participante@example.com', '12345678902');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        $page->login($creator->email, '123456');
        $page->amOnPage("/events/{$event->id}/participants");
        $page->wait(1);

        // Verifica que a página de gerenciamento carregou
        $page->see('Gerenciar Participantes: Evento de Teste');
        $page->see('Adicionar Participante');

        // Verifica que o participante ainda não está na lista
        $page->see('Nenhum participante inscrito ainda');
    }

    // Teste 2: Remover participante de um evento
    public function removerParticipanteDoEvento(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678903');
        $participant = $this->criarUsuario('Participante', 'participante@example.com', '12345678904');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        // Adiciona participante diretamente no banco
        $eventParticipant = new EventParticipant();
        $eventParticipant->event_id = $event->id;
        $eventParticipant->participant_id = $participant->id;
        $eventParticipant->save();

        $page->login($creator->email, '123456');
        $page->amOnPage("/events/{$event->id}/participants");
        $page->wait(1);

        // Verifica que o participante está na lista
        $page->see('Participante');
        $page->see('participante@example.com');

        // Remove o participante
        $page->click('.bi-trash');
        $page->wait(1);

        // Verifica mensagem de sucesso e que o participante não está mais na lista
        $page->see('Participante removido com sucesso.');
        $page->dontSee('participante@example.com');
    }

    // Teste 3: Visualizar participantes de um evento
    public function visualizarParticipantesDoEvento(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678905');
        $participant1 = $this->criarUsuario('Participante 1', 'participante1@example.com', '12345678906');
        $participant2 = $this->criarUsuario('Participante 2', 'participante2@example.com', '12345678907');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        // Adiciona participantes diretamente no banco
        $eventParticipant1 = new EventParticipant();
        $eventParticipant1->event_id = $event->id;
        $eventParticipant1->participant_id = $participant1->id;
        $eventParticipant1->save();

        $eventParticipant2 = new EventParticipant();
        $eventParticipant2->event_id = $event->id;
        $eventParticipant2->participant_id = $participant2->id;
        $eventParticipant2->save();

        $page->login($creator->email, '123456');
        $page->amOnPage("/events/{$event->id}/participants");
        $page->wait(1);

        // Verifica o título e contador de participantes
        $page->see('Participantes Inscritos (2)');

        // Verifica que ambos participantes aparecem na lista
        $page->see('Participante 1');
        $page->see('participante1@example.com');
        $page->see('Participante 2');
        $page->see('participante2@example.com');
    }

    // Teste 4: Tentar adicionar mesmo participante duas vezes
    public function tentarAdicionarParticipanteDuplicado(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678908');
        $participant = $this->criarUsuario('Participante', 'participante@example.com', '12345678909');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        // Adiciona participante pela primeira vez
        $eventParticipant = new EventParticipant();
        $eventParticipant->event_id = $event->id;
        $eventParticipant->participant_id = $participant->id;
        $eventParticipant->save();

        $page->login($creator->email, '123456');
        $page->amOnPage("/events/{$event->id}/participants");
        $page->wait(1);

        // Verifica que o participante já não aparece no select (pois já está inscrito)
        $page->dontSeeOptionIsSelected('participant_id', $participant->id);
    }

    // Teste 5: Visualizar evento na listagem com contagem de participantes
    public function visualizarEventoComContagemDeParticipantes(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678910');
        $participant1 = $this->criarUsuario('Participante 1', 'participante1@example.com', '12345678911');
        $participant2 = $this->criarUsuario('Participante 2', 'participante2@example.com', '12345678912');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        // Adiciona participantes
        $eventParticipant1 = new EventParticipant();
        $eventParticipant1->event_id = $event->id;
        $eventParticipant1->participant_id = $participant1->id;
        $eventParticipant1->save();

        $eventParticipant2 = new EventParticipant();
        $eventParticipant2->event_id = $event->id;
        $eventParticipant2->participant_id = $participant2->id;
        $eventParticipant2->save();

        $page->login($creator->email, '123456');
        $page->amOnPage('/events');
        $page->wait(1);

        // Verifica que o evento aparece com a contagem de participantes
        $page->see('Evento de Teste');
        $page->see('2'); // Contador de participantes
    }

    // Teste 6: Usuário não-criador não pode gerenciar participantes
    public function usuarioNaoCriadorNaoPodeGerenciarParticipantes(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678913');
        $otherUser = $this->criarUsuario('Outro Usuario', 'outro@example.com', '12345678914');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        $page->login($otherUser->email, '123456');
        $page->amOnPage("/events/{$event->id}/participants");
        $page->wait(1);

        // Verifica que foi redirecionado e não pode acessar
        $page->seeInCurrentUrl('/events');
        $page->see('Evento não encontrado ou você não tem permissão para gerenciá-lo!');
    }

    // Teste 7: Remover evento remove todos os participantes
    public function removerEventoRemoveTodosParticipantes(AcceptanceTester $page): void
    {
        $creator = $this->criarUsuario('Criador do Evento', 'criador@example.com', '12345678915');
        $participant = $this->criarUsuario('Participante', 'participante@example.com', '12345678916');
        $event = $this->criarEvento($creator, 'Evento de Teste');

        // Adiciona participante
        $eventParticipant = new EventParticipant();
        $eventParticipant->event_id = $event->id;
        $eventParticipant->participant_id = $participant->id;
        $eventParticipant->save();

        // Verifica que o participante existe
        $participantExists = EventParticipant::exists([
            'event_id' => $event->id,
            'participant_id' => $participant->id
        ]);
        if (!$participantExists) {
            throw new \Exception('Participante deveria existir no banco');
        }

        $page->login($creator->email, '123456');
        $page->amOnPage("/events/{$event->id}");
        $page->wait(1);

        // Remove o evento
        $page->click('Remover Evento');
        $page->wait(1);

        // Verifica que o evento foi removido
        $page->see('Evento removido com sucesso!');

        // Verifica que o participante também foi removido (CASCADE)
        $participantStillExists = EventParticipant::exists([
            'event_id' => $event->id,
            'participant_id' => $participant->id
        ]);
        if ($participantStillExists) {
            throw new \Exception('Participante deveria ter sido removido (CASCADE)');
        }
    }

    private function criarUsuario(string $name, string $email, string $cpf): User
    {
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => $cpf
        ]);

        // Debug: check if password was encrypted
        if (empty($user->encrypted_password)) {
            throw new \Exception('encrypted_password is empty after setting password');
        }

        $saved = $user->save();

        if (!$saved) {
            $errors = [];
            if ($user->hasErrors()) {
                foreach (['name', 'email', 'cpf', 'phone', 'password'] as $field) {
                    if ($error = $user->errors($field)) {
                        $errors[$field] = $error;
                    }
                }
            }
            throw new \Exception('Falha ao salvar usuário. Erros: ' . json_encode($errors));
        }

        // Debug: verify user was saved with ID
        if (empty($user->id)) {
            throw new \Exception('User ID is empty after save()');
        }

        return $user;
    }

    private function criarEvento(User $creator, string $name): Event
    {
        $event = new Event();
        $event->name = $name;
        $event->description = 'Descrição do evento de teste';
        $event->start_date = '2025-12-01';
        $event->end_date = null;
        $event->event_location = 'Local de Teste';
        $event->workload_hours = 10;
        $event->event_type = 'Workshop';
        $event->logo_id = null;
        $event->creator_id = $creator->id;
        $event->owner_id = $creator->id;
        $event->created_at = date('Y-m-d H:i:s');
        $event->updated_at = date('Y-m-d H:i:s');
        $event->is_active = true;

        $saved = $event->save();

        if (!$saved) {
            $errors = [];
            if ($event->hasErrors()) {
                foreach (['name', 'description', 'start_date', 'creator_id', 'owner_id'] as $field) {
                    if ($error = $event->errors($field)) {
                        $errors[$field] = $error;
                    }
                }
            }
            throw new \Exception('Falha ao salvar evento. Erros: ' . json_encode($errors) . ' - creator_id: ' . $creator->id);
        }

        return $event;
    }
}
