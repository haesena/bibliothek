<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class GenreController extends Controller {

    // Diese Funktion wird als Callback zur Route /genre aufgerufen
    public function getGenre($request, $response, $args) {

        // Hole alle genres aus der Datenbank
        $array_genre = $this->db->select("SELECT * FROM genre ORDER BY genre, sub_genre");

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'array_genre' => $array_genre,
            'mainTitle' => 'Genre'
        ];
        //$this->log($genre,'genresHH');

        // HTML-View anyeigen
        return $this->view->render($response, 'genre.phtml', $templateValues);
    }

    // Diese Funktion wird als Callback zur Route /locations/{id} aufgerufen
    public function getSinglegenre($request, $response, $args) {

        $genreId = $args['id'];

        // Fehler abfangen falls Genre-ID nicht numerisch ist
        if(!is_numeric($genreId)) {
            $response->getBody()->write("ERROR: Invalid genreId, must be numeric");
            return $response;
        }

        $genre = $this->db->select("SELECT * FROM genre WHERE genre_id = :id", [':id' => $genreId]);

        if(count($genre) != 1) {
            $response->getBody()->write("ERROR: no genre found for genreId {$genreId}");
            return $response;
        } else {
            $sgenre = current($genre);
        }

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'Edit genre',
            'genre' => $sgenre
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'genre-form.phtml', $templateValues);
    }

    public function newGenre($request, $response, $args) {
        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New genre'
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'genre-form.phtml', $templateValues);
    }

    public function copyGenre($request, $response, $args) {

        $genreId = $args['id'];

        // Fehler abfangen falls Genre-ID nicht numerisch ist
        if(!is_numeric($genreId)) {
            $response->getBody()->write("ERROR: Invalid genreId, must be numeric");
            return $response;
        }

        $genre = $this->db->selectValue("SELECT genre FROM genre WHERE genre_id = :id", [':id' => $genreId]);

        
        //$sgenre [ 'sub_genre' ] = '';

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New genre (copy)',
            'genre'  => [ 'genre' => $genre ]
        //     'genre' => [ 'genre' => $sgenre[ 'genre' ] ] 
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