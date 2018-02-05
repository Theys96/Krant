<?php
Class Stukjes {
	private $db;
	
	function __construct(Mysqli $db)
		{
		$this->db = $db;
		}
	
	function draft($auteur, $titel, $categorie, $tekst, $klaar, $err) {
		$done = ($klaar == '1' ? 1 : 0);
        $categorie = intval($categorie);
        $auteur = $this->db->real_escape_string($auteur);
        $titel = $this->db->real_escape_string($titel);
        $tekst = $this->db->real_escape_string($tekst);
					
		$query = "INSERT INTO drafts (categorie, titel, user, tekst, klaar) VALUES (" . $categorie . ", '" . $titel . "', '" . $auteur . "', '" . $tekst . "', " . $done . ")";
		if(!$this->db->query($query)) {
			$err->throwError("Fout bij het aanmaken van een draft. Controleer de query: ");
			$err->throwError($query);
		}
		$err->throwMessage("Laatste draft: " . date("H:i:s"));
		return $this->db->insert_id;
	}
	
	function updatedraft($id, $titel, $categorie, $tekst, $klaar, $err) {
		$done = ($klaar == '1' ? 1 : 0);
       	$categorie = intval($categorie);
        $auteur = $this->db->real_escape_string($auteur);
        $titel = $this->db->real_escape_string($titel);
        $tekst = $this->db->real_escape_string($tekst);
					
		$query = "UPDATE drafts SET titel='" . $titel . "', categorie='" . $categorie . "', tekst='" . $tekst . "', klaar=" . $done . " WHERE id=" . $id;
		if(!$this->db->query($query)) {
			$err->throwError("Fout bij het bijwerken van een draft. Controleer de query: ");
			$err->throwError($query);
		}
		$err->throwMessage("Laatste draft: " . date("H:i:s"));
		return ($query != false);
	}
		
	function plaatsdraft($draftid, $type, $stukje, $err) {
		$query = "SELECT * FROM drafts WHERE id=" . $draftid;
		$draft = $this->db->query($query);
		if ($draft) {
			$draft = $draft->fetch_assoc();
		} else {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}
		
		if ($draft['titel'] == '') {
			$draft['titel'] = "Geen titel";
		}

		$categorie = $this->db->real_escape_string($draft['categorie']);
		$titel = $this->db->real_escape_string($draft['titel']);
		$auteur = $this->db->real_escape_string($draft['user']);
		$tekst = $this->db->real_escape_string($draft['tekst']);
		$done = $this->db->real_escape_string($draft['klaar']);
		
		switch($type) {
			case 'Edit':
				$query = "INSERT INTO stukjes (stukje, categorie, version, type, titel, user, tekst, klaar) VALUES (" . $stukje . ", " . $categorie . ", " . ($this->getVersion($stukje, $err)+1) . ", '" . $type . "', '" . $titel . "', '" . $auteur . "', '" . $tekst . "', " . $done . ")";
				break;

			case 'Nagekeken':
				$version = $this->getVersion($stukje, $err) + 1;
				$query = "INSERT INTO stukjes (stukje, categorie, version, type, titel, user, tekst, klaar) VALUES (" . $stukje . ", " . $categorie . ", " . ($this->getVersion($stukje, $err)+1) . ", '" . $type . "', '" . $titel . "', '" . $auteur . "', '" . $tekst . "', " . $done . ")";
				break;

			case 'Nieuw':
				$query = "INSERT INTO stukjes (stukje, categorie, version, type, titel, user, tekst, klaar) VALUES (" . ($this->numStukjes()+1) . ", " . $categorie . ", 0, '" . $type . "', '" . $titel . "', '" . $auteur . "', '" . $tekst . "', " . $done . ")";
				break;
		}
		
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$query = "DELETE FROM drafts WHERE id=" . $draftid;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$err->throwMessage("Stukje geplaatst.");
	}
	
	function getAuthor($stukje, $table, $err) {
		if (in_array($table, array('stukjes','bin','geplaatst'))) {
			$query = "SELECT * FROM ".$table." WHERE type != 'Nagekeken' AND stukje=" . $stukje . " ORDER BY timestamp DESC";
			$result = $this->db->query($query);
			if ($result) {
				$stukje = $result->fetch_assoc();
				return $stukje['user'];
			} else {
				$err->throwError("Er is iets misgegaan bij het zoeken van de auteur. Controleer de query: ");
				$err->throwError($query);
				return "geen auteur";
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het zoeken van de auteur.");
			$err->throwError("De tabel '" . htmlspecialchars($table) . "' bestaat niet.");
		}
	}
	
	function getVersion($stukje, $err) {
		$query = "SELECT MAX(version) FROM `stukjes` WHERE stukje=" . $stukje;
		$result = $this->db->query($query);
		if ($result) {
			$result = $result->fetch_assoc();
			return $result['MAX(version)'];
		}
		return 0;
	}
	
	function getStukje($stukje, $version, $err) {
		if (isset($version)) {
			$query = "SELECT *, CHAR_LENGTH(tekst) AS lengte FROM `stukjes` WHERE `stukje`=" . $stukje . " AND `version`=" . $version;
		} else {
			$query = "
			SELECT o.*, CHAR_LENGTH(o.tekst) AS lengte
			FROM `stukjes` o
			LEFT JOIN `stukjes` b
			ON o.stukje = b.stukje AND o.version < b.version
			WHERE b.version is NULL
			AND o.`stukje`=" . $stukje;
			}

		$result = $this->db->query($query);
		if ($result) {
			return $result->fetch_assoc();
		} else {
			$err->throwError("Er is iets misgegaan bij het opvragen van een stukje. Controleer de query: ");
			$err->throwError($query);
			return array();
		}
	}
	
	function getVersions($stukje, $err) {
		$out = array();
		$query = "SELECT *, CHAR_LENGTH(tekst) as lengte FROM `stukjes` WHERE `stukje`=" . $stukje . " ORDER BY timestamp DESC";

		$result = $this->db->query($query);
		if ($result != false) {
			while (($version = $result->fetch_assoc()) != false) {
				$out[$version['version']] = $version;
				}
		} else {
			$err->throwError("Er is iets misgegaan bij het opvragen van de versies. Controleer de query: ");
			$err->throwError($query);
		}
		return $out;
	}
	
	function getStukjes($condition, $err) {
		$query = "
		SELECT o.*, CHAR_LENGTH(o.tekst) AS lengte 
		FROM `stukjes` o
		LEFT JOIN `stukjes` b
		ON o.stukje = b.stukje AND o.version < b.version
		WHERE b.version is NULL";
		if (isset($condition['cat']))
			{
			$query .= " AND o.categorie='" . $condition['cat'] . "'";
			}

		$array = array();
		$result = $this->db->query($query);
		if ($result) {
			while ($stukje = $result->fetch_assoc()) {
				$array[$stukje['stukje']] = $stukje;
			}
			return $array;
		} else {
			$err->throwError("Er is iets misgegaan bij het binnenhalen van stukjes. Controleer de query: ");
			$err->throwError($query);
			return array();
		}
	}
	
	function getDeletedStukjes($condition, $err)
		{
		$query = "
		SELECT o.*, CHAR_LENGTH(o.tekst) AS lengte 
		FROM `bin` o
		LEFT JOIN `bin` b
		ON o.stukje = b.stukje AND o.version < b.version
		WHERE b.version is NULL";
		if (isset($condition['cat']))
			{
			$query .= " AND o.categorie='" . $condition['cat'] . "'";
			}
		$array = array();
		$result = $this->db->query($query);
		
		if ($result) {
			while ($stukje = $result->fetch_assoc()) {
				$array[$stukje['stukje']] = $stukje;
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het ophalen van de verwijderde stukjes. Controleer de query: ");
			$err->throwError($query);
		}
		
		
		return $array;
		}
	
	function getDrafts($condition, $err) {
		$query = "SELECT * FROM drafts";
		
		$array = array();
		$result = $this->db->query($query);
		
		if ($result) {
			while ($draft = $result->fetch_assoc()) {
			$array[$draft['id']] = $draft;
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het ophalen van de drafts. Controleer de query: ");
			$err->throwError($query);
		}
		
		return $array;
	}
	
	function getDraft($stukje, $err) {
		$query = "SELECT *, CHAR_LENGTH(drafts.tekst) AS lengte FROM drafts WHERE id=" . $stukje;

		$result = $this->db->query($query);

		if ($result) {
			return $result->fetch_assoc();
		} else {
			$err->throwError("Er is iets misgegaan bij het ophalen van de draft. Controleer de draft: ");
			$err->throwError($query);
			return array();
		}
	}
	
	function numStukjes($err)
		{
		$query = "SELECT MAX(stukje) FROM stukjes";
		$result = $this->db->query($query);
		if ($result) {
			$result = $result->fetch_assoc();
			return (int) $result['MAX(stukje)'];
		} else {
			$err->throwError("Er is iets misgegaan bij het tellen van de stukjes. Controleer de query: ");
			$err->throwError($query);
		}
	}

	function numStukjesGeplaatst($err)
		{
		$query = "SELECT MAX(stukje) FROM geplaatst";
		$result = $this->db->query($query);
		if ($result) {
			$result = $result->fetch_assoc();
			return (int) $result['MAX(stukje)'];
		} else {
			$err->throwError("Er is iets misgegaan bij het tellen van de geplaatste stukjes. Controleer de query: ");
			$err->throwError($query);
		}
	}

	function numStukjesVerwijderd($err)
		{
		$query = "SELECT MAX(stukje) FROM bin";
		$result = $this->db->query($query);
		if ($result) {
			$result = $result->fetch_assoc();
			return (int) $result['MAX(stukje)'];
		} else {
			$err->throwError("Er is iets misgegaan bij het tellen van de verwijderde stukjes. Controleer de query: ");
			$err->throwError($query);
		}
	}
	
	function replaceVersion($stukje, $version, $err)
		{
		$new_version = $this->getVersion($stukje, $err) + 1;
		$query = "INSERT INTO stukjes (stukje, categorie, version, type, titel, user, tekst, klaar) 
				SELECT stukje, categorie, " . $new_version . ", 'Herplaatst', titel, user, tekst, klaar FROM stukjes
				WHERE stukje=" . $stukje . " AND version=" . $version;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
		}
	}


	function delStukje($stukje, $err) {
		$id = $this->numStukjesVerwijderd($err)+1;

		$query = "SELECT DISTINCT(stukje) FROM stukjes WHERE stukje=" . $stukje;
		$result = $this->db->query($query);
		if (!$result) {
			$err->throwError("Er is iets misgegaan bij het verwijderen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		if ($result->num_rows == 0) {
			$err->throwWarning("Het te verwijderen stukje bestaat niet.");
			return;
		}

		$query = "INSERT INTO bin (stukje, categorie, version, type, timestamp, titel, user, tekst, klaar) SELECT " . $id . ", categorie, version, type, timestamp, titel, user, tekst, klaar FROM stukjes WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het verwijderen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$query = "DELETE FROM stukjes WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het verwijderen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$err->throwMessage("Stukje verplaatst naar prullenbak.");
	}
	

	function undoDelStukje($stukje, $err) {
		$id = $this->numStukjes($err)+1;

		$query = "SELECT DISTINCT(stukje) FROM bin WHERE stukje=" . $stukje;
		$result = $this->db->query($query);
		if (!$result) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		if ($result->num_rows == 0) {
			$err->throwWarning("Het terug te plaatsen stukje bestaat niet.");
			return;
		}

		$query = "INSERT INTO stukjes (stukje, categorie, version, type, timestamp, titel, user, tekst, klaar) SELECT " . $id . ", categorie, version, type, timestamp, titel, user, tekst, klaar FROM bin WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$query = "DELETE FROM bin WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$err->throwMessage("Stukje teruggeplaatst.");
	}
	

	function plaatsStukje($stukje, $err) {
		$id = $this->numStukjesGeplaatst($err)+1;

		$query = "SELECT DISTINCT(stukje) FROM stukjes WHERE stukje=" . $stukje;
		$result = $this->db->query($query);
		if (!$result) {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		if ($result->num_rows == 0) {
			$err->throwWarning("Het te plaatsen stukje bestaat niet.");
			return;
		}

		$query = "INSERT INTO geplaatst (stukje, categorie, version, type, timestamp, titel, user, tekst, klaar) SELECT " . $id . ", categorie, version, type, timestamp, titel, user, tekst, klaar FROM stukjes WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$query = "DELETE FROM stukjes WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het plaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$err->throwMessage("Stukje geplaatst.");
	}
	
	function undoPlaatsStukje($stukje, $err) {
		$id = $this->numStukjes($err)+1;

		$query = "SELECT DISTINCT(stukje) FROM geplaatst WHERE stukje=" . $stukje;
		$result = $this->db->query($query);
		if (!$result) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		if ($result->num_rows == 0) {
			$err->throwWarning("Het terug te plaatsen stukje bestaat niet.");
			return;
		}

		$query = "INSERT INTO stukjes (stukje, categorie, version, type, timestamp, titel, user, tekst, klaar) SELECT " . $id . ", categorie, version, type, timestamp, titel, user, tekst, klaar FROM geplaatst WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$query = "DELETE FROM geplaatst WHERE stukje=" . $stukje;
		if (!$this->db->query($query)) {
			$err->throwError("Er is iets misgegaan bij het terugplaatsen van het stukje. Controleer de query: ");
			$err->throwError($query);
			return;
		}

		$err->throwMessage("Stukje teruggeplaatst.");
	}
	
	function getGeplaatsteStukjes($condition, $err)
		{
		$query = "
		SELECT o.*, CHAR_LENGTH(o.tekst) AS lengte 
		FROM `geplaatst` o
		LEFT JOIN `geplaatst` b
		ON o.stukje = b.stukje AND o.version < b.version
		WHERE b.version is NULL";
		if (isset($condition['cat']))
			{
			$query .= " AND o.categorie='" . $condition['cat'] . "'";
			}
		$array = array();
		$result = $this->db->query($query);
		
		if ($result) {
			while ($stukje = $result->fetch_assoc()) {
				$array[$stukje['stukje']] = $stukje;
			}
		} else {
			$err->throwError("Er is is iets misgegaan bij het ophalen van de geplaatste stukjes. Controleer de query: ");
			$err->throwError($query);
		}
		
		return $array;
		}
	
	function numChecks($stukje, $err) {
		$query = "SELECT * FROM stukjes WHERE stukje=" . $stukje . " ORDER BY version DESC";
		$result = $this->db->query($query);
		$checks = array();
		
		if ($result) {
			do {
			$succes = ($row = $result->fetch_assoc());
			if ($row['type'] == 'Nagekeken')
				{
				if (!in_array($row['user'], $checks))
					$checks[] = $row['user'];
				}
			} while ($succes && $row['type'] == 'Nagekeken');
			return count($checks);
		} else {
			$err->throwError("Er is iets misgegaan bij het tellen van de checks. Controleer de query: ");
			$err->throwError($query);
		}
	}
	
	function getChecks($stukje, $err) {
		$query = "SELECT * FROM stukjes WHERE stukje=" . $stukje . " ORDER BY version DESC";
		$result = $this->db->query($query);
		if (!$result) {
			$err->throwError("Er is iets misgegaan bij het binnenhalen van de checks. Controleer de query: ");
			$err->throwError($query);
			return;
		}
		$checks = array();
		
		do {
			$succes = ($row = $result->fetch_assoc());
			if ($row['type'] == 'Nagekeken')
				{
				if (!in_array($row['user'], $checks))
					$checks[] = $row['user'];
				}
		} while ($succes && $row['type'] == 'Nagekeken');
		
		return $checks;
	}
}
?>
