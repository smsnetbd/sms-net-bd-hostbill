# 📱 SMS.BD - HostBill SMS Notification Module

This module enables **SMS notifications** in your **HostBill** application using the **sms.bd** API.  
It allows automated SMS sending for invoices, tickets, and client updates directly from HostBill.


## 🚀 Features

- Seamless integration with [sms.bd](https://sms.bd)
- Supports both **Client** and **Staff** notifications
- Customizable **message templates**
- Department-wise notification setup
- Manual SMS sending to clients
- Secure API-based connection

### 📝 Notification Capabilities
#### Notify Clients about:
- Billing-related events
- Support-ticket events (new ticket, new reply etc.)
- Hosting accounts events (new passwords, account created)
- Domain events - renewals, expirations
#### Notify staff about:
- Billing-related events
- Support events (new ticket, new reply etc.)
- New orders
- Failed logins
- Automation failures
- Incoming payments


## 📦 Installation

1. **Connect to your HostBill server** via SSH or file manager.
2. Navigate to:
   ```
   /public_html/includes/modules/Notification/
   ```
3. Create a new folder named:
   ```
   smsnetbd_sms
   ```
4. Set folder and file ownership to:
   ```
   hostbill:hostbill
   ```
5. Upload the file:
   ```
   class.smsnetbd_sms.php
   ```
   into the `smsnetbd_sms` directory.



## ⚙️ Activation in HostBill Admin

1. Log in to your **HostBill Admin Dashboard**.
2. Go to:  
   `Settings → Modules → Notification Modules`
3. Under **Inactive Modules**, find:
   ```
   SMS NET BD SMS Notifications
   ```
   and **activate** it.
4. Create a new app connection with:
   - **Application:** SMS NET BD SMS Notifications  
   - **Name:** *(any custom name)*  
   - **API Key:** Obtain from [portal.sms.bd/api](https://portal.sms.bd/api)  
   - **Sender ID (Optional):** Obtain from [portal.sms.bd/sender_id](https://portal.sms.bd/sender_id)  
     > ⚠️ Sender ID must have *Approved* status if used.


## 🛠️ General Setup

1. Navigate to:  
   `Settings → System Settings → General Settings → Mobile Notification`
2. Enable notifications for **Clients** and/or **Staff Members**.
3. Click **Save Changes**.


## ✉️ Message Templates

1. Go to:  
   `Settings → Message Template`
2. Enable SMS notifications for desired events, such as:
   - New Invoice
   - Overdue Invoice 
   - Paid Invoice
   - Support Ticket Reply  
   - ...and more.
3. *(Optional)* Edit SMS message templates to match your preferences.
4. Assign the correct **App Connection** (gateway/SMS account created earlier).

## 🧩 Department-Wise Notifications

To configure SMS notifications for specific support departments:

1. Navigate to:  
   `Support → Ticket Department → [Select Department] → Department Details`
2. Under **Notification**, set up **Mobile Notifications**.
3. Enable SMS notifications for the selected department.


## 📤 Sending Individual SMS

To send a message to a specific client or new number:

1. Go to:  
   `Clients → Notify Client → Mobile Notification`
2. Ensure **Mobile Notifications** are enabled under:  
   `Settings → System Settings → General Settings → Mobile Notification`
3. Send your custom SMS instantly.


## ✅ Integration Complete

Your **HostBill** installation is now fully integrated with **sms.bd** for automated and manual SMS notifications!


## 🧾 License

This module is licensed under the **MIT License**.  
See [LICENSE](./LICENSE) for more details.


## 💬 Support

For support or inquiries, visit:  
👉 [https://sms.bd/contact](https://sms.bd/contact/)  


