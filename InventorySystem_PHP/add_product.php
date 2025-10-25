<?php
$page_title = 'Add Product';
require_once('includes/load.php');
page_require_level(2);

// Initialize variables
$p_id = '';
$product_name = '';
$category_name = '';
$quantity = 0;
$buying_price = 0.00;
$selling_price = 0.00;
$expire_date = '';
$s_id = ''; // Supplier ID

// If redirected from purchase_order 'Add' button
if(isset($_GET['o_id'])){
    $o_id = (int)$_GET['o_id'];
    $order = find_by_id('purchase_order', $o_id);
    if($order){
        // Pre-fill form from purchase order
        $product_name   = $order['product_name'];
        $category_name  = $order['category_name'];
        $quantity       = $order['quantity'];
        $buying_price   = $order['price'];
        $s_id           = $order['s_id']; // load supplier ID from purchase_order
    } else {
        $session->msg('d','Purchase order not found.');
        redirect('purchase_orders.php');
    }
}

// Handle form submission
if(isset($_POST['add_product'])){
    $req_fields = ['p_id','product_name','category_name','quantity','buying_price','selling_price','s_id'];
    validate_fields($req_fields);

    if(empty($errors)){
        $p_id           = remove_junk($db->escape($_POST['p_id']));
        $product_name   = remove_junk($db->escape($_POST['product_name']));
        $category_name  = remove_junk($db->escape($_POST['category_name']));
        $quantity       = (int)$_POST['quantity'];
        $buying_price   = (float)$_POST['buying_price'];
        $selling_price  = (float)$_POST['selling_price'];
        $expire_date    = remove_junk($db->escape($_POST['expire_date']));
        $s_id           = remove_junk($db->escape($_POST['s_id']));

        // Insert product, recorded_date will be auto-set by database
        $sql = "INSERT INTO product (p_id, product_name, quantity, buying_price, selling_price, category_name, s_id, expire_date) VALUES (";
        $sql .= "'{$p_id}','{$product_name}',{$quantity},{$buying_price},{$selling_price},'{$category_name}','{$s_id}',";
        $sql .= $expire_date ? "'{$expire_date}'" : "NULL";
        $sql .= ")";
        
        if($db->query($sql)){
            // Optionally, update purchase_order status
            if(isset($o_id)){
                $db->query("UPDATE purchase_order SET status='Added' WHERE o_id={$o_id}");
            }
            $session->msg('s',"Product has been added successfully.");
            redirect('product.php');
        } else {
            $session->msg('d','Failed to add product.');
            redirect('add_product.php');
        }
    } else {
        $session->msg("d", $errors);
        redirect('add_product.php');
    }
}

// Generate a new product ID if adding manually
if(!$p_id){
    $p_id = 'p'.str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <?php echo display_msg($msg); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><span class="glyphicon glyphicon-plus"></span> Add Product</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="add_product.php<?php echo isset($o_id) ? '?o_id='.$o_id : ''; ?>">
                    <div class="form-group">
                        <label for="p_id">Product ID</label>
                        <input type="text" class="form-control" name="p_id" value="<?php echo $p_id; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" name="product_name" value="<?php echo $product_name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_name">Category</label>
                        <input type="text" class="form-control" name="category_name" value="<?php echo $category_name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" name="quantity" value="<?php echo $quantity; ?>" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="buying_price">Buying Price (Rs)</label>
                        <input type="number" step="0.01" class="form-control" name="buying_price" value="<?php echo $buying_price; ?>" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="selling_price">Selling Price (Rs)</label>
                        <input type="number" step="0.01" class="form-control" name="selling_price" value="<?php echo $selling_price; ?>" min="<?php echo $buying_price; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="s_id">Supplier ID</label>
                        <input type="text" class="form-control" name="s_id" value="<?php echo $s_id; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="expire_date">Expire Date</label>
                        <input type="date" class="form-control" name="expire_date" value="<?php echo $expire_date ? date('Y-m-d', strtotime($expire_date)) : ''; ?>">
                    </div>
                    <div class="form-group">
                         <label for="added_date">Add Date</label>
                         <input type="text" class="form-control" name="added_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>

                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    <a href="purchase_orders.php" class="btn btn-default">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>
