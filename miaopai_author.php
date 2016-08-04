<?php
function wk_get($url,$refer='') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = array();
    $headers[] = 'Cache-Control: no-cache, must-revalidate';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Content-Encoding: gzip';
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Server: Tengine';

    $headers[] = 'ATransfer-Encoding:chunked';
    $headers[] = 'X-Via:1.1 yc6:8111 (Cdn Cache Server V2.0), 1.1 wenzhoudianxin10:5 (Cdn Cache Server V2.0)';


    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt ($ch,CURLOPT_REFERER,$refer);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

 function miaopai_author($source_url) {
    $c = file_get_contents($source_url);
    preg_match('|var suids="([^"]+)|si',$c,$arr);
    $suid = trim($arr[1]);
    for($i=1;$i<10;$i++)
    {
        $newurl = 'http://www.miaopai.com/gu/u?page='.$i.'&suid='.$suid.'&fen_type=channel';
        $c1 = wk_get($newurl);
        $arr = array();
        $arr = json_decode($c1, true);
        if(empty($arr)) continue;
        $tmp = array();
        preg_match_all('|<div class="D_video"(.*?)</div></div>|si',$arr['msg'],$tmp);
        foreach($tmp[1] as $k=>$v) {
            $arr1 = array();

            preg_match('|t="blank" href="([^"]+)">赞|si',$v,$arr1);
            $source_url = $arr1[1];
            //$fdata['author'] = $data['author'];

            $arr1 = array();
            preg_match('|t="blank" href="([^"]+)">赞|si',$v,$arr1);
            $source_url = $arr1[1];
            $fdata['source_url'] = $source_url;

            $c = wk_get($source_url);

            $arr2 = array();
            preg_match('|<div class="introduction">(.*?)<div class="talk">|si',$c,$arr2);
            $arr1 = array();
            preg_match('|<p>(.*?)</p>\s+</div>|si',$arr2[1],$arr1); 
            $fdata['description'] = trim(strip_tags($arr1[1]));
       
            $arr1 = array();
            preg_match("|<h1><a title='([^\']+)' href='([^\']+)'|si",$arr2[1],$arr1);
            $fdata['author'] = trim(strip_tags($arr1[1]));

            $arr1 = array();
            preg_match('|http://www.miaopai.com/show/([^\']+)\.htm|si',$source_url,$arr1);
            $fdata['video_url'] = 'http://wsqncdn.miaopai.com/stream/'.$arr1[1].'.mp4';

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
            $fdata['publish_time'] = $miaopai_publish_time;
            
            $views = $arr[2];
            $views = str_replace(",","",$views);

            if(stristr($views,'万')){
                $views = str_replace("万","",$views);
                $views = $views*10000;
            }   
            $fdata['views'] = $views;

            $fdata['fetch_time'] = date('Y-m-d H:i:s');
            print_r($fdata);
        }
    }
}
miaopai_author('http://www.miaopai.com/u/paike_vhz2smxnyc');
