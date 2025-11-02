<?php
$page_title='Add Product';
require_once('includes/load.php');
page_require_level(2);

$msg = $session->msg();

$p_id='';
$product_name='';
$category_name='';
$quantity=0;
$buying_price=0;
$selling_price=0;
$expire_date='';
$s_id='';
$o_id=null;
$existing_product=false;

// Load details if called with ?o_id=
if(isset($_GET['o_id'])){
  $o_id=(int)$_GET['o_id'];
  $sql="SELECT * FROM purchase_order WHERE o_id={$o_id} LIMIT 1";
  $result=$db->query($sql);
  if($result && $db->num_rows($result)>0){
    $order=$db->fetch_assoc($result);
    $product_name=$order['product_name'];
    $category_name=$order['category_name'];
    $quantity=$order['quantity'];
    $buying_price=$order['price'];
    $s_id=$order['s_id'];

    // Check if product already exists in product table
    $check_sql="SELECT * FROM product WHERE product_name='{$product_name}' LIMIT 1";
    $check_result=$db->query($check_sql);
    if($check_result && $db->num_rows($check_result)>0){
      $existing=$db->fetch_assoc($check_result);
      $p_id=$existing['p_id'];
      $selling_price=$existing['selling_price'];
      $expire_date=$existing['expire_date'];
      $existing_product=true;
      $session->msg('i','This product already exists. You can update quantity and other details.');
    }
  }else{
    $session->msg('d','Purchase order not found.');
    redirect('purchase_orders.php');
  }
}

// Handle form submit
if(isset($_POST['add_product'])){
  $req_fields=['p_id','product_name','category_name','quantity','buying_price','selling_price','s_id'];
  validate_fields($req_fields);

  if(empty($errors)){
    $p_id=remove_junk($db->escape($_POST['p_id']));
    $product_name=remove_junk($db->escape($_POST['product_name']));
    $category_name=remove_junk($db->escape($_POST['category_name']));
    $quantity=(int)$_POST['quantity'];
    $buying_price=(float)$_POST['buying_price'];
    $selling_price=(float)$_POST['selling_price'];
    $expire_date=remove_junk($db->escape($_POST['expire_date']));
    $s_id=remove_junk($db->escape($_POST['s_id']));
    $is_existing=isset($_POST['existing_product']) && $_POST['existing_product']=='1';

    if($selling_price < $buying_price){
      $session->msg('d','Selling price must be greater than or equal to buying price.');
      redirect('add_product.php'.(isset($_POST['o_id'])?'?o_id='.$_POST['o_id']:''));
      exit;
    }

    if($is_existing){
      $sql="UPDATE product SET 
            quantity=quantity+{$quantity},
            buying_price={$buying_price},
            selling_price={$selling_price},
            s_id='{$s_id}'".
            ($expire_date?",expire_date='{$expire_date}'":"").
            " WHERE p_id='{$p_id}'";
      $success_msg='Product stock updated successfully.';
    }else{
      $sql="INSERT INTO product (p_id,product_name,quantity,buying_price,selling_price,category_name,s_id,expire_date)
            VALUES ('{$p_id}','{$product_name}',{$quantity},{$buying_price},{$selling_price},
                    '{$category_name}','{$s_id}',".($expire_date?"'{$expire_date}'":"NULL").")";
      $success_msg='Product added successfully.';
    }

    if($db->query($sql)){
      if(isset($_POST['o_id']) && !empty($_POST['o_id'])){
        $update_o_id=(int)$_POST['o_id'];
        $db->query("UPDATE purchase_order SET status='Added' WHERE o_id={$update_o_id}");
      }
      $session->msg('s',$success_msg);
      redirect('product.php');
    }else{
      $session->msg('d','Failed to process product. '.$db->error());
      redirect('add_product.php'.(isset($_POST['o_id'])?'?o_id='.$_POST['o_id']:''));
    }
  }else{
    $session->msg('d',$errors);
    redirect('add_product.php'.(isset($_POST['o_id'])?'?o_id='.$_POST['o_id']:''));
  }
}

// Generate next product ID if not existing product
if(!$p_id){
  $result=$db->query("SELECT p_id FROM product ORDER BY p_id DESC LIMIT 1");
  if($result && $db->num_rows($result)>0){
    $row=$db->fetch_assoc($result);
    $last_id=$row['p_id'];
    $num=(int)substr($last_id,1);
    $num++;
    $p_id='p'.str_pad($num,3,'0',STR_PAD_LEFT);
  }else{
    $p_id='p001';
  }
}

include_once('layouts/header.php');
?>
<link rel="stylesheet" href="libs/css/add_product.css">

<style>
/* ===== Enhanced Creative Interface ===== */
body {
  background: #f4f7fa;
  font-family: 'Poppins', sans-serif;
}

