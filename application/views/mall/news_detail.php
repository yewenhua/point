
   <div class="news-title"><?php echo $news['title'];?></div>
   <div class="news-time"><?php echo $news['created_at'];?></div>
   <?php if($news['img_url']):?>
       <div class="news-img-face"><img src="<?php echo $news['img_url'];?>"/></div>
   <?php endif;?>
   <div class="news-content"><?php echo $news['content'];?></div>