<?php

use Psr\Http\Message\ResponseInterface;

class Table {
    /** @var Database die Datenbank-Verbindung  */
    private $db = null;
    /** @var \Slim\Views\PhpRenderer Slim View für die Anzeige */
    private $view;
    /** @var \Slim\Interfaces\RouteParserInterface */
    private $router;
    /** @var string */
    private $urlBase;

    /** @var string die Haupttabelle aus welcher die Daten gelesen werden */
	private $databaseTable = null;
	/** @var array Weitere Tabellen welche über JOIN im SQL ergänzt werden */
	private $joins = [];
	/** @var string Spalte in welcher die ID dieses Eintrag geführt wird */
	private $idCol;
	private $columns = [];

    /**
     * Table constructor.
     * @param Database $database
     * @param \Slim\Views\PhpRenderer $view
     */
	function __construct($database, $view) {
		$this->db = $database;
		$this->view = $view;

		$this->router = $view->getAttribute('router');
	}

	public function setUrls($urlBase) {
        $this->view->addAttribute('newUrl', $this->getUrl($urlBase, 'new'));
        $this->view->addAttribute('detailUrl', $this->getUrl($urlBase, 'detail'));
        $this->view->addAttribute('copyUrl', $this->getUrl($urlBase, 'copy'));
        $this->view->addAttribute('deleteUrl', $this->getUrl($urlBase, 'delete'));
    }

    private function getUrl($base, $method) {
	    $url = $base.'-'.$method;
        try {
            // Versuche die URL aus dem router zu lesen
            $test = $this->router->urlFor($url, ['id'=>'tmp'])."<br>";
        } catch(\Exception $e) {
            // Falls es die URL nicht gibt, wird ein Fehler geworfen der hier abgefangen wird
            $test = false;
        }
        // Falls der Test erfolgreich war, wird die URL geliefert, sonst nichts
        return $test ? $url : null;
    }

	public function setDBTable($dbTable, $alias) {
	    $this->databaseTable = "{$dbTable} AS {$alias}";
	    return $this;
    }

    /**
     * @param string $dbTable
     * @param string $alias
     * @param string $joinCondition
     */
    public function addJoin($dbTable, $alias, $joinCondition) {
	    $this->joins[]= "JOIN {$dbTable} AS {$alias} ON {$joinCondition}";
	    return $this;
    }

    public function setIdCol($idCol) {
        $this->idCol = $idCol;
        return $this;
    }

	public function addColum($dbSelect, $colTitle) {
		$this->columns[]= [
		    'sql' => $dbSelect,
            'title'=> $colTitle,
            'alias' => "col".count($this->columns)
        ];
		return $this;
	}

	private function getSQL() {

        $collist = [$this->idCol." AS id"];
        foreach($this->columns as $i => $col) {
            $collist[]= $col['sql']." AS col{$i}";
        }

        $collist = implode(", ", $collist);

        $sql = "SELECT {$collist} 
                FROM {$this->databaseTable}
                ";
        if(count($this->joins) > 0) {
            $sql.= implode("\n", $this->joins);
        }

        return $sql;
    }

    /**
     * @param ResponseInterface $response
     * @param array $templateValues
     */
	public function printTable($response, $templateValues) {
        $sql = $this->getSQL();

        $templateValues['data'] = $this->db->select($sql);
        $templateValues['cols'] = $this->columns;


        // HTML-View anzeigen
        return $this->view->render($response, 'table.phtml', $templateValues);
	}
}