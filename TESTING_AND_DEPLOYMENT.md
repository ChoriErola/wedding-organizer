# 🧪 Testing & Deployment Guide - Invoice Pelanggan

## 🚀 Quick Start

### Prerequisites
- ✅ Laravel project running
- ✅ Database migrations complete
- ✅ User authenticated (logged in)
- ✅ Orders exist in database

### Step 1: Verify Files Exist

```bash
# Check if InvoiceShow component exists
ls app/Livewire/Pelanggan/InvoiceShow.php

# Check if invoice template exists
ls resources/views/livewire/pelanggan/invoice-show.blade.php

# Check if routes updated
grep "pelanggan.pesanan.invoice" routes/web.php
```

### Step 2: Verify Routes

```bash
php artisan route:list | grep invoice
```

Expected output:
```
GET|HEAD  /pelanggan/pesanan/{order}/invoice .......................... pelanggan.pesanan.invoice › InvoiceShow
```

### Step 3: Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache --force
```

---

## ✅ Manual Testing

### Test Case 1: Access Orders Page

**Steps:**
1. Login to customer account
2. Navigate to `/pelanggan/pesanan`
3. Verify you see list of orders
4. Verify "Lihat Invoice" button appears on each order

**Expected Result:**
- Orders list displays
- Button is visible and styled correctly
- Button text shows "📄 Lihat Invoice"

---

### Test Case 2: Click Invoice Button

**Steps:**
1. From orders page, click "Lihat Invoice" button
2. Wait for page to load
3. Verify invoice displays

**Expected Result:**
- URL changes to `/pelanggan/pesanan/{order_id}/invoice`
- Invoice data loads
- All fields display correctly

---

### Test Case 3: Verify Invoice Data

**Steps:**
1. On invoice page, check each section:
   - Order code matches
   - Customer name displays
   - Package name displays
   - Services listed correctly
   - Total price matches
   - Status displays

**Expected Result:**
- All data is correct and properly formatted
- Currency shows as "Rp" with proper formatting
- Dates show in Indonesian format

---

### Test Case 4: View PDF

**Steps:**
1. On invoice page, click "👁️ Lihat Invoice" button
2. Wait for PDF to load (2-3 seconds)

**Expected Result:**
- PDF opens in browser
- Invoice displays in PDF format
- Can scroll through PDF
- Print button works

---

### Test Case 5: Download PDF

**Steps:**
1. On invoice page, click "⬇️ Unduh Invoice" button
2. Wait for download to complete

**Expected Result:**
- File downloads to computer
- Filename is `invoice-{order_code}.pdf`
- File opens correctly in PDF viewer

---

### Test Case 6: Return to Orders

**Steps:**
1. On invoice page, click "← Kembali ke Pesanan" button

**Expected Result:**
- Redirected to `/pelanggan/pesanan`
- Orders list displays
- No errors

---

### Test Case 7: Security - Unauthorized Access

**Steps:**
1. Login as Customer A
2. Get Order ID from Customer B
3. Try accessing: `/pelanggan/pesanan/{customer_b_order_id}/invoice`

**Expected Result:**
- HTTP 403 error displayed
- Message: "Unauthorized" or similar
- User cannot see other customer's invoice

---

### Test Case 8: Security - No Authentication

**Steps:**
1. Logout from application
2. Try accessing: `/pelanggan/pesanan/1/invoice`

**Expected Result:**
- Redirected to `/login` page
- Cannot access invoice without login

---

### Test Case 9: Multiple Orders

**Steps:**
1. Create/access multiple orders
2. Click invoice buttons on different orders
3. Verify each displays correct data

**Expected Result:**
- Each invoice shows correct data
- No data mixing between orders

---

### Test Case 10: Mobile Responsive

**Steps:**
1. Open invoice on mobile device
2. Verify layout adjusts to mobile size
3. Test PDF on mobile

**Expected Result:**
- Layout is readable on mobile
- Buttons are clickable
- PDF displays properly

---

## 🧪 Automated Testing (Optional)

### Feature Test

```php
// tests/Feature/InvoiceTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;

class InvoiceTest extends TestCase
{
    public function test_customer_can_view_own_invoice()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('pelanggan.pesanan.invoice', $order->id));

        $response->assertStatus(200);
        $response->assertViewIs('livewire.pelanggan.invoice-show');
    }

    public function test_customer_cannot_view_other_customer_invoice()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)
            ->get(route('pelanggan.pesanan.invoice', $order->id));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_view_invoice()
    {
        $order = Order::factory()->create();

        $response = $this->get(route('pelanggan.pesanan.invoice', $order->id));

        $response->assertStatus(302); // Redirect to login
        $response->assertRedirect('/login');
    }

    public function test_customer_can_download_pdf()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('pelanggan.pesanan.invoice', $order->id), [
                'action' => 'download'
            ]);

        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
