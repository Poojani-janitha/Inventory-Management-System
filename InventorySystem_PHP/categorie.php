<?php 
$page_title = 'All Categories';
require_once('includes/load.php');
page_require_level(1);

// Fetch all categories
$all_categories = find_all('categories');
$msg = $session->msg();

// Handle edit category form submission
if (isset($_POST['edit_cat'])) {
  $req_field = array('categorie-name');
  validate_fields($req_field);
  $cat_name = remove_junk($db->escape($_POST['categorie-name']));
  $cat_id = (int)$_POST['category_id'];
  
  if (empty($errors)) {
    $sql = "UPDATE categories SET category_name='{$cat_name}' WHERE c_id='{$cat_id}'";
    if ($db->query($sql) && $db->affected_rows() === 1) {
      $session->msg("s", "Category updated successfully!");
      redirect('categorie.php', false);
      exit();
    } else {
      $session->msg("d", "Failed to update category.");
      redirect('categorie.php', false);
      exit();
    }
  } else {
    $session->msg("d", $errors);
    redirect('categorie.php', false);
    exit();
  }
}

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

<!-- Force full width layout with inline styles -->
<style>
/* Override any external CSS that might be constraining the layout */
.container-fluid {
  width: 100% !important;
  max-width: none !important;
}

.row {
  margin-left: -15px !important;
  margin-right: -15px !important;
}

.col-md-4, .col-md-8 {
  padding-left: 15px !important;
  padding-right: 15px !important;
  float: left !important;
}

.col-md-4 {
  width: 33.33333333% !important;
}

.col-md-8 {
  width: 66.66666667% !important;
}

/* Panel styling */
.panel {
  width: 100% !important;
  margin-bottom: 20px !important;
  background-color: #fff !important;
  border: 1px solid #ddd !important;
  border-radius: 4px !important;
  box-shadow: 0 1px 1px rgba(0,0,0,.05) !important;
}

.panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  padding: 10px 15px !important;
  border-bottom: 1px solid #ddd !important;
  border-top-left-radius: 3px !important;
  border-top-right-radius: 3px !important;
}

.panel-body {
  padding: 15px !important;
}

/* Table styling */
.table {
  width: 100% !important;
  max-width: 100% !important;
  margin-bottom: 0 !important;
  background-color: transparent !important;
}

.table > thead > tr > th {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  vertical-align: bottom !important;
  border-bottom: 2px solid #ddd !important;
  padding: 8px !important;
}

.table > tbody > tr > td {
  padding: 8px !important;
  line-height: 1.42857143 !important;
  vertical-align: top !important;
  border-top: 1px solid #ddd !important;
}

.table-striped > tbody > tr:nth-of-type(odd) {
  background-color: #f9f9f9 !important;
}

.table-hover > tbody > tr:hover {
  background-color: #f5f5f5 !important;
}

/* Button styling */
.btn {
  display: inline-block !important;
  padding: 6px 12px !important;
  margin-bottom: 0 !important;
  font-size: 14px !important;
  font-weight: normal !important;
  line-height: 1.42857143 !important;
  text-align: center !important;
  white-space: nowrap !important;
  vertical-align: middle !important;
  cursor: pointer !important;
  border: 1px solid transparent !important;
  border-radius: 4px !important;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border-color: #667eea !important;
  color: #fff !important;
}

.btn-info {
  background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%) !important;
  border-color: #4ecdc4 !important;
  color: #fff !important;
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%) !important;
  border-color: #ff6b6b !important;
  color: #fff !important;
}

.btn-sm {
  padding: 5px 10px !important;
  font-size: 12px !important;
  line-height: 1.5 !important;
  border-radius: 3px !important;
}

/* Form styling */
.form-control {
  display: block !important;
  width: 100% !important;
  height: 34px !important;
  padding: 6px 12px !important;
  font-size: 14px !important;
  line-height: 1.42857143 !important;
  color: #555 !important;
  background-color: #fff !important;
  border: 1px solid #ccc !important;
  border-radius: 4px !important;
}

.form-group {
  margin-bottom: 15px !important;
}

/* Clear floats */
.clearfix:before,
.clearfix:after {
  display: table !important;
  content: " " !important;
}

.clearfix:after {
  clear: both !important;
}
</style>

<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>

