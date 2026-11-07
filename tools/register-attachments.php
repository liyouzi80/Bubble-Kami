<?php
/**
 * 为 usr/uploads 里「有文件、但数据库没有附件记录」的旧文件补登记附件，
 * 让它们出现在 Typecho 后台「管理 → 文件」里（典型场景：从 WordPress 迁移过来的图片）。
 *
 * 不会修改、移动或删除任何文件，也不改文章正文；只往 contents 表插入 type=attachment 的记录。
 * 能找到引用它的文章时，会把附件关联到那篇文章（文章编辑页的「附件」里也能看到）。
 *
 * 用法（放到 Typecho 根目录，与 config.inc.php 同级）：
 *
 *   命令行（推荐）：
 *     php register-attachments.php            预览，不写入
 *     php register-attachments.php --apply    写入数据库
 *     加 --only-referenced                    只登记被文章引用过的文件
 *
 *   浏览器（没有命令行时）：
 *     1. 把下面 RUN_KEY 改成一串自己才知道的随机字符
 *     2. 访问 https://你的域名/register-attachments.php?key=那串字符          预览
 *        再加 &apply=1 写入，加 &only-referenced=1 只登记被引用的文件
 *
 * 用完请立即删除本文件。操作前建议先备份数据库。
 */

const RUN_KEY = '';

$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
    if (RUN_KEY === '' || !isset($_GET['key']) || !hash_equals(RUN_KEY, (string)$_GET['key'])) {
        http_response_code(403);
        exit("拒绝访问：请先在脚本里设置 RUN_KEY，并通过 ?key= 传入。\n");
    }
}
$argv = $argv ?? [];
$apply = $isCli ? in_array('--apply', $argv, true) : !empty($_GET['apply']);
$onlyReferenced = $isCli ? in_array('--only-referenced', $argv, true) : !empty($_GET['only-referenced']);

$configFile = __DIR__ . '/config.inc.php';
if (!is_file($configFile)) {
    exit("找不到 config.inc.php：请把本脚本放到 Typecho 根目录再运行。\n");
}
require_once $configFile;

$db = \Typecho\Db::get();
$uploadDir = defined('__TYPECHO_UPLOAD_DIR__') ? __TYPECHO_UPLOAD_DIR__ : '/usr/uploads';
$uploadRoot = defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__;
$scanDir = rtrim($uploadRoot, '/') . $uploadDir;
if (!is_dir($scanDir)) {
    exit("上传目录不存在：{$scanDir}\n");
}

function out($line = '')
{
    echo $line, "\n";
    if (PHP_SAPI !== 'cli') {
        @ob_flush();
        @flush();
    }
}

/** 解析附件记录里的 text：1.2+ 为 JSON，1.1 及更早为 PHP 序列化 */
function attachmentPath($text)
{
    $data = json_decode((string)$text, true);
    if (!is_array($data)) {
        $data = @unserialize((string)$text, ['allowed_classes' => false]);
    }
    return is_array($data) && isset($data['path']) ? $data['path'] : null;
}

// 1) 已登记的附件路径
$registered = [];
foreach ($db->fetchAll($db->select('text')->from('table.contents')->where('type = ?', 'attachment')) as $row) {
    $path = attachmentPath($row['text']);
    if ($path !== null) {
        $registered[$path] = true;
    }
}

// 2) 文章 / 页面正文，用来找引用关系
$docs = $db->fetchAll($db->select('cid', 'text', 'authorId', 'type')->from('table.contents')
    ->where('type IN ?', ['post', 'page', 'post_draft', 'page_draft']));

// 3) 默认作者：第一位管理员
$admin = $db->fetchRow($db->select('uid')->from('table.users')->where('table.users.group = ?', 'administrator')->order('uid', \Typecho\Db::SORT_ASC)->limit(1));
if (!$admin) {
    $admin = $db->fetchRow($db->select('uid')->from('table.users')->order('uid', \Typecho\Db::SORT_ASC)->limit(1));
}
$defaultAuthor = $admin ? (int)$admin['uid'] : 1;

