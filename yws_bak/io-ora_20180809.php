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
	define('SERVER','10.131.81.141');
	define('USER','DA05');
	define('PASSWORD','DA05');
	define('DB','MROK');
	define('TABLE','컬럼정의서');
/**/    
function oci_query(){
    ob_start();
    $c = oci_pconnect(USER, PASSWORD , SERVER . "/" . DB, "AL32UTF8" );
    if (!$c) {
     $e = oci_error();
     trigger_error('Could not connect to database: '. $e['message'],E_USER_ERROR);
    }
    $s = oci_parse($c, "select * from  컬럼정의서 WHERE TBL_NM = 'DP_DSHOP'  AND MDL_NM = '전시[DP]' ORDER BY ORD");
    if (!$s) {
     $e = oci_error($c);
     trigger_error('Could not parse statement: '. $e['message'], E_USER_ERROR);
    }
    $r = oci_execute($s);
    if (!$r) {
     $e = oci_error($s);
     trigger_error('Could not execute statement: '. $e['message'], E_USER_ERROR);
    }
    echo "<table border='1'>\n";
    $ncols = oci_num_fields($s);
    echo "<tr>\n";
    for ($i = 1; $i <= $ncols; ++$i) {
     $colname = oci_field_name($s, $i);
     echo " <th><b>".htmlspecialchars($colname, ENT_QUOTES, "UTF-8")."</b></th>\n";    
    }
    echo "</tr>\n";
    while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
     echo "<tr>\n";
     foreach ($row as $item) {
     echo " <td>".($item !== null? htmlspecialchars($item, ENT_QUOTES, "UTF-8") : "&nbsp;")."</td>\n";
     }
     echo "</tr>\n";
    }
    echo "</table>\n";
    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

echo '<html>
<meta charset="UTF-8">
<head>
</head>
<body>';
print(oci_query());
echo '
</body>
</html>';