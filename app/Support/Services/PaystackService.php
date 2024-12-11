<?php

namespace App\Support\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PaystackService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->secretKey = env('PAYSTACK_SECRET_KEY');
    }

    /**
     * Initialize Paystack Payment
     * @param float $amount
     * @param string $email
     * @param string $reference
     * @return array
     */
    public function initializePayment($amount, $email, $reference)
    {
        try {
            // Make API request to Paystack to initialize the transaction
            $response = $this->client->post('https://api.paystack.co/transaction/initialize', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'amount' => $amount * 100,  // Paystack expects the amount in kobo
                    'reference' => $reference,
                    'redirect_url' => route('payment.callback'),  // Paystack will redirect after payment
                ],
            ]);

            // Get the response body and decode it
            $responseBody = json_decode($response->getBody()->getContents(), true);

            if ($responseBody['status'] === true) {
                return [
                    'status' => true,
                    'payment_url' => $responseBody['data']['authorization_url'],  // URL to redirect the user to for payment
                    'reference' => $responseBody['data']['reference'],  // Store the reference for verification later
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Error initializing Paystack payment',
                ];
            }
        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Paystack Payment
     * @param string $reference
     * @return array
     */
    public function verifyPayment($reference)
    {
        try {
            // Send GET request to verify payment status
            $response = $this->client->get('https://api.paystack.co/transaction/verify/' . $reference, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secretKey,
                ],
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Check if the payment was successful
            if ($responseBody['status'] === true && $responseBody['data']['status'] === 'success') {
                return [
                    'status' => true,
                    'data' => $responseBody['data'],
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Payment verification failed.',
                ];
            }
        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
