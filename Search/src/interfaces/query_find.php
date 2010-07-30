<?php
/**
 * File containing the ezcSearchFindQuery class.
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @package Search
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

/**
 * Class to create select search backend indepentent search queries.
 *
 * @package Search
 * @version //autogentag//
 * @mainclass
 */
interface ezcSearchFindQuery extends ezcSearchQuery
{
    /**
     * Opens the query and selects which fields you want to return with
     * the query.
     *
     * select() accepts an arbitrary number of parameters. Each parameter must
     * contain either the name of a field or an array containing the names of
     * the fields.  Each call to select() appends fields to the list of
     * fields that will be used in the query.
     *
     * Example:
     * <code>
     * $q->select( 'field1', 'field2' );
     * </code>
     * The same could also be written:
     * <code>
     * $fields[] = 'field1';
     * $fields[] = 'field2;
     * $q->select( $fields );
     * </code>
     * or using several calls
     * <code>
     * $q->select( 'field1' )->select( 'field2' );
     * </code>
     *
     * @param string|array(string) $... Either a string with a field name or an array of field names.
     * @return ezcSearchFindQuery
     */
    public function select();

    /**
     * Registers from which offset to start returning results, and how many results to return.
     *
     * $limit controls the maximum number of rows that will be returned.
     * $offset controls which row that will be the first in the result
     * set from the total amount of matching rows.
     *
     * @param int $limit
     * @param int $offset
     * @return ezcSearchQuery
     */
    public function limit( $limit, $offset = 0 );

    /**
     * Tells the query on which field to sort on, and in which order
     *
     * You can call orderBy multiple times. Each call will add a
     * column to order by.
     *
     * @param string $column
     * @param int    $type
     * @return ezcSearchQuery
     */
    public function orderBy( $column, $type = ezcSearchQueryTools::ASC );

    /**
     * Adds one facet to the query.
     *
     * @param string $facet
     * @return ezcSearchQuery
     */
    public function facet( $facet );

    /**
     * Creates an 'important' clause
     *
     * This method accepts a clause and marks it as important.
     *
     * @param string $clause
     * @return string
     */
    public function important( $clause );

    /**
     * Modifies a clause to give it higher weight while searching.
     *
     * This method accepts a clause and adds a boost factor.
     *
     * @param string $clause
     * @param float $boostFactor
     * @return string
     */
    public function boost( $clause, $boostFactor );

    /**
     * Modifies a clause make it fuzzy.
     *
     * This method accepts a clause and registers it as a fuzzy search, an
     * optional fuzz factor is also supported.
     *
     * @param string $clause
     * @param float $fuzzFactor
     * @return string
     */
    public function fuzz( $clause, $fuzzFactor = null );
}

?>