<div class="row clearfix">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-plus"></span>
          <span>Add New Category</span>
        </strong>
      </div>
      <div class="panel-body">
        <form method="post" action="categorie.php">
          <div class="form-group">
            <input type="text" class="form-control" name="categorie-name" placeholder="Category Name">
          </div>
          <button type="submit" name="add_cat" class="btn btn-primary">Add Category</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong><span class="glyphicon glyphicon-th-list"></span> All Categories</strong>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 80px;">#</th>
              <th>Category Name</th>
              <th class="text-center" style="width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $count = 1; foreach ($all_categories as $cat): ?>
              <tr>
                <td class="text-center"><?php echo $count++; ?></td>
                <td><?php echo remove_junk(ucfirst($cat['category_name'])); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit" 
                            onclick="showEditModal(<?php echo (int)$cat['c_id']; ?>, '<?php echo addslashes($cat['category_name']); ?>')">
                      <span class="glyphicon glyphicon-edit"></span>
                    </button>
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
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="editCategoryModalLabel">
          <i class="glyphicon glyphicon-edit"></i> Edit Category
        </h4>
      </div>
      <div class="modal-body">
        <form method="post" action="categorie.php" id="editCategoryForm">
          <input type="hidden" name="category_id" id="edit_category_id">
          
          <div class="form-group">
            <label for="edit_category_name">Category Name</label>
            <input type="text" class="form-control" name="categorie-name" id="edit_category_name" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> -->
        <button type="submit" form="editCategoryForm" name="edit_cat" class="btn btn-primary">
          <i class="glyphicon glyphicon-floppy-disk"></i> Update Category
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Professional Styling with Layout Fixes -->
<style>
/* Force proper column layout */
.col-md-4, .col-md-8 {
  padding-left: 15px !important;
  padding-right: 15px !important;
}

/* Ensure table panel takes full width of its container */
.col-md-8 .panel {
  width: 100% !important;
  margin: 0 !important;
}

/* Force table to expand to full container width */
.col-md-8 .panel-body {
  padding: 0 !important;
}

.col-md-8 .table {
  width: 100% !important;
  margin: 0 !important;
  table-layout: fixed !important;
}

/* Adjust column widths for better space utilization */
.col-md-8 .table th:first-child,
.col-md-8 .table td:first-child {
  width: 80px !important;
  text-align: center !important;
}

.col-md-8 .table th:nth-child(2),
.col-md-8 .table td:nth-child(2) {
  width: auto !important;
  padding-left: 20px !important;
}

.col-md-8 .table th:last-child,
.col-md-8 .table td:last-child {
  width: 180px !important;
  text-align: center !important;
}

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
  padding: 12px 15px !important;
}

.table tbody td {
  padding: 12px 15px !important;
  vertical-align: middle;
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

/* Remove any conflicting overflow settings */
.panel-body {
  overflow: visible !important;
}

/* Modal Styling */
.modal-content {
  border-radius: 8px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  border: none;
}

.modal-header {
  border-radius: 8px 8px 0 0;
  border-bottom: none;
  padding: 20px;
}

.modal-title {
  font-size: 18px;
  font-weight: 600;
}

.modal-body {
  padding: 25px;
}

.modal-footer {
  border-top: 1px solid #e5e5e5;
  padding: 15px 25px;
  background-color: #f8f9fa;
  border-radius: 0 0 8px 8px;
}

.modal-footer .btn {
  min-width: 100px;
  font-weight: 600;
}

.modal-footer .btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.modal-footer .btn-primary:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  transform: translateY(-1px);
}

/* Debug: Add border to see column boundaries */
.col-md-4 {
  border-right: 1px solid #eee;
}

/* Responsive adjustments */
@media (max-width: 991px) {
  .col-md-4, .col-md-8 {
    width: 100% !important;
    margin-bottom: 20px;
  }
  
  .col-md-4 {
    border-right: none;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
  }
}
</style>

<script>
function showEditModal(categoryId, categoryName) {
  // Populate the form fields
  document.getElementById('edit_category_id').value = categoryId;
  document.getElementById('edit_category_name').value = categoryName;
  
  // Update the modal title
  document.getElementById('editCategoryModalLabel').innerHTML = '<i class="glyphicon glyphicon-edit"></i> Edit Category: ' + categoryName;
  
  // Show the modal
  $('#editCategoryModal').modal('show');
}

// Initialize tooltips
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<?php include_once('layouts/footer.php'); ?>
