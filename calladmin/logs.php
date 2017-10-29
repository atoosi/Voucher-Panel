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

//ini_set('display_errors', 1);

$conn = connect_ora( );

$trace = "";
$term = "";
$card = "";
$serial = "";
$category = "";
$date = "";
//if ( $Tmp_0 )
//{
    if ( isset( $_GET['Find'], $_GET['TraceNo'] ) )
    {
        $trace = $_GET['TraceNo'];
    }
    if ( isset( $_GET['TermNo'] ) )
    {
        $term = $_GET['TermNo'];
    }
    if ( isset( $_GET['CardNo'] ) )
    {
        $card = $_GET['CardNo'];
    }
    if ( isset( $_GET['SerialNo'] ) )
    {
        $serial = $_GET['SerialNo'];
    }
//}
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
echo "\n<table width=\"100%\" border=\"0\">\n\t<tr>\n\t\t<td>\n\t\t\t";
show_link( );
echo "\t\t</td>\n\t\t<form action=\"\">\n\t\t\t<input name=\"Category\" type=\"hidden\" value=\"";
echo $category;
echo "\" />\n\t\t<td><input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Refresh\" /></td>\n\t\t</form>\n\t</tr>\n\n<tr>\n<td align=\"center\">\n\t<hr>\n\t<table>\n\t<tr align=\"center\">\n\t\t<td>\n\t<table width=\"100%\">\n\t\t<tr>\n";
$stid = oci_parse( $conn, "SELECT NAME,CATID,CATEGORY FROM TBLCATEGORY ORDER BY 1" );
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
echo "\t\t</tr>\n</table>\n</td>\n\t</tr>\n\t<tr>\n\t<td width=\"100%\">\n\t<table width=\"100%\">\n\t\t<tr>\n\t\t\t<th>نوع شارژ</th>\n\t\t\t<th>فروش امروز</th>\n\t\t\t<th>مبلغ کل به ريال</th>\n\t\t</tr>\n\t\t";
$stid = oci_parse( $conn, "SELECT BANKIIN,P_NAME,E_NAME FROM TBLBANKS" );
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
    $Banks[$row[0]] = $row[1];
}


oci_free_statement( $stid );
$stid = oci_parse( $conn, "SELECT (sysdate - to_date('01-JAN-1970','DD-MON-YYYY')) * (86400) AS dt FROM dual" );
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
    $last_day_time = $row[0];
    break;
}


oci_free_statement( $stid );



$stid = oci_parse( $conn, "SELECT CATEGORY,COUNT(*),SUM(AMOUNT) FROM TBLPINS WHERE CHARGEFLAG=1 AND CAPTUREDATE = to_char(sysdate,'yyyymmdd') GROUP BY CATEGORY ORDER BY 2 DESC" );
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
$TodayTotal = 0;
$TodayTotalAmount = 0;
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $extra = "";
    if ( $i % 2 == 0 )
    {
        $extra = "bgcolor=\"#EEEEEE\"";
    }
    echo "<tr {$extra}>";
    echo "<td align=\"center\">".$Categ[$row[0]]."</td>";
    echo "<td align=\"center\">{$row['1']}</td>";
    echo "<td align=\"center\">".number_format( $row[2], 0 )."</td>";
    $TodayTotal += $row[1];
    $TodayTotalAmount += $row[2];
    echo "</tr>";
    ++$i;
}
oci_free_statement( $stid );



