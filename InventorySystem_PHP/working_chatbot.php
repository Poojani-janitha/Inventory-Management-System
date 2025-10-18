<?php
// Working chatbot with real database data
header('Content-Type: application/json');

// Handle CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database connection
require_once('includes/load.php');
global $db;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    $response = "";
    $data = [];
    
    try {
        if (strpos(strtolower($message), 'low stock') !== false) {
            // Get real low stock products from database
            $query = $db->query("SELECT name, quantity, sale_price FROM products WHERE CAST(quantity AS UNSIGNED) < 50 ORDER BY CAST(quantity AS UNSIGNED) ASC LIMIT 10");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "Here are your low stock products (less than 50 units):";
            } else {
                $response = "Great news! You don't have any products with low stock levels.";
            }
            
        } elseif (strpos(strtolower($message), 'top selling') !== false || strpos(strtolower($message), 'best selling') !== false) {
            // Get top selling products based on actual sales data
            $query = $db->query("
                SELECT p.name, SUM(s.qty) as total_sold, SUM(s.price) as total_revenue, p.sale_price, c.name as category
                FROM sales s 
                JOIN products p ON s.product_id = p.id 
                JOIN categories c ON p.categorie_id = c.id
                GROUP BY p.id, p.name, p.sale_price, c.name 
                ORDER BY total_sold DESC 
                LIMIT 5
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "Here are your top selling products (ranked by quantity sold):";
            } else {
                $response = "You don't have any sales data yet to determine top selling products.";
            }
            
        } elseif (strpos(strtolower($message), 'sales') !== false) {
            // Get real sales data from database
            $query = $db->query("SELECT p.name, s.qty, s.price, s.date FROM sales s JOIN products p ON s.product_id = p.id ORDER BY s.date DESC LIMIT 10");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "Here are your recent sales:";
            } else {
                $response = "You don't have any sales data yet.";
            }
            
        } elseif (strpos(strtolower($message), 'expensive') !== false || strpos(strtolower($message), 'high price') !== false) {
            // Get most expensive products
            $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.sale_price DESC LIMIT 10");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            $response = "Here are your most expensive products:";
            
        } elseif (strpos(strtolower($message), 'cheap') !== false || strpos(strtolower($message), 'low price') !== false) {
            // Get cheapest products
            $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.sale_price ASC LIMIT 10");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            $response = "Here are your cheapest products:";
            
        } elseif (strpos(strtolower($message), 'restock') !== false || strpos(strtolower($message), 'need restocking') !== false) {
            // Get products that need restocking (low stock)
            $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id WHERE CAST(p.quantity AS UNSIGNED) < 50 ORDER BY CAST(p.quantity AS UNSIGNED) ASC");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "Here are products that need restocking (less than 50 units):";
            } else {
                $response = "Great news! All your products have sufficient stock levels.";
            }
            
        } elseif (strpos(strtolower($message), 'product') !== false) {
            // Get real product data from database
            $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.name ASC LIMIT 20");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            $response = "Here are your products:";
            
        } elseif (strpos(strtolower($message), 'categor') !== false) {
            // Get real category data from database
            $query = $db->query("SELECT c.name, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.categorie_id GROUP BY c.id, c.name ORDER BY product_count DESC");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            $response = "Here are your product categories:";
            
        } elseif (strpos(strtolower($message), 'available') !== false || strpos(strtolower($message), 'is ') !== false) {
            // Check if specific product is available
            $product_name = "";
            $words = explode(' ', strtolower($message));
            
            // Find product name in the message
            foreach ($words as $word) {
                if ($word != 'is' && $word != 'available' && $word != '?' && $word != 'the' && $word != 'a' && $word != 'an') {
                    $product_name = $word;
                    break;
                }
            }
            
            if ($product_name) {
                $query = $db->query("SELECT name, quantity, sale_price FROM products WHERE LOWER(name) LIKE '%" . $db->escape($product_name) . "%'");
                $found = false;
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                    $found = true;
                }
                
                if ($found) {
                    $response = "Here's what I found for '" . $product_name . "':";
                } else {
                    $response = "I couldn't find any products matching '" . $product_name . "'. Here are some similar products:";
                    // Show similar products
                    $query = $db->query("SELECT name, quantity, sale_price FROM products ORDER BY name ASC LIMIT 10");
                    while ($row = $db->fetch_assoc($query)) {
                        $data[] = $row;
                    }
                }
            } else {
                $response = "Please specify which product you're looking for. For example: 'Is wheat available?' or 'Is demo product available?'";
            }
            
        } elseif (strpos(strtolower($message), 'quantity') !== false || strpos(strtolower($message), 'how many') !== false) {
            // Get quantity of specific product
            $product_name = "";
            $words = explode(' ', strtolower($message));
            
            // Find product name in the message
            foreach ($words as $word) {
                if ($word != 'quantity' && $word != 'of' && $word != 'how' && $word != 'many' && $word != '?' && $word != 'the' && $word != 'a' && $word != 'an') {
                    $product_name = $word;
                    break;
                }
            }
            
            if ($product_name) {
                $query = $db->query("SELECT name, quantity, sale_price FROM products WHERE LOWER(name) LIKE '%" . $db->escape($product_name) . "%'");
                $found = false;
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                    $found = true;
                }
                
                if ($found) {
                    $response = "Here's the quantity information for '" . $product_name . "':";
                } else {
                    $response = "I couldn't find any products matching '" . $product_name . "'. Here are some similar products:";
                    // Show similar products
                    $query = $db->query("SELECT name, quantity, sale_price FROM products ORDER BY name ASC LIMIT 10");
                    while ($row = $db->fetch_assoc($query)) {
                        $data[] = $row;
                    }
                }
            } else {
                $response = "Please specify which product you want to check. For example: 'Quantity of demo product' or 'How many wheat do we have?'";
            }
            
        } elseif (strpos(strtolower($message), 'search') !== false || strpos(strtolower($message), 'find') !== false) {
            // Search for products
            $search_term = "";
            $words = explode(' ', strtolower($message));
            
            // Find search term
            foreach ($words as $word) {
                if ($word != 'search' && $word != 'for' && $word != 'find' && $word != '?' && $word != 'the' && $word != 'a' && $word != 'an') {
                    $search_term = $word;
                    break;
                }
            }
            
            if ($search_term) {
                $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id WHERE LOWER(p.name) LIKE '%" . $db->escape($search_term) . "%' ORDER BY p.name ASC");
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                }
                
                if (!empty($data)) {
                    $response = "Here are the products matching '" . $search_term . "':";
                } else {
                    $response = "No products found matching '" . $search_term . "'. Here are all your products:";
                    $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.name ASC LIMIT 20");
                    while ($row = $db->fetch_assoc($query)) {
                        $data[] = $row;
                    }
                }
            } else {
                $response = "Please specify what you want to search for. For example: 'Search for wheat' or 'Find demo product'";
            }
            
        } elseif (strpos(strtolower($message), 'hi') !== false || strpos(strtolower($message), 'hello') !== false) {
            // Get inventory summary
            $products = $db->query("SELECT COUNT(*) as total FROM products");
            $categories = $db->query("SELECT COUNT(*) as total FROM categories");
            $sales = $db->query("SELECT COUNT(*) as total FROM sales");
            
            $product_count = $db->fetch_assoc($products)['total'];
            $category_count = $db->fetch_assoc($categories)['total'];
            $sales_count = $db->fetch_assoc($sales)['total'];
            
            $response = "Hello! I'm your inventory assistant. Here's your current inventory summary:\n";
            $response .= "• Products: " . $product_count . "\n";
            $response .= "• Categories: " . $category_count . "\n";
            $response .= "• Sales: " . $sales_count . "\n";
            $response .= "How can I help you today?";
            
        } elseif (strpos(strtolower($message), 'under') !== false && strpos(strtolower($message), '$') !== false) {
            // Get products under specific price
            $price = 50; // Default to $50
            if (preg_match('/\$(\d+)/', $message, $matches)) {
                $price = intval($matches[1]);
            }
            
            $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id WHERE CAST(p.sale_price AS DECIMAL(10,2)) < " . $price . " ORDER BY p.sale_price ASC");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "Here are products under $" . $price . ":";
            } else {
                $response = "No products found under $" . $price . ". Here are your cheapest products:";
                $query = $db->query("SELECT p.name, p.quantity, p.sale_price, c.name as category FROM products p JOIN categories c ON p.categorie_id = c.id ORDER BY p.sale_price ASC LIMIT 10");
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                }
            }
            
        } else {
            $response = "I can help you with:\n- Product information\n- Sales data\n- Category management\n- Low stock alerts\n- Products under specific prices\n\nWhat would you like to know?";
        }
        
        echo json_encode([
            'success' => true,
            'response' => $response,
            'data' => $data
        ]);
        
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => true,
        'response' => 'Chatbot is working! Send me a message.',
        'data' => []
    ]);
}
?>
