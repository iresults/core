<?php
namespace Iresults\Core\Helpers;

/*
 * The MIT License (MIT)
 * Copyright (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
 * SOFTWARE.
 */


/**
 * The iresults search helper provides some functions to search repositories.
 *
 * @package Iresults
 * @subpackage Iresults_Helpers
 * @version 1.5
 */
class SearchHelper extends \Iresults\Core\Model {
	/**
	 * Search mode for a flexible search.
	 */
	const SEARCH_MODE_FLEXIBLE = 'SEARCH_MODE_FLEXIBLE';

	/**
	 * Search mode for a strict search.
	 */
	const SEARCH_MODE_STRICT = 'SEARCH_MODE_STRICT';

	/**
	 * @var array An array of fields the value is searched in.
	 */
	protected $searchFields = array();

	/**
	 * @var mixed The value to search.
	 */
	protected $searchValue = NULL;

	/**
	 * @var string The mode for the search.
	 */
	protected $searchMode = self::SEARCH_MODE_FLEXIBLE;

	/**
	 * @var Tx_Extbase_Persistence_Query The query object.
	 */
	protected $query = NULL;

	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage The filters to apply to the
	 * search.
	 */
	protected $filters;


	/**
	 * The repository to search.
	 *
	 * @var Tx_Extbase_Persistence_Repository
	 */
	protected $repository = NULL;

	/**
	 * The constructor
	 *
	 * @param	array   $parameters	 Optional parameters to pass to the constructor
	 * @return	Iresults_Model
	 */
	public function __construct(array $parameters = array()) {
		parent::__construct($parameters);

		$this->setPropertiesFromArray($parameters);

		if (!$this->filters) {
			$this->filters = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
		}

		return $this;
	}

	/**
	 * Searches the repositorie's given fields for the specified value.
	 *
	 * @param	Tx_Extbase_Persistence_Repository	$repository	The repository to search
	 *
	 * @param	mixed	$searchValue	The value to search for
	 * @param	array 	$searchFields	The fields to search in
	 * @param	string 	$mode			The search mode
	 * @return	Tx_Extbase_Persistence_QueryResultInterface|array The query
	 * result object.
	 */
	public function searchRepository(Tx_Extbase_Persistence_Repository $repository = NULL, $searchValue = "", $searchFields = NULL, $mode = NULL) {
		if (!is_null($repository)) {
			$this->setRepository($repository);
		}

		if ($searchValue) {
			$this->searchValue = $this->_prepareSearchValue($searchValue);
		}
		if ($searchFields) {
			if (!is_array($searchFields)) {
				if ($searchFields instanceof Traversable) {
					$searchFields = iterator_to_array($searchFields);
				} else {
					$searchFields = array($searchFields);
				}
			}
			$this->searchFields = $searchFields;
		}
		if ($mode) {
			$this->searchMode = $mode;
		}

		if (!$this->checkSettings()) return array();

		$this->createQuery();
		$this->buildQuery();

		return $this->query->execute();
	}

	/**
	 * Prepares the search value.
	 *
	 * If the dbal extension is used single quotes (') will be replaced.
	 *
	 * @param	string	$searchValue The search value
	 * @return	string    The returned search value
	 */
	protected function _prepareSearchValue($searchValue) {
		/*
		 * Apply dbal bugfixes.
		 * http://forge.typo3.org/issues/27858
		 */
		if (t3lib_extMgm::isLoaded('dbal')) {
			if (is_string($searchValue)) {
				$replace = array("\\'","'");
				$searchValue = str_replace($replace,"_",$searchValue);
			}
		}

		return $searchValue;
	}

	/**
	 * Creates a new query object if it doesn't exist.
	 *
	 * @return	Tx_Extbase_Persistence_Query    Returns the query
	 */
	public function createQuery() {
		if (!$this->query) {
			$this->query = $this->getRepository()->createQuery();
		}
		return $this->query;
	}

