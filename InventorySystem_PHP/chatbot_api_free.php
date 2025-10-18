<?php
require_once('includes/load.php');
require_once('includes/database.php');

// Set content type to JSON
header('Content-Type: application/json');

// Enable CORS for AJAX requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Get inventory data for context
function getInventoryData() {
    global $db;
    
    $data = [];
    
    // Get product statistics
    $products = $db->query("SELECT COUNT(*) as total FROM products");
    $data['total_products'] = $db->fetch_assoc($products)['total'];
    
    // Get category statistics
    $categories = $db->query("SELECT COUNT(*) as total FROM categories");
    $data['total_categories'] = $db->fetch_assoc($categories)['total'];
    
    // Get sales statistics
    $sales = $db->query("SELECT COUNT(*) as total FROM sales");
    $data['total_sales'] = $db->fetch_assoc($sales)['total'];
    
    // Get user statistics
    $users = $db->query("SELECT COUNT(*) as total FROM users");
    $data['total_users'] = $db->fetch_assoc($users)['total'];
    
    // Get low stock products
    $low_stock = $db->query("SELECT name, quantity FROM products WHERE CAST(quantity AS UNSIGNED) < 50 ORDER BY CAST(quantity AS UNSIGNED) ASC LIMIT 5");
    $data['low_stock_products'] = [];
    while ($row = $db->fetch_assoc($low_stock)) {
        $data['low_stock_products'][] = $row;
    }
    
    // Get top selling products
    $top_selling = $db->query("
        SELECT p.name, SUM(s.qty) as total_sold, SUM(s.price) as total_revenue 
        FROM products p 
        JOIN sales s ON p.id = s.product_id 
        GROUP BY p.id, p.name 
        ORDER BY total_sold DESC 
        LIMIT 5
    ");
    $data['top_selling_products'] = [];
    while ($row = $db->fetch_assoc($top_selling)) {
        $data['top_selling_products'][] = $row;
    }
    
    // Get recent sales
    $recent_sales = $db->query("
        SELECT p.name, s.qty, s.price, s.date 
        FROM sales s 
        JOIN products p ON s.product_id = p.id 
        ORDER BY s.date DESC 
        LIMIT 5
    ");
    $data['recent_sales'] = [];
    while ($row = $db->fetch_assoc($recent_sales)) {
        $data['recent_sales'][] = $row;
    }
    
    // Get categories with product counts
    $categories_with_counts = $db->query("
        SELECT c.name, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.categorie_id 
        GROUP BY c.id, c.name 
        ORDER BY product_count DESC
    ");
    $data['categories_with_counts'] = [];
    while ($row = $db->fetch_assoc($categories_with_counts)) {
        $data['categories_with_counts'][] = $row;
    }
    
    return $data;
}

// Function to query specific data based on user question
function queryInventoryData($question) {
    global $db;
    
    $question = strtolower($question);
    $results = [];
    
    // Check for specific product queries
    if (strpos($question, 'product') !== false) {
        if (strpos($question, 'low stock') !== false || strpos($question, 'low inventory') !== false) {
            $query = "SELECT name, quantity, sale_price FROM products WHERE CAST(quantity AS UNSIGNED) < 50 ORDER BY CAST(quantity AS UNSIGNED) ASC LIMIT 10";
            $result = $db->query($query);
            while ($row = $db->fetch_assoc($result)) {
                $results[] = $row;
            }
        } elseif (strpos($question, 'expensive') !== false || strpos($question, 'high price') !== false) {
            $query = "SELECT name, sale_price FROM products ORDER BY sale_price DESC LIMIT 10";
            $result = $db->query($query);
            while ($row = $db->fetch_assoc($result)) {
                $results[] = $row;
            }
        } elseif (strpos($question, 'cheap') !== false || strpos($question, 'low price') !== false) {
            $query = "SELECT name, sale_price FROM products ORDER BY sale_price ASC LIMIT 10";
            $result = $db->query($query);
            while ($row = $db->fetch_assoc($result)) {
                $results[] = $row;
            }
        } else {
            // General product search
            $query = "SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.name ASC LIMIT 20";
            $result = $db->query($query);
            while ($row = $db->fetch_assoc($result)) {
                $results[] = $row;
            }
        }
    }
    
    // Check for top selling products queries
    if (strpos($question, 'top selling') !== false || strpos($question, 'best selling') !== false) {
        $query = "SELECT p.name, SUM(s.qty) as total_sold, SUM(s.price) as total_revenue, p.sale_price, c.name as category FROM sales s JOIN products p ON s.product_id = p.id JOIN categories c ON p.categorie_id = c.id GROUP BY p.id, p.name, p.sale_price, c.name ORDER BY total_sold DESC LIMIT 5";
        $result = $db->query($query);
        while ($row = $db->fetch_assoc($result)) {
            $results[] = $row;
        }
    }
    // Check for sales queries
    elseif (strpos($question, 'sales') !== false || strpos($question, 'revenue') !== false) {
        if (strpos($question, 'today') !== false) {
            $query = "SELECT p.name, s.qty, s.price, s.date FROM sales s JOIN products p ON s.product_id = p.id WHERE DATE(s.date) = CURDATE() ORDER BY s.date DESC";
        } elseif (strpos($question, 'recent') !== false) {
            $query = "SELECT p.name, s.qty, s.price, s.date FROM sales s JOIN products p ON s.product_id = p.id ORDER BY s.date DESC LIMIT 10";
        } else {
            $query = "SELECT p.name, SUM(s.qty) as total_qty, SUM(s.price) as total_revenue FROM sales s JOIN products p ON s.product_id = p.id GROUP BY p.id, p.name ORDER BY total_revenue DESC LIMIT 10";
        }
        $result = $db->query($query);
        while ($row = $db->fetch_assoc($result)) {
            $results[] = $row;
        }
    }
    
    // Check for category queries
    if (strpos($question, 'categor') !== false) {
        $query = "SELECT c.name, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.categorie_id GROUP BY c.id, c.name ORDER BY product_count DESC";
        $result = $db->query($query);
        while ($row = $db->fetch_assoc($result)) {
            $results[] = $row;
        }
    }
    
    return $results;
}

// Generate intelligent response based on question and data
function generateResponse($question, $inventoryData, $specificData) {
    $question = strtolower($question);
    $response = "";
    
    // Greeting responses
    if (strpos($question, 'hello') !== false || strpos($question, 'hi') !== false) {
        $response = "Hello! I'm your inventory assistant. I can help you with product information, sales data, and inventory management. What would you like to know?";
    }
    
    // Product count queries
    elseif (strpos($question, 'how many product') !== false || strpos($question, 'total product') !== false) {
        $response = "You currently have " . $inventoryData['total_products'] . " products in your inventory across " . $inventoryData['total_categories'] . " categories.";
    }
    
    // Low stock queries
    elseif (strpos($question, 'low stock') !== false || strpos($question, 'running out') !== false) {
        if (!empty($inventoryData['low_stock_products'])) {
            $response = "Here are your low stock products (less than 50 units):";
        } else {
            $response = "Great news! You don't have any products with low stock levels.";
        }
    }
    
    // Sales queries
    elseif (strpos($question, 'sales') !== false || strpos($question, 'revenue') !== false) {
        if (strpos($question, 'today') !== false) {
            $response = "Here are today's sales:";
        } elseif (strpos($question, 'recent') !== false) {
            $response = "Here are your recent sales:";
        } else {
            $response = "Here's your sales data:";
        }
    }
    
    // Category queries
    elseif (strpos($question, 'categor') !== false) {
        $response = "Here are your product categories with product counts:";
    }
    
    // Top selling queries
    elseif (strpos($question, 'top selling') !== false || strpos($question, 'best selling') !== false) {
        if (!empty($inventoryData['top_selling_products'])) {
            $response = "Here are your top selling products:";
        } else {
            $response = "You don't have any sales data yet to determine top selling products.";
        }
    }
    
    // General inventory overview
    elseif (strpos($question, 'overview') !== false || strpos($question, 'summary') !== false || strpos($question, 'inventory') !== false) {
        $response = "Here's your inventory overview:\n";
        $response .= "• Total Products: " . $inventoryData['total_products'] . "\n";
        $response .= "• Total Categories: " . $inventoryData['total_categories'] . "\n";
        $response .= "• Total Sales: " . $inventoryData['total_sales'] . "\n";
        $response .= "• Total Users: " . $inventoryData['total_users'] . "\n";
        
        if (!empty($inventoryData['low_stock_products'])) {
            $response .= "\n⚠️ Low Stock Alert: " . count($inventoryData['low_stock_products']) . " products need restocking.";
        }
    }
    
    // User count queries
    elseif (strpos($question, 'how many user') !== false || strpos($question, 'total user') !== false) {
        $response = "You have " . $inventoryData['total_users'] . " registered users in your system.";
    }
    
    // Default response
    else {
        $response = "I found some information for you. Here's what I can tell you about your inventory:";
        if (!empty($specificData)) {
            $response .= " I found " . count($specificData) . " relevant items.";
        } else {
            $response .= " You have " . $inventoryData['total_products'] . " products in " . $inventoryData['total_categories'] . " categories.";
        }
    }
    
    return $response;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    if (empty($message)) {
        echo json_encode(['error' => 'No message provided']);
        exit();
    }
    
    // Get inventory data
    $inventoryData = getInventoryData();
    $specificData = queryInventoryData($message);
    
    // Generate response
    $response = generateResponse($message, $inventoryData, $specificData);
    
    echo json_encode([
        'success' => true,
        'response' => $response,
        'data' => $specificData
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
