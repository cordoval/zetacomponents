<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package TemplateTranslationTiein
 * @subpackage Tests
 */

/**
 * @package TemplateTranslationTiein
 * @subpackage Tests
 */
class ezcTemplateTranslationExtracterTest extends ezcTestCase
{
    function testExtracter()
    {
        $file = dirname( __FILE__ ) . '/test_files/test.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getTranslation();
        self::assertEquals( 
            array( 'een', 'twee', 'drie', 'vier', 'vijf', 'zes', 'zeven', 'acht', 'negen', 'tien', 'elf' ),
            array_keys( $this->readAttribute( $tr['test'], 'translationMap' ) ) 
        );
    }

    function testExtracterWithoutDefaultContext()
    {
        $file = dirname( __FILE__ ) . '/test_files/test_without_default_context.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        $tr = $et->getTranslation();
        self::assertEquals( 
            array( 'een', 'twee', 'drie', 'vier', 'vijf', 'zes', 'zeven', 'acht', 'negen', 'tien', 'elf' ),
            array_keys( $this->readAttribute( $tr['test'], 'translationMap' ) ) 
        );
    }

    function testExtracterWithoutContext()
    {
        $file = dirname( __FILE__ ) . '/test_files/test_without_context.ezt';
        $source = new ezcTemplateSourceCode( $file, $file );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        try
        {
            $eted = $tst->accept( $et );
            self::fail( "Expected exception not thrown." );
        }
        catch ( ezcTemplateParserException $e )
        {
            self::assertEquals( "/home/derick/dev/ezcomponents/trunk/TemplateTranslationTiein/tests/test_files/test_without_context.ezt:3:11: Expecting a 'context' parameter, or a default context set with {tr_context}.\n\n{tr \"een\"}\n          ^\n", $e->getMessage() );
        }
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( 'ezcTemplateTranslationExtracterTest' );
    }
}

?>