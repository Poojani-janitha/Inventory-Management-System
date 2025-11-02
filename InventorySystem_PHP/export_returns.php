<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
    page_require_level(1);
   
   $format = isset($_GET['format']) ? $_GET['format'] : 'csv';
   $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
   $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
   
   // Get returns data
   $sql = "SELECT r.*, p.name as product_name, u.name as user_name, c.name as category_name
           FROM returns r 
           LEFT JOIN products p ON r.product_id = p.id 
           LEFT JOIN users u ON r.processed_by = u.id
           LEFT JOIN categories c ON p.categorie_id = c.id
           WHERE r.return_date BETWEEN '{$date_from}' AND '{$date_to}'
           ORDER BY r.return_date DESC";
   
   $returns = $db->while_loop($db->query($sql));
   
   if($format == 'csv') {
     // CSV Export
     header('Content-Type: text/csv');
     header('Content-Disposition: attachment; filename="returns_' . date('Y-m-d') . '.csv"');
     
     $output = fopen('php://output', 'w');
     
     // CSV Headers
     fputcsv($output, [
       'ID', 'Product Name', 'Category', 'Quantity', 'Return Reason', 
       'Status', 'Refund Amount', 'Return Date', 'Processed By', 'Notes'
     ]);
     
     // CSV Data
     foreach($returns as $return) {
       fputcsv($output, [
         $return['id'],
         $return['product_name'],
         $return['category_name'],
         $return['quantity'],
         $return['return_reason'],
         $return['status'],
         $return['refund_amount'],
         $return['return_date'],
         $return['user_name'],
         $return['notes']
       ]);
     }
     
     fclose($output);
     exit;
     
   } elseif($format == 'excel') {
     // Excel Export (using simple HTML table)
     header('Content-Type: application/vnd.ms-excel');
     header('Content-Disposition: attachment; filename="returns_' . date('Y-m-d') . '.xls"');
     ?>
     <!DOCTYPE html>
     <html>
     <head>
       <meta charset="utf-8">
       <title>Returns Report</title>
     </head>
     <body>
       <table border="1">
         <tr>
           <th>ID</th>
           <th>Product Name</th>
           <th>Category</th>
           <th>Quantity</th>
           <th>Return Reason</th>
           <th>Status</th>
           <th>Refund Amount</th>
           <th>Return Date</th>
           <th>Processed By</th>
           <th>Notes</th>
         </tr>
         <?php foreach($returns as $return): ?>
         <tr>
           <td><?php echo $return['id']; ?></td>
           <td><?php echo $return['product_name']; ?></td>
           <td><?php echo $return['category_name']; ?></td>
           <td><?php echo $return['quantity']; ?></td>
           <td><?php echo $return['return_reason']; ?></td>
           <td><?php echo $return['status']; ?></td>
           <td><?php echo $return['refund_amount']; ?></td>
           <td><?php echo $return['return_date']; ?></td>
           <td><?php echo $return['user_name']; ?></td>
           <td><?php echo $return['notes']; ?></td>
         </tr>
         <?php endforeach; ?>
       </table>
     </body>
     </html>
     <?php
     exit;
     
   } elseif($format == 'pdf') {
     // PDF Export (requires TCPDF or similar library)
     // For now, redirect to a simple HTML version
     header('Location: return_reports.php?export=1');
     exit;
   }
?>
