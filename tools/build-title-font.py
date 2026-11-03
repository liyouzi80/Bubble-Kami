#!/usr/bin/env python3
"""
为站点生成「标题字体子集」：只包含站点标题类文字实际用到的字符的思源宋体（Noto Serif SC 500），
通常几十 KB，代替按需下载的上百 KB 字体切片。

用法：
    pip install fonttools brotli
    python3 tools/build-title-font.py https://你的域名/ [-o title-font.woff2]

脚本会抓取站点所有公开文章 / 独立页面，收集每个页面都可能出现的衬线文字（站名、文章与页面标题、
分类与标签名、页脚和评论区的固定标题等）；加 --with-headings 时也收录文章正文里的小标题，再从 Google Fonts 的
Noto Serif SC 可变字体（首次运行自动下载，约 25MB，缓存在 ~/.cache/bubble-kami/）
截出 500 字重的子集。

把生成的 woff2 上传到网站（例如 usr/uploads/），在「设置外观 → 标题字体子集文件」填入地址即可。
子集里没有的字（例如之后新文章标题里的新字）会自动回退到原来的字体切片，显示不会出错；
隔一段时间重新生成一次即可保持最小下载量。
"""
import argparse
import html
import os
import re
import sys
import urllib.parse
import urllib.request
import concurrent.futures as cf

FONT_URL = 'https://raw.githubusercontent.com/google/fonts/main/ofl/notoserifsc/NotoSerifSC%5Bwght%5D.ttf'
CACHE = os.path.join(os.path.expanduser('~'), '.cache', 'bubble-kami')
UA = {'User-Agent': 'Mozilla/5.0 (Bubble Kami title font builder)'}

# 主题模板里用衬线字体显示的固定文字
FIXED = ('相关文章 发表评论 暂无评论 仅有一条评论 条评论 评论已关闭 页面没找到 最近的文章 '
         '这里空空如也 不如换个地方看看吧 的搜索结果 的文章 下的文章 标签 包含关键字 '
         '最新评论 最新文章 近期归档 文章目录 404')
# 标题里常见的标点与符号（中英文）
PUNCT = '，。、；：？！“”‘’（）《》〈〉【】「」『』〔〕—…·～￥%'


def get(url):
    try:
        with urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=30) as r:
            return r.status, r.read().decode('utf-8', 'ignore')
    except urllib.error.HTTPError as e:
        return e.code, ''
    except Exception:
        return 0, ''


def text(fragment):
    return html.unescape(re.sub(r'<[^>]+>', '', fragment)).strip()


def collect(site, with_headings=False):
    site = site.rstrip('/') + '/'
    strings, pages = set(), set()
    host = urllib.parse.urlparse(site).netloc

    # 首页：站名、导航里的独立页面、页脚标题
    st, home = get(site)
    if st != 200:
        sys.exit(f'无法打开 {site}（HTTP {st}）')
    for m in re.findall(r'<a class="navbar-brand[^"]*"[^>]*>(.*?)</a>', home, re.S):
        strings.add(text(m))
        strings.update(re.findall(r'alt="([^"]*)"', m))
    strings.update(text(m) for m in re.findall(r'<h1[^>]*>(.*?)</h1>', home, re.S))
    for href in re.findall(r'<a class="nav-link" href="([^"]+)"', home):
        if urllib.parse.urlparse(href).netloc == host:
            pages.add(href)

    # 分页收集文章
    for n in range(1, 500):
        st, body = (200, home) if n == 1 else get(f'{site}page/{n}/')
        links = re.findall(r'<h1><a class="text-default[^"]*" href="([^"]+)"[^>]*>(.*?)</a>', body, re.S)
        if st != 200 or not links:
            break
        for href, title in links:
            pages.add(href)
            strings.add(text(title))

    # 逐篇抓取：大标题、正文小标题、分类与标签
    def scan(url):
        st, body = get(url)
        if st != 200:
            return []
        # 顶部大标题
        found = [text(m) for m in re.findall(r'<section class="section section-lg section-hero.*?<h1[^>]*>(.*?)</h1>', body, re.S)[:1]]
        # 正文里的小标题（不含评论区：评论者昵称等用正文字体显示）。默认不收：只有打开那篇文章才需要，
        # 由原字体切片按需补齐即可，免得少数长小标题把全站共用的子集撑大
        m = with_headings and re.search(r'<div class="content">(.*?)(?:<nav class="post-pager"|<div class="card shadow content-card related-posts-card"|</section>)', body, re.S)
        if m:
            found += [text(h) for h in re.findall(r'<h[1-5][^>]*>(.*?)</h[1-5]>', m.group(1), re.S)]
        found += [text(t) for t in re.findall(r'<a class="post-taxonomy[^"]*"[^>]*>(.*?)</a>', body, re.S)]
        return found

    with cf.ThreadPoolExecutor(4) as ex:
        for found in ex.map(scan, sorted(pages)):
            strings.update(found)
    return strings, len(pages)


def ensure_font(path):
    if path:
        return path
    os.makedirs(CACHE, exist_ok=True)
    path = os.path.join(CACHE, 'NotoSerifSC-VF.ttf')
    if not os.path.exists(path):
        print('首次运行，下载 Noto Serif SC 可变字体（约 25MB）…', flush=True)
        urllib.request.urlretrieve(FONT_URL, path + '.part')
        os.replace(path + '.part', path)
    return path


def build(font_path, chars, out):
    from fontTools import subset
    from fontTools.ttLib import TTFont
    from fontTools.varLib import instancer

    font = TTFont(font_path)
    options = subset.Options()
    options.layout_features = ['*']
    options.name_IDs = ['*']
    options.notdef_outline = True
    subsetter = subset.Subsetter(options)
    subsetter.populate(unicodes=[ord(c) for c in chars])
    subsetter.subset(font)
    font = instancer.instantiateVariableFont(font, {'wght': 500})
    font.flavor = 'woff2'
    font.save(out)
    cmap = font.getBestCmap()
    return len(cmap), [c for c in chars if ord(c) not in cmap]


def main():
    ap = argparse.ArgumentParser(description='生成 Bubble Kami 标题字体子集')
    ap.add_argument('site', help='站点地址，例如 https://example.com/')
    ap.add_argument('-o', '--out', default='title-font.woff2', help='输出文件（默认 title-font.woff2）')
    ap.add_argument('--font', help='本地 Noto Serif SC 可变字体路径（不填则自动下载）')
    ap.add_argument('--extra', default='', help='额外需要包含的文字')
    ap.add_argument('--with-headings', action='store_true', help='同时收录文章正文里的小标题（子集会更大）')
    args = ap.parse_args()

    strings, n_pages = collect(args.site, args.with_headings)
    chars = set(''.join(strings) + FIXED + PUNCT + args.extra)
    chars |= {chr(c) for c in range(0x20, 0x7F)}  # 英文字母、数字与半角符号
    chars = sorted(c for c in chars if not c.isspace())
    han = sum(1 for c in chars if ord(c) >= 0x2E80)
    print(f'抓取 {n_pages} 个页面，收集到 {len(chars)} 个不同字符（汉字 {han} 个）', flush=True)

    count, missing = build(ensure_font(args.font), chars, args.out)
    size = os.path.getsize(args.out)
    print(f'已生成 {args.out}：{count} 个字形，{size / 1024:.0f}KB')
    if missing:
        print('字体里没有这些字符，将由系统字体显示：' + ''.join(missing))


if __name__ == '__main__':
    main()
