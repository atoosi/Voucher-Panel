<?php
/*********************/
/*                   */
/*  Dezend for PHP5  */
/*         NWS       */
/*      Nulled.WS    */
/*                   */
/*********************/

echo "<html dir=\"rtl\">\n<head>\n<link href=\"test.css\" rel=\"stylesheet\" type=\"text/css\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n</head><body>\n";
include( "head.php" );
$conn = connect_ora( );
$category = "";
if ( isset( $_GET['Category'] ) )
{
    $category = $_GET['Category'];
}
$start = 0;
$end = 100;
if ( isset( $_GET['Start'] ) )
{
    $start = $_GET['Start'];
}
if ( isset( $_GET['End'] ) )
{
    $end = $_GET['End'];
}
if ( isset( $_GET['Prev'] ) )
{
    $start += 100;
}
if ( isset( $_GET['Next'] ) && 100 <= $start )
{
    $start -= 100;
}
echo "\n<table width=\"100%\" border=\"0\">\n\t<tr>\n\t\t<td>\n\t\t\t\t";
show_link( );
echo "\t\t</td>\n\t\t<form action=\"\">\n\t\t\t<input name=\"Category\" type=\"hidden\" value=\"";
echo $category;
echo "\" />\n\t\t<td><input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Refresh\" /></td>\n\t\t</form>\n\t</tr>\n\n<tr>\n<td align=\"center\">\n\t<hr>\n\t<table>\n\t<tr align=\"center\">\n\t\t<td>\n\t<table width=\"100%\">\n\t\t<tr>\n";
$stid = oci_parse( $conn, "SELECT NAME,CATID,CATEGORY FROM TBLCATEGORY" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    echo "<td><a href=?Category={$row['2']}>".$row[0]."</a></td>";
    $Categ[$row[2]] = $row[0];
}
oci_free_statement( $stid );
echo "\n\t\t</tr>\n\t</table>\n\t\t</td>\n\t</tr>\n<tr>\n\t<td width=\"100%\">\n\t<table width=\"100%\">\n\t\t<tr>\n\t\t\t<th>نوع شارژ</th>\n\t\t\t<th>تعداد موجود</th>\n\t\t\t<th>supplier</th>\n\t\t\t<th>فروش امروز</th>\n\t\t\t<th>مبلغ کل به ریال</th>\n\t\t\t<th>سایر</th>\n\t\t</tr>\n\t\t";
$stid = oci_parse( $conn, "SELECT SUPPLIERNAME FROM TBLSUPPLIERS" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $TodayTotal[$row[0]] = "";
    $TodayTotalAmount[$row[0]] = "";
}
oci_free_statement( $stid );
$stid = oci_parse( $conn, "SELECT CATEGORY,COUNT(*),SUM(AMOUNT),SUPPLIERNAME FROM TBLPINS LEFT JOIN TBLSUPPLIERS ON SUPPLIERID=SUPPLIER WHERE {$extra_where} CHARGEFLAG=1 AND CAPTUREDATE = to_char(sysdate,'yyyymmdd') GROUP BY CATEGORY, SUPPLIERNAME ORDER BY 2 DESC" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $Today[$row[3]][$row[0]] = $row[1];
    $TodayAmount[$row[3]][$row[0]] = $row[2];
    $TodayTotal[$row[3]] += $row[1];
    $TodayTotalAmount[$row[3]] += $row[2];
    ++$i;
}
oci_free_statement( $stid );
$stid = oci_parse( $conn, "SELECT CATEGORY,COUNT(*),SUPPLIERNAME FROM TBLPINS LEFT JOIN TBLSUPPLIERS ON SUPPLIER=SUPPLIERID  WHERE {$extra_where} CHARGEFLAG!=0 AND CAPTUREDATE=0 GROUP BY CATEGORY,SUPPLIERNAME" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
$TodayTotalOther = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $TodayOther[$row[2]][$row[0]] = $row[1];
    $TodayTotalOther += $row[1];
    ++$i;
}
oci_free_statement( $stid );
$stid = oci_parse( $conn, "SELECT CATEGORY,COUNT(*),SUPPLIERNAME FROM TBLPINSTEMP LEFT JOIN TBLSUPPLIERS ON SUPPLIER=SUPPLIERID WHERE {$extra_where} CHARGEFLAG!=0 AND CAPTUREDATE=0 GROUP BY CATEGORY,SUPPLIERNAME" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
$TodayTotalOther2 = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    if ( isset( $TodayOther[$row[2]][$row[0]] ) )
    {
        $TodayOther[$row[2]][$row[0]] += $row[1];
    }
    else
    {
        $TodayOther[$row[2]][$row[0]] = $row[1];
    }
    $TodayTotalOther2 += $row[1];
    ++$i;
}
oci_free_statement( $stid );
$stid = oci_parse( $conn, "SELECT NAME,TOTAL,CATEGORY,suppliername FROM TBLCATEGORY LEFT JOIN ( SELECT COUNT(*) AS TOTAL,CATEGORY AS CATEGORY2,suppliername FROM TBLPINS left join tblsuppliers on supplierid=supplier WHERE {$extra_where} CHARGEFLAG='0' GROUP BY CATEGORY, suppliername ) ON TBLCATEGORY.CATEGORY=CATEGORY2 where category<>'9355' and category<>'9356' and category<>'9358' and category<>'1002' and category<>'1001' and category<>'9198' and category<>'9201' and category<>'9324' and category<>'9325' ORDER BY ename DESC" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $extra = "";
    if ( $i % 2 == 0 )
    {
        $extra = "bgcolor=\"#EEEEEE\"";
    }
    echo "<tr {$extra}>";
    echo "<td align=\"center\">{$row['0']}</td>";
    echo "<td align=\"center\">{$row['1']}</td>";
    echo "<td align=\"center\">{$row['3']}</td>";
    $Total[$row[3]] += $row[1];
    if ( isset( $Today[$row[3]][$row[2]] ) )
    {
        echo "<td align=\"center\">".$Today[$row[3]][$row[2]]."</td>";
        echo "<td align=\"center\">".number_format( $TodayAmount[$row[3]][$row[2]], 0 )."</td>";
    }
    else
    {
        echo "<td align=\"center\">-</td>";
        echo "<td align=\"center\">-</td>";
    }
    if ( isset( $TodayOther[$row[2]][$row[2]] ) )
    {
        echo "<td align=\"center\">".$TodayOther[$row[2]][$row[2]]."</td>";
    }
    else
    {
        echo "<td align=\"center\">-</td>";
    }
    echo "</tr>";
    ++$i;
}
oci_free_statement( $stid );
echo "\t\t<tr>\n\t\t</tr>\n\t\t";
$stid = oci_parse( $conn, "SELECT SUPPLIERNAME FROM TBLSUPPLIERS {$where_supplier_id}" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    echo "<tr bgcolor=\"#EEFFEE\">";
    echo "<td align=\"center\">تعداد کل ({$row['0']})</td>";
    echo "<td align=\"center\">".$Total[$row[0]]."</td>";
    echo "<td></td>";
    echo "<td align=\"center\">".$TodayTotal[$row[0]]."</td>";
    echo "<td align=\"center\">".number_format( $TodayTotalAmount[$row[0]], 0 )."</td>";
    echo "<td align=\"center\">".$TodayTotalOther[$row[0]] + $TodayTotalOther2[$row[0]]."</td>";
    echo "</tr>";
}
oci_free_statement( $stid );
echo "\t</table>\n</td>\n<td>\n</td>\n\t<td align=\"center\">\n\t<table width=\"100%\">\n\t\t<tr>\n\t\t\t<td>\n\t\t\t<img src=\"sale_graph.php\" />\n\t\t</td>\n\t\t</tr>\n\t</table>\n</td>\n\n</tr>\n</table>\n\t<hr>\n";
if ( isset( $Categ[$category] ) )
{
    echo "<p><b>".$Categ[$category]."</b></p>";
}
echo "<form action=\"\" method=\"get\">\n\t<input name=\"Category\" type=\"hidden\" value=\"";
echo $category;
echo "\" />\n\t<input name=\"Start\" type=\"hidden\" value=\"";
echo $start;
echo "\" />\n<p><input name=\"Next\" type=\"submit\" value=\"Next\" />\n<input name=\"Prev\" type=\"submit\" value=\"Prev\" /></p>\n</form>\n<table width=\"90%\" border=\"0\"  cellpadding=\"3\">\n  <tr bgcolor=\\\"#AAFFEE\\\">\n    <th scope=\"col\">Id</th>\n    <th scope=\"col\">سریال پین</th>\n    <th scope=\"col\">زمان آخرین تغییرات</th>\n    <th scope=\"col\">شماره پیگیری</th>\n    <th scope=\"col\">شماره مرجع</th>";
echo "\n    <th scope=\"col\">شماره کارت</th>\n    <th scope=\"col\">شماره پایانه</th>\n    <th scope=\"col\">مبلغ به ریال</th>\n    <th scope=\"col\">نوع پایانه</th>\n    <th scope=\"col\">توع شارژ</th>\n    <th scope=\"col\"></th>\n  </tr>\n";
$stid = oci_parse( $conn, "SELECT TERMNO,NAME,VGROUPID,DAILYCOUNTLIMIT FROM TBLTERMINALS" );
if ( !$stid )
{
    $e = oci_error( $conn );
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
/*while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $Terminal[$row[0]] = $row[1];
    $VGroup[$row[0]] = $row[2];
    $Limit[$row[0]] = $row[3];
    ++$i;
} */
//oci_free_statement( $stid );


/*if ( $category != "" )   UPDATE QUERY BY MOHSEN 1396/06/14
{
    $query = "SELECT * from (SELECT PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'  ) as updtime,TRACENO,TXNDATE,RRN,CARDNO,NVL(TERMNO,'-') as tmno,POSTYPE,AMOUNT,CATEGORY,ROWNUM RN,suppliername from (SELECT * FROM TBLPINS left join tblsuppliers on tblsuppliers.supplierid=tblpins.supplier WHERE {$extra_where} CATEGORY={$category} AND CHARGEFLAG=1 ORDER BY UPDATETIME DESC)) WHERE RN>{$start} and RN<{$start}+100";
}
else
{ 
    $query = "select * from (SELECT PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'  ) as updtime,TRACENO,TXNDATE,RRN,CARDNO,NVL(TERMNO,'-') as tmno,POSTYPE,AMOUNT,CATEGORY,ROWNUM RN,suppliername from (SELECT * FROM TBLPINS left join tblsuppliers on tblsuppliers.supplierid=tblpins.supplier WHERE {$extra_where} CHARGEFLAG=1 ORDER BY UPDATETIME DESC)) WHERE RN>{$start} and RN<{$start}+100";
} */


if ( $category != "" )
{
    $query = "SELECT 
	/* +  FIRST_ROWS */
	* from (SELECT
/* +  FIRST_ROWS */
	PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'  ) 
	as updtime,TRACENO,TXNDATE,RRN,CARDNO,NVL(TERMNO,'-') as tmno,POSTYPE,AMOUNT,CATEGORY,ROWNUM RN,suppliername from 
	(
	
	SELECT * 
	
	
	FROM 
	(
	SELECT * FROM TBLPINS
	WHERE 
	
	WHERE {$extra_where} CATEGORY={$category} 
	AND CHARGEFLAG=1 
	 and UPDATETIME is not null
		ORDER BY UPDATETIME DESC
	
	
	)
	
	
	TBLPINS


	left join tblsuppliers on tblsuppliers.supplierid=tblpins.supplier 
	
	
	
	
	)) WHERE RN BETWEEN {$start} and {$start}+100";
}
else
{ 
    $query = "select 
	
	/* +  FIRST_ROWS */
	* from (SELECT PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'  ) as updtime,
	TRACENO,TXNDATE,RRN,CARDNO,NVL(TERMNO,'-') as tmno,
	POSTYPE,AMOUNT,CATEGORY,ROWNUM RN,suppliername 
	from (
	SELECT 
	/* +  FIRST_ROWS */
	* FROM 
	(
	SELECT * FROM  TBLPINS
	 WHERE {$extra_where} CHARGEFLAG=1  and UPDATETIME is not null ORDER BY UPDATETIME DESC
	
	)
	TBLPINS


	left join tblsuppliers on

	tblsuppliers.supplierid=tblpins.supplier)
	) 
	WHERE RN BETWEEN {$start} and {$start}+100";
}



$last_time = "";
$stid = oci_parse( $conn, $query );
if ( !$stid )
{
    $e = oci_error( $conn );
    print $query;
    print htmlentities( $e['message'] );
    exit( );
}
$r = oci_execute( $stid, OCI_DEFAULT );
if ( !$r )
{
    $e = oci_error( $stid );
    echo htmlentities( $e['message'] );
    exit( );
}
$i = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    if ( $i == 0 )
    {
        $last_time = $row[1];
    }
    $extra = "";
    if ( $i % 2 == 0 )
    {
        $extra = "bgcolor=\"#EEEEEE\"";
    }
    if ( $row[2] == "1" )
    {
        $extra = "bgcolor=\"#66CC66\"";
    }
    echo "<tr {$extra}>";
    echo "<th bgcolor=\"#AAFFEE\" scope=\"row\" align=\"center\">{$row['0']}</th>";
    $S_Flag = "";
    if ( substr( $category, 0, 3 ) == "935" )
    {
        $S_Flag = "IR";
    }
    echo "<td align=\"center\">{$S_Flag}{$row['1']}</td>";
    echo "<td align=\"center\">{$row['3']}</td>";
    echo "<td align=\"center\">{$row['4']}</td>";
    echo "<td align=\"center\">{$row['6']}</td>";
    echo "<td align=\"center\"><a href=find.php?CardNo={$row['7']}&Find=OK >{$row['7']}</a></td>";
    if ( $row[8] == "-" )
    {
        echo "<td align=\"center\"><a href=find.php?TermNo={$row['8']}&Find=OK >".$Terminal[$row[8]]."</a></td>";
    }
    else if (isset( $Terminal[$row[8]], $Limit ) )
    {
        echo "<td align=\"center\"><a href=find.php?TermNo={$row['8']}&Find=OK title=".$Limit[$row[8]].">".$Terminal[$row[8]]."</a></td>";
    }
    else
    {
        echo "<td align=\"center\"><a href=find.php?TermNo={$row['8']}&Find=OK title=0>".$row[8]."</a></td>";
    }
    echo "<td align=\"center\">{$row['10']}</td>";
    if ( $row[9] == "59" )
    {
        echo "<td align=\"center\">مجازی</td>";
    }
    else if ( $row[9] == "14" )
    {
        echo "<td align=\"center\">pos</td>";
    }
    else
    {
        echo "<td align=\"center\">نامشخص</td>";
    }
    echo "<td align=\"center\">".$Categ[$row[11]]."</td>";
    if ( isset( $row[13] ) )
    {
        echo "<td align=\"center\">".$row[13]."</td>";
    }
    else
    {
        echo "<td align=\"center\">-</td>";
    }
    echo "</tr>";
    ++$i;
}
oci_close( $conn );
echo "\n</table>\n\n</body>\n</html>\n";
?>
