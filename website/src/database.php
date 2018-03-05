<?php 

class Database
{
    protected static $connection;

    public function connect()
    {    
        if(!isset(self::$connection))
        {
            $config = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/config/database.ini');
            self::$connection = new mysqli($config['servername'],$config['username'],$config['password'],$config['dbname']);

            if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
        }

        if(self::$connection === false)
        {
            return false;
        }

        return self::$connection;
    }

    public function query($query)
    {
        $connection = $this -> connect();

        $result = $connection -> query($query);
        if (!$result)
        {
            echo "<p>There was an error in query: $query</p>";
            echo $connection->error;
        }

        return $result;
    }

    public function select($query)
    {
        $rows = array();
        $result = $this -> query($query);
        if($result === false)
        {
            return false;
        }
        while ($row = $result -> fetch_assoc())
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