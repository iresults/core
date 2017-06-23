<?php
/**
 * @author COD
 * Created 10.10.16 13:55
 */


namespace Iresults\Core\Cli\Table;

use Iresults\Core\Cli\Table;

class CellFormatter implements CellFormatterInterface
{
    /**
     * Formats the given cell data
     *
     * @param mixed $data
     * @param Table $table
     * @return string
     */
    public function formatCellData($data, Table $table)
    {
        return $this->removeNewLines($this->transformDataToString($data));
    }

    /**
     * Transform the data into a string
     *
     * @param mixed $data
     * @return string
     */
    protected function transformDataToString($data)
    {
        if (is_scalar($data)) {
            return (string)$data;
        } elseif (is_array($data)) {
            return implode(',', $data);
        } elseif ($data instanceof \Traversable) {
            return implode(',', iterator_to_array($data));
        } elseif ($data instanceof \DateTimeInterface) {
            return $data->format('r');
        } elseif (is_object($data)) {
            return method_exists($data, '__toString') ? (string)$data : get_class($data);
        }

        return (string)$data;
    }

    /**
     * @param string $input
     * @return string
     */
    protected function removeNewLines($input)
    {
        $parts = [];
        $separator = "\r\n";
        $line = strtok($input, $separator);

        while ($line !== false) {
            $parts[] = trim($line);
            $line = strtok($separator);
        }

        return implode(' ', $parts);
    }
}