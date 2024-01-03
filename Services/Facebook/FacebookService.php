<?php


namespace Services\Facebook;


class FacebookService
{
    public function matchObjectFromFacebookUrl($url, $type = 'like')
    {
        $objectId = $url;
        if (strpos($url, "facebook.com")) {
            switch ($type) {
                case "like_comment":
                    $re = '/comment_id=([a-z_0-9]+)/m';
                    preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);
                    if (isset($matches[0][1])) {
                        $objectId = $matches[0][1];
                    }
                    break;
                case "follow":
                case "like_page":
                    $res = $this->getProfileId($url);
                    if (!isset($res) && isset($re['object_id'])) {
                        $objectId = $re['object_id'];
                    }
                    break;
                default:
                    // match post_id
                    preg_match('/(.*)\/posts\/([0-9A-Za-z]{8,})/', $url, $post_id);
                    // match photo_id
                    preg_match('/(.*)\/photo.php\?fbid=([0-9A-Za-z]{8,})/', $url, $photo_id);
                    // match video_id
                    preg_match('/(.*)\/video.php\?v=([0-9A-Za-z]{8,})/', $url, $video_id);
                    // store Id
                    preg_match('/(.*)\/story.php\?story_fbid=([0-9A-Za-z]{8,})/', $url, $store_id);
                    // match link_id
                    preg_match('/(.*)\/permalink.php\?story_fbid=([0-9A-Za-z]{8,})/', $url, $link_id);
                    // match other_id
                    preg_match('/(.*)\/([0-9A-Za-z]{8,})/', $url, $other_id);
                    // comment Id
                    preg_match('/(.*)\/([0-9A-Za-z]{8,})/', $url, $comment_id);
                    if (!empty($post_id)) {
                        $objectId = $post_id[2];
                    }
                    if (!empty($photo_id)) {
                        $objectId = $photo_id[2];
                    }
                    if (!empty($video_id)) {
                        $objectId = $video_id[2];
                    }
                    if (!empty($link_id)) {
                        $objectId = $link_id[2];
                    }
                    if (!empty($store_id)) {
                        $objectId = $store_id[2];
                    }
                    if (!empty($other_id)) {
                        $objectId = $other_id[2];
                    }
                    if (!empty($comment_id)) {
                        $objectId = '_' . $other_id[2];
                    }
            }
        }
        return $objectId;
    }

    private function getProfileId($link)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://finduid.net/finduidfromlink.php?link=$link",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,vi;q=0.8',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        if ($err) {
            return [
                'error' => $err
            ];
        }
        curl_close($curl);
        if ($response) {
            $response = json_decode($response);
            if (isset($response->id)) {
                return [
                    'object_id' => $response->id
                ];
            }
        }
        return [
            'error' => 'Get Profile Id Error'
        ];
    }
}
