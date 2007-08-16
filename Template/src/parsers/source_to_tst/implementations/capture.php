<?php
/**
 * File containing the ezcTemplateCaptureSourceToTstParser class
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 *
 * @package Template
 * @version //autogen//
 * @access private
 */
class ezcTemplateCaptureSourceToTstParser extends ezcTemplateSourceToTstParser
{
    /**
     * Passes control to parent.
     * 
     * @param ezcTemplateParser $parser
     * @param ezcTemplateSourceToTstParser $parentParser
     * @param ezcTemplateCursor $startCursor
     */
    function __construct( ezcTemplateParser $parser, /*ezcTemplateSourceToTstParser*/ $parentParser, /*ezcTemplateCursor*/ $startCursor )
    {
        parent::__construct( $parser, $parentParser, $startCursor );
        $this->block = null;
    }

    /**
     * Parses the expression by using the ezcTemplateExpressionSourceToTstParser class.
     *
     * @param ezcTemplateCursor $cursor
     * @return bool
     */
    protected function parseCurrent( ezcTemplateCursor $cursor )
    {
        if ( $this->block->name == "capture" )
        {
            // handle closing block
            if ( $this->block->isClosingBlock )
            {
                $this->findNextElement();
                if ( !$this->parentParser->atEnd( $cursor, null, false ) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
                }

                $cursor->advance();

                $el = new ezcTemplateCaptureTstNode( $this->parser->source, $this->startCursor, $cursor );
                $el->isClosingBlock = true;
                $this->appendElement( $el );
                return true;
            }



            $capture = new ezcTemplateCaptureTstNode( $this->parser->source, $this->startCursor, $cursor );
            $this->findNextElement();

            if ( !$this->parseOptionalType( 'Variable', null, false ) )
            {
                throw new ezcTemplateSourceToTstParserException( $this, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_VARIABLE );
            }

            $capture->variable = $this->lastParser->element;

            $type = $this->parser->symbolTable->retrieve( $capture->variable->name );
            if ( $type === false )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->endCursor, $this->endCursor, $this->parser->symbolTable->getErrorMessage() );
            }




            $this->findNextElement();
            if ( !$cursor->match( "}" ) ) 
            {
                throw new ezcTemplateParserException( $this->parser->source, $cursor, $cursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }

            $this->appendElement( $capture);
            return true;
        }

        return false;
    }
                
        /*
                if ( !$this->parseOptionalType( "Variable", null, false) )
                {
                    throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_VARIABLE );
                }

                $cycle->variables[] = $this->lastParser->elements[0];

                $this->findNextElement();

            }
            while ( $this->currentCursor->match( "," ) );

            $this->appendElement( $cycle );
            // $this->elements[0] = $cycle; // Replace the variable, with the cycle.


            if ( !$this->parentParser->atEnd( $cursor, null, false ) )
            {
                throw new ezcTemplateParserException( $this->parser->source, $this->startCursor, $this->currentCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CURLY_BRACKET_CLOSE );
            }
            $cursor->advance();

            return true;
        }



        return false;
         */
}

?>
