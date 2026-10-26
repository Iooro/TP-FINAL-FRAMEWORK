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

    public function contacts(Request $request): Response
    {
        $contacts = json_decode(file_get_contents(__DIR__ . '/../../data/bdd.json'), true);
        $contacts = $contacts['contacts'] ?? [];
        $q = $request->query('q') ?? null;
        if ($q !== null) {
            $contacts = array_filter($contacts, function ($contact) use ($q) {
                return str_contains(strtolower($contact['nom']), strtolower($q)) || 
                    str_contains(strtolower($contact['prenom']), strtolower($q)) || 
                    str_contains(strtolower($contact['numero']), strtolower($q)) || 
                    str_contains(strtolower($contact['commentaire']), strtolower($q));
            });
        }
        return Response::html($this->view->render('appContact/contact.html.twig', [
            'contacts' => $contacts
        ]));
    }

    public function edit(Request $request, array $params = []): Response
    {
        $contacts = json_decode(file_get_contents(__DIR__ . '/../../data/bdd.json'), true);
        $contacts = $contacts['contacts'] ?? [];
        $id = $params['id'] ?? null;

        $contact = array_filter($contacts, function ($contacts) use ($id) {
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
        $contacts = $data['contacts'] ?? [];
        $id = $params['id'] ?? null;

        foreach ($contacts as &$contact) {
            if ($contact['id'] == $id) {
                $contact['nom'] = $request->input('nom') ?? $contact['nom'];
                $contact['prenom'] = $request->input('prenom') ?? $contact['prenom'];
                $contact['numero'] = $request->input('numero') ?? $contact['numero'];
                $contact['commentaire'] = $request->input('commentaire') ?? $contact['commentaire'];
                break;
            }
        }
        unset($contact);

        $data['contacts'] = $contacts;
        
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        file_put_contents($fichier, $json);

        return Response::redirect('/contacts');
    }
    
    public function delete(Request $request, array $params = []): Response
    {
        $fichier = __DIR__ . '/../../data/bdd.json';

        $data = json_decode(file_get_contents($fichier), true);
        $contacts = $data['contacts'] ?? [];

        $id = $params['id'] ?? null;

        $contacts = array_filter($contacts, function ($contact) use ($id) {
            return $contact['id'] != $id;
        });

        $data['contacts'] = array_values($contacts);

        file_put_contents(
            $fichier,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        return Response::redirect('/contacts');
    }

    public function creer(Request $request): Response
    {
        return Response::html($this->view->render('appContact/creer.html.twig'));
    }

    public function ajouter(Request $request): Response
    {
        $fichier = __DIR__ . '/../../data/bdd.json';

        $data = json_decode(file_get_contents($fichier), true);
        $contacts = $data['contacts'] ?? [];

        // Trouver le plus grand ID
        $maxId = 0;

        foreach ($contacts as $contact) {
            if ($contact['id'] > $maxId) {
                $maxId = $contact['id'];
            }
        }

        // Créer le nouveau contact
        $nouveauContact = [
            'id' => $maxId + 1,
            'nom' => $request->input('nom'),
            'prenom' => $request->input('prenom'),
            'numero' => $request->input('numero'),
            'commentaire' => $request->input('commentaire') ?? ''
        ];

        // Ajouter le contact
        $contacts[] = $nouveauContact;

        // Mettre à jour le JSON
        $data['contacts'] = $contacts;

        file_put_contents(
            $fichier,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );

        return Response::redirect('/contacts');
    }
}