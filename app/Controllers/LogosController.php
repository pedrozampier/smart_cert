<?php

namespace App\Controllers;

use App\Models\Logo;
use App\Services\LogoUpload;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class LogosController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = Logo::paginate(page: $request->getParam('page', 1));
        $logos = $paginator->registers();

        $title = 'Logos Cadastrados';

        if ($request->acceptJson()) {
            $this->renderJson('logos/index', compact('paginator', 'logos', 'title'));
        } else {
            $this->render('logos/index', compact('paginator', 'logos', 'title'));
        }
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();

        $logo = Logo::findById($params['id']);

        $title = "Visualização do Logo #{$logo->id}";
        $this->render('logos/show', compact('logo', 'title'));
    }

    public function new(): void
    {
        $logo = new Logo();

        $title = 'Novo Logo';
        $this->render('logos/new', compact('logo', 'title'));
    }

    public function create(Request $request): void
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            FlashMessage::danger('Nenhum arquivo foi enviado ou ocorreu um erro no upload!');
            $logo = new Logo();
            $title = 'Novo Logo';
            $this->render('logos/new', compact('logo', 'title'));
            return;
        }

        $logoUpload = new LogoUpload(
            user_id: $this->current_user->id,
            validations: [
                'extension' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'size' => 5 * 1024 * 1024
            ]
        );

        $logo = $logoUpload->upload($_FILES['logo']);

        if ($logo) {
            FlashMessage::success('Logo cadastrado com sucesso!');
            $this->redirectTo(route('logos.index'));
        } else {
            FlashMessage::danger('Erro ao fazer upload do logo! Verifique o formato e tamanho do arquivo.');
            $logo = new Logo();
            $title = 'Novo Logo';
            $this->render('logos/new', compact('logo', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();

        $logo = Logo::findById($params['id']);

        $logoUpload = new LogoUpload($logo->user_id);

        if ($logoUpload->delete($logo)) {
            FlashMessage::success('Logo removido com sucesso!');
        } else {
            FlashMessage::danger('Erro ao remover logo!');
        }

        $this->redirectTo(route('logos.index'));
    }
}
