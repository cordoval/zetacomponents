<?php
/**
 * File containing the ezcFeedSchema class.
 *
 * @package Feed
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class providing handling of feed schemas.
 *
 * @package Feed
 * @version //autogentag//
 */
class ezcFeedSchema
{
    /**
     * Holds a feed schema.
     *
     * @var array(string=>mixed)
     */
    protected $schema;

    /**
     * Creates a new feed schema.
     *
     * @param array(string=>mixed) $schema The new schema for this class
     */
    public function __construct( array $schema )
    {
        $this->schema = $schema;
    }

    /**
     * Returns the attributes defined for the element $element in this feed
     * schema.
     *
     * @param string $element The schema element
     * @param string $subElement The subelement of $element
     * @return array(string)
     */
    public function getAttributes( $element, $subElement = null )
    {
        $result = array();

        if ( $subElement === null )
        {
            if ( isset( $this->schema[$element]['ATTRIBUTES'] ) )
            {
                $result = $this->schema[$element]['ATTRIBUTES'];
            }
        }
        else
        {
            if ( isset( $this->schema[$element]['NODES'][$subElement]['ATTRIBUTES'] ) )
            {
                $result = $this->schema[$element]['NODES'][$subElement]['ATTRIBUTES'];
            }
        }

        return $result;
    }

    /**
     * Returns the required elements defined for the element $element in this
     * feed schema. If $element is null then it returns the required elements
     * in the root.
     *
     * @param string $element The schema element
     * @return array(string)
     */
    public function getRequired( $element = null )
    {
        if ( $element === null )
        {
            return isset( $this->schema['REQUIRED'] ) ? $this->schema['REQUIRED'] : array();
        }
        else
        {
            return isset( $this->schema[$element]['NODES']['REQUIRED'] ) ? $this->schema[$element]['NODES']['REQUIRED'] : array();
        }
    }

    /**
     * Returns the optional elements defined for the element $element in this
     * feed schema. If $element is null then it returns the optional elements
     * in the root.
     *
     * @param string $element The schema element
     * @return array(string)
     */
    public function getOptional( $element = null )
    {
        if ( $element === null )
        {
            return isset( $this->schema['OPTIONAL'] ) ? $this->schema['OPTIONAL'] : array();
        }
        else
        {
            return isset( $this->schema[$element]['NODES']['OPTIONAL'] ) ? $this->schema[$element]['NODES']['OPTIONAL'] : array();
        }
    }

    /**
     * Returns the elements which need to be present at least once defined for the
     * element $element in this feed schema. If $element is null then it returns
     * the elements which need to be present at least once in the root.
     *
     * @param string $element The schema element
     * @return array(string)
     */
    public function getAtLeastOne( $element = null )
    {
        if ( $element === null )
        {
            return isset( $this->schema['AT_LEAST_ONE'] ) ? $this->schema['AT_LEAST_ONE'] : array();
        }
        else
        {
            return isset( $this->schema[$element]['NODES']['AT_LEAST_ONE'] ) ? $this->schema[$element]['NODES']['AT_LEAST_ONE'] : array();
        }
    }

    /**
     * Returns whether the $element accepts multiple values or not. If $subElement
     * is present then returns whether the $subElement of $element accepts multiple
     * values or not.
     *
     * @param string $element The schema element
     * @param string $subElement The subelement of $element
     * @return bool
     */
    public function isMulti( $element, $subElement = null )
    {
        if ( $subElement === null )
        {
            return isset( $this->schema[$element]['MULTI'] );
        }
        else
        {
            return isset( $this->schema[$element]['NODES'][$subElement]['MULTI'] );
        }
    }

    /**
     * Returns the multiple name for the $element. If $subElement is present then
     * returns the multiple name for the subelement $subElement of $element.
     *
     * @param string $element The schema element
     * @param string $subElement The subelement of $element
     * @return string
     */
    public function getMulti( $element, $subElement = null )
    {
        if ( $subElement === null )
        {
            return $this->schema[$element]['MULTI'];
        }
        else
        {
            return $this->schema[$element]['NODES'][$subElement]['MULTI'];
        }
    }

    /**
     * Returns whether the $element is an attribute.
     *
     * @param string $element The schema element
     * @return bool
     */
    public function isAttribute( $element )
    {
        return isset( $this->schema['ATTRIBUTES'][$element] );
    }

    /**
     * Returns if $element does not accept a value for the root node. If $subElement
     * is present then returns if the subelement $subElement of element $element
     * accepts a value for the root node.
     *
     * @param string $element The schema element
     * @param string $subElement The subelement of $element
     * @return bool
     */
    public function isEmpty( $element, $subElement = null )
    {
        if ( $subElement === null )
        {
            return $this->schema[$element]['#'] === 'none';
        }
        else
        {
            return $this->schema[$element]['NODES'][$subElement]['#'] === 'none';
        }
    }

    /**
     * Returns the subschema which defines the element $element.
     *
     * @param string $element The schema element
     * @return array(string=>mixed)
     */
    public function getSchema( $element )
    {
        return $this->schema[$element];
    }

    /**
     * Returns the mapping of ezcFeed names to feed element names.
     *
     * @return array(string=>string)
     */
    public function getElementsMap()
    {
        return isset( $this->schema['ELEMENTS_MAP'] ) ? $this->schema['ELEMENTS_MAP'] : array();
    }

    /**
     * Returns the mapping of ezcFeed names to feed items element names.
     *
     * @return array(string=>string)
     */
    public function getItemsMap()
    {
        return isset( $this->schema['items']['ITEMS_MAP'] ) ? $this->schema['items']['ITEMS_MAP'] : array();
    }
}
?>