echo "\t\t<tr>\n\t\t</tr>\n\t\t\t<tr bgcolor=\"#EEFFEE\">\n\t\t\t\t<td align=\"center\">تعداد کل </td>\n\t\t\t\t<td align=\"center\">";
echo $TodayTotal;
echo "</td>\n\t\t\t\t<td align=\"center\">";
echo number_format( $TodayTotalAmount, 0 );
echo "</td>\n\t\t\t</tr>\n\t</table>\n\t</td>\n\n\t<td align=\"center\">\n\t<table width=\"100%\">\n\t\t<tr>\n\t\t\t<td>\n\t\t\t<img src=\"sale_graph.php\" />\n\t\t</td>\n\t\t</tr>\n\n\t</table>\n\t</td>\n\n\t</tr>\n\t<tr>\n\t<td>\n\t\t<form action=\"\" method=\"get\">\n\t\t\t<label> شماره پایانه \n\t\t\t<input name=\"TermNo\" type=\"text\" value=\"";
echo $term;
echo "\" />\n\t\t\t</label>\n\t\t\t<label> شماره سریال\n\t\t\t<input name=\"SerialNo\" type=\"text\" value=\"";
echo $serial;
echo "\" />\n\t\t\t</label>\n\t\t\t<label> تاریخ\n\t\t\t<input name=\"Date\" type=\"text\" value=\"";
echo $date;
echo "\" />\n\t\t\t     YYYYMMDD\n\t\t\t</label>\n\t\t\t<input name=\"Find\" type=\"submit\" value=\"Find\" />\n\t\t</form>\n\t</td>\n\n\n\t<td>\n\t\t\t\n\t\t";
if ( isset( $_GET['TermNo'], $_GET['Find'] ) && ( $_GET['Find'] = "Find" ) )
{
    if ( isset( $_GET['Date'] ) )
    {
        $date = "'".$_GET['Date']."'";
    }
    else
    {
        $date = "to_char(sysdate,'yyyymmdd')";
    }
    $stid = run_my_query( $conn, "SELECT ACTIONID,COUNT(1) FROM TBLLOGS WHERE TERMNO ='".$_GET['TermNo']."' AND TO_CHAR(FROM_UNIXTIME(UPDATETIME),'yyyymmdd')={$date} GROUP BY ACTIONID" );
    $SaleCount = 0;
    $RevCount = 0;

	
    while ( $mrow = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
    {
        if ( $mrow[0] == "1" )
        {
            $SaleCount = $mrow[1];
        }
        else if ( $mrow[0] == "2" )
        {
            $RevCount = $mrow[1];
        }
    }
	

	
    oci_free_statement( $stid );
    $TotCount = $SaleCount - $RevCount;
    echo "<p> تعداد خرید پایانه در امروز ".$TotCount."</p>";
}
echo "\t</td>\n\n\t</tr>\n</table>\n\t<tr>\n<td>\n\t<hr>\n";
if ( isset( $Categ[$category] ) )
{
    echo "<p><b>".$Categ[$category]."</b></p>";
}
echo "<form action=\"\" method=\"get\">\n\t<input name=\"Start\" type=\"hidden\" value=\"";
echo $start;
echo "\" />\n";
if ( $term != "" )
{
    echo "<input name=\"TermNo\" type=\"hidden\" value=\"{$term}\" />";
    echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
}
else if ( $card != "" )
{
    echo "<input name=\"CardNo\" type=\"hidden\" value=\"{$card}\" />";
    echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
}
else if ( $trace != "" )
{
    echo "<input name=\"TraceNo\" type=\"hidden\" value=\"{$trace}\" />";
    echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
}
else if ( $serial != "" )
{
    echo "<input name=\"SerialNo\" type=\"hidden\" value=\"{$serial}\" />";
    echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
}
echo "<p><input name=\"Next\" type=\"submit\" value=\"Next\" />\n<input name=\"Prev\" type=\"submit\" value=\"Prev\" /></p>\n</form>\n\n<table width=\"100%\" border=\"0\" class=\"STable\">\n  <tr bgcolor=\\\"#AAFFEE\\\">\n    <th scope=\"col\">ردیف</th>\n    <th scope=\"col\">فروشگاه</th>\n    <th scope=\"col\">سريال پين</th>\n    <th scope=\"col\">نوع شارژ</th>\n    <th scope=\"col\">زمان</th>\n    <th scope=\"col\">بانک</th>\n    <t";
echo "h scope=\"col\">شماره پيگيري</th>\n    <th scope=\"col\">پاسخ</th>\n    <th scope=\"col\">تراکنش اصلي</th>\n    <th scope=\"col\">نوع تراکنش</th>\n  </tr>\n";

$stid = run_my_query( $conn, "SELECT NAME,TERMNO FROM TBLTERMINALS" );


/*
while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
{
    $Terminal[$row[1]] = $row[0];
}

*/

oci_free_statement( $stid );





$query = "";
$end = $start + 100;
if ( $trace == "" && $term == "" && $card == "" && $serial == "" )
{
/*-----------------------------   edit by ghanadan 1393/11/20
/*-----------------------------   edit by TAHERI AND YOUSEFZADEH 1393/11/20
/* ------------------------------CHANGE THE  RNK  TO BETWEEN AND AND  FIRST_ROWS HINT FOR PERFORMANCE 


    $query = "SELECT *
  FROM (SELECT t.* ,
               ROWNUM AS RNUM
          FROM (  SELECT *
                    FROM mvtbllogs
               )t
               
               )
               WHERE RNUM>{$start} and RNUM<{$end}";
*/

/*   $query = "SELECT *
  FROM (SELECT t.*, RANK () OVER (ORDER BY termno DESC) rnk
          FROM thp.mvtbllogs t)
WHERE rnk < 100 and rnk>{$start} and rnk<{$end}";
*/

   $query = "SELECT *
  FROM (SELECT 
   /*+ FIRST_ROWS*/
  t.*, RANK () OVER (ORDER BY termno DESC) rnk
          FROM thp.mvtbllogs t)
WHERE  rnk  BETWEEN  {$start} and {$end}";

}
else if ( $serial != "" )
{
    $query = "SELECT *
  FROM (SELECT t.*,
               ROWNUM AS RNUM
          FROM (  SELECT *
                    FROM mvtbllogs
                   WHERE PINSERIAL = '".$serial."'
               )t)
 WHERE RNUM > {$start} and RNUM<{$end}";
}


$stid = run_my_query( $conn, $query );
$last_time = "";
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
    if ( substr( $row[5], 0, 3 ) == "935" )
    {
        $S_Flag = "IR";
    }
    if ( isset( $Terminal[$row[1]] ) )
    {
        echo "<td align=\"center\"><a href=logs.php?TermNo={$row['1']}&Find=OK title=\"{$row['1']}\" >".$Terminal[$row[1]]."</a></td>";
    }
    else
    {
        echo "<td align=\"center\"><a href=logs.php?TermNo={$row['1']}&Find=OK title=\"{$row['1']}\" >".$row[1]."</a></td>";
    }
    echo "<td align=\"center\"><a href=logs.php?SerialNo={$row['2']}&Find=OK >{$S_Flag}{$row['2']}</a></td>";
    if ( isset( $Categ[$row[5]] ) )
    {
        echo "<td align=\"center\">".$Categ[$row[5]]."</td>";
    }
    else
    {
        echo "<td align=\"center\"></td>";
    }
    echo "<td align=\"center\">{$row['3']}</td>";
    echo "<td align=\"center\"><a href=logs.php?CardNo={$row['4']}&Find=OK title=\"{$row['4']}\" >".$Banks[substr( $row[4], 0, 6 )]."</a></td>";
    echo "<td align=\"center\"><a href=logs.php?TraceNo={$row['6']}&Find=OK >{$row['6']}</a></td>";
    $resp = "";
    switch ( $row[7] )
    {
        case "00" :
            $resp = "موفق";
            break;
        case "84" :
            $resp = "صادر کننده کارت غیر فعال";
            break;
        case "91" :
            $resp = "زمان بیش از حد مجاز - غیر فعال";
            break;
        case "51" :
            $resp = "عدم موجودی کافی";
            break;
        case "55" :
            $resp = "رمز کارت اشتباه";
            break;
        case "NOTVALIDTERMINAL" :
            $resp = "پایانه نامعتبر";
            break;
        case "NOTPERMITED" :
            $resp = "بیش از حد مجاز";
            break;
        default :
            $resp = "{$row['7']} نامشخص";
            break;
    }
    echo "<td align=\"center\">{$resp}</td>";
    echo "<td align=\"center\"><a href=logs.php?TraceNo={$row['8']}&Find=OK >{$row['8']}</a></td>";
    if ( $row[9] == "1" )
    {
        echo "<td align=\"center\">خريد شارژ</td>";
    }
    else
    {
        echo "<td align=\"center\">اصلاحيه</td>";
    }
    echo "</tr>";
    ++$i;
}
oci_free_statement( $stid );
oci_close( $conn );
echo "\n</table>\n\n</td>\n</tr>\n</table>\n</td>\n</tr>\n</table>\n</body>\n</html>\n";


?>
