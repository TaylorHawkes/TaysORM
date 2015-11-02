<?php
include("../../autoload.php");


/*you can these settings to meet your needs*/
$host="";//database host
$user="";//database user 
$pass="";//database pass 
$database="";//database name


$model_directory="../../../TModel/"; //this is the directory where your models will be created


/*pry don't edit under here unless you know what your doing....*/
$base_model_directory=$model_directory."Base/";
$options=getopt("",array("table:","host:","user:","pass:","database:"));
$table=$options['table'];

if($options["host"]){ $host=$options["host"]; }
if($options["user"]){ $user=$options["user"]; }
if($options["pass"]){ $pass=$options["pass"]; }
if($options["database"]){$database=$options["database"]; }

$db=EasyConnect::getInstance($host,$user,$pass,$database);


$query="Describe `$table`";

$results=$db->fetchAssoc($query);

$table_new_name=str_replace("_", " ",$table);
$table_new_name=Ucwords($table_new_name);
$table_new_name=str_replace(" ", "",$table_new_name);
$file_content=
'<?php
namespace TModel\Base;
use TaysORM\TaysORMBase;

class '.$table_new_name.'Base extends TaysORMBase {

    public $table_name="'.$table.'";
    public $primary_keys = '.get_primary_key($results).';
    
    public $columns=array( 
    ';

    foreach ($results as $column){
        $field=$column['Field'];
        $datatype=$column['Type'];

        $auto_increment =($column['Extra']=="auto_increment") ? ',"auto_increment"=>true' : "";  
        $primary_key =($column['Key']=="PRI") ? ',"primary_key"=>true' : "" ;

        $file_content.=
            '"'.$field.'"=>array("dataype"=>"'.$datatype.'"'.$auto_increment.$primary_key.'),
        ';
    }

    $file_content.=');
}    
';

$new_file_path= $base_model_directory.$table_new_name."Base.php";

    file_put_contents($new_file_path,$file_content);


//then we just need to build the regular model
$model = 
'<?php
namespace TModel;
use TModel\Base\\'.$table_new_name.'Base;

class '.$table_new_name.' extends '.$table_new_name.'Base
{   

}
';
//only put content if file does not exist
$new_file_path= $model_directory.$table_new_name.".php";

if(!file_exists($new_file_path)) {
    file_put_contents($new_file_path,$model);

}
    
    /*
     *  get the primary key based on results
     */
    function get_primary_key($results)
    {
        $pkeys='array(';
        foreach($results as $column)
        {
            if($column['Key']=="PRI"){
                $pkeys.="'".$column["Field"]."',";
            } 
        } 
        $pkeys=substr($pkeys,0,-1);    
        $pkeys.=')';
            
        return $pkeys; 
    }


?>


