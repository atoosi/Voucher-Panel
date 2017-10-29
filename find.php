<?php
/*********************/
/*                   */
/*  Dezend for PHP5  */
/*         NWS       */
/*      Nulled.WS    */
/*                   */
/*********************/

include( "head.php" );
$conn = connect_ora( );
$f_pin_id = "";
$f_card_no = "";
$f_trace_no = "";
$f_date = "";
$where_c = "";
$f_term_no = "";
if ( isset( $_GET['Export'] ) )
{
    $filename = "terminal.csv";
    header( "Content-Disposition: attachment; filename=\"{$filename}\"" );
    header( "Content-Type: application/ms-excel" );
    if ( isset( $_GET['PinId'] ) )
    {
        $f_pin_id = $_GET['PinId'];
    }
    if ( isset( $_GET['CardNo'] ) )
    {
        $f_card_no = $_GET['CardNo'];
    }
    if ( isset( $_GET['TraceNo'] ) )
    {
        $f_trace_no = $_GET['TraceNo'];
    }
    if ( isset( $_GET['Date'] ) )
    {
        $f_date = $_GET['Date'];
    }
    if ( isset( $_GET['TermNo'] ) )
    {
        $f_term_no = $_GET['TermNo'];
    }
    $where_c = " ";
    $last = 0;
    if ( $f_pin_id != "" )
    {
        $where_c .= "pinserial='".$f_pin_id."' ";
        $last = 1;
    }
    if ( $f_card_no != "" )
    {
        if ( $last == 1 )
        {
            $where_c .= " AND ";
        }
        $where_c .= "cardno='".$f_card_no."' ";
        $last = 1;
    }
    if ( $f_trace_no != "" )
    {
        if ( $last == 1 )
        {
            $where_c .= " AND ";
        }
        $where_c .= "rrn='".$f_trace_no."' ";
        $last = 1;
    }
    if ( $f_term_no != "" )
    {
        if ( $last == 1 )
        {
            $where_c .= " AND ";
        }
        $where_c .= "termno='".$f_term_no."' ";
        $last = 1;
    }
    if ( $f_date != "" )
    {
        if ( $last == 1 )
        {
            $where_c .= " AND ";
        }
        $where_c .= "capturedate= '".$f_date."' ";
    }
    if ( $where_c != "" )
    {
        $result = run_my_query( $conn, "SELECT PINID,PINSERIAL,CHARGEFLAG,FROM_UNIXTIME(UPDATETIME),TRACENO,TXNDATE,RRN,FROM_UNIXTIME(TBLBATCH.BATCHDATE),TBLCATEGORY.NAME,CARDNO,TERMNO,TBPINS.AMOUNT,TBPINS.CATEGORY,ROWNUM AS RNUM FROM (SELECT * FROM TBLPINS WHERE {$where_c} AND CHARGEFLAG=1 ORDER BY UPDATETIME DESC) TBPINS LEFT JOIN TBLBATCH ON TBPINS.BATCHID=TBLBATCH.BATCHID LEFT JOIN TBLCATEGORY ON TBLCATEGORY.CATEGORY=TBPINS.CATEGORY ORDER BY UPDATETIME DESC" );
    }
    while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        echo "\"{$row['0']}\",\"{$row['1']}\",\"{$row['2']}\",\"{$row['3']}\",\"{$row['4']}\",\"{$row['5']}\",\"{$row['6']}\",\"{$row['7']}\",\"{$row['8']}\",\"{$row['9']}\",\"{$row['10']}\",\"{$row['11']}\",\"{$row['12']}\"\n";
    }
    if ( $where_c != "" )
    {
        $result = run_my_query( $conn, "SELECT PINID,PINSERIAL,CHARGEFLAG,FROM_UNIXTIME(UPDATETIME),TRACENO,TXNDATE,RRN,FROM_UNIXTIME(TBLBATCH.BATCHDATE),TBLCATEGORY.NAME,CARDNO,TERMNO,TBPINS.AMOUNT,TBPINS.CATEGORY,ROWNUM AS RNUM FROM (SELECT * FROM TBLPINSOLD WHERE {$where_c} AND CHARGEFLAG=1 ORDER BY UPDATETIME DESC) TBPINS LEFT JOIN TBLBATCH ON TBPINS.BATCHID=TBLBATCH.BATCHID LEFT JOIN TBLCATEGORY ON TBLCATEGORY.CATEGORY=TBPINS.CATEGORY ORDER BY UPDATETIME DESC" );
    }
    while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        echo "\"{$row['0']}\",\"{$row['1']}\",\"{$row['2']}\",\"{$row['3']}\",\"{$row['4']}\",\"{$row['5']}\",\"{$row['6']}\",\"{$row['7']}\",\"{$row['8']}\",\"{$row['9']}\",\"{$row['10']}\",\"{$row['11']}\",\"{$row['12']}\"\n";
    }
    oci_close( $conn );
}
else
{
    echo "<html dir=\"rtl\">\n<head>\n<link href=\"test.css\" rel=\"stylesheet\" type=\"text/css\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n<title>جستجو</title>\n</head>\n<body>\n";
    $start = 0;
    $end = 100;
    $ostart = 0;
    $oend = 100;
    $total_day = 8;
    if ( "\"{$row['0']}\",\"{$row['1']}\",\"{$row['2']}\",\"{$row['3']}\",\"{$row['4']}\",\"{$row['5']}\",\"{$row['6']}\",\"{$row['7']}\",\"{$row['8']}\",\"{$row['9']}\",\"{$row['10']}\",\"{$row['11']}\",\"{$row['12']}\"\n" )
    {
        if ( isset( $_GET['Find'], $_GET['PinId'] ) )
        {
            $f_pin_id = $_GET['PinId'];
        }
        if ( isset( $_GET['CardNo'] ) )
        {
            $f_card_no = $_GET['CardNo'];
        }
        if ( isset( $_GET['TraceNo'] ) )
        {
            $f_trace_no = $_GET['TraceNo'];
        }
        if ( isset( $_GET['Date'] ) )
        {
            $f_date = $_GET['Date'];
        }
        if ( isset( $_GET['TermNo'] ) )
        {
            $f_term_no = $_GET['TermNo'];
        }
        $where_c = " ";
        $last = 0;
        if ( $f_pin_id != "" )
        {
            $where_c .= "pinserial='".$f_pin_id."' ";
            $last = 1;
        }
        if ( $f_card_no != "" )
        {
            if ( $last == 1 )
            {
                $where_c .= " AND ";
            }
            $where_c .= "cardno='".$f_card_no."' ";
            $last = 1;
        }
        if ( $f_trace_no != "" )
        {
            if ( $last == 1 )
            {
                $where_c .= " AND ";
            }
            $where_c .= "rrn='".$f_trace_no."' ";
            $last = 1;
        }
        if ( $f_term_no != "" )
        {
            if ( $last == 1 )
            {
                $where_c .= " AND ";
            }
            $where_c .= "termno='".$f_term_no."' ";
            $last = 1;
        }
        if ( $f_date != "" )
        {
            if ( $last == 1 )
            {
                $where_c .= " AND ";
            }
            $where_c .= "capturedate= '".$f_date."' ";
        }
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
        if ( isset( $_GET['OStart'] ) )
        {
            $ostart = $_GET['OStart'];
        }
        if ( isset( $_GET['OEnd'] ) )
        {
            $oend = $_GET['OEnd'];
        }
        if ( isset( $_GET['OPrev'] ) )
        {
            $ostart += 100;
        }
        if ( isset( $_GET['ONext'] ) && 100 <= $ostart )
        {
            $ostart -= 100;
        }
    }
    echo "\n<table width=\"100%\" border=\"0\">\n\t<tr>\n\t\t<td>\n\t\t\t\t";
    show_link( );
    echo "\t\t</td>\n\t\t<form action=\"\">\n\t\t<td><input name=\"Submit\" type=\"submit\" id=\"Submit\" value=\"Refresh\" /></td>\n\t\t</form>\n\t</tr>\n\n<tr>\n<td>\n\t<hr>\n\n</td>\n</tr>\n\n<tr>\n\t<td>\n\t\t<table width=\"100%\">\n\t\t\t<tr>\n\t\t\t\t<td>\n\t\t\t\t\t<form method=\"GET\">\n\t\t\t\t\t<table>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t\t<label> سریال :\n\t\t\t\t\t\t\t\t<input name=\"PinId\" type=\"text\" id=\"PinId\" value=\"";
    echo $f_pin_id;
    echo "\" />\n\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<label> شماره کارت :\n\t\t\t\t\t\t\t\t<input name=\"CardNo\" type=\"text\" id=\"CardNo\" value=\"";
    echo $f_card_no;
    echo "\"  />\n\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<label> شماره پایانه :\n\t\t\t\t\t\t\t\t<input name=\"TermNo\" type=\"text\" id=\"TermNo\" value=\"";
    echo $f_term_no;
    echo "\"  />\n\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<label> شماره پیگیری :\n\t\t\t\t\t\t\t\t<input name=\"TraceNo\" type=\"text\" id=\"TraceNo\" value=\"";
    echo $f_trace_no;
    echo "\"  />\n\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<label> تاریخ میلادی :\n\t\t\t\t\t\t\t\t<input name=\"Date\" type=\"text\" id=\"Date\" value=\"";
    echo $f_date;
    echo "\"  />\n\t\t\t\t\t\t\t\tYYYYMMDD\n\t\t\t\t\t\t\t\t</label>\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<input name=\"Find\" type=\"Submit\" id=\"Submit\" Value=\"جستجو\"  />\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t\t<tr>\n\t\t\t\t\t\t\t<td>\n\t\t\t\t\t\t\t\t<input name=\"Export\" type=\"Submit\" id=\"Submit\" Value=\"Export\" />\n\t\t\t\t\t\t\t</td>\n\t\t\t\t\t\t</tr>\n\t\t\t\t\t</table>\n\t\t\t\t\t</form>\n\t\t\t\t</td>\n\t\t\t\t<td>\n\t\t\t\t\t<table width=\"100%\">\n\t\t\t\t\t\t";
    if ( isset( $_GET['TermNo'] ) )
    {
        $stid = run_my_query( $conn, "SELECT ((sysdate-{$total_day}) - to_date('01-JAN-1970','DD-MON-YYYY')) * (86400),to_char(sysdate-{$total_day},'yyyy-mm-dd'),to_char(sysdate-{$total_day},'yyyymmdd') FROM DUAL" );
        while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
        {
            $DATE = $row[0];
            $DATE1 = $row[1];
            $DATE2 = $row[2];
        }
        oci_free_statement( $stid );
        $stid = run_my_query( $conn, "SELECT to_char(to_date(CAPTUREDATE,'yyyymmdd'),'yyyy-mm-dd'),CATEGORY,COUNT(*),SUM(AMOUNT),MIN(UPDATETIME) FROM TBLPINS WHERE TERMNO='".$_GET['TermNo']."' AND CHARGEFLAG=1 GROUP BY CAPTUREDATE,CATEGORY" );
        $i = 0;
        while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
        {
            $m_date = $row[4];
            $diff_date = round( ( strtotime( $row[0] ) - strtotime( $DATE1 ) ) / 86400, 0 );
            $Day[$row[1]][$diff_date] = $row[2];
            $Day_Name[$diff_date] = convmjd2jalali( strftime( "%m/%d/%Y", $m_date ) );
            $DayAmount[$row[1]][$diff_date] = $row[3];
            if ( isset( $DayTotal[$diff_date] ) )
            {
                $DayTotal[$diff_date] += $row[2];
            }
            else
            {
                $DayTotal[$diff_date] = $row[2];
            }
            if ( isset( $DayTotalAmount[$diff_date] ) )
            {
                $DayTotalAmount[$diff_date] += $row[2];
            }
            else
            {
                $DayTotalAmount[$diff_date] = $row[2];
            }
            ++$i;
        }
        oci_free_statement( $stid );
        $stid = run_my_query( $conn, "SELECT to_char(to_date(CAPTUREDATE,'yyyymmdd'),'yyyy-mm-dd'),CATEGORY,COUNT(1),SUM(AMOUNT),MIN(UPDATETIME) FROM TBLPINSOLD WHERE CAPTUREDATE>=to_char(sysdate-{$total_day},'yyyymmdd') AND TERMNO='".$_GET['TermNo']."' AND CHARGEFLAG=1 GROUP BY CAPTUREDATE,CATEGORY" );
        $i = 0;
        while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
        {
            $m_date = $row[4];
            $diff_date = round( ( strtotime( $row[0] ) - strtotime( $DATE1 ) ) / 86400, 0 );
            $Day[$row[1]][$diff_date] = $row[2];
            $Day_Name[$diff_date] = convmjd2jalali( strftime( "%m/%d/%Y", $m_date ) );
            $DayAmount[$row[1]][$diff_date] = $row[3];
            if ( isset( $DayTotal[$diff_date] ) )
            {
                $DayTotal[$diff_date] += $row[2];
            }
            else
            {
                $DayTotal[$diff_date] = $row[2];
            }
            if ( isset( $DayTotalAmount[$diff_date] ) )
            {
                $DayTotalAmount[$diff_date] += $row[2];
            }
            else
            {
                $DayTotalAmount[$diff_date] = $row[2];
            }
            ++$i;
        }
        oci_free_statement( $stid );
        $stid = run_my_query( $conn, "SELECT NAME,TOTAL,CATEGORY,ENAME FROM TBLCATEGORY LEFT JOIN ( SELECT COUNT(1) AS TOTAL,CATEGORY AS CATEGORY2 FROM TBLPINS WHERE CHARGEFLAG='0' GROUP BY CATEGORY ) ON TBLCATEGORY.CATEGORY=CATEGORY2" );
        $i = 0;
        $Total = 0;
        $j = 0;
        $diff_date = 0;
        while ( $row = oci_fetch_array( $stid, OCI_RETURN_NULLS ) )
        {
            $Name[$row[2]] = $row[0];
            if ( isset( $Day[$row[2]] ) )
            {
                $k = 0;
                for ( ; $k <= $total_day; ++$k )
                {
                    if ( isset( $Day[$row[2]][$k] ) )
                    {
                    }
                    else
                    {
                        $Day[$row[2]][$k] = 0;
                    }
                    if ( !isset( $DayTotal[$k] ) )
                    {
                        $DayTotal[$k] = 0;
                    }
                    if ( !isset( $Day_Name[$k] ) )
                    {
                        $d = $total_day - $k;
                        $Day_Name[$k] = convmjd2jalali( strftime( "%m/%d/%Y", date( strtotime( "-8 day" ) ) ) );
                    }
                }
            }
            if ( $i == 0 )
            {
                echo "<tr>";
                echo "<td></td>";
                $k = 0;
                for ( ; $k <= $total_day; ++$k )
                {
                    if ( isset( $Day_Name[$k] ) )
                    {
                        echo "<td align=\"center\">".$Day_Name[$k]."</td>";
                    }
                    else
                    {
                        echo "<td align=\"center\">-</td>";
                    }
                }
                echo "</tr>";
            }
            $extra = "";
            if ( $i % 2 == 0 )
            {
                $extra = "bgcolor=\"#FFFFCC\"";
            }
            echo "<tr {$extra}>";
            echo "<td>{$row['0']}</td>";
            $k = 0;
            for ( ; $k <= $total_day; ++$k )
            {
                if ( isset( $Day[$row[2]][$k] ) )
                {
                    echo "<td align=\"center\">".$Day[$row[2]][$k]."</td>";
                }
                else
                {
                    echo "<td align=\"center\">-</td>";
                }
            }
            echo "</tr>";
            ++$i;
        }
        oci_free_statement( $stid );
    }
    echo "\t\t\t\t\t</table>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</table>\n\t</td>\n\t\n</tr>\n<tr>\n<td>\n\t<hr>\n\t\n\t<table width=\"100%\">\n\t\t<tr>\n\t\t<td align=\"center\">\n";
    if ( isset( $_GET['TermNo'] ) )
    {
        echo "<img border=\"0\" src=\"term_sale_graph.php?TermNo=".$_GET['TermNo']."\" />";
        echo "</td><td align=\"center\">";
        $result = run_my_query( $conn, "SELECT NAME,LOCATION,PHONE,MOBILE,TBLTERMINALS.GROUPNO,STATUS,TBLGRADES.GROUPNAME FROM TBLTERMINALS LEFT JOIN TBLGRADES ON TBLGRADES.GROUPNO=TBLTERMINALS.GROUPNO WHERE TERMNO='".$_GET['TermNo']."'" );
        $i = 0;
        $last_time = "";
        while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
        {
            echo "<p align=\"center\"><b>".$row[0]."</b></p>";
            echo "<p align=\"center\">".$row[1]."</p>";
            echo "<p align=\"center\">".$row[2]."</p>";
            echo "<p align=\"center\">".$row[3]."</p>";
            echo "<p align=\"center\">".$row[4]." - ".$row[6]."</p>";
            ++$i;
        }
        oci_free_statement( $result );
    }
    echo "\t\n\t</td>\n</tr>\n</table>\n</td>\n</tr>\n<tr>\n\t<td>\n<table width=\"100%\" border=\"0\" cellpadding=\"2\" class=\"STable\">\n  ";
    $count = 0;
    echo "\t<tr>\n\t\t<th> ماه جاری </th>\n\t\t<th>";
    echo $count;
    echo "</th>\n\t\t<td>\n<form action=\"\" method=\"get\">\n\t";
    if ( isset( $_GET['Find'] ) )
    {
        $my_head = "";
        if ( isset( $_GET['PinId'] ) )
        {
            echo "<input name=\"PinId\" type=\"hidden\" value=\"".$_GET['PinId']."\" />";
        }
        if ( isset( $_GET['CardNo'] ) )
        {
            echo "<input name=\"CardNo\" type=\"hidden\" value=\"".$_GET['CardNo']."\" />";
        }
        if ( isset( $_GET['TraceNo'] ) )
        {
            echo "<input name=\"TraceNo\" type=\"hidden\" value=\"".$_GET['TraceNo']."\" />";
        }
        if ( isset( $_GET['Date'] ) )
        {
            echo "<input name=\"Date\" type=\"hidden\" value=\"".$_GET['Date']."\" />";
        }
        if ( isset( $_GET['TermNo'] ) )
        {
            echo "<input name=\"TermNo\" type=\"hidden\" value=\"".$_GET['TermNo']."\" />";
        }
        echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
    }
    echo "\t<input name=\"Start\" type=\"hidden\" value=\"";
    echo $start;
    echo "\" />\n<p><input name=\"Next\" type=\"submit\" value=\"Next\" />\n<input name=\"Prev\" type=\"submit\" value=\"Prev\" /></p>\n</form>\n\t\t</td>\n\t</tr>\n  <tr bgcolor=\"#00FFCC\">\n    <th scope=\"col\">ردیف</th>\n    <th scope=\"col\">سريال پين</th>\n    <th scope=\"col\">زمان آخرين تغييرات</th>\n    <th scope=\"col\">شماره پيگيري</th>\n    <th scope=\"col\">شماره مرجع</th>\n    <th scope=\"col\">شماره";
    echo " کارت</th>\n    <th scope=\"col\">شماره پایانه</th>\n    <th scope=\"col\">تاريخ ورود دسته </th>\n    <th scope=\"col\">نوع کارت</th>\n    <th scope=\"col\">مبلغ به ريال</th>\n  </tr>\n";
    $i = 0;
    $last_time = "";
    if ( $where_c != "" )
    {
        $result = run_my_query( $conn, "SELECT * FROM (SELECT PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'),TRACENO,TXNDATE,RRN,TO_CHAR(FROM_UNIXTIME(TBLBATCH.BATCHDATE),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'),TBLCATEGORY.NAME,CARDNO,TERMNO,TBPINS.AMOUNT,TBPINS.CATEGORY,ROWNUM AS RNUM FROM (SELECT * FROM TBLPINS WHERE {$where_c} AND CHARGEFLAG=1 ORDER BY UPDATETIME DESC) TBPINS LEFT JOIN TBLBATCH ON TBPINS.BATCHID=TBLBATCH.BATCHID LEFT JOIN TBLCATEGORY ON TBLCATEGORY.CATEGORY=TBPINS.CATEGORY ORDER BY UPDATETIME DESC) WHERE RNUM>{$start} AND RNUM<{$end}" );
        while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
        {
            if ( $i == 0 )
            {
                $last_time = $row[1];
            }
            $extra = "";
            if ( $i % 2 == 0 )
            {
                $extra = "bgcolor=\"#FFFFCC\"";
            }
            echo "<tr {$extra}>";
            echo "<th bgcolor=\"#00FFCC\" scope=\"row\" align=\"center\">{$row['0']}</th>";
            if ( substr( $row[12], 0, 3 ) == "935" )
            {
                echo "<td align=\"center\">IR{$row['1']}</td>";
            }
            else
            {
                echo "<td align=\"center\">{$row['1']}</td>";
            }
            echo "<td align=\"center\">{$row['3']}</td>";
            echo "<td align=\"center\">{$row['4']}</td>";
            echo "<td align=\"center\">{$row['6']}</td>";
            echo "<td align=\"center\">{$row['9']}</td>";
            echo "<td align=\"center\">{$row['10']}</td>";
            echo "<td align=\"center\">{$row['7']}</td>";
            echo "<td align=\"center\">{$row['8']}</td>";
            echo "<td align=\"center\">{$row['11']}</td>";
            echo "</tr>";
            ++$i;
        }
        oci_free_statement( $result );
    }
    echo "</table>\n</td>\n</tr>\n<tr>\n\t<td>\n</td>\n</tr>\n<tr>\n\t<td>\n<table width=\"100%\" border=\"0\" cellpadding=\"2\" class=\"STable\">\n  ";
    $count = 0;
    if ( $where_c != "" )
    {
        $result = run_my_query( $conn, "SELECT * FROM (SELECT PINID,PINSERIAL,CHARGEFLAG,TO_CHAR(FROM_UNIXTIME(UPDATETIME),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'),TRACENO,TXNDATE,RRN,TO_CHAR(FROM_UNIXTIME(TBLBATCH.BATCHDATE),'YYYY-MM-DD HH24:MI:SS','nls_calendar=persian'),TBLCATEGORY.NAME,CARDNO,TERMNO,TBPINS.AMOUNT,TBPINS.CATEGORY,ROWNUM AS RNUM FROM (SELECT * FROM TBLPINSOLD WHERE {$where_c} AND CHARGEFLAG=1 ORDER BY UPDATETIME DESC) TBPINS LEFT JOIN TBLBATCH ON TBPINS.BATCHID=TBLBATCH.BATCHID LEFT JOIN TBLCATEGORY ON TBLCATEGORY.CATEGORY=TBPINS.CATEGORY ORDER BY UPDATETIME DESC) WHERE RNUM>{$ostart} AND RNUM<{$ostart}+{$oend}" );
    }
    echo "\t<tr>\n\t\t<th> ماههای گذشته </th>\n\t\t<th>";
    echo $count;
    echo "</th>\n\t\t<th>\n<form action=\"\" method=\"get\">\n\t";
    if ( isset( $_GET['Find'] ) )
    {
        $my_head = "";
        if ( isset( $_GET['PinId'] ) )
        {
            echo "<input name=\"PinId\" type=\"hidden\" value=\"".$_GET['PinId']."\" />";
        }
        if ( isset( $_GET['CardNo'] ) )
        {
            echo "<input name=\"CardNo\" type=\"hidden\" value=\"".$_GET['CardNo']."\" />";
        }
        if ( isset( $_GET['TraceNo'] ) )
        {
            echo "<input name=\"TraceNo\" type=\"hidden\" value=\"".$_GET['TraceNo']."\" />";
        }
        if ( isset( $_GET['Date'] ) )
        {
            echo "<input name=\"Date\" type=\"hidden\" value=\"".$_GET['Date']."\" />";
        }
        if ( isset( $_GET['TermNo'] ) )
        {
            echo "<input name=\"TermNo\" type=\"hidden\" value=\"".$_GET['TermNo']."\" />";
        }
        echo "<input name=\"Find\" type=\"hidden\" value=\"OK\" />";
    }
    echo "\t<input name=\"OStart\" type=\"hidden\" value=\"";
    echo $ostart;
    echo "\" />\n<p><input name=\"ONext\" type=\"submit\" value=\"Next\" />\n<input name=\"OPrev\" type=\"submit\" value=\"Prev\" /></p>\n</form>\n\t\t</th>\n\t</tr>\n  <tr bgcolor=\"#00FFCC\">\n    <th scope=\"col\">ردیف</th>\n    <th scope=\"col\">سريال پين</th>\n    <th scope=\"col\">زمان آخرين تغييرات</th>\n    <th scope=\"col\">شماره پيگيري</th>\n    <th scope=\"col\">شماره مرجع</th>\n    <th scope=\"col\">شمار";
    echo "ه کارت</th>\n    <th scope=\"col\">شماره پایانه</th>\n    <th scope=\"col\">تاريخ ورود دسته </th>\n    <th scope=\"col\">نوع کارت</th>\n    <th scope=\"col\">مبلغ به ريال</th>\n  </tr>\n  ";
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
            $extra = "bgcolor=\"#FFFFCC\"";
        }
        echo "<tr {$extra}>";
        echo "<th bgcolor=\"#00FFCC\" scope=\"row\" align=\"center\">{$row['0']}</th>";
        if ( substr( $row[12], 0, 3 ) == "935" )
        {
            echo "<td align=\"center\">IR{$row['1']}</td>";
        }
        else
        {
            echo "<td align=\"center\">{$row['1']}</td>";
        }
        echo "<td align=\"center\">{$row['3']}</td>";
        echo "<td align=\"center\">{$row['4']}</td>";
        echo "<td align=\"center\">{$row['6']}</td>";
        echo "<td align=\"center\">{$row['9']}</td>";
        echo "<td align=\"center\">{$row['10']}</td>";
        echo "<td align=\"center\">{$row['7']}</td>";
        echo "<td align=\"center\">{$row['8']}</td>";
        echo "<td align=\"center\">{$row['11']}</td>";
        echo "</tr>";
        ++$i;
    }
    oci_close( $conn );
    echo "</table>\t</td>\n</tr>\n</table>\n</body>\n</html>\n";
}
?>
