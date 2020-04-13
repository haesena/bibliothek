<?php

class Database {

	// Die Variable in welcher die Verbindung zur Datenbank gespeichert wird
	private $pdo;

	function __construct($dsn, $user, $password) {
		$this->pdo = new PDO($dsn, $user, $password);

		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
	}

	// Eine Funktion um eine SQL Abfrage ausyuführen und das Resultat als Array zu liefern
	public function select($sql, $placeholders = []) {
		$query = $this->pdo->prepare($sql);
		$query->execute($placeholders);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	// Eine Funktion um eine SQL Abfrage ausyuführen und das Resultat als Array zu liefern
	public function selectValue($sql, $placeholders = []) {
		$query = $this->pdo->prepare($sql);
		$query->execute($placeholders);

		return current($query->fetch(PDO::FETCH_ASSOC));
	}

}