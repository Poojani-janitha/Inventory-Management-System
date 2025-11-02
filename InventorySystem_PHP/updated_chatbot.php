<?php
// Updated chatbot for new pharmacy database structure
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
        $message_lower = strtolower($message);
        
        // Antibiotic queries
        if (strpos($message_lower, 'antibiotic') !== false || strpos($message_lower, 'how many antibiotic') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name, p.expire_date
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.category_name = 'Antibiotic' 
                ORDER BY p.quantity DESC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $total_antibiotics = array_sum(array_column($data, 'quantity'));
                $response = "You have {$total_antibiotics} antibiotic units in total. Here's the breakdown:";
            } else {
                $response = "No antibiotics found in inventory.";
            }
            
        // Expired products
        } elseif (strpos($message_lower, 'expired') !== false || strpos($message_lower, 'expire') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.expire_date, p.quantity, s.s_name as supplier_name, p.category_name
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.expire_date < CURDATE() 
                ORDER BY p.expire_date ASC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "⚠️ Warning! You have " . count($data) . " expired products:";
            } else {
                $response = "✅ Great news! No expired products found.";
            }
            
        // Supplier queries
        } elseif (strpos($message_lower, 'supplier') !== false && (strpos($message_lower, 'supply') !== false || strpos($message_lower, 'provide') !== false)) {
            // Extract product name from query
            $product_name = "";
            if (preg_match('/supply\s+(\w+)/i', $message, $matches) || preg_match('/provide\s+(\w+)/i', $message, $matches)) {
                $product_name = $matches[1];
            }
            
            if ($product_name) {
                $query = $db->query("
                    SELECT DISTINCT s.s_name, s.contact_number, s.email, sp.price, p.product_name
                    FROM supplier_info s 
                    JOIN supplier_product sp ON s.s_id = sp.s_id 
                    LEFT JOIN product p ON sp.product_name = p.product_name
                    WHERE LOWER(sp.product_name) LIKE '%" . $db->escape(strtolower($product_name)) . "%'
                    ORDER BY sp.price ASC
                ");
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                }
                
                if (!empty($data)) {
                    $response = "Suppliers that provide '{$product_name}':";
                } else {
                    $response = "No suppliers found for '{$product_name}'. Here are all suppliers:";
                    $query = $db->query("SELECT s_name, contact_number, email FROM supplier_info ORDER BY s_name ASC LIMIT 10");
                    while ($row = $db->fetch_assoc($query)) {
                        $data[] = $row;
                    }
                }
            } else {
                $query = $db->query("SELECT s_name, contact_number, email FROM supplier_info ORDER BY s_name ASC");
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                }
                $response = "Here are all suppliers:";
            }
            
        // Category-specific queries
        } elseif (strpos($message_lower, 'painkiller') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.category_name = 'Painkiller' 
                ORDER BY p.quantity DESC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are all painkillers in inventory:";
            
        } elseif (strpos($message_lower, 'vitamin') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.category_name = 'Vitamin' 
                ORDER BY p.quantity DESC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are all vitamins in inventory:";
            
        } elseif (strpos($message_lower, 'cough syrup') !== false || strpos($message_lower, 'cough') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.category_name = 'Cough Syrup' 
                ORDER BY p.quantity DESC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are all cough syrups in inventory:";
            
        // Low stock queries
        } elseif (strpos($message_lower, 'low stock') !== false || strpos($message_lower, 'restock') !== false) {
            $query = $db->query("
                SELECT p.product_name, p.quantity, p.selling_price, s.s_name as supplier_name, p.category_name
                FROM product p 
                JOIN supplier_info s ON p.s_id = s.s_id 
                WHERE p.quantity < 50 
                ORDER BY p.quantity ASC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            
            if (!empty($data)) {
                $response = "⚠️ Products with low stock (less than 50 units):";
            } else {
                $response = "✅ All products have sufficient stock levels.";
            }
            
        // Sales queries
        } elseif (strpos($message_lower, 'sales') !== false || strpos($message_lower, 'recent sales') !== false) {
            $query = $db->query("
                SELECT s.sales_id, p.product_name, s.quantity, s.total, s.created_at
                FROM sales s 
                JOIN product p ON s.sale_product_id = p.p_id 
                ORDER BY s.created_at DESC 
                LIMIT 10
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are recent sales:";
            
        // Purchase orders
        } elseif (strpos($message_lower, 'purchase') !== false || strpos($message_lower, 'order') !== false) {
            $query = $db->query("
                SELECT po.o_id, po.product_name, po.quantity, po.price, po.status, s.s_name as supplier_name, po.order_date
                FROM purchase_order po 
                JOIN supplier_info s ON po.s_id = s.s_id 
                ORDER BY po.order_date DESC 
                LIMIT 10
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are recent purchase orders:";
            
        // Returns
        } elseif (strpos($message_lower, 'return') !== false) {
            $query = $db->query("
                SELECT r.return_id, r.product_name, r.return_quantity, r.buying_price, s.s_name as supplier_name, r.return_date
                FROM return_details r 
                JOIN supplier_info s ON r.s_id = s.s_id 
                ORDER BY r.return_date DESC 
                LIMIT 10
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are recent returns:";
            
        // Categories
        } elseif (strpos($message_lower, 'categor') !== false) {
            $query = $db->query("
                SELECT c.category_name, COUNT(p.p_id) as product_count
                FROM categories c 
                LEFT JOIN product p ON c.category_name = p.category_name 
                GROUP BY c.category_name 
                ORDER BY product_count DESC
            ");
            while ($row = $db->fetch_assoc($query)) {
                $data[] = $row;
            }
            $response = "Here are all product categories:";
            
        // Specific product search
        } elseif (strpos($message_lower, 'panadol') !== false || strpos($message_lower, 'amoxicillin') !== false || 
                  strpos($message_lower, 'paracetamol') !== false || strpos($message_lower, 'ibuprofen') !== false) {
            
            // Extract product name
            $search_terms = ['panadol', 'amoxicillin', 'paracetamol', 'ibuprofen', 'azithromycin', 'vitamin c', 'aspirin'];
            $product_search = "";
            
            foreach ($search_terms as $term) {
                if (strpos($message_lower, $term) !== false) {
                    $product_search = $term;
                    break;
                }
            }
            
            if ($product_search) {
                $query = $db->query("
                    SELECT p.product_name, p.quantity, p.selling_price, p.buying_price, s.s_name as supplier_name, p.expire_date, p.category_name
                    FROM product p 
                    JOIN supplier_info s ON p.s_id = s.s_id 
                    WHERE LOWER(p.product_name) LIKE '%" . $db->escape($product_search) . "%'
                    ORDER BY p.quantity DESC
                ");
                while ($row = $db->fetch_assoc($query)) {
                    $data[] = $row;
                }
                
                if (!empty($data)) {
                    $response = "Found products matching '{$product_search}':";
                } else {
                    $response = "No products found matching '{$product_search}'.";
                }
            }
            
        // General greeting
        } elseif (strpos($message_lower, 'hi') !== false || strpos($message_lower, 'hello') !== false) {
            // Get inventory summary
            $products = $db->query("SELECT COUNT(*) as total FROM product");
            $categories = $db->query("SELECT COUNT(*) as total FROM categories");
            $suppliers = $db->query("SELECT COUNT(*) as total FROM supplier_info");
            $sales = $db->query("SELECT COUNT(*) as total FROM sales");
            
            $product_count = $db->fetch_assoc($products)['total'];
            $category_count = $db->fetch_assoc($categories)['total'];
            $supplier_count = $db->fetch_assoc($suppliers)['total'];
            $sales_count = $db->fetch_assoc($sales)['total'];
            
<<<<<<< HEAD
            $response = "Hello! Welcome to your Pharmacy Inventory System. Here's your current summary:\n";
=======
            $response = "Hello! Welcome to your HealStock Warehouse Inventory System. Here's your current summary:\n";
>>>>>>> 12fb767f0f1424802642c5a2161ca1500c832017
            $response .= "• Products: " . $product_count . "\n";
            $response .= "• Categories: " . $category_count . "\n";
            $response .= "• Suppliers: " . $supplier_count . "\n";
            $response .= "• Sales: " . $sales_count . "\n\n";
            $response .= "Try asking: 'How many antibiotics?', 'What are expired products?', 'Suppliers that supply Panadol'";
            
        } else {
<<<<<<< HEAD
            $response = "I can help you with pharmacy inventory queries like:\n";
=======
            $response = "I can help you with HealStock warehouse inventory queries like:\n";
>>>>>>> 12fb767f0f1424802642c5a2161ca1500c832017
            $response .= "• 'How many antibiotics?' - Get antibiotic stock\n";
            $response .= "• 'What are expired products?' - Check expired medicines\n";
            $response .= "• 'Suppliers that supply Panadol' - Find suppliers\n";
            $response .= "• 'Show me painkillers' - Category-wise products\n";
            $response .= "• 'Low stock products' - Restock alerts\n";
            $response .= "• 'Recent sales' - Sales information\n\n";
<<<<<<< HEAD
            $response .= "What would you like to know?";
=======
            $response .= "What would you like to know about HealStock warehouse?";
>>>>>>> 12fb767f0f1424802642c5a2161ca1500c832017
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
<<<<<<< HEAD
        'response' => 'Pharmacy Chatbot is working! Send me a message.',
=======
        'response' => 'HealStock Warehouse Chatbot is working! Send me a message.',
>>>>>>> 12fb767f0f1424802642c5a2161ca1500c832017
        'data' => []
    ]);
}
?>