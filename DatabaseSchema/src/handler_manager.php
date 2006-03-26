<?php
/**
 * File containing the ezcDbSchemaHandlerManager class.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Deals with schema handlers for a ezcDbSchema object.
 *
 * Determines which handlers to use for the specified storage type or internal format.
 * Creates handlers on demand.
 *
 * @package DatabaseSchema
 */
class ezcDbSchemaHandlerManager
{
    /**
     * Set of standard read handlers.
     */
    static public $readHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayReader',
        'mysql' => 'ezcDbSchemaMysqlReader',
        'oracle' => 'ezcDbSchemaOracleReader',
        'pgsql' => 'ezcDbSchemaPgsqlReader',
        'sqlite' => 'ezcDbSchemaSqliteReader',
        'xml' => 'ezcDbSchemaXmlReader',
    );

    /**
     * Set of standard write handlers.
     */
    static public $writeHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayWriter',
        'mysql' => 'ezcDbSchemaMysqlWriter',
        'oracle' => 'ezcDbSchemaOracleWriter',
        'pgsql' => 'ezcDbSchemaPgsqlWriter',
        'sqlite' => 'ezcDbSchemaSqliteWriter',
        'xml' => 'ezcDbSchemaXmlWriter',
    );

    /**
     * Returns the name of the appropriate handler for the specified format.
     *
     */
    static public function getReaderByFormat( $format )
    {
        if ( !isset( self::$readHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'read' );
        }
        return self::$readHandlers[$format];
    }

    /**
     * Returns the name of the appropriate handler for the specified format.
     *
     */
    static public function getWriterByFormat( $format )
    {
        if ( !isset( self::$writeHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'write' );
        }
        return self::$writeHandlers[$format];
    }

    /**
     * Returns list of schema types supported by all known handlers.
     *
     * Goes through the list of known handlers and gathers information
     * of which schema types do they support.
     */
    static public function getSupportedFormats()
    {
        return array_keys( self::$readHandlers ) + array_keys( self::$writeHandlers );
    }

    /**
     * Adds the read handler class $readerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the $readerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaReader.
     * @param string $type
     * @param string $readerClass
     */
    static public function addReader( $type, $readerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $readerClass, true ) )
        {
            throw new ezcDbSchemaInvalidReaderClassException( $readerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaReader' );
        $handlerClass = new ReflectionClass( $readerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidReaderClassException( $readerClass );
        }
        self::$readHandlers[$type] = $readerClass;
    }

    /**
     * Adds the write handler class $writerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the $writerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaWriter.
     * @param string $type
     * @param string $writerClass
     */
    static public function addWriter( $type, $writerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $writerClass, true ) )
        {
            throw new ezcDbSchemaInvalidWriterClassException( $writerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaWriter' );
        $handlerClass = new ReflectionClass( $writerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidWriterClassException( $writerClass );
        }
        self::$writeHandlers[$type] = $writerClass;
    }
}
?>
