/**
 * Created by 1 on 2016-10-14.
 */
console.log("初始化v_app");
Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month
        "d+": this.getDate(), //day
        "h+": this.getHours(), //hour
        "m+": this.getMinutes(), //minute
        "s+": this.getSeconds(), //second
        "q+": Math.floor((this.getMonth() + 3) / 3)
    }
    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
}

var v_app = new Vue({
    components: {
        'by-alert': by_alert
    },
    el: '#v_app',
    http: {
        root: '',
        headers:{
            "Content-type":"application/x-www-form-urlencoded"
        }
    },
    created: function() {
        if(this.api_json_url.length > 0){
            console.log(this.api_json_url);
            this.selected_json_url = this.api_json_url[0].url;
        }


    },
    data: {
        //标记接口描述文件是否载入完毕
        myCodeMirror:'',
        is_json_parsed:false,
        selected_json_url: '',
        api_json_url:[
            {title:'纺织快车guannan.itboye.com',url:'http://api.guannan.itboye.com/public/app.json'},
            {title:'经贸四方合作',url:'https://api.itboye.com/public/json/jmsf.json'},
            {title:'住家接口测试',url:'https://api.itboye.com/public/json/ihomebank.json'},
            {title:'虎头奔接口测试',url:'http://api.hutouben.itboye.com/public/app.json'}
        ],

        options: [
            { text: 'POST请求', value: 'POST' },
            { text: 'GET请求', value: 'GET' },
            { text: 'PUT请求', value: 'PUT' },
            { text: 'DELETE请求', value: 'DELETE' },
        ],
        selected_api_type:"",
        selected_api_ver:"",
        api_data: {
            client_id:'',
            client_secret:'',
            selected_api_method: 'POST',
            selected_api_url:"",
            // last_modified:'',
            project_name:"",
            url_list:[],
            api_list:[],
            meta_list:[],
            current_api_info:{},
            results:[]
        }

    },
    mounted:function () {
        console.log("mounted");
    },

    updated:function(ev){
        console.log("update",ev);
    },
    watch: {
        selected_api_type:function (newVal,oldVal) {
            this.selected_api_ver = "";
        }
    },

    methods:{
        highlight:function () {
        },
        reverse_result:function () {
            console.log('reverse_result',this.api_data.results.length);
            if(this.api_data.results.length > 0){
                var tmp = [] ;
                for (var i=this.api_data.results.length-1;i>=0;i--){
                    tmp.push({'time':this.api_data.results[i].time,'info':this.api_data.results[i].info});
                }

                console.log(this.api_data.results,tmp);

                return tmp;
            }
            return this.api_data.results;
        },
        clear_result:function(){
            this.api_data.results = [];
        },
        //返回选中服务接口的信息
        selected_api_info:function () {
            this.current_api_info = {};
            for(var i=0;i<this.api_data.api_list.length;i++){
                var api_info = this.api_data.api_list[i];
                for(var j=0;j < api_info.list.length; j++ ) {
                    if (api_info.list[j].type == this.selected_api_type) {
                        this.current_api_info = api_info.list[j];
                        return this.current_api_info;
                    }
                }
            }

            return "";
        },
        //调用api
        request_api:function () {
            var api_test_url = "https://api.itboye.com/public/index.php/Index/test";
            // var api_test_url = "http://localhost:8070/github/itboye_common_api/public/front.php/Index/test";

            var params =  this.current_api_info.params;

            var data = new FormData();
            for (var i=0;i<params.length;i++){
                var key = params[i].key ;
                data.append(key,params[i].value);
            }

            data.append("alg",this.api_data.alg);
            data.append("api_type",this.selected_api_type);
            data.append("api_ver",this.selected_api_ver);
            data.append("api_url",this.api_data.selected_api_url);
            data.append("client_id",this.api_data.client_id);
            data.append("client_secret",this.api_data.client_secret);

            var options = {
                'method': this.selected_api_method,
                'timeout': 20000
            };
            console.log("业务数据参数",data);
            api_test_url = api_test_url + "?v="+ Date.parse(new Date());

            this.$http.post(api_test_url,data,options).then((response) => {
                console.log("*API*-- api调用成功！");
                console.log(response);
                var result = response.body;
                console.log(result);
                // this.clear_result();
                var result_tmp = {
                    'info':result,
                    'time': new Date().format("yyyy-M-d h:m:s")
                };
                if(result){
                    var unescapResult = unescape(result.replace(/\\u/g, "%u"));

                    if(!this.myCodeMirror){
                        var myTextArea = document.getElementById("codemirror");
                        this.myCodeMirror = CodeMirror.fromTextArea(myTextArea,{
                            mode : "text/javascript",
                            lineNumbers : true,
                            lineWrapping:true,
                            foldGutter: true,
                            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                            json:true
                        });
                        this.myCodeMirror.setSize('auto','auto');
                    }
                    unescapResult = js_beautify(unescapResult);
                    
                    document.getElementById('codemirror').innerHtml = unescapResult;

                    console.log(this.myCodeMirror,unescapResult);
                    this.myCodeMirror.getDoc().setValue(unescapResult);
                    this.myCodeMirror.refresh();


                    this.api_data.results.push(result_tmp);
                    console.log("请求结果缓存",this.api_data.results.length);
                }else{
                    this.api_data.results.push({
                        'info':'请求失败',
                        'time': new Date().format("yyyy-M-d h:m:s")});
                }
            }, (response) => {
                console.log("*API*-- api调用失败！");
                console.log(response);
            });
        },
        request_json_file:function () {
            // var url = "http://localhost:8070/github/itboye_common_api/public/front.php/Index/read_xml";
            var url = "https://api.itboye.com/public/index.php/Index/read_xml";
            this.is_json_parsed = false;
            console.log("*API*-- ",this.selected_json_url);
            if(this.selected_json_url){
                var data = new FormData();
                data.append('file_url',this.selected_json_url);
                
                this.$http.post(url,(data),{'content-type':'application/x-www-form-urlencoded'}).then((response) => {
                    console.log("*API*-- 读取xml文件成功！");
                this.jsonpcallback(response);
            }, (response) => {
                    console.log("*API*-- 读取xml文件失败！");
                    console.log(response);
                });

            }else{
                console.log("请先选择xml地址");
            }
        },
        jsonpcallback:function (data) {
            //解析接口描述文件
            console.log(data);

            var content   = data.body;
            content = JSON.parse(content);
            // var date   =  new Date(content.last_modified);
            //
            // this.api_data.last_modified = date.format("yyyy-MM-dd h:m:s");
            console.log(content);

            this.api_data.project_name = content.project_name;
            this.api_data.url_list = content.url_list;
            this.api_data.meta_list = content.meta_list;
            this.api_data.api_list = content.api_list;
            this.api_data.alg      = content.alg;
            this.api_data.client_id = content.client_id;
            this.api_data.client_secret = content.client_secret;

            this.is_json_parsed = true;
            if(this.api_data.url_list.length > 0){
                this.api_data.selected_api_url =  this.api_data.url_list[0].url;
            }
            if(this.api_data.api_list.length > 0){
                var api_info = this.api_data.api_list[0];
                if(api_info.list.length > 0){
                    this.selected_api_type = api_info.list[0].type;
                    if(api_info.list[0].version.length > 0) {
                        this.selected_api_ver = api_info.list[0].version[0];
                    }
                }
            }

        },

    }
});