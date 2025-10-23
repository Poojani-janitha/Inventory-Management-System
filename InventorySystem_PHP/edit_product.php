<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
?>
<?php
$product = find_by_id('product',$_GET['id']);
$all_categories = find_all('categories');
$all_photo = find_all('media');
$all_suppliers = find_all('supplier_info');
if(!$product){
  $session->msg("d","Missing product id.");
  redirect('product.php');
}
?>
<?php
 if(isset($_POST['product'])){
    $req_fields = array('product-title','product-categorie','product-supplier','product-quantity','buying-price', 'saleing-price' );
    validate_fields($req_fields);

   if(empty($errors)){
       $p_name  = remove_junk($db->escape($_POST['product-title']));
       $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
       $p_supplier = remove_junk($db->escape($_POST['product-supplier']));
       $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
       $p_buy   = remove_junk($db->escape($_POST['buying-price']));
       $p_sale  = remove_junk($db->escape($_POST['saleing-price']));
<<<<<<< HEAD
       $p_expire = remove_junk($db->escape($_POST['expire-date']));
      //  if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
      //    $media_id = '0';
      //  } else {
      //    $media_id = remove_junk($db->escape($_POST['product-photo']));
      //  }

       $query   = "UPDATE products SET";
       $query  .=" name ='{$p_name}', quantity ='{$p_qty}',";
       $query  .=" buy_price ='{$p_buy}', sale_price ='{$p_sale}', categorie_id ='{$p_cat}',expire_date ='{$p_expire}'";
       $query  .=" WHERE id ='{$product['id']}'";
=======
       if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
         $media_id = '1';
       } else {
         $media_id = remove_junk($db->escape($_POST['product-photo']));
       }
       $query   = "UPDATE product SET";
       $query  .=" product_name ='{$p_name}', quantity ='{$p_qty}',";
       $query  .=" buying_price ='{$p_buy}', selling_price ='{$p_sale}', category_name ='{$p_cat}', s_id='{$p_supplier}', media_id='{$media_id}'";
       $query  .=" WHERE p_id ='{$product['p_id']}'";
>>>>>>> d0d722e9e0d9be224cf00b85a3a8308ff9598136
       $result = $db->query($query);
               if($result && $db->affected_rows() === 1){
                 $session->msg('s',"Product updated ");
                 redirect('product.php', false);
               } else {
                 $session->msg('d',' Sorry failed to updated!');
                 redirect('edit_product.php?id='.$product['p_id'], false);
               }

   } else{
       $session->msg("d", $errors);
       redirect('edit_product.php?id='.$product['p_id'], false);
   }

 }

?>
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Update Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo $product['p_id'] ?>">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['product_name']);?>">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-4">
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a category</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo $cat['category_name']; ?>" <?php if($product['category_name'] === $cat['category_name']): echo "selected"; endif; ?> >
                       <?php echo remove_junk($cat['category_name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
<<<<<<< HEAD
                  <!-- <div class="col-md-6">
=======
                  <div class="col-md-4">
                    <select class="form-control" name="product-supplier">
                      <option value=""> Select a supplier</option>
                      <?php  foreach ($all_suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['s_id'];?>" <?php if($product['s_id'] === $supplier['s_id']): echo "selected"; endif; ?> >
                          <?php echo $supplier['s_name'] ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-4">
>>>>>>> d0d722e9e0d9be224cf00b85a3a8308ff9598136
                    <select class="form-control" name="product-photo">
                      <option value=""> No image</option>
                      <?php  foreach ($all_photo as $photo): ?>
                        <option value="<?php echo (int)$photo['id'];?>" <?php if($product['media_id'] === $photo['id']): echo "selected"; endif; ?> >
                          <?php echo $photo['file_name'] ?></option>
                      <?php endforeach; ?> 
                    </select>
                  </div> -->

                   <!-- Category & Expire Date -->
                   <div class="col-md-6">
                     <select class="form-control" name="product-categorie">
                       <option value="">Select a category</option>
                       <?php foreach ($all_categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php if ($product['categorie_id'] === $cat['id']) echo "selected"; ?>>
                          <?php echo remove_junk($cat['name']); ?>
                        </option>
                      <?php endforeach; ?>
                     </select>
                   </div>

                   <div class="col-md-6">
                     <label for="expire-date">Expire Date</label>
                     <div class="input-group">
                       <span class="input-group-addon">
                         <i class="glyphicon glyphicon-calendar"></i>
                       </span>
                       <input type="date" class="form-control" name="expire-date" value="<?php echo remove_junk($product['expire_date']); ?>">
                     </div>
                   </div>
            </div>
          </div>

                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Quantity</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                       <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                   </div>
                  </div>
                 </div>
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Buying price</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                      </span>
                      <input type="number" class="form-control" name="buying-price" value="<?php echo remove_junk($product['buying_price']);?>">
                      <span class="input-group-addon">.00</span>
                   </div>
                  </div>
                 </div>
                  <div class="col-md-4">
                   <div class="form-group">
                     <label for="qty">Selling price</label>
                     <div class="input-group">
                       <span class="input-group-addon">
                         <i class="glyphicon glyphicon-usd"></i>
                       </span>
                       <input type="number" class="form-control" name="saleing-price" value="<?php echo remove_junk($product['selling_price']);?>">
                       <span class="input-group-addon">.00</span>
                    </div>
                   </div>
                  </div>
               </div>
              </div>
              <button type="submit" name="product" class="btn btn-danger">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
