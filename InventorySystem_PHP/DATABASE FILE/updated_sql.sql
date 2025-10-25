
CREATE TABLE IF NOT EXISTS categories (
c_id int(11) PRIMARY KEY NOT NULL,
 category_name varchar(100) NOT NULL
);
ALTER TABLE categories ADD UNIQUE (category_name);


INSERT INTO categories (c_id, category_name ) VALUES
(1, 'Antibiotic'),
(2, 'Painkiller'),
(3, 'Vitamin'),
(4, 'Antacid'),
(5, 'Cough Syrup'),
(6, 'Antiseptic'),
(7, 'Antihistamine'),
(8, 'Antidiabetic'),
(9, 'Antihypertensive'),
(10, 'Cholesterol'),
(11, 'Respiratory'),
(12, 'Steroid'),
(13, 'Supplement'),
(14, 'Antidiarrheal'),
(15, 'Antiparasitic'),
(16, 'Antifungal'),
(17, 'Topical Steroid');


CREATE TABLE supplier_info (
    s_id VARCHAR(10) PRIMARY KEY,
    s_name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    contact_number VARCHAR(15),
    email VARCHAR(200),
    CHECK (s_id LIKE 's%')
);




INSERT INTO supplier_info (s_id, s_name, address, contact_number, email) VALUES
('s01', 'Anura Wickramasinghe', 'Colombo 05, Sri Lanka', '0771234567', 'anura.wick@gmail.com'),
('s02', 'Nimal Perera', 'Kandy, Sri Lanka', '0712345678', 'nimalp@gmail.com'),
('s03', 'Kumari Weerasinghe', 'Galle, Sri Lanka', '0756543210', 'kumariw@hotmail.com'),
('s04', 'Ranjith Jayasinghe', 'Matara, Sri Lanka', '0769876543', 'ranjithj@yahoo.com'),
('s05', 'Shani Wijesinghe', 'Nuwara Eliya, Sri Lanka', '0725554321', 'shani.vw@gmail.com'),
('s06', 'Lakshan Senevirathna', 'Anuradhapura, Sri Lanka', '0782345567', 'lakshan.s@gmail.com'),
('s07', 'Aruna Dissanayake', 'Kandy, Sri Lanka', '0701122334', 'aruna.d@gmail.com'),
('s08', 'Champika Kumarasinghe', 'Galle, Sri Lanka', '0719988776', 'champika.k@gmail.com'),
('s09', 'Niluka Perera', 'Kurunegala, Sri Lanka', '0753344556', 'niluka.p@gmail.com'),
('s10', 'Saman Ratnapriya', 'Negombo, Sri Lanka', '0726677889', 'saman.rp@gmail.com'),
('s11', 'Thilina Jayawardena', 'Colombo 03, Sri Lanka', '0779988776', 'thilina.j@gmail.com'),
('s12', 'Ruwan Abeykoon', 'Kandy, Sri Lanka', '0714569872', 'ruwana@gmail.com'),
('s13', 'Sanduni Rajapaksha', 'Matale, Sri Lanka', '0751122443', 'sanduni.r@gmail.com'),
('s14', 'Chathura Silva', 'Colombo 10, Sri Lanka', '0763344556', 'chathura.s@gmail.com'),
('s15', 'Gayani Fernando', 'Panadura, Sri Lanka', '0724433221', 'gayani.f@gmail.com'),
('s16', 'Kasun Liyanage', 'Kalutara, Sri Lanka', '0772211445', 'kasun.l@gmail.com'),
('s17', 'Isuru Bandara', 'Kurunegala, Sri Lanka', '0717788990', 'isurub@gmail.com'),
('s18', 'Nadeesha Dissanayake', 'Colombo 07, Sri Lanka', '0786655443', 'nadeesha.d@gmail.com'),
('s19', 'Chatura Perera', 'Ratnapura, Sri Lanka', '0759988775', 'chatura.p@gmail.com'),
('s20', 'Pradeep Senanayake', 'Gampaha, Sri Lanka', '0768877665', 'pradeep.s@gmail.com'),
('s21', 'Madhavi Weerasekara', 'Kandy, Sri Lanka', '0729988774', 'madhavi.w@gmail.com'),
('s22', 'Asela Samarasinghe', 'Matara, Sri Lanka', '0776655443', 'asela.s@gmail.com'),
('s23', 'Suresh Rathnayake', 'Colombo 02, Sri Lanka', '0715544332', 'suresh.r@gmail.com'),
('s24', 'Rashmi Perera', 'Nugegoda, Sri Lanka', '0753322110', 'rashmi.p@gmail.com'),
('s25', 'Sithara Jayawardana', 'Negombo, Sri Lanka', '0784433221', 'sithara.j@gmail.com'),
('s26', 'Janith Wickramaratne', 'Kandy, Sri Lanka', '0766677889', 'janith.w@gmail.com'),
('s27', 'Dinusha Abeywardena', 'Colombo 08, Sri Lanka', '0718899776', 'dinusha.a@gmail.com'),
('s28', 'Pubudu Silva', 'Kalutara, Sri Lanka', '0757766554', 'pubudu.s@gmail.com'),
('s29', 'Nirosha Wijeratne', 'Galle, Sri Lanka', '0789988776', 'nirosha.w@gmail.com'),
('s30', 'Ruwanthi Fernando', 'Panadura, Sri Lanka', '0775544331', 'ruwanthi.f@gmail.com'),
('s31', 'Harsha Karunaratne', 'Colombo 04, Sri Lanka', '0726677885', 'harsha.k@gmail.com'),
('s32', 'Dinuka Samarasinghe', 'Kegalle, Sri Lanka', '0769988774', 'dinuka.s@gmail.com'),
('s33', 'Tharindu Perera', 'Kurunegala, Sri Lanka', '0783344552', 'tharindu.p@gmail.com'),
('s34', 'Manori Jayasekara', 'Gampaha, Sri Lanka', '0712233445', 'manori.j@gmail.com'),
('s35', 'Pasindu Wickramanayake', 'Kandy, Sri Lanka', '0774455667', 'pasindu.w@gmail.com'),
('s36', 'Roshani Abeysinghe', 'Matara, Sri Lanka', '0725544331', 'roshani.a@gmail.com'),
('s37', 'Chandana Bandara', 'Kurunegala, Sri Lanka', '0758899776', 'chandana.b@gmail.com'),
('s38', 'Amali Senavirathna', 'Colombo 09, Sri Lanka', '0716655443', 'amali.s@gmail.com'),
('s39', 'Roshan Jayasuriya', 'Negombo, Sri Lanka', '0787788990', 'roshan.j@gmail.com'),
('s40', 'Dilusha Perera', 'Kandy, Sri Lanka', '0764433221', 'dilusha.p@gmail.com'),
('s41', 'Sujith Kumara', 'Galle, Sri Lanka', '0776677889', 'sujith.k@gmail.com'),
('s42', 'Chathurika Rajapaksha', 'Matale, Sri Lanka', '0759988772', 'chathurika.r@gmail.com'),
('s43', 'Rasika Ranasinghe', 'Colombo 06, Sri Lanka', '0715544330', 'rasika.r@gmail.com'),
('s44', 'Nimesha Dissanayake', 'Kandy, Sri Lanka', '0786655441', 'nimesha.d@gmail.com'),
('s45', 'Pramod Weerakoon', 'Gampaha, Sri Lanka', '0729988773', 'pramod.w@gmail.com'),
('s46', 'Ruwangi Silva', 'Negombo, Sri Lanka', '0768877661', 'ruwangi.s@gmail.com'),
('s47', 'Supun Karunathilaka', 'Matara, Sri Lanka', '0775544335', 'supun.k@gmail.com'),
('s48', 'Gayasha Perera', 'Kalutara, Sri Lanka', '0752233441', 'gayasha.p@gmail.com'),
('s49', 'Chandima Jayasinghe', 'Kandy, Sri Lanka', '0717788992', 'chandima.j@gmail.com'),
('s50', 'Manjula Rathnayake', 'Colombo 01, Sri Lanka', '0786655449', 'manjula.r@gmail.com');




