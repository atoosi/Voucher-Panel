<?php
/*********************/
/*                   */
/*  Dezend for PHP5  */
/*         NWS       */
/*      Nulled.WS    */
/*                   */
/*********************/

echo "<html>\n<head>\n<link href=\"../test.css\" rel=\"stylesheet\" type=\"text/css\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n<title>تغییر مبلغ فروش</title>\n</head>\n<body>\n";
include( "../head.php" );
$conn = connect_ora( );
$result = run_my_query( $conn, "select to_char(current_date,'yyyy-mm-dd hh24:mi:ss') from dual" );
$row = oci_fetch_array( $result, OCI_RETURN_NULLS );
$db_date = $row[0];
$category = "";
if ( $_GET['Submit'] == "Save" && isset( $_GET['Submit'], $_GET['Amount'], $_GET['Category'], $_GET['TermGroup'] ) )
{
    $result = run_my_query( $conn, "select amount from tblcategory where category='".$_GET['Category']."'" );
    $real_amount = 0;
    if ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        $real_amount = $row[0];
    }
    if ( $real_amount + 1500 < $_GET['Amount'] )
    {
        echo "<p>مبلغ وارد شده بیشتر از حد مجاز می باشد</p>";
    }
    else
    {
        $result = run_my_query( $conn, "insert into tblprice (priceid,price,category,acdate,groupno,postype,date_,wuser) VALUES(NULL,".$_GET['Amount'].",".$_GET['Category'].",to_date('".$_GET['Date']."','yyyy-mm-dd hh24:mi:ss'),'".$_GET['TermGroup']."','0',(to_date('".$_GET['Date']."','yyyy-mm-dd hh24:mi:ss') - to_date('01-JAN-1970','DD-MON-YYYY')) * (86400),'".$_SERVER['PHP_AUTH_USER']."')" );
        oci_commit( $conn );
    }
}
else if ( isset( $_GET['Category'] ) )
{
    $category = $_GET['Category'];
    $result = run_my_query( $conn, "SELECT PRICE,TBLCATEGORY.NAME AS NAME FROM (SELECT PRICE,CATEGORY AS CAT FROM TBLPRICE WHERE CATEGORY='{$category}' ORDER BY DATE_ DESC) LEFT JOIN TBLCATEGORY ON TBLCATEGORY.CATEGORY=CAT WHERE ROWNUM<2" );
    if ( $row = oci_fetch_assoc( $result ) )
    {
        $count = 1;
    }
}
echo "\n<table width=\"100%\" border=\"0\">\n\t<tr>\n\t\t<td><a href=/voucher/index.php>Home</a></td>\n\t</tr>\n\t<tr>\n\t\t<td>\n<table width=\"50%\" border=\"1\" style=\"TABLE-LAYOUT: auto; CURSOR: auto; BORDER-COLLAPSE: collapse\">\n\t<tr>\n\t\t<th>نوع شارژ</th>\n\t\t<th>آخرين مبلغ</th>\n\t\t<th>گروه</th>\n\t\t<th>زمان آخرين مبلغ</th>\n\t\t<th>تعداد موجود</th>\n\t</tr>\n";
$result2 = run_my_query( $conn, "select tblcategory.name as NAME,tblgrades.groupname as GNAME,gr,price,cat,from_unixtime(ADATE) as ADATE  from ( select category as cat,groupno as gr,MAX(price) KEEP (DENSE_RANK FIRST ORDER BY date_ desc) as price,MAX(date_) KEEP (DENSE_RANK FIRST ORDER BY date_ desc) as ADATE from tblprice group by category,groupno) left join tblcategory on tblcategory.category=cat left join tblgrades on tblgrades.groupno=gr where gr=140 order by name" );
$i = 0;
while ( $row2 = oci_fetch_assoc( $result2 ) )
{
    echo "<tr>";
    echo "<td align=\"center\"><a href=\"prices.php?Category=".$row2['CAT']."\">".$row2['NAME']."</a></td>";
    echo "<td align=\"center\">".$row2['PRICE']."</td>";
    echo "<td align=\"center\">".$row2['GR']." - ".$row2['GNAME']."</td>";
    echo "<td align=\"center\">".$row2['ADATE']."</td>";
    echo "<td align=\"center\">-</td>";
    echo "</tr>";
    ++$i;
}
oci_free_statement( $result2 );
echo "</table>\t\n\t\t\n\t\t</td>\n\t</tr>\n\t<tr>\n\t<hr>\n\t</tr>\n<tr>\n<td>\n<table width=\"80%\" border=\"0\" style=\"TABLE-LAYOUT: auto; CURSOR: auto; BORDER-COLLAPSE: collapse\">\n\t<tr>\n\t\t<td>\n\t\t\t<form id=\"form1\" name=\"form1\" method=\"get\" action=\"\">\n";
if ( $category == "" )
{
    echo "\t\t\t\t\t";
    echo "<s";
  //  echo "elect size=\"1\" name=\"Category\">\n\t\t\t\t\t\t";
    $result = run_my_query( $conn, "select category,name,amount from tblcategory order by 1" );
    $i = 0;
    $last_time = "";
    while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
 //       echo "<option value=\"".$row[0]."\" >{$row['1']}</option>";
        ++$i;

    }
    oci_free_statement( $result );
    echo "\t\t\t\t\t</select>\n";
}
else if ( 0 < $count )
{
    echo "<input name=\"Category\" type=\"hidden\" value=\"".$category."\" maxlenght=5 />";
    echo "<label> Service Category ".$row['NAME']."</label>";
}
//echo "\t\t\t</td>\n\t\t\t<td>\n\t\t\t  <label> Price\n\t\t\t  <input name=\"Amount\" type=\"text\" value=\"";
if ( $category == "" )
{
    echo "";
}
else
{
    echo $row['PRICE'];
}
//echo "\" maxlenght=10 />\n\t\t\t  </label>\n\t\t\t</td>\n\t\t\t<td>\n\t\t\t  <label> Group\n\t\t\t\t\t";
echo "<s";
//echo "elect size=\"1\" name=\"TermGroup\">\n\t\t\t\t\t\t";
$result = run_my_query( $conn, "select groupno,groupname,groupdesc from tblgrades where groupno='140'" );
$i = 0;
$last_time = "";
while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
{
  //  echo "<option value=\"".$row[0]."\" alt=\"".$row[2]."\" >{$row['1']} - {$row['0']}</option>";
    ++$i;
}
oci_free_statement( $result );
//echo "\t\t\t\t\t</select>\n\t\t\t  </label>\n\t\t\t</td>\n\t\t\t<td>\n\t\t\t  <label> Activation Time  (YYYY-MM-DD [HH:MM:SS])\n\t\t\t  <input name=\"Date\" type=\"text\" value=\"";
echo $db_date;
//echo "\" maxlenght=30 />\n\t\t\t  </label>\n\t\t\t</td>\n\t\t\t<td>\n\t\t\t  <input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Save\" />\n\t\t\t</td>\n\t\t\t</form>\n\t\t</td>\n\t</tr>\n</table>\t\n";
if ( $category == "" )
{
    $result = run_my_query( $conn, "select priceid,price,from_unixtime(date_),category,wuser from tblprice order by date_ desc" );
}
else
{
    $result = run_my_query( $conn, "select priceid,price,from_unixtime(date_),category,groupno,wuser from tblprice where category='".$category."' order by date_ desc", $msql );
}
echo "\n<table width=\"100%\" border=\"1\">\n  <tr>\n    <th scope=\"col\">رديف</th>\n    <th scope=\"col\">زمان فعال سازي</th>\n    <th scope=\"col\">سرويس مورد نظر</th>\n    <th scope=\"col\">مبلغ</th>\n    <th scope=\"col\">user</th>\n  </tr>\n  ";
$i = 0;
$last_time = "";
while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
{
    if ( $i == 0 )
    {
        $last_time = $row[1];
    }
    $extra = "";
    if ( $i % 2 == 0 )
    {
        $extra = "bgcolor=\"#CCFFFF\"";
    }
    if ( $row[2] == "1" )
    {
        $extra = "bgcolor=\"#FF9900\"";
    }
    echo "<tr {$extra}>";
    echo "<th scope=\"row\" align=\"center\">{$row['0']}</th>";
    echo "<td align=\"center\">{$row['2']}</td>";
    echo "<td align=\"center\">{$row['3']}</td>";
    echo "<td align=\"center\">{$row['1']}</td>";
    echo "<td align=\"center\">{$row['4']}</td>";
    echo "</tr>";
    ++$i;
}
oci_close( $conn );
echo "</table>\n\n</body>\n</html>\n";
?>
