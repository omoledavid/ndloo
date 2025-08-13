<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Service response Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by services for various
    | messages that need to be sent as a response. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    //Authentication responses
    'emailVerified' => 'Email successfully verified.',
    'invalidCode' => 'Invalid code entered.',
    'otpSent' => 'A 4-digit code has been sent to your email/phone number.',
    'invalidCredentials' => 'Incorrect login credentials.',
    'unknownError' => 'An unknown error occured. Please try again later.',
    'userRegistered' => 'Your account has been successfully created.',
    'invalidRequest' => 'Invalid request sent.',
    'invalidPhoneNo' => 'Invalid phone number',

    //dashboard responses
    'reportSubmitted' => 'Your report has been submitted and is being reviewed.',
    'insufficientFunds' => 'Insufficient funds.',
    'withdrawalSuccessful' => 'Your withdrawal has been processed successfully.',
    'withdrawalPending' => 'Your withdrawal is still processing',
    'withdrawalFailed' => 'Your withdrawal failed. Your funds have been returned to your wallet',
    'planSubscribed' => 'Successfully subscribed to :name plan.',
    'planUnsubscribed' => 'Successfully unsubscribed to :name plan.',
    'giftSent' => 'Your gift has been sent successfully.',
    'profileUpdated' => 'Your profile has been updated.',
    'imagesUploaded' => 'Profile images successfully added.',
    'imageRemoved' => 'Image deleted.',
    'reactionToggled' => 'Action success.',
    'invalidAction' => 'Invalid action performed',
    'languageChanged' => 'Language changed',
    'invalidCurrency' => 'Invalid currency',
    'invalidChannel' => 'Invalid default channel selected',
    'invalidPayment' => 'This topup payment may have been settled already or is invalid',
    'paymentSuccessful' => 'Your topup is successful',
    'paymentFailed' => 'Your topup failed',
    'pendingConfirmation' => 'Your topup payment is still being processed',
    'passwordChanged' => 'Password changed successfully/',
    'giftRedeemed' => 'Gift conversion funds have been deposited into your wallet',
    'wrongPassword' => 'Wrong account password entered.',
    'passwordChanged' => 'Password changed successfully.',
    'emailChanged' => 'Email changed successfully.',
    'transferProcessing' => 'Your withdrawal request is being processed.',
    'messageCountReached' => 'Your message sending limit has been reached. Upgrade to send more messages',
    'invalidContent' => 'Your message contains censored words like :content',
    'newMessage' => 'New message',
    'newLike' => 'New like',
    'likeMessage' => ':name liked your profile',
    'cannotLike' => 'You can not like or unlike yourself',
    'giftNotMine' => 'Gift is not mine.',
    'nudityDetected' => 'Nudity detected. Please upload a different image.',
    'faceDetectionFailed' => 'Profile picture must contain exactly one face.',

];
