    <style>
       [v-cloak] { display: none }
    </style>
    <script src="/media/vendor/vue/vue.js"></script>   
    <div id="box">
	    <div class="list-header">
	       <div class="left" style="display:none;" @click="back()" v-show="back_show">上一级</div>
	       <div class="title" v-cloak>{{title}}</div>
	       <div class="right"></div>
	    </div>
    
	    <div class="weui-panel category-content">
	        <div class="category-item" v-for="item in arr">
	           <div class="category-inner-item" @click="detail(item)">
	               <span v-cloak>{{item.label}}</span>
	               <span class="category-jt"></span>
	           </div>
	        </div>

            <div class="nodata" v-if="category.length == 0" style="display:none;" v-show="category.length > 0">没有分类数据</div>
	        <div class="clear"></div>
	    </div>
	    
	    <div class="look-all-category-area">
	        <a href="/mall/goodslist?key={{all}}">
	            <div class="look-all-category">查看全部商品</div>
	        </a>
	    </div>
	    <br/>
    </div>
    
    <script>
       var category = <?php echo json_encode($category);?>;
       var arr = <?php echo json_encode($category);?>;
       var init_arr = [];
       for(var i=0; i<arr.length; i++){
           if(arr[i].level == 2){
        	   init_arr.push(arr[i]);
           }
       }
       
       window.onload = function(){
           new Vue({
               el: '#box',
               data: {
        	       category: category,
        	       arr: init_arr,
        	       all: 'all',
        	       back_show: false,
        	       title: '商品分类'
               },
               methods:{
            	   detail: function(param){
            	       if(param.level == 2){
            	    	   location.href = '/mall/goodslist?key=' + param.key;
            	    	   return false;
            	    	   
            	    	   this.arr = [];
                	       for(var i=0; i<category.length; i++){
	       	                   var searchKey = param.path + '/';
	       	                   var item_str = category[i].path;
	       	                   var index = item_str.indexOf(searchKey);
	       	             
                    	       if(item_str.indexOf(searchKey) != -1){
                	               this.arr.push(category[i]);
                    	       }
                	       }
                	       this.all = param.key;

                	       location.hash = "#sub";
                	       this.back_show = true;
                	       this.title = param.label;
            	       }
            	       else{
                	       location.href = '/mall/goodslist?key=' + param.key;
            	       }
            	   },
            	   back: function(){
            		   location.hash = "#";
            		   this.arr = init_arr;
            		   this.back_show = false;
            		   this.all = 'all';
            		   this.title = '商品分类';
                   }
               }
           });
       }
    </script>
