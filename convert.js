   //**************************************************************************************************************
   // 
   //    luoxiaojin 20190727 1106 国际苗文于国内苗文互相转换工具Js版
   //
   // 
   //**************************************************************************************************************


   // 依照对照表获取全部声韵调字母对应关系
   function getChars() {
       // 依据声韵母对照表整理
       let sm = "";
       sm += "b-p,p-ph,nb-np,np-nph,m-m,hm-hm,f-f,v-v,bl-pl,pl-plh,nbl-npl,npl-nplh,z-tx,c-txh,nz-ntx,nc-ntxh,";
       sm += "s-x,d-t,t-th,nd-nt,nt-nth,n-n,hn-hn,dl-d,tl-dh,ndl-nd,ntl-ndh,l-l,hl-hl,dr-r,tr-rh,";
       sm += "ndr-nr,ntr-nrh,zh-ts,ch-tsh,nzh-nts,nch-ntsh,sh-s,r-z,j-c,q-ch,nj-nc,nq-nch,ny-ny,hny-hny,x-xy,y-y,";
       sm += "g-k,k-kh,ng-nk,nk-nkh,ngg-_,h-h,gh-q,kh-qh,ngh-nq,nkh-nqh,w-_";
       let ym = "";
       ym += "a-ia,ai-ai,ang-a,ao-o,e-e,en-ee,er-_,eu-aw,i-i,iang-_,iao-_,in-_,iu-_,o-u,ong-oo,ou-au,u-w,ua-ua,";
       ym += "uai-_,uang-_,ue-_,un-_";
       let sd = "";
       sd = "b-b,x-j,d-v,l-s,t-,s-g,k-s,f-m";

       // 将字符串切割成一一对应的数组元素
       sm = sm.split(",");
       ym = ym.split(",");
       sd = sd.split(",");
       let chars = [sm, ym, sd];
       for (char of chars) {
           for (let i in char) {
               char[i] = char[i].split("-");
           }
       }
       return chars;
   }

   // 第一个比较器，数组元素字符长度长的排在前面
   function fcmp(first, second) {
       if (first[0].length > second[0].length) {
           return -1;
       } else if (first[0].length < second[0].length) {
           return 1;
       } else {
           return 0;
       }
   }

   // 第二个比较器，数组元素字符长度长的排在前面
   function scmp(first, second) {
       if (first[1].length > second[1].length) {
           return -1;
       } else if (first[1].length < second[1].length) {
           return 1;
       } else {
           return 0;
       }
   }

   // 判断苗文类型
   function getType(content) {
       // return 说明 LAO-》老挝苗文 CN-》国内苗文 ERROR-》语法错误，既不是也不是 NOTSURE-》不能确定
       if (content.length < 1) return "ERROR";
       // 老挝苗文特征->特有的声调
       let lao_re = /\B['jvgm']{1}\b/i;
       // 国内苗文特征->特有的声调
       let ch_re = /\B['txkfl']{1}\b/i;
       // 共同特征->公共声调标识
       let mix_re = /\B['bsd']{1}\b/i;

       if (!ch_re.test(content) && lao_re.test(content)) {
           console.log("老挝苗文");
           return "LAO";
       }
       if (ch_re.test(content) && !lao_re.test(content)) {
           console.log("国内苗文");
           return "CN";
       }
       if (!ch_re.test(content) && !lao_re.test(content) && mix_re.test(content)) {
           console.log("不确定");
           return "NOTSURE";
       }
       return "ERROR";
   }

   // 国内转国外
   function convert2Lao(content) {
       // 预处理
       content = " " + content + " ";
       content = content.replace(/\&/ig, "^_^");

       let chars = getChars();
       let sm = chars[0];
       let ym = chars[1];
       let sd = chars[2];
       sm.sort(fcmp);
       ym.sort(fcmp);
       sd.sort(fcmp);
       // 声母转换
       for (value of sm) {
           let pattern = new RegExp("[^&]\\b" + value[0] + "\\B", "igm");
           content = content.replace(pattern, "&" + value[1]);
       }
       content = content.replace(/&/ig, " ");
       // 韵母转换
       for (value of ym) {
           let pattern = new RegExp("(?<!&)" + value[0] + "(?!&)", "igm");
           content = content.replace(pattern, "&" + value[1] + "&");
       }
       content = content.replace(/&/ig, "");
       // 声调转换
       for (value of sd) {
           let pattern = new RegExp("\\B(?<!&)" + value[0] + "(?!&)\\b", "igm");
           content = content.replace(pattern, "&" + value[1] + "&");
       }
       content = content.replace(/&/ig, "");
       // 末处理
       content = content.replace(/\^\_\^/ig, "&");
       content = content.trim();
       return content;
   }

   // 国外转国内
   function convert2Cn(content) {
       // 预处理
       content = " " + content + " ";
       content = content.replace(/\&/ig, "^_^");

       let chars = getChars();
       let sm = chars[0];
       let ym = chars[1];
       let sd = chars[2];
       sm.sort(scmp);
       ym.sort(scmp);
       sd.sort(scmp);
       // 声母
       for (value of sm) {
           if (value[1] != "_") {
               let pattern = new RegExp("[^&]\\b" + value[1] + "\\B", "igm");
               content = content.replace(pattern, "&" + value[0]);
           }
       }
       content = content.replace(/&/ig, " ");
       // 声调
       for (value of sd) {
           let pattern = new RegExp("\\B(?<!&)" + value[1] + "(?!&)\\b", "igm");
           content = content.replace(pattern, "&" + value[0] + "&");
       }
       content = content.replace(/&/ig, "");
       // 韵母
       for (value of ym) {
           if (value[1] != "_") {
               let pattern = new RegExp("(?<!&)" + value[1] + "(?!&)", "igm");
               content = content.replace(pattern, "&" + value[0] + "&");
           }
       }
       content = content.replace(/&/ig, "");
       // 空白调(ua调)处理
       for (value of ym) {
           let pattern = new RegExp(value[0] + "\\b", "igm");
           content = content.replace(pattern, value[0] + "t");
       }
       // 末处理
       content = content.replace(/\^\_\^/ig, "&");
       content = content.trim();
       return content;
   }

   // 检查浏览器支持情况
   function isSupport() {
       let pattern = undefined;
       try {
        //    firefox浏览器不支持反向肯定|否定预查
           pattern = new RegExp("(?<!SUPPORT)", "i");
       } catch (error) {
           // console.log(error);
       }
       if (pattern != undefined) {
           return true;
       }
       return false;
   }