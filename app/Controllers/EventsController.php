<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Logo;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class EventsController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = $this->current_user->createdEvents()->paginate(
            page: $request->getParam('page', 1),
            route: 'events.paginate'
        );
        $events = $paginator->registers();

        $title = 'Meus Eventos';

        if ($request->acceptJson()) {
            $this->renderJson('events/index', compact('paginator', 'events', 'title'));
        } else {
            $this->render('events/index', compact('paginator', 'events', 'title'));
        }
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();

        $event = $this->current_user->createdEvents()->findById($params['id']);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para visualizá-lo!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $title = "Evento: {$event->name}";
        $this->render('events/show', compact('event', 'title'));
    }

    public function new(): void
    {
        $event = $this->current_user->createdEvents()->new();
        $logos = Logo::where(['user_id' => $this->current_user->id]);

        $title = 'Novo Evento';
        $this->render('events/new', compact('event', 'logos', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParam('event');

        $event = $this->current_user->createdEvents()->new($params);
        $event->owner_id = $this->current_user->id;
        $event->created_at = date('Y-m-d H:i:s');
        $event->updated_at = date('Y-m-d H:i:s');
        $event->is_active = true;

        if ($event->save()) {
            FlashMessage::success('Evento criado com sucesso!');
            $this->redirectTo(route('events.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $logos = Logo::where(['user_id' => $this->current_user->id]);
            $title = 'Novo Evento';
            $this->render('events/new', compact('event', 'logos', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $event = $this->current_user->createdEvents()->findById($params['id']);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para editá-lo!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $logos = Logo::where(['user_id' => $this->current_user->id]);

        $title = "Editar Evento: {$event->name}";
        $this->render('events/edit', compact('event', 'logos', 'title'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $params = $request->getParam('event');

        $event = $this->current_user->createdEvents()->findById($id);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para editá-lo!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $event->name = $params['name'];
        $event->description = $params['description'];
        $event->start_date = $params['start_date'];
        $event->end_date = $params['end_date'];
        $event->event_location = $params['event_location'];
        $event->workload_hours = $params['workload_hours'];
        $event->event_type = $params['event_type'];
        $event->logo_id = $params['logo_id'] ?? null;
        $event->updated_at = date('Y-m-d H:i:s');

        if ($event->save()) {
            FlashMessage::success('Evento atualizado com sucesso!');
            $this->redirectTo(route('events.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $logos = Logo::where(['user_id' => $this->current_user->id]);
            $title = "Editar Evento: {$event->name}";
            $this->render('events/edit', compact('event', 'logos', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $event = $this->current_user->createdEvents()->findById($params['id']);

        if (!$event) {
            FlashMessage::danger('Evento não encontrado ou você não tem permissão para removê-lo!');
            $this->redirectTo(route('events.index'));
            return;
        }

        $event->destroy();

        FlashMessage::success('Evento removido com sucesso!');
        $this->redirectTo(route('events.index'));
    }

    public function search(Request $request): void
    {
        $searchTerm = $request->getParam('q', '');

        if (empty($searchTerm)) {
            $events = $this->current_user->createdEvents()->get();
        } else {
            $events = Event::searchByName($searchTerm, $this->current_user->id);
        }

        if ($request->acceptJson()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'events' => array_map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->name,
                        'description' => $event->description,
                        'start_date' => $event->start_date,
                        'end_date' => $event->end_date,
                        'event_location' => $event->event_location,
                        'workload_hours' => $event->workload_hours,
                        'event_type' => $event->event_type
                    ];
                }, $events)
            ]);
        } else {
            $this->redirectTo(route('events.index'));
        }
    }
}
