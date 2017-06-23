<?php

namespace Iresults\Core\Model;

use Iresults\Core\Core;
use Iresults\Core\Mutable;
use Iresults\Core\Ui\Table;
use Traversable;


/**
 * A iresults data grid is an object which stores tables of data. It provides
 * abilities to add rows or columns, access cell values and change them.
 *
 * @author        Daniel Corn <cod@iresults.li>
 * @package       Iresults
 * @subpackage    Iresults_Model
 */
class DataGrid extends Core implements \Iterator, \ArrayAccess
{
    /**
     * The dictionary holding the data map.
     *
     * @var array<array<mixed>>
     */
    protected $data = [];

    /**
     * The dictionary holding the data map with all missing fields filled.
     *
     * @var array<array<mixed>>
     */
    protected $filledData = [];

    /**
     * The total number of rows in the grid.
     *
     * @var integer
     */
    protected $rowCount = 0;

    /**
     * The total number of columns in the grid.
     *
     * @var integer
     */
    protected $columnCount = 0;

    /**
     * Indicates if the data has changed. If TRUE the filledData property has to be rebuilt.
     *
     * @var boolean
     */
    protected $isDirty = true;

    /**
     * The value missing elements are filled with.
     *
     * @var mixed
     */
    protected $_missingElementPlaceholder = '';

    /**
     * If this property is set elements that exist but are empty are replaced with this placeholder.
     *
     * @var string
     */
    protected $_emptyElementPlaceholder = null;

    /**
     * The current depth in the filling routine.
     *
     * @var integer
     */
    protected $_fillMissingArrayElementsDepth = 0;

    /**
     * The last search's value to look for
     *
     * @var mixed
     */
    protected $lastSearchValue = null;

    /**
     * The last search's result
     *
     * @var object
     */
    protected $lastSearchResult = null;

    /**
     * Indicates if the last search was strict
     *
     * @var boolean
     */
    protected $lastSearchStrict = false;

    /**
     * Indexed Grid data
     *
     * @var array
     */
    protected $_indexes = [];

    /**
     * Array of columns to index
     *
     * @var array
     */
    protected $_indexColumns = [];

    /**
     * Defines if the data indexes are dirty and need to be rebuilt
     *
     * @var bool
     */
    protected $_indexIsDirty = false;


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* INITIALIZATION    MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * @param array $parameters Optional parameters to pass to the constructor
     */
    public function __construct(array $parameters = [])
    {
        //parent::__construct($parameters);
        $this->data = $parameters;

        $this->rowCount = $this->_getLengthOfElement($this->data);
        $this->columnCount = $this->_getLengthOfLongestElementOfInput($this->data);

        return $this;
    }

    /**
     * Initializes the object with the contents from the file at the given URL
     *
     * @param string $url The URL to the file to load data from
     * @return $this
     */
    public function initWithContentsOfUrl($url)
    {
        if (strtolower(substr($url, -4)) == '.csv') {
            $this->initWithContentsOfCSVFile($url);
        }

        return $this;
    }

    /**
     * @see initWithContentsOfUrl()
     */
    public function initWithContentsOfFile($filePath)
    {
        return $this->initWithContentsOfUrl($filePath);
    }

    /**
     * Initializes the object with the contents from the CSV file at the given
     * path.
     *
     * @param string $filePath  The path to the file to load data from
     * @param string $delimiter ," The CSV field delimiter
     * @param string $enclosure The CSV field enclosure character
     * @param string $escape    \" The CSV files escape character
     * @return    \Iresults\Core\Model\DataGrid    Returns the initialized object
     */
    public function initWithContentsOfCSVFile($filePath, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $csvParser = new \Iresults\Core\Parser\CsvFileParser();
        $csvParserConfiguration = [];
        $numberOfArguments = func_num_args();
        switch (true) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case $numberOfArguments > 3:
                $csvParserConfiguration['escape'] = $escape;
            /** @noinspection PhpMissingBreakStatementInspection */
            case $numberOfArguments > 2:
                $csvParserConfiguration['delimiter'] = $delimiter;
            case $numberOfArguments > 1:
                $csvParserConfiguration['enclosure'] = $enclosure;
            default;
        }
        $csvParser->setConfiguration($csvParserConfiguration);

        $this->data = $csvParser->parse($filePath);
        $this->_markAsDirty();

        return $this;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS NORMAL DATA        MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the total number of rows in the grid.
     *
     * @return    integer
     */
    public function getRowCount()
    {
        $this->_repairIfDirty();

        return $this->rowCount;
    }

    /**
     * Returns the total number of columns in the grid.
     *
     * @return    integer
     */
    public function getColumnCount()
    {
        $this->_repairIfDirty();

        return $this->columnCount;
    }

