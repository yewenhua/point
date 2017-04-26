    <!-- 
    <div class="list-header">
       <div class="left goback">返回</div>
       <div class="title">复投列表</div>
       <div class="right"></div>
    </div>
     -->
     
    <style>
        .page__bd{
            height: -moz-calc(100% - 60px);
			height: -webkit-calc(100% - 60px);
			height: calc(100% - 60px);
		}
		.weui-navbar{
		    position: fixed;
		}
    </style>
     
    <div class="page__bd">
        <div class="weui-tab">
            <div class="weui-navbar">
                <div class="weui-navbar__item weui-bar__item_on" data-type="1">进行中</div>
                <div class="weui-navbar__item" data-type="2">已过期</div>
                <div class="weui-navbar__item" data-type="3">待开启</div>
            </div>
            <div class="weui-tab__panel">
                <div id="list" class="wait_record"></div>
			    <div class="load-more" style="display:none;">点击加载更多</div>
			    <div class="list-bottom-last" style="display:none;">我是有底线的</div>
            </div>
        </div>
    </div>

    
    

