var stripe = Stripe('pk_test_51OUV2BEEycS1RaBOjkqBxJ4P1QvjA31vsuJo47dTh2Q1UV9si5sgpCyUClPs7BtPTqMvJFJfgsTDcUFfKbZSMzQj00HY0ateJA');
const elements = stripe.elements();

const cardElement = elements.create('card');

cardElement.mount('#card-element');

const form = document.getElementById('payment-form');
const button = document.getElementById('submit');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const { paymentIntent, error } = await stripe.confirmCardPayment(
        '{{ $clientSecret }}',
        {
            payment_method: {
                card: cardElement,
            },
        }
    );

    if (error) {
        alert(error.message);
    } else if (paymentIntent.status === 'succeeded') {
        window.location.href = "{{ route('hotel.success') }}";
    }
});
