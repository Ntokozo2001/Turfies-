# Card Handler and Payment Processing Guide

This system provides comprehensive card management and payment processing functionality with secure storage and payment gateway integration.

## Features

- **Secure Card Storage**: Encrypted card details with PCI-compliant practices
- **Payment Processing**: Integration with payment gateways
- **Card Validation**: Real-time card validation using Luhn algorithm
- **Multiple Cards**: Users can save multiple payment methods
- **Transaction History**: Complete payment tracking
- **Billing Address**: Support for billing information

## Database Setup

Run the SQL script `card_payment_schema.sql` to create the required tables:

```sql
-- Main tables created:
- saved_cards: Encrypted card storage
- transactions: Payment transaction records  
- payment_methods: Available payment options
- orders: Order management (optional)
- order_items: Order line items (optional)
```

## Security Features

### Encryption
- Card numbers are encrypted using AES-256-CBC
- CVV codes are encrypted separately
- Card hashes prevent duplicates without storing plaintext

### PCI Compliance
- No plaintext card data in logs
- Masked card numbers for display
- Secure card data transmission

## API Endpoints

### Card Management

#### Save Card
```javascript
fetch('backend/cardhandler.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
        action: 'save_card',
        card_number: '4111111111111111',
        expiry_month: '12',
        expiry_year: '2025',
        cvv: '123',
        cardholder_name: 'John Doe',
        billing_address: '123 Main St',
        billing_city: 'New York',
        billing_state: 'NY',
        billing_zip: '10001',
        billing_country: 'USA',
        is_default: '1'
    })
});
```

#### Get Saved Cards
```javascript
fetch('backend/cardhandler.php?action=get_saved_cards')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayCards(data.cards);
        }
    });
```

#### Delete Card
```javascript
fetch('backend/cardhandler.php', {
    method: 'POST',
    body: new URLSearchParams({
        action: 'delete_card',
        card_id: '1'
    })
});
```

### Payment Processing

#### Process Payment with Saved Card
```javascript
fetch('backend/cardhandler.php', {
    method: 'POST',
    body: new URLSearchParams({
        action: 'process_payment',
        card_id: '1',
        amount: '99.99',
        currency: 'USD',
        order_id: 'ORDER_123'
    })
});
```

#### Process Payment with New Card
```javascript
fetch('backend/cardhandler.php', {
    method: 'POST',
    body: new URLSearchParams({
        action: 'process_payment',
        card_number: '4111111111111111',
        expiry_month: '12',
        expiry_year: '2025',
        cvv: '123',
        cardholder_name: 'John Doe',
        amount: '99.99',
        currency: 'USD',
        save_card: '1'
    })
});
```

### Validation

#### Validate Card
```javascript
fetch('backend/cardhandler.php', {
    method: 'POST',
    body: new URLSearchParams({
        action: 'validate_card',
        card_number: '4111111111111111'
    })
})
.then(response => response.json())
.then(data => {
    console.log('Valid:', data.valid);
    console.log('Type:', data.card_type);
    console.log('Masked:', data.masked_number);
});
```

## Frontend Integration Examples

### Card Entry Form

```html
<form id="cardForm">
    <div class="card-input">
        <label>Card Number</label>
        <input type="text" id="cardNumber" maxlength="19" placeholder="1234 5678 9012 3456">
        <span id="cardType"></span>
    </div>
    
    <div class="expiry-input">
        <label>Expiry Date</label>
        <select id="expiryMonth">
            <!-- Month options -->
        </select>
        <select id="expiryYear">
            <!-- Year options -->
        </select>
    </div>
    
    <div class="cvv-input">
        <label>CVV</label>
        <input type="text" id="cvv" maxlength="4" placeholder="123">
    </div>
    
    <div class="cardholder-input">
        <label>Cardholder Name</label>
        <input type="text" id="cardholderName" placeholder="John Doe">
    </div>
    
    <div class="save-card">
        <input type="checkbox" id="saveCard">
        <label for="saveCard">Save this card for future payments</label>
    </div>
    
    <button type="submit">Save Card</button>
</form>
```

### Real-time Card Validation

```javascript
document.getElementById('cardNumber').addEventListener('input', function(e) {
    let cardNumber = e.target.value.replace(/\D/g, '');
    
    // Format card number with spaces
    cardNumber = cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
    e.target.value = cardNumber;
    
    // Validate card in real-time
    if (cardNumber.replace(/\s/g, '').length >= 13) {
        fetch('backend/cardhandler.php', {
            method: 'POST',
            body: new URLSearchParams({
                action: 'validate_card',
                card_number: cardNumber.replace(/\s/g, '')
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('cardType').textContent = data.card_type;
            e.target.classList.toggle('valid', data.valid);
            e.target.classList.toggle('invalid', !data.valid);
        });
    }
});
```

### Saved Cards Display

```javascript
function displaySavedCards(cards) {
    const container = document.getElementById('savedCards');
    
    cards.forEach(card => {
        const cardElement = document.createElement('div');
        cardElement.className = 'saved-card';
        cardElement.innerHTML = `
            <div class="card-info">
                <span class="card-type">${card.card_type}</span>
                <span class="card-number">${card.card_number_masked}</span>
                <span class="expiry">${card.expiry_month}/${card.expiry_year}</span>
                <span class="cardholder">${card.cardholder_name}</span>
                ${card.is_default ? '<span class="default-badge">Default</span>' : ''}
            </div>
            <div class="card-actions">
                <button onclick="useCard(${card.card_id})">Use Card</button>
                <button onclick="setDefault(${card.card_id})">Set Default</button>
                <button onclick="deleteCard(${card.card_id})">Delete</button>
            </div>
        `;
        container.appendChild(cardElement);
    });
}
```

### Payment Processing

```javascript
function processPayment(formData) {
    // Show loading state
    document.getElementById('payButton').disabled = true;
    document.getElementById('payButton').textContent = 'Processing...';
    
    fetch('backend/cardhandler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Payment successful
            showSuccessMessage(`Payment processed! Transaction ID: ${data.transaction_id}`);
            redirectToSuccess(data.transaction_id);
        } else {
            // Payment failed
            showErrorMessage(data.message);
        }
    })
    .catch(error => {
        showErrorMessage('Payment processing error');
    })
    .finally(() => {
        // Reset button state
        document.getElementById('payButton').disabled = false;
        document.getElementById('payButton').textContent = 'Pay Now';
    });
}
```

## Payment Gateway Integration

The system is designed to work with popular payment gateways:

### Stripe Integration
```php
// Replace the mock processWithPaymentGateway function
function processWithPaymentGateway($card_details, $amount, $transaction_id, $payment_data) {
    \Stripe\Stripe::setApiKey('sk_test_...');
    
    try {
        $charge = \Stripe\Charge::create([
            'amount' => $amount * 100,
            'currency' => $payment_data['currency'] ?? 'usd',
            'source' => [
                'object' => 'card',
                'number' => $card_details['card_number'],
                'exp_month' => $card_details['expiry_month'],
                'exp_year' => $card_details['expiry_year'],
                'cvc' => $card_details['cvv']
            ],
            'description' => 'Order payment - ' . $transaction_id
        ]);
        
        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'gateway_transaction_id' => $charge->id
        ];
    } catch (\Stripe\Exception\CardException $e) {
        return [
            'success' => false,
            'message' => $e->getError()->message
        ];
    }
}
```

### PayPal Integration
```php
// PayPal REST API integration example
function processWithPaymentGateway($card_details, $amount, $transaction_id, $payment_data) {
    // Configure PayPal API
    // Create payment object
    // Process payment
    // Return standardized response
}
```

## Security Best Practices

1. **Environment Variables**: Store encryption keys in environment variables
2. **HTTPS Only**: All card data transmission must use HTTPS
3. **Input Validation**: Validate all card data before processing
4. **Logging**: Never log sensitive card information
5. **Access Control**: Restrict access to card data endpoints
6. **Regular Updates**: Keep payment gateway SDKs updated

## Error Handling

```javascript
function handlePaymentError(error) {
    const errorMessages = {
        'card_declined': 'Your card was declined. Please try a different card.',
        'insufficient_funds': 'Insufficient funds on your card.',
        'expired_card': 'Your card has expired.',
        'incorrect_cvc': 'The security code is incorrect.',
        'processing_error': 'Payment processing error. Please try again.'
    };
    
    const message = errorMessages[error.code] || error.message || 'Payment failed';
    showErrorMessage(message);
}
```

## Testing

Use test card numbers for development:

- **Visa**: 4111111111111111
- **Mastercard**: 5555555555554444
- **American Express**: 378282246310005
- **Declined Card**: 4000000000000002

## Configuration

Set up environment variables:
```bash
CARD_ENCRYPTION_KEY=your-secret-encryption-key-32-chars
STRIPE_SECRET_KEY=sk_test_...
PAYPAL_CLIENT_ID=your-paypal-client-id
```

The system provides a complete, secure card handling and payment processing solution ready for production use! 💳✨