.panel {
  border-radius: 15px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  border: none;
  background: #ffffff;
  margin-top: 30px;
}

.panel-heading {
  background: linear-gradient(90deg, #007bff, #00c6ff);
  color: white;
  padding: 18px 25px;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  font-size: 18px;
  font-weight: 600;
  text-align: center;
}

.panel-body {
  padding: 35px;
}

.form-group {
  margin-bottom: 20px;
}

.form-control {
  border-radius: 8px;
  border: 1px solid #d0d7de;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  padding: 10px 20px;
}

.btn-primary {
  background: linear-gradient(90deg, #007bff, #00c6ff);
  border: none;
  color: #fff;
  transition: 0.3s ease;
}

.btn-primary:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, #0069d9, #00a4e0);
}

.btn-cancel {
  background: #f44336;
  color: #fff;
  border: none;
  transition: all 0.3s ease;
}

.btn-cancel:hover {
  background: #d32f2f;
  transform: scale(1.05);
}

.text-muted {
  font-size: 12px;
  color: #6c757d !important;
}

.alert-info {
  background: #e8f4fd;
  color: #0d47a1;
  border: none;
  border-left: 5px solid #2196f3;
  border-radius: 8px;
  padding: 10px 15px;
}

.text-center {
  text-align: center;
}

.btn-space {
  margin-right: 15px;
}
</style>

<div class="row">
  <div class="col-md-12"><?php echo display_msg($msg);?></div>
</div>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <div class="panel">
      <div class="panel-heading">
        <span class="glyphicon glyphicon-plus"></span>
        <?php if($existing_product): ?>
          Update Existing Product Stock
        <?php elseif(isset($o_id)): ?>
          Add Product from Purchase Order
        <?php else: ?>
          Add New Product
        <?php endif; ?>
      </div>

      <div class="panel-body">
        <?php if($existing_product): ?>
          <div class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign"></span>
            This product already exists. Updating will add to the current stock.
          </div>
        <?php endif; ?>

        <form method="post" action="add_product.php<?php echo isset($o_id)?'?o_id='.$o_id:'';?>">
          <?php if(isset($o_id)):?>
            <input type="hidden" name="o_id" value="<?php echo $o_id;?>">
          <?php endif;?>
          <?php if($existing_product):?>
            <input type="hidden" name="existing_product" value="1">
          <?php endif;?>

          <div class="form-group">
            <label>Product ID</label>
            <input type="text" class="form-control" name="p_id" value="<?php echo $p_id;?>" readonly>
            <small class="text-muted"><?php echo $existing_product?'Existing Product':'Auto-generated';?></small>
          </div>

          <div class="form-group">
            <label>Product Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="product_name" value="<?php echo $product_name;?>" readonly required>
          </div>

          <div class="form-group">
            <label>Category <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="category_name" value="<?php echo $category_name;?>" readonly required>
          </div>

          <div class="form-group">
            <label>Quantity to Add <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="quantity" value="<?php echo $quantity>0?$quantity:'';?>" min="1" required>
            <small class="text-muted"><?php echo $existing_product?'This quantity will be added to current stock':'Enter quantity';?></small>
          </div>

          <div class="form-group">
            <label>Buying Price (Rs) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="buying_price" value="<?php echo $buying_price>0?$buying_price:'';?>" min="0" <?php echo isset($o_id)?'readonly':'';?> required>
          </div>

          <div class="form-group">
            <label>Selling Price (Rs) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="selling_price" value="<?php echo $selling_price>0?$selling_price:'';?>" min="0" required>
            <small class="text-muted">Must be greater than or equal to buying price</small>
          </div>

          <div class="form-group">
            <label>Supplier ID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="s_id" value="<?php echo $s_id;?>" <?php echo isset($o_id)?'readonly':'required';?>>
          </div>

          <div class="form-group">
            <label>Expire Date</label>
            <input type="date" class="form-control" name="expire_date" value="<?php echo $expire_date?date('Y-m-d',strtotime($expire_date)):'';?>">
            <small class="text-muted">Optional - Leave blank if not applicable</small>
          </div>

          <div class="form-group">
            <label><?php echo $existing_product?'Update Date':'Added Date';?></label>
            <input type="text" class="form-control" value="<?php echo date('Y-m-d');?>" readonly>
          </div>

          <div class="text-center mt-4">
            <button type="submit" name="add_product" class="btn btn-primary btn-space">
              <span class="glyphicon glyphicon-save"></span> 
              <?php echo $existing_product?'Update Stock':'Add Product';?>
            </button>
            <a href="<?php echo isset($o_id)?'purchase_orders_Accepted.php':'product.php';?>" class="btn btn-cancel">
              <span class="glyphicon glyphicon-remove"></span> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once('layouts/footer.php');?>
