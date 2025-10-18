<?php
require_once('includes/load.php');
require_once('includes/database.php');
require_once('includes/chatbot_config.php');

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

// Check if chatbot is enabled
if (!isChatbotEnabled()) {
    echo json_encode(['error' => 'Chatbot is currently disabled']);
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

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    // Debug logging
    error_log("Chatbot API called with message: " . $message);
    
    if (empty($message)) {
        echo json_encode(['error' => 'No message provided']);
        exit();
    }
    
    // Get inventory data for context
    $inventoryData = getInventoryData();
    $specificData = queryInventoryData($message);
    
    // Prepare context for ChatGPT
    $context = "You are an AI assistant for an inventory management system. Here's the current data:\n\n";
    $context .= "Total Products: " . $inventoryData['total_products'] . "\n";
    $context .= "Total Categories: " . $inventoryData['total_categories'] . "\n";
    $context .= "Total Sales: " . $inventoryData['total_sales'] . "\n";
    $context .= "Total Users: " . $inventoryData['total_users'] . "\n\n";
    
    if (!empty($inventoryData['low_stock_products'])) {
        $context .= "Low Stock Products:\n";
        foreach ($inventoryData['low_stock_products'] as $product) {
            $context .= "- " . $product['name'] . " (Stock: " . $product['quantity'] . ")\n";
        }
        $context .= "\n";
    }
    
    if (!empty($inventoryData['top_selling_products'])) {
        $context .= "Top Selling Products:\n";
        foreach ($inventoryData['top_selling_products'] as $product) {
            $context .= "- " . $product['name'] . " (Sold: " . $product['total_sold'] . ", Revenue: $" . $product['total_revenue'] . ")\n";
        }
        $context .= "\n";
    }
    
    if (!empty($specificData)) {
        $context .= "Specific Query Results:\n";
        foreach ($specificData as $item) {
            $context .= "- " . json_encode($item) . "\n";
        }
        $context .= "\n";
    }
    
    $context .= "User Question: " . $message . "\n\n";
    $context .= "Please provide a helpful response based on the inventory data. Be concise and actionable.";
    
    // Get configuration
    $config = getChatbotConfig();
    
    // Validate API key
    if (!isValidAPIKey($config['api_key'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing OpenAI API key. Please configure your API key in includes/chatbot_config.php'
        ]);
        exit();
    }
    
    // ChatGPT API integration
    $data = [
        'model' => $config['model'],
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful AI assistant for an inventory management system. Provide accurate, concise, and actionable responses based on the provided data.'
            ],
            [
                'role' => 'user',
                'content' => $context
            ]
        ],
        'max_tokens' => $config['max_tokens'],
        'temperature' => $config['temperature']
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['api_url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $config['api_key']
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        $aiResponse = $responseData['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';
        
        echo json_encode([
            'success' => true,
            'response' => $aiResponse,
            'data' => $specificData
        ]);
    } else {
        // Fallback response if API fails
        if ($config['fallback_enabled']) {
            $fallbackResponse = "I can see your inventory data, but I'm having trouble connecting to the AI service. ";
            
            if (!empty($specificData)) {
                $fallbackResponse .= "Here's what I found: ";
                foreach (array_slice($specificData, 0, 3) as $item) {
                    $fallbackResponse .= $item['name'] . " ";
                }
            } else {
                $fallbackResponse .= "You have " . $inventoryData['total_products'] . " products in " . $inventoryData['total_categories'] . " categories.";
            }
            
            echo json_encode([
                'success' => true,
                'response' => $fallbackResponse,
                'data' => $specificData
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'AI service is currently unavailable. Please try again later.'
            ]);
        }
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
