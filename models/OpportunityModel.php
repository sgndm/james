<?php

class OpportunityModel
{ 
 // kumar
   public function __construct() 
   { 
   } 

   static $st_instance = null;
   public static function get()
   {
      if ( OpportunityModel::$st_instance == null  )
         OpportunityModel::$st_instance = new OpportunityModel();
      return OpportunityModel::$st_instance;
   }

   public function createProduct($l_product_type)
   {
      $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $p_product_id= 0;
         $l_res=false;

         $tm =  date('Y-m-d H:i:s');
         $fields= "created_user_id,updated_user_id,product_type,created_ts";
         $values= $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_product_type) . "," .
                  $ctrl->MYSQLQ($tm);
         $sql = "insert into product ( $fields ) values ( $values ) ";
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         { 
               $sql = "select COALESCE(max(product_id) ,0) from product where created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                            " and " .
                           "  created_ts = " . $ctrl->MYSQLQ($tm); 
               $p_product_id = $ctrl->getRecordID($sql);
               if ( $l_product_type == "realestate" )
               {
                   $sql = "insert into product_realestate ( product_id ) values ( $p_product_id ) ";
                   $l_id = $ctrl->execute($sql);
               }
               if ( $l_product_type == "note" )
               {
                   $sql = "insert into product_notes ( product_id ) values ( $p_product_id ) ";
                   $l_id = $ctrl->execute($sql);
               }
         }
         return $p_product_id;
   }  // createProduct()

   public function saveOverview()
   {  
      $ctrl = Controller::get(); 
         $p1data = array();
         $l_user_id= $ctrl->getUserID();
         $l_product_type = $ctrl->getPostParamValue("product_type");
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $product_id=$_POST["product_id"];
         $l_makevisible_fld=$_POST["ovr_makevisible"];
         if ( $product_id == 0 ) 
         {
            $product_id = $this->createProduct($l_product_type);
            $p1data["product_id"]= $product_id;
            if ( $product_id  == 0 )
            {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
               return $p1data;
            }
         } // new product

         $l_makevisible ="N"; 
         if ( $l_makevisible_fld == "true" )
             $l_makevisible ="Y"; 
     

         //kumar changed it it is causing for the user to publish oct 4
         if ( $l_makevisible == "Y" && $l_product_type == "realestate" )
         {
            $sql = "select account_id from account ".
                          "  where product_id     = " . $ctrl->MYSQLQ($product_id) . 
                          "    and product_id >  0  ".
                          "    and isactive = " . $ctrl->MYSQLQ('Y');
            $isval= $ctrl->getRecordID($sql);
            if ( $isval == 0 ) 
            {
               $p1data["warning"]=  "Please enter banking info before publishing ";
               $p1data["message"]=  "Please enter banking info before publishing ";
               $p1data["success"]= "false";
               //return $p1data;
            }
         }

         $fieldlists =  
           " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
           $this->buildwithpost("name","ovr_name");

         $sql= "update product set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
         if ( $l_product_type == "realestate" )
         {
         $fieldlists =  
                     " makevisible = " .  $ctrl->MYSQLQ($l_makevisible). "," .
                     $this->buildwithpost("name","ovr_name") . "," .
                     $this->buildwithpost("area_description_1","ovr_area_description_1") . "," .
                     $this->buildwithpost("area_description_2","ovr_area_description_2") . "," .
                     $this->buildwithpost("area_description_3","ovr_area_description_3") . "," .
                     $this->buildwithpost("area_description_4","ovr_area_description_4") . "," .
                     $this->buildwithpost("area_description_5","ovr_area_description_5") . "," .
                     $this->buildwithpost("area_image_url","ovr_area_image_url") . "," .
                     $this->buildwithpost("offering_size","ovr_offering_size") . "," .
                     $this->buildwithpost("per_share","ovr_per_share") . "," .
                     $this->buildwithpost("no_of_shares","ovr_no_of_shares") . "," .
                     $this->buildwithpost("investment_summary","ovr_investment_summary") . "," .
                     $this->buildwithpost("finance_overview","ovr_finance_overview") ;
             $sql= "update product_realestate set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         }
         if ( $l_product_type == "note" )
         {
         $l_d1 = $ctrl->getPostParamValue("ovr_expiration_date");
         $l_expiration_date = '0000-00-00 00:00:00';
         if ( strlen($l_d1) > 0 ) 
         {
             $l_d2 = date_create($l_d1);
             $l_expiration_date = date_format($l_d2, 'Y-m-d H:i:s');
         }
         $fieldlists =  
                     " makevisible = " .  $ctrl->MYSQLQ($l_makevisible). "," .
                     " expiration_date = " .  $ctrl->MYSQLQ($l_expiration_date). "," .
                     $this->buildwithpost("name","ovr_name") . "," .
                     $this->buildwithpost("rating","ovr_rating") . "," .
                     $this->buildwithpost("loanno","ovr_loanno") . "," .
                     $this->buildwithpost("summary","ovr_summary") . "," .
                     $this->buildwithpost("geography","ovr_geography") . "," .
                     $this->buildwithpost("duration","ovr_duration") . "," .
                     $this->buildwithpost("assettype","ovr_assettype") . "," .
                     $this->buildwithpost("amount","ovr_amount") ;
             $sql= "update product_notes set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         }

         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
                $p1data["product_id"]= $product_id;
                $p1data["message"]= "";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $sql. " " .$ctrl->getServerError();
               $p1data["success"]= "false";
         }
         } // if success 
         return $p1data;
   } // saveInvSummary()

   public function saveFinance()
   {
         $ctrl = Controller::get(); 
         $p1data = array();
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $product_id=$_POST["product_id"];
         $l_product_type = $ctrl->getPostParamValue("product_type");
         if ( $product_id == 0 ) 
         {
            $product_id = $this->createProduct($l_product_type);
            $p1data["product_id"]= $product_id;
            if ( $product_id  == 0 )
            {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
               return $p1data;
            }
         } // new product

         $fieldlists =  
           " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id) ;

         $sql= "update product set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
         if ( $l_product_type == "realestate" )
         {
         $fieldlists =  
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     $this->buildwithpost("total_equity","fin_total_equity") . "," .
                     $this->buildwithpost("private_investor_equity","fin_private_investor_equity") . "," .
                     $this->buildwithpost("fundrise_investor_equity","fin_fundrise_investor_equity") . "," .
                     $this->buildwithpost("developers_equity","fin_developers_equity") . "," .

                     $this->buildwithpost("pref_returnto_investors","fin_pref_returnto_investors") . "," .
                     $this->buildwithpost("developers_promote","fin_developers_promote") . "," .

                     $this->buildwithpost("annual_net_income","fin_annual_net_income") . "," .
                     $this->buildwithpost("total_project_cost","fin_project_budget") . "," .
                     $this->buildwithpost("annual_yield","fin_annual_yield") . "," .

                     $this->buildwithpost("annual_rent_per_sqft","fin_annual_rent_per_sqft") . "," .
                     $this->buildwithpost("building_area_in_sqft","fin_building_area_in_sqft") . "," .

                     $this->buildwithpost("acquisition_cost","fin_acquisition_cost") . "," .
                     $this->buildwithpost("development_cost","fin_development_cost") . "," .

                     $this->buildwithpost("total_hard_cost","fin_total_hard_cost") . "," .
                     $this->buildwithpost("total_soft_cost","fin_total_soft_cost") ;
         $l_success = true;

         $sql= "update product_realestate set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 0 ) 
            $l_success = false;
        
         if ( $l_success ) 
         {
             $sql = $this->updateproductcost($l_user_id,$product_id,"soft");
             $sql = $this->updateproductcost($l_user_id,$product_id,"hard");
         }
         $ds=$_POST["fin_soft_desc_1"];
         if ( $l_success  ) 
         {
                $p1data["product_id"]= $product_id;
                $p1data["message"]= "good one  $sql iu-$l_user_id p=$product_id  ds=$ds";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
         }

       } // realestate
       if ( $l_product_type == "note" )
       {
         $fieldlists =  
                     $this->buildwithpost("collateralvalue","fin_collateralvalue") . "," .
                     $this->buildwithpost("loan_type","fin_loan_type") . "," .
                     $this->buildwithpost("downpayment_percent","fin_downpayment_percent") . "," .
                     $this->buildwithpost("loantocostratio_percent","fin_loantocostratio_percent") . "," .
                     $this->buildwithpost("ltvratio_percent","fin_ltvratio_percent") . "," .
                     $this->buildwithpost("coupon_percent","fin_coupon_percent") . "," .
                     $this->buildwithpost("borrower_status","fin_borrower_status") ;
         $l_success = true;

         $sql= "update product_notes set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 0 ) 
            $l_success = false;
       
         #$p1data["message"]= $sql;
         #$p1data["success"]= "false";
         #return  $p1data;
        
         if ( $l_success  ) 
         {
                $p1data["product_id"]= $product_id;
                $p1data["message"]= "good one ";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $sql . " " . $ctrl->getServerError();
               $p1data["success"]= "false";
         }

       } // 
       } // if success 
         return $p1data;
   } // saveFinance()

   public function saveProperty()
   {
      $ctrl = Controller::get(); 
         $p1data = array();
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $product_id=$_POST["product_id"];
         $l_product_type = $ctrl->getPostParamValue("product_type");
         if ( $product_id == 0 ) 
         {
            $product_id = $this->createProduct($l_product_type);
            $p1data["product_id"]= $product_id;
            if ( $product_id  == 0 )
            {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
               return $p1data;
            }
         } // new product

         if ( $l_product_type == "realestate" )
         {
         $fieldlists =  
                     $this->buildwithpost("development_plans","prop_development_plans") . "," .
                     $this->buildwithpost("development_image_url","prop_development_image_url") . "," .
                     $this->buildwithpost("image_url","prop_image_url") . "," .
                     $this->buildwithpost("description_1","prop_description_1") . "," .
                     $this->buildwithpost("description_2","prop_description_2") . "," .
                     $this->buildwithpost("description_3","prop_description_3") . "," .
                     $this->buildwithpost("description_4","prop_description_4") . "," .
                     $this->buildwithpost("description_5","prop_description_5") . "," .

                     $this->buildwithpost("highlight_image_url","prop_highlight_image_url") . "," .
                     $this->buildwithpost("highlight_description_1","prop_highlight_description_1") . "," .
                     $this->buildwithpost("highlight_description_2","prop_highlight_description_2") . "," .
                     $this->buildwithpost("highlight_description_3","prop_highlight_description_3") . "," .
                     $this->buildwithpost("highlight_description_4","prop_highlight_description_4") . "," .
                     $this->buildwithpost("highlight_description_5","prop_highlight_description_5") . "," .

                     $this->buildwithpost("timeline_description_1","prop_timeline_description_1") . "," .
                     $this->buildwithpost("timeline_completiondate_1","prop_timeline_completiondate_1") . "," .
                     $this->buildwithpost("timeline_description_2","prop_timeline_description_2") . "," .
                     $this->buildwithpost("timeline_completiondate_2","prop_timeline_completiondate_2")  . "," .
                     $this->buildwithpost("timeline_description_3","prop_timeline_description_3") . "," . 
                     $this->buildwithpost("timeline_completiondate_3","prop_timeline_completiondate_3") . "," .
                     $this->buildwithpost("timeline_description_4","prop_timeline_description_4") . "," .
                     $this->buildwithpost("timeline_completiondate_4","prop_timeline_completiondate_4")  . "," .
                     $this->buildwithpost("timeline_description_5","prop_timeline_description_5") . "," .
                     $this->buildwithpost("timeline_completiondate_5","prop_timeline_completiondate_5") ;

         $sql= "update product_realestate set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         }
         if ( $l_product_type == "note" )
         {

         $fieldlists =  
                     $this->buildwithpost("address1","prop_address1") . "," .
                     $this->buildwithpost("address2","prop_address2") . "," .
                     $this->buildwithpost("city","prop_city") . "," .
                     $this->buildwithpost("zipcode","prop_zipcode") . "," .
                     $this->buildwithpost("state","prop_state") . "," .
                     $this->buildwithpost("country","prop_country") . "," .
                     $this->buildwithpost("nooftimes_borrowed","prop_nooftimes_borrowed") . "," .
                     $this->buildwithpost("phone","prop_phone") . "," .
                     $this->buildwithpost("borrower_type","prop_borrower_type");

         $sql= "update product_notes set  " . $fieldlists . 
                       " where product_id  = " . $ctrl->MYSQLQ($product_id);
         $l_id = $ctrl->execute($sql);
         }

         if ( $l_id == 1 ) 
         {
                $p1data["product_id"]= $product_id;
                $p1data["message"]= "good one  $sql ";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $sql. $ctrl->getServerError();
               $p1data["success"]= "false";
         }
         return $p1data;
   } // saveProperty()

   public function getAllNotesProducts()
   {
       return $this->getAllNotesProducts_select("");
   }
   public function getAllNotesProducts_select($p_filter)
   {
      $ctrl = Controller::get(); 
 
         $p_filter=trim($p_filter);

         $p1data = array();

          $user_id =$ctrl->getUserID();
          $isadmin =$ctrl->getIsAdmin();
          if ( $user_id > 0 && $isadmin  == "Y" )
              $sql = "select * from product_notes where product_id in ( select product_id from product where isactive = 'Y' and product_type = 'note' )";
          else
              $sql = "select * from product_notes where makevisible = 'Y' and product_id in ( select product_id from product where isactive = 'Y' and product_type = 'note' )";
         if ( strlen($p_filter) > 0 ) 
                 $sql .= " and " . $p_filter;
         $sql .= " and expiration_date >= CURRENT_TIMESTAMP  ";
          $l_record = $ctrl->query($sql);
          return $l_record;
   } // 

   public function getPaidLoansProducts()
   {
      return $this->getPaidLoanProducts_select("ALL",0);
   } // 

   public function getPaidLoanProducts_select($p_type,$p_value)
   {
      $ctrl = Controller::get(); 

         $p1data = array();

         $l_user_id =$ctrl->getUserID();

         $sql = " select a.product_id, c.loanno , c.name, b.amount user_amount , b.user_id,b.created_ts , ".
                   "     c.coupon_percent,c.amount loan_amount ,u.email, u.first_name ,u.last_name,".
                   " ( ( b.amount /c.amount ) * 100 ) ownership_percent, ".
                   " c.name, c.address1,c.address2,c.city,c.state,c.zipcode,c.country ,c.phone ".
                   "  from product a , user_payment b , product_notes c ,user u".
                   "  where a.product_id = b.product_id ".
                   "    and a.product_id = c.product_id " .
                   "    and b.created_user_id = u.id  ";
          if ( $ctrl->getIsAdmin() != "Y" )
                $sql .= "    and b.user_id  = $l_user_id " ;

          if ( $p_type == "LOANNO" && strlen($p_value) > 0 )
          {
             $sql .=  "and c.loanno  like " . $p_value ;
          }
          if ( $p_type == "PRODUCT_ID" ) 
          {
             $sql .= "    and a.product_id  = $p_value ";
          }
          $sql .= " order by b.created_ts desc";
          $l_record = $ctrl->query($sql);
          return $l_record;
   } // 

   public function getAllOpportunityProducts()
   {
         $ctrl = Controller::get(); 

         $p1data = array();

          $user_id =$ctrl->getUserID();
          $isadmin =$ctrl->getIsAdmin();
          if ( $user_id > 0 && $isadmin  == "Y" )
              $sql = "select * from product_realestate where product_id in ( select product_id from product where isactive = 'Y' and product_type = 'realestate' )";
          else
              $sql = "select * from product_realestate where makevisible = 'Y' and product_id in ( select product_id from product where isactive = 'Y' and product_type = 'realestate' )";
          $l_record = $ctrl->query($sql);

          return $l_record;
   } // 

   public function getPaidOpportunityProducts()
   {
         $ctrl = Controller::get(); 

         $p1data = array();

         $l_user_id =$ctrl->getUserID();

         $sql = "select a.*, p.amount user_amount, p.envelope_id ,  p.envelope_status , p.created_ts user_created_ts " .
             " from user_payment p , product_realestate a " . 
             " where a.product_id in ( select product_id from product where isactive = 'Y' and product_type = 'realestate' )  ".
             " and a.product_id = p.product_id " . 
             " and p.user_id = $l_user_id and p.envelope_status = 'Completed'  ".
             " order by p.created_ts desc ";

          $l_record = $ctrl->query($sql);

          return $l_record;
   } // 

   public function getProduct($p_product_id)
   {
         $ctrl = Controller::get(); 
          $sql = "select * from product where product_id = " . $ctrl->MYSQLQ($p_product_id) ;
          $l_record = $ctrl->getRecord($sql);
          if ( $l_record["product_type"] == "realestate" ) 
              return $this->getRealEstateProduct($p_product_id);
          if ( $l_record["product_type"] == "note" ) 
              return $this->getLoanProduct($p_product_id);
   }
   public function getRealEstateProduct($p_product_id)
   {
         $ctrl = Controller::get(); 
         $p1data = array();

         // Needs init
          $sql = "select * from product_realestate where product_id = " . $ctrl->MYSQLQ($p_product_id) ;
          $l_record = $ctrl->getRecord($sql);

          $sql = "select * from product_realestate_cost where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and cost_type = 'soft' ";
          $l_record["soft_cost"] = $ctrl->query($sql);

          $sql = "select * from product_realestate_cost where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and cost_type = 'hard' ";
          $l_record["hard_cost"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'area' order by file_id desc ";
          $l_record["uploads_area"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'property' order by file_id desc ";
          $l_record["uploads_property"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'document' order by file_id desc ";
          $l_record["uploads_document"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'highlight' order by file_id desc ";
          $l_record["uploads_highlight"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'development' order by file_id desc ";
          $l_record["uploads_development"] = $ctrl->query($sql);

          return $l_record;
     } // getProduct($p_product_id)

     public function getLoanProduct($p_product_id)
     {
         $ctrl = Controller::get(); 
         $p1data = array();

         // Needs init
          $sql = "select * from product_notes where product_id = " . $ctrl->MYSQLQ($p_product_id) ;
          $l_record = $ctrl->getRecord($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'property' order by file_id desc ";
          $l_record["uploads_property"] = $ctrl->query($sql);

          $sql = "select * from product_uploads where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                 " and isactive = 'Y' ".
                 " and doc_type = 'document' order by file_id desc ";
          $l_record["uploads_document"] = $ctrl->query($sql);

          return $l_record;
     } // getProduct($p_product_id)

     public function  buildwithpost($fld,$id)
     {
         $ctrl = Controller::get(); 
         return $this->buildwithval($fld,$ctrl->getPostParamValue($id));
     }
     public function  buildwithval($fld,$val)
     {
         return " $fld = "  . $this->MYSQLQ($val);
     }
     public function Q($val) 
     { 
        return "'". $val. "'";
     } 
      
   public function MYSQLQ($val) 
   {     
      //$val=mysql_real_escape_string($val);
      return "'". $val. "'";
   }

  public function createUploads($p1data)
  {
         $ctrl = Controller::get(); 
         $file_name  = $p1data["file_name"];
         $dx  = $p1data["doc_name"];
         $noofitems  = $p1data["noofitems"];
         $dir_path = $p1data["dir_path"];
         $full_path = $p1data["full_path"];
         $l_doc_type = $p1data["doc_type"];
         $size = $p1data["size"];
         $product_id = $p1data["product_id"];
         $l_user_id = $p1data["user_id"];
         $tm =  date('Y-m-d H:i:s');
         $p1data = array();
         $l_product_type = $ctrl->getPostParamValue("product_type");

         if (  $product_id == 0 ) 
         {
            $product_id = $this->createProduct($l_product_type);
            if ( $product_id  == 0 )
            {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
               return $p1data;
            }
            $p1data["product_id"]= $product_id;
         } // new product

         $fields= "file_name,doc_name,dir_path,full_path,doc_type,size,created_user_id,product_id,created_ts";
         $values= $ctrl->MYSQLQ($file_name) . "," .
                  $ctrl->MYSQLQ($dx) . "," .
                  $ctrl->MYSQLQ($dir_path) . "," .
                  $ctrl->MYSQLQ($full_path) . "," .
                  $ctrl->MYSQLQ($l_doc_type) . "," .
                  $ctrl->MYSQLQ($size) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($product_id) . "," .
                  $ctrl->MYSQLQ($tm) ;
         $sql = "insert into product_uploads ( $fields ) values ( $values ) ";
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         { 
               $sql = "select COALESCE(max(file_id) ,0) from product_uploads " . 
                              " where  created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                                 " and created_ts = " . $ctrl->MYSQLQ($tm); 
               $l_id = $ctrl->getRecordID($sql);
               $p1data["file_id"]= $l_id;
               $p1data["doc_type"]= $l_doc_type;
               $p1data["message"]= "file added ". $sql;
               $p1data["success"]= "true";
               $l_str=$this->getUploadfileslist($product_id,$l_doc_type);
               $p1data["list"]= $l_str;
         }
         else
         {
               $p1data["message"]= $ctrl->getServerError(). $sql;
              $p1data["success"]= "false";
         }
         return $p1data;
   }  // createUploads($p1data)

   public function deactivateFile()
   {
         $ctrl = Controller::get(); 
         $l_doc_type=$_POST["doc_type"];
         $p_product_id=$_POST["product_id"];
         $l_file_id=$_POST["file_id"];
         $p1data = array();

         // Needs init
          $sql = "update product_uploads set isactive = 'N' where product_id = " . $ctrl->MYSQLQ($p_product_id) .
                             " and file_id = " . $ctrl->MYSQLQ($l_file_id) ;
          $l_id = $ctrl->execute($sql);
          if ( $l_id > 0 ) 
          {
                 $p1data["message"]= "file deactivated";
                 $p1data["success"]= "true";
                 $l_str=$this->getUploadfileslist($p_product_id,$l_doc_type);
                 $p1data["list"]= $l_str;
                 $p1data["doc_type"]= $l_doc_type;
          }
          else
          {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
          }
          return $p1data;
   } // 

   public function getUploadfiles($p_product_id,$p_type="")
   {
         $ctrl = Controller::get(); 
         $p1data = array();

         // Needs init
          $sql = "select * from product_uploads  where product_id = " . $ctrl->MYSQLQ($p_product_id)  .
                           " and  isactive =  " . $ctrl->MYSQLQ("Y");
          if ( strlen($p_type) > 0 ) 
                   $sql .= " and  doc_type = " .  $ctrl->MYSQLQ($p_type) ;
          $sql .= " order by file_id desc ";
          $l_record = $ctrl->query($sql);
          return $l_record;
   } // 

   public function getUploadfileslist($p_product_id,$p_type="")
   {
         $ctrl = Controller::get(); 
         $l_result = $this->getUploadfiles($p_product_id,$p_type);
         $l_str="";
         $l_all_str="";
         $cnt = count($l_result);
    if ( $cnt > 0  && $l_result[0][0] > 0 ) 
    {
       for ( $idx=0; $idx<$cnt;$idx++)
       {
              $l_record=$l_result[$idx];
              $l_doc_type = $l_record["doc_type"];
              $file_name = $l_record["file_name"];
              $dx = $l_record["doc_name"];
              if ( $dx == null || $dx == "" || strlen($dx) == 0 ) 
                  $dx = $file_name;
              $blnk="&nbsp;";
              $blnk.="&nbsp;";
              $blnk.="&nbsp;";
              $isactive = $l_record["isactive"];
              $id = $l_record["file_id"];
              if ( $p_type !=  "document" )
              {
                   $image_fl="http://" . $_SERVER["SERVER_NAME"] . "/uploads/".$p_product_id."/".$file_name;
		   $d1 =" <img class='investmentsicon' src='$image_fl' />";
                   $l_str = $d1 .'<button class=\'btn\' onclick="deactiveatefile(\''.$l_doc_type.'\',\''. $p_product_id . '\',\'' . $id . '\')">Delete</button>';
                    $l_all_str .= $l_str;
              }
              else
              {
                   $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                   $fl="other.png";
                   $ext = strtolower($ext);
                   if ( $ext == "pdf" )
                      $fl="pdf.png";
                   if ( $ext == "doc" | $ext == "docx" )
                      $fl="word.png";
                  $image_fl="http://" . $_SERVER["SERVER_NAME"] . "/assets/images/". $fl;
		  $d1 =" <tr><td><img class='investmentstiny' src='$image_fl' />" ."</td>";
                  $l_str = $d1 . "<td>".  $dx . "</td><td>".'<button class=\'btn\' onclick="deactiveatefile(\''.$l_doc_type.'\',\''. $p_product_id . '\',\'' . $id . '\')">Delete</button></td></tr>';
                  $l_all_str .= $l_str;
   
              }
       }
       if ( $p_type ==  "document" )
           $l_all_str = "<table>" .$l_all_str . "</table>";
    }
    return $l_all_str;
    } //  getUploadfileslist($p_product_id,$p_type="")

   public function updateproductcost($l_user_id,$product_id,$l_cost_type)
   {
         $res=true;
         $tm =  date('Y-m-d H:i:s');
         $ctrl = Controller::get(); 
         /**  fixing hard cost **/
         if ( $l_cost_type == "soft" ) 
            $totrows = $_POST["fin_soft_table_rows"];
         else
            $totrows = $_POST["fin_hard_table_rows"];

         for ( $idx=1;$idx<=$totrows;$idx++)
         {
               if ( $l_cost_type == "soft" ) 
                   $fldid   = "fin_soft_desc_" . $idx;
               else
                   $fldid   = "fin_hard_desc_" . $idx;
               $desc = $_POST[$fldid];

               if ( $l_cost_type == "soft" ) 
                  $fldid   = "fin_soft_cost_" . $idx;
               else
                  $fldid   = "fin_hard_cost_" . $idx;

               $cost = $_POST[$fldid];

              $sql = "select slno from product_realestate_cost " .  
                       "  where product_id  = " . $ctrl->MYSQLQ($product_id) . 
                       "    and cost_type   = " . $ctrl->MYSQLQ($l_cost_type) . 
                       "    and slno   = " . $idx;
               $l_slno = $ctrl->getRecordID($sql);
               if ( $l_slno == $idx ) 
               {
                   $fields= " description = " .  $ctrl->MYSQLQ($desc)  . ","  .
                       " cost   = " .  $ctrl->MYSQLQ($cost)  . ","  .
                       " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id);

                    $sql = "update product_realestate_cost set " .  $fields  . 
                             "  where product_id  = " . $ctrl->MYSQLQ($product_id) . 
                             "    and cost_type   = " . $ctrl->MYSQLQ($l_cost_type) . 
                             "    and slno   = " . $idx;
                     $l_id = $ctrl->execute($sql);
               }
               else
               {
                     $fields= "product_id,slno,description,cost,cost_type,created_user_id,updated_user_id,created_ts";
                     $values= $ctrl->MYSQLQ($product_id) . "," .
                              $ctrl->MYSQLQ($idx) . "," .
                              $ctrl->MYSQLQ($desc) . "," .
                              $ctrl->MYSQLQ($cost) . "," .
                              $ctrl->MYSQLQ($l_cost_type) . "," .
                              $ctrl->MYSQLQ($l_user_id) . "," .
                              $ctrl->MYSQLQ($l_user_id) . "," .
                              $ctrl->MYSQLQ($tm);
                     $sql = "insert into product_realestate_cost ( $fields ) values ( $values ) ";
                     $l_id = $ctrl->execute($sql);
                }
         }
     return $sql. "fldid=".$fldid;
     } // updateproductcost($product_id,$l_cost_type)

   public function getsharePercent($availed,$offered)
   {
       $l_res = 0;
       if ( $availed  > 0 ) 
       {
             if ( $availed > 0 ) 
                if ( $availed > 0 &&  $offered > 0 ) 
                $l_res = ($availed  / $offered ) * 100;
             if ( $l_res > 100 )
                $l_res = 100;
             $l_res = number_format( $l_res , 2);
       }
       return $l_res;
     }

   public function savemybank()
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $p_account_id= $_POST["account_id"];
         $l_res=false;
         $p1data = array();
         $p1data["message"]= "None";
         $p1data["success"]= "false";

         $l_name = $ctrl->getPostParamValue("invest_name");
         $l_taxid = $ctrl->getPostParamValue("invest_taxid");
         $l_address1 = $ctrl->getPostParamValue("invest_address1");
         $l_address2 = $ctrl->getPostParamValue("invest_address2");
         $l_city = $ctrl->getPostParamValue("invest_city");
         $l_state = $ctrl->getPostParamValue("invest_state");
         $l_zipcode = $ctrl->getPostParamValue("invest_zipcode");
         $l_country = $ctrl->getPostParamValue("invest_country");
         $l_bankname = $ctrl->getPostParamValue("invest_bankname");
         $l_nick_name = $ctrl->getPostParamValue("invest_nick_name");
         $l_bankroutingno = $ctrl->getPostParamValue("invest_bankroutingno");
         $l_bankaccountno = $ctrl->getPostParamValue("invest_bankaccountno");
         $l_inter_bankname  = $ctrl->getPostParamValue("invest_intermediate_bankname");
         $l_inter_bankaccno  = $ctrl->getPostParamValue("invest_intermediate_bankaccno");
         $l_account_type  = $ctrl->getPostParamValue("invest_account_type");
         $l_comments  = $ctrl->getPostParamValue("invest_comments");
         $tm =  date('Y-m-d H:i:s');

         if (  $p_account_id == 0 ) 
         {
            $fields= "created_user_id,updated_user_id,created_ts,user_id,account_type,".
                     "name,taxid,address1,address2,city,state,zipcode,country,".
                     "nick_name,bankname,bankaccountno,bankroutingno,inter_bankname,comments,inter_bankaccno";
            $values= $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($tm)  . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_account_type) . "," .
                  $ctrl->MYSQLQ($l_name) . "," .
                  $ctrl->MYSQLQ($l_taxid) . "," .
                  $ctrl->MYSQLQ($l_address1) . "," .
                  $ctrl->MYSQLQ($l_address2) . "," .
                  $ctrl->MYSQLQ($l_city) . "," .
                  $ctrl->MYSQLQ($l_state) . "," .
                  $ctrl->MYSQLQ($l_zipcode) . "," .
                  $ctrl->MYSQLQ($l_country) . "," .
                  $ctrl->MYSQLQ($l_nick_name) . "," .
                  $ctrl->MYSQLQ($l_bankname) . "," .
                  $ctrl->MYSQLQ($l_bankaccountno) . "," .
                  $ctrl->MYSQLQ($l_bankroutingno) . "," .
                  $ctrl->MYSQLQ($l_inter_bankname) . "," .
                  $ctrl->MYSQLQ($l_comments) . "," .
                  $ctrl->MYSQLQ($l_inter_bankaccno);

            $sql = "insert into account ( $fields ) values ( $values ) ";
            $l_id = $ctrl->execute($sql);
            if ( $l_id == 1 ) 
            { 
                  $sql = "select COALESCE(max(account_id) ,0) from account where created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                               " and " .
                              "  created_ts = " . $ctrl->MYSQLQ($tm); 
                  $p_account_id = $ctrl->getRecordID($sql);
                  $p1data["account_id"]= $p_account_id;
                  $p1data["success"]= "true";
            }
         }
         else
         {
                  $fieldlists =  
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " name = " .  $ctrl->MYSQLQ($l_name). "," .
                     " taxid = " .  $ctrl->MYSQLQ($l_taxid). "," .
                     " address1 = " .  $ctrl->MYSQLQ($l_address1). "," .
                     " address2 = " .  $ctrl->MYSQLQ($l_address2). "," .
                     " city = " .  $ctrl->MYSQLQ($l_city). "," .
                     " state = " .  $ctrl->MYSQLQ($l_state). "," .
                     " zipcode = " .  $ctrl->MYSQLQ($l_zipcode). "," .
                     " country = " .  $ctrl->MYSQLQ($l_country). "," .
                     " nick_name = " .  $ctrl->MYSQLQ($l_nick_name). "," .
                     " bankname = " .  $ctrl->MYSQLQ($l_bankname). "," .
                     " bankaccountno = " .  $ctrl->MYSQLQ($l_bankaccountno). "," .
                     " bankroutingno = " .  $ctrl->MYSQLQ($l_bankroutingno). "," .
                     " inter_bankname = " .  $ctrl->MYSQLQ($l_inter_bankname). "," .
                     " comments       = " .  $ctrl->MYSQLQ($l_comments). "," .
                     " account_type       = " .  $ctrl->MYSQLQ($l_account_type). "," .
                     " inter_bankaccno = " .  $ctrl->MYSQLQ($l_inter_bankaccno);

         $sql= "update account set  " . $fieldlists . 
                       " where account_id  = " . $ctrl->MYSQLQ($p_account_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
                $p1data["message"]= "Saved";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
         }

         }
         return $p1data;
   }  // createAccount()

   public function savebanking()
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $p_account_id= $_POST["account_id"];
         $l_res=false;
         $p1data = array();
         $p1data["message"]= "None";
         $p1data["success"]= "false";

         $l_product_id = $ctrl->getPostParamValue("product_id");
         $l_product_type = $ctrl->getPostParamValue("product_type");

         if ( $l_product_id == 0 ) 
         {
            $l_product_id = $this->createProduct($l_product_type);
            $p1data["product_id"]= $l_product_id;
            if ( $l_product_id  == 0 )
            {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
               return $p1data;
            }
         } // new product
         $l_bankname = $ctrl->getPostParamValue("invest_bankname");
         $l_nick_name = $ctrl->getPostParamValue("invest_nick_name");
         $l_bankroutingno = $ctrl->getPostParamValue("invest_bankroutingno");
         $l_bankswiftibanno = $ctrl->getPostParamValue("invest_bankswiftibanno");
         $l_bankaccountno = $ctrl->getPostParamValue("invest_bankaccountno");
         $l_comments  = $ctrl->getPostParamValue("invest_comments");
         $l_comments  = trim($l_comments);
         $tm =  date('Y-m-d H:i:s');
         $l_address1 = $ctrl->getPostParamValue("invest_address1");
         $l_address2 = $ctrl->getPostParamValue("invest_address2");
         $l_city = $ctrl->getPostParamValue("invest_city");
         $l_state = $ctrl->getPostParamValue("invest_state");
         $l_zipcode = $ctrl->getPostParamValue("invest_zipcode");
         $l_country = $ctrl->getPostParamValue("invest_country");

         if (  $p_account_id == 0 ) 
         {
            $fields= "created_user_id,updated_user_id,created_ts,product_id," .
                     "address1,address2,city,state,zipcode,country,".
                     "comments,nick_name,bankname,bankaccountno,bankswiftibanno,bankroutingno";
            $values= $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($tm)  . "," .
                  $ctrl->MYSQLQ($l_product_id) . "," .
                  $ctrl->MYSQLQ($l_address1) . "," .
                  $ctrl->MYSQLQ($l_address2) . "," .
                  $ctrl->MYSQLQ($l_city) . "," .
                  $ctrl->MYSQLQ($l_state) . "," .
                  $ctrl->MYSQLQ($l_zipcode) . "," .
                  $ctrl->MYSQLQ($l_country) . "," .
                  $ctrl->MYSQLQ($l_comments) . "," .
                  $ctrl->MYSQLQ($l_nick_name) . "," .
                  $ctrl->MYSQLQ($l_bankname) . "," .
                  $ctrl->MYSQLQ($l_bankaccountno) . "," .
                  $ctrl->MYSQLQ($l_bankswiftiban) . "," .
                  $ctrl->MYSQLQ($l_bankroutingno);

            $sql = "insert into account ( $fields ) values ( $values ) ";
            $sql1 = $sql;
            $l_id = $ctrl->execute($sql);
            if ( $l_id == 1 ) 
            { 
                  $sql = "select COALESCE(max(account_id) ,0) from account where created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                               " and " .
                              "  created_ts = " . $ctrl->MYSQLQ($tm); 
                  $p_account_id = $ctrl->getRecordID($sql);
                  $p1data["account_id"]= $p_account_id;
                  $p1data["success"]= "true";
                  $p1data["message"]= "goodone sql =" . $sql1;
            }
         }
         else
         {
                  $fieldlists =  
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " bankname = " .  $ctrl->MYSQLQ($l_bankname). "," .
                     " nick_name = " .  $ctrl->MYSQLQ($l_nick_name). "," .
                     " bankaccountno = " .  $ctrl->MYSQLQ($l_bankaccountno). "," .
                     " bankroutingno = " .  $ctrl->MYSQLQ($l_bankroutingno). "," .
                     " bankswiftibanno = " .  $ctrl->MYSQLQ($l_bankswiftibanno). "," .
                     " address1 = " .  $ctrl->MYSQLQ($l_address1). "," .
                     " address2 = " .  $ctrl->MYSQLQ($l_address2). "," .
                     " city = " .  $ctrl->MYSQLQ($l_city). "," .
                     " state = " .  $ctrl->MYSQLQ($l_state). "," .
                     " zipcode = " .  $ctrl->MYSQLQ($l_zipcode). "," .
                     " country = " .  $ctrl->MYSQLQ($l_country). "," .
                     " comments       = " .  $ctrl->MYSQLQ($l_comments);

         $sql= "update account set  " . $fieldlists . 
                       " where account_id  = " . $ctrl->MYSQLQ($p_account_id) . 
                       "   and product_id  = " . $ctrl->MYSQLQ($l_product_id);
         $l_id = $ctrl->execute($sql);
         if ( $l_id == 1 ) 
         {
                $p1data["message"]= "Saved";
                $p1data["success"]= "true";
         }
         else
         {
               $p1data["message"]= $ctrl->getServerError();
               $p1data["success"]= "false";
         }

         }
         return $p1data;
   }  // createAccount()
   public function getAccountList()
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $sql = "select *  from account ".
                       "  where user_id     = " . $ctrl->MYSQLQ($l_user_id) .
                       "    and isactive      = " . $ctrl->MYSQLQ('Y') .
                       "  order by account_type, bankname,bankaccountno";
         return $ctrl->query($sql);
   }
   public function getAccountForProduct($p_product_id)
   {
         $ctrl = Controller::get(); 
         $sql = "select * from account ".
                       "  where product_id     = " . $ctrl->MYSQLQ($p_product_id) . 
                       "    and product_id >  0  ".
                       "    and isactive = " . $ctrl->MYSQLQ('Y');
         return $ctrl->getRecord($sql);
         //return $sql;
    }
   public function getAccount($p_account_id)
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $sql = "select * from account ".
                       "  where user_id     = " . $ctrl->MYSQLQ($l_user_id) . 
                       "    and account_id  = " . $ctrl->MYSQLQ($p_account_id) ;
         return $ctrl->getRecord($sql);
    }

   public function savePayment($l_account_id,$l_amount,$l_envelope_id,$l_envelope_status,$l_product_id,$l_document)
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $l_res=false;
         $p1data = array();
         $p1data["message"]= "None";
         $p1data["success"]= "false";

         $tm =  date('Y-m-d H:i:s');
         $sql = "select payment_id  from user_payment where product_id = " . $ctrl->MYSQLQ($l_product_id) . 
              " and user_id = " . $ctrl->MYSQLQ($l_user_id) ;
         $l_payment_id = $ctrl->getRecordID($sql);

          if ( $l_payment_id == 0 ) 
          {
         $fields= "created_user_id,updated_user_id,created_ts,user_id,account_id,".
                     "document_file,envelope_id,envelope_status,signed_ts,product_id,amount";
          $values= $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($tm)  . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_account_id) . "," .
                  $ctrl->MYSQLQ($l_document) . "," .
                  $ctrl->MYSQLQ($l_envelope_id) . "," .
                  $ctrl->MYSQLQ($l_envelope_status) . "," .
                  $ctrl->MYSQLQ($tm) . "," .
                  $ctrl->MYSQLQ($l_product_id) . "," .
                  $ctrl->MYSQLQ($l_amount) ;

        $sql = "insert into user_payment ( $fields ) values ( $values ) ";
        $l_id = $ctrl->execute($sql);
        if ( $l_id == 1 ) 
        { 
                  $sql = "select COALESCE(max(payment_id) ,0) from user_payment where created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                               " and " .
                              "  created_ts = " . $ctrl->MYSQLQ($tm); 
                  $p_payement = $ctrl->getRecordID($sql);
                  $p1data["payement"]= $p_payement;
                  $p1data["success"]= "true";

                  $sql=
                            "update product_realestate set " . 
                            "   no_of_shareholders =  ( select count(distinct user_id ) " .
                            " from user_payment ". 
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id)  .
                               "   and isactive  =  " .  $ctrl->MYSQLQ('Y') . ")" .
                            "   ,offering_availed =  ( select sum(amount) " .
                            " from user_payment ". 
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id)  .
                               "   and isactive  =  " .  $ctrl->MYSQLQ('Y') . ")" .
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id);
                  $lctr = $ctrl->execute($sql);
                  if ( $lctr == 1 ) 
                  {
                         $p1data["message"]= "Saved";
                         $p1data["success"]= "true";
                  }
                  else
                  {
                        $p1data["message"]= $ctrl->getServerError();
                        $p1data["success"]= "false";
                        return $p1data;
                  }
          }
         } // l_payment_id == 0 
         else
         {
                  $fieldlists =  
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " account_id = " .  $ctrl->MYSQLQ($l_account_id). "," .
                     " document_file = " .  $ctrl->MYSQLQ($l_document). "," .
                     " envelope_id = " .  $ctrl->MYSQLQ($l_envelope_id). "," .
                     " envelope_status = " .  $ctrl->MYSQLQ($l_envelope_status). "," .
                     " signed_ts = " .  $ctrl->MYSQLQ($tm). "," .
                     " product_id = " .  $ctrl->MYSQLQ($l_product_id). "," .
                     " amount = " .  $ctrl->MYSQLQ($l_amount) ;

                 $sql= "update user_payment set  " . $fieldlists . 
                               " where payment_id  = " . $ctrl->MYSQLQ($l_payment_id);
                 $l_id = $ctrl->execute($sql);
                 if ( $l_id == 1 ) 
                 {
                        $p1data["message"]= "Saved";
                        $p1data["success"]= "true";
                 }
                 else
                 {
                       $p1data["message"]= $ctrl->getServerError();
                       $p1data["success"]= "false";
                 }
         }
         return $p1data;
   }  // createPayment()

   public function loginplease()
   {
         $p1data = array();
         $p1data["message"]= "Login session expired ... please sign in";
         $p1data["success"]= "false";
         return $p1data;
   }
   public function isValidProduct($p_product_id)
   {
         $ctrl = Controller::get(); 
         $sql = "select COALESCE(max(product_id) ,0) from product where product_id = " . $ctrl->MYSQLQ($p_product_id);
         $id = $ctrl->getRecordID($sql);
         if ( $id == $p_product_id ) 
            return True;
         else
           return False;
   }  // isValidProduct()

   public function getPaidRealestateProducts_select($p_product_id)
   {
      $ctrl = Controller::get(); 

         $p1data = array();

         $l_user_id =$ctrl->getUserID();

         $sql = " select a.product_id, c.name, b.amount user_amount , b.user_id,b.created_ts , ".
                   "   u.first_name ,u.last_name".
                   "  from product a , user_payment b , product_realestate c ,user u".
                   "  where a.product_id = b.product_id ".
                   "    and a.product_id = c.product_id " .
                   "    and b.created_user_id = u.id  ";
          if ( $ctrl->getIsAdmin() != "Y" )
                $sql .= "    and b.user_id  = $l_user_id " ;

          if ( $p_product_id != -1 ) 
                   $sql .= "    and a.product_id  = $p_product_id ";
          $sql .= " order by a.product_id, c.name,b.created_ts desc";
          $l_record = $ctrl->query($sql);
          return $l_record;
   } // 
   public function getPaidRealestateProducts()
   {
      return $this->getPaidRealestateProducts_select(-1);
   } // 
   public function investnote()
   {
         $ctrl = Controller::get(); 
         $l_user_id= $ctrl->getUserID();
         if ( $l_user_id == 0 ) 
            return  $this->loginplease();
         $l_res=false;
         $p1data = array();
         $p1data["message"]= "None";
         $p1data["success"]= "false";
         foreach($_POST as $key => $value)
         {
            $val=explode("_",$key);
            if ( count($val) == 3 && $val[0]= "invest" && $val[1] == "amount" )
            { 
                $l_product_id = $val[2];
                $amount =  $value;
                if ( $this->isValidProduct($l_product_id) && $amount > 0  )
                {
         $tm =  date('Y-m-d H:i:s');
         $l_payment_id = 0;

          if ( $l_payment_id == 0 ) 
          {
         $fields= "created_user_id,updated_user_id,created_ts,user_id," .
                     "product_id,amount";
          $values= $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($tm)  . "," .
                  $ctrl->MYSQLQ($l_user_id) . "," .
                  $ctrl->MYSQLQ($l_product_id) . "," .
                  $ctrl->MYSQLQ($amount);

                  $sql = "insert into user_payment ( $fields ) values ( $values ) ";
                  $l_id = $ctrl->execute($sql);
                  if ( $l_id == 1 ) 
                  { 
                            $sql = "select COALESCE(max(payment_id) ,0) from user_payment where created_user_id = " . $ctrl->MYSQLQ($l_user_id) . 
                                         " and " .
                                        "  created_ts = " . $ctrl->MYSQLQ($tm); 
                            $p_payement = $ctrl->getRecordID($sql);
                            $p1data["payement"]= $p_payement;
                            $p1data["success"]= "true";

                   }
         } // l_payment_id == 0 
         else
         {
                  $fieldlists =  
                     " updated_user_id = " .  $ctrl->MYSQLQ($l_user_id). "," .
                     " product_id = " .  $ctrl->MYSQLQ($l_product_id). "," .
                     " amount = " .  $ctrl->MYSQLQ($amount) ;

                 $sql= "update user_payment set  " . $fieldlists . 
                               " where payment_id  = " . $ctrl->MYSQLQ($l_payment_id);
                 $l_id = $ctrl->execute($sql);
                 if ( $l_id == 1 ) 
                 {
                        $p1data["message"]= "Saved";
                        $p1data["success"]= "true";
                 }
                 else
                 {
                       $p1data["message"]= $ctrl->getServerError();
                       $p1data["success"]= "false";
                       return $p1data;
                 }
         }
                  $sql=
                            "update product_notes set " . 
                            "   no_of_shareholders =  ( select count(distinct user_id ) " .
                            " from user_payment ". 
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id)  .
                               "   and isactive  =  " .  $ctrl->MYSQLQ('Y') . ")" .
                            "   ,amount_availed =  ( select sum(amount) " .
                            " from user_payment ". 
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id)  .
                               "   and isactive  =  " .  $ctrl->MYSQLQ('Y') . ")" .
                               " where product_id =  " .  $ctrl->MYSQLQ($l_product_id);
                  $lctr = $ctrl->execute($sql);
                  if ( $lctr == 1 ) 
                  {
                         $p1data["message"]= "Saved";
                         $p1data["success"]= "true";
                  }
                  else
                  {
                        $p1data["message"]= $ctrl->getServerError();
                        $p1data["success"]= "false";
                        return $p1data;
                  }
         } // valid product
         }  //if valid 
         } // foreach
         return $p1data;
   }  // createAccount()

  public function uploaddatafile($p1data)
  {
         $ctrl = Controller::get(); 
         $file_name  = $p1data["file_name"];
         $dir_path = $p1data["dir_path"];
         $full_path = $p1data["full_path"];
         $l_doc_type = $p1data["doc_type"];
         $size = $p1data["size"];
         $l_user_id = $p1data["user_id"];
         $tm =  date('Y-m-d H:i:s');

         $p1data = array();
         $p1data["message"]= "None";
         $p1data["success"]= "false";
         return $p1data;
   }  // createUploads($p1data)
}
