<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class LocationController extends Controller {

    // Diese Funktion wird als Callback zur Route /locations aufgerufen
    public function getLocations($request, $response, $args) {

        // Hole alle Locations aus der Datenbank
        $locations = $this->db->select("SELECT * FROM location");

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'locations' => $locations
        ];

        // HTML-View anyeigen
        return $this->view->render($response, 'locations.phtml', $templateValues);
    }

    // Diese Funktion wird als Callback zur Route /locations/{id} aufgerufen
    public function getSingleLocation($request, $response, $args) {
        $response->getBody()->write("TODO: get the location {$locId} from database");
        return $response;
    }
}