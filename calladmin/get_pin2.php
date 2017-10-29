<?php
/*********************/
/*                   */
/*  Dezend for PHP5  */
/*         NWS       */
/*      Nulled.WS    */
/*                   */
/*********************/

echo "<html>\r\n<head>\r\n<link href=\"test.css\" rel=\"stylesheet\" type=\"text/css\">\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body>\r\n";
include( "head.php" );
$conn = connect_ora( );
$f_pin_id = "";
$f_card_no = "";
$f_trace_no = "";
$f_date = "";
$where_c = "";
echo "\r\n<table width=\"100%\" border=\"0\">\r\n\t<tr>\r\n\t\t<td>\r\n\t\t<table width=\"100%\" border=\"0\">\r\n\t\t\t<tr>\r\n\t\t\t<td><a href=/voucher>صفحه اصلي</a></td>\r\n\t\t\t<td><a href=admin/insert_pin.php>وارد کردن پين</a></td>\r\n\t\t\t<td><a href=admin/prices.php>تغيير مبلغ</a></td>\r\n\t\t\t<td><a href=find.php>جستجو</a></td>\r\n\t\t\t<td><p></p></td>\r\n\t\t\t</tr>\r\n\t\t</table>\r\n\t\t</td>\r\n\t\t<form action=\"\">\r\n\t\t<td><input name=\"Submit\" type=\"su";
echo "bmit\" id=\"Submit\" value=\"Refresh\" /></td>\r\n\t\t</form>\r\n\t</tr>\r\n\r\n<tr>\r\n<td>\r\n\t<hr>\r\n\t\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n\t<td>\r\n\t\t<form method=\"GET\">\r\n\t\t<table>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t  <label> Category\r\n";
$result = run_my_query( $conn, "select name,category from tblcategory order by 1" );
echo "\t\t\t  ";
echo "<s";
echo "elect  name=\"Category\">\r\n\r\n";
$i = 0;
while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
{
    if ( $row[1] == "21" )
    {
        $sel = "selected";
    }
    else
    {
        $sel = "";
    }
    echo "<option value=\"".$row[1]."\" ".$sel." >".$row[0]."</option>\n";
    ++$i;
}
oci_free_statement( $result );
echo "\t\t\t  </select>\r\n\t\t\t  </label>\r\n\t\t\t\t</td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<label> سريال :\r\n\t\t\t\t\t<input name=\"PinId\" type=\"text\" id=\"PinId\" value=\"";
echo $f_pin_id;
echo "\" />\r\n\t\t\t\t\t</label>\r\n\t\t\t\t</td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<input name=\"Get\" type=\"Submit\" id=\"Submit\" Value=\"جستجو\"  />\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\r\n\t\t</table>\r\n\t\t</form>\r\n\t</td>\r\n</tr>\r\n<tr>\r\n<td>\r\n\t<hr>\r\n<table width=\"100%\" border=\"1\">\r\n\t<tr>\r\n";
if ( isset( $_GET['Get'] ) )
{
    $f_pin_id = $_GET['PinId'];
    $f_category = $_GET['Category'];
    if ( substr( $f_pin_id, 0, 2 ) != "IR" )
    {
        $result = run_my_query( $conn, "SELECT CHARGEPIN,CHARGEFLAG FROM TBLPINS WHERE PINSERIAL='".$f_pin_id."' AND CATEGORY='".$f_category."'" );
        if ( !( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) ) )
        {
            oci_free_statement( $result );
            $result = run_my_query( $conn, "SELECT CHARGEPIN,CHARGEFLAG FROM tblpinsold_13940114 WHERE PINSERIAL='".$f_pin_id."' AND CATEGORY='".$f_category."'" );
            $row = oci_fetch_array( $result, OCI_RETURN_NULLS );
        }
        $i = 0;
        $last_time = "";
        echo "<td>";
        if ( $row )
        {
            ++$i;
            if ( $row[1] == "1" )
            {
                $pin = pack( "H*", $row[0] );
                $keyA = pack( "H*", "1A4BBC3F842E9681" );
                $keyB = pack( "H*", "C1531D19AC137DCA" );
                $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_DECRYPT );
                $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_ENCRYPT );
                $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_DECRYPT );
                echo "<P> Serial: ".$f_pin_id."</P>";
                echo "<P>\tPIN :".$encrypted_data."</P>";
            }
            else
            {
                echo "<P> شماره رمز مذکور به کسي داده نشده است </P>";
            }
            echo "</td>";
        }
    }
    else
    {
        echo "<td>";
        echo "<P> لطفا شماره سريال را بدون IR وارد کنيد </P>";
        echo "</td>";
    }
}
oci_close( $conn );
echo "</tr>\r\n</table>\r\n</td>\r\n</tr>\r\n</table>\r\n\r\n</body>\r\n</html>\r\n";
?>