	/**
	 * Checks if all needed data has been given.
	 *
	 * @return	boolean    TRUE on success, FALSE on error
	 */
	public function checkSettings() {
		if (!$this->searchValue) {
			throw new Exception("No search value specified.");
			return FALSE;
		}
		if (!$this->searchFields || empty($this->searchFields)) {
			throw new Exception("No search fields specified.");
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Loops through all the fields and adds the conditions, combined with OR,
	 * to the query.
	 *
	 * @return	void
	 */
	public function buildQuery() {
		$queryParts = array();


		foreach ($this->searchFields as $field) {
			if ($this->searchMode == self::SEARCH_MODE_STRICT) {
				$queryParts[] = $this->query->equals($field, $this->searchValue);
			} else {
				$preparedValue = $this->getPreparedSearchValue();
				$constraint = $this->query->like($field, $preparedValue);
				$queryParts[] = $constraint;
			}
		}

		if (!empty($queryParts)) {
			$matching = $this->query->logicalOr($queryParts);
			$this->query->matching($matching);

			$this->_applyFilters($matching);

		} else {
			throw new Exception("The query parts are empty.", 1313763574);
		}
	}

	/**
	 * Returns the search value prepared for a flexible search.
	 *
	 * @return	string    The prepared value
	 */
	public function getPreparedSearchValue() {
		$returnValue = NULL;
		if (is_int($this->searchValue) && is_float($this->searchValue)) {
			$returnValue = $this->searchValue;
		} else if (is_array($this->searchValue)) {
			$returnValue = "'".implode("','",$this->searchValue)."'";
		} else {
			$returnValue = "%".$this->searchValue."%";
		}
		return $returnValue;
	}

	/**
	 * Applies the all filters to the query.
	 *
	 * @param	Tx_Extbase_Persistence_QOM_ConstraintInterface	$searchMatching The
	 * constraints built from the search.
	 *
	 * @return	void
	 */
	protected function _applyFilters($searchMatching) {
		if (!$this->filters || !$this->filters->count()) return;

		foreach ($this->filters as $filter) {
			$searchMatching = $this->_applyFilter($filter, $searchMatching);
		}
	}

	/**
	 * Applies the given filter's conditions to the query.
	 *
	 * @param	Search\FilterHelper	$filter The filter to
	 * apply.
	 *
	 * @param	Tx_Extbase_Persistence_QOM_ConstraintInterface	$searchMatching The
	 * constraints built from the search.
	 *
	 * @return	void
	 */
	protected function _applyFilter($filter, $searchMatching) {
		$allConditions = $filter->getConditions();
		$queryParts = array();

		#$queryParts[] = $searchMatching;

		foreach ($allConditions as $condition) {
			/**
			 * If the condition is a comparison just add it.
			 */
			if (is_object($condition) && $condition instanceof Tx_Extbase_Persistence_QOM_ComparisonInterface) {
				$queryParts[] = $condition;
				continue;
			}
			/*
			 * The condition is expected to be either an array or an object with
			 * values for the keys "key" and "value".
			 * If a dictionary is passed without those two elements the current
			 * dicionary key and value will be used.
			 */
			if (is_array($condition) && !empty($condition) && !isset($condition['key'])) {
				$originalCondition = $condition;
				$condition = array(
					'key' => key($originalCondition),
					'value' => current($originalCondition)
				);
			}

			/*
			 * Check if the condition is a string.
			 */
			if (!is_array($condition) && !($condition instanceof ArrayAccess)) {
				throw new Exception("A bad condition of type ".gettype($condition)." was given.",1314112780);

				//$concreteCondition = $condition->getConcreteCondition();
				//$oldStatement = $this->query->getStatement();
				//$newStatemnet = $oldStatement . " AND " . $concreteCondition;
				//
				//$this->query->statement($newStatemnet);
				//#$queryParts[] = $constraint;
				//$GLOBALS['TYPO3_DB']->debugOutput = TRUE;
			/*
			 * Check if the value is an array.
			 */
			} else if (is_array($condition['value'])) {
				$queryParts[] = $this->query->in($condition['key'],$condition['value']);
			/*
			 * Handle normal conditions.
			 */
			} else if ($this->searchMode == self::SEARCH_MODE_STRICT) {
				$queryParts[] = $this->query->equals($condition['key'], $condition['value']);
			} else {
				$preparedValue = $this->getPreparedSearchValue();
				$queryParts[] = $this->query->like($condition['key'], $condition['value']);
			}
		}



		if (!empty($queryParts)) {
			if ($filter->getExpression() == Search\AbstractFilter::EXPRESSION_OR) {
				$queryParts = $this->query->logicalOr($queryParts);

				//\Iresults\Core\Iresults::pd($queryParts,$filter->getConditions());

				$queryParts = array($searchMatching,$queryParts);
			} else if ($filter->getExpression() == Search\AbstractFilter::EXPRESSION_AND) {
				array_unshift($queryParts,$searchMatching);
			} else {
				throw new Exception("Bad filter expression ".$filter->getExpression());
			}



			$matching = $this->query->logicalAnd($queryParts);
			$this->query->matching($matching);
			return $matching;
		/*
		 * If query parts is empty and the all conditions is empty there is
		 * nothing to add.
		 */
		} else if (empty($allConditions)) {
			$this->query->matching($searchMatching);
			return $searchMatching;
		/*
		 * If query parts is empty but the filter has conditions there has been
		 * an error.
		 */
		} else {
			throw new Exception("The query parts are empty for applying the filter conditions, of filter ".get_class($filter).".", 1314004762);
		}
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MANAGING THE FILTER                   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Sets the filters to apply.
	 *
	 * @param	Tx_Extbase_Persistence_ObjectStorage<Search\FilterHelper> $filters The filters to
	 * apply to the search.
	 *
	 * @return	void
	 */
	public function setFilters($filters) {
		$this->removeAllFilters();
		foreach ($filters as $filter) {
			$this->addFilter($filter);
		}
	}


	/**
	 * Returns the filter to apply to the search.
	 *
	 * @return	Tx_Extbase_Persistence_ObjectStorage<Search\FilterHelper>
	 * Returns the filter if one is set.
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * Adds a filter to apply to the search.
	 *
	 * @param	Search\FilterHelper	$filter The filter to
	 * apply to the search.
	 *
	 * @return	void
	 */
	public function addFilter($filter) {
		$this->filters->attach($filter);
		$filter->_setSearchHelper($this);
	}

	/**
	 * Removes a filter from the search.
	 *
	 * @param	Search\FilterHelper	$filter The filter to
	 * remove.
	 *
	 * @return	void
	 */
	public function removeFilter($filter) {
		$this->filters->detach($filter);
	}

	/**
	 * Removes all filters from the search.
	 *
	 * @return	void
	 */
	public function removeAllFilters() {
		unset($this->filters);
		$this->filters = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
	}



	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MANAGING THE REPOSITORY               MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the repository.
	 *
	 * @return	Tx_Extbase_Persistence_Repository
	 */
	public function getRepository() {
		if (!$this->repository) {
			throw new Exception("No repository set.",1314116478);
		}
		return $this->repository;
	}

	/**
	 * Sets the repository.
	 *
	 * @param	Tx_Extbase_Persistence_Repository	$repo The new repository
	 * @return	void
	 */
	public function setRepository($repo) {
		$this->repository = $repo;
	}
}












