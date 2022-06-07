<?php
Class Categories {
	private $db;
	
	function __construct(Mysqli $db)
		{
		$this->db = $db;
		}
	
	function getCats($err) {
		$query = "SELECT * FROM categories";
		$array = array();
		$result = $this->db->query($query);
		
		if ($result) {
			while ($cat = $result->fetch_assoc()) {
			$array[$cat['id']] = $cat;
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het binnenhalen van de categorieën. Controleer de query: ");
			$err->throwError($query);
		}
		return $array;
	}
		
	function getCat($id, $err) {
		$id = intval($id);
		$query = "SELECT * FROM categories WHERE id=" . $id;
		$result = $this->db->query($query);

		if ($result) {
			if ($result->num_rows) {
				return $result->fetch_assoc();
			} else {
				$err->throwWarning("Categorie " . $id . " niet gevonden.");
			}
		} else {
			$err->throwError("Er is iets misgegaan. Controleer de query: ");
			$err->throwError($query);
		}
		return array();
	}
	
	function getCatValue($id, $value, $err) {
		$cat = $this->getCat($id, $err);
		return $cat[$value];
	}
	
	function numCats($err) {
		$query = "SELECT COUNT(*) as count FROM `categories`";
		$result = $this->db->query($query);
		if ($result) {
			$result = $result->fetch_assoc();
			return intval($result['count']);
		} else {
			$err->throwError("Er is iets misgegaan. Controleer de query: ");
			$err->throwError($query);
		}
		return 0;
	}
	
	function addCat($name, $descr, $err) {
		$name = $this->db->real_escape_string($name);
		$descr = $this->db->real_escape_string($descr);

		$queryText = "INSERT INTO categories (name, description) VALUES ('" . $name . "', '" . $descr . "')";
		$query = $this->db->query($queryText);
		if ($query) {
			$err->throwMessage("Categorie '".$name."' toegevoegd.");
		} else {
			$err->throwError("Er is iets misgegaan bij het toevoegen van de categorie. Controleer de query: ");
			$err->throwError($queryText);
		}
	}
	
	function delCat($id, $err) {
		$id = intval($id);

		if ($id == 1) {
			$err->throwError("De 'geen categorie' categorie kan niet verwijderd worden.");
			return;
		}

		foreach (array('bin', 'drafts', 'geplaatst', 'stukjes') as $table) {
			$queryText = "UPDATE ".$table." SET categorie=1 WHERE categorie=" . $id;
			$query = $this->db->query($queryText);
			if (!$query) {
				$err->throwError("Er is iets misgegaan bij het verwijderen van de categorie. Controleer de query: ");
				$err->throwError($queryText);
				return;
			}
		}

		$queryText = "DELETE FROM categories WHERE id=" . $id;
		$query = $this->db->query($queryText);
		if ($query) {
			if ($this->db->affected_rows > 0) {
				$err->throwMessage("Categorie verwijderd.");
			} else {
				$err->throwWarning("De te verwijderen categorie bestaat niet (meer).");
			}
		} else {
			$err->throwError("Er is iets misgegaan bij het verwijderen van de categorie. Controleer de query: ");
			$err->throwError($queryText);
		}
	}
}

?>