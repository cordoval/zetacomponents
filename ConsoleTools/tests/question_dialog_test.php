<?php

require_once dirname( __FILE__ ) . "/dialog_test.php";

class ezcConsoleToolsQuestionDialogTest extends ezcConsoleToolsDialogTest
{

	public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( "ezcConsoleToolsQuestionDialogTest" );
    }

    public function testGetAccessSuccess()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );
        
        $this->assertSame( $output, $dialog->output );
        $this->assertEquals( new ezcConsoleQuestionDialogOptions(), $dialog->options );
    }

    public function testGetAccessFailure()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );
        
        $exceptionCaught = false;
        try
        {
            echo $dialog->foo;
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Exception not thrown on access of nonexistent property foo." );
    }

    public function testSetAccessSuccess()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );

        $outputNew  = new ezcConsoleOutput();
        $optionsNew = new ezcConsoleQuestionDialogOptions();

        $dialog->output  = $outputNew;
        $dialog->options = $optionsNew;

        $this->assertSame( $outputNew, $dialog->output );
        $this->assertSame( $optionsNew, $dialog->options );
    }
    
    public function testSetAccessFailure()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );
       
        $exceptionCaught = false;
        try
        {
            $dialog->output = "Foo";
        }
        catch ( ezcBaseValueException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Exception not thrown on invalid value for output." );
       
        $exceptionCaught = false;
        try
        {
            $dialog->options = "Foo";
        }
        catch ( ezcBaseValueException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Exception not thrown on invalid value for options." );
        
        $exceptionCaught = false;
        try
        {
            $dialog->foo = "bar";
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Exception not thrown on access of nonexistent property foo." );
        
        $this->assertSame( $output, $dialog->output );
        $this->assertEquals( new ezcConsoleQuestionDialogOptions(), $dialog->options );
    }

    public function testIssetAccess()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );
        
        $this->assertTrue( isset( $dialog->options ), "Property options is not set." );
        $this->assertTrue( isset( $dialog->output ), "Property options is not set." );
        $this->assertFalse( isset( $dialog->foo ), "Property foo is set." );
    }

    public function testBasicMethods()
    {
        $output = new ezcConsoleOutput();
        $dialog = new ezcConsoleQuestionDialog( $output );

        $this->assertFalse( $dialog->hasValidResult(), "Fresh dialog has valid result." );

        $exceptionCaught = false;
        try
        {
            $dialog->getResult();
        }
        catch ( ezcConsoleNoValidDialogResultException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Excption not thrown on getResult() without result." );

        $dialog->reset();

        $exceptionCaught = false;
        try
        {
            $dialog->getResult();
        }
        catch ( ezcConsoleNoValidDialogResultException $e )
        {
            $exceptionCaught = true;
        }
        $this->assertTrue( $exceptionCaught, "Excption not thrown on getResult() without result." );
    }

    public function testYesNoQuestionFactory()
    {
        $output = new ezcConsoleOutput();
        $dialog  = ezcConsoleQuestionDialog::YesNoQuestion( $output, "Is Jean-Luc a borg?", "y" );

        $this->assertType( "ezcConsoleQuestionDialogOptions", $dialog->options );
        $this->assertEquals( "Is Jean-Luc a borg?", $dialog->options->text );
        $this->assertTrue( $dialog->options->showResults );
        $this->assertType( "ezcConsoleQuestionDialogCollectionValidator", $dialog->options->validator );
        $this->assertEquals( array( "y", "n" ), $dialog->options->validator->collection );
        $this->assertEquals( "y", $dialog->options->validator->default );
        $this->assertEquals( ezcConsoleQuestionDialogCollectionValidator::CONVERT_LOWER, $dialog->options->validator->conversion );
    }

    public function testDialog1()
    {
        $this->runDialog( __METHOD__ );

        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "A\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "Y\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        // $this->saveDialogResult( __METHOD__, $res );
        $this->assertEquals( $this->res, $res );
    }

    public function testDialog2()
    {
        $this->runDialog( __METHOD__ );

        $res[] = fread( $this->pipes[1], 1024 );

        fputs( $this->pipes[0], "A\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "3.14\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "true\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "23\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        // $this->saveDialogResult( __METHOD__, $res );
        $this->assertEquals( $this->res, $res );
    }

    public function testDialog3()
    {
        $this->runDialog( __METHOD__ );

        $res[] = fread( $this->pipes[1], 1024 );

        fputs( $this->pipes[0], "A\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        fputs( $this->pipes[0], "y\n" );
        $res[] = fread( $this->pipes[1], 1024 );
        
        // $this->saveDialogResult( __METHOD__, $res );
        $this->assertEquals( $this->res, $res );
    }
}

?>
