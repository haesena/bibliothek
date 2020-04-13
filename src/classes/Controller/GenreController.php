<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class GenreController extends Controller {

    // Diese Funktion wird als Callback zur Route /genre aufgerufen
    public function getGenre($request, $response, $args) {

        // Hole alle genres aus der Datenbank
        $array_genre = $this->db->select("SELECT * FROM genre");

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'array_genre' => $array_genre,
            'mainTitle' => 'Genre'
        ];
        //$this->log($genre,'genresHH');

        // HTML-View anyeigen
        return $this->view->render($response, 'genre.phtml', $templateValues);
    }
/*
    // Diese Funktion wird als Callback zur Route /locations/{id} aufgerufen
    public function getSingleLocation($request, $response, $args) {

        $locationId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(!is_numeric($locationId)) {
            $response->getBody()->write("ERROR: Invalid bookId, must be numeric");
            return $response;
        }

        $locations = $this->db->select("SELECT * FROM location WHERE loc_id = :id", [':id' => $locationId]);

        if(count($locations) != 1) {
            $response->getBody()->write("ERROR: no location found for locationId {$locationId}");
            return $response;
        } else {
            $location = current($locations);
        }

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'Edit location',
            'location' => $location
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'locations-form.phtml', $templateValues);
    }
*/
    public function newGenre($request, $response, $args) {
        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New genre'
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'genre-form.phtml', $templateValues);
    }


    public function saveGenre($request, $response, $args) {
        $body = $request->getParsedBody();

        $values = [
            'genre' => $body['genre'],
            'sub_genre' => $body['sub_genre'],
        ];

        if(is_numeric($body['id'])) {
            $this->db->update('genre', $values, "genre_id = ".$body['id']);
        } else {
            $this->db->insert('genre', $values);
        }

        return $this->redirect($response, '/genre');
    }

    public function deleteGenre($request, $response, $args) {

        $genreId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(is_numeric($genreId)) {
            $this->db->delete("genre", "genre_id = ".$genreId);
        }

        return $this->redirect($response, '/genre');
    }
    
}