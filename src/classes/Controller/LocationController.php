<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class LocationController extends Controller {

    // Diese Funktion wird als Callback zur Route /locations aufgerufen
    public function getLocations($request, $response, $args) {

        // Hole alle Locations aus der Datenbank
        $locations = $this->db->select("SELECT * FROM location ORDER BY room, storage");

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'locations' => $locations,
            'mainTitle' => 'Locations'
        ];

        // HTML-View anyeigen
        return $this->view->render($response, 'locations.phtml', $templateValues);
    }

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

    public function copyLocation($request, $response, $args) {

        $locationId = $args['id'];

        // Fehler abfangen falls Genre-ID nicht numerisch ist
        if(!is_numeric($locationId)) {
            $response->getBody()->write("ERROR: Invalid locationId, must be numeric");
            return $response;
        }


        $locations = $this->db->select("SELECT location, room, storage FROM location WHERE loc_id = :id", [':id' => $locationId]);

        if(count($locations) != 1) {
            $response->getBody()->write("ERROR: no location found for locationId {$locationId}");
            return $response;
        } else {
            $location = current($locations);
        }
        
        //$locations [ 'storage' ] = '';

        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New genre (copy)',
            'location' => $location
        ];

        // HTML-View anyeigen
        return $this->view->render($response, 'locations-form.phtml', $templateValues);
    }

    public function newLocation($request, $response, $args) {
        // Vairablen welche dem HTML übergeben werden sollen
        $templateValues = [
            'mainTitle' => 'New location'
        ];
        // HTML-View anyeigen
        return $this->view->render($response, 'locations-form.phtml', $templateValues);
    }

    public function saveLocation($request, $response, $args) {
        $body = $request->getParsedBody();

        $values = [
            'location' => $body['location'],
            'room' => $body['room'],
            'storage' => $body['storage'],
        ];

        if(is_numeric($body['id'])) {
            $this->db->update('location', $values, "loc_id = ".$body['id']);
        } else {
            $this->db->insert('location', $values);
        }

        return $this->redirect($response, '/locations');
    }

    public function deleteLocation($request, $response, $args) {

        $locationId = $args['id'];

        // Fehler abfangen falls Book-ID nicht numerisch ist
        if(is_numeric($locationId)) {
            $this->db->delete("location", "loc_id = ".$locationId);
        }

        return $this->redirect($response, '/locations');
    }
}