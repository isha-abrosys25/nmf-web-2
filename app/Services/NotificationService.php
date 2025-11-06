<?php
namespace App\Services;
use App\Models\Category;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\FirebaseCloudMessaging;

class NotificationService
{
    public function sendNotification(Request $request, $blogId,int $isbreaking = null)
    {      
       
        $blogId = (string) $blogId;
         Log::info(json_encode("in notification service", JSON_PRETTY_PRINT));
        $catid = $request->category;
        $blogcat = Category::where('id', $catid)->first();
        $blogcategoryName = $blogcat ? $blogcat->name : null;
	 //NL1041:06Oct2025:added
        $blogcategoryEngName = $blogcat ? $blogcat->eng_name : null;

        if (isset($request->images) && $request->images != '' && !empty($request->images) && empty($request->link)) {
            $imagefile = File::where("id", $request->images)->first();  // ? FIXED THIS LINE
        } else {
            $imagefile = File::where("id", $request->thumb_images)->first();
        }
       //NL1041:06Oct2025:Commented and added from config
       // $baseUrl = 'https://www.newsnmf.com/';
       $baseUrl =  config('global.base_url');
      
        $liveimagepath = null;

        if ($imagefile && isset($imagefile->full_path)) {
            $imagePath = $imagefile->full_path;

            if (strpos($imagePath, 'file') !== false) {
                $findFilePos = strpos($imagePath, 'file');
                $liveimagepath = substr($imagePath, $findFilePos);
                $liveimagepath = $baseUrl . $liveimagepath . '/' . $imagefile->file_name;
            }
        }

        $notificationTitle = $request->input('notification_title');
        $blogname = $request->input('name');
        $notificationBody = $notificationTitle ? $blogname : null;

        if ($isbreaking === null) {
            $isbreaking = $request->has('breaking_status') ? 1 : 0;
        }
           Log::info(json_encode($isbreaking, JSON_PRETTY_PRINT));
        $type = ($isbreaking == 1)
            ? 'breaking_news'
            : ($request->link ? 'video' : 'article');

        if (empty($notificationTitle)) {
            $notificationTitle = $request->input('name');
        }

        $serviceAccountPath = base_path('nmf-news-app-a2eb4fb2b37d.json');

        $client = new Client();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->useApplicationDefaultCredentials();

        $accessToken = $client->fetchAccessTokenWithAssertion();
	//NL1041:06Oct2025:added blogcategoryEngName
        $categoryTopic = strtolower(str_replace(' ', '_', $blogcategoryEngName));

       if($isbreaking == 1){

        $payload = [
            'message' => [
                "topic"=>  "breaking-news",
                'data' => [
                    'id' => $blogId,
                    'type' => $type,
                    'category' => $blogcategoryName,
                    'imageUrl' => $liveimagepath,
                    'videoUri' => $request->link,
                    'image' => $liveimagepath,
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                ],
                'android' => [
                    'priority' => 'high',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'content-available' => 1,
                        ],
                    ],
                ],
            ],
        ];
    } else{
      $payload = [
            'message' => [
                //NL1041:06Oct2025:Commented topic added from condition
                //'topic' => 'nmf-news-app-stagging-v2',
                'condition' => "'nmf-news-app' in topics || '" . $categoryTopic . "' in topics",
                'data' => [
                    'id' => $blogId,
                    'type' => $type,
                    'category' => $blogcategoryName,
                    'imageUrl' => $liveimagepath,
                    'videoUri' => $request->link,
                    'image' => $liveimagepath,
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                ],
                'android' => [
                    'priority' => 'high',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'content-available' => 1,
                        ],
                    ],
                ],
            ],
        ];

    }

        Log::info(json_encode($payload, JSON_PRETTY_PRINT));
      // Send the notification
       $response = $this->sendFCMNotification($accessToken['access_token'], $payload);
       Log::info("notification sent");
       echo json_encode($response, JSON_PRETTY_PRINT);
       return response()->json(['message' => 'Notification sent', 'response' => $response]);
    }
      public function sendFCMNotification($accessToken, $payload)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/nmf-news-app/messages:send';  // Replace with your Firebase project ID

        // Send the request to Firebase Cloud Messaging API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute the request
        $response = curl_exec($ch);
        curl_close($ch);
        echo json_encode($response, JSON_PRETTY_PRINT);
        return json_decode($response, true);
    }
    
}
