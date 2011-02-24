<?php
# a mess
require_once "dbeng/db_abs.php";

class db_sqlite3 extends db_abs {
	private $_conn;
	
	function __construct($path)
    {
		$this->_conn = sqlite_factory($path);
		$this->createDatabase();
    }
		
	static function safe($s) {
		return sqlite_escape_string($s);
	} # safe
	
	function exec($s, $p = array()) {
		$p = array_map(array('db_sqlite3', 'safe'), $p);
		
		# echo "EXECUTING: " . vsprintf($s, $p) . "\r\n";
		
		return $this->_conn->queryExec(vsprintf($s, $p));
	} # exec
		
	function singleQuery($s, $p = array()) {
		$p = array_map(array('db_sqlite3', 'safe'), $p);
		
		return $this->_conn->singleQuery(vsprintf($s, $p));
	} # singleQuery

	function arrayQuery($s, $p = array()) {
		$p = array_map(array('db_sqlite3', 'safe'), $p);
		
		return $this->_conn->arrayQuery(vsprintf($s, $p));
	} # arrayQuery

	
	function createDatabase() {
		$q = $this->_conn->singleQuery("PRAGMA table_info(spots)");
		if (!$q) {
			$this->_conn->queryExec("CREATE TABLE spots(id INTEGER PRIMARY KEY ASC, 
											messageid TEXT,
											spotid INTEGER,
											category INTEGER, 
											subcat INTEGER,
											poster TEXT,
											groupname TEXT,
											subcata TEXT,
											subcatb TEXT,
											subcatc TEXT,
											subcatd TEXT,
											title TEXT,
											tag TEXT,
											stamp INTEGER);");
			$this->_conn->queryExec("CREATE TABLE nntp(server TEXT PRIMARY KEY,
										   maxarticleid INTEGER UNIQUE);");

			# create indices
			$this->_conn->queryExec("CREATE INDEX idx_spots_1 ON spots(id, category, subcata, subcatd, stamp DESC)");
			$this->_conn->queryExec("CREATE INDEX idx_spots_2 ON spots(id, category, subcatd, stamp DESC)");
			$this->_conn->queryExec("CREATE INDEX idx_spots_3 ON spots(messageid)");
		} # if
	} # Createdatabase

} # class