```

### Run Tests

```bash
php artisan test tests/Feature/InvoiceTest.php
```

---

## 🐛 Troubleshooting

### Issue: Route not found (404)

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

---

### Issue: Component not found

**Solution:**
1. Check file exists: `app/Livewire/Pelanggan/InvoiceShow.php`
2. Check namespace is correct
3. Clear cache:
   ```bash
   php artisan cache:clear
   ```

---

### Issue: PDF not generating

**Solution:**
1. Check DomPDF is installed:
   ```bash
   composer show | grep dompdf
   ```
2. Check storage permissions:
   ```bash
   chmod -R 755 storage/
   ```
3. Check error logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

### Issue: Data not displaying

**Solution:**
1. Check order has data:
   ```bash
   php artisan tinker
   > Order::find(1)->with(['customer', 'services', 'package'])->first()
   ```
2. Check JSON data is valid
3. Check service relations are loaded

---

### Issue: Unauthorized error (403)

**Solution:**
1. Verify `Auth::id()` returns current user ID:
   ```php
   dd(auth()->id()); // Should not be null
   ```
2. Verify order belongs to current user:
   ```php
   dd($order->user_id); // Should match auth()->id()
   ```

---

### Issue: Button not appearing

**Solution:**
1. Check orders.blade.php was updated
2. Clear view cache:
   ```bash
   php artisan view:clear
   ```
3. Refresh browser (hard refresh: Ctrl+Shift+R)

---

## 📊 Performance Testing

### Load Testing

```bash
# Test with Apache Bench
ab -n 100 -c 10 http://localhost/pelanggan/pesanan/1/invoice

# Test with wrk
wrk -t12 -c400 -d30s http://localhost/pelanggan/pesanan/1/invoice
```

### Expected Performance
- Response time: < 500ms
- PDF generation: < 3 seconds
- No memory leaks

---

## 📋 Deployment Checklist

Before deploying to production:

- [ ] All files copied to server
- [ ] Routes registered correctly
- [ ] Database migrations run
- [ ] Permissions set correctly
- [ ] Cache cleared
- [ ] Tested on staging
- [ ] Security validated
- [ ] Performance tested
- [ ] Error logs monitored
- [ ] Backup created

### Deployment Commands

```bash
# Copy files to server
scp -r app/Livewire/Pelanggan/InvoiceShow.php user@server:/path/to/app/
scp -r resources/views/livewire/pelanggan/invoice-show.blade.php user@server:/path/to/resources/

# On server
cd /path/to/project
git pull origin main
php artisan migrate
php artisan cache:clear
php artisan view:clear
php artisan route:cache
```

---

## 📈 Monitoring

### Logs to Monitor

```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Search for errors
grep "error" storage/logs/laravel.log

# Search for invoice errors
grep -i "invoice" storage/logs/laravel.log
```

### Key Metrics

- Page load time
- PDF generation time
- User authentication success rate
- 403 error rate (should be low for valid users)
- PDF download completion rate

---

## 🔍 Browser Developer Tools

### Console Checks

```javascript
// Check route is registered
document.querySelector('[href*="invoice"]') // Should find button

// Check JavaScript errors
console.log('No errors') // Should show in console
```

### Network Tab

- Check `/pelanggan/pesanan/{id}/invoice` request
- Status should be 200
- Response time < 500ms
- PDF download should show application/pdf

### Application Tab

- Check localStorage has auth token
- Check session is active

---

## 📱 Mobile Testing

### iOS Testing

```bash
# Open Safari Remote Debugging
# Plug iPhone
# Safari → Develop → [Device] → [App]
```

### Android Testing

```bash
# Open Chrome DevTools
# chrome://inspect/#devices
# Select device and application
```

### Expected Mobile Behavior

- Buttons are clickable
- Tables scroll horizontally if needed
- PDF displays properly
- No layout issues

---

## 🎯 Sign-off Criteria

All tests must pass:

- [ ] ✅ Customer can view own invoice
- [ ] ✅ Customer cannot view other's invoice
- [ ] ✅ Unauthenticated cannot access
- [ ] ✅ PDF generates correctly
- [ ] ✅ PDF downloads correctly
- [ ] ✅ Data displays correctly
- [ ] ✅ Mobile responsive
- [ ] ✅ No performance issues
- [ ] ✅ No security issues
- [ ] ✅ Works in all browsers

---

## 📞 Support & Escalation

### If Issues Found

1. **Check documentation first**
   - [INVOICE_QUICK_REFERENCE.md](INVOICE_QUICK_REFERENCE.md)
   - [INVOICE_IMPLEMENTATION_CHECKLIST.md](INVOICE_IMPLEMENTATION_CHECKLIST.md)

2. **Check error logs**
   - `storage/logs/laravel.log`
   - Browser console
   - Network tab

3. **Escalate if needed**
   - Create GitHub issue
   - Contact development team
   - Include error logs and screenshots

---

## ✅ Final Verification

```bash
# Run final checks
php artisan route:list | grep invoice
php artisan tinker
> $order = Order::first();
> auth()->login($order->user);
> route('pelanggan.pesanan.invoice', $order->id)
> // Should output: /pelanggan/pesanan/1/invoice

# Test access
curl http://localhost/pelanggan/pesanan/1/invoice \
  -H "Cookie: PHPSESSID=..." \
  -H "Accept: text/html"
```

---

**Version**: 1.0  
**Status**: ✅ Ready for Production  
**Last Updated**: 2026-01-01

✅ **All systems ready for deployment!**
