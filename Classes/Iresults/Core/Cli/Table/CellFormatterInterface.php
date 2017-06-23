<?php
/**
 * @author COD
 * Created 10.10.16 13:57
 */

namespace Iresults\Core\Cli\Table;

use Iresults\Core\Cli\Table;

interface CellFormatterInterface
{
    /**
     * Formats the given cell data
     *
     * @param mixed $data
     * @param Table $table
     * @return string
     */
    public function formatCellData($data, Table $table);
}
