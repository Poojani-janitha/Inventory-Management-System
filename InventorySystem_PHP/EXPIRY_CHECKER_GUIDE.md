# Auto Expiry Checker - Complete Guide 📊

## Overview
The Auto Expiry Checker is a comprehensive tool that monitors product expiration dates, displays detailed analysis with charts, and helps manage expired or expiring inventory.

---

## 🎯 Features

### 1. **Smart Product Analysis**
- ✅ Identifies expired products
- ✅ Finds products expiring in 7 days (critical warning)
- ✅ Finds products expiring in 30 days (plan ahead)
- ✅ Finds products expiring in 90 days (monitor)

### 2. **Visual Statistics Dashboard**
Four beautiful statistic cards showing:
- 🔴 **Expired Products** - Red danger card
- 🟡 **Expiring in 7 Days** - Orange warning card
- 🔵 **Expiring in 30 Days** - Blue info card
- 🟢 **Expiring in 90 Days** - Green success card

### 3. **Interactive Charts** 📈
Three professional charts using Chart.js:

#### **Chart 1: Expiry Status Distribution (Doughnut Chart)**
- Visual breakdown of all products by expiry status
- Color-coded segments:
  - Red: Expired
  - Orange: 7 days
  - Blue: 30 days
  - Green: 90 days
- Interactive tooltips showing exact counts

#### **Chart 2: Products by Category (Bar Chart)**
- Shows which categories have expiring products
- Helps identify problem categories
- Multi-colored bars for visual appeal
- Vertical bar chart with counts

#### **Chart 3: Expiry Timeline (Line Chart)**
- Shows days until expiry for each product
- X-axis: Product names
- Y-axis: Days (negative = already expired)
- Color-coded points:
  - Red points: Expired (below 0 line)
  - Orange points: 0-7 days
  - Blue points: 8-30 days
  - Green points: 31-90 days
- Red horizontal line at y=0 (expiry threshold)
- Smooth curved line connecting all points
- Interactive tooltips with exact days

### 4. **Detailed Data Tables** 📋

#### **Table 1: EXPIRED PRODUCTS** (Red Panel)
Shows all expired products with:
- Product details (name, ID, category)
- Quantity in stock
- Buying price per unit
- **Total value at risk**
- Expiry date
- **Days expired** (how long ago it expired)
- Supplier information (name, ID, phone)
- **"Create Return" button** - Direct link to add return

**Example Row:**
```
#1 | Amoxicillin 500mg | 10 units | Rs. 45.00 | Rs. 450.00 | 2025-10-15 | 13 days ago | Anura (s01) 077-xxx | [Create Return]
```

#### **Table 2: EXPIRING IN 7 DAYS** (Orange Panel)
Critical warning for products expiring this week:
- Same columns as expired table
- Shows **days remaining** (1-7 days)
- **"Plan Return" button** for quick action

#### **Table 3: EXPIRING IN 30 DAYS** (Blue Panel)
Products to monitor and plan for:
- Same product and supplier details
- Shows days remaining (8-30 days)
- No action button (informational)

### 5. **Financial Impact Summary** 💰
Displays total value at risk:
- **Expired value**: Total cost of expired inventory
- **7 days value**: Products expiring this week
- **30 days value**: Products expiring this month
- **Total at Risk**: Sum of all three

**Example:**
```
Total Expired Value:       Rs. 12,450.00
Value Expiring in 7 Days:  Rs. 5,230.00
Value Expiring in 30 Days: Rs. 8,790.00
───────────────────────────────────────
Total at Risk:            Rs. 26,470.00
```

### 6. **Recommended Actions** ⚡
Automatic suggestions based on findings:

**Immediate Actions (Red Alert):**
- Process returns for X expired products
- Contact suppliers for return authorization
- Remove expired stock from shelves

**This Week (Orange Alert):**
- Plan promotions for products expiring soon
- Arrange supplier returns if applicable

**This Month (Blue Alert):**
- Monitor products expiring within 30 days
- Adjust inventory ordering

---

## 📖 How to Use

### Step 1: Navigate to Expiry Checker
```
Sidebar Menu → Return Management → Expiry Checker
```

### Step 2: Run the Check
```
Click the "Run Expiry Check" button
```

