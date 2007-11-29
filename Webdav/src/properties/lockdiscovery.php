<?php
/**
 * File containing the lockdiscovery property class.
 *
 * @package Webdav
 * @version //autogenlastmodified//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * An object of this class represents the Webdav property <lockdiscovery>.
 *
 * @property array(ezcWebdavLockDiscoveryPropertyActiveLock) $activeLock
 *           Lock information according to <activelock> elements.
 *
 * @version //autogenlastmodified//
 * @package Webdav
 *
 * @access private
 */
class ezcWebdavLockDiscoveryProperty extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavLockDiscoveryProperty.
     * 
     * @param array(ezcWebdavLockDiscoveryPropertyActiveLock) $activeLock Lock info.
     * @return void
     */
    public function __construct( array $activeLock = null )
    {
        parent::__construct( 'lockdiscovery' );

        $this->activeLock = $activeLock;
    }

    /**
     * Sets a property.
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'activeLock':
                if ( is_array( $propertyValue ) === false && $propertyValue !== null )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'array(ezcWebdavLockDiscoveryPropertyActiveLock)' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }

    /**
     * Check if property has no content.
     *
     * Should return true, if property has no assigned content.
     * 
     * @access public
     * @return bool
     */
    public function hasNoContent()
    {
        return $this->properties['activeLock'] === null;
    }
}

?>