// 4) 扫描文件
$skipExt = ['php', 'phtml', 'php5', 'phar', 'htaccess', 'html', 'htm', 'ini'];
$files = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scanDir, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $name = $file->getFilename();
    if ($name[0] === '.') continue;
    if (in_array(strtolower($file->getExtension()), $skipExt, true)) continue;
    $rel = $uploadDir . substr($file->getPathname(), strlen($scanDir));
    $rel = str_replace(DIRECTORY_SEPARATOR, '/', $rel);
    $files[$rel] = $file->getPathname();
}
ksort($files);

$stats = ['total' => count($files), 'registered' => 0, 'new' => 0, 'linked' => 0, 'orphan' => 0, 'skipped' => 0];
$plan = [];
foreach ($files as $rel => $abs) {
    if (isset($registered[$rel])) {
        $stats['registered']++;
        continue;
    }
    // 正文里可能是原样中文路径，也可能是 URL 编码后的路径
    $encoded = implode('/', array_map('rawurlencode', explode('/', $rel)));
    $parent = null;
    foreach ($docs as $doc) {
        if (strpos($doc['text'], $rel) !== false || ($encoded !== $rel && strpos($doc['text'], $encoded) !== false)) {
            $parent = $doc;
            break;
        }
    }
    if (!$parent && $onlyReferenced) {
        $stats['skipped']++;
        continue;
    }
    $plan[] = [$rel, $abs, $parent];
    $stats['new']++;
    $parent ? $stats['linked']++ : $stats['orphan']++;
}

out(($apply ? '【写入模式】' : '【预览模式，未写入任何数据】') . ' 扫描目录：' . $scanDir);
out("文件共 {$stats['total']} 个，已有附件记录 {$stats['registered']} 个，待补登记 {$stats['new']} 个"
    . "（能关联到文章 {$stats['linked']} 个，未被任何文章引用 {$stats['orphan']} 个）"
    . ($onlyReferenced ? "，按 --only-referenced 跳过未引用的 {$stats['skipped']} 个" : ''));
out();

$done = 0;
foreach ($plan as [$rel, $abs, $parent]) {
    $name = basename($rel);
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    out(sprintf('%s  %s', $parent ? '→ 文章 cid=' . str_pad($parent['cid'], 5) : '  （未引用）    ', $rel));
    if (!$apply) continue;

    $mtime = filemtime($abs) ?: time();
    $text = json_encode([
        'name' => $name,
        'path' => $rel,
        'size' => filesize($abs),
        'type' => $ext,
        'mime' => \Typecho\Common::mimeContentType($abs),
    ]);
    $cid = $db->query($db->insert('table.contents')->rows([
        'title' => htmlspecialchars($name),
        'created' => $mtime,
        'modified' => time(),
        'text' => $text,
        'order' => 0,
        'authorId' => $parent ? (int)$parent['authorId'] : $defaultAuthor,
        'type' => 'attachment',
        'status' => 'publish',
        'commentsNum' => 0,
        'allowComment' => 1,
        'allowPing' => 0,
        'allowFeed' => 1,
        'parent' => $parent ? (int)$parent['cid'] : 0,
    ]));
    // 与 Typecho 上传时一样，用文件名生成缩略名，重名时追加 -1、-2……
    $base = \Typecho\Common::slugName($name, $cid);
    $slug = $base;
    $n = 1;
    while ($db->fetchRow($db->select('cid')->from('table.contents')->where('slug = ? AND cid <> ?', $slug, $cid)->limit(1))) {
        $slug = $base . '-' . $n++;
    }
    $db->query($db->update('table.contents')->rows(['slug' => $slug])->where('cid = ?', $cid));
    $done++;
}

out();
out($apply ? "完成：已补登记 {$done} 个附件。请到后台「管理 → 文件」查看，然后删除本脚本。"
    : '以上为预览。确认无误后加 --apply（浏览器加 &apply=1）写入。');
