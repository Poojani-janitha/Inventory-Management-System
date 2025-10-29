-- Sample data for purchase_order and sales tables
-- These match the existing product and supplier data

-- ============================================
-- PURCHASE ORDER SAMPLE DATA
-- ============================================
-- Based on products from supplier_product and product tables

INSERT INTO purchase_order (s_id, product_name, category_name, quantity, price, order_date, status) VALUES

-- Orders from Supplier s01
('s01', 'Amoxicillin 500mg', 'Antibiotic', 50, 45.00, '2025-09-01 10:00:00', 'accepted'),
('s01', 'Paracetamol 500mg', 'Painkiller', 100, 22.00, '2025-09-05 14:30:00', 'accepted'),
('s01', 'Ibuprofen 200mg', 'Painkiller', 75, 35.00, '2025-09-10 09:15:00', 'accepted'),
('s01', 'Vitamin C 1000mg', 'Vitamin', 60, 55.00, '2025-09-15 11:20:00', 'accepted'),
('s01', 'Azithromycin 250mg', 'Antibiotic', 40, 65.00, '2025-09-20 16:45:00', 'pending'),

-- Orders from Supplier s02
('s02', 'Cefuroxime 250mg', 'Antibiotic', 30, 80.00, '2025-09-03 10:30:00', 'accepted'),
('s02', 'Ciprofloxacin 500mg', 'Antibiotic', 25, 75.00, '2025-09-07 13:00:00', 'accepted'),
('s02', 'Multivitamin Tablet', 'Vitamin', 45, 95.00, '2025-09-12 15:30:00', 'accepted'),
('s02', 'Gaviscon 150ml', 'Antacid', 35, 125.00, '2025-09-18 09:45:00', 'accepted'),
('s02', 'Aspirin 100mg', 'Painkiller', 80, 28.00, '2025-09-22 12:00:00', 'pending'),

-- Orders from Supplier s03
('s03', 'Corex 100ml', 'Cough Syrup', 40, 110.00, '2025-09-04 14:20:00', 'accepted'),
('s03', 'Benadryl 100ml', 'Cough Syrup', 30, 115.00, '2025-09-08 10:15:00', 'accepted'),
('s03', 'Dettol 100ml', 'Antiseptic', 50, 70.00, '2025-09-14 16:00:00', 'accepted'),
('s03', 'Panadol Extra', 'Painkiller', 90, 32.00, '2025-09-19 11:30:00', 'accepted'),

-- Orders from Supplier s04
('s04', 'Vitamin D3 1000IU', 'Vitamin', 55, 70.00, '2025-09-06 09:00:00', 'accepted'),
('s04', 'Zinc Tablet 50mg', 'Vitamin', 65, 60.00, '2025-09-11 13:45:00', 'accepted'),
('s04', 'Eno Sachet', 'Antacid', 100, 25.00, '2025-09-16 15:00:00', 'accepted'),
('s04', 'Erythromycin 500mg', 'Antibiotic', 35, 85.00, '2025-09-21 10:20:00', 'pending'),

-- Orders from Supplier s05
('s05', 'Amoxicillin 500mg', 'Antibiotic', 45, 46.00, '2025-09-02 11:00:00', 'accepted'),
('s05', 'Ibuprofen 200mg', 'Painkiller', 70, 33.00, '2025-09-09 14:30:00', 'accepted'),
('s05', 'Diclofenac 50mg', 'Painkiller', 50, 40.00, '2025-09-13 09:15:00', 'accepted'),
('s05', 'Tixylix 100ml', 'Cough Syrup', 25, 118.00, '2025-09-17 16:00:00', 'accepted');


-- ============================================
-- SALES SAMPLE DATA
-- ============================================
-- Based on products from product table (using p_id as sale_product_id)
-- Using realistic invoice numbers, customer names, and quantities

INSERT INTO sales (sale_product_id, category_name, sale_selling_price, total, discount, name, pNumber, email, invoice_number, quantity, created_at) VALUES

