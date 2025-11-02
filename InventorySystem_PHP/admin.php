<?php
  $page_title = 'Admin Home Page';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(1);

   $msg = $session->msg();
?>
<?php
// CHANGE 1: Updated for new pharmacy database structure
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
<!-- CHANGE 2: Added styled dashboard with welcome section -->
<div class="admin-dashboard">
  <div class="row">
    <div class="col-md-12">
      <div class="dashboard-header">
        <h1><i class="glyphicon glyphicon-dashboard"></i> HealStock Warehouse Management System</h1>
        <p>Welcome to HealStock’s all-in-one inventory management system.</p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <?php echo display_msg($msg); ?>
    </div>
  </div>
<!-- 
  <div class="row">
    <div class="col-md-12">
      <div class="welcome-section">
        <h3><i class="glyphicon glyphicon-home"></i> HealStock Management Hub</h3>
        <p>Monitor your warehouse inventory, track sales, manage suppliers, and ensure optimal stock levels for HealStock operations</p>
      </div>
    </div>
  </div> -->
<!-- CHANGE 3: Updated dashboard cards for new database structure -->
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
<!-- CHANGE 4: Removed all data tables, keeping only cards and chatbot -->


  <div class="row">
    <div class="col-md-12">
      <div class="panel chatbot-panel">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-comment"></span>
            <span>HealStock Assistant Chatbot</span>
            <button type="button" class="btn btn-sm btn-primary pull-right" id="toggleChatbot" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
              <span class="glyphicon glyphicon-chevron-down" id="chatbotToggleIcon"></span>
            </button>
          </strong>
        </div>
      <div class="panel-body" id="chatbotPanel" style="display: none;">
        <div class="chat-container">
          <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
              <div class="message-content">
                <strong>HealStock Inventory Assistant:</strong> Hello! I'm your HealStock warehouse inventory management assistant. I can help you with:
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
              <input type="text" class="form-control" id="chatInput" placeholder="Ask me about HealStock inventory, suppliers, expired products..." maxlength="500">
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
  </div>

  <!-- REMOVED: Duplicate cards section -->

</div> <!-- End admin-dashboard -->

<!-- CHANGE 5: Added returns.php styling for admin dashboard -->
<style>
/* Admin Dashboard Styling - Based on returns.php */
.admin-dashboard {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
  padding: 5px 5px;
}

.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 30px;
  border-radius: 8px;
  margin-bottom: 30px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  text-align: center;
}

.dashboard-header h1 {
  margin: 0;
  font-size: 2.5em;
  font-weight: 600;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.dashboard-header p {
  margin: 10px 0 0 0;
  font-size: 1.2em;
  opacity: 0.9;
}

/* Enhanced Panel Styling */
.panel {
  border: none;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  margin-bottom: 25px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.panel:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.panel-box {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  height: 120px;
  position: relative;
}

.panel-box:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.panel-icon {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  width: 40%;
  float: left;
}

.panel-icon i {
  font-size: 2.5em;
  color: white;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.panel-value {
  padding: 20px;
  height: 100%;
  width: 60%;
  float: right;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: white;
}

.panel-value h2 {
  margin: 0;
  font-size: 2.2em;
  font-weight: 700;
  color: #2c3e50;
}

.panel-value p {
  margin: 5px 0 0 0;
  color: #7f8c8d;
  font-size: 1em;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Color variations for different panels */
.bg-secondary1 { background: linear-gradient(135deg, #b17897 0%, #9b6b8a 100%) !important; }
.bg-red { background: linear-gradient(135deg, #FF7857 0%, #e85a4f 100%) !important; }
.bg-blue2 { background: linear-gradient(135deg, #7a83ee 0%, #6c75d8 100%) !important; }
.bg-green { background: linear-gradient(135deg, #A3C86D 0%, #8fb85c 100%) !important; }
.bg-orange { background: linear-gradient(135deg, #FF8C42 0%, #e67e22 100%) !important; }
.bg-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; }
.bg-warning { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%) !important; }
.bg-purple { background: linear-gradient(135deg, #8E44AD 0%, #7d3c98 100%) !important; }

/* Chatbot Panel Styling */
.chatbot-panel {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  margin-top: 30px;
}

.chatbot-panel .panel-heading {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  border: none !important;
  border-radius: 8px 8px 0 0;
  padding: 20px;
}

.chatbot-panel .panel-heading strong {
  font-size: 1.3em;
  font-weight: 600;
}

.chatbot-panel .panel-body {
  padding: 0;
}

/* Welcome Section */
.welcome-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 25px;
  border-radius: 8px;
  margin-bottom: 30px;
  text-align: center;
}

.welcome-section h3 {
  margin: 0 0 15px 0;
  font-size: 1.8em;
  font-weight: 600;
}

.welcome-section p {
  margin: 0;
  font-size: 1.1em;
  opacity: 0.9;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-header h1 {
    font-size: 2em;
  }
  
  .dashboard-header p {
    font-size: 1em;
  }
  
  .panel-value h2 {
    font-size: 1.8em;
  }
  
  .panel-icon i {
    font-size: 2em;
  }
  
  .panel-box {
    height: 100px;
  }
}

@media (max-width: 480px) {
  .panel-icon {
    width: 35%;
  }
  
  .panel-value {
    width: 65%;
    padding: 15px;
  }
  
  .panel-value h2 {
    font-size: 1.5em;
  }
  
  .panel-value p {
    font-size: 0.9em;
  }
}

/* Animation for cards */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.panel-box {
  animation: fadeInUp 0.6s ease-out;
}

.panel-box:nth-child(1) { animation-delay: 0.1s; }
.panel-box:nth-child(2) { animation-delay: 0.2s; }
.panel-box:nth-child(3) { animation-delay: 0.3s; }
.panel-box:nth-child(4) { animation-delay: 0.4s; }

/* Chatbot specific styles */
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
<!-- CHANGE 6: Reverted JavaScript for original chatbot panel -->
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
  });
  
  // Send message function
  function sendMessage() {
    const message = chatInput.value.trim();
    if (!message || isTyping) return;
    
    // Add user message to chat
    addMessage(message, 'user');
    chatInput.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send to API - CHANGE 7: Updated to use new pharmacy chatbot
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
</script>

<?php include_once('layouts/footer.php'); ?>