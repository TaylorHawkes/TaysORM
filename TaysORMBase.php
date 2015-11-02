<?php
namespace TaysORM;

abstract class TaysORMBase
{
    /*
     * insert row into db,
     * inserts any properties of the object that are set as columns
     * note: does not insert any auto increment fields
     * note: sets the object to the values inserted 
     */ 
    public function insert()
    {

        $connection = EasyConnect::getInstance();        
        $insert_columns='';
        $insert_values='';

        //some extra stuff
        if(array_key_exists("created_at",$this->columns)){
            $this->created_at=date("Y-m-d H:i:s",time());
        }
            
        foreach($this->columns as $column=>$properties){
            if(isset($this->$column) && @!$properties['auto_increment']){
                $insert_columns .= "`".$column. "`,";
                $insert_values .= "'".mysqli_real_escape_string($connection::get_conn(),$this->$column). "',";
            }
        }    

        
          //remore trailing (,)
         $insert_columns=substr($insert_columns,0,-1);
         $insert_values=substr($insert_values,0,-1);

    $sql= "INSERT INTO `".$this->table_name."` (".$insert_columns. ")  VALUES  (".$insert_values.") ";

    $last_insert_id=$connection->query($sql,true);

        //so if this table has an auto increment field we need to set it
        //note: most tables do and usually they are primary key 
        foreach($this->columns as $column=>$properties){
            if(@$properties['auto_increment']){
                    $this->$column= $last_insert_id;
            }
        }    

       return true; 

    
    }

    /*
     * fetches by primary key,
     * @return true - on success | false - on failure
     * sets the propperties of this object 
     *
     * note: does not support multiple column primary keys
     *
     */  
    public function fetchRow($primary_key_id){
         $query= "Select * from `".$this->table_name."` where ".$this->primary_keys[0]." = '".$primary_key_id ."' limit 1";

         $connection = EasyConnect::getInstance();        
         $results=$connection->fetchAssoc($query);

         if(!$results){
            return false; 
         }
            
         foreach($results[0] as $key=>$val){
            $this->$key=$val;
         } 

             return true;

    }
    public function query($query){
         $connection = EasyConnect::getInstance();        
         $results=$connection->query($query);
        return $results;
 
    }
        
    public function fetchAssoc($query){
         $connection = EasyConnect::getInstance();        
        
 $results=$connection->fetchAssoc($query);
            return $results;
    }

    public function fetchAssocPaginate($query){

        $this->paginate_total= count($this->fetchAssoc($query));
        $this->paginate_limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 25;
        $this->paginate_page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : 1;
        $this->paginate_links= ( isset( $_GET['links'] ) ) ? $_GET['links'] : 7; 
        $query .=" LIMIT " . ( ( $this->paginate_page - 1 ) * $this->paginate_limit ) . ", $this->paginate_limit";
        
        return $this->fetchAssoc($query);
}


public function paginateLinks( $links, $list_class ) {
     parse_str($_SERVER["QUERY_STRING"], $get_array);
    foreach($get_array as $key=>$val){
        if(!in_array($key,array("limit","page","links"))){
            $add_qs.="&".$key."=".$val;
        }
    }

    if ( $this->paginate_limit == 'all' ) {
        return '';
    }
 
    $last       = ceil( $this->paginate_total / $this->paginate_limit );
 
    $start      = ( ( $this->paginate_page - $links ) > 0 ) ? $this->paginate_page - $links : 1;
    $end        = ( ( $this->paginate_page + $links ) < $last ) ? $this->paginate_page + $links : $last;
 
    $html       = '<ul class="' . $list_class . '">';
 
    $class      = ( $this->paginate_page == 1 ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->paginate_limit . '&page=' . ( $this->paginate_page - 1 ) . $add_qs.'">&laquo;</a></li>';
 
    if ( $start > 1 ) {
        $html   .= '<li><a href="?limit=' . $this->paginate_limit . '&page=1'.$add_qs.'">1</a></li>';
        $html   .= '<li class="disabled"><span>...</span></li>';
    }
 
    for ( $i = $start ; $i <= $end; $i++ ) {
        $class  = ( $this->paginate_page == $i ) ? "active" : "";
        $html   .= '<li class="' . $class . '"><a href="?limit=' . $this->paginate_limit . '&page=' . $i .$add_qs.'">' . $i . '</a></li>';
    }
 
    if ( $end < $last ) {
        $html   .= '<li class="disabled"><span>...</span></li>';
        $html   .= '<li><a href="?limit=' . $this->paginate_limit . '&page=' . $last .$add_qs.'">' . $last . '</a></li>';
    }
 
    $class      = ( $this->paginate_page == $last ) ? "disabled" : "";
    $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->paginate_limit . '&page=' . ( $this->paginate_page + 1 ) .$add_qs.'">&raquo;</a></li>';
 
    $html       .= '</ul>';
 
    return $html;
}
        
    /*
     * fetches Row where something is the case 
     * set params on myself
     */  
    public function fetchRowWhere($where){
         $query= "Select * from `".$this->table_name."` where ".$where." limit 1";
         $connection = EasyConnect::getInstance();        
         $results=$connection->fetchAssoc($query);
         if(!$results){
            return false; 
         }
            
         foreach($results[0] as $key=>$val){
            $this->$key=$val;
         } 

             return true;

    }


   
    /*
     * dynamicly set properties
     * open to setting anything?? - could cause saving issues?? 
     * this magic method so can dynamicly set properties
     */
    public function __set($key,$value){
        $this->$key= $value; 
    }

    /*
     * this updates a row, clients should just call save
     */   
    public function update(){

        $connection = EasyConnect::getInstance();        
        //so here we get the primary key name
        foreach($this->primary_keys as $pkey_name){
            if(!isset($this->$pkey_name)){
                throw new Exception("Cannot update a row w/o primary key"); 

            }
        }

        
        $update_sets=''; 

        foreach($this->columns as $column=>$properties){
            if(isset($this->$column) && !@$properties['auto_increment'] && !@$properties['primary_key']){
                $update_sets.="`".$column. "`= '".mysqli_real_escape_string($connection::get_conn(), $this->$column)."',";
            }
        }    

        if(empty($update_sets)){
            //no need to update 
            return true;
        }
        
        $update_sets=substr($update_sets,0,-1);
        
        $where_clause= "";
        foreach($this->primary_keys as $key=>$pkey){
            if($key > 0) {
                $where_clause.= " AND ";
            }
            $where_clause.= "`".$pkey."` = '".$this->$pkey."'";
        }

        $sql= "Update `".$this->table_name."` SET  ".$update_sets. "  WHERE  ".$where_clause;

        $connection = EasyConnect::getInstance();        
        $connection->query($sql);

       return true; 

    }


    /*
     * inserts or updates a row depending on if the primary key isset
     * return true on success false on failure
     */ 
    public function save(){
    $sql=''; 
        foreach($this->primary_keys as $key=> $pkey) {
                if(!isset($this->$pkey)){
                        return $this->insert();
                }  
            
                if($key > 0) { $sql.=" AND " ; }
                $sql.= $pkey." = '".$this->$pkey."'";
            }
    
        //this is issue bc we populate ourself
        $MirrorMe=new $this;
        if($MirrorMe->fetchRowWhere($sql)) {
         return   $this->update(); 
        }else{
         return   $this->insert(); 
        }   
           

    }
    public function delete(){

    }
}
