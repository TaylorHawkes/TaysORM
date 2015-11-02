<?php
namespace TModel\Base;
use TaysORM\TaysORMBase;

class UsersBase extends TaysORMBase {

    public $table_name="users";
    public $primary_keys = array('id');
    
    public $columns=array( 
    "id"=>array("dataype"=>"int(11)","auto_increment"=>true,"primary_key"=>true),
        "name"=>array("dataype"=>"varchar(85)"),
        "password"=>array("dataype"=>"varchar(45)"),
        "email"=>array("dataype"=>"varchar(45)"),
        "phone"=>array("dataype"=>"varchar(45)"),
        "account_id"=>array("dataype"=>"varchar(105)"),
        "auth_token"=>array("dataype"=>"varchar(105)"),
        "created_at"=>array("dataype"=>"datetime"),
        "created_with_ip"=>array("dataype"=>"varchar(45)"),
        "phone_cc"=>array("dataype"=>"varchar(45)"),
        "account_closed"=>array("dataype"=>"int(11)"),
        "has_read_e911_notice"=>array("dataype"=>"int(11)"),
        "has_seen_tour"=>array("dataype"=>"int(11)"),
        "email_opt_in"=>array("dataype"=>"int(11)"),
        "is_paying_customer"=>array("dataype"=>"int(11)"),
        "sas_tracking_id"=>array("dataype"=>"varchar(45)"),
        "company"=>array("dataype"=>"varchar(255)"),
        "email_check"=>array("dataype"=>"varchar(65)"),
        "email_check2"=>array("dataype"=>"varchar(65)"),
        "email_is_verified"=>array("dataype"=>"int(11)"),
        "notes"=>array("dataype"=>"varchar(1000)"),
        "channel"=>array("dataype"=>"varchar(255)"),
        "autocharge"=>array("dataype"=>"int(11)"),
        "autocharge_from"=>array("dataype"=>"int(11)"),
        "autocharge_to"=>array("dataype"=>"int(11)"),
        "autocharge_payment_id"=>array("dataype"=>"int(11)"),
        "feature_update_opt_in"=>array("dataype"=>"int(11)"),
        "days_out_of_funds"=>array("dataype"=>"int(11)"),
        "timezone"=>array("dataype"=>"varchar(255)"),
        "api_user"=>array("dataype"=>"varchar(100)"),
        "api_pass"=>array("dataype"=>"varchar(100)"),
        "lock_number_purchases"=>array("dataype"=>"int(11)"),
        );
}    