CREATE TABLE supplier_product (
    s_id VARCHAR(10),
    category_name VARCHAR(100) NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (s_id, product_name),
    FOREIGN KEY (category_name) REFERENCES categories(category_name)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (s_id) REFERENCES supplier_info(s_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);



INSERT INTO supplier_product (s_id, category_name, product_name, price) VALUES

('s01', 'Antibiotic', 'Amoxicillin 500mg', 45.00),
('s01', 'Antibiotic', 'Azithromycin 250mg', 65.00),
('s01', 'Painkiller', 'Paracetamol 500mg', 22.00),
('s01', 'Painkiller', 'Ibuprofen 200mg', 35.00),
('s01', 'Vitamin', 'Vitamin C 1000mg', 55.00),


('s02', 'Antibiotic', 'Cefuroxime 250mg', 80.00),
('s02', 'Antibiotic', 'Ciprofloxacin 500mg', 75.00),
('s02', 'Painkiller', 'Aspirin 100mg', 28.00),
('s02', 'Vitamin', 'Multivitamin Tablet', 95.00),
('s02', 'Antacid', 'Gaviscon 150ml', 125.00),


('s03', 'Cough Syrup', 'Benadryl 100ml', 115.00),
('s03', 'Cough Syrup', 'Corex 100ml', 110.00),
('s03', 'Antiseptic', 'Dettol 100ml', 70.00),
('s03', 'Antiseptic', 'Savlon 200ml', 90.00),
('s03', 'Painkiller', 'Panadol Extra', 32.00),


('s04', 'Vitamin', 'Vitamin D3 1000IU', 70.00),
('s04', 'Vitamin', 'Zinc Tablet 50mg', 60.00),
('s04', 'Antacid', 'Eno Sachet', 25.00),
('s04', 'Antacid', 'Digene Tablet', 40.00),
('s04', 'Antibiotic', 'Erythromycin 500mg', 85.00),


('s05', 'Antibiotic', 'Amoxicillin 500mg', 46.00),
('s05', 'Painkiller', 'Ibuprofen 200mg', 33.00),
('s05', 'Painkiller', 'Diclofenac 50mg', 40.00),
('s05', 'Vitamin', 'Multivitamin Tablet', 90.00),
('s05', 'Cough Syrup', 'Tixylix 100ml', 118.00),


('s06', 'Antibiotic', 'Azithromycin 500mg', 70.00),
('s06', 'Antiseptic', 'Dettol 200ml', 130.00),
('s06', 'Antacid', 'Gaviscon 150ml', 120.00),
('s06', 'Painkiller', 'Aspirin 100mg', 30.00),
('s06', 'Vitamin', 'Vitamin C 1000mg', 54.00),


('s07', 'Antibiotic', 'Ciprofloxacin 500mg', 74.00),
('s07', 'Painkiller', 'Paracetamol 500mg', 21.50),
('s07', 'Painkiller', 'Ibuprofen 400mg', 45.00),
('s07', 'Antacid', 'Digene Tablet', 38.00),
('s07', 'Vitamin', 'Vitamin D3 1000IU', 68.00),

('s08', 'Cough Syrup', 'Benadryl 100ml', 117.00),
('s08', 'Cough Syrup', 'Corex 100ml', 108.00),
('s08', 'Antiseptic', 'Savlon 200ml', 89.00),
('s08', 'Painkiller', 'Panadol Extra', 30.00),
('s08', 'Vitamin', 'Zinc Tablet 50mg', 58.00),


('s09', 'Antibiotic', 'Cefuroxime 500mg', 88.00),
('s09', 'Antibiotic', 'Erythromycin 500mg', 84.00),
('s09', 'Antacid', 'Eno Sachet', 24.50),
('s09', 'Painkiller', 'Diclofenac 50mg', 39.00),
('s09', 'Vitamin', 'Multivitamin Tablet', 92.00),


('s10', 'Antibiotic', 'Amoxicillin 500mg', 47.00),
('s10', 'Antibiotic', 'Cefuroxime 250mg', 82.00),
('s10', 'Painkiller', 'Aspirin 100mg', 29.00),
('s10', 'Antiseptic', 'Dettol 100ml', 72.00),
('s10', 'Vitamin', 'Vitamin C 1000mg', 56.00),

('s11', 'Painkiller', 'Paracetamol 500mg', 23.00),
('s11', 'Painkiller', 'Ibuprofen 400mg', 42.00),
('s11', 'Antacid', 'Eno Sachet', 26.00),
('s11', 'Antibiotic', 'Ciprofloxacin 250mg', 69.00),
('s11', 'Vitamin', 'Vitamin D3 1000IU', 62.00),


('s12', 'Antibiotic', 'Cefixime 200mg', 75.00),
('s12', 'Painkiller', 'Panadol Extra', 34.00),
('s12', 'Antacid', 'Digene Tablet', 41.00),
('s12', 'Cough Syrup', 'Corex 100ml', 109.00),
('s12', 'Vitamin', 'Multivitamin Tablet', 91.00),


('s13', 'Antibiotic', 'Amoxicillin 250mg', 43.00),
('s13', 'Painkiller', 'Diclofenac 50mg', 37.00),
('s13', 'Vitamin', 'Zinc Tablet 50mg', 59.00),
('s13', 'Antacid', 'Gaviscon 150ml', 124.00),
('s13', 'Cough Syrup', 'Benadryl 100ml', 116.00),


('s14', 'Antibiotic', 'Erythromycin 250mg', 68.00),
('s14', 'Painkiller', 'Aspirin 75mg', 26.00),
('s14', 'Antacid', 'Eno Sachet', 23.50),
('s14', 'Vitamin', 'Vitamin C 1000mg', 57.00),
('s14', 'Antiseptic', 'Savlon 200ml', 91.00),


('s15', 'Cough Syrup', 'Tixylix 100ml', 119.00),
('s15', 'Cough Syrup', 'Benadryl 100ml', 112.00),
('s15', 'Painkiller', 'Paracetamol 500mg', 22.50),
('s15', 'Antacid', 'Digene Tablet', 42.00),
('s15', 'Vitamin', 'Multivitamin Tablet', 88.00),


('s16', 'Antibiotic', 'Azithromycin 500mg', 72.00),
('s16', 'Painkiller', 'Ibuprofen 400mg', 46.00),
('s16', 'Antacid', 'Gaviscon 200ml', 122.00),
('s16', 'Cough Syrup', 'Corex 100ml', 111.00),
('s16', 'Vitamin', 'Vitamin D3 1000IU', 69.00),


('s17', 'Antibiotic', 'Cefuroxime 500mg', 87.00),
('s17', 'Painkiller', 'Panadol Extra', 33.00),
('s17', 'Vitamin', 'Zinc Tablet 50mg', 61.00),
('s17', 'Antacid', 'Eno Sachet', 25.00),
('s17', 'Cough Syrup', 'Tixylix 100ml', 120.00),


('s18', 'Antibiotic', 'Ciprofloxacin 500mg', 77.00),
('s18', 'Painkiller', 'Ibuprofen 200mg', 36.00),
('s18', 'Antacid', 'Digene Tablet', 39.50),
('s18', 'Antiseptic', 'Dettol 100ml', 74.00),
('s18', 'Vitamin', 'Vitamin C 1000mg', 53.00),


('s19', 'Antibiotic', 'Amoxicillin 500mg', 45.50),
('s19', 'Painkiller', 'Aspirin 75mg', 27.00),
('s19', 'Antacid', 'Eno Sachet', 24.00),
('s19', 'Cough Syrup', 'Benadryl 100ml', 113.00),
('s19', 'Vitamin', 'Multivitamin Tablet', 89.00),


('s20', 'Antibiotic', 'Erythromycin 500mg', 86.00),
('s20', 'Antibiotic', 'Cefuroxime 250mg', 81.00),
('s20', 'Painkiller', 'Ibuprofen 400mg', 44.00),
('s20', 'Antacid', 'Gaviscon 150ml', 121.00),
('s20', 'Vitamin', 'Vitamin D3 1000IU', 71.00),


('s21', 'Antibiotic', 'Azithromycin 250mg', 64.00),
('s21', 'Painkiller', 'Paracetamol 500mg', 21.00),
('s21', 'Painkiller', 'Panadol Extra', 31.00),
('s21', 'Antacid', 'Eno Sachet', 25.50),
('s21', 'Vitamin', 'Zinc Tablet 50mg', 60.00),


('s22', 'Cough Syrup', 'Corex 100ml', 112.00),
('s22', 'Cough Syrup', 'Tixylix 100ml', 120.00),
('s22', 'Painkiller', 'Ibuprofen 200mg', 34.00),
('s22', 'Antiseptic', 'Dettol 100ml', 71.00),
('s22', 'Vitamin', 'Vitamin C 1000mg', 52.00);



CREATE TABLE product (
    p_id VARCHAR(10) PRIMARY KEY,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    buying_price DECIMAL(10,2) NOT NULL COMMENT 'Price in LKR (Rs)',
    selling_price DECIMAL(10,2) NOT NULL COMMENT 'Price in LKR (Rs)',
    category_name VARCHAR(100) NOT NULL,
    s_id VARCHAR(10) NOT NULL,  
    expire_date DATE,
    recorded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (p_id LIKE 'p%'),
    CHECK (quantity >= 0),
    CHECK (selling_price >= buying_price),
    FOREIGN KEY (category_name) REFERENCES categories(category_name)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (s_id) REFERENCES supplier_info(s_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT 
);
INSERT INTO product (p_id, product_name, quantity, buying_price, selling_price, category_name, s_id, expire_date) VALUES

('p001', 'Amoxicillin 500mg', 120, 45.00, 65.00, 'Antibiotic', 's01', '2026-08-15'),
('p002', 'Azithromycin 250mg', 90, 65.00, 85.00, 'Antibiotic', 's01', '2026-09-20'),
('p003', 'Paracetamol 500mg', 180, 22.00, 35.00, 'Painkiller', 's01', '2027-02-18'),
('p004', 'Ibuprofen 200mg', 150, 35.00, 50.00, 'Painkiller', 's01', '2027-04-12'),
('p005', 'Vitamin C 1000mg', 100, 55.00, 75.00, 'Vitamin', 's01', '2027-01-30'),


('p006', 'Cefuroxime 250mg', 70, 80.00, 105.00, 'Antibiotic', 's02', '2026-07-10'),
('p007', 'Ciprofloxacin 500mg', 85, 75.00, 98.00, 'Antibiotic', 's02', '2026-11-05'),
('p008', 'Aspirin 100mg', 160, 28.00, 42.00, 'Painkiller', 's02', '2027-03-15'),
('p009', 'Multivitamin Tablet', 90, 95.00, 125.00, 'Vitamin', 's02', '2027-11-20'),
('p010', 'Gaviscon 150ml', 75, 125.00, 165.00, 'Antacid', 's02', '2026-12-15'),


('p011', 'Benadryl 100ml', 60, 115.00, 150.00, 'Cough Syrup', 's03', '2026-09-15'),
('p012', 'Corex 100ml', 55, 110.00, 145.00, 'Cough Syrup', 's03', '2026-11-08'),
('p013', 'Dettol 100ml', 100, 70.00, 95.00, 'Antiseptic', 's03', '2027-06-20'),
('p014', 'Savlon 200ml', 85, 90.00, 120.00, 'Antiseptic', 's03', '2027-07-15'),
('p015', 'Panadol Extra', 140, 32.00, 45.00, 'Painkiller', 's03', '2026-11-25'),


('p016', 'Vitamin D3 1000IU', 80, 70.00, 95.00, 'Vitamin', 's04', '2028-01-15'),
('p017', 'Zinc Tablet 50mg', 100, 60.00, 82.00, 'Vitamin', 's04', '2027-10-30'),
('p018', 'Eno Sachet', 150, 25.00, 35.00, 'Antacid', 's04', '2027-08-20'),
('p019', 'Digene Tablet', 90, 40.00, 58.00, 'Antacid', 's04', '2027-09-10'),
('p020', 'Erythromycin 500mg', 70, 85.00, 110.00, 'Antibiotic', 's04', '2026-11-22'),


('p021', 'Amoxicillin 500mg', 100, 46.00, 66.00, 'Antibiotic', 's05', '2026-08-15'),
('p022', 'Ibuprofen 200mg', 130, 33.00, 48.00, 'Painkiller', 's05', '2027-04-20'),
('p023', 'Diclofenac 50mg', 90, 40.00, 58.00, 'Painkiller', 's05', '2027-03-25'),
('p024', 'Multivitamin Tablet', 100, 90.00, 120.00, 'Vitamin', 's05', '2027-11-20'),
('p025', 'Tixylix 100ml', 50, 118.00, 155.00, 'Cough Syrup', 's05', '2026-09-30'),


('p026', 'Azithromycin 500mg', 80, 70.00, 92.00, 'Antibiotic', 's06', '2025-10-03'),
('p027', 'Dettol 200ml', 70, 130.00, 170.00, 'Antiseptic', 's06', '2027-05-25'),
('p028', 'Gaviscon 150ml', 60, 120.00, 160.00, 'Antacid', 's06', '2026-12-15'),
('p029', 'Aspirin 100mg', 120, 30.00, 45.00, 'Painkiller', 's06', '2026-12-01'),
('p030', 'Vitamin C 1000mg', 90, 54.00, 75.00, 'Vitamin', 's06', '2027-01-30'),


('p031', 'Ciprofloxacin 500mg', 75, 74.00, 95.00, 'Antibiotic', 's07', '2026-11-05'),
('p032', 'Paracetamol 500mg', 150, 21.50, 35.00, 'Painkiller', 's07', '2027-03-15'),
('p033', 'Ibuprofen 400mg', 100, 45.00, 65.00, 'Painkiller', 's07', '2027-06-18'),
('p034', 'Digene Tablet', 80, 38.00, 55.00, 'Antacid', 's07', '2027-09-10'),
('p035', 'Vitamin D3 1000IU', 75, 68.00, 90.00, 'Vitamin', 's07', '2028-01-15'),


('p036', 'Benadryl 100ml', 55, 117.00, 155.00, 'Cough Syrup', 's08', '2026-10-15'),
('p037', 'Corex 100ml', 60, 108.00, 148.00, 'Cough Syrup', 's08', '2026-11-08'),
('p038', 'Savlon 200ml', 85, 89.00, 120.00, 'Antiseptic', 's08', '2027-07-15'),
('p039', 'Panadol Extra', 130, 30.00, 45.00, 'Painkiller', 's08', '2026-11-25'),
('p040', 'Zinc Tablet 50mg', 90, 58.00, 80.00, 'Vitamin', 's08', '2027-10-30'),


('p041', 'Cefuroxime 500mg', 60, 88.00, 115.00, 'Antibiotic', 's09', '2026-08-25'),
('p042', 'Erythromycin 500mg', 70, 84.00, 110.00, 'Antibiotic', 's09', '2025-11-22'),
('p043', 'Eno Sachet', 200, 24.50, 35.00, 'Antacid', 's09', '2027-08-20'),
('p044', 'Diclofenac 50mg', 90, 39.00, 58.00, 'Painkiller', 's09', '2027-03-25'),
('p045', 'Multivitamin Tablet', 95, 92.00, 120.00, 'Vitamin', 's09', '2027-11-20'),


('p046', 'Amoxicillin 500mg', 120, 47.00, 68.00, 'Antibiotic', 's10', '2026-08-15'),
('p047', 'Cefuroxime 250mg', 70, 82.00, 105.00, 'Antibiotic', 's10', '2026-07-10'),
('p048', 'Aspirin 100mg', 140, 29.00, 42.00, 'Painkiller', 's10', '2027-03-15'),
('p049', 'Dettol 100ml', 100, 72.00, 95.00, 'Antiseptic', 's10', '2027-06-20'),
('p050', 'Vitamin C 1000mg', 95, 56.00, 75.00, 'Vitamin', 's10', '2027-01-30');

CREATE TABLE return_details (
    return_id INT AUTO_INCREMENT PRIMARY KEY,
    p_id VARCHAR(10) NOT NULL,
    s_id VARCHAR(10) NOT NULL,
    product_name VARCHAR(150) NOT NULL,      
    buying_price DECIMAL(10,2) NOT NULL,     
    return_quantity INT NOT NULL,
    return_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (p_id) REFERENCES product(p_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (s_id) REFERENCES supplier_info(s_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CHECK (return_quantity > 0),
    CHECK (buying_price >= 0)
);


INSERT INTO return_details (p_id, s_id, product_name, buying_price, return_quantity, return_date) VALUES

('p001', 's01', 'Amoxicillin 500mg', 45.00, 10, '2025-10-30 10:15:00'),

('p003', 's01', 'Paracetamol 500mg', 22.00, 12, '2025-11-01 09:30:00'),

('p021', 's05', 'Amoxicillin 500mg', 46.00, 5, '2025-10-28 14:45:00'),

('p030', 's06', 'Vitamin C 1000mg', 54.00, 8, '2025-10-30 11:00:00'),

('p049', 's10', 'Dettol 100ml', 72.00, 20, '2025-11-3 16:20:00');



CREATE TABLE purchase_order (
    o_id INT AUTO_INCREMENT PRIMARY KEY,
    s_id VARCHAR(10) NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL,


    FOREIGN KEY (s_id) REFERENCES supplier_info(s_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,


    FOREIGN KEY (category_name) REFERENCES categories(category_name)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);




CREATE TABLE IF NOT EXISTS users (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(60) NOT NULL,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  user_level int(11) NOT NULL,
  image varchar(255) DEFAULT 'no_image.jpg',
  status int(1) NOT NULL,
  last_login datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);





INSERT INTO users (id, name, username, password, user_level, image, status, last_login) VALUES
(1, 'Kavindu Perera', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'no_image.png', 1, '2025-04-04 19:45:52'),
(2, 'Nimesh Lakshan', 'staff_1', 'ba36b97a41e7faf742ab09bf88405ac04f99599a', 2, 'no_image.png', 1, '2025-04-04 19:53:26'),
(3, 'Sanduni Madushani', 'staff_2', '12dea96fec20593566ab75692c9949596833adc9', 2, 'no_image.png', 1, '2025-04-04 19:54:46'),
(4, 'Tharushi Senadheera', 'salesman', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 3, 'no_image.png', 1, '2025-04-04 19:54:46');



CREATE TABLE IF NOT EXISTS user_groups (
id int(11) NOT NULL,
  group_name varchar(150) NOT NULL,
  group_level int(11) NOT NULL,
  group_status int(1) NOT NULL
);



INSERT INTO user_groups (id, group_name, group_level, group_status) VALUES
(1, 'Admin', 1, 1),
(2, 'Staff', 2, 1),
(3, 'finance_DEP', 3, 1);

-- Sales table for recording sales transactions

CREATE TABLE sales (
    sales_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_product_id VARCHAR(10) NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    sale_selling_price DECIMAL(10,2),
    total DECIMAL(10,2),
    discount DECIMAL(10,2),
    name VARCHAR(100),
    pNumber VARCHAR(10),
    email VARCHAR(100),
    invoice_number VARCHAR(20) NOT NULL,
    quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

   
    FOREIGN KEY (sale_product_id) REFERENCES product(p_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    FOREIGN KEY (category_name) REFERENCES categories(category_name)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

   
    INDEX idx_invoice_number (invoice_number)
);

