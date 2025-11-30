<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class EventParticipantsController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = Event::paginate(page: $request->getParam('page', 1), route: 'event.participants.paginate');
        $events = $paginator->registers();

        $title = 'Todos os Eventos';

        $this->render('event_participants/index', compact('paginator', 'events', 'title'));
    }

    public function myEvents(): void
    {
        $events = $this->current_user->participating_events;
        $title = 'Meus Eventos';

        $this->render('event_participants/my_events', compact('events', 'title'));
    }

    public function manage(Request $request): void
    {
        $event_id = $request->getParam('id');
        $event = $this->current_user->createdEvents()->findById($event_id);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para gerenciá-lo!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $participants = $event->participants;
        $allUsers = User::all();

        $title = "Gerenciar Participantes: {$event->name}";

        $this->render('event_participants/manage', compact('event', 'participants', 'allUsers', 'title'));
    }

    public function addParticipant(Request $request): void
    {
        $event_id = $request->getParam('event_id');
        $participant_id = $request->getParam('participant_id');

        $event = $this->current_user->createdEvents()->findById($event_id);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para adicionar participantes!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $eventParticipant = new EventParticipant([
            'event_id' => $event_id,
            'participant_id' => $participant_id
        ]);

        if ($eventParticipant->save()) {
            FlashMessage::success('Participante adicionado com sucesso!');
        } else {
            $message = $eventParticipant->errors('participant_id');
            FlashMessage::danger('Erro ao adicionar participante: ' . $message);
        }

        $this->redirectTo(route('event.participants.manage', ['id' => $event_id]));
    }

    public function removeParticipant(Request $request): void
    {
        $event_id = $request->getParam('event_id');
        $participant_id = $request->getParam('participant_id');

        $event = $this->current_user->createdEvents()->findById($event_id);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para remover participantes!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $eventParticipant = EventParticipant::findBy([
            'event_id' => $event_id,
            'participant_id' => $participant_id
        ]);

        if ($eventParticipant == null) {
            FlashMessage::danger('Participante não encontrado neste evento.');
        } else {
            $eventParticipant->destroy();
            FlashMessage::success('Participante removido com sucesso.');
        }

        $this->redirectTo(route('event.participants.manage', ['id' => $event_id]));
    }
}
