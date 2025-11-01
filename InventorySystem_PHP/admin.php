<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);
?>
<?php
// Updated for new database structure
$c_categorie     = count_by_id('categories');
$c_product       = count_by_id('product');
$c_sale          = count_by_id('sales');
$c_user          = count_by_id('users');
$c_supplier      = count_by_id('supplier_info');
$c_purchase_order = count_by_id('purchase_order');

// Get recent products from new structure
$recent_products_query = "SELECT p.p_id, p.product_name, p.selling_price, p.category_name, p.recorded_date 
                         FROM product p 
                         ORDER BY p.recorded_date DESC 
                         LIMIT 5";
$recent_products = find_by_sql($recent_products_query);

// Get recent sales from new structure  
$recent_sales_query = "SELECT s.sales_id, s.sale_product_id, p.product_name, s.total, s.created_at 
                      FROM sales s 
                      JOIN product p ON s.sale_product_id = p.p_id 
                      ORDER BY s.created_at DESC 
                      LIMIT 5";
$recent_sales = find_by_sql($recent_sales_query);

// Get top selling products
$top_products_query = "SELECT p.product_name, SUM(s.quantity) as total_sold, p.selling_price 
                      FROM sales s 
                      JOIN product p ON s.sale_product_id = p.p_id 
                      GROUP BY p.p_id, p.product_name, p.selling_price 
                      ORDER BY total_sold DESC 
                      LIMIT 5";
$products_sold = find_by_sql($top_products_query);

// Get expired products
$expired_products_query = "SELECT p.product_name, p.expire_date, p.quantity, s.s_name as supplier_name 
                          FROM product p 
                          JOIN supplier_info s ON p.s_id = s.s_id 
                          WHERE p.expire_date < CURDATE() 
                          ORDER BY p.expire_date ASC";
$expired_products = find_by_sql($expired_products_query);

// Get low stock products
$low_stock_query = "SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name 
                   FROM product p 
                   JOIN supplier_info s ON p.s_id = s.s_id 
                   WHERE p.quantity < 50 
                   ORDER BY p.quantity ASC 
                   LIMIT 10";
$low_stock_products = find_by_sql($low_stock_query);
?>
<?php include_once('layouts/header.php'); ?>
<!-- CHANGE 1: Uncommented dashboard message display -->
<div class="row">
   <div class="col-md-6">
     <?php echo display_msg($msg); ?>
   </div>
</div>
<!-- CHANGE 2: Updated dashboard cards for new database structure -->
  <div class="row">
    <a href="users.php" style="color:black;">
		<div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-secondary1">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_user['total']; ?> </h2>
          <p class="text-muted">Users</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="categorie.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-red">
          <i class="glyphicon glyphicon-th-large"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_categorie['total']; ?> </h2>
          <p class="text-muted">Categories</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="product.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-blue2">
          <i class="glyphicon glyphicon-shopping-cart"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_product['total']; ?> </h2>
          <p class="text-muted">Products</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="sales.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-green">
          <i class="glyphicon glyphicon-usd"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_sale['total']; ?></h2>
          <p class="text-muted">Sales</p>
        </div>
       </div>
    </div>
	</a>
</div>

<div class="row">
	<a href="returns.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-orange">
          <i class="glyphicon glyphicon-arrow-left"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"><?php echo count_by_id('return_details')['total']; ?></h2>
          <p class="text-muted">Returns</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="suppliers.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-info">
          <i class="glyphicon glyphicon-briefcase"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_supplier['total']; ?> </h2>
          <p class="text-muted">Suppliers</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="purchase_orders.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-warning">
          <i class="glyphicon glyphicon-list-alt"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top"> <?php  echo $c_purchase_order['total']; ?> </h2>
          <p class="text-muted">Purchase Orders</p>
        </div>
       </div>
    </div>
	</a>
	
	<a href="database_backup.php" style="color:black;">
    <div class="col-md-3">
       <div class="panel panel-box clearfix">
         <div class="panel-icon pull-left bg-purple">
          <i class="glyphicon glyphicon-download-alt"></i>
        </div>
        <div class="panel-value pull-right">
          <h2 class="margin-top">Backup</h2>
          <p class="text-muted">Database Backup</p>
        </div>
       </div>
    </div>
	</a>