-- Sales from product p001 (Amoxicillin 500mg - s01)
('p001', 'Antibiotic', 65.00, 65.00, 0.00, 'John Silva', '0771234567', 'john.silva@gmail.com', 'INV-2025-001', 1, '2025-10-01 10:15:00'),
('p001', 'Antibiotic', 65.00, 130.00, 0.00, 'Mary Perera', '0712345678', 'mary.p@gmail.com', 'INV-2025-002', 2, '2025-10-01 14:30:00'),
('p001', 'Antibiotic', 65.00, 260.00, 10.00, 'David Fernando', '0763456789', 'david.f@gmail.com', 'INV-2025-003', 4, '2025-10-02 09:20:00'),

-- Sales from product p002 (Azithromycin 250mg - s01)
('p002', 'Antibiotic', 85.00, 85.00, 0.00, 'Sarah Jayasuriya', '0724567890', 'sarah.j@gmail.com', 'INV-2025-004', 1, '2025-10-02 11:45:00'),
('p002', 'Antibiotic', 85.00, 170.00, 5.00, 'Peter Wickramasinghe', '0775678901', 'peter.w@gmail.com', 'INV-2025-005', 2, '2025-10-03 15:10:00'),

-- Sales from product p003 (Paracetamol 500mg - s01)
('p003', 'Painkiller', 35.00, 35.00, 0.00, 'Lisa Dissanayake', '0716789012', 'lisa.d@gmail.com', 'INV-2025-006', 1, '2025-10-03 10:00:00'),
('p003', 'Painkiller', 35.00, 70.00, 0.00, 'Michael Abeysekera', '0767890123', 'michael.a@gmail.com', 'INV-2025-007', 2, '2025-10-04 13:25:00'),
('p003', 'Painkiller', 35.00, 105.00, 0.00, 'Nimal Karunaratne', '0728901234', 'nimal.k@gmail.com', 'INV-2025-008', 3, '2025-10-05 09:45:00'),

-- Sales from product p004 (Ibuprofen 200mg - s01)
('p004', 'Painkiller', 50.00, 50.00, 0.00, 'Kamali Senanayake', '0779012345', 'kamali.s@gmail.com', 'INV-2025-009', 1, '2025-10-05 14:20:00'),
('p004', 'Painkiller', 50.00, 100.00, 0.00, 'Rohan Mendis', '0710123456', 'rohan.m@gmail.com', 'INV-2025-010', 2, '2025-10-06 11:30:00'),

-- Sales from product p005 (Vitamin C 1000mg - s01)
('p005', 'Vitamin', 75.00, 75.00, 0.00, 'Chaminda Ratnayake', '0761234567', 'chaminda.r@gmail.com', 'INV-2025-011', 1, '2025-10-06 16:00:00'),
('p005', 'Vitamin', 75.00, 150.00, 15.00, 'Priyanka Gunawardena', '0722345678', 'priyanka.g@gmail.com', 'INV-2025-012', 2, '2025-10-07 10:15:00'),

-- Sales from product p006 (Cefuroxime 250mg - s02)
('p006', 'Antibiotic', 105.00, 105.00, 0.00, 'Anura Bandara', '0773456789', 'anura.b@gmail.com', 'INV-2025-013', 1, '2025-10-07 13:40:00'),
('p006', 'Antibiotic', 105.00, 210.00, 10.00, 'Sanduni Jayawardena', '0714567890', 'sanduni.j@gmail.com', 'INV-2025-014', 2, '2025-10-08 09:25:00'),

-- Sales from product p007 (Ciprofloxacin 500mg - s02)
('p007', 'Antibiotic', 98.00, 98.00, 0.00, 'Tharindu Perera', '0765678901', 'tharindu.p@gmail.com', 'INV-2025-015', 1, '2025-10-08 15:10:00'),

-- Sales from product p008 (Aspirin 100mg - s02)
('p008', 'Painkiller', 42.00, 42.00, 0.00, 'Dilani Amarasinghe', '0726789012', 'dilani.a@gmail.com', 'INV-2025-016', 1, '2025-10-09 11:00:00'),
('p008', 'Painkiller', 42.00, 84.00, 0.00, 'Supun Weerakoon', '0777890123', 'supun.w@gmail.com', 'INV-2025-017', 2, '2025-10-09 14:30:00'),

