<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/***************************************************
   This code was written by wonseokyou@gmail.com
   Date : 2015-01-17
   All Copyrights reserved by wonseokyou@gmail.com
***************************************************/
// OCI를 이용하여 Oracle Query를 수행하는 함수 
if ( ! function_exists('ora_query')){
 function ora_query($c, $query,$ba = false, &$cols = false , $option = false)
 {
  /* 변수 default값 설정 시작 */
  if ( ! isset($option['fetch']) ) $option['fetch'] = true;  
  if ( ! isset($option['flags']) ) $option['flags'] = OCI_ASSOC+OCI_RETURN_LOBS+OCI_RETURN_NULLS ; 
  if ( isset($option['row']) && $option['row'] == true) $option['flags'] += OCI_FETCHSTATEMENT_BY_ROW;
  /* 변수 default값 설정 종료  */
  $s = oci_parse($c, $query);
  if (!$s) {
    $e = oci_error($conn); // Connection resource passed
     echo $e["message"] . "<br>\n";
      exit(1);
  }
  if ($ba && is_array($ba)) {
   foreach ($ba as $key => $val) {
    if ( is_array($val)) {
     switch ( $val['type'] ) {    
      case  OCI_B_BLOB : 
        $blob = oci_new_descriptor($c, OCI_D_LOB);
        oci_bind_by_name($s, $val['name'],$blob , -1, OCI_B_BLOB);      
        $blob->WriteTemporary($val['value'],OCI_TEMP_BLOB);      
       break;
      case  OCI_B_CLOB : 
        $clob = oci_new_descriptor($c, OCI_D_LOB);
        oci_bind_by_name($s, $val['name'],$clob , -1, OCI_B_CLOB);      
        $clob->WriteTemporary($val['value']);      
       break;
      default : 
       oci_bind_by_name($s, $val['name'], $val['value'], $val['length'], $val['type']);
     }
    } else {
     oci_bind_by_name($s, $key, $ba[$key]);
    }
   }
  }  
  $rc = oci_execute($s);
  if ( $rc ) {
    if ( is_null($cols) ) { 
    $cols = field_data($s); 
    }
  }  
  /*
  if (!$rc) {
   $e = oci_error($s); // Statement resource passed
   var_dump($e);
  } 
  */   
  if ( $option['fetch'] == true ) {
  $rc = oci_fetch_all($s, $res,0,-1,$option['flags']);
  /*
  if (!$rc) {
   $e = oci_error($s); // Statement resource passed
   var_dump($e);
  } 
  */
  return $res;
  }
 }
 //--------------------------------------------------------
 function field_data($s)
 {
  $retval = array();
  for ($ci = 1, $fieldCount = oci_num_fields($s); $ci <= $fieldCount; $ci++)
  {
   $F   = new stdClass();
   $F->name  = oci_field_name($s, $ci);
   $F->type  = oci_field_type($s, $ci);
   $F->max_length = oci_field_size($s, $ci);

   $retval[] = $F;
  }
  return $retval;
 } 
}

//---------------------- array를 string으로 해서 보내줌 -------------//
if ( ! function_exists('join_qt')){
 function join_qt($sep = "," , $ct_ary, $qt = "'" ) {
   $join_val = '';
   foreach ( $ct_ary as $val ) {
     $join_val .=  ( $sep . $qt . $val . $qt );
   }    
   return substr($join_val,1);
 }
}