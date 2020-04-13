<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class BookController extends Controller {

    public function getBooks($request, $response, $args) {

        $books = $this->db->select("SELECT * FROM book");
        $this->log($books, "Books from Database");

        $response->getBody()->write("TODO: return list of books from database");
        return $response;
    }

    public function newBook($request, $response, $args) {
        
    }

    public function getSingleBook($request, $response, $args) {
        $bookId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(!is_numeric($bookId)) {
            $response->getBody()->write("ERROR: Invalid bookId, must be numeric");
            return $response;
        }

        $books = $this->db->select("SELECT * FROM book WHERE book_id = :id", [':id' => $bookId]);

        if(count($books) != 1) {
            $response->getBody()->write("ERROR: no book found for bookId {$bookId}");
            return $response;
        } else {
            $book = current($books);
        }

        $this->log($book, "Book from Database");

        $response->getBody()->write("TODO: get the book {$bookId} from database");
        return $response;
    }
}