<?php
function miaopai($url){
    $arr = array();
    preg_match('|http://www.miaopai.com/show/([^\']+)\.htm|si',$url,$arr);
    $fdata['video_url'] = 'http://wsqncdn.miaopai.com/stream/'.$arr[1].'.mp4';
    $c = file_get_contents($url); 

    $arr = array();

    preg_match('|<div class="head_icon2"><a title=\'[^\']+\' href=\'[^\']+\' target=\'_blank\'><img src="([^"]+)|si',$c,$arr);

    $fdata['avatar'] = trim($arr[1]);

    $arr = array();
    preg_match('|<meta property="og:image" content="([^"]+)"/>|si',$c,$arr);
    if(empty($arr[1])) return false;

    $fdata['screenshot'] = trim($arr[1]);

    $arr = array();
    preg_match('|<div class="introduction">(.*?)<div class="talk">|si',$c,$arr);
    $arr1 = array();
    preg_match('|<p>([^<]+)|si',$arr[1],$arr1);
    $fdata['description'] = !empty($arr1[1]) ? trim(strip_tags($arr1[1])) : '';

    $arr1 = array();
    preg_match("|<h1><a title='([^\']+)' href='([^\']+)'|si",$arr[1],$arr1);
    $fdata['author'] = trim(strip_tags($arr1[1]));

    $arr = array();
    preg_match('|<h2><b>([^&]+)&nbsp;&nbsp;&nbsp;([^<]+) 观看|si',$c,$arr);
    $xdd = isset($arr[1]) ? $arr[1] : '';
    if(stristr($xdd,'今天'))
    {
        $xdd = date('Y-m-d').' '.$xdd;
        $miaopai_publish_time = date('Y-m-d',strtotime($xdd));
    } else {
        if(substr_count($xdd,'-') == 1) {
            $xdd = date('Y').'-'.$xdd;
        }

        $miaopai_publish_time = date('Y-m-d',strtotime($xdd));
    }
    $this_day = date('Y-m-d');
    if($miaopai_publish_time > $this_day){
        $miaopai_publish_time  = str_replace('2016', '2015', $miaopai_publish_time);
    }
    $fdata['miaopai_publish_time'] = $miaopai_publish_time;
    
    $views = $arr[2];
    if(stristr($views,'万')){
        $views = str_replace("万","",$views);
        $views = $views*10000;
    }   
    $fdata['miaopai_views'] = $views;
    $fdata['miaopai_url'] = $url;
    return $fdata;  
}
$mdata = miaopai('http://www.miaopai.com/show/F-Nk57-sefv8uezyWab3uA__.htm');
print_r($mdata);
/*Array
(
    [video_url] => http://wsqncdn.miaopai.com/stream/F-Nk57-sefv8uezyWab3uA__.mp4
    [avatar] => http://dynimg3.yixia.com/square.124/storage.video.sina.com.cn/user-icon/Mxqmv0u3Z6DoDF9o_480__1440473911096.jpg
    [screenshot] => http://wscdn.miaopai.com/stream/F-Nk57-sefv8uezyWab3uA___tmp_11_617_.jpg
    [description] => 3 分钟早餐｜吐司叉烧包＃美食＃
    [author] => 微在涨姿势
    [miaopai_publish_time] => 2016-05-16
    [miaopai_views] => 3160000
    [miaopai_url] => http://www.miaopai.com/show/F-Nk57-sefv8uezyWab3uA__.htm
)
*/
