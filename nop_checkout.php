<!DOCTYPE html>
<html>
<head>
   <title></title>
</head>
<body>
<?php

$admin_email = 'orders@yourwebsite.com';

$admin_subject = 'New Online Order';

$customer_subject = 'Order Confirmation';

$success_url = 'shop_success.html';

$error_url = 'shop_error.html';

$csv_file = './orders.csv';



if ($_SERVER['REQUEST_METHOD'] == 'POST')

{

   $billing_first = isset($_POST['billing_first']) ? $_POST['billing_first'] : '';

   $billing_last = isset($_POST['billing_last']) ? $_POST['billing_last'] : '';

   $billing_address = isset($_POST['billing_address']) ? $_POST['billing_address'] : '';

   $billing_address2 = isset($_POST['billing_address2']) ? $_POST['billing_address2'] : '';

   $billing_city = isset($_POST['billing_city']) ? $_POST['billing_city'] : '';

   $billing_state = isset($_POST['billing_state']) ? $_POST['billing_state'] : '';

   $billing_zip = isset($_POST['billing_zip']) ? $_POST['billing_zip'] : '';

   $billing_phone = isset($_POST['billing_phone']) ? $_POST['billing_phone'] : '';

   $billing_fax = isset($_POST['billing_fax']) ? $_POST['billing_fax'] : '';

   $billing_email = isset($_POST['billing_email']) ? $_POST['billing_email'] : '';

   $shipping_first = isset($_POST['shipping_first']) ? $_POST['shipping_first'] : '';

   $shipping_last = isset($_POST['shipping_last']) ? $_POST['shipping_last'] : '';

   $shipping_address = isset($_POST['shipping_address']) ? $_POST['shipping_address'] : '';

   $shipping_address2 = isset($_POST['shipping_address2']) ? $_POST['shipping_address2'] : '';

   $shipping_city = isset($_POST['shipping_city']) ? $_POST['shipping_city'] : '';

   $shipping_state = isset($_POST['shipping_state']) ? $_POST['shipping_state'] : '';

   $shipping_zip = isset($_POST['shipping_zip']) ? $_POST['shipping_zip'] : '';

   $shipping_phone = isset($_POST['shipping_phone']) ? $_POST['shipping_phone'] : '';

   $shipping_fax = isset($_POST['shipping_fax']) ? $_POST['shipping_fax'] : '';

   $SUBTOTAL = isset($_POST['SUBTOTAL']) ? $_POST['SUBTOTAL'] : '';

   $TOTAL = isset($_POST['TOTAL']) ? $_POST['TOTAL'] : '';

   $TAX = isset($_POST['TAX']) ? $_POST['TAX'] : '';

   $SHIPPING = isset($_POST['SHIPPING']) ? $_POST['SHIPPING'] : '';

   $comments = isset($_POST['comments']) ? $_POST['comments'] : '';;

   if (($billing_first == '') || ($billing_last == '') || ($billing_address == '') || ($billing_city == '') || ($billing_state == '') || ($billing_zip == '') || ($billing_phone == '') || ($billing_email == ''))

   {

      // invalid input

      header('Location: '. $error_url);

      exit;

   }

   if (!preg_match('/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i', $billing_email))

   {

      // invalid email address

      header('Location: '. $error_url);

      exit;

   }

   $today = date("l, F jS Y");

   $message = "";

   $message .= "A new order has been received.  A summary of this order appears below.\n";

   $message .= "\n";

   $message .= "Order Date: $today \n";

   $message .= " \n";

   $message .= "Bill To: \n";

   $message .= "-------- \n";

   $message .= "   $billing_first $billing_last \n";

   $message .= "   $billing_address \n";

   $message .= "   $billing_address2 \n";

   $message .= "   $billing_city, $billing_state  $billing_zip \n";

   $message .= "   $billing_phone \n";

   $message .= "   $billing_fax \n";

   $message .= "   $billing_email \n";

   $message .= " \n";

   $message .= " \n";

   if ($shipping_first != '')

   {

      $message .= "Ship To: \n";

      $message .= "-------- \n";

      $message .= "   $shipping_first $shipping_last \n";

      $message .= "   $shipping_addr \n";

      $message .= "   $shipping_addr2 \n";

      $message .= "   $shipping_city, $shipping_state  $shipping_zip \n";

      $message .= "   $shipping_phone \n";

      $message .= " \n";

      $message .= " \n";

   }

   $message .= "Qty  Price(\$)   Product ID  - Product Name\n";

   $message .= "===================================================================== \n";

   for ($i = 1; $i< 25; $i++)

   {

      if (isset($_POST['NAME_' . $i]))

      {

         $name = $_POST['NAME_' . $i];

         $quantity = $_POST['QUANTITY_' . $i];

         $price = $_POST['PRICE_' . $i];

         $id = $_POST['ID_' . $i];

         $addtlinfo = $_POST['ADDTLINFO_' . $i];

         $message .= "$quantity    \$$price    $id - $name   $addtlinfo  \n";

      }

   }

   $message .= "===================================================================== \n";

   $message .= "SUBTOTAL: $SUBTOTAL \n";

   $message .= "TOTAL: $TOTAL \n";

   $message .= "TAX: $TAX \n";

   $message .= "\n";

   $message .= "FREIGHT: $SHIPPING \n";

   $message .= "\n\n";

   $message .= "Comments: \n";

   $message .= "--------- \n";

   $message .= "$comments \n";

   $message .= " \n";



   // write to log

   foreach ($_POST as $key => $value)

   {

      $logdata .= ',';

      $logdata .= str_replace(",", " ", $value);

   }

   $logdata = str_replace("\r", "", $logdata);

   $logdata = str_replace("\n", " ", $logdata);

   $logdata .= "\r\n";

   $handle = fopen($csv_file, 'a') or die("can't open csv file");

   fwrite($handle, date("Y-m-d H:i:s,"));

   fwrite($handle, $_SERVER['REMOTE_ADDR']);

   fwrite($handle, $logdata);

   fclose($handle);



   // send message to admin

   $header  = "From: $billing_email\r\n";

   $header .= "Reply-To: $billing_email\r\n";

   $header .= "MIME-Version: 1.0\r\n";

   $header .= "X-Mailer: PHP Mail generated by: NOP Shopping Cart\r\n";

   mail($admin_email, $admin_subject, $message, $header);



   // send message to customer

   $header  = "From: $admin_email\r\n";

   $header .= "Reply-To: $admin_email\r\n";

   $header .= "MIME-Version: 1.0\r\n";

   $header .= "X-Mailer: PHP Mail generated by: NOP Shopping Cart\r\n";

   mail($billing_email, $customer_subject, $message, $header);



   header('Location: '.$success_url);

   exit;

   }

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

<title>Check out</title>

<style type="text/css">

body

{

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

   text-align: center;

}

div#container

{

   margin-left: auto;

   margin-right: auto;

   width: 50em;

   text-align: left;

}

}

input

{

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

}

.title

{

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #006600;

}

.blacktext

{

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

}

.nobr

{

   white-space: nowrap;

}

.nopcart

{

   background: #464444;

   border: 1px;

   font-family: Verdana, Arial, Helvetica, sans-serif; 

   font-size: 12px;

   color: #ffffff;

}

.nopheader

{

   background: #464444;

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #FFFFFF;

}

.nopentry

{

   background: #FFFFFF;

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

}

.noptotal

{

   background: #FFFFFF;

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

}

.nopbutton

{

   background: #FFFFFF;

   font-family: Verdana, Arial, Helvetica, sans-serif;

   font-size: 12px;

   color: #000000;

}

</style>

<script type="text/javascript" src="./language-en.js"></script>

<script type="text/javascript" src="./nopcart.js"></script>

<script type="text/javascript">

function CheckForm(theform)

{

   var bMissingFields = false;

   var strFields = "";

   if (theform.billing_first.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: First Name\n";

   }

   if (theform.billing_last.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: Last Name\n";

   }

   if (theform.billing_address.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: Address\n";

   }

   if (theform.billing_city.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: City\n";

   }

   if (theform.billing_state.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: State\n";

   }

   if (theform.billing_zip.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: Zipcode\n";

   }

   if (theform.billing_phone.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: Phone\n";

   }

   if (theform.billing_email.value == '')

   {

      bMissingFields = true;

      strFields += "     Billing: Email\n";

   }

   if (bMissingFields)

   {

      alert("We're sorry, but you must provide the following field(s) before continuing:\n" + strFields);

      return false;

   }

   return true;

}

</script>

</head>

<body>

<div id="container">

<blockquote>

<form method="post" action="<?php echo basename(__FILE__); ?>" onsubmit="return CheckForm(this)">

<span class="nobr">

<script type="text/javascript">

   CheckoutCart();

</script>

</span>

<br>

<p><span class="blacktext"><b>Please fill out the following information below to complete your order.</b></span></p>

<p><span class="title"><b>Billing Information:</b></span></p>

<table class="blacktext">

<tr><td>Name:    </td><td><input type="text" size="18" name="billing_first"> <input type="text" size="15" name="billing_last"></td></tr>

<tr><td>Address: </td><td><input type="text" size="37" name="billing_address"></td></tr>

<tr><td>Address: </td><td><input type="text" size="37" name="billing_address2"></td></tr>

<tr><td>City:    </td><td><input type="text" size="21" name="billing_city"></td></tr>

<tr><td>State:   </td><td><input type="text" size="1"  name="billing_state"></td></tr>

<tr><td>Zip:     </td><td><input type="text" size="5"  name="billing_zip"></td></tr>

<tr><td>Phone:   </td><td><input type="text" size="37" name="billing_phone"></td></tr>

<tr><td>Fax:     </td><td><input type="text" size="37" name="billing_fax"></td></tr>

<tr><td>Email:   </td><td><input type="text" size="37" name="billing_email"></td></tr>

</table>

<p><span class="title"><b>Shipping Information (if different than billing):</b></span></p>

<table class="blacktext">

<tr><td>Name:    </td><td><input type="text" size="18" name="shipping_first"> <input type="text" size="15" name="shipping_last"></td></tr>

<tr><td>Address: </td><td><input type="text" size="37" name="shipping_address"></td></tr>

<tr><td>Address: </td><td><input type="text" size="37" name="shipping_address2"></td></tr>

<tr><td>City:    </td><td><input type="text" size="21" name="shipping_city"></td></tr>

<tr><td>State:   </td><td><input type="text" size="1"  name="shipping_state"></td></tr>

<tr><td>Zip:     </td><td><input type="text" size="5"  name="shipping_zip"></td></tr>

<tr><td>Phone:   </td><td><input type="text" size="37" name="shipping_phone"></td></tr>

<tr><td>Fax:     </td><td><input type="text" size="37" name="shipping_fax"></td></tr>

<tr><td>Email:   </td><td><input type="text" size="37" name="shipping_email"></td></tr>

</table>

<p><span class="title"><b>Comments/Special Instructions:</b></span></p>

<span class="blacktext">

<textarea name="comments" rows="6" cols="40">

</textarea>

</span>

<p><input type="submit" value="submit order"> <input type="reset" value="Reset"></p>

</form>

</blockquote>

</div>

</body>

</html>


</body>
</html>