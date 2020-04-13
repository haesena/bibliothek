<?php

class Database {

	// Die Variable in welcher die Verbindung zur Datenbank gespeichert wird
	private $pdo;

	function __construct($dsn, $user, $password) {
		$this->pdo = new PDO($dsn, $user, $password);

		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
	}

	// Eine Funktion um eine SQL Abfrage auszuführen und das Resultat als Array zu liefern
	public function select($sql, $placeholders = []) {
		$query = $this->pdo->prepare($sql);
		$query->execute($placeholders);
		return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	// Eine Funktion um eine SQL Abfrage auszuführen und das Resultat als Wert zu liefern
	public function selectValue($sql, $placeholders = []) {
		$query = $this->pdo->prepare($sql);
		$query->execute($placeholders);

		return current($query->fetch(PDO::FETCH_ASSOC));
	}

	// Eine Funktion um Werte in einer Tabelle einzufügren
	public function insert($table, $values) {

		if(count($values) == 0) {
			return;
		}

		$colNames = implode(', ', array_keys($values));

		$preparedValues = $placeholders = [];
		foreach($values as $val) {
			if(mb_strlen($val) == 0) {
				$placeholders[]= "NULL";
			} else {
				$placeholders[]= "?";
				$preparedValues[]= $val;
			}
		}

		$placeholders = implode(', ', $placeholders);

		$sql = "INSERT INTO ".$table." (".$colNames.") VALUES(".$placeholders.")";

		try {
			$query = $this->pdo->prepare($sql);
			$query->execute($preparedValues);
		} catch(Exception $e) {
			print $e->getMessage()."<br>";
			print "Fehler beim Ausführen des INSERT:<br>";
			print $sql;
			print "<br>".print_r($values, true);

			exit();
		}
		
		return true;
	}

	// Eine Funktion um Werte in einer Tabelle zu einer ID zu aktualisieren
	public function update($table, $values, $where) {

		if(count($values) == 0) {
			return;
		}

		$updateValues = [];
		foreach($values as $col => $value) {
			$updateValues[]= $col." = ?";
		}

		$sql = "UPDATE ".$table." SET ".implode(', ', $updateValues)." WHERE ".$where;

		$query = $this->pdo->prepare($sql);
		$query->execute(array_values($values));

		return true;
	}

	public function delete($table, $where) {

		$sql = "DELETE FROM ".$table." WHERE ".$where;

		$query = $this->pdo->prepare($sql);
		$query->execute();

		return true;

	}

}