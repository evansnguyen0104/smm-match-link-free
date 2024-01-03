<?php


namespace Services\Instagram;


class LazadaService
{
    protected function checkCharExistStr($str, $char)
    {
        if (strpos($str, $char) !== false) {
            return true;
        }
        return false;
    }

    public function matchObjectIdFromLazadaLink($link, $type = 'follow')
    {
        if ($type == 'follow') {
            $re = '/https:\/\/www.lazada.vn\/shop\/(.*)/m';
            $str = $this->reconstructUrl($link);
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
            if (!isset($matches[0][1])) {
                return [
                    'error' => 'Link phải có dạng https://www.lazada.vn/shop/{username_shop}...',
                    'status' => 400
                ];
            }
            if ($this->checkCharExistStr($matches[0][1], "http")) {
                return [
                    'error' => 'Link phải có dạng https://www.lazada.vn/shop/{username_shop}...',
                    'status' => 400
                ];
            }
            return str_replace('/', '', $matches[0][1]);
        }

        if ($type == 'like') {
            $re = '/https:\/\/www.lazada.vn\/products\/(.*)/m';
            $str = $this->reconstructUrl($link);
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
            if (!isset($matches[0][1])) {
                return [
                    'error' => 'Link phải có dạng https://www.lazada.vn/products/{alias_san_pham}...',
                    'status' => 400
                ];
            }
            return str_replace('.html', '', $matches[0][1]);
        }
        return [
            'error' => 'Link không đúng định dạng',
            'status' => 400
        ];
    }

    protected function reconstructUrl($url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['scheme'])) {
            return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'];
        }
        return $url;
    }
}