### Step 3: View Results
After clicking, the page displays:
1. **Statistics Cards** at top
2. **Three Charts** in the middle
3. **Detailed Tables** below charts
4. **Summary Report** at bottom

### Step 4: Take Action
For expired products:
```
Click "Create Return" button → Redirects to add_return.php
```

---

## 🎨 Visual Layout

```
┌─────────────────────────────────────────────────────────┐
│  AUTO EXPIRY CHECKER                                    │
│  [Run Expiry Check Button]                              │
└─────────────────────────────────────────────────────────┘

┌──────────┬──────────┬──────────┬──────────┐
│ EXPIRED  │ 7 DAYS   │ 30 DAYS  │ 90 DAYS  │
│   12     │    8     │    15    │    25    │
│  (Red)   │ (Orange) │  (Blue)  │ (Green)  │
└──────────┴──────────┴──────────┴──────────┘

┌─────────────────────┬──────────────────────┐
│  STATUS CHART       │  CATEGORY CHART      │
│  (Doughnut)         │  (Bar)               │
│                     │                      │
│                     │                      │
└─────────────────────┴──────────────────────┘

┌──────────────────────────────────────────┐
│  TIMELINE CHART                          │
│  (Line Graph)                            │
│                                          │
│                                          │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  EXPIRED PRODUCTS TABLE (Red)            │
│  [Detailed rows with Create Return btn]  │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  EXPIRING IN 7 DAYS TABLE (Orange)       │
│  [Detailed rows with Plan Return btn]    │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  EXPIRING IN 30 DAYS TABLE (Blue)        │
│  [Detailed rows - informational]         │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│  SUMMARY REPORT                          │
│  Financial Impact | Recommended Actions  │
└──────────────────────────────────────────┘
```

---

## 📊 Chart Details

### Doughnut Chart (Expiry Status)
**Purpose**: Quick visual overview of expiry distribution

**Data Shown**:
- Expired: 12 products (Red segment)
- 7 Days: 8 products (Orange segment)
- 30 Days: 15 products (Blue segment)
- 90 Days: 25 products (Green segment)

**Interactivity**:
- Hover to see exact counts
- Click legend to show/hide segments
- Responsive design

### Bar Chart (Categories)
**Purpose**: Identify problem categories

**Data Shown**:
- Category names on X-axis
- Product count on Y-axis
- Different color for each category

**Example**:
```
Antibiotics:  8 products
Painkillers:  5 products
Vitamins:     3 products
Antacids:     2 products
```

### Line Chart (Timeline)
**Purpose**: See expiry pattern over time

**Data Shown**:
- Each point = one product
- Y-axis: Days until expiry
- Negative values = already expired
- Zero line = expiry threshold

**Color Coding**:
- Red points (below 0): Expired
- Orange points (0-7): Critical
- Blue points (8-30): Warning
- Green points (31-90): Monitor

**Example**:
```
Product A: -15 days (expired 15 days ago) [Red]
Product B: +3 days (expires in 3 days)    [Orange]
Product C: +20 days (expires in 20 days)  [Blue]
Product D: +60 days (expires in 60 days)  [Green]
```

---

## 🔢 Duration Calculation

### How Days are Calculated:
```php
Today: 2025-10-28
Expiry Date: 2025-11-05

Days Until Expiry = (Expiry - Today) / 86400
                  = 8 days (still good)
```

### Negative Days (Expired):
```php
Today: 2025-10-28
Expiry Date: 2025-10-15

Days Until Expiry = (Expiry - Today) / 86400
                  = -13 days
Display: "13 days ago" (expired)
```

### Display Format:
- **Positive**: "15 days" (future expiry)
- **Negative**: "15 days ago" (already expired)
- **Zero**: "Expires today" (same day)

---

## 💡 Understanding the Data

### What Each Duration Means:

#### **Expired (Negative Days)**
```
Status: CRITICAL ⛔
Color: Red
Action: Immediate return/disposal
Display: "15 days ago"
Meaning: Product expired 15 days in the past
```

#### **0-7 Days**
```
Status: CRITICAL WARNING ⚠️
Color: Orange
Action: Plan return/promotion this week
Display: "5 days"
Meaning: Product expires in 5 days
```

#### **8-30 Days**
```
Status: WARNING ℹ️
Color: Blue
Action: Monitor and plan
Display: "20 days"
Meaning: Product expires in 20 days
```

