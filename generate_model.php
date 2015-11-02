<?php
include("../../autoload.php");
//put you db params in here
$db=EasyConnect::getInstance("host","user","pass","db");
//change path to wherever you want you Models
$model_directory="/Users/taylorhawkes/Desktop/roost-server/roost/Model/";
//change path wherever you want your base models
$base_model_directory="/Users/taylorhawkes/Desktop/roost-server/roost/Model/Base/";

$options=getopt("",array("table:"));


$table=$options['table'];



$query="Describe `$table`";

$results=$db->fetchAssoc($query);

$table_new_name=str_replace("_", " ",$table);
$table_new_name=Ucwords($table_new_name);
$table_new_name=str_replace(" ", "",$table_new_name);
$file_content=
'<?php
namespace Model\Base;
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

$new_file_path= $model_directory_base.$table_new_name."Base.php";

    file_put_contents($new_file_path,$file_content);


//then we just need to build the regular model
$model = 
'<?php
namespace Model;
use TaysORM\TaysORMBase\\'.$table_new_name.'Base;

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


