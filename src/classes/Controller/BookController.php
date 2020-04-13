<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class BookController extends Controller {

    public function getBooks($request, $response, $args) {

        $books = $this->db->select("SELECT * FROM book");
        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'books' => $books,
            'mainTitle' => 'Books'
        ];
        //$this->log($genre,'genresHH');

        // HTML-View anyeigen
        return $this->view->render($response, 'books.phtml', $templateValues);
    }

    public function newBook($request, $response, $args) {
        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New book',
            'locations' => $this->getLocationsList(),
            'genres' => $this->getGenresList()
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'books-form.phtml', $templateValues);
    }

    public function getSingleBook($request, $response, $args) {

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'Edit book'
        ];

        $bookId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(!is_numeric($bookId)) {
            $templateValues['error'] = "ERROR: Invalid bookId, must be numeric";
            $templateValues['back'] = "books";
            return $this->view->render($response, 'error.phtml', $templateValues);
        }

        $books = $this->db->select("SELECT * FROM book WHERE book_id = :id", [':id' => $bookId]);

        if(count($books) != 1) {
            $templateValues['error'] = "ERROR: no book found for bookId {$bookId}";
            $templateValues['back'] = "books";
            return $this->view->render($response, 'error.phtml', $templateValues);
        } else {
            $book = current($books);
        }


        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New book',
            'book' => $book,
            'locations' => $this->getLocationsList(),
            'genres' => $this->getGenresList()
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'books-form.phtml', $templateValues);

        return $response;
    }

    public function saveBook($request, $response, $args) {
        $body = $request->getParsedBody();

        $values = [
            'title' => $body['title'],
            'author' => $body['author'],
            'published_year' => $body['year'],
            'loc_id' => $body['location'] ?? null,
            'genre_id' => $body['genre'] ?? null,
        ];

        if(is_numeric($body['id'])) {
            $this->db->update('book', $values, "book_id = ".$body['id']);
        } else {
            $this->db->insert('book', $values);
        }

        return $this->redirect($response, '/books');
    }

    public function deleteBook($request, $response, $args) {

        $bookId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(is_numeric($bookId)) {
            $this->db->delete("book", "book_id = ".$bookId);
        }

        return $this->redirect($response, '/books');
    }

    private function getLocationsList() {
        $locationsFromDB = $this->db->select("SELECT * FROM location");
        $locations = [];

        foreach($locationsFromDB as $loc) {
            $display = $loc['location'];
            if(strlen($loc['room']) > 0) {
                $display.= " - " . $loc['room'];
            }
            if(strlen($loc['storage']) > 0) {
                $display.= " - " . $loc['storage'];
            }

            $locations[$loc['loc_id']] = $display;
        }

        return $locations;
    }

    private function getGenresList() {
        $genresFromDB = $this->db->select("SELECT * FROM genre");
        $genres = [];

        foreach($genresFromDB as $genre) {
            $display = $genre['genre'];
            if(strlen($genre['sub_genre']) > 0) {
                $display.= " - " . $genre['sub_genre'];
            }

            $genres[$genre['genre_id']] = $display;
        }

        return $genres;
    }
}