@extends('layouts.app')

@section('content')

<div class="hero-wrap js-fullheight" style="margin-top:-25px; background-image: url({{asset('assets/images/room-1.jpg')}});" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-start" data-scrollax-parent="true">
          <div class="col-md-7 ftco-animate">
          	<h2 class="subheading"></h2>
          	<h1 class="mb-4">Payment Gateway</h1>
            <!-- <p><a href="{{ route('home')}}" class="btn btn-primary">Go Home</a></p> -->
          </div>
        </div>
      </div>
</div>
<div class="row ">

       <div class="col-md-6 col-md-offset-3 mx-auto max-w-max rounded border-solid border m-2 py-4 px-3 ">
           <div class="panel panel-default credit-card-box">

               <div class="panel-body">


                   @if (Session::has('success'))
                       <div class="alert alert-success text-center">
                           <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                           <p>{{ Session::get('success') }}</p>
                       </div>
                   @endif

                   <form
                           role="form"
                           action="{{ route('hotel.checkout') }}"
                           method="post"
                           class="mx-auto "
                           data-cc-on-file="false"
                           data-stripe-publishable-key="{{ env('STRIPE_KEY') }}"
                           id="payment-form">
                       @csrf



                       <div class='form-row row'>
                           <div class='col-xs-12 form-group required'>
                               <label class='control-label'>Name on Card</label> <input
                                   class='form-control' size='4' type='text' >
                           </div>
                       </div>
                       <div class='form-row row'>
                           <div class='col-xs-12 form-group required'>
                               <label class='control-label'>Card Number</label> <input
                                   class='form-control' size='4' type='card'  >
                           </div>
                       </div>
                       <!-- <div class='form-row row'>
                           <div class='col-xs-12 form-group card required'>
                               <label class='control-label'>Card Number</label> <input
                                   autocomplete='off' class='form-control card-number' size='20'
                                   type='text' >
                           </div>
                       </div> -->

                       <div class='form-row row'>
                           <div class='col-xs-12 col-md-4 form-group cvc required'>
                               <label class='control-label'>CVC</label> <input autocomplete='off'
                                   class='form-control card-cvc' placeholder='ex. 311' size='4'
                                   type='text' required>
                           </div>
                           <div class='col-xs-12 col-md-4 form-group expiration required'>
                               <label class='control-label'>Expiration Month</label> <input
                                   class='form-control card-expiry-month' placeholder='MM' size='2'
                                   type='text' required>
                           </div>
                           <div class='col-xs-12 col-md-4 form-group expiration required'>
                               <label class='control-label'>Expiration Year</label> <input
                                   class='form-control card-expiry-year' placeholder='YYYY' size='4'
                                   type='text' required>
                           </div>
                       </div>

                       <div class="row">
                           <div class="col-xs-12">
                               <button class="btn btn-primary btn-lg btn-block" type="submit">Pay </button>
                           </div>
                       </div>

                   </form>
               </div>
           </div>
       </div>
   </div>

</div>

</body>

<script type="text/javascript" src="https://js.stripe.com/v3/"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var $form = $(".require-validation");

        $form.submit(function (e) {
            var $inputs = $form.find('input, textarea, select').not(':input[type=submit]');
            var valid = true;

            $inputs.each(function () {
                if ($(this).val() === '') {
                    $(this).closest('.form-group').addClass('has-error');
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                return;
            }

            if (!$form.data('cc-on-file')) {
                e.preventDefault();
                var stripeKey = $form.data('stripe-pk');
                Stripe.setPublishableKey(stripeKey);

                Stripe.createToken({
                    number: $('.card-number').val(),
                    cvc: $('.card-cvc').val(),
                    exp_month: $('.card-expiry-month').val(),
                    exp_year: $('.card-expiry-year').val()
                }, stripeResponseHandler);
            }
        });

        function stripeResponseHandler(status, response) {
            var $errorContainer = $('.error').removeClass('hide').find('.alert');

            if (response.error) {
                $errorContainer.text(response.error.message);
            } else {
                var token = response.id;
                $form.find('input[type=text]').val('');
                $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                $form.get(0).submit();
            }
        }
    });
</script>

@endsection
