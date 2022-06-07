<?php
Class Users {
	private $db;
	
	function __construct(Mysqli $db)
		{
		$this->db = $db;
		}
	
	function getUsers($level, $err)
		{
		$level = intval($level);
		$query = "SELECT * FROM users ORDER BY username ASC";
		if (isset($level)) {
			$query = "SELECT * FROM users WHERE perm_level >= " . $level . " ORDER BY username ASC";
			}
		$array = array();
		$result = $this->db->query($query);
		
		if ($result) {
			while ($user = $result->fetch_assoc()) {
				$array[$user['id']] = $user;
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het binnenhalen van de gebruikers. Controleer de query: ");
			$err->throwError($query);
		}
		
		return $array;
		}
		
	function getUser($id, $err) {
		$id = intval($id);
		$query = "SELECT * FROM users WHERE id=" . $id;
		$result = $this->db->query($query);
		
		if ($result) {
			if ($result->num_rows) {
				return $result->fetch_assoc();
			} else {
				$err->throwWarning("Gebruiker " . $id . " niet gevonden.");
			}
		} else {
			$err->throwError("Er is iets misgegaan. Controleer de query: ");
			$err->throwError($query);
		}
		return array();
	}
	
	function addUser($name, $perm_level, $err) {
		$name = $this->db->real_escape_string($name);
		$perm_level = intval($perm_level);

		$query = "INSERT INTO users (username, perm_level) VALUES ('" . $name . "', " . $perm_level . ")";
		if ($this->db->query($query)) {
			$err->throwMessage("Gebruiker toegevoegd.");
		} else {
			$err->throwError("Er is iets misgegaan bij het toevoegen van de gebruiker. Controleer de query: ");
			$err->throwError($query);
		}
	}
	
	function delUser($id, $err) {
		$query = "DELETE FROM users WHERE id=" . $id;
		$result = $this->db->query($query);

		if ($result) {
			if ($this->db->affected_rows > 0) {
				$err->throwMessage("Gebruiker verwijderd.");
			} else {
				$err->throwWarning("De te verwijderen gebruiker bestaat niet (meer).");
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het verwijderen van de gebruiker. Controleer de query: ");
			$err->throwError($query);
		}
	}

}
?>