<?php
$data = '<?xml version="1.0" ?>
 <!-- WWWSQLEditor XML export -->
 <sql>
 	<table id="0" title="czf_smaha" x="100" y="120" >
 		<row id="0" pk="pk" index="index">
 			<title>id</title>
 			<default>0</default>
 			<type>Integer</type>
 		</row>
 		<row id="1" special="32">
 			<title>jmeno</title>
 			<default></default>
 			<type>String</type>
 		</row>
 		<row id="2" special="32">
 			<title>mail</title>
 			<default></default>
 			<type>String</type>
 		</row>
 	</table>
 	<table id="1" title="czf_squat" x="952" y="186" >
 		<row id="0" pk="pk" index="index">
 			<title>id</title>
 			<default>0</default>
 			<type>Integer</type>
 		</row>
 		<row id="1" special="128">
 			<title>adresa</title>
 			<default></default>
 			<type>String</type>
 		</row>
 		<row id="2">
 			<title>food_amount</title>
 			<default>0</default>
 			<type>Single precision</type>
 		</row>
 		<row id="3">
 			<title>beer_amount</title>
 			<default>0</default>
 			<type>Single precision</type>
 		</row>
 	</table>
 	<table id="2" title="obyvatel" x="510" y="171" >
 		<row id="0" pk="pk" index="index">
 			<title>id</title>
 			<default>0</default>
 			<type>Integer</type>
 		</row>
 		<row id="1" fk="fk" index="index">
 			<title>id_smaha</title>
 			<default>0</default>
 			<type>Integer</type>
 		</row>
 		<row id="2" fk="fk" index="index">
 			<title>id_squat</title>
 			<default>0</default>
 			<type>Integer</type>
 		</row>
 		<row id="3">
 			<title>najem</title>
 			<default>0</default>
 			<type>Single precision</type>
 		</row>
 	</table>
 	<relation>
 		<table_1>0</table_1>
 		<row_1>0</row_1>
 		<table_2>2</table_2>
 		<row_2>1</row_2>
 	</relation>
 	<relation>
 		<table_1>1</table_1>
 		<row_1>0</row_1>
 		<table_2>2</table_2>
 		<row_2>2</row_2>
 	</relation>
 </sql> ';
 /* oracle : */
    define('BASEPATH','WERD'); 
    require_once('./txm_query_helper.php'); 
    
	define('SERVER','10.131.81.141');
	define('USER','DA05');
	define('PASSWORD','DA05');
	define('DB','MROK');
	define('TABLE','컬럼정의서');
/**/

function get_special($str) {
    preg_match_all('/\d+/', $str, $matches);
    return (String) $matches[0][0];
}
// $TBL_ID_LIST에서 테이블 번호를 가지고옮
function get_tbl_no($tbl_id){
    global $TBL_ID_LIST; 
    if ( isset($TBL_ID_LIST[$tbl_id]))
        $ret =  $TBL_ID_LIST[$tbl_id];    
     else {
        $ret = null;
        throw new Exception('undefined index in $TBL_ID_LIST array.');
     }
    return $ret ;
    
}
// SQL IN 절에 사용하는 IN LIST 
function make_in_list($v1,$v2)
{
    return $v1 . " ,'" . $v2 . "'" ;
}
// $ATTR_ID_LIST에서 속성 번호를 가지고옮
function get_attr_no($attr_id){
    global $ATTR_ID_LIST; 
    if( isset($ATTR_ID_LIST[$attr_id]))    
        $ret =  $ATTR_ID_LIST[$attr_id];
    else {
        $ret = null;
        throw new Exception('undefined index in $ATTR_ID_LIST array.');
    } 
        
    return $ret ;
}
  
