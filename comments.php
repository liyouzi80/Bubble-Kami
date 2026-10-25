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
                <label class="sr-only" for="author">昵称</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                    </div>
                    <input type="text" name="author" id="author" class="form-control"
                           placeholder="昵称" autocomplete="name" value="<?php $this->remember('author'); ?>" required />
                </div>
            </div>
            <div class="col-md-6">
                <label class="sr-only" for="mail">邮箱（不会公开）</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                    </div>
                    <input type="email" name="mail" id="mail" class="form-control"
                           placeholder="邮箱（不会公开）" autocomplete="email" value="<?php $this->remember('mail'); ?>"
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
<?php if($this->options->Pjax): ?>
<?php
$__u = parse_url($this->request->getRequestUrl());
$__tokenUrl = (empty($__u['scheme']) ? 'http://' : $__u['scheme'] . '://') . $__u['host'] . $__u['path'];
?>
<script>
	window.BB = window.BB || {};
	window.BB.comment = {
		token: <?php echo Typecho_Common::shuffleScriptVar($this->security->getToken($__tokenUrl)); ?>,
		respondId: <?php echo json_encode($this->respondId); ?>,
		pluginScripts: <?php echo json_encode($this->pluginHandle()->header("", $this), JSON_UNESCAPED_UNICODE); ?>
	};
</script>
<?php endif; ?>