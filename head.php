<?php
	putenv("NLS_LANG=American_America.UTF8");

$supplier_id=" in (14,15,16)";
	$extra_where = "supplier $supplier_id and";
	$andSupplier_id=" and supplier $supplier_id ";
	$where_supplier_id = "where supplierid $supplier_id";
	$terminal_supplier = "GROUPNO IN(select groupno from tblgrades where supplier $supplier_id) and";
	//$db = '(DESCRIPTION =(ADDRESS =(PROTOCOL = TCP)(HOST = 192.168.90.105) (PORT = 1521))(ADDRESS =(PROTOCOL = TCP) (HOST = 192.168.90.106) (PORT = 1521)) (CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = racdb) )  )';
	//$db = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.11.30)(PORT = 1521))(CONNECT_DATA = (SERVER = DEDICATED)(SID = evoucher)))';
       $db = '(DESCRIPTION =(ADDRESS =(PROTOCOL = TCP)(HOST = 172.16.11.45) (PORT = 1521))(ADDRESS =(PROTOCOL = TCP) (HOST = 172.16.11.44) (PORT = 1521)) (CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = vchrdb) ))';
	

	$user = 'thp';
	$pass = '1234';
function run_my_query( $conn , $query )
{
          
          $stid = oci_parse($conn, $query );
          if (!$stid) {
            $e = oci_error($conn);
            print htmlentities($e['message']);
            exit;
          }
          $r = oci_execute($stid, OCI_DEFAULT);
          if (!$r) {
            $e = oci_error($stid);
            echo htmlentities($e['message'])."  ($query)";
            exit;
          }
          return $stid;

}

function connect_ora()
{
	//$db = '(DESCRIPTION =(ADDRESS =(PROTOCOL = TCP)(HOST = 192.168.90.105) (PORT = 1521))(ADDRESS =(PROTOCOL = TCP) (HOST = 192.168.90.106) (PORT = 1521)) (CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = racdb) )  )';
	//$db = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = 172.16.11.30)(PORT = 1521))(CONNECT_DATA = (SERVER = DEDICATED)(SID = evoucher)))';
   	$db = '(DESCRIPTION =(ADDRESS =(PROTOCOL = TCP)(HOST = 172.16.11.45) (PORT = 1521))(ADDRESS =(PROTOCOL = TCP) (HOST = 172.16.11.44) (PORT = 1521)) (CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = vchrdb) ))';
	


	$user = 'thp';
	$pass = '1234';
	$conn = oci_connect( $user , $pass , $db );
	if( !$conn ){
        $e = oci_error();
        print htmlentities($e['message']);
        exit;
		
	}
    $stid = oci_parse( $conn , "alter session set NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'" );
    oci_execute( $stid );
    $stid = oci_parse( $conn , "alter session set NLS_TIMESTAMP_FORMAT='YYYY-MM-DD HH24:MI:SS'" );
    oci_execute( $stid );
    return $conn;    

}

function convMJD2JALALI($dt_in)
{
	$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
	$dt_out="N";


	$exp = explode( "/" , $dt_in );
	
	
	$gm = $exp[0] - 1;
	$gd = $exp[1] - 1;
	$gy = $exp[2] - 1600;
	
	
	
	$g_day_no = 365 * $gy + ($gy + 3) / 4 - ($gy + 99) / 100 + ($gy + 399) / 400;
	for ($i = 0; $i < $gm; ++$i) $g_day_no += $g_days_in_month[$i];
	if ($gm >= 0 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0) || ($gy == 409) )) ++$g_day_no;	/* leap and after Feb */
	
	$g_day_no += $gd;
	$j_day_no = $g_day_no - 79;
	
	$j_np = $j_day_no / 12053;
	$j_day_no %= 12053;
	
	$jy = 979 + 33 * $j_np + 4 * ($j_day_no / 1461);
	$j_day_no %= 1461;
	
	if ($j_day_no >= 366) {
		$jy += ($j_day_no - 1) / 365;
		$j_day_no = ($j_day_no - 1) % 365;
	}
	
	for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
		$j_day_no -= $j_days_in_month[$i];
	$jm = $i + 1;
	$jd = $j_day_no + 1;
	
	return sprintf(  "%02d/%02d"  , $jm , $jd  );
}

function utf2html ($str) { 
$ret = ""; 
$max = strlen($str); 
$last = 0;  // keeps the index of the last regular character 
for ($i=0; $i<$max; $i++) { 
 $c = $str{$i}; 
 $c1 = ord($c); 
 if ($c1>>5 == 6) {  // 110x xxxx, 110 prefix for 2 bytes unicode 
   $ret .= substr($str, $last, $i-$last); // append all the regular characters we've passed 
   $c1 &= 31; // remove the 3 bit two bytes prefix 
   $c2 = ord($str{++$i}); // the next byte 
   $c2 &= 63;  // remove the 2 bit trailing byte prefix 
   $c2 |= (($c1 & 3) << 6); // last 2 bits of c1 become first 2 of c2 
   $c1 >>= 2; // c1 shifts 2 to the right 
   $ret .= "&#" . ($c1 * 100 + $c2) . ";"; // this is the fastest string concatenation 
   $last = $i+1;        
 } 
} 
return $ret . substr($str, $last, $i); // append the last batch of regular characters 
} 


    