</div>
  
  <div class="row">
   <div class="col-md-4">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Highest Selling Products</span>
         </strong>
       </div>
       <div class="panel-body">
         <table class="table table-striped table-bordered table-condensed">
          <thead>
           <tr>
             <th>Product Name</th>
             <th>Total Sold</th>
             <th>Price (Rs.)</th>
           <tr>
          </thead>
          <tbody>
            <?php foreach ($products_sold as  $product_sold): ?>
              <tr>
                <td><?php echo remove_junk(first_character($product_sold['product_name'])); ?></td>
                <td><?php echo (int)$product_sold['total_sold']; ?></td>
                <td>Rs. <?php echo number_format($product_sold['selling_price'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          <tbody>
         </table>
       </div>
     </div>
   </div>
   <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>LATEST SALES</span>
          </strong>
        </div>
        <div class="panel-body">
          <table class="table table-striped table-bordered table-condensed">
       <thead>
         <tr>
           <th class="text-center" style="width: 50px;">#</th>
           <th>Product Name</th>
           <th>Date</th>
           <th>Total Sale</th>
         </tr>
       </thead>
       <tbody>
         <?php foreach ($recent_sales as  $recent_sale): ?>
         <tr>
           <td class="text-center"><?php echo $recent_sale['sales_id'];?></td>
           <td>
            <a href="edit_sale.php?id=<?php echo (int)$recent_sale['sales_id']; ?>">
             <?php echo remove_junk(first_character($recent_sale['product_name'])); ?>
           </a>
           </td>
           <td><?php echo date('Y-m-d', strtotime($recent_sale['created_at'])); ?></td>
           <td>Rs. <?php echo number_format($recent_sale['total'], 2); ?></td>
        </tr>

       <?php endforeach; ?>
       </tbody>
     </table>
    </div>
   </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>Recently Added Products</span>
        </strong>
      </div>
      <div class="panel-body">

        <div class="list-group">
      <?php foreach ($recent_products as  $recent_product): ?>
            <a class="list-group-item clearfix" href="edit_product.php?id=<?php echo $recent_product['p_id'];?>">
                <h4 class="list-group-item-heading">
                    <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                <?php echo remove_junk(first_character($recent_product['product_name']));?>
                  <span class="label label-warning pull-right">
                 Rs. <?php echo number_format($recent_product['selling_price'], 2); ?>
                  </span>
                </h4>
                <span class="list-group-item-text pull-right">
                <?php echo remove_junk(first_character($recent_product['category_name'])); ?>
              </span>
          </a>
      <?php endforeach; ?>
    </div>
  </div>
 </div>
</div>
 </div>
<!-- CHANGE 3: Added new widgets for expired products and low stock -->
  <div class="row">
   <div class="col-md-6">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-warning-sign"></span>
           <span>Expired Products</span>
         </strong>
       </div>
       <div class="panel-body">
         <?php if(!empty($expired_products)): ?>
         <table class="table table-striped table-bordered table-condensed">
          <thead>
           <tr>
             <th>Product Name</th>
             <th>Expire Date</th>
             <th>Quantity</th>
             <th>Supplier</th>
           </tr>
          </thead>
          <tbody>
            <?php foreach ($expired_products as $expired_product): ?>
              <tr class="danger">
                <td><?php echo remove_junk($expired_product['product_name']); ?></td>
                <td><?php echo $expired_product['expire_date']; ?></td>
                <td><?php echo $expired_product['quantity']; ?></td>
                <td><?php echo remove_junk($expired_product['supplier_name']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
         </table>
         <?php else: ?>
           <p class="text-success">No expired products found!</p>
         <?php endif; ?>
       </div>
     </div>
   </div>
   
   <div class="col-md-6">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-exclamation-sign"></span>
           <span>Low Stock Alert (< 50 units)</span>
         </strong>
       </div>
       <div class="panel-body">
         <?php if(!empty($low_stock_products)): ?>
         <table class="table table-striped table-bordered table-condensed">
          <thead>
           <tr>
             <th>Product Name</th>
             <th>Quantity</th>
             <th>Price (Rs.)</th>
             <th>Supplier</th>
           </tr>
          </thead>
          <tbody>
            <?php foreach ($low_stock_products as $low_stock): ?>
              <tr class="warning">
                <td><?php echo remove_junk($low_stock['product_name']); ?></td>
                <td><span class="label label-danger"><?php echo $low_stock['quantity']; ?></span></td>
                <td>Rs. <?php echo number_format($low_stock['selling_price'], 2); ?></td>
                <td><?php echo remove_junk($low_stock['supplier_name']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
         </table>
         <?php else: ?>
           <p class="text-success">All products have sufficient stock!</p>
         <?php endif; ?>
       </div>
     </div>
   </div>
  </div>


<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-comment"></span>
          <span>Pharmacy Assistant Chatbot</span>
          <button type="button" class="btn btn-sm btn-primary pull-right" id="toggleChatbot">
            <span class="glyphicon glyphicon-chevron-down" id="chatbotToggleIcon"></span>
          </button>
        </strong>
      </div>
      <div class="panel-body" id="chatbotPanel" style="display: none;">
        <div class="chat-container">
          <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
              <div class="message-content">
                <strong>Pharmacy Inventory Assistant:</strong> Hello! I'm your pharmacy inventory management assistant. I can help you with:
                <ul>
                  <li>Medicine information and stock levels (e.g., "How many antibiotics?")</li>
                  <li>Expired products alerts (e.g., "What are the expired products?")</li>
                  <li>Supplier information (e.g., "Supplier names that supply Panadol")</li>
                  <li>Category-wise product queries (e.g., "Show me all painkillers")</li>
                  <li>Low stock alerts and restocking needs</li>
                  <li>Sales data and purchase orders</li>
                </ul>
                How can I assist you today?
              </div>
            </div>
          </div>
          <div class="chat-input-container">
            <div class="input-group">
              <input type="text" class="form-control" id="chatInput" placeholder="Ask me about medicines, suppliers, expired products..." maxlength="500">
              <span class="input-group-btn">
                <button class="btn btn-primary" type="button" id="sendMessage">
                  <span class="glyphicon glyphicon-send"></span>
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> -->
<!-- 
<style>
.chat-container {
  height: 400px;
  border: 1px solid #ddd;
  border-radius: 5px;
  display: flex;
  flex-direction: column;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
  background-color: #f9f9f9;
}

.message {
  margin-bottom: 15px;
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.user-message {
  text-align: right;
}

.bot-message {
  text-align: left;
}

.message-content {
  display: inline-block;
  max-width: 80%;
  padding: 10px 15px;
  border-radius: 18px;
  word-wrap: break-word;
}

.user-message .message-content {
  background-color: #007bff;
  color: white;
}

.bot-message .message-content {
  background-color: white;
  border: 1px solid #ddd;
  color: #333;
}

.chat-input-container {
  padding: 15px;
  background-color: white;
  border-top: 1px solid #ddd;
}

.typing-indicator {
  display: none;
  padding: 10px 15px;
  color: #666;
  font-style: italic;
}

.typing-indicator.show {
  display: block;
}

.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.data-table {
  margin-top: 10px;
  font-size: 12px;
}

.data-table table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 5px;
  border: 1px solid #ddd;
  text-align: left;
}

.data-table th {
  background-color: #f5f5f5;
  font-weight: bold;
}
</style>
 -->
<!-- 
<script>
document.addEventListener('DOMContentLoaded', function() {
  const chatbotPanel = document.getElementById('chatbotPanel');
  const chatbotToggle = document.getElementById('toggleChatbot');
  const chatbotToggleIcon = document.getElementById('chatbotToggleIcon');
  const chatMessages = document.getElementById('chatMessages');
  const chatInput = document.getElementById('chatInput');
  const sendButton = document.getElementById('sendMessage');
  
  let isTyping = false;
  
  // Toggle chatbot panel
  chatbotToggle.addEventListener('click', function() {
    if (chatbotPanel.style.display === 'none') {
      chatbotPanel.style.display = 'block';
      chatbotToggleIcon.className = 'glyphicon glyphicon-chevron-up';
    } else {
      chatbotPanel.style.display = 'none';
      chatbotToggleIcon.className = 'glyphicon glyphicon-chevron-down';
    }
  }); -->
<!--   
  // Send message function
  function sendMessage() {
    const message = chatInput.value.trim();
    if (!message || isTyping) return;
    
    // Add user message to chat
    addMessage(message, 'user');
    chatInput.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send to API - CHANGE 4: Updated to use new chatbot for pharmacy database
    fetch('updated_chatbot.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ message: message })
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      hideTypingIndicator();
      console.log('API Response:', data);
      
      if (data.success) {
        addMessage(data.response, 'bot');
        
        // If there's data, display it in a table
        if (data.data && data.data.length > 0) {
          displayDataTable(data.data);
        }
      } else {
        addMessage('Sorry, I encountered an error: ' + (data.error || 'Unknown error'), 'bot');
      }
    })
    .catch(error => {
      hideTypingIndicator();
      console.error('Detailed Error:', error);
      addMessage('Sorry, I\'m having trouble connecting. Please try again later. Error: ' + error.message, 'bot');
    });
  }
  
  // Add message to chat
  function addMessage(content, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}-message`;
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.innerHTML = content;
    
    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  // Show typing indicator
  function showTypingIndicator() {
    isTyping = true;
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator show';
    typingDiv.id = 'typingIndicator';
    typingDiv.textContent = 'Assistant is typing...';
    chatMessages.appendChild(typingDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }
  
  // Hide typing indicator
  function hideTypingIndicator() {
    isTyping = false;
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
      typingIndicator.remove();
    }
  }
  
  // Display data in table format
  function displayDataTable(data) {
    if (!data || data.length === 0) return;
    
    const tableDiv = document.createElement('div');
    tableDiv.className = 'data-table';
    
    let table = '<table><thead><tr>';
    
    // Get headers from first object
    const headers = Object.keys(data[0]);
    headers.forEach(header => {
      table += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
    });
    table += '</tr></thead><tbody>';
    
    // Add data rows
    data.forEach(row => {
      table += '<tr>';
      headers.forEach(header => {
        table += `<td>${row[header] || ''}</td>`;
      });
      table += '</tr>';
    });
    
    table += '</tbody></table>';
    tableDiv.innerHTML = table;
    
    // Add to last bot message
    const lastMessage = chatMessages.querySelector('.bot-message:last-child .message-content');
    lastMessage.appendChild(tableDiv);
  }
  
  // Event listeners
  sendButton.addEventListener('click', sendMessage);
  
  chatInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      sendMessage();
    }
  });
  
  // Focus input when panel opens
  chatbotToggle.addEventListener('click', function() {
    if (chatbotPanel.style.display === 'block') {
      setTimeout(() => chatInput.focus(), 300);
    }
  });
});
</script> -->

<?php include_once('layouts/footer.php'); ?>