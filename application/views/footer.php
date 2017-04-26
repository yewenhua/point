	    <div class="footer-placeholder"></div>
	    <div class="footer-menu">
	        <div class="footer-item <?php if($menu == 'mall'){echo 'active';}?>">
	            <a href="/mall/center">
		            <div class="footer-item-img mall"></div>
		            <div class="footer-item-title">首页</div>
	            </a>
	        </div>
	        <div class="footer-item <?php if($menu == 'category'){echo 'active';}?>">
	            <a href="/mall/category">
		            <div class="footer-item-img category"></div>
		            <div class="footer-item-title">分类</div>
	            </a>
	        </div>
	        <div class="footer-item <?php if($menu == 'order'){echo 'active';}?>">
	            <a href="<?php if(isset($type) && $type == 'service'){echo '/service/record';}else{echo '/orders/lists';}?>">
		            <div class="footer-item-img order"></div>
		            <div class="footer-item-title"><?php if(isset($type) && $type == 'service'){echo '我的报单';}else{echo '我的订单';}?></div>
	            </a>
	        </div>
	        <div class="footer-item <?php if($menu == 'personal'){echo 'active';}?>">
	            <a href="<?php if(isset($type) && $type == 'service'){echo '/service/center';}else{echo '/member/center';}?>">
		            <div class="footer-item-img personal"></div>
		            <div class="footer-item-title">个人中心</div>
	            </a>
	        </div>
	        <div class="clear"></div>
	    </div>
	    <?php if(isset($page_detail_js) && $page_detail_js):?>
	        <?php foreach($page_detail_js as $item):?>
	            <script src="<?php echo $item;?>"></script>
	        <?php endforeach;?>
	    <?php endif;?>
    </body>
	<script type='text/javascript'>
		(function(m, ei, q, i, a, j, s) {
			m[i] = m[i] || function() {
					(m[i].a = m[i].a || []).push(arguments)
				};
			j = ei.createElement(q),
				s = ei.getElementsByTagName(q)[0];
			j.async = true;
			j.charset = 'UTF-8';
			j.src = '//static.meiqia.com/dist/meiqia.js';
			s.parentNode.insertBefore(j, s);
		})(window, document, 'script', '_MEIQIA');
		_MEIQIA('entId', 22454);
	</script>
	<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1260920126'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/z_stat.php%3Fid%3D1260920126%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
    <script>
    $(function(){
        var meiqia = setInterval(function(){
        	$("#MEIQIA-BTN").css({'width' : '30px', 'font-size' : '13px'});
            $("#MEIQIA-BTN #MEIQIA-BTN-ICON").css('margin', '5px');
            $("#MEIQIA-BTN #MEIQIA-BTN-TEXT").css({'width' : '30px', 'font-size' : '13px', 'padding' : '0px 8px 2px'});
        }, 200);
    });
    </script>
</html>