-- Sales from product p009 (Multivitamin Tablet - s02)
('p009', 'Vitamin', 125.00, 125.00, 0.00, 'Lakmal Fernando', '0718901234', 'lakmal.f@gmail.com', 'INV-2025-018', 1, '2025-10-10 10:45:00'),
('p009', 'Vitamin', 125.00, 250.00, 20.00, 'Kavindi Jayaweera', '0769012345', 'kavindi.j@gmail.com', 'INV-2025-019', 2, '2025-10-10 16:20:00'),

-- Sales from product p010 (Gaviscon 150ml - s02)
('p010', 'Antacid', 165.00, 165.00, 0.00, 'Ramesh Silva', '0720123456', 'ramesh.s@gmail.com', 'INV-2025-020', 1, '2025-10-11 09:30:00'),

-- Sales from product p011 (Benadryl 100ml - s03)
('p011', 'Cough Syrup', 150.00, 150.00, 0.00, 'Nirosha Perera', '0771234567', 'nirosha.p@gmail.com', 'INV-2025-021', 1, '2025-10-11 13:15:00'),
('p011', 'Cough Syrup', 150.00, 300.00, 15.00, 'Amal Gunathilaka', '0712345678', 'amal.g@gmail.com', 'INV-2025-022', 2, '2025-10-12 10:00:00'),

-- Sales from product p012 (Corex 100ml - s03)
('p012', 'Cough Syrup', 145.00, 145.00, 0.00, 'Chathuri Ranasinghe', '0763456789', 'chathuri.r@gmail.com', 'INV-2025-023', 1, '2025-10-12 15:40:00'),
('p012', 'Cough Syrup', 145.00, 145.00, 0.00, 'Kasun Bandara', '0724567890', 'kasun.b@gmail.com', 'INV-2025-024', 1, '2025-10-13 11:25:00'),

-- Sales from product p013 (Dettol 100ml - s03)
('p013', 'Antiseptic', 95.00, 95.00, 0.00, 'Sithara Wickramasuriya', '0775678901', 'sithara.w@gmail.com', 'INV-2025-025', 1, '2025-10-13 14:00:00'),
('p013', 'Antiseptic', 95.00, 190.00, 0.00, 'Dhanuka Karunaratne', '0716789012', 'dhanuka.k@gmail.com', 'INV-2025-026', 2, '2025-10-14 09:50:00'),

-- More recent sales for October
('p001', 'Antibiotic', 65.00, 195.00, 0.00, 'Roshan Mendis', '0767890123', 'roshan.m@gmail.com', 'INV-2025-027', 3, '2025-10-15 10:30:00'),
('p003', 'Painkiller', 35.00, 140.00, 5.00, 'Thushari Jayawardena', '0728901234', 'thushari.j@gmail.com', 'INV-2025-028', 4, '2025-10-15 14:15:00'),
('p004', 'Painkiller', 50.00, 150.00, 0.00, 'Pradeep Rathnayake', '0779012345', 'pradeep.r@gmail.com', 'INV-2025-029', 3, '2025-10-16 11:00:00'),
('p005', 'Vitamin', 75.00, 225.00, 10.00, 'Iresha Gunasekara', '0710123456', 'iresha.g@gmail.com', 'INV-2025-030', 3, '2025-10-16 15:45:00'),
('p006', 'Antibiotic', 105.00, 315.00, 15.00, 'Nishan Fernando', '0761234567', 'nishan.f@gmail.com', 'INV-2025-031', 3, '2025-10-17 10:20:00'),
('p008', 'Painkiller', 42.00, 126.00, 6.00, 'Ruvini Perera', '0722345678', 'ruvini.p@gmail.com', 'INV-2025-032', 3, '2025-10-17 13:30:00'),
('p012', 'Cough Syrup', 145.00, 290.00, 0.00, 'Ashan Weerakoon', '0773456789', 'ashan.w@gmail.com', 'INV-2025-033', 2, '2025-10-18 09:15:00'),
('p013', 'Antiseptic', 95.00, 285.00, 15.00, 'Chamari Jayaweera', '0714567890', 'chamari.j@gmail.com', 'INV-2025-034', 3, '2025-10-18 14:50:00');