#### **31-90 Days**
```
Status: MONITOR ✅
Color: Green
Action: Regular monitoring
Display: "60 days"
Meaning: Product expires in 60 days
```

---

## 📈 Real-World Example

### Scenario: Pharmacy Running Expiry Check

**Click "Run Expiry Check"**

**Results Displayed:**

#### Statistics Cards:
```
┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│  EXPIRED     │  │  7 DAYS      │  │  30 DAYS     │  │  90 DAYS     │
│     5        │  │     3        │  │     12       │  │     20       │
│  Immediate   │  │  Critical    │  │  Plan Ahead  │  │  Monitor     │
└──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘
```

#### Charts Show:
1. **Doughnut**: 5 expired, 3 critical, 12 warning, 20 monitoring
2. **Bar**: Antibiotics (10), Painkillers (8), Vitamins (5)...
3. **Line**: Timeline from -20 days to +90 days

#### Expired Products Table:
```
# | Product              | Qty | Price  | Total      | Expired    | Days      | Supplier | Action
1 | Amoxicillin 500mg    | 20  | 45.00  | Rs.900.00  | 2025-10-10 | 18 days ago | Anura   | [Create Return]
2 | Paracetamol 500mg    | 15  | 22.00  | Rs.330.00  | 2025-10-15 | 13 days ago | Nimal   | [Create Return]
3 | Vitamin C 1000mg     | 10  | 55.00  | Rs.550.00  | 2025-10-20 | 8 days ago  | Kumari  | [Create Return]
...
```

#### Financial Impact:
```
Total Expired Value:       Rs. 5,450.00  ← Lost money
Value Expiring in 7 Days:  Rs. 2,230.00  ← Act fast!
Value Expiring in 30 Days: Rs. 8,790.00  ← Plan ahead
───────────────────────────────────────────
Total at Risk:            Rs. 16,470.00  ← Total exposure
```

#### Recommended Actions:
```
✓ Process returns for 5 expired products immediately
✓ Contact 3 suppliers about products expiring this week
✓ Plan promotions for 12 products expiring this month
✓ Adjust ordering for categories with high expiry rates
```

---

## 🔗 Integration with Returns System

### Workflow:
```
1. Run Expiry Check
   ↓
2. See expired product (e.g., Amoxicillin)
   ↓
3. Click "Create Return" button
   ↓
4. Redirected to add_return.php
   ↓
5. Product auto-selected in dropdown
   ↓
6. Fill quantity and reason ("Expired")
   ↓
7. Click "Process Return"
   ↓
8. Return created, stock updated
   ↓
9. View in Returns Management page
```

### Direct Link:
```html
<a href="add_return.php?product=p001">Create Return</a>
```

---

## 🎨 Color Scheme

### Status Colors:
```css
Expired:      #d9534f (Red/Danger)
7 Days:       #f0ad4e (Orange/Warning)
30 Days:      #5bc0de (Blue/Info)
90 Days:      #5cb85c (Green/Success)
```

### Chart Colors:
```javascript
Doughnut: ['#d9534f', '#f0ad4e', '#5bc0de', '#5cb85c']
Bar:      ['#667eea', '#764ba2', '#f093fb', '#4facfe', ...]
Line:     '#667eea' with gradient fill
```

### Visual Indicators:
- 🔴 Red: Immediate action required
- 🟠 Orange: Critical warning
- 🔵 Blue: Plan ahead
- 🟢 Green: Monitor regularly

---

## 📱 Responsive Design

### Desktop (>1200px):
- Cards: 4 columns (25% each)
- Charts: 2 columns (50% each)
- Tables: Full width with all columns

### Tablet (768px - 1200px):
- Cards: 2 columns (50% each)
- Charts: 1 column (100% each)
- Tables: Scrollable horizontally

### Mobile (<768px):
- Cards: 1 column (100% each)
- Charts: 1 column (100% each)
- Tables: Horizontal scroll with touch

---

## 🖨️ Print Functionality

### Print-Friendly Features:
- Button hidden when printing
- Clean table layout
- Charts included
- Black and white optimization
- Page breaks at logical points

### To Print:
```
Press Ctrl+P (Windows) or Cmd+P (Mac)
```

---

## ⚙️ Technical Details

