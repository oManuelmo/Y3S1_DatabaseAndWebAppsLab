<div class="transaction-card border p-3 mb-3 rounded-md">
    <div class="transaction-body">
        <p class="transaction-text text-gray-600">
            <p class="transaction-type">{{ $transaction->transactiontype }}</p>
            <p class="{{ ($transaction->transactiontype == 'Buying' || $transaction->transactiontype == 'Withdraw') ? 'negative-transaction-value' : 'positive-transaction-value' }}">{{ $transaction->value }}</p>
            <p class="transaction-time">{{ $transaction->time }}</p>
        </p>
    </div>
</div>