function show_link() {
	
	$i = 0;
	$page[ $i ][ "Name" ] = "صفحه اصلی";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/index.php";
	
	$i++;
	$page[ $i ][ "Name" ] = "فروش";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/sales.php";

	$i++;
	$page[ $i ][ "Name" ] = "فروشگاه جدید";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/admin2/new_term.php";

	$i++;
	$page[ $i ][ "Name" ] = "وارد کردن پین";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/insert_pin.php";

	$i++;
	$page[ $i ][ "Name" ] = "تغییر مبلغ";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/admin/prices.php";

	$i++;
	$page[ $i ][ "Name" ] = "جستجو";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/find.php";

	$i++;
	$page[ $i ][ "Name" ] = "مشاهده رمز";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/calladmin/get_pin.php";

	$i++;
	$page[ $i ][ "Name" ] = "لیست دسته ها";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/batch_list.php";

	$i++;
	$page[ $i ][ "Name" ] = "گروه ها";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/groups.php";

	$i++;
	$page[ $i ][ "Name" ] = "وقایع";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/logs.php";

	$i++;
	$page[ $i ][ "Name" ] = "گزارش ساعتی";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/days.php";

	$i++;
	$page[ $i ][ "Name" ] = "شهرها";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/cities.php";

	$i++;
	$page[ $i ][ "Name" ] = "نمایندگیها";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/vgroups.php";

	$i++;
	$page[ $i ][ "Name" ] = "گزارش";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/repFinalsPin.php";

	$i++;
	$page[ $i ][ "Name" ] = "فروشگاه فعال";
	$page[ $i ][ "Link" ] = "/kiccc/voucher/active_report.php";
	
       $i++;
       $page[ $i ][ "Name" ] = "بازیابی شماره سریال و تعداد پینهای لود شده";
	$page[ $i ][ "Link" ] = "/post/voucher/count3.php";
	
	$i++;
?>
<table>
	<tr>
		<td align="center">
<div class="hovermenu">
<ul align="center">

<?
//		<table border="1" width="100%" bgcolor="#FFFFFF" bordercolor="#FFFFFF" bordercolorlight="#FFFFFF" bordercolordark="#FFFFFF">
//			<tr>
//			for( $j=0 ; $j < $i ;$j ++ ){
//				if( $_SERVER["REQUEST_URI"] == $page[ $j ][ "Link" ] or substr($_SERVER["REQUEST_URI"],1,strlen($page[ $j ][ "Link" ])) == $page[ $j ][ "Link" ] ){
//					echo "<th align=\"center\" bordercolorlight=\"#FFFFFF\" bordercolordark=\"#FFFFFF\" bgcolor=\"#C0C0C0\" >".$page[ $j ][ "Name" ]."</th>";
//				}else{
//					echo "<th align=\"center\" bordercolorlight=\"#FFFFFF\" bordercolordark=\"#FFFFFF\" bgcolor=\"#C0C0C0\" ><a href=".$page[ $j ][ "Link" ]." alt=\"".$_SERVER["REQUEST_URI"]."\">".$page[ $j ][ "Name" ]."</a></th>";
//				}
//			}
//			<td><p></p></td>
//			</tr>
//		</table>

			for( $j=$i-1 ; $j >=0 ;$j -- ){
				if( $_SERVER["REQUEST_URI"] == $page[ $j ][ "Link" ] or substr($_SERVER["REQUEST_URI"],1,strlen($page[ $j ][ "Link" ])) == $page[ $j ][ "Link" ] ){
					echo "<li><d href=".$page[ $j ][ "Link" ].">".$page[ $j ][ "Name" ]."</d></li>";
				}else{
					echo "<li><a href=".$page[ $j ][ "Link" ].">".$page[ $j ][ "Name" ]."</a></li>\n";
				}
			}

				
?>				
</ul>
</div>
</td>
</tr>
</table>

<?	
	
}

function show_category(){
?>	
	<table width="100%">
		<tr>
<?
//	$msql = mysql_connect( 'localhost' , 'root' , '1234' );
//	mysql_select_db( "THP" , $msql );
//	mysql_query( "SET CHARACTER SET UTF8" );

	$result = mysql_query( "select Name,CatId,Category from tblCategory" , $msql );
	if( !$result ){
		echo "<P> error ";
		echo mysql_error( $msql );
		echo "</p>";
		exit;
	}
 	$count = mysql_num_rows( $result );
	$i = 0;
	while( $i < $count ){
		$row = mysql_fetch_row( $result );
		echo "<td><a href=/?Category=$row[2]>".$row[0]."</a></td>";
		$Categ[ $row[2] ]=$row[0];
		$i++;
	}

	mysql_free_result( $result );
	//mysql_close( $msql );
	
	
?>

		</tr>
	</table>	

<?	
}


?>
