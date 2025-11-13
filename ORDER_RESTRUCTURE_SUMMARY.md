# Order Table Restructure Summary

## Database Changes

### Orders Table Modifications
The `orders` table has been restructured with the following changes:

**Removed Fields:**
- `item_id` (moved to order_items)
- `price` (moved to order_items as sale_amount)
- `address_line_1`, `address_line_2`, `state`, `postal_code`, `country` (consolidated)
- `phone` (renamed)
- `weight`, `delivery_date` (removed)

**Modified Fields:**
- `name` → `customer_name` (renamed for clarity)
- `city` (now nullable)

**New Fields:**
- `address` - Full address string
- `contact_number_one` - Primary contact number
- `contact_number_two` - Secondary contact number (nullable)
- `email` - Customer email
- `other` - Additional information (nullable)
- `due_date` - Order due date (nullable)
- `lead_from` - Lead source (enum: facebook, whatsapp, advertisement, other)

### Order Items Table (NEW)
A new `order_items` table has been created to handle multiple items per order:

**Fields:**
- `id` - Primary key
- `order_id` - Foreign key to orders table (cascade delete)
- `product_id` - Foreign key to items table
- `qty` - Quantity (integer, default: 1)
- `sale_amount` - Sale amount per item (decimal)
- `del_fee` - Delivery fee per item (decimal, default: 0)
- `is_invoiced` - Invoice status (boolean, default: false)
- `timestamps` - Created/updated timestamps

## Model Changes

### Order Model
**Updated Relationships:**
- Removed: `item()` belongsTo relationship
- Added: `orderItems()` hasMany relationship

**New Computed Attributes:**
- `total_amount` - Calculates sum of all order items (qty * sale_amount + del_fee)
- `total_delivery_fee` - Sum of all delivery fees

**Updated Methods:**
- `getFullAddressAttribute()` - Now concatenates address and city
- `getLeadFromOptions()` - Returns array of lead source options

### OrderItem Model (NEW)
**Relationships:**
- `order()` - belongsTo Order
- `product()` - belongsTo Item (via product_id)

**Computed Attributes:**
- `total` - Calculate item total (qty * sale_amount + del_fee)
- `subtotal` - Calculate item subtotal (qty * sale_amount)

**Fillable Fields:**
- order_id, product_id, qty, sale_amount, del_fee, is_invoiced

## Controller Changes

### OrderController
**Updated Methods:**

1. **index()** - List orders
   - Now loads `orderItems.product` relationship
   - Updated search to use `customer_name`, `contact_number_one`, `contact_number_two`
   - Added filter by `lead_from`

2. **store()** - Create order
   - Uses database transactions
   - Accepts `order_items` array in request
   - Creates order and multiple order items
   - Auto-fills `sale_amount` from item price if not provided

3. **show()** - Get single order
   - Loads `orderItems.product` relationship

4. **update()** - Update order
   - Uses database transactions
   - Handles order items update/create/delete
   - Maintains existing items by ID
   - Deletes removed items

5. **statistics()** - Get stats
   - Updated revenue calculation to sum order items
   - Added `total_order_items` stat

## Request Validation

### StoreOrderRequest
**Required Fields:**
- `customer_name`
- `order_items` (array, min: 1)
- `order_items.*.product_id`
- `order_items.*.qty`

**Optional Fields:**
- `status`, `address`, `city`, `contact_number_one`, `contact_number_two`
- `email`, `other`, `due_date`, `lead_from`, `notes`
- `order_items.*.sale_amount`, `order_items.*.del_fee`, `order_items.*.is_invoiced`

### UpdateOrderRequest
**All Fields Optional (use "sometimes" rule):**
- Same as StoreOrderRequest but with `order_items.*.id` for existing items

## API Request Format

### Creating an Order
```json
{
  "customer_name": "John Doe",
  "address": "123 Main St, Apt 4B",
  "city": "New York",
  "contact_number_one": "+1234567890",
  "contact_number_two": "+0987654321",
  "email": "john@example.com",
  "due_date": "2025-12-01",
  "lead_from": "facebook",
  "status": "pending",
  "notes": "Urgent delivery",
  "other": "Additional info",
  "order_items": [
    {
      "product_id": 1,
      "qty": 2,
      "sale_amount": 50.00,
      "del_fee": 5.00,
      "is_invoiced": false
    },
    {
      "product_id": 2,
      "qty": 1,
      "sale_amount": 75.00,
      "del_fee": 10.00,
      "is_invoiced": false
    }
  ]
}
```

### Updating an Order
```json
{
  "customer_name": "John Doe Updated",
  "city": "Los Angeles",
  "status": "processing",
  "order_items": [
    {
      "id": 1,
      "product_id": 1,
      "qty": 3,
      "sale_amount": 50.00,
      "del_fee": 5.00
    },
    {
      "product_id": 3,
      "qty": 1,
      "sale_amount": 100.00,
      "del_fee": 15.00
    }
  ]
}
```

## Next Steps

### Frontend Updates Required:
1. Update create/edit forms to handle new order structure
2. Add fields for: customer_name, address, city, contact numbers, email, other, due_date, lead_from
3. Implement order items management (add/remove/edit multiple items)
4. Update order list/table to display new fields
5. Update order detail view to show order items list
6. Add lead source filter dropdown

### Migration Notes:
- ✅ Migrations have been successfully run
- ✅ Order table restructured
- ✅ Order items table created
- ⚠️ Existing order data will need manual migration if any exists
- ⚠️ Frontend pages need complete restructure to match new API format

## Files Modified/Created:

### Backend:
- ✅ `database/migrations/2025_11_12_173204_modify_orders_table_structure.php` (created)
- ✅ `database/migrations/2025_11_12_173211_create_order_items_table.php` (created)
- ✅ `app/Models/Order.php` (updated)
- ✅ `app/Models/OrderItem.php` (created)
- ✅ `src/Admin/Order/Controllers/OrderController.php` (updated)
- ✅ `src/Admin/Order/Requests/StoreOrderRequest.php` (updated)
- ✅ `src/Admin/Order/Requests/UpdateOrderRequest.php` (updated)

### Frontend (Requires Update):
- ⏳ `pages/admin/orders/create.vue` (needs update)
- ⏳ `pages/admin/orders/[id].vue` (needs update)
- ⏳ `pages/admin/orders/index.vue` (needs update)
