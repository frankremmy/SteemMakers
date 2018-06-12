<?php
 
class Paginator
{
	private $database;
	private $maxNofItemsOnPage;
	private $currentPageIndex;
	private $queryStatement;
	private $nofItems = 100;
	private $startIndex;

	public function __construct( $database, $queryStatement ) 
	{
		$this->database = $database;
		$this->queryStatement = $queryStatement;

		$queryResult = $this->database->select($this->queryStatement, []);
		$this->nofItems = sizeof($queryResult);
	}

	public function getData( $maxNofItems = 10, $currentPageIndex = 1 )
	{
		$this->maxNofItemsOnPage = $maxNofItems;
		$this->currentPageIndex = $currentPageIndex;
		$compiledStatement = '';
		$queryInputParameters = [];

		if ( $this->maxNofItemsOnPage == 'all' )
		{
			$compiledStatement = $this->queryStatement;
			$queryInputParameters = [];
		}
		else
		{
			$this->startIndex = (($this->currentPageIndex - 1 ) * $this->maxNofItemsOnPage);

			$compiledStatement = $this->queryStatement . " LIMIT :startindex , :maxnofitems";
			$queryInputParameters = array('startindex' => $this->startIndex, 'maxnofitems' => $this->maxNofItemsOnPage);
		}
		
		$queryResult = $this->database->select($compiledStatement, $queryInputParameters) or die($this->database->error());

		$result = new stdClass();
		$result->currentPageIndex = $this->currentPageIndex;
		$result->maxNofItems = $this->maxNofItemsOnPage;
		$result->nofItems = $this->nofItems;
		$result->data = $queryResult;

		return $result;
	}
}
?>