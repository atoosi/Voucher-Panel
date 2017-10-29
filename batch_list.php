<html dir="rtl">
<head>
<link href="test.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>لسیت پینهای وارد شده</title>
</head>
<body>
<?php

	include( "head.php" );


	$conn = connect_ora();

	$category = "9351";
	if( isset( $_GET[ "Category" ] ) ){
		$category=$_GET[ "Category" ];
	}

	$order = " 4 desc,2 desc";
	
	if( isset( $_GET[ "Order" ] ) ){
		if( $_GET[ "Order" ] == "4" ){
			$order = " 4 desc,2 desc";
		}else if( $_GET[ "Order" ] == "3" ){
			$order = " 3 desc,2 desc";
		}else{
			$order = $_GET[ "Order" ]." desc";
		}		
	}
	
?>

<table width="100%" border="0">
	<tr>
		<td>
				<? show_link(); ?>
		</td>
		<form action="">
		<td><input name="Submit" type="submit" id="Submit" value="Refresh" /></td>
		</form>
	</tr>

<tr>
<td align="center">
	<hr>
	<table>
	<tr>
	
<?

	$result = run_my_query( $conn , "SELECT NAME,CATID,CATEGORY FROM thp.TBLCATEGORY ORDER BY 1" );
	$i = 0;
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
		echo "<td><a href=?Category=$row[2]>".$row[0]."</a></td>";
		$i++;
	}

	oci_free_statement( $result );
	
	
?>
</tr>
<tr>
	<td>
</td>
<td>
</td>
	<td align="center">
</td>

</tr>
</table>
	<hr>

<table width="80%" border="0" cellspacing="0" cellpadding="2" class="STable">
  <tr bgcolor=\"#AAFFEE\">
    <th scope="col">رديف</th>
    <th scope="col">شماره دسته</th>
    <th scope="col">زمان ورود</th>
    <th scope="col"></th>
    <th scope="col">نوع شارژ</th>
    <th scope="col">تعداد کل</th>
    <th scope="col">تعداد فروخته شده</th>
    <th scope="col">نام دسته</th>
    <th scope="col">شماره فاکتور</th>
  </tr>
  <?
  
/*  	if( isset( $_GET[ "Category" ] ) ){
  		$result = run_my_query( $conn , "SELECT BATCHID,COUNT(*) FROM TBLPINS WHERE CATEGORY='".$_GET[ "Category" ]."' GROUP BY BATCHID" );
  	}else{
  		$result = run_my_query( $conn , "SELECT BATCHID,COUNT(*) FROM TBLPINS  GROUP BY BATCHID" );
  	}
		$i = 0;
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
			$BatchCount[ $row[0] ] = $row[1];
			
			$i++;
    }
  	
  	oci_free_statement( $result );
*/
  	if( isset( $_GET[ "Category" ] ) ){
  		$result = run_my_query( $conn , "SELECT BATCHID,COUNT(*) FROM thp.TBLPINSOLD WHERE CATEGORY='".$_GET[ "Category" ]."' $andSupplier_id GROUP BY BATCHID" );
  	}else{
  		$result = run_my_query( $conn , "SELECT BATCHID,COUNT(*) FROM thp.TBLPINSOLD WHERE CAPTUREDATE>TO_CHAR(SYSDATE-180,'YYYYMMDD') $andSupplier_id GROUP BY BATCHID" );
  	}
		$i = 0;
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
  //          if( isset( $BatchCount[ $row[0] ] ) ){
  //              $BatchCount[ $row[0] ] += $row[1];
  //          }else{
                $BatchCount[ $row[0] ] = $row[1];
  //			}
			
			$i++;
    }
  	
  	oci_free_statement( $result );
  	

	//$result = mysql_query( "select tblPins.TermNo,count(*),date(from_unixtime(min(UpdateTime))) as sdate,date(from_unixtime(max(UpdateTime))) as edate,PosType,ifnull(tblTerminals.Name,'-'),sum(amount) from tblPins left join  tblTerminals on tblTerminals.TermNo=tblPins.TermNo where ChargeFlag=1 group by TermNo order by 4 desc,2 desc" , $msql );
	
       $result = run_my_query( $conn , " SELECT thp.FROM_UNIXTIME(TBLBATCH.BATCHDATE),TBLBATCH.TOTAL,TBLCATEGORY.NAME,TBLBATCH.NAME,NVL(BATCHCOUNT.TOTAL,0),TBLBATCH.INVOICENO,TBLBATCH.BATCHID,TO_CHAR(thp.FROM_UNIXTIME(BATCHDATE),'MM/DD/YYYY') FROM ( select * from thp.TBLBATCH where supplier='0' or supplier is null and  TBLBATCH.NAME not like 'shetat%') TBLBATCH  LEFT JOIN thp.BATCHCOUNT ON BATCHCOUNT.BATCHID=TBLBATCH.BATCHID LEFT JOIN thp.TBLCATEGORY ON TBLCATEGORY.CATEGORY=TBLBATCH.CATEGORY WHERE /* TBLBATCH.TOTAL>0  AND */ TBLBATCH.CATEGORY=$category  ORDER BY 1 DESC");
	$i = 0;
	$last_time="";
	$batch_tot=0;
	$sale_tot=0;
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
		$extra="";
		if( $i % 2 == 0 )
			$extra = "bgcolor=\"#EEEEEE\"";
			
		$cnt = $i + 1;
		$fdate=convMJD2JALALI($row[7]);
		echo "<tr $extra>";
		echo "<th bgcolor=\"#AAFFEE\" scope=\"row\" align=\"center\">".$cnt."</th>";
		echo "<td align=\"center\">$row[6]</td>";
		echo "<td align=\"center\">$row[0]</td>";
		echo "<td align=\"center\">".$fdate."</td>";
		echo "<td align=\"center\">$row[2]</td>";
		echo "<td align=\"center\">$row[1]</td>";
		$batch_tot += $row[1];
		if( isset( $BatchCount[ $row[6] ] ) ){
			$tot = $row[4]+$BatchCount[ $row[6] ];
		}else{
			$tot = $row[4];
		}
		echo "<td align=\"center\">".$tot."</td>";
		echo "<td align=\"center\">$row[3]</td>";
		echo "<td align=\"center\">$row[5]</td>";
		$sale_tot += $tot;
		if( $tot != $row[1] )
			echo "<td bgcolor=\"#FF0000\" align=\"center\"> </td>";
		else
			echo "<td bgcolor=\"#00FF00\" align=\"center\"> </td>";
		
		echo "</tr>";
		$i++;
	}
	echo "<tr>";
	echo "<td></td>";
	echo "<td align=\"center\">جمع کل</td>";
	echo "<td></td>";
	echo "<td align=\"center\" >".$batch_tot."</td>";
	echo "<td align=\"center\" >".$sale_tot."</td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "</tr>";
	mysql_close( $msql );
  ?>
</table>

</body>
</html>
