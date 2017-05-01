<?php
require_once 'utils/databaseConnect.php';
require_once 'models/path.php';

class Pin
{
	public $id;
	public $name;
	public $posX;
	public $posY;
	public $mapId;
	public $editorId;

	public static function GetById($id)
	{
		$pin = new Pin;
		$dbconn = new DatabaseConnect;
		$stmt = $dbconn->prepare("select * from pins where id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();

		$stmt->bind_result($pin->id, $pin->name, $pin->posX, $pin->posY, $pin->mapId, $pin->editorId);

		if ($stmt->fetch())
		{
			return $pin;
		}
		else 
		{
			return null;
		}
	}

	public function getPaths()
	{
		$paths = array();
		$path = new path;
		$dbconn = new DatabaseConnect;
		$stmt = $dbconn->prepare("select * from paths where first_pin_id = ? or second_pin_id = ?");
		$stmt->bind_param("ii", $this->id, $this->id);
		$stmt->execute();

		$stmt->bind_result($path->id, $path->firstPinId, $path->secondPinId, $path->editorId);

		while ($stmt->fetch())
		{
			array_push($paths, $path);
		}

		return $paths;
	}

	public function update()
	{
		$dbconn = new DatabaseConnect;

		if ($this->id === null)
		{	
			$stmt = $dbconn->prepare("insert into pins (name, pos_x, pos_y, map_id, editor_id)
				values (?, ?, ?, ?, ?)");
			$stmt->bind_param("sddii", $this->name, $this->posX, $this->posY, $this->mapId,
				$this->editor_id);

			return $stmt->execute();
		}
		else
		{
			$stmt = $dbconn->prepare("update maps set name = ?, pos_x = ?, pos_y = ?,
				map_id = ?, editor_id = ? where id = ?");
			$stmt->bind_param("sddii", $this->name, $this->posX, $this->posY, $this->mapId,
				$this->editor_id, $this->id);
			return $stmt->execute();
		}
	}
}

?>