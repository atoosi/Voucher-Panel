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
<title>لیست فروشگاه ها</title>
</head>
<body>
<?php

	$m_date_from="";
	$m_date_to="";
	$start = 0;
	$end = 100;
	if( isset( $_GET["dateFrom"] ) )
		$m_date_from = $_GET["dateFrom"];
	if( isset( $_GET["dateTo"] ) )
		$m_date_to = $_GET["dateTo"];
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
			<legend> اطلاعات گزارش </legend>
		<table border="0" width="60%" id="table1" style="border-collapse: collapse">
			<tr>
				<td align="center" >
					<label for="TermNo" >1391-01-01 از تاريخ
				<input type="text" id="dateFrom" name="dateFrom" size="31" value="<? echo $m_date_from; ?>" ></label></td>
			</tr>
			<tr>
				<td align="center">
					<label for="TermName">1391-01-01 تا تاريخ
				<input type="text" name="dateTo" id="dateTo" size="47" value="<? echo $m_date_to; ?>" dir="rtl"></label></td>
			</tr>
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
	<form method="GET" action="">
	<table border="0" width="90%" cellspacing="0" cellpadding="2">
		<tr>
				<td>
			<input name="Start" type="hidden" value="<? echo $start; ?>" />
				<p><input name="Next" type="submit" value="Next" />
				<input name="Prev" type="submit" value="Prev" /></p>
				</td>
				<td>
					<p> Export to excel <input type="submit" value="Export" name="Export"></p>
				</td>
				<td>
					<!-- <input type="submit" value="Submit" name="Prev"> -->
				</td>
		</tr>
	</table>
	</form>
	<table border="0" width="90%" cellspacing="0" cellpadding="2" class="STable">
	<tr>
		<th align="center" width="84">رديف</th>
		<th align="center" width="180"><a href=repFinalsPin.php?order=1>تاريخ</a></th>
		<th align="center" width="263"><a href=repFinalsPin.php?order=2>تامين كننده</a></th>
		<th align="center"><a href=repFinalsPin.php?order=3>نوع </a></th>
		<th align="center" width="158"><a href=repFinalsPin.php?order=4> مبلغ هر پين</a></th>
		<th align="center" width="200"><a href=repFinalsPin.php?order=8&desc=1>تعداد فروخته شده نهايي</a></th>
		<th align="center" width="200"><a href=repFinalsPin.php?order=8&desc=1>تعداد فروخته شده غير نهايي</a></th>
   		<th align="center" width="158"><a href=repFinalsPin.php?order=4> مبلغ کل</a></th>

	</tr>
