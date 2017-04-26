   var vm = null;
   window.onload = function(){
	   Vue.prototype.$http = axios;
	   vm = new Vue({
           el: '#box',
           data: {
    	       arr: [],
    	       page: 1,
    	       num: 4,
    	       totalPage: 1,
    	       ovar: {},
    	       isLoading: true,
    	       loadMore: false,
    	       isBottom: false,
    	       hasData: true
           },
           methods:{
	           getData: function(){
	               this.isLoading = true;
	        	   if(this.page == 1){
	        		   this.showLoading();
	        	   }
	        	   
	        	   var url = '/index.php/mall/timebuy_page_data';
		           var str = 'page=' + this.page + '&num=' + this.num;
        	       this.$http.post(url, str).then((response)=>{
        	    	   if(response.status == 200 && response.data && response.data.code == 0){
        	    		   this.hasData = true;
	        	    	   this.totalPage = Math.ceil(response.data.data.count/this.num);
	        	    	   var datalist = response.data.data.data;
	        	    	   if(this.page < this.totalPage){
		   					   this.isBottom = false;
		   					   this.loadMore = true;
		   				   }
		   				   else{
			   				   this.isBottom = true;
		   					   this.loadMore = false;
		   				   }
	        	    	   
	        	    	   for(var i=0; i<datalist.length; i++){
	        	    		   if(datalist[i].model == 1){
									var exchange = '购物券' + datalist[i].point_price + '分';
							   }
							   else if(datalist[i].model == 2){
									var exchange = '消费积分' + datalist[i].point_price + '分';
							   }
							   else{
									var exchange = '消费积分' + datalist[i].point_price + '分 + 现金' + datalist[i].cash_price + '元';
							   }
	        	    		   datalist[i].exchange = exchange;
	        	    		   datalist[i].link_url = "/mall/detail?key=" + datalist[i].key;
	        	    		   datalist[i].img_url = "/backend/uploads/" + datalist[i].face;
	        	    		   datalist[i].kucun = Number(datalist[i].sales_volume) + Number(datalist[i].total);
	        	    		   datalist[i].percent = (Number(datalist[i].sales_volume)/datalist[i].kucun) * 100 + '%';
	        	    		   var time = (Number(datalist[i].buy_time_seconds) * 1000 - (new Date()).getTime())/1000;
	        	    		   if(Number(datalist[i].sales_volume) == datalist[i].kucun){
	        	    			   datalist[i].sale_over = true;
	        	    		   }
	        	    		   else{
	        	    			   datalist[i].sale_over = false;
	        	    		   }
	        	    		   
	    		        	   if(time >= 0){
	    			        	   var day = Math.floor(time/(24 * 60 * 60));
	    						   var hour = Math.floor((time - (24 * 60 * 60) * day)/(60 * 60));
	    						   var minute = Math.floor((time - (24 * 60 * 60) * day - (60 * 60) * hour)/60);
	    						   var second = Math.floor(time - (24 * 60 * 60) * day - (60 * 60) * hour - minute * 60);
	    						   datalist[i].day_html = day >= 10 ? day : '0' + day;
	    						   datalist[i].hour_html = hour >= 10 ? hour : '0' + hour;
	    						   datalist[i].minute_html = minute >= 10 ? minute : '0' + minute;
	    						   datalist[i].second_html = second >= 10 ? second : '0' + second;
	    						   datalist[i].is_over = false;
	    		        	   }
	    		        	   else{
	    		        		   datalist[i].day_html = '00';
	    		        		   datalist[i].hour_html = '00';
	    		        		   datalist[i].minute_html = '00';
	    		        		   datalist[i].second_html = '00';
	    		        		   datalist[i].is_over = true;
	    		        	   }
	    		        	   this.ovar['var_'+i] = null; 
		        	    	   this.arr.push(datalist[i]);
	        	    	   }
	        	    	   this.timeup();
        	    	   }
        	    	   else{
        	    		   this.hasData = false;
        	    		   this.isBottom = false;
	   					   this.loadMore = false;
        	    	   }
        	    	   if(this.page == 1){
        	    	       layer.closeAll('loading');
        	    	   }
        	    	   this.isLoading = false;
        	       });
	           },
	           timeup(){
	        	   var temp = setInterval(function(){
	        		   for(var i=0; i<this.arr.length; i++){
	        			   var data = this.arr[i];
						   var time = Math.ceil((data.buy_time_seconds * 1000 - (new Date()).getTime())/1000);

						   if(time >= 0){
							   var day = Math.floor(time/(24 * 60 * 60));
							   var hour = Math.floor((time - (24 * 60 * 60) * day)/(60 * 60));
							   var minute = Math.floor((time - (24 * 60 * 60) * day - (60 * 60) * hour)/60);
							   var second = Math.floor(time - (24 * 60 * 60) * day - (60 * 60) * hour - minute * 60);
							   data.day_html = day >= 10 ? day : '0' + day;
							   data.hour_html = hour >= 10 ? hour : '0' + hour;
							   data.minute_html = minute >= 10 ? minute : '0' + minute;
							   data.second_html = second >= 10 ? second : '0' + second;
							   data.is_over = false;
						   }
						   else{
							   data.day_html = '00';
							   data.hour_html = '00';
							   data.minute_html = '00';
							   data.second_html = '00';
							   data.is_over = true;
	  						   this.ovar['var_'+i] = null; 
			        	   }
	        		   }
				   }.bind(this), 1000);
	           },
	           showLoading(){
	           	   var index = layer.load(2, {
	       		       shade: [0.1,'#fff'] //0.1透明度的白色背景
	       		   });
	           },
	           hideLoading(){
	        	   layer.closeAll('loading');
	           }
           },
           mounted(){
        	   this.getData();
           }
       });
   }

   $(function(){
	   $(window).scroll(function(){  
		    // 当滚动到最底部以上100像素时， 加载新内容  
		    if ($(document).height() - $(this).scrollTop() - $(this).height()<100) {
		    	if(!vm.isLoading && vm.page < vm.totalPage){
		    		vm.loadMore = true;
		    		vm.page++;
		    		vm.getData();
		    	}
		    };  
		});  
   });