// 컬럼 목록을 을 가져옴 
function get_relations($c,$ent_id,$id_no){
    global $TBL_ID_LIST;
    $tbl_list = array_filter($TBL_ID_LIST, function($var) { global $id_no; return ($var == $id_no ); } );
    ob_start();
    $sql = "select * from DAM_ENT_ATTR_REL 
where mdl_id =  'd2cb3324-33fc-4f78-893e-2d6f00af28d3' 
and AVAL_END_DT = '99991231235959' 
AND ENT_ID2 IN ( 'X' " . array_reduce(array_keys($tbl_list),"make_in_list","") . ")";
    $params = array();    
    $ret['rows'] = ora_query($c,$sql,$params,$cols,array('row' => true));
    $ret['cols'] = $cols;
    $i = 0 ;
    foreach ( $ret['rows'] as $row ){ 
        try {
            $out_str =  '<relation>' . "\n";
            $out_str .= "\t<table_1>".get_tbl_no($row['ENT_ID1'])."</table_1>\n";
            $out_str .= "\t<row_1>".get_attr_no($row['ATTR_ID1'])."</row_1>\n";
            $out_str .= "\t<table_2>".get_tbl_no($row['ENT_ID2'])."</table_2>\n";
            $out_str .= "\t<row_2>".get_attr_no($row['ATTR_ID2'])."</row_2>\n";        
            $out_str .= "</relation>\n";
            echo $out_str;
        } catch (Exception $e){
            $out_str = "";
        }
        $i++;
    }
    $ret_val = ob_get_contents();
    ob_end_clean();
    return $ret_val;
}
// 컬럼 목록을 을 가져옴 
function get_columns($c,$mdl_nm,$tbl_nm,$ent_id,$id_no,$x,$y){
    // TBL_ID_LIST  초기화
    global $TBL_ID_LIST,$ATTR_ID_LIST;
   
    ob_start();
    $sql = "select * from  컬럼정의서_EXT WHERE TBL_NM = '".$tbl_nm."'  AND MDL_NM = '".$mdl_nm."' ORDER BY ORD";
    $params = array();    
    $ret['rows'] = ora_query($c,$sql,$params,$cols,array('row' => true));
    $ret['cols'] = $cols;
    
    echo '<table id="'.$id_no.'" title="'.$tbl_nm.'" x="'. intval($x) .'" y="'. intval($y) .'" >' . "\n";
    $i = 0 ;
    foreach ( $ret['rows'] as $row ){ 
      // TBL_ID_LIST  추가
      $TBL_ID_LIST[$row['ENT_ID']] = $id_no;
      //if ( ! in_array($row['ENT_ID'],$tbl_list) ) array_push($tbl_list,$row['ENT_ID']);  
      // ATTR_ID_LIST 추가
      $ATTR_ID_LIST[$row['ATTR_ID']] = $i;
      
     if ( $i == 0 )
        echo '<row id="' . (string)($i) . '" pk="pk" index="index">' . "\n";
     else {
         if (substr( $row["DT"],0,3) == "var" || substr( $row["DT"],0,3) == "int" ) 
             $special = ' special="' . get_special($row["DT"])  . '"';
         else
             $special = '';
         echo '<row id="' . (string)($i) . '"'. $special . '>' . "\n";
     }         
     echo "\t<title>" . $row["COL_NM"] . '</title>' . "\n";
     echo "\t<default>" . $row["DEFT"] . '</default>' . "\n";
     if (substr($row["DT"],0,3) == "int" )  // integer
        echo "\t<type>" . "Integer" . '</type>' . "\n";
     elseif (substr($row["DT"],0,7) == "varchar") //varchar
        echo "\t<type>" . "String" . '</type>' . "\n";
     else
        echo "\t<type>" . $row["DT"] . '</type>' . "\n";         
     echo "</row>\n";
     $i++;
    }
    echo "</table>\n";   
    $ret_val = ob_get_contents();
    ob_end_clean();
    return $ret_val;
}
//테이블 목록을 가져옮 
function get_tables($c,$mdl_nm){  
    $mdl_id = 'd2cb3324-33fc-4f78-893e-2d6f00af28d3';
    $sql = "SELECT B.TXT TBL_NM
     , SUBSTR(B.EXCOL05,1, INSTR(B.EXCOL05,',')-1) ENT_NM
     , SUBSTR(B.DRAW_ITEM_ORGIN_COORD,1, INSTR(B.DRAW_ITEM_ORGIN_COORD,',')-1) X
     , SUBSTR(B.DRAW_ITEM_ORGIN_COORD,INSTR(B.DRAW_ITEM_ORGIN_COORD,',')+1) Y
     , A.ENT_ID
FROM (
SELECT * FROM DAG_DRAWITEM WHERE mdl_id =  :mdl and aval_end_dt = '99991231235959' 
) B 
,( 
SELECT *  FROM   DA05.DAM_ENT_OBJ E WHERE  mdl_id =  :mdl and AVAL_END_DT = '99991231235959' AND TYPE >='200'  
) A
WHERE B.EXTRN_OBJ_ID = A.ENT_ID
--AND  A.ENT_ID IN ( '0b669a84-90df-407f-a26e-f6295740a7d2', '02af64a5-9460-4bd2-8407-24a02cc18ea7')
AND B.CNVAS_ID != 'MASTER' ";
    $params = array(array('name'=>':mdl', 'value'=>$mdl_id, 'type'=>SQLT_CHR, 'length'=>-1)
                   );     
    $ret['rows'] = ora_query($c,$sql,$params,$cols,array('row' => true));
    $ret['cols'] = $cols;
    return $ret;
}
/***************************** start main ****************************/
function get_conn() {
    $c = oci_pconnect(USER, PASSWORD , SERVER . "/" . DB, "AL32UTF8" );
    if (!$c) {
         $e = oci_error();
         trigger_error('Could not connect to database: '. $e['message'],E_USER_ERROR);
         return null;
    }    
    else {
        return $c;
    }
}
    
$c = get_conn();

/***************************** start output ****************************/
echo '<?xml version="1.0" ?>
 <!-- WWWSQLEditor XML export -->
 <sql>';
$mdl_nm = '전시[DP]';
$ret = get_tables($c,'전시[DP]');
$ENT_ID_LIST = array();
$TBL_ID_LIST = array();
$ATTR_ID_LIST = array();
$i = 0;
foreach ( $ret['rows'] as $row ){    
    $x = 100 + 250  * ( $i % 10);
    $y = 120 + 500  * intval( $i / 10);;
    $ENT_ID_LIST[$row['ENT_ID']] = $i;
    //echo $mdl_nm, $row['TBL_NM'], $i,$x, $y , "\n" ;
    //print(get_columns($c,$mdl_nm, $row['TBL_NM'],$row['ENT_ID'], $i,$x, $y));
    print(get_columns($c,$mdl_nm, $row['TBL_NM'],$row['ENT_ID'], $i,100+intval(intval($row['X'])/2), 120+ intval(intval($row['Y'])/2)));
    $i++;
    //if( $i > 10 ) break;
}
foreach ( $ENT_ID_LIST as $ent_id => $id_no ) {
    print(get_relations($c,$ent_id,$id_no));
}
echo '</sql> ';