<?php
namespace Markei\DoctrineMigrationWebBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\OutputWriter;

/**
 * Saves messages in to memory
 *
 * @author maartendekeizer
 * @copyright Markei.nl
 */
class MemoryOutputWriter extends OutputWriter
{
    /**
     * @var array
     */
    private $memory = [];

    /**
     * Do not accept any parameter
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\DBAL\Migrations\OutputWriter::write()
     */
    public function write($message)
    {
        $this->memory[] = $message;
    }

    /**
     * Returns the memory
     * @return array
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Clear the memory
     */
    public function clear()
    {
        $this->memory = [];
    }
}