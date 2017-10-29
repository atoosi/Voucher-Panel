<?php
/*********************/
/*                   */
/*  Dezend for PHP5  */
/*         NWS       */
/*      Nulled.WS    */
/*                   */
/*********************/

//echo "<html dir=\"rtl\">\r\n<head>\r\n<link href=\"../test.css\" rel=\"stylesheet\" type=\"text/css\">\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n<title>وارد کردن پین</title>\r\n</head><body>\r\n";
echo "<html dir=\"rtl\">\n<head>\n<link href=\"test.css\" rel=\"stylesheet\" type=\"text/css\">\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n</head><body>\n";

include( "./head.php" );
//echo "\n<table width=\"100%\" border=\"0\">\n\t<tr>\n\t\t<td>\n\t\t\t\t";
show_link( );
$conn = connect_ora( );
$target_table = "TBLPINS";
if ( isset( $_POST['Submit2'] ) )
{
    $Batch_ID = "";
    $timestamp = "";
    $supplier = 0;
    $invoice_no = "";
    $batch_name = $_POST['BatchName'];
    $category = $_POST['Category'];
    $invoice_no = $_POST['InvoiceNo'];
    $supplier = $_POST['Supplier'];
    $fname = $_FILES['Filename']['tmp_name'];
    $file_name = $_FILES['Filename']['name'];
    $handle = fopen( $fname, "r" );
    $result = run_my_query( $conn, "select cast((sysdate - to_date('01-01-1970','DD-MM-YYYY')) * (86400) as number(10)) from dual" );
    if ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        $timestamp = $row[0];
        oci_free_statement( $result );
    }
    else
    {
        exit( );
    }
    $inc_amount = 0;
    $i = 0;
    $result = run_my_query( $conn, "select category,amount,name from tblcategory" );
    while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        $CategoryTable[$row[0]] = $row[1];
        $cat_i[$i]['Category'] = $row[0];
        $cat_i[$i]['Name'] = $row[2];
        ++$i;
    }
    $c_count = $i;
    oci_free_statement( $result );
    $result = run_my_query( $conn, "select TBLBATCH_BATCHID_SEQ.nextval from dual" );
    if ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        $Batch_ID = $row[0];
        oci_free_statement( $result );
    }
    else
    {
        echo "<p> timestamp problem </p>";
        exit( );
    }
    $result = run_my_query( $conn, "insert into tblbatch ( batchid,batchdate , category , name , invoiceno ,filename, supplier) values({$Batch_ID},".$timestamp.",'".$category."','".$batch_name."','".$invoice_no."','".$file_name."',{$supplier})" );
    oci_commit( $conn );
    $pin_total = 0;
    $tmp_total = 0;
    $a_pin_total = 0;
    $a_row = 0;
    if ( $handle )
    {
        $keyA = pack( "H*", "1a4bbc3f842E9681" );
        $keyB = pack( "H*", "c1531D19ac137dca" );
        while ( !feof( $handle ) )
        {
            $line = fgets( $handle, 100 );
			
			if ( substr( $category, 0, 3 ) == "920" )
            {				
				 if ( 44 == strlen( $line ) )
                    {
					  sscanf( $line, "%16s;%16s", $serial, $pin );
					   					   
                        if ( strlen( $pin ) == 16 && strlen( $serial ) == 16 )
                        {
                              $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                            $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                            $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                            $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                            oci_bind_by_name( $statement, ":serial", $serial, -1 );
                            oci_bind_by_name( $statement, ":categ", $category, -1 );
                            oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                            oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                            oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                            if ( oci_execute( $statement ) == false )
                            {
                                $e = oci_error( $statement );
                                echo "<p> serial: {$serial} </p>";
                                echo htmlentities( $e['message'] );
                                oci_rollback( $conn );
                                exit( );
                            }
                            $a_row = oci_num_rows( $statement );
                            ++$pin_total;
                            $a_pin_total = $a_pin_total + $a_row;
                        }
                    }
                    else
                    {
                        if (    (strlen($line)<30 ) &&   ($pin_total==0)   )
                        {
                            continue;
                            echo "<p> First row is not Serial </p>";
                            exit( );
                        }
                    }
                }
                    
            else if ( substr( $category, 0, 3 ) == "935" )
            {
                if ( 26 <= strlen( $line ) && strchr( $line, ";" ) )
                {
                    sscanf( $line, "%12s;%12s", $serial, $pin );
                    if ( strlen( $serial ) == 12 && strlen( $pin ) == 12 )
                    {
                        $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                        $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                        $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                        $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                        oci_bind_by_name( $statement, ":serial", $serial, -1 );
                        oci_bind_by_name( $statement, ":categ", $category, -1 );
                        oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                        oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                        oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                        if ( oci_execute( $statement ) == false )
                        {
                            $e = oci_error( $statement );
                            echo "<p> serial: {$serial} </p>";
                            echo htmlentities( $e['message'] );
                            oci_rollback( $conn );
                            exit( );
                        }
                        $a_row = oci_num_rows( $statement );
                        ++$pin_total;
                        $a_pin_total = $a_pin_total + $a_row;
                    }
                    else
                    {
                        echo "<p> incorrect len ".strlen( $serial ).",".strlen( $pin )." </p>";
                    }
                }
                else
                {
                    if ( strlen( $line ) == 41 )
                    {
                        sscanf( $line, "IR%12s%16s%9s", $serial, $pin, $amount );
                        if ( strlen( $serial ) == 12 && strlen( $pin ) == 16 )
                        {
                            if ( $amount == $CategoryTable[$category] )
                            {
                                $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                                $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                                $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                                $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                                oci_bind_by_name( $statement, ":serial", $serial, -1 );
                                oci_bind_by_name( $statement, ":categ", $category, -1 );
                                oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                                oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                                oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                                if ( oci_execute( $statement ) == false )
                                {
                                    $e = oci_error( $statement );
                                    echo "<p> serial: {$serial} </p>";
                                    echo htmlentities( $e['message'] );
                                    oci_rollback( $conn );
                                    exit( );
                                }
                                $a_row = oci_num_rows( $statement );
                                ++$pin_total;
                                $a_pin_total = $a_pin_total + $a_row;
                            }
                            else
                            {
                                ++$inc_amount;
                            }
                        }
                        else
                        {
                            echo "<p> incorrect len ".strlen( $serial ).",".strlen( $pin )." </p>";
                        }
                    }
                    else
                    {
                        if ( strlen( 3 < $line ) && substr( $line, 0, 3 ) == "PIN" )
                        {
                            echo "<p> First row is not Serial </p>";
                            exit( );
                        }
                        else
                        {
                            echo "<p>".strlen( $line )."</p>";
                        }
                    }
                }
            }
            else if ( substr( $category, 0, 3 ) == "919" )
            {
                if ( 50 <= strlen( $line ) )
                {
                    sscanf( $line, "%[^,], %[^,], %[^,], %[^,], %[^,], %[^,]", $serial, $pin, $dum1, $amount, $dum2, $dum3 );
                    if ( strlen( $pin ) == 14 && $amount == $CategoryTable[$category] )
                    {
                        $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                        $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                        $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                        $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                        oci_bind_by_name( $statement, ":serial", $serial, -1 );
                        oci_bind_by_name( $statement, ":categ", $category, -1 );
                        oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                        oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                        oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                        if ( oci_execute( $statement ) == false )
                        {
                            $e = oci_error( $statement );
                            echo "<p> serial: {$serial} </p>";
                            echo htmlentities( $e['message'] );
                            oci_rollback( $conn );
                            exit( );
                        }
                        $a_row = oci_num_rows( $statement );
                        ++$pin_total;
                        $a_pin_total = $a_pin_total + $a_row;
                    }
                }
                else
                {
                    if ( 34 == strlen( $line ) )
                    {
                        sscanf( $line, "%17s%15s", $serial, $pin );
						//----- added by jamal for check amount of hamrahe aval
						if  ( 
                                          (  $category==9191  and substr( $serial,4,1)!='1' ) or
    					                  (  $category==9192  and substr( $serial,4,1)!='3' ) or
    					                  (  $category==9194  and substr( $serial,4,1)!='4' ) or    					       
                                          (  $category==9195  and substr( $serial,4,1)!='0' ) or
                                          (  $category==9196  and substr( $serial,4,1)!='2' ) 
							) {
							echo htmlentities(  $category.'***'. substr( $serial,4,1).'Invalid Amount' );
                                oci_rollback( $conn );
                                exit( );
						}
						//------------------------------------------------
                        if ( strlen( $pin ) == 15 && strlen( $serial ) == 17 )
                        {
                            $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                            $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                            $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                            $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                            oci_bind_by_name( $statement, ":serial", $serial, -1 );
                            oci_bind_by_name( $statement, ":categ", $category, -1 );
                            oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                            oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                            oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                            if ( oci_execute( $statement ) == false )
                            {
                                $e = oci_error( $statement );
                                echo "<p> serial: {$serial} </p>";
                                echo htmlentities( $e['message'] );
                                oci_rollback( $conn );
                                exit( );
                            }
                            $a_row = oci_num_rows( $statement );
                            ++$pin_total;
                            $a_pin_total = $a_pin_total + $a_row;
                        }
                    }
                    else
                    {
                        if ( !( strlen( 3 < $line ) && substr( $line, 0, 3 ) == "PIN" ) )
                        {
                            continue;
                            echo "<p> First row is not Serial </p>";
                            exit( );
                        }
                    }
                }
            }
            else if ( !( substr( $category, 0, 3 ) == "932" ) )
            {
                continue;
            }
            else if ( strlen( $line ) == 50 )
            {
                sscanf( $line, "%5s;%12s;%14s;%12s;%1s",$amount,$temp1 ,$pin ,$serial ,$temp2 );// infotech format file added by ghanadan
               if ( $amount == $CategoryTable[$category] ) 
                {
                    $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                    $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                    $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                    $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                    oci_bind_by_name( $statement, ":serial", $serial, -1 );
                    oci_bind_by_name( $statement, ":categ", $category, -1 );
                    oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                    oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                    oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                    oci_execute( $statement );
                    $a_row = oci_num_rows( $statement );
                    oci_free_statement( $statement );
                    ++$pin_total;
                    $a_pin_total = $a_pin_total + $a_row;
                }
                else
                {
                    ++$inc_amount;
                }
            }
			
			 else if ( strlen( $line ) == 51 )
            {
                sscanf( $line, "%5s;%13s;%14s;%12s;%1s",$amount ,$temp1 ,$pin ,$serial ,$temp2 );// infotech format file added by ghanadan
                           
			if ( $amount == $CategoryTable[$category] ) 
        //    if ( strlen( $serial ) == 12 && strlen( $pin ) == 14 )   
       		  {
				 
                    $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                    $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                    $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                    $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                    oci_bind_by_name( $statement, ":serial", $serial, -1 );
                    oci_bind_by_name( $statement, ":categ", $category, -1 );
                    oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                    oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                    oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                    oci_execute( $statement );
                    $a_row = oci_num_rows( $statement );
                    oci_free_statement( $statement );
                    ++$pin_total;
                    $a_pin_total = $a_pin_total + $a_row;
                }
                else
                {
                    ++$inc_amount;
                }
            }
			
			else if ( strlen( $line ) == 52 )
            {
                sscanf( $line, "%6s;%13s;%14s;%12s;%1s",$amount,$temp1 ,$pin ,$serial ,$temp2 );// infotech format file added by ghanadan 
               if ( $amount == $CategoryTable[$category] ) 
                {
                    $part1 = mcrypt_ecb( MCRYPT_DES, $keyA, $pin, MCRYPT_ENCRYPT );
                    $part2 = mcrypt_ecb( MCRYPT_DES, $keyB, $part1, MCRYPT_DECRYPT );
                    $encrypted_data = mcrypt_ecb( MCRYPT_DES, $keyA, $part2, MCRYPT_ENCRYPT );
                    $statement = oci_parse( $conn, "insert into {$target_table} ( pinserial,category,chargepin,batchid,supplier) VALUES (:serial,:categ,:chrgpin,:batch,:suppl)" );
                    oci_bind_by_name( $statement, ":serial", $serial, -1 );
                    oci_bind_by_name( $statement, ":categ", $category, -1 );
                    oci_bind_by_name( $statement, ":chrgpin", bin2hex( $encrypted_data ), -1 );
                    oci_bind_by_name( $statement, ":batch", $Batch_ID, -1 );
                    oci_bind_by_name( $statement, ":suppl", $supplier, -1 );
                    oci_execute( $statement );
                    $a_row = oci_num_rows( $statement );
                    oci_free_statement( $statement );
                    ++$pin_total;
                    $a_pin_total = $a_pin_total + $a_row;
                }
                else
                {
                    ++$inc_amount;
                }
            }
            
            else if ( strlen( 3 < $line ) && substr( $line, 0, 3 ) == "PIN" )
            {
                echo "<p> First row is not Serial </p>";
                exit( );
            }
        }
        fclose( $handle );
    }
    else
    {
        echo "<p> system error </p>";
    }
    $result = run_my_query( $conn, "select count(1) from tblpins where batchid={$Batch_ID}" );
    if ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
    {
        $Batch_count = $row[0];
        oci_free_statement( $result );
        echo "<p>batch count {$Batch_count}</p>";
    }
    echo "<p>amount is incorrect {$inc_amount}</p>";
    if ( $a_pin_total == $pin_total )
    {
        if ( 0 < $pin_total )
        {
            run_my_query( $conn, "UPDATE tblbatch SET total={$a_pin_total} WHERE batchid={$Batch_ID}" );
            oci_commit( $conn );
        }
        else
        {
            echo "<p>rollback</p>";
            oci_rollback( $conn );
        }
    }
    else
    {
        oci_rollback( $conn );
    }
    echo "<p>".$a_pin_total." , ".$pin_total."</p>";
}
echo "\r\n<table width=\"100%\" border=\"0\">\r\n\t<tr>\r\n\t\t<td></td>\r\n\t</tr>\r\n\r\n<tr>\r\n<td>\r\n<table width=\"100%\" border=\"0\" style=\"TABLE-LAYOUT: auto; CURSOR: auto; BORDER-COLLAPSE: collapse\">\r\n\t<tr>\r\n\t\t<td>\r\n\t\t\t<form method=\"post\" enctype=\"multipart/form-data\" >\r\n\t\t\t  <label> Category\r\n";
$result = run_my_query( $conn, "SELECT NAME,CATEGORY FROM TBLCATEGORY where category<>'9355' and category<>'9356' and category<>'9358' and category<>'1002' and category<>'1001' and category<>'9198' ORDER BY ename DESC" );
echo "\t\t\t  ";
echo "<s";
echo "elect size=\"20\" name=\"Category\">\r\n\r\n";
$i = 0;
while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
{
    $sel = "";
    echo "<option value=\"".$row[1]."\" ".$sel." >".$row[0]."</option>\n";
    ++$i;
}
oci_free_statement( $result );
echo "\t\t\t  </select>\r\n\t\t\t  </label>\r\n\t\t\t  <label> شماره فاکتور\r\n\t\t\t  <input name=\"InvoiceNo\" type=\"text\" size=\"20\"/>\r\n\t\t\t  </label>\r\n\t\t\t  <label> نام دسته\r\n\t\t\t  <input name=\"BatchName\" type=\"text\" size=\"40\"/>\r\n\t\t\t  </label>\r\n\t\t\t  <label> فایل\r\n\t\t\t  <input name=\"Filename\" type=\"file\" size=\"40\"/>\r\n\t\t\t  </label>\r\n\t\t\t  <label> Supplier\r\n\t\t\t\t\t";
echo "<s";
echo "elect size=\"1\" id=\"GroupSupplier\" name=\"Supplier\" dir=\"rtl\">\r\n\t\t\t\t\t\t";
$result = run_my_query( $conn, "SELECT SUPPLIERID,SUPPLIERNAME FROM TBLSUPPLIERS {$where_supplier_id} ORDER BY 1" );
$i = 0;
$last_time = "";
while ( $row = oci_fetch_array( $result, OCI_RETURN_NULLS ) )
{
    $select = "";
    if ( $row[0] == $supplier )
    {
        $select = "selected";
    }
    echo "<option value=\"".$row[0]."\" {$select}>{$row['1']}</option>";
    ++$i;
}
if ( $supplier == "0" )
{
    echo "<option value=\"0\" selected></option>";
}
oci_free_statement( $result );
echo "\t\t\t\t\t\t\r\n\t\t\t\t\t</select>\t\t\t  </label>\r\n\t\t\t  <input name=\"Submit2\" type=\"submit\" id=\"Submit2\" value=\"Import\" />\r\n\t\t\t</form>\r\n\t\t</td>\r\n\t</tr>\r\n\t\r\n";
echo "</table>\r\n<table width=\"100%\" border=\"1\">\r\n  ";
oci_close( $conn );
echo "</table>\r\n\r\n</body>\r\n</html>\r\n";
?>