### Database Query:
```sql
SELECT p.*, s.s_name AS supplier_name, s.contact_number
FROM product p
LEFT JOIN supplier_info s ON p.s_id = s.s_id
WHERE p.expire_date IS NOT NULL
ORDER BY p.expire_date ASC
```

### Duration Calculation:
```php
$expiry_date = '2025-11-05';
$today = '2025-10-28';
$days_diff = (strtotime($expiry_date) - strtotime($today)) / 86400;
// Result: 8 days
```

### Categorization Logic:
```php
if ($days_diff < 0) {
    // Expired
} elseif ($days_diff <= 7) {
    // Critical (7 days)
} elseif ($days_diff <= 30) {
    // Warning (30 days)
} elseif ($days_diff <= 90) {
    // Monitor (90 days)
}
```

---

## 🔧 Customization Options

### Change Duration Thresholds:
Edit in `get_expiry_analysis()` function:
```php
// Default: 7, 30, 90 days
// Change to: 10, 45, 120 days
if($days_diff <= 10) { ... }
if($days_diff <= 45) { ... }
if($days_diff <= 120) { ... }
```

### Add Email Notifications:
```php
// Add after generating report
if($data['stats']['expired'] > 0) {
    send_email('admin@company.com', 
               'Expired Products Alert', 
               $message);
}
```

### Export to Excel:
```javascript
// Add button to export charts and data
function exportToExcel() {
    // Implementation here
}
```

---

## 💼 Business Benefits

### 1. **Reduce Waste**
- Identify expired products before disposal required
- Plan promotions for products expiring soon
- Minimize financial losses

### 2. **Improve Cash Flow**
- Return expired products to suppliers
- Recover costs through supplier returns
- Optimize inventory levels

### 3. **Compliance**
- Meet regulatory requirements
- Maintain quality standards
- Avoid selling expired products

### 4. **Better Planning**
- Adjust ordering patterns
- Identify slow-moving products
- Optimize stock levels by category

### 5. **Data-Driven Decisions**
- Visual charts for quick understanding
- Financial impact clearly shown
- Category-wise analysis available

---

## 📊 Sample Output

### Example Report Summary:
```
═══════════════════════════════════════════
    EXPIRY ANALYSIS REPORT
    Date: 2025-10-28
═══════════════════════════════════════════

CRITICAL ALERTS:
• 5 products EXPIRED (Rs. 5,450.00)
• 3 products expire in 7 days (Rs. 2,230.00)

WARNINGS:
• 12 products expire in 30 days (Rs. 8,790.00)

MONITORING:
• 20 products expire in 90 days

TOTAL AT RISK: Rs. 16,470.00

TOP CATEGORIES WITH EXPIRY ISSUES:
1. Antibiotics: 10 products
2. Painkillers: 8 products
3. Vitamins: 5 products

RECOMMENDED ACTIONS:
✓ Process 5 returns immediately
✓ Contact suppliers for 3 products
✓ Plan promotions for 12 products
✓ Review ordering for Antibiotics category

═══════════════════════════════════════════
```

---

## 🚀 Quick Reference

### Key Metrics Displayed:
- ✅ Expired product count
- ✅ Products expiring in 7/30/90 days
- ✅ Total value at risk
- ✅ Category breakdown
- ✅ Timeline visualization
- ✅ Duration in days
- ✅ Supplier contact info
- ✅ Financial impact

### Actions Available:
- 🔴 Create Return (expired products)
- 🟠 Plan Return (7-day products)
- 📊 View Charts
- 📋 Export/Print Report
- 📧 Email Alerts (if configured)

---

## ✅ Checklist for Daily Use

### Morning Routine:
- [ ] Run expiry check
- [ ] Review expired products count
- [ ] Check financial impact
- [ ] Plan day's returns

### Weekly Tasks:
- [ ] Process all expired product returns
- [ ] Contact suppliers for critical items
- [ ] Plan promotions for 7-day items
- [ ] Review category trends

### Monthly Tasks:
- [ ] Analyze expiry patterns
- [ ] Adjust inventory ordering
- [ ] Review supplier performance
- [ ] Generate management reports

---

**Version**: 1.0  
**Last Updated**: October 28, 2025  
**Status**: ✅ Production Ready  
**Chart Library**: Chart.js 3.9.1

