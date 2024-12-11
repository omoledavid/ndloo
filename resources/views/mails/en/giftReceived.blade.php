<x-mail::message>
# Hello {{ $name }},
 
You received a gift of {{ $amount }} credits from {{ $sender }}.

You can redeem such gifts into cash in the Wallets page of your dashboard. 
Simply click on Sell Gift and follow the steps. 

<br>
Love,

{{ config('app.name') }}
</x-mail::message>