    /**
     * Returns the data of the grid.
     *
     * Missing fields inside this array are NOT filled.
     *
     * @return    array<array<mixed>>
     */
    public function getGridData()
    {
        return $this->data;
    }

    /**
     * Returns the raw data of the grid.
     *
     * Missing fields inside this array are NOT filled.
     *
     * @return    array<array<mixed>>
     */
    public function getRawGridData()
    {
        return $this->data;
    }

    /**
     * Returns the data of the grid.
     *
     * Missing fields inside this array are NOT filled.
     *
     * @return    array<array<mixed>>
     */
    public function toArray()
    {
        return $this->getGridData();
    }

    /**
     * Returns the data of the grid with all missing fields filled.
     *
     * @return    array<array<mixed>>
     */
    public function getGrid()
    {
        $this->_repairIfDirty();

        return $this->filledData;
    }

    /**
     * Returns the data of the grid with all missing fields filled.
     *
     * @return    array<array<mixed>>
     */
    public function getFilledGridData()
    {
        $this->_repairIfDirty();

        return $this->filledData;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS CELLS         WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the value of the cell in row $row and column $column.
     *
     * @param integer $row    The index of the row
     * @param integer $column The index of the column
     * @return    mixed    The value of the cell, or FALSE if the coordinates are beyond the gounds of the grid
     */
    public function getCellInRowAndColumn($row, $column)
    {
        $filledGrid =& $this->getFilledGridData();

        if (!isset($filledGrid[$row])) {
            return false;
        }
        if (!isset($filledGrid[$row][$column])) {
            return false;
        }

        return $filledGrid[$row][$column];
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FINDING VALUES       WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Searches the given value in the grid and returns an array of objects
     * with the properties row and column, for the cells the value was found in.
     *
     * @param mixed   $value         The value to search for
     * @param boolean $strictCompare Defines if strict comparison should be used
     * @return    array<object>
     */
    public function findValue($value, $strictCompare = false)
    {
        // Reuse the last search
        if (!$this->isDirty && $this->lastSearchResult
            && $this->lastSearchValue === $value
            && (!$strictCompare || $this->lastSearchStrict === true)
        ) {
            return $this->lastSearchResult;
        }

        // Check the indexes
        $indexedObjectPoint = $this->getIndexedDataForValue($value, true);
        if ($indexedObjectPoint) {
            return [$indexedObjectPoint];
        }


        $filledGrid = $this->getFilledGridData();
        $foundCells = [];

        $currentRowIndex = 0;
        while (($currentRow = current($filledGrid)) !== false) {
            foreach ($currentRow as $currentColumnIndex => $cell) {
                if ($cell === $value || ($strictCompare === false && $cell == $value)) {
                    $point = new \stdClass();
                    $point->row = $currentRowIndex;
                    $point->column = $currentColumnIndex;
                    $foundCells[] = $point;
                }
            }
            next($filledGrid);
            $currentRowIndex++;
        }

        $this->lastSearchValue = $value;
        $this->lastSearchResult = $foundCells;
        $this->lastSearchStrict = $strictCompare;

        return $foundCells;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DATA INDEX           WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Adds the given column to the Data Index
     *
     * The Data Index will be queried by findValue() which speeds up the method
     * significantly for huge Data Grids and recurring calls to findValue()
     *
     * Information: The Data Index(es) will be built on the fly
     *
     * Example:
     *
     *  $grid->addIndexForColumn(0); // Add a Data Index for the first column
     *  $searched = array('Daniel', 'Peter', 'Andreas', ...);
     *  foreach ($searched as $search) {
     *    $grid->findValue($search);
     *  }
     *
     * @param integer $column Key of the column
     */
    public function addIndexForColumn($column)
    {
        $this->_indexColumns[$column] = true;
        $this->_indexIsDirty = true;
    }

    /**
     * Rebuilds the data index
     */
    protected function _rebuildIndex()
    {
        $indexedColumns = array_keys($this->_indexColumns);
        foreach ($indexedColumns as $columnIndexToRebuild) {
            $this->_rebuildIndexOfColumn($columnIndexToRebuild);
        }
        $this->_indexIsDirty = false;
    }

    /**
     * Rebuild the index of the given column
     *
     * @param integer $columnIndex
     * @throws \UnexpectedValueException if an invalid value should be used as key
     */
    protected function _rebuildIndexOfColumn($columnIndex)
    {
        $temporaryPrimaryKeys = [];
        $columnData = $this->getColumnAtIndex($columnIndex);

        $rowCount = count($columnData);
        $currentRowIndex = 0;
        do {
            $point = new \stdClass();
            $point->row = $currentRowIndex;
            $point->column = $columnIndex;

            $data = $columnData[$currentRowIndex];

            $primaryKeyObject = [
                'data'  => $data,
                'point' => $point,
            ];

            if (is_scalar($data)) {
                $temporaryPrimaryKeys[$data] = $primaryKeyObject;
            } elseif (is_object($data) && method_exists($data, '__toString')) {
                $temporaryPrimaryKeys[$data . ''] = $primaryKeyObject;
            } else {
                throw new \UnexpectedValueException(
                    'Could not use value at row ' . $currentRowIndex . ' column ' . $columnIndex . ' as key', 1379504589
                );
            }
        } while (++$currentRowIndex < $rowCount);

        $this->_indexes[$columnIndex] = $temporaryPrimaryKeys;
    }

    /**
     * Returns the full key object for the given value if it is a valid primary
     * key, or NULL if it doesn't exist
     *
     * @param mixed $value
     * @param bool  $returnPoint
     * @return mixed|null
     */
    public function getIndexedDataForValue($value, $returnPoint = false)
    {
        if ($this->_indexIsDirty) {
            unset($this->_indexes);
            #$this->_rebuildIndex()
            $this->_indexIsDirty = false;
        }

        $indexedColumns = array_keys($this->_indexColumns);
        foreach ($indexedColumns as $columnIndexToSearchIn) {
            // Check if the index for the current column is valid
            if (!isset($this->_indexes[$columnIndexToSearchIn])) {
                $this->_rebuildIndexOfColumn($columnIndexToSearchIn);
            }

            $dataIndex = $this->_indexes[$columnIndexToSearchIn];
            if (isset($dataIndex[$value])) {
                $indexDataObject = $dataIndex[$value];
                if ($returnPoint) {
                    return $indexDataObject['point'];
                } else {
                    return $indexDataObject['data'];
                }
            }
        }

        return null;
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ACCESS COLUMNS AND ROWS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the contents of the row at the given index.
     *
     * @param integer $index The index of the row
     * @return    array|bool Returns the contents of the row or FALSE if the index lies beyond bounds
     */
    public function getRowAtIndex($index)
    {
        if ($this->offsetExists($index)) {
            return $this->offsetGet($index);
        } else {
            return false;
        }
    }

    /**
     * Returns the contents of the row at the given index as an instance of \Iresults\Core\Mutable
     *
     * If the property keys should be built from the column index instead of the contents of the first row, set
     * $useIndexedPropertyKeys to TRUE.
     *
     * @param integer $index                  The index of the row
     * @param boolean $useIndexedPropertyKeys Set this to TRUE to create the property keys from column index
     * @return    Mutable Returns the mutable object or FALSE if the index lies beyond bounds
     */
    public function getRowAtIndexAsMutable($index, $useIndexedPropertyKeys = false)
    {
        $dataArray = null;
        $mutableObject = null;
        $propertyArray = null;

        if ($useIndexedPropertyKeys) { // Use the indexes as property keys
            $propertyArray = $this->getRowAtIndex($index);
        } else { // Use the first row as property keys
            $propertyArray = $this->getRowAtIndexAsDictionary($index);
        }

        return Mutable::mutableWithArray($propertyArray);
    }

    /**
     * Returns the contents of the row at the given index as a dictionary.
     *
     * @param integer $index The index of the row
     * @return    array|bool Returns the dictionary or FALSE if the index lies beyond bounds
     */
    public function getRowAtIndexAsDictionary($index)
    {
        $dataArray = null;
        $keyArray = $this->getRowAtIndex(0);
        if ($index === 0) {
            $dataArray = $keyArray;
        } else {
            $dataArray = $this->getRowAtIndex($index);
        }
        if ($dataArray === false) {
            return false;
        }

        return array_combine($this->_buildKeysFromArray($keyArray), $dataArray);
    }

    /**
     * Builds and returns an array of keys for the input array
     *
     * @param array $input
     * @return array<string>
     */
    protected function _buildKeysFromArray($input)
    {
        $keysArray = [];
        $currentElement = reset($input);
        do {
            switch (true) {
                case is_scalar($currentElement):
                    $keysArray[] = $currentElement;
                    break;

                case is_array($currentElement):
                    $keysArray[] = 'Array (' . count($currentElement) . ')';
                    break;

                case is_object($currentElement):
                    if ($currentElement instanceof Core) {
                        /** @var Core $currentElement */
                        $keysArray[] = $currentElement->description();
                    } elseif (method_exists($currentElement, '__toString')) {
                        $keysArray[] = $currentElement->__toString();
                    }
                    break;
            }
        } while ($currentElement = next($input));

        return $keysArray;
    }

    /**
     * Replaces the contents of the row at the given index.
     *
     * If no row exists at the given index, it will be created.
     *
     * @param integer $index  The index of the row to replace
     * @param array   $newRow The new row data
     * @return    void
     */
    public function setRowAtIndex($index, $newRow)
    {
        $this->data[$index] = $newRow;
        $this->_markAsDirty();
    }

    /**
     * Removes the row at the given index with all of it's data.
     *
     * @param integer $index The index of the row to remove
     * @return    void
     */
    public function removeRowAtIndex($index)
    {
        $tempData = [];
        foreach ($this->data as $key => $row) {
            if ($key !== $index) {
                $tempData[] = $row;
            }
        }
        $this->data = null;
        unset($this->data);
        $this->data = $tempData;

        $this->_markAsDirty();
    }

    /**
     * Removes and returns the last row of the grid.
     *
     * @return    array<mixed>
     */
    public function popRow()
    {
        $lastRow = $this->getRowAtIndex($this->getRowCount() - 1);
        end($this->data);
        $lastKey = key($this->data);
        unset($this->data[$lastKey]);

        $this->_markAsDirty();

        return $lastRow;
    }

    /**
     * Returns the contents of the column at the given index.
     *
     * @param integer $index The index of the column
     * @return    array|bool Returns the contents of the column or FALSE if the index lies beyond bounds
     */
    public function getColumnAtIndex($index)
    {
        $columnData = [];

        $filledDataL = $this->getFilledGridData();
        $count = count($filledDataL);
        for ($rowIndex = 0; $rowIndex < $count; $rowIndex++) {
            $row = $filledDataL[$rowIndex];
            if (!array_key_exists($index, $row) && $index > count($row)) {
                //$this->pd("DAG NOTEX",$index,$row,$rowIndex,$filledDataL);
                $this->pd('Warning: Index \'' . $index . '\' out of bounds in getColumnAtIndex()');

                return false;
            }
            $columnData[$rowIndex] = $row[$index];
        }


        return $columnData;
    }

    /**
     * Returns an instance of \Iresults\Core\Mutable. The contents of the column at
     * the given index define the property values whereas the contents of the
     * first column define the property keys.
     *
     * If the property keys should be built from the row index instead of the
     * contents of the first column, set $useIndexedPropertyKeys to TRUE.
     *
     * @param integer $index                  The index of the column
     * @param boolean $useIndexedPropertyKeys Set this to TRUE to create the property keys from row index
     * @return    Mutable Returns the mutable object or FALSE if the index lies beyond bounds
     */
    public function getColumnAtIndexAsMutable($index, $useIndexedPropertyKeys = false)
    {
        $propertyArray = null;

        if ($useIndexedPropertyKeys) { // Use the indexes as property keys
            $propertyArray = $this->getColumnAtIndex($index);
        } else { // Use the first column as property keys
            $propertyArray = $this->getColumnAtIndexAsDictionary($index);
        }

        return Mutable::mutableWithArray($propertyArray);
    }

    /**
     * Returns a dictionary The contents of the column at the given index define
     * the values whereas the contents of the first column define the keys.
     *
     * @param integer $index The index of the column
     * @return    array|bool Returns the mutable object or FALSE if the index lies beyond bounds
     */
    public function getColumnAtIndexAsDictionary($index)
    {
        $dataArray = null;
        $keyArray = $this->getColumnAtIndex(0);
        if ($index === 0) {
            $dataArray = $keyArray;
        } else {
            $dataArray = $this->getColumnAtIndex($index);
        }
        if ($dataArray === false) {
            return false;
        }

        return array_combine($keyArray, $dataArray);
    }

    /**
     * Replaces the contents of the column at the given index.
     *
     * If no column exists at the given index, it will be created.
     *
     * @param integer $index     The index of the row to replace
     * @param array   $newColumn The new column data
     * @return    void
     */
    public function setColumnAtIndex($index, $newColumn)
    {
        $count = count($newColumn);
        for ($i = 0; $i < $count; $i++) {
            /**
             * Check if the row exists.
             */
            if (!array_key_exists($i, $this->data)) {
                $this->data[$i] = [];
            }
            $this->data[$i][$index] = $newColumn[$i];
        }
        $this->_markAsDirty();
    }

    /**
     * Removes the column at the given index with all of it's data.
     *
     * @param integer $index The index of the column to remove
     * @return    void
     */
    public function removeColumnAtIndex($index)
    {
        $rowCountL = $this->getRowCount();
        for ($i = 0; $i < $rowCountL; $i++) {
            /**
             * Check if the row exists.
             */
            if (isset($this->data[$i]) && is_array($this->data[$i])) {
                $tempData = [];
                $row = $this->data[$i];
                foreach ($row as $key => $column) {
                    if ($key !== $index) {
                        $tempData[] = $column;
                    }
                }
                $this->data[$i] = $tempData;
            }
        }
        $this->_markAsDirty();
    }

    /**
     * Removes and returns the last column of the grid.
     *
     * @return    array<mixed>
     */
    public function popColumn()
    {
        $rowCountL = $this->getRowCount();
        $lastColumn = $this->getColumnAtIndex($this->getColumnCount() - 1);
        for ($i = 0; $i < $rowCountL; $i++) {
            /**
             * Check if the row exists.
             */
            if (array_key_exists($i, $this->data) && is_array($this->data[$i])) {
                $this->data[$i][$this->columnCount - 1] = null;
                unset($this->data[$i][$this->columnCount - 1]);
            }

        }
        $this->_markAsDirty();

        return $lastColumn;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* SORTING & REVERSING MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Sorts the grid by the values of the column at the given index.
     *
     * @param integer $index       The index of the column by which to sort
     * @param array   $sortOptions An array of sort options
     * @return    boolean    Returns TRUE on success, otherwise FALSE
     */
    public function sortByColumnAtIndex($index, $sortOptions = null)
    {
        //if (is_null($sortOptions)) $sortOptions = array(SORT_REGULAR);
        $result = false;

        $sortValues = $this->getColumnAtIndex($index);
        if (!$sortValues) {
            return false;
        }

        if (!$sortOptions) {
            $result = array_multisort($sortValues, $this->data);
        } else {
            switch (count($sortOptions)) {
                case 1:
                    $result = array_multisort($sortValues, $this->data, $sortOptions[0]);
                    break;
                case 2:
                    $result = array_multisort($sortValues, $this->data, $sortOptions[0], $sortOptions[1]);
                    break;
                case 3:
                    $result = array_multisort(
                        $sortValues,
                        $this->data,
                        $sortOptions[0],
                        $sortOptions[1],
                        $sortOptions[2]
                    );
                    break;

                default:
                    $result = array_multisort($sortValues, $this->data);
                    break;
            }
        }

        $this->_markAsDirty();

        return $result;
    }

    /**
     * Reverses the order of columns.
     *
     * @return    void
     */
    public function reverseColumns()
    {
        $tempGrid = new \Iresults\Core\Model\DataGrid();
        $this->_sortRawData();

        $columnCount = $this->getColumnCount();
        $currentColumn = $columnCount;
        for ($i = 0; $i < $columnCount; $i++) {
            $columnData = $this->getColumnAtIndex($i);
            $tempGrid->setColumnAtIndex($currentColumn, $columnData);
            $currentColumn--;
        }

        $this->data = $tempGrid->getRawGridData();
        $this->_markAsDirty();
    }

    /**
     * Sorts the grid by the values of the row at the given index.
     *
     * @param integer $index       The index of the row by which to sort
     * @param array   $sortOptions An array of sort options
     * @return    boolean    Returns TRUE on success, otherwise FALSE
     */
    public function sortByRowAtIndex($index)
    {
        throw new \Exception("Sorry. This method is currently not implemented.");
    }

    /**
     * Reverses the order of rows.
     *
     * @return    void
     */
    public function reverseRows()
    {
        $this->_sortRawData();
        $this->data = array_reverse($this->data, false);

        $this->_markAsDirty();
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ROTATION            MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Rotates to grid 90° to the left.
     *
     * @return    $this
     */
    public function rotateLeft()
    {
        $filledDataL = $this->getFilledGridData();
        $newDataGrid = new \Iresults\Core\Model\DataGrid();

        /**
         * For each row of the grid.
         */
        for ($i = 0; $i < $this->rowCount; $i++) {
            $row = $filledDataL[$i];

            /**
             * For each cell of the row.
             */
            for ($j = 0; $j < $this->columnCount; $j++) {
                $cell = $row[$j];
                $x = $this->columnCount + 1 - $j;
                $y = $i;

                $newDataGrid->setCellAtRowNumberAndColumnNumber($cell, $x, $y);
            }
        }

        $this->data = $newDataGrid->getRawGridData();
        $this->filledData = null;
        $this->_markAsDirty();

        return $this;
    }

    /**
     * Rotates to grid 90° to the right.
     *
     * @return    $this
     */
    public function rotateRight()
    {
        $filledDataL = $this->getFilledGridData();
        $newDataGrid = new \Iresults\Core\Model\DataGrid();

        /**
         * For each row of the grid.
         */
        for ($i = 0; $i < $this->rowCount; $i++) {
            $row = $filledDataL[$i];

            /**
             * For each cell of the row.
             */
            for ($j = 0; $j < $this->columnCount; $j++) {
                $cell = $row[$j];
                $x = $this->columnCount + 1 - $j;
                $y = $this->rowCount - $i - 1;


                $newDataGrid->setCellAtRowNumberAndColumnNumber($cell, $x, $y);
            }
        }

        $this->data = $newDataGrid->getRawGridData();
        $this->filledData = null;
        $this->_markAsDirty();

        return $this;

        #throw new Exception("Sorry. This method is currently not implemented.");
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* SETTING ROW AND COLUMN VALUES    WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Set the cell values to the elements of $newRow, starting at the cell with
     * the row index $rowNumber and column index $columnNumber.
     *
     * @param array   & $target       Reference to the target array to set the data
     * @param array   $newColumn      The data to set the cells to
     * @param integer & $rowNumber    Reference to the y offset
     * @param integer & $columnNumber Reference to the x offset
     *
     * @return    void
     */
    //public function setColumnAtRowNumberAndColumnNumber(&$target, $newColumn, &$rowNumber, &$columnNumber) {
    //	foreach ($newColumn as $cell) {
    //		$this->setCellAtRowNumberAndColumnNumber($target, $cell, $rowNumber, $columnNumber);
    //		$rowNumber++;
    //	}
    //	$rowNumber--;
    //	return;
    //}

    /**
     * Each element of $newColumns is treated a single column, starting at the
     * position $rowNumber/$columnNumber.
     *
     * @param array   & $target       Reference to the target array to set the data
     * @param array   $newColumns     The data to set the cells to
     * @param integer & $rowNumber    Reference to the y offset
     * @param integer & $columnNumber Reference to the x offset
     *
     * @return    void
     */
    //public function setColumnsAtRowNumberAndColumnNumber(&$target, $newColumns, &$rowNumber, &$columnNumber) {
    //	$startRow = $rowNumber;
    //	$startColumn = $columnNumber;
    //	foreach ($newColumns as $newColumn) {
    //		$rowNumber = $startRow;
    //		$this->setColumnAtRowNumberAndColumnNumber($target, $newColumn, $rowNumber, $columnNumber);
    //		$columnNumber++;
    //	}
    //	$columnNumber--;
    //}

    /**
     * Set the cell values to the elements of $newRow, starting at the position
     * $rowNumber/$columnNumber.
     *
     * @param array   & $target       Reference to the target array to set the data
     * @param array   $newRow         The data to set the cells to
     * @param integer & $rowNumber    Reference to the y offset
     * @param integer & $columnNumber Reference to the x offset
     *
     * @return    void
     */
    //public function setRowAtRowNumberAndColumnNumber(&$target, $newRow, &$rowNumber, &$columnNumber) {
    //	foreach ($newRow as $cell) {
    //		$this->setCellAtRowNumberAndColumnNumber($target, $cell, $rowNumber, $columnNumber);
    //		$columnNumber++;
    //	}
    //	// $columnNumber--;
    //}

    /**
     * Set the rows to the values in newRows, starting at the position
     * $rowNumber/$columnNumber. Each element of newRows is treated a single row.
     *
     * @param array   & $target       Reference to the target array to set the data
     * @param array   $newRows        The data to set the cells to
     * @param integer & $rowNumber    Reference to the y offset
     * @param integer & $columnNumber Reference to the x offset
     *
     * @return    void
     */
    //public function setRowsAtRowNumberAndColumnNumber(&$target, $newRows, &$rowNumber, &$columnNumber) {
    //	$startRow = $rowNumber;
    //	$startColumn = $columnNumber;
    //	foreach ($newRows as $newRow) {
    //		$columnNumber = $startColumn;
    //		$this->setRowAtRowNumberAndColumnNumber($target, $newRow, $rowNumber, $columnNumber);
    //		$rowNumber++;
    //	}
    //	$rowNumber--;
    //}

    /**
     * Set the value of the cell at $rowNumber/$columnNumber to the given value.
     *
     * @param mixed   $newCell        The data to set the cell value to
     * @param integer & $rowNumber    Reference to the row offset
     * @param integer & $columnNumber Reference to the column offset
     *
     * @return    integer The number of columns inserted
     */
    public function setCellAtRowNumberAndColumnNumber($newCell, &$rowNumber, &$columnNumber)
    {
        if (!isset($this->data[$rowNumber])) {
            $this->data[$rowNumber] = [];
        }
        $this->data[$rowNumber][$columnNumber] = $newCell;

        return 1;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* DATA MAP PROCESSING MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Checks if the grid data is dirty, which requires some data to be rebuilt.
     * The rebuilding is invoked automatically
     *
     * @return    \Iresults\Core\Model\DataGrid
     */
    protected function _repairIfDirty()
    {
        if ($this->checkIfDirty()) {
            $this->_sortRawData();

            $this->filledData = $this->data;

            //profile("Pre fill missing ");
            //$this->_fillMissingArrayElements($this->filledData,count($this->filledData),TRUE);
            $this->_fillMissingArrayElements($this->filledData, null, true);
            //profile("Post fill missing ");

            //end($this->data);
            //$this->rowCount = key($this->data);
            $this->rowCount = $this->_getLengthOfElement($this->data);
            $this->columnCount = $this->_getLengthOfLongestElementOfInput($this->data);

            $this->isDirty = false;
        }

        return $this;
    }

    /**
     * Marks the data as dirty
     */
    protected function _markAsDirty()
    {
        $this->isDirty = true;
        $this->_indexIsDirty = true;
    }

    /**
     * Sorts the raw data grid.
     *
     * @return    void
     */
    protected function _sortRawData()
    {
        foreach ($this->data as &$row) {
            ksort($row);
        }
        ksort($this->data);
    }

    /**
     * Returns if the data is dirty.
     *
     * @return    boolean    Returns TRUE if the data is dirty, otherwise FALSE
     */
    public function checkIfDirty()
    {
        if ($this->isDirty || !$this->filledData) {
            return true;
        }

        return false;
    }

    /**
     * Loops through the input array and sets each missing element.
     *
     * Missing elements are filled with an empty string, or an empty array if
     * the $insertArrays argument is TRUE.
     *
     * After the missing elements are inserted the array elements will be ordered
     * by their index.
     *
     * Filled missing points are shown as '-N-'.
     *  0 -------------------------------> (x)
     *  | v1.1  v1.2  v1.3  -N-   v1.5
     *  | v2.1  v2.2  v2.3  v2.4  v2.5
     *  | v3.1  v3.2  v3.3  v3.4  -N-
     *  | -N-   v4.2  v4.3  v4.4  v4.5
     *  | -N-   -N-   -N-   -N-   v5.5
     *  |
     *  V (y)
     *
     * If $length is given the loop stops after $length run. If $length is not
     * specified the length of the longest element in $input is determined.
     *
     * @param array   & $input         Reference to the array to fill
     * @param integer $length          The length of the filled array
     * @param boolean $insertArrays    If set to true an array will be inserted
     *                                 for a missing element, otherwise an empty string.
     *
     * @return    void
     */
    protected function _fillMissingArrayElements(&$input, $length = null, $insertArrays = false)
    {
        $this->_fillMissingArrayElementsDepth++;
        if ($length === null) {
            $length = $this->_getLengthOfElement($input);
        }

        $i = 0;
        do {
            if (isset($input[$i])) {
                /*
                 * If the element exists and it is an array. It has to be filled
                 * too.
                 */
                if (is_array($input[$i])) {
                    //echo "IS ARRAY";
                    $element = &$input[$i];
                    end($element);

                    //$elementLength = key($element);
                    //echo " KEY ".var_export($elementLength,TRUE)." ";
                    //$this->pd($element,$elementsLastElement);


                    $this->_fillMissingArrayElements($element);#,$elementLength);
                    $input[$i] = $element;
                } elseif (!$input[$i] && $this->_emptyElementPlaceholder) {
                    $input[$i] = $this->_emptyElementPlaceholder;
                } elseif ($input[$i] == ' ' && $this->_emptyElementPlaceholder) {
                    $input[$i] = $this->_emptyElementPlaceholder;
                }
            } else {
                //echo "NEW INPUT $i<br>";
                if ($insertArrays || $this->_fillMissingArrayElementsDepth < 2) {
                    $input[$i] = [];
                } else {
                    $input[$i] = $this->_missingElementPlaceholder;
                }

                //$this->pd($input[$i]);
            }
        } while (++$i < $length);

        /**
         * Sort the current input.
         */
        ksort($input);

        $this->_fillMissingArrayElementsDepth--;
    }

    /**
     * Returns the length of the longest element in the given input.
     *
     * @param array <array<mixed>> $input Reference to the input array
     *
     * @return    integer    The length of the longest element
     */
    protected function _getLengthOfLongestElementOfInput(&$input)
    {
        $i = 0;
        $length = 0;
        $count = $this->_getLengthOfElement($input);

        do {
            $rowLength = $this->_getLengthOfElement($input[$i]);
            if ($rowLength > $length) {
                $length = $rowLength;
            }
        } while (++$i < $count);

        return $length;
    }

    /**
     * Returns the length of the element while taking account of missing elements.
     *
     * @param array $element The element array
     * @return    integer    The length of the element
     */
    protected function _getLengthOfElement(&$element)
    {
        if (!is_array($element) && !($element instanceof Traversable)) {
            return -1;
        }
        end($element);
        $elementLength = key($element) + 1;
        if (count($element) > $elementLength) {
            $elementLength = count($element);
        }

        reset($element);

        return $elementLength;
    }



    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ITERATOR       WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function current()
    {
        $this->_repairIfDirty();

        return current($this->filledData);
    }

    public function key()
    {
        $this->_repairIfDirty();

        return key($this->filledData);
    }

    public function next()
    {
        $this->_repairIfDirty();

        return next($this->filledData);
    }

    public function rewind()
    {
        $this->_repairIfDirty();

        return reset($this->filledData);
    }

    public function valid()
    {
        $this->_repairIfDirty();

        return (current($this->filledData) == true);
    }

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* ARRAY ACCESS   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    public function offsetExists($offset)
    {
        $this->_repairIfDirty();

        return array_key_exists($offset, $this->filledData);
    }

    public function offsetGet($offset)
    {
        $this->_repairIfDirty();

        return $this->filledData[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        $this->_markAsDirty();
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
        $this->_markAsDirty();
    }




    /**
     * Prepares the given array of data to be inserted into the Excel table.
     *
     * As one preparation the method checks the outermost points of the data map
     * and fills the missing points with an empty string.
     *
     * Filled missing points are shown as '-N-'.
     *  0 -------------------------------> (x)
     *  | v1.1  v1.2  v1.3  -N-   v1.5
     *  | v2.1  v2.2  v2.3  v2.4  v2.5
     *  | v3.1  v3.2  v3.3  v3.4  -N-
     *  | -N-   v4.2  v4.3  v4.4  v4.5
     *  | -N-   -N-   -N-   -N-   v5.5
     *  |
     *  V (y)
     *
     *
     * @param array $input The input data
     * @return    array    The prepared data
     */
    //public function prepareData() {
    //	$result = $input;
    //	$lastValue = end($input);
    //	$lastKey = key($input);
    //	$firstValue = reset($input);
    //
    //	$highestKey = 0;
    //
    //	/**
    //	 * Get the highest key.
    //	 */
    //	if (is_array($firstValue)) {
    //		foreach ($input as $row) {
    //			end($row);
    //			$rowLastKey = key($row);
    //			if ($rowLastKey > $highestKey) {
    //				$highestKey = $rowLastKey;
    //			}
    //		}
    //
    //		/**
    //		 * Create the missing rows
    //		 */
    //		$this->_fillMissingArrayElements($result, $lastKey, TRUE);
    //
    //		/**
    //		 * Fill the rows.
    //		 */
    //		foreach ($result as &$row) {
    //			$this->_fillMissingArrayElements($row, $highestKey);
    //		}
    //	/**
    //	 * If the input is a one dimensional array -> fill it.
    //	 */
    //	} else {
    //		$this->_fillMissingArrayElements($result,$lastKey);
    //	}
    //
    //
    //	$this->pd("PREPARED DATA",$result);
    //	return $result;
    //}

    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* CONFIGURATION ACCESSOR METHODS   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Getter for the placeholder for empty elements.
     *
     * @return    string
     */
    public function getEmptyElementPlaceholder()
    {
        return $this->_emptyElementPlaceholder;
    }

    /**
     * Setter for the placeholder for empty elements.
     *
     * @param string $newValue The new placeholder to use
     * @return    void
     */
    public function setEmptyElementPlaceholder($newValue)
    {
        $this->_emptyElementPlaceholder = $newValue;
    }

    /**
     * Getter for the placeholder for missing elements.
     *
     * @return    string
     */
    public function getMissingElementPlaceholder()
    {
        return $this->_missingElementPlaceholder;
    }

    /**
     * Setter for the placeholder for missing elements.
     *
     * @param string $newValue The new value to set
     * @return    void
     */
    public function setMissingElementPlaceholder($newValue)
    {
        $this->_missingElementPlaceholder = $newValue;
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* HELPER METHODS                   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Returns the data of the grid as a table
     *
     * @return    string    The HTML code for the table
     */
    public function createHtmlTable()
    {
        $data = $this->getGrid();
        $table = new Table();

        return $table->render($data);
    }


    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* FACTORY METHODS   MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
    /**
     * Factory method: Returns a mutable object representing the data from the
     * given URL.
     *
     * @param string $url URL of the file to read
     * @return    \Iresults\Core\Model\DataGrid
     */
    static public function gridWithContentsOfUrl($url)
    {
        /** @var \Iresults\Core\Model\DataGrid $mutable */
        $mutable = new static();
        $mutable->initWithContentsOfUrl($url);

        return $mutable;
    }
}
