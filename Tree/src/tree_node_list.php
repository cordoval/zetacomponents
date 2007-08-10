<?php
/**
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeNodeList represents a lists of nodes.
 *
 * The nodes in the list can be accessed through an array as this class
 * implements the ArrayAccess SPL interface. The array is indexed based on the
 * the node's ID.
 *
 * @property-read string $size The number of nodes in the list.
 *
 * @package Tree
 * @version //autogentag//
 */
class ezcTreeNodeList implements ArrayAccess
{
    /**
     * Holds the nodes of this list.
     *
     * @var array(ezcTreeNode)
     */
    private $nodes;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    //private $properties = array();

    /**
     * Constructs a new ezcTreeNode object
     */
    public function __construct()
    {
        $this->nodes = array();
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'size':
                return count( $this->nodes );

        }
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBasePropertyPermissionException if a read-only property is
     *         tried to be modified.
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'size':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns whether a node with the ID $nodeId exists in the list
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param  string $nodeId
     * @return bool
     * @ignore
     */
    public function offsetExists( $nodeId )
    {
        return array_key_exists( $nodeId, $this->nodes );
    }

    /**
     * Returns the node with the ID $nodeId
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param  string $nodeId
     * @return ezcTreeNode
     * @ignore
     */
    public function offsetGet( $nodeId )
    {
        return $this->nodes[$nodeId];
    }

    /**
     * Adds a new node with node ID $nodeId to the list.
     *
     * This method is part of the SPL ArrayAccess interface.
     * 
     * @throws ezcTreeInvalidClassException if the data to be set as array
     *         element is not an instance of ezcTreeNode
     * @throws ezcTreeIdsDoNotMatchException if the array index $nodeId does not
     *         match the tree node's ID that is stored in the $data object
     * @param  string      $nodeId
     * @param  ezcTreeNode $data
     * @ignore
     */
    public function offsetSet( $nodeId, $data )
    {
        if ( !$data instanceof ezcTreeNode )
        {
            throw new ezcTreeInvalidClassException( 'ezcTreeNode', get_class( $data ) );
        }
        if ( $data->id !== $nodeId )
        {
            throw new ezcTreeIdsDoNotMatchException( $data->id, $nodeId );
        }
        $this->addNode( $data );
    }

    /**
     * Removes the node with ID $nodeId from the list.
     *
     * This method is part of the SPL ArrayAccess interface.
     *
     * @param string $nodeId
     * @ignore
     */
    public function offsetUnset( $nodeId )
    {
        unset( $this->nodes[$nodeId] );
    }


    /**
     * Adds the node $node to the list
     *
     * @param ezcTreeNode $node
     */
    public function addNode( ezcTreeNode $node )
    {
        $this->nodes[$node->id] = $node;
    }

    /**
     * Returns all nodes in the list
     *
     * @return array(string=>ezcTreeNode)
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Fetches data for all nodes in the node list
     *
     * @param ezcTreeNodeList $nodeList
     */
    public function fetchDataForNodes()
    {
        // We need to use a little trick to get to the tree object. We can do
        // that through ezcTreeNode objects that are part of this list. We
        // can't do that when the list is empty. In that case we just return.
        if ( count( $this->nodes ) === 0 )
        {
            return;
        }
        // Find a node in the list
        reset( $this->nodes );
        $node = current( $this->nodes );
        // Grab the tree and use it to fetch data for all nodes from the store
        $tree = $node->tree;
        $tree->store->fetchDataForNodes( $this );
    }
}
?>