<?
	if( $desc == 1 ){
		$desc = "desc";
	}else{
		$desc = "";
	}
	//echo "<p> $spwhere </p>";
	// $result = run_my_query( $conn , "select REPORTDATE,CATEGORY,TOTALCOUNT,TOTALAMOUNT,SUPPLIER from thp.totaltxnsum where " );
	//پرس و جوي اصلي صفحه اصلي
	if( isset( $_GET[ "Create" ] ))
	{ 
		//	$query = "insert into thp.totaltxnsum (sumid,reportdate,supplier,category,totalcount,totalamount,updatetime)  select thp.TotalTxnSum_SumId_SEQ.NEXTVAL,dt,supplier,category,cnt,sss,ts from( select to_date(capturedate,'yyyymmdd') dt,supplier,category,count(*) cnt,sum(amount) sss,sysdate ts from thp.tblpins  where capturedate !=0 and to_date(capturedate,'yyyymmdd')< trunc(sysdate) and capturedate not in (select distinct to_char(reportdate,'yyyymmdd') from   thp.totaltxnsum )  group by capturedate,supplier,category )";
		  	$result = run_my_query( $conn , $query );
			oci_commit( $conn );
	//select to_char(REPORTDATE,'yyyy-mm-dd','nls_calendar=persian') as datetr,TOTALCOUNT,TOTALAMOUNT,SUPPLIERNAME ,cat.name from thp.totaltxnsum txn,THp.tblsuppliers,thp.tblcategory cat where supplierid=SUPPLIER and cat.CATEGORY=txn.category and trunc(REPORTDATE)>=to_date('$m_date_from','yyyy-mm-dd','nls_calendar=persian') and  trunc(REPORTDATE)<=to_date('$m_date_to','yyyy-mm-dd','nls_calendar=persian') $andSupplier_id  order by REPORTDATE desc");
	//{ $result = run_my_query( $conn ,"select to_char(REPORTDATE,'yyyy-mm-dd','nls_calendar=persian') as datetr,TOTALCOUNT,TOTALAMOUNT,SUPPLIERNAME ,cat.name from thp.totaltxnsum txn,THp.tblsuppliers,thp.tblcategory cat where supplierid=SUPPLIER and cat.CATEGORY=txn.category and trunc(REPORTDATE)>=trunc(sysdate-10) order by REPORTDATE desc");
	
	} else
	if( isset( $_GET[ "search" ] ))
	{ $result = run_my_query( $conn ,"select to_char(to_date (CAPTUREDATE,'yyyymmdd'),'yyyy-mm-dd','nls_calendar=persian') as date_tr,SUPPLIERNAME,NAME,txn.AMOUNT,COUNT_PIN_OK,COUNT_PIN_NOT from(select case when o.capturedate is null then p.capturedate else o.capturedate end as capturedate  , case when o.supplier is null then p.supplier else o.supplier end as supplier ,case when o.category is null then p.category else o.category end as category , case when o.AMOUNT is null then p.AMOUNT else o.AMOUNT end as AMOUNT ,p.c as count_pin_NOt,o.c count_pin_OK from( (select capturedate,supplier,category,AMOUNT,count(*) as c from thp.tblpinsold where CAPTUREDATE<>0 and to_date (CAPTUREDATE,'yyyymmdd')>=to_date ('$m_date_from','yyyy-mm-dd','nls_calendar=persian') and to_date (CAPTUREDATE,'yyyymmdd')<=to_date ('$m_date_to','yyyy-mm-dd','nls_calendar=persian')  group by capturedate,supplier,category,AMOUNT) o full outer join (select capturedate,supplier,category,AMOUNT,count(*) as c from thp.tblpins where CAPTUREDATE<>0 and to_date (CAPTUREDATE,'yyyymmdd')>=to_date ('$m_date_from','yyyy-mm-dd','nls_calendar=persian') and to_date (CAPTUREDATE,'yyyymmdd')<=to_date ('$m_date_to','yyyy-mm-dd','nls_calendar=persian')  group by capturedate,supplier,category,AMOUNT) p on p.capturedate=o.capturedate and p.supplier=o.supplier and p.category=o.category and  p.AMOUNT=o.AMOUNT) order by o.capturedate,p.capturedate,o.supplier,p.supplier,o.category,p.category,o.AMOUNT,p.AMOUNT) txn  ,THp.tblsuppliers,thp.tblcategory cat where supplierid=txn.SUPPLIER and cat.CATEGORY=txn.category $andSupplier_id order by CAPTUREDATE desc ,supplierid, NAMe");
	//{ $result = run_my_query( $conn ,"select to_char(REPORTDATE,'yyyy-mm-dd','nls_calendar=persian') as datetr,TOTALCOUNT,TOTALAMOUNT,SUPPLIERNAME ,cat.name from thp.totaltxnsum txn,THp.tblsuppliers,thp.tblcategory cat where supplierid=SUPPLIER and cat.CATEGORY=txn.category and trunc(REPORTDATE)>=trunc(sysdate-10) order by REPORTDATE desc");
	
	} else
	{ // by jamal	$result = run_my_query( $conn ,"select to_char(to_date (CAPTUREDATE,'yyyymmdd'),'yyyy-mm-dd','nls_calendar=persian') as date_tr,SUPPLIERNAME,NAME,txn.AMOUNT,COUNT_PIN_OK,COUNT_PIN_NOT from(select case when o.capturedate is null then p.capturedate else o.capturedate end as capturedate  , case when o.supplier is null then p.supplier else o.supplier end as supplier ,case when o.category is null then p.category else o.category end as category , case when o.AMOUNT is null then p.AMOUNT else o.AMOUNT end as AMOUNT ,p.c as count_pin_NOt,o.c count_pin_OK from( (select capturedate,supplier,category,AMOUNT,count(*) as c from thp.tblpinsold where CAPTUREDATE<>0 and to_date (CAPTUREDATE,'yyyymmdd')=trunc(sysdate-2)  group by capturedate,supplier,category,AMOUNT) o full outer join (select capturedate,supplier,category,AMOUNT,count(*) as c from thp.tblpins where CAPTUREDATE<>0 and to_date (CAPTUREDATE,'yyyymmdd')=trunc(sysdate-2)  group by capturedate,supplier,category,AMOUNT) p on p.capturedate=o.capturedate and p.supplier=o.supplier and p.category=o.category and  p.AMOUNT=o.AMOUNT) order by o.capturedate,p.capturedate,o.supplier,p.supplier,o.category,p.category,o.AMOUNT,p.AMOUNT) txn  ,THp.tblsuppliers,thp.tblcategory cat where supplierid=txn.SUPPLIER and cat.CATEGORY=txn.category $andSupplier_id order by CAPTUREDATE desc ,supplierid, NAMe");
	}

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
		echo "<td align=\"center\" width=\"180\"><a href=repFinalsPin.php?dateFrom=$row[0]&dateTo=$row[0]&search=search title=\"".$row[10]."\" >$row[0]</a></td>";
		echo "<td align=\"center\" width=\"200\">$row[1]</td>";
		echo "<td align=\"center\">$row[2]</td>";
		echo "<td align=\"center\" width=\"158\">$row[3]</td>";
		echo "<td align=\"center\" width=\"200\">$row[4]</td>";
		echo "<td align=\"center\" width=\"200\">$row[5]</td>";
		//echo "<td align=\"center\" width=\"70\">$row[7]</td>";
		echo "<td align=\"center\" width=\"158\">". (($row[5]+$row[4])* $row[3])."</td>";
//		}
		
		echo "</tr>";
		$sumCount=$sumCount +$row[4];
		$sumSum=$sumSum+$row[5];
		$sumall=$sumall+ (($row[5]+$row[4])* $row[3]);
		$i++;
	}
	echo " <hr> ";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"84\">-</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"180\"  >سرجمع </td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\"></td>";
		echo "<td bgcolor=FF99FF align=\"center\"></td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"158\"></td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$sumCount</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"200\">$sumSum</td>";
		echo "<td bgcolor=FF99FF align=\"center\" width=\"158\">$sumall</td>";

		echo "</tr>";
	oci_free_statement( $result );
	oci_close( $conn );
?>
	</table>
</td>
</tr>
</table>
</div>

</body>
</html>
<?
	}
?>