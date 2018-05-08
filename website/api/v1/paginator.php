<?php
 
class Paginator
{
	private $database;
	private $maxNofItemsOnPage;
	private $currentPageIndex;
	private $query;
	private $nofItems;
	private $startIndex;

	public function __construct( $database, $query ) 
	{
		$this->database = $database;
		$this->query = $query;

		$queryResult = $this->database->select( $this->query );
		$this->nofItems = sizeof($queryResult);
	}

	public function getData( $maxNofItems = 10, $currentPageIndex = 1 )
	{
		$this->maxNofItemsOnPage = $maxNofItems;
		$this->currentPageIndex = $currentPageIndex;

		if ( $this->maxNofItemsOnPage == 'all' )
		{
			$query = $this->query;
		}
		else
		{
			$this->startIndex = (($this->currentPageIndex - 1 ) * $this->maxNofItemsOnPage);
			$query = $this->query . " LIMIT {$this->startIndex}, $this->maxNofItemsOnPage";
		}
		
		$queryResult = $this->database->query($query) or die($this->database->error());

		while($row = $queryResult->fetch_assoc())
		{
			$results[] = $row; 
		}

		$result = new stdClass();
		$result->currentPageIndex = $this->currentPageIndex;
		$result->maxNofItems = $this->maxNofItemsOnPage;
		$result->nofItems = $this->nofItems;
		$result->data = $results;

		return $result;
	}
}
?>