<?php
####################################################################################################################
#
#       20190727 1759 luoxiaojin 国际苗文与国内苗文互转 php 版
#
#               20190727 2216 测试通过 luoxiaojin
#
#                   xiaojin_luo@foxmail.com
####################################################################################################################

// 依照对照表获取全部声韵调字母对应关系
function getChars()
{
    $sd = '[["b","b"],["x","j"],["d","v"],["l","s"],["s","g"],["k","s"],["f","m"],["t",""]]';
    $ym1 = '[["iang","_"],["uang","_"],["ang","a"],["iao","_"],["ong","oo"],["uai","_"],["ai","ai"],["ao","o"],["en","ee"],["er","_"],["eu","aw"],["in","_"],["iu","_"],["ou","au"],["ua","ua"],["ue","_"],["un","_"],["a","ia"],["e","e"],["i","i"],["o","u"],["u","w"]]';
    $ym2 = '[["ong","oo"],["ai","ai"],["en","ee"],["eu","aw"],["ou","au"],["ua","ua"],["a","ia"],["iang","_"],["uang","_"],["ang","a"],["iao","_"],["uai","_"],["ao","o"],["er","_"],["in","_"],["iu","_"],["ue","_"],["un","_"],["e","e"],["i","i"],["o","u"],["u","w"]]';
    $sm1 = '[["npl","nplh"],["nch","ntsh"],["nbl","npl"],["ntl","ndh"],["ntr","nrh"],["nzh","nts"],["hny","hny"],["nkh","nqh"],["ndl","nd"],["ndr","nr"],["ngh","nq"],["ngg","_"],["nc","ntxh"],["np","nph"],["pl","plh"],["nz","ntx"],["nt","nth"],["ch","tsh"],["nq","nch"],["nk","nkh"],["nb","np"],["hm","hm"],["bl","pl"],["nd","nt"],["hn","hn"],["tl","dh"],["hl","hl"],["tr","rh"],["zh","ts"],["nj","nc"],["ny","ny"],["ng","nk"],["kh","qh"],["dl","d"],["dr","r"],["sh","s"],["gh","q"],["c","txh"],["p","ph"],["z","tx"],["t","th"],["q","ch"],["x","xy"],["k","kh"],["b","p"],["m","m"],["f","f"],["v","v"],["s","x"],["d","t"],["n","n"],["l","l"],["r","z"],["j","c"],["y","y"],["g","k"],["h","h"],["w","_"]]';
    $sm2 = '[["npl","nplh"],["nch","ntsh"],["nc","ntxh"],["nbl","npl"],["ntl","ndh"],["ntr","nrh"],["nzh","nts"],["hny","hny"],["nkh","nqh"],["np","nph"],["pl","plh"],["nz","ntx"],["nt","nth"],["ch","tsh"],["nq","nch"],["nk","nkh"],["c","txh"],["ndl","nd"],["ndr","nr"],["ngh","nq"],["nb","np"],["hm","hm"],["bl","pl"],["nd","nt"],["hn","hn"],["tl","dh"],["hl","hl"],["tr","rh"],["zh","ts"],["nj","nc"],["ny","ny"],["ng","nk"],["kh","qh"],["p","ph"],["z","tx"],["t","th"],["q","ch"],["x","xy"],["k","kh"],["ngg","_"],["dl","d"],["dr","r"],["sh","s"],["gh","q"],["b","p"],["m","m"],["f","f"],["v","v"],["s","x"],["d","t"],["n","n"],["l","l"],["r","z"],["j","c"],["y","y"],["g","k"],["h","h"],["w","_"]]';

    $sd = json_decode($sd);
    $sm1 = json_decode($sm1);
    $sm2 = json_decode($sm2);
    $ym1 = json_decode($ym1);
    $ym2 = json_decode($ym2);

    return compact("sd", "sm1", "sm2", "ym1", "ym2");
}
// 判断苗文类型
function getCharsType($content)
{
    // return 说明 LAO-》老挝苗文 CN-》国内苗文 ERROR-》语法错误，既不是也不是 NOTSURE-》不能确定
    if (count($content) < 1) return "ERROR";
    // 老挝苗文特征->特有的声调
    $lao_re = "/\B['jvgm']{1}\b/i";
    // 国内苗文特征->特有的声调
    $ch_re = "/\B['txkfl']{1}\b/i";
    // 共同特征->公共声调标识
    $mix_re = "/\B['bsd']{1}\b/i";

    if (!preg_match($ch_re, $content) && preg_match($lao_re, $content)) {
        // echo "老挝苗文";
        return "LAO";
    }
    if (preg_match($ch_re, $content) && !preg_match($lao_re, $content)) {
        // echo "国内苗文";
        return "CN";
    }
    if (!preg_match($ch_re, $content) && !preg_match($lao_re, $content) && preg_match($mix_re, $content)) {
        // echo "不确定";
        return "NOTSURE";
    }
    return "ERROR";
}

