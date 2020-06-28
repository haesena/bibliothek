<?php

use Psr\Http\Message\ResponseInterface;

class Table {
    /** @var Database die Datenbank-Verbindung  */
    private $db = null;
    /** @var \Slim\Views\PhpRenderer Slim View für die Anzeige */
    private $view;
    /** @var \Slim\Interfaces\RouteParserInterface */
    private $router;

    /** @var string die Haupttabelle aus welcher die Daten gelesen werden */
	private $databaseTable = null;
	/** @var array Weitere Tabellen welche über JOIN im SQL ergänzt werden */
	private $joins = [];
	/** @var string Spalte in welcher die ID dieses Eintrag geführt wird */
	private $idCol;
	private $columns = [];

	/** @var array  */
	private $columnFilter = [];


	/** @var \Slim\Psr7\Request */
	private $request;
	/** @var \Slim\Psr7\Response */
    private $response;

    /** @var string key for the session entries of this table */
    private $sessionPath = null;

    private $session = [];

    /**
     * Table constructor.
     * @param Database $database
     * @param \Slim\Views\PhpRenderer $view
     * @param \Slim\Psr7\Request $request
     * @param \Slim\Psr7\Response $response
     */
	function __construct($database, $view, $request, $response) {
		$this->db = $database;
		$this->view = $view;

		$this->router = $view->getAttribute('router');
		$this->request = $request;
		$this->response = $response;

		$this->sessionPath = 'tbl_ctrl_' . $request->getUri()->getPath();
		$this->session = $_SESSION[$this->sessionPath];

        $this->view->addAttribute('currentUrl', $request->getAttribute('route'));
	}

	private function debug($val, $title = null) {
	    if($title) {
	        print "<b>$title</b>";
        }
	    print "<pre>";
	    print_r($val);
        print "</pre>";
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
    public function addJoin($dbTable, $alias, $joinCondition, $leftJoin = true) {
        $join = $leftJoin ? "LEFT JOIN" : "JOIN";

	    $this->joins[]= "{$join} {$dbTable} AS {$alias} ON {$joinCondition}";
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

	private function getData() {

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

        list($filter, $filterValues) = $this->getFilterSQL();

        if(count($filter) > 0) {
            $sql.= " WHERE ".implode(" AND ", $filter);
        }

        // Anzahl Datensätze zählen
        $countSQL = str_replace("SELECT {$collist}", "SELECT COUNT(*)", $sql);
        $count = $this->db->selectValue($countSQL, $filterValues);

        $data = $this->db->select($sql, $filterValues);

        return $data;
    }

    private function getFilterSQL() {

        $filter = [];
        $filterValues = [];

        $queryParams = $this->request->getQueryParams();
        foreach($this->columns as $col) {
            $filterCol = "filter-".$col['alias'];

            $filterVal = null;

            if(isset($queryParams[$filterCol])) {
                $filterVal = $queryParams[$filterCol];
            } elseif(isset($this->session['filter'][$filterCol])) {
                $filterVal = $this->session['filter'][$filterCol];
            }

            if(strlen($filterVal) === 0) {
                unset($this->session['filter'][$filterCol]);
                continue;
            }

            $this->session['filter'][$filterCol] = $filterVal;

            $filterValues[":filter_{$col['alias']}"] = "%".$filterVal."%";
            $filter[]= "CAST({$col['sql']} AS CHAR(255)) LIKE :filter_{$col['alias']}";

            $this->columnFilter[$col['alias']] = $filterVal;
        }

        return [$filter, $filterValues];
    }

    /**
     * @param array $templateValues
     */
	public function printTable($templateValues) {
        $data = $this->getData();

        $templateValues['data'] = $data;
        $templateValues['cols'] = $this->columns;
        $templateValues['filters'] = $this->columnFilter;
        $templateValues['filterSet'] = count($this->columnFilter) > 0;

        $_SESSION[$this->sessionPath] = $this->session;

        // HTML-View anzeigen
        return $this->view->render($this->response, 'table.phtml', $templateValues);
	}
}