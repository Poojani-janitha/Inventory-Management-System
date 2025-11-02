<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
?>
<?php
  // Get category using correct column name for new database structure
  $categorie_id = (int)$_GET['id'];
  $sql = "SELECT * FROM categories WHERE c_id = '{$categorie_id}' LIMIT 1";
  $result = $db->query($sql);
  $categorie = $db->fetch_assoc($result);
  
  if(!$categorie){
    $session->msg("d","Missing Category id.");
    redirect('categorie.php');
    exit();
  }
?>
<?php
  // Delete category using correct column name
  $sql = "DELETE FROM categories WHERE c_id = '{$categorie_id}' LIMIT 1";
  $result = $db->query($sql);
  
  if($result && $db->affected_rows() === 1){
      $session->msg("s","Category deleted successfully.");
      redirect('categorie.php');
      exit();
  } else {
      $session->msg("d","Category deletion failed.");
      redirect('categorie.php');
      exit();
  }
?>