// 国内转国外
function convert2Lao($content)
{
    $chars = getChars();
    $sd = $chars['sd'];
    $sm = $chars['sm1'];
    $ym = $chars['ym1'];
    // 预处理
    $content = " " . $content . " ";
    $content = str_replace("&", "^_^", $content);

    // 声母转换
    foreach ($sm as $value) {
        $pattern = "/[^&]\b" . $value[0] . "\B/i";
        $content = preg_replace($pattern, "&" . $value[1], $content);
    }
    $content = str_replace("&", " ", $content);
    // 韵母转换
    foreach ($ym as $value) {
        $pattern = "/(?<!&)" . $value[0] . "(?!&)/im";
        $content = preg_replace($pattern, "&" . $value[1] . "&", $content);
    }
    $content = str_replace("&", "", $content);
    // 声调转换
    foreach ($sd as $value) {
        $pattern = "/\B(?!&)" . $value[0] . "(?!&)\b/im";
        $content = preg_replace($pattern, "&" . $value[1] . "&", $content);
    }
    $content = str_replace("&", "", $content);
    // 末处理
    $content = str_replace("^_^","&",$content);
    $content = trim($content);
    return $content;
}

// 国外转国内
function convert2Cn($content)
{
    $content = " ".$content." ";
    $content = str_replace("&","^_^",$content);
    $chars = getChars();
    $sd = $chars['sd'];
    $sm = $chars['sm2'];
    $ym = $chars['ym2'];
    // 声母
    foreach ($sm as $value) {
        if ($value[1] != "_") {
            $pattern = "/[^&]\b" . $value[1] . "\B/i";
            $content = preg_replace($pattern,"&". $value[0], $content);
        }
        // $pattern = "/[^&*]\b" . $value[0] . "\B/i";
        // $content = preg_replace($pattern, "&*" . $value[1], $content);
    }
    $content = str_replace("&", " ", $content);
    // 声调
    foreach ($sd as $value) {
        $pattern = "/\B(?<!&)" . $value[1] . "(?!&)\b/i";
        $content = preg_replace($pattern, "&" . $value[0] . "&", $content);
    }
    $content = str_replace("&", "", $content);
    // 韵母
    foreach ($ym as $value) {
        if ($value[1] != "_") {
            $pattern = "/(?<!&)" . $value[1] . "(?!&)/i";
            $content = preg_replace($pattern, "&" . $value[0] . "&", $content);
        }
    }
    $content = str_replace("&", "", $content);
    // 空白调处理
    foreach ($ym as $value) {
        $pattern = "/" . $value[0] . "\b/i";
        $content = preg_replace($pattern, $value[0] . "t", $content);
    }
    $content = str_replace("^_^", "&", $content);
    $content = trim($content);
    return $content;
}

// 初始化
function init(){
    $content = isset($_POST['content'])?trim($_POST['content']):"Kuv Los Hlub Koj Thiab Os!";
    $type = getCharsType($content);
    if($type==="LAO"){
        return convert2Cn($content);
    }else if($type==="CN"){
        return convert2Lao($content);
    }
    return "输入内容太少或有误！";
}

echo init();