# PayHere Payment Gateway Integration

This document describes the complete PayHere payment gateway integration for Creators Space learning platform.

## 📋 Overview

The integration provides:
- Secure payment processing via PayHere (Sandbox mode)
- Dynamic cart total calculation
- User enrollment automation
- Payment status tracking
- Comprehensive logging

## 🗂️ Files Created

### 1. `frontend/checkout.php`
**Purpose**: Main checkout page that prepares PayHere payment form
- ✅ Calculates cart total dynamically
- ✅ Retrieves user details from database
- ✅ Generates secure hash for PayHere
- ✅ Beautiful responsive UI
- ✅ Form validation and error handling

### 2. `frontend/notify.php`
**Purpose**: PayHere notification handler
- ✅ Verifies payment authenticity with hash
- ✅ Processes payment status updates
- ✅ Creates course enrollments on success
- ✅ Clears cart after successful payment
- ✅ Comprehensive logging system

### 3. `frontend/success.html`
**Purpose**: Payment success confirmation page
- ✅ Animated success indicators
- ✅ Next steps guidance
- ✅ Quick navigation to courses
- ✅ Confetti celebration effect

### 4. `frontend/cancel.html`
**Purpose**: Payment cancellation page
- ✅ Clear cancellation message
- ✅ Help and troubleshooting tips
- ✅ Options to retry or return to cart
- ✅ Support contact information

### 5. `backend/sql/payments_schema.sql`
**Purpose**: Database schema for payment records
```sql
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id VARCHAR(100) UNIQUE NOT NULL,
    payment_id VARCHAR(100),
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'LKR',
    status ENUM('pending', 'completed', 'failed', 'canceled', 'chargedback'),
    payment_method VARCHAR(50),
    card_holder_name VARCHAR(100),
    card_no VARCHAR(20),
    card_expiry VARCHAR(10),
    status_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 🔧 Configuration

### PayHere Settings
```php
$merchant_id = "1221149";  // PayHere Sandbox Merchant ID
$merchant_secret = "1232176";  // ⚠️ REPLACE THIS
$currency = "LKR";
```

### URLs Configuration
- **Return URL**: `http://localhost/Creators-Space-GroupProject/frontend/success.html`
- **Cancel URL**: `http://localhost/Creators-Space-GroupProject/frontend/cancel.html`
- **Notify URL**: `http://localhost/Creators-Space-GroupProject/frontend/notify.php`

## 🔄 Payment Flow

1. **User clicks "Proceed to Checkout"** → Redirects to `checkout.php`
2. **Checkout page loads** → Calculates total, displays order summary
3. **User clicks "Pay with PayHere"** → Submits form to PayHere sandbox
4. **PayHere processes payment** → User completes payment
5. **PayHere sends notification** → `notify.php` processes the result
6. **User redirected** → Success or cancel page based on result

## 🛡️ Security Features

- ✅ **Hash verification** for all PayHere notifications
- ✅ **Merchant ID validation** to prevent spoofing
- ✅ **Order ID uniqueness** prevents duplicate processing
- ✅ **SQL injection protection** with prepared statements
- ✅ **Transaction integrity** with database transactions

## 📊 Payment Status Handling

| Status Code | Meaning | Action Taken |
|-------------|---------|-------------|
| 2 | Success | Create enrollments, clear cart, log payment |
| 0 | Pending | Log pending status |
| -1 | Canceled | Log cancellation |
| -2 | Failed | Log failure |
| -3 | Chargedback | Log chargeback |

## 🚀 Testing

### Test Cards (Sandbox)
PayHere sandbox accepts these test cards:
- **Visa**: 4111 1111 1111 1111
- **Mastercard**: 5555 5555 5555 4444
- **Any CVV**: 123
- **Any Expiry**: Future date

### Test Flow
1. Add courses to cart
2. Navigate to cart
3. Click "Proceed to Checkout"
4. Complete payment with test card
5. Verify success page and enrollment

## 📝 Logging

All payment activities are logged to:
- **File**: `logs/payhere_notifications.log`
- **Format**: `[TIMESTAMP] MESSAGE`
- **Includes**: All notifications, errors, and status changes

## 🔧 Setup Instructions

1. **Run setup script**:
   ```bash
   cd backend
   php setup_payments.php
   ```

2. **Update merchant secret** in:
   - `checkout.php` (line 37)
   - `notify.php` (line 12)

3. **Test the integration**:
   - Add items to cart
   - Proceed to checkout
   - Complete test payment

## 🌐 Production Deployment

### Required Changes for Production:

1. **Update PayHere URLs**:
   ```php
   // Change from sandbox to live
   https://www.payhere.lk/pay/checkout
   ```

2. **Update return URLs**:
   ```php
   // Replace localhost with your domain
   $return_url = "https://yourdomain.com/frontend/success.html";
   ```

3. **Use live merchant credentials**:
   ```php
   $merchant_id = "YOUR_LIVE_MERCHANT_ID";
   $merchant_secret = "YOUR_LIVE_MERCHANT_SECRET";
   ```

4. **Enable HTTPS** for all payment-related pages

## 🔍 Troubleshooting

### Common Issues:

1. **Hash mismatch error**:
   - Check merchant secret matches in both files
   - Verify amount formatting (2 decimal places)

2. **Notification not received**:
   - Check notify_url is publicly accessible
   - Verify server accepts POST requests

3. **Database errors**:
   - Ensure payments table exists
   - Check foreign key constraints

## 📞 Support

For PayHere integration support:
- **Documentation**: https://support.payhere.lk/
- **Test environment**: https://sandbox.payhere.lk/
- **Support email**: support@payhere.lk

---

## ✅ Integration Complete!

Your PayHere payment gateway is now fully integrated and ready for testing. The system provides:
- Secure payment processing
- Automatic course enrollment
- Comprehensive logging
- Beautiful user experience
- Production-ready architecture

Test thoroughly in sandbox mode before going live! 🚀