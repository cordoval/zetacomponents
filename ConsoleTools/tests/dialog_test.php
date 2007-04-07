<?php


class ezcConsoleToolsDialogTest extends ezcTestCase
{

    protected $dataDir;

    protected $phpPath;

    protected $output;

    protected $proc;

    protected $pipes = array();

    protected $res = array();

    protected function setUp()
    {
        $this->dataDir = dirname( __FILE__ ) . "/data/" . ( ezcBaseFeatures::os() === "windows" ? "windows" : "posix" );
        $this->phpPath = $_SERVER["_"];
        $this->output  = new ezcConsoleOutput();
        $this->output->formats->test->color = "blue";
    }

    protected function tearDown()
    {
        unset( $this->output );
    }

    protected function runDialog( $methodName )
    {
        $methodName = strtr(
            $methodName,
            array(
                ":" => "_",
            )
        );
        $scriptFile = "{$this->dataDir}/{$methodName}.php";
        $resFile    = "{$this->dataDir}/{$methodName}_res.php";
        if ( !file_exists( $scriptFile ) || !file_exists( $resFile ) )
        {
            throw new RuntimeException( "Missing file $scriptFile or $resFile!" );
        }

        $desc = array(
            0 => array( "pipe", "r" ),  // stdin
            1 => array( "pipe", "w" ),  // stdout
            2 => array( "pipe", "w" )   // stderr
        );
        $this->proc = proc_open("'{$this->phpPath}' '{$scriptFile}'", $desc, $this->pipes );
        $this->res  = require( $resFile );
    }

    protected function closeDialog()
    {
        proc_close( $this->proc );
        unset( $this->pipes, $this->res );
    }

    protected function saveDialogResult( $methodName, $res )
    {
        $methodName = strtr(
            $methodName,
            array(
                ":" => "_",
            )
        );
        $resFile    = "{$this->dataDir}/{$methodName}_res.php";
        file_put_contents(
            $resFile,
            "<?php\n\nreturn " . var_export( $res, true ) . ";\n\n?>"
        );
    }
}

?>
