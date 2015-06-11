<?php
	$myfile = fopen("live", "w");
	fclose($myfile);
    Notify();

    function Notify()
    {
        // Your ID and token
        $authToken = 'Basic M2I1M2UxNmYyMGVkMDgwYjliNmZmNjA1ZDBmYWUyMjI6YTdkNTgxNzI3ZTBjNGFjY2RlZDIzYTczYjg0OWM0Yzg=';

        // The data to send to the API
        $postData = array(
            'chrome' => array('redirect_url' => '/#/live','header' => 'Streaming Now','icon' => 'http://stream.tekoone.ru/favicon.ico' ),
            'message' => 'new stream on stream.tekoone.ru',
            'audience' => array('all' => 1)
        );

        // Create the context for the request
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Authorization: {$authToken}\r\n" .
                    "Content-Type: application/json\r\n",
                'content' => json_encode($postData)
            )
        ));

        // Send the request
        $response = file_get_contents('https://go.jeapie.com/api/v2/push.json', FALSE, $context);

        // Check for errors
        if ($response === FALSE) {
            die('Error');
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);

        // Print the date from the response
        echo $responseData['published'];
    }