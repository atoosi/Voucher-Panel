<?
echo "<html dir=\"rtl\">\n<head>\n<link href=\"test.css\" rel=\"stylesheet\" type=\"text/css\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n</head><body>\n";

	include "head.php";

	$conn = connect_ora( );
    if( isset( $_GET["Export"] ) ){
        $city_no = 0;
        if( isset( $_GET[ "CityNo" ] ) ){
            $filter = " AND TBLTERMINALS.CITYNO=".$_GET[ "CityNo" ];
            $city_no = $_GET["CityNo"];
        }
        $filename="terminal_$city_no.csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/ms-excel");
 //       $result = run_my_query( $conn , "select REPORTDATE,CATEGORY,TOTALCOUNT,TOTALAMOUNT,SUPPLIER from thp.totaltxnsum" );
// by jamal		$result = run_my_query( $conn ,"select to_char(REPORTDATE,'yyyy-mm-dd','nls_calendar=persian') as datetr,TOTALCOUNT,TOTALAMOUNT,SUPPLIERNAME ,cat.name from thp.totaltxnsum txn,THp.tblsuppliers,thp.tblcategory cat where supplierid=SUPPLIER and cat.CATEGORY=txn.category and trunc(REPORTDATE)=trunc(sysdate-1)");

        while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
            echo "\"$row[0]\",\"$row[1]\",\"$row[2]\",\"$row[3]\",\"$row[4]\"\n";        
        }
        oci_close( $conn );
    }else{
?>
<html dir="rtl">
<head>
<link href="../test.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>فرم تعیین درصد فروش</title>
</head>
<body>
<?php

	$TERMID="";
	$GROUPID="";
	$start = 0;
	$end = 100;
	if( isset( $_GET["TERMID"] ) )
		$TERMID = $_GET["TERMID"];
	if( isset( $_GET["GROUPID"] ) )
		$GROUPID = $_GET["GROUPID"];
	if( isset( $_GET["Start"] ) )
		$start = $_GET["Start"];
	if( isset( $_GET["Start"] ) )
		$start = $_GET["Start"];
	if( isset( $_GET["End"] ) )
		$end = $_GET["End"];
		
	if( isset( $_GET["Prev"] ) ){
		$start += 100;
	}
	if( isset( $_GET["Next"] ) and $start >= 100 ){
		$start -= 100;
	}

	
?>

<div align="center">

<table width="100%" border="0">
	<tr>
		<td>
				<? show_link(); ?>
		</td>
		<form action="">
		<td><input name="Refresh" type="submit" id="Refresh" value="Refresh" /></td>
		</form>
	</tr>

<tr>
<td align="center">
	<hr>
	<table>
	<tr>
	
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
	<div align="center">
	<form method="GET" action="">
		<fieldset>
			<legend>گزارش اطلاعات فروش </legend>
		<table border="0" width="60%" id="table1" style="border-collapse: collapse">
			<tr>
				<td align="center">
				<input type="submit" value="search" name="search"> </td>
				<td align="center">
								
								</td>
			</tr>
		</table>
		</fieldset>			
	</form>
	</div>
	
	<table border="0" width="90%" cellspacing="0" cellpadding="2" class="STable">
	<tr>
		<th align="center" width="84">ردیف</th>
		<th align="center" width="84">شماره ترمینال</th>
		<th align="center" width="180">شماره گروه</th>
		<th align="center" width="263">تامين كننده</th>
		<th align="center">بانک </th>
		<th align="center" width="158"> درصد</th>
		<th align="center" width="200">تعداد تراکنش</th>
		
	</tr>
<?
	if( isset( $_GET[ "search" ] ))
	{ 
	$result = run_my_query( $conn ,"select tid,g.groupno,groupname,bank_type,persent,count_tr from THP.kicc_terminal_persent p ,tblgrades g where g.groupno=p.groupno ORDER BY GROUPNO,PERSENT DESC");
	
	$i = 0;
	$sumCount=0;
	$sumSum=0;
	$sumall=0;
	$last_time="";
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
		$extra="";
		
		echo "<tr $extra>";
		$cnt = $i+1;
		
		$term_no = $row[0];
		
		echo "<td align=\"center\" width=\"84\">$cnt</td>";
		echo "<td align=\"center\" width=\"180\">$row[0]</td>";
		echo "<td align=\"center\" width=\"200\">$row[1]</td>";
		echo "<td align=\"center\">$row[2]</td>";
		echo "<td align=\"center\" width=\"158\">$row[3]</td>";
		echo "<td align=\"center\" width=\"200\">$row[4]</td>";
		echo "<td align=\"center\" width=\"200\">$row[5]</td>";
		//echo "<td align=\"center\" width=\"70\">$row[7]</td>";
		
//		}
		
		echo "</tr>";
		$sumCount=$sumCount +$row[4];
		$sumSum=$sumSum+$row[5];
		$sumall=$sumall+ (($row[5]+$row[4])* $row[3]);
		$i++;
	}
	echo " <hr> ";
		
			//$result = run_my_query( $conn ,"exec update_terminal_persent()");
			
			$result = run_my_query( $conn ,"select '' AS AAAA, g.groupno,groupname ,'' AS AA, SUM(persent),SUM(count_tr) from THP.kicc_terminal_persent p ,tblgrades g where g.groupno=p.groupno GROUP BY g.groupno,groupname");
	
	$i = 0;
	$sumCount=0;
	$sumSum=0;
	$sumall=0;
	$last_time="";
    while ($row = oci_fetch_array($result, OCI_RETURN_NULLS)) {
		$extra="";
		
		echo "<tr $extra>";
		$cnt = $i+1;
		
		$term_no = $row[0];
		
		echo "<td bgcolor=FF99FF align=\"center\" width=\"84\">$cnt</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$row[0]</td>";
		echo "<td bgcolor=FF99FF align=\"center\">$row[1]</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"158\">$row[2]</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$row[3]</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$row[4]</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$row[5]</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$row[6]</td>";
		
//		}
		
		echo "</tr>";
		$sumCount=$sumCount +$row[4];
		$sumSum=$sumSum+$row[5];
		$sumall=$sumall+ (($row[5]+$row[4])* $row[3]);
		$i++;
	
	echo " <hr> ";

	
	
		
		
		echo "</tr>";
	}
	
	}
	
	
