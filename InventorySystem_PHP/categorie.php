<?php 
$page_title = 'All Categories';
require_once('includes/load.php');
page_require_level(1);

// Fetch all categories
$all_categories = find_all('categories');
$msg = $session->msg();

// Add new category
if (isset($_POST['add_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));
  if (empty($errors)) {
    $sql = "INSERT INTO categories (category_name) VALUES ('{$cat_name}')";
    if ($db->query($sql)) {
      $session->msg("s", "Successfully added new category");
      redirect('categorie.php', false);
    } else {
      $session->msg("d", "Failed to add category.");
      redirect('categorie.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('categorie.php', false);
  }
}
?>

<?php include_once('layouts/header.php'); ?>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
    
    <!-- ======= ADD NEW CATEGORY PANEL ======= -->
    <div class="panel panel-default" style="width: 500px; margin-bottom: 20px;">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-plus"></span> Add New Category</strong>
      </div>
      <div class="panel-body" style="padding: 20px;">
        <form method="post" action="categorie.php">
          <div class="form-group">
            <label for="categorie-name">Category Name</label>
            <input type="text" class="form-control" name="categorie-name" id="categorie-name" placeholder="Enter category name" required>
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary btn-block">
            <span class="glyphicon glyphicon-send"></span> Add Category
          </button>
        </form>
      </div>
    </div>

    <!-- ======= CATEGORY LIST TABLE ======= -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th-list"></span> All Categories</strong>
      </div>
      <div class="panel-body" style="overflow-x:auto;">
        <table class="table table-bordered table-striped table-hover" style="min-width: 400px;">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Category Name</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; foreach ($all_categories as $cat): ?>
              <tr>
                <td class="text-center"><?php echo $count++; ?></td>
                <td><?php echo remove_junk(ucfirst($cat['category_name'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_categorie.php?id=<?php echo (int)$cat['c_id']; ?>" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_categorie.php?id=<?php echo (int)$cat['c_id']; ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Delete">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- Professional Styling (Match order.php) -->
<style>
/* Panel Heading Gradient */
.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
  padding: 15px 20px;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  font-size: 18px;
}

/* Table Header Gradient */
.table thead tr {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.table thead th {
  color: white !important;
  border-bottom: 2px solid #dee2e6;
  vertical-align: middle;
  font-size: 13px;
  padding: 12px 8px;
}

/* Table Hover Effect */
.table-hover tbody tr:hover {
  background-color: #f1f8ff;
  cursor: pointer;
}

/* Button Styling with Gradients */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
  border: none;
}

.btn-info:hover {
  background: linear-gradient(135deg, #44a08d 0%, #4ecdc4 100%);
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  border: none;
}

.btn-danger:hover {
  background: linear-gradient(135deg, #ee5a52 0%, #ff6b6b 100%);
}

/* Form Control Focus */
.form-control:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Label Styling */
label {
  font-weight: 600;
  color: #2c3e50;
}

/* Panel Shadow */
.panel {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 8px;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb {
  background: #667eea;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #764ba2;
}
</style>

<?php include_once('layouts/footer.php'); ?>
