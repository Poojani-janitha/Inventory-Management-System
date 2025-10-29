     </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
  <?php 
    // Handle JS path for subdirectories
    $current_path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
    if(empty($current_path) && isset($_SERVER['REQUEST_URI'])) {
      $current_path = $_SERVER['REQUEST_URI'];
    }
    $is_reports = (strpos($current_path, '/reports/') !== false);
    $js_path = $is_reports ? '../libs/js/functions.js' : 'libs/js/functions.js';
  ?>
  <script type="text/javascript" src="<?php echo $js_path; ?>"></script>
  </body>
</html>

<?php if(isset($db)) { $db->db_disconnect(); } ?>
