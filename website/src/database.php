<?php 

class Database
{
	protected static $pdo = null;

	public function connect()
	{
		if(!isset(self::$pdo))
		{
			try
			{
				$config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config/database.ini');
				self::$pdo = new PDO('mysql:host=' . $config['servername'] . ';dbname=' . $config['dbname'] . ';charset=utf8mb4', $config['username'], $config['password']);
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch (PDOException $e)
			{
				self::$pdo = null;
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		}

		return self::$pdo;
	}

	private function query(string $statement, array $inputParameters)
	{
		$pdo = $this -> connect();

		$stmt = $pdo->prepare($statement);
		$stmt->execute($inputParameters);
		return $stmt;
	}

	public function select(string $statement, array $inputParameters)
	{
		$rows = array();
		$result = $this -> query($statement, $inputParameters);
		if($result === false)
		{
			return false;
		}
		foreach ($result as $row)
		{
			$rows[] = $row; 
		}
		return $rows;
	}

	public function error()
	{
		$connection = $this -> connect();
		return $connection -> error;
	}
}

?>