?>

	</table>
</td>
</tr>
</table>
</div>
	<hr>
	<div align="center">
	<form method="GET" action="">
		<fieldset>
			<legend> بروز رسانی گروه ها </legend>
		<table border="0" width="60%" id="table1" style="border-collapse: collapse">
			<tr>
				<td align="center" >
					<label for="TermNo" >   ترمینال
				<input type="text" id="TERMID" name="TERMID" size="20" value="<? echo $TERMID; ?>" ></label></td>
			</tr>
			<tr>
				<td align="center">
					<label for="TermName">شماره گروه
				<input type="text" name="GROUPID" id="GROUPID" size="20" value="<? echo $GROUPID; ?>" dir="rtl"></label></td>
			</tr>
			<tr>
				<td align="center">
				<input type="submit" value="change" name="change"> </td>
				<td align="center">
								
								</td>
			</tr>
		</table>
		</fieldset>			
	</form>
	</div>

	
</body>
</html>
<?

if( isset( $_GET[ "change" ] ))
	{ 
	$result = run_my_query( $conn ,"update tblterminals set groupno='$GROUPID' where  termno='$TERMID' and groupno in ('130','120') and termno in (select tid from kicc_terminal_persent) and '$GROUPID' in ('130','120')");
	oci_commit( $conn );
	$result = run_my_query( $conn ,"update THP.kicc_terminal_persent set groupno=( select t.groupno from   tblterminals t where tid=termno) where tid='$TERMID'");
    oci_commit( $conn );
    
	} 
	
	oci_free_statement( $result );
	oci_close( $conn );
	}
?>