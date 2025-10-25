<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
?>
<?php
  // Get invoice number from URL
  $invoice_number = isset($_GET['invoice_number']) ? $db->escape($_GET['invoice_number']) : '';
  
  if(empty($invoice_number)){
    $session->msg("d","Missing invoice number.");
    redirect('invoice_list.php');
  }
?>
<?php
  // Delete all sales records with this invoice number
  $delete_sql = "DELETE FROM sales WHERE invoice_number = '{$invoice_number}'";
  $delete_result = $db->query($delete_sql);
  
  if($delete_result){
      $session->msg("s","Invoice {$invoice_number} deleted successfully.");
      redirect('invoice_list.php');
  } else {
      $session->msg("d","Invoice deletion failed.");
      redirect('invoice_list.php');
  }
?>