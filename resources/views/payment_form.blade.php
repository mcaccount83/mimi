<form action="/process-payment" method="POST">
    @csrf
    <div>
        <label for="card_number">Card Number</label> <span class="field-required">*</span>
        <input type="text" id="card_number" name="card_number">
    </div>
    <div>
        <label for="expiration_date">Expiration Date</label> <span class="field-required">*</span>
        <input type="text" id="expiration_date" name="expiration_date">
    </div>
    <div>
        <label for="cvv">CVV</label> <span class="field-required">*</span>
        <input type="text" id="cvv" name="cvv">
    </div>
    <div>
        <button type="submit">Submit Payment</button>
    </div>
</form>

