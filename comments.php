<?php function threadedComments($comments, $options) {
		$commentLevelClass = $comments->_levels > 0 ? 'comment-child' : 'comment-parent';
?>

<li id="li-<?php $comments->theId(); ?>">
	<div id="<?php $comments->theId(); ?>">
		<div  class="comment-item">
			<div class="<?php echo $commentLevelClass; ?>">
				<?php $comments->gravatar(80, ''); ?>
			</div>
			<div class="comment-body">
				<div class="comment-head">
					<h5><?php $comments->author(); ?> · <small><?php $comments->date('Y-m-d H:i'); ?></small><?php
					if ($comments->authorId) {
						if ($comments->authorId == $comments->ownerId) {
							_e(' <span class="badge badge-pill badge-primary"><i class="fa fa-user-o" aria-hidden="true"></i> 作者</span>');
						}
					}
					?></h5>
				</div>
				<?php $comments->content(); ?>
				<div style="float: right;">
					<?php $comments->reply('<i class="fa fa-reply" aria-hidden="true"></i> 回复'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php if ($comments->children) { ?>
	<div class="comment-children">
		<?php $comments->threadedComments($options); ?>
	</div>
	<?php } ?>
</li>
 
<?php } ?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="section">
	<div class="container" id="comments">
		<div class="content">
			<div class="row align-items-center justify-content-center">
				<h3><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('%d 条评论')); ?></h3>
			</div>
			<?php $this->comments()->to($comments); ?>
			<?php if ($comments->have()): ?>
				<?php $comments->listComments(); ?>
				<div class="row align-items-center justify-content-center"><nav class="page-nav"><?php $comments->pageNav('<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => '', 'nextClass' => '')); ?></nav></div>
			<?php endif; ?>
			<div class="comment-card">
				<?php if($this->allow('comment')): ?>
				<div id="<?php $this->respondId(); ?>" class="comment-reply">
					<div class="row align-items-center justify-content-center">
						<h3 id="response"><?php _e('发表评论'); ?></h3>
					</div>
					<div class="row align-items-center justify-content-center">
						<?php $comments->cancelReply(); ?>
					</div>
					<br/>
					<form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form" class="container">
    <?php if ($this->user->hasLogin()): ?>
        <p>
            <?php _e('已登录为'); ?>
            <a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>。
            <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('注销？'); ?></a>
        </p>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <div class="input-group mb-4">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                    </div>
                    <input type="text" name="author" id="author" class="form-control"
                           placeholder="名称" value="<?php $this->remember('author'); ?>" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group mb-4">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                    </div>
                    <input type="email" name="mail" id="mail" class="form-control"
                           placeholder="邮箱" value="<?php $this->remember('mail'); ?>"
                           <?php if ($this->options->commentsRequireMail): ?>required<?php endif; ?> />
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="comment-editor">
        <textarea rows="6" name="text" id="textarea" class="form-control" required><?php $this->remember('text'); ?></textarea>
    </div>

    <div class="comment-actions">
        <?php if (array_key_exists('XCaptcha', Typecho_Plugin::export()['activated'])): ?>
            <?php XCaptcha_Plugin::showCaptcha(); ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-outline-success" id="add-comment-button">
            <?php _e('提交评论'); ?>
        </button>
    </div>
</form>
				</div>
				<?php else: ?>
				<div class="row align-items-center justify-content-center"><h3 id="response"><h3><?php _e('评论已关闭'); ?></h3></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if($this->options->Pjax=="1"): ?>
<script>
	function focusToComment(username){
		var roots = document.querySelectorAll('#comments > div');
		function getcomments(eles){
			var comlist = [];
			var childrenlist = [];
			for(var p = 0; p<eles.length; p++){
				var lis = eles[p].querySelectorAll(':scope > ol > li');
				for(var j = 0; j<lis.length; j++){
					var h5 = lis[j].querySelectorAll(':scope > div[id^="comment"] > .comment-item > .comment-body > .comment-head > h5');
					for(var i = 0; i<h5.length; i++){
						var name = h5[i].innerText.split("·")[0].trim();
						if(name==username){
							comlist.push(parseInt(lis[j].id.split("-")[2]));
						}
					}
					var child = lis[j].querySelector(':scope > .comment-children');
					if(child){
						childrenlist.push(child);
					}
				}
			}
			if(childrenlist.length>0){
				return comlist.concat(getcomments(childrenlist));
			}else{
				return comlist;
			}
		}
		var commentIds = getcomments(roots);
		var commentId = Math.max.apply(null, commentIds);
		if(commentId!=-Infinity){
			var target = document.getElementById('comment-'+commentId);
			if(target){
				window.scrollTo({ top: target.getBoundingClientRect().top + window.pageYOffset - 100, behavior: 'smooth' });
				setTimeout(function(){
					target.style.display = 'none';
					setTimeout(function(){ target.style.display = ''; }, 90);
				}, 500);
			}
		}
	}
	function bindsubmit(){
		var form = document.getElementById("comment-form");
		if(!form) return;
		form.addEventListener("submit", function(e) {
			e.preventDefault();
			var pgid = start_progress();
			var btn = document.getElementById("add-comment-button");
			if(btn) btn.disabled = true;
			var data = new FormData(form);
			var rubbish = <?php echo Typecho_Common::shuffleScriptVar(
            $this->security->getToken(clear_urlcan($this->request->getRequestUrl()))); ?>;
			data.append("_", rubbish);
			fetch(form.getAttribute("action"), { method: form.getAttribute("method") || "POST", body: data })
				.then(function(res) { return res.text(); })
				.then(function(html) {
					var newdocument = new DOMParser().parseFromString(html, "text/html");
					if(newdocument.title == "Error"){
						var container = newdocument.getElementsByClassName("container")[0];
						var error = container ? container.innerText.trim() : "";
						alert("评论提交错误", error);
					}else{
						var newComments = newdocument.getElementById("comments");
						var curComments = document.getElementById("comments");
						if(newComments && curComments) curComments.innerHTML = newComments.innerHTML;
						bindsubmit();
						var authorInput = document.getElementById("author");
						var authorName = authorInput && authorInput.value ? authorInput.value : (document.querySelector("a[href$='profile.php']") ? document.querySelector("a[href$='profile.php']").innerText : "");
						if(authorName) focusToComment(authorName);
					}
				})
				.catch(function() {
					alert("网络请求错误", "请重新尝试提交评论");
				})
				.finally(function() {
					if(btn) btn.disabled = false;
					stop_progress(pgid);
				});
		});
	}
	bindsubmit()
	var rubbishScripts = new DOMParser().parseFromString(`<?php echo $this->pluginHandle()->header("", $this);?>`, "text/html").getElementsByTagName("script")
	var script
	for(var i = 0; i<rubbishScripts.length; i++){
		script = rubbishScripts[i].innerHTML
		try {
			eval(script)
		} catch (error) {
			alert(error)
		}
	}
</script>

<script type="text/javascript">
    (function () {
    window.TypechoComment = {
        dom : function (id) {
            return document.getElementById(id);
        },
    
        create : function (tag, attr) {
            var el = document.createElement(tag);
        
            for (var key in attr) {
                el.setAttribute(key, attr[key]);
            }
        
            return el;
        },

        reply : function (cid, coid) {
            var comment = this.dom(cid), parent = comment.parentNode,
                response = this.dom('<?php $this->respondId() ?>'), input = this.dom('comment-parent'),
                form = 'form' == response.tagName ? response : response.getElementsByTagName('form')[0],
                textarea = response.getElementsByTagName('textarea')[0];

            if (null == input) {
                input = this.create('input', {
                    'type' : 'hidden',
                    'name' : 'parent',
                    'id'   : 'comment-parent'
                });

                form.appendChild(input);
            }

            input.setAttribute('value', coid);

            if (null == this.dom('comment-form-place-holder')) {
                var holder = this.create('div', {
                    'id' : 'comment-form-place-holder'
                });

                response.parentNode.insertBefore(holder, response);
            }

            comment.appendChild(response);
            this.dom('cancel-comment-reply-link').style.display = '';

            if (null != textarea && 'text' == textarea.name) {
                textarea.focus();
            }

            return false;
        },

        cancelReply : function () {
            var response = this.dom('<?php $this->respondId() ?>'),
            holder = this.dom('comment-form-place-holder'), input = this.dom('comment-parent');

            if (null != input) {
                input.parentNode.removeChild(input);
            }

            if (null == holder) {
                return true;
            }

            this.dom('cancel-comment-reply-link').style.display = 'none';
            holder.parentNode.insertBefore(response, holder);
            return false;
        }
    };
})();
</script>
<?php endif; ?>