<?php

namespace Controller;

class HomeController extends Controller {
    public function getHome($request, $response, $args) {

        $sql = "SELECT COUNT(*) FROM book";
        $countBooks = $this->db->selectValue($sql);

        $templateValues = [
            'countBooks' => $countBooks
        ];

        return $this->view->render($response, 'home.phtml', $templateValues);
    }
}