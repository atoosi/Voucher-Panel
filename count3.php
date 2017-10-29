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
echo "bmit\" id=\"Submit\" value=\"Refresh\" /></td>\r\n\t\t</form>\r\n\t</tr>\r\n\r\n<tr>\r\n<td>\r\n\t<hr>\r\n\t\r\n</td>\r\n</tr>\r\n\r\n<tr>\r\n\t<td>\r\n\t\t<form method=\"GET\">\r\n\t\t<table>\r\n\t\t\t<tr>\r\n\t\t\t\t<td>\r\n\t\t\t  <label> \r\n";
//$result = run_my_query( $conn, "select name,category from tblcategory order by 1" );
//echo "\t\t\t  ";
//echo "<s";
//echo "elect  name=\"Category\">\r\n\r\n";

oci_free_statement( $result );
echo "\t\t\t  </select>\r\n\t\t\t  </label>\r\n\t\t\t\t</td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<label> شماره فاکتور :\r\n\t\t\t\t\t<input name=\"PinId\" type=\"text\" id=\"PinId\" value=\"";
echo $f_pin_id;
echo "\" />\r\n\t\t\t\t\t</label>\r\n\t\t\t\t</td>\r\n\t\t\t\t<td>\r\n\t\t\t\t\t<input name=\"Get\" type=\"Submit\" id=\"Submit\" Value=\"جستجو\"  />\r\n\t\t\t\t</td>\r\n\t\t\t</tr>\r\n\t\t</table>\r\n\t\t</form>\r\n\t</td>\r\n</tr>\r\n<tr>\r\n<td>\r\n\t<hr>\r\n<table width=\"100%\" border=\"1\">\r\n\t<tr>\r\n";
if ( isset( $_GET['Get'] ) )
{
    $f_pin_id = $_GET['PinId'];
   
       $result = run_my_query( $conn, "select count(*),max(p.pinserial) from tblpins p , tblbatch b where b.batchid=p.batchid and b.invoiceno='".$f_pin_id."'" );
        
		$i = 0;
        $last_time = "";
         while ($row = oci_fetch_array($result, OCI_RETURN_NULLS))
		 {
		echo "<td>";
        
            ++$i;
            echo "<P>\tcont of loaded pins :".$row[0]."</P>";
			echo "<P>\tMax serial number :".$row[1]."</P>";
				
			echo "</td>";
        
    }
  
}
oci_close( $conn );
echo "</tr>\r\n</table>\r\n</td>\r\n</tr>\r\n</table>\r\n\r\n</body>\r\n</html>\r\n";
?>
