<?php

namespace App\Controllers;

use App\Container;
use App\Request;
use App\Response;

class ContactController
{
    private \App\view\TwigRenderer $view;

    public function __construct(Container $container)
    {
        $this->view = $container->get('view');
    }

    public function contacts(Request $request, array $params = []): Response
    {
        $dossiers = json_decode(file_get_contents(__DIR__ . '/../../data/bdd.json'), true);
        $dossiers = $dossiers['contacts'] ?? [];
        $q = $request->query('q') ?? null;
        if ($q !== null) {
            $dossiers = array_filter($dossiers, function ($contact) use ($q) {
                return str_contains(strtolower($contact['nom']), strtolower($q)) || 
                    str_contains(strtolower($contact['prenom']), strtolower($q)) || 
                    str_contains(strtolower($contact['numero']), strtolower($q)) || 
                    str_contains(strtolower($contact['commentaire']), strtolower($q));
            });
        }
        return Response::html($this->view->render('appContact/contact.html.twig', [
            'dossiers' => $dossiers
        ]));
    }

    public function edit(Request $request, array $params = []): Response
    {
        $dossiers = json_decode(file_get_contents(__DIR__ . '/../../data/bdd.json'), true);
        $dossiers = $dossiers['contacts'] ?? [];
        $id = $params['id'] ?? null;

        $contact = array_filter($dossiers, function ($contacts) use ($id) {
            return $contacts['id'] == $id;
        });

        $contact = array_shift($contact);

        return Response::html($this->view->render('appContact/update.html.twig', [
            'contact' => $contact
        ]));
    }

    public function update(Request $request, array $params = []): Response
    {
        $fichier = __DIR__ . '/../../data/bdd.json';
        $data = json_decode(file_get_contents($fichier), true);
        $dossiers = $data['contacts'] ?? [];
        $id = $params['id'] ?? null;

        foreach ($dossiers as &$contact) {
            if ($contact['id'] == $id) {
                $contact['nom'] = $request->input('nom') ?? $contact['nom'];
                $contact['prenom'] = $request->input('prenom') ?? $contact['prenom'];
                $contact['numero'] = $request->input('numero') ?? $contact['numero'];
                $contact['commentaire'] = $request->input('commentaire') ?? $contact['commentaire'];
                break;
            }
        }
        unset($contact);

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        file_put_contents($fichier, $json);

        return Response::redirect('/contacts');
    }
}