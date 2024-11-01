=== Smart Keywords Tool - 智能关键词插件 ===
Contributors: wbolt,mrkwong
Donate link: https://www.wbolt.com/
Tags: Baidu, Google, Baidu, Bing, SEO, Keyword, Block, Dandelion, OpenCalais, iflytek
Requires at least: 5.6
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 1.6.8
License: GNU General Public License v2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

智能关键词插件（Smart Keywords Tool）是一款集即时关键词推荐、关键词选词工具、文章智能标签及关键词库功能于一体的WordPress网站SEO优化插件。

== Description ==

智能关键词插件（Smart Keywords Tool）是一款集即时关键词推荐、关键词选词工具、文章智能标签及关键词库功能于一体的WordPress网站SEO优化插件。

功能包括：

### 1.热门关键词推荐引擎

1.1 **热门关键词推荐引擎**
支持360搜索、必应、谷歌（Pro）、百度（Pro）、淘宝（Pro）和Bing+（Pro）六种关键词引擎，实现编辑文章标题和标签时，根据编辑输入实时推送搜索引擎推荐的热门关键词，帮助站长快速布局文章热门关键词，提升网站内容质量及制作搜索引擎热门内容。；

* **国内站长**-建议采用360搜索、Bing和百度其中一种热门关键词推荐接口即可，当然在国内百度搜索引擎占主要市场份额，使用百度热门关键词推荐接口效果最佳；
* **淘宝客站长**-可使用淘宝关键词推荐接口，方便淘宝客站长获取到淘宝的关键词数据，在文章标题及文章关键词快速布局商品关键词，以达到关键词更符合淘宝系购买用户搜索行为；
* **外贸站长**-建议使用Bing+热门关键词接口，该接口可以根据站长当前IP推送IP所在区域最热门的相关关键词，这有利于外贸客户依据不同的国家地域，做个性化关键词布局。

> ℹ️ <strong>Tips</strong> 
> 
> 1.关键词引擎数据接口基于<a href="https://www.wbolt.com/tools/keyword-finder?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="关键词查找工具API">关键词查找工具API</a>。
> 2.中文网站内容应优先考虑百度、谷歌、必应和360引擎接口。
> 3.国内电商相关网站内容优先考虑淘宝引擎接口。
> 4.海外电商独立站或者海外内容站应优先考虑Bing+和谷歌引擎，并且应使用受众所在国家IP访问。

1.2 **关键词推荐位置**
支持选择搜索引擎推荐关键词应用位置：文章标题及文章标签；

1.3 **关闭WordPress标签默认推荐功能**
支持站长关闭WordPress标签自带的推荐功能，仅使用插件的搜索引擎热门关键词推荐功能。

### 2.智能标签

智能标签是插件通过接入百度云、讯飞、OpenCalais和Dandelion人工智能技术，对文章内容进行分析并智能抽取出核心的关键词作为文章标签。能够帮助站长大大提升文章关键词提炼的有效性及效率。

> ℹ️ <strong>Tips</strong> 
> 
> 1.中文站点建议使用百度或者讯飞的NLP关键词提取接口。
> 2.英文站点建议使用OpenCalais和Dandelion分词接口。
> 3.理论上，文章文字内容越丰富，分词越精准。
> 4.讯飞关键词提取API接口每日20000次限额，百度关键词提取API已经不提供免费额度

### 3.选词工具

选词工具是该插件基于360搜索、Bing、谷歌（Pro）、百度（Pro）、淘宝（Pro）和Bing+（Pro）几个搜索引擎的热门关键词数据库，为站长设立的一个独立关键词选词工具。

站长只需要选择其中一种搜索引擎，输入相关关键词，即会查找到该关键词相似的词，可以帮助站长挖掘更多相似的关键词，以用于分析搜索引擎受欢迎的热门内容及关键词布局。

选词工具支持站长快速将关键词清单添加至标签库，或者加入关键词库以作备用。

> ℹ️ <strong>Tips</strong> 
> 
> 1.选词工具使用<a href="https://www.wbolt.com/tools/keyword-finder?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="关键词查找工具API">关键词查找工具API</a>。
> 2.不建议在长尾关键词上使用以词搜词，查询结果可能没有或者非常少。
> 3.中文选词优先使用谷歌、百度、必应三大引擎。
> 4.英文及其他语种选词优先使用谷歌和Bing+引擎。

### 4.关键词库
关键词库模块旨在帮助站长创建不同的主题，利用选词工具收集整理不同主题的关键词库，以作站内或者站外关键词挖掘及研究使用。支持站长将关键词库的关键词快速导入至WordPress标签库，或者导出CSV表格作其他用途使用。

> ℹ️ <strong>Tips</strong> 
> 
> 1.关键词库仅用于站长关键词研究使用，数据基于<a href="https://www.wbolt.com/tools/keyword-finder?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="关键词查找工具API">关键词查找工具API</a>。
> 2.谨慎使用添加词库的关键词至标签，如需使用，建议添加高频次关键词。
> 3.除本关键词库外，站长还可以结合<a href="https://www.wbolt.com/top-free-keyword-research-tools.html?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="更多其他关键词工具">更多其他关键词工具</a>进行关键词挖掘和分析工作。
> 4.学习<a href="https://www.wbolt.com/keywords-seo-tips.html?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="关键词挖掘和研究教程">如何做好页面关键词查找、布局及优化</a>，以提升关键词挖掘和选词水平。

智能关键词插件是闪电博专门为WordPress网站开发的SEO优化插件之一，帮助站长快速选取互联网热门关键词进行布局及利用智能云自动分析文章内容提炼关键词。WordPress站长可以利用该插件，并结合<a href='https://www.wbolt.com/plugins/sst?utm_source=wp&utm_medium=link&utm_campaign=skt' rel='friend' title='WordPress网站SEO优化插件'>WordPress网站SEO优化插件</a>、<a href='https://www.wbolt.com/plugins/bsl?utm_source=wp&utm_medium=link&utm_campaign=skt' rel='friend' title='百度推送插件'>百度推送插件</a>和<a href='https://www.wbolt.com/plugins/spider-analyser?utm_source=wp&utm_medium=link&utm_campaign=skt' rel='friend' title='蜘蛛统计分析插件'>蜘蛛统计分析插件</a>，对WordPress网站内容的搜索引擎收录及排名优化可以做到事半功倍的效果！

== 其他WP插件 ==

<a href="https://www.wbolt.com/plugins/skt?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="智能关键词插件">智能关键词插件</a>是目前WordPress插件市场甚至全网首创的搜索引擎热门关键词库实时推送插件。通过我们的关键词库服务器同步谷歌、百度、Bing和360搜索引擎热门关键词库，在站长编辑文章标题和标签时，基于键入内容实时推送搜索引擎热门关键词，方便站长实现热门关键词布局，提升网站内容质量。

闪电博（<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="闪电博官网">wbolt.com</a>）专注于WordPress主题和插件开发,为中文博客提供更多优质和符合国内需求的主题和插件。此外我们也会分享WordPress相关技巧和教程。

除了Smart Keywords Tool插件外，目前我们还开发了以下WordPress插件：

- [多合一搜索推送管理插件-历史下载安装数200,000+](https://wordpress.org/plugins/baidu-submit-link/)
- [Spider Analyser–搜索引擎蜘蛛分析插件](https://wordpress.org/plugins/spider-analyser/)
- [IMGspider-轻量外链图片采集插件](https://wordpress.org/plugins/imgspider/)
- [Smart SEO Tool-高效便捷的WP搜索引擎优化插件](https://wordpress.org/plugins/smart-seo-tool/)
- [MagicPost – WordPress文章管理功能增强插件](https://wordpress.org/plugins/magicpost/)
- [WPTurbo -WordPress性能优化插件](https://wordpress.org/plugins/wpturbo/)
- [WP VK-WordPress知识付费插件](https://wordpress.org/plugins/wp-vk/)
- [Online Contact Widget-多合一在线客服插件](https://wordpress.org/plugins/online-contact-widget/)

- 更多主题和插件，请访问<a href="https://www.wbolt.com/?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="闪电博官网">wbolt.com</a>!

如果你在WordPress主题和插件上有更多的需求，也希望您可以向我们提出意见建议，我们将会记录下来并根据实际情况，推出更多符合大家需求的主题和插件。

致谢！

闪电博团队

== WordPress资源 ==

由于我们是WordPress重度爱好者，在WordPress主题插件开发之余，我们还独立开发了一系列的在线工具及分享大量的WordPress教程，供国内的WordPress粉丝和站长使用和学习，其中包括：

**<a href="https://www.wbolt.com/learn?utm_source=wp&utm_medium=link&utm_campaign=skt" target="_blank">1. Wordpress学院:</a>** 这里将整合全面的WordPress知识和教程，帮助您深入了解WordPress的方方面面，包括基础、开发、优化、电商及SEO等。WordPress大师之路，从这里开始。

**<a href="https://www.wbolt.com/tools/keyword-finder?utm_source=wp&utm_medium=link&utm_campaign=skt" target="_blank">2. 关键词查找工具:</a>** 选择符合搜索用户需求的关键词进行内容编辑，更有机会获得更好的搜索引擎排名及自然流量。使用我们的关键词查找工具，以获取主流搜索引擎推荐关键词。

**<a href="https://www.wbolt.com/tools/wp-fixer?utm_source=wp&utm_medium=link&utm_campaign=skt">3. WOrdPress错误查找:</a>** 我们搜集了大部分WordPress最为常见的错误及对应的解决方案。您只需要在下方输入所遭遇的错误关键词或错误码，即可找到对应的处理办法。

**<a href="https://www.wbolt.com/tools/seo-toolbox?utm_source=wp&utm_medium=link&utm_campaign=skt">4. SEO工具箱:</a>** 收集整理国内外诸如链接建设、关键词研究、内容优化等不同类型的SEO工具。善用工具，往往可以达到事半功倍的效果。

**<a href="https://www.wbolt.com/tools/seo-topic?utm_source=wp&utm_medium=link&utm_campaign=skt">5. SEO优化中心:</a>** 无论您是 SEO 初学者，还是想学习高级SEO 策略，这都是您的 SEO 知识中心。

**<a href="https://www.wbolt.com/tools/spider-tool?utm_source=wp&utm_medium=link&utm_campaign=skt">6. 蜘蛛查询工具:</a>** 网站每日都可能会有大量的蜘蛛爬虫访问，或者搜索引擎爬虫，或者安全扫描，或者SEO检测……满目琳琅。借助我们的蜘蛛爬虫检测工具，让一切假蜘蛛爬虫无处遁形！

**<a href="https://www.wbolt.com/tools/wp-codex?utm_source=wp&utm_medium=link&utm_campaign=skt">7. WP开发宝典:</a>** WordPress作为全球市场份额最大CMS，也为众多企业官网、个人博客及电商网站的首选。使用我们的开发宝典，快速了解其函数、过滤器及动作等作用和写法。

**<a href="https://www.wbolt.com/tools/robots-tester?utm_source=wp&utm_medium=link&utm_campaign=skt">8. robots.txt测试工具:</a>** 标准规范的robots.txt能够正确指引搜索引擎蜘蛛爬取网站内容。反之，可能让蜘蛛晕头转向。借助我们的robots.txt检测工具，校正您所写的规则。

**<a href="https://www.wbolt.com/tools/theme-detector?utm_source=wp&utm_medium=link&utm_campaign=skt">9. WordPress主题检测器:</a>** 有时候，看到一个您为之着迷的WordPress网站。甚是想知道它背后的主题。查看源代码定可以找到蛛丝马迹，又或者使用我们的小工具，一键查明。

== Installation ==

方式1：在线安装(推荐)
1. 进入WordPress仪表盘，点击“插件-安装插件”，关键词搜索“Smart Keywords Tool”，找搜索结果中找到“Smart Keywords Tool”插件，点击“现在安装”；
2. 安装完毕后，启用 `Smart Keywords Tool` 插件.
3. 通过“设置”->“Smart Keywords Tool” 进入插件设置界面，依据个人需求设置插件.
4. 在编辑文章标题及文章标签时，即可获得即时推荐热门关键词。

方式2：上传安装

FTP上传安装
1. 解压插件压缩包smart-keywords-tool.zip，将解压获得文件夹上传至wordpress安装目录下的 `/wp-content/plugins/`目录.
2. 访问WordPress仪表盘，进入“插件”-“已安装插件”，在插件列表中找到“Smart Keywords Tool”，点击“启用”.
3. 通过“设置”->“Smart Keywords Tool” 进入插件设置界面，依据个人需求设置插件.
4. 在编辑文章标题及文章标签时，即可获得即时推荐热门关键词。

仪表盘上传安装
1. 进入WordPress仪表盘，点击“插件-安装插件”；
2. 点击界面左上方的“上传按钮”，选择本地提前下载好的插件压缩包smart-keywords-tool.zip，点击“现在安装”；
3. 安装完毕后，启用 `Smart Keywords Tool` 插件；
4. 通过“设置”->“Smart Keywords Tool” 进入插件设置界面，依据个人需求设置插件.
5. 在编辑文章标题及文章标签时，即可获得即时推荐热门关键词。

关于本插件，你可以通过阅读<a href="https://www.wbolt.com/skt-plugin-documentation.html?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="插件教程">智能关键词插件教程</a>学习了解插件安装、设置等详细内容。

== Frequently Asked Questions ==

= OpenCalais分词API请求是否有次数限制？ =
OpenCalais分词API免费服务每天最多支持5000个请求，并提供了一组广泛的语义元数据标签。根据官方话术，未来可能每日免费配额将减少。 

= Dandelion分词API请求是否有次数限制？ =
就目前而言，Dandelion API免费版本每13个小时可请求1000次，这对于一般站长来说是足够的了。如果需要大量请求，则可以考虑购买他们的<a href="https://dandelion.eu/profile/plans-and-pricing/?utm_source=wp&utm_medium=link&utm_campaign=skt">专业套餐</a>。

= 应该使用智能标签还是搜索关键词推荐？ =
两者不相冲突。智能标签是分词API依据文章内容生成，标签匹配度较高及效果更高；关键词推荐则通过API获取搜索引擎更受欢迎的关键词，以保证内容关键词布局更受搜索引擎欢迎。站长应该结合两种标签获取方式，以实现标签到达合理的文章内容占比及其搜索引擎的受欢迎度。

= 为什么智能标签提示请求失败？ =
这可能是由于网站服务器与分词API接口服务器之间的网络问题所导致，请稍后再试，或者检查网站服务器网络。

= 智能标签生成数量过少？ =
智能标签生成数量不是由插件控制的，而是由分词API接口基于文章内容分析生成。如智能标签太少，站长应该检查文章内容是否足够丰富；又或者通过热门关键词推荐功能进行标签补充。

= 插件是否兼容古腾堡编辑器？ =
兼容。您可以点击古腾堡区块编辑器右上角的闪电博图标以调用智能关键词插件。

= Bing+关键词推荐引擎与其他有何区别？ =
Bing+热门关键词推荐引擎主要是针对外贸站长，可以根据外贸站长当前所在IP地址，推荐该IP地址所属国家地域的热门关键词。为了确保所推荐的关键词符合当地人的搜索习惯，建议外贸站长在使用外贸专用引擎时，尽可能将客户端IP切换至对应的国家或者地区。

= 智能关键词插件（Smart Keywords Tool）支持哪些搜索引擎？ =
Smart Keywords Tool免费版支持360搜索及Bing两个搜索引擎的热门关键词库引擎。PRO版在免费版的基础上增加百度、谷歌、淘宝和外贸专用四个热门关键词推荐引擎，访问<a href="https://www.wbolt.com/plugins/skt?utm_source=wp&utm_medium=link&utm_campaign=skt" rel="friend" title="Smart Keywords Tool">关键词推荐专业插件Pro版本</a>。

= 国内是否可以使用Smart Keywords Tool（Pro版本）的谷歌热门关键词库引擎? =
无需代理服务器即可直接使用Smart Keywords Tool的谷歌热门关键词库引擎。

== Screenshots ==

1. 关键词引擎设置界面截图.
2. 智能标签API接口设置界面.
3. 选词工具界面截图.
4. 关键词库管理界面截图.
5. 编辑文章标题时关键词实时推荐截图.
6. 编辑文章标签时关键词实时推荐截图.
7. Bing+之英文站热门关键词推荐示例.
8. Bing+之葡萄牙语站热门关键词推荐示例.
9. 文章智能标签生成截图.

== Changelog ==

= 1.6.8 =
* 修复选词工具必应无法查询问题。

= 1.6.7 =
* 基于编码规范进一步优化PHP代码；
* 优化PHP代码以提升性能；
* 优化PHP代码以增强代码安全性。

= 1.6.6 =
* 使用gulp代替vite

= 1.6.5 =
* 古腾堡模式下，隐藏页面文章类型右侧栏智能关键词标签模块。
* 修复经典编辑器tag联想会被wp自动遮挡的问题；
* 修复古腾堡模式下智能关键词交互异常问题。

= 1.6.4 =
* 修复关键词下拉无效bug；
* 屏蔽插件console log。

= 1.6.3 =
* 移除腾讯云NLP API接口支持；
* 优化智能自动标签逻辑；
* 新增API接口错误日志支持；
* 增加nonce安全校验。

= 1.6.2 =
* 新增百度关键词分词API Token重置支持。

= 1.6.1 =
* 新增讯飞分词API接口支持；
* 新增各功能模块温馨提示模块；
* 优化关键词库列表样式及操作项命名；
* 其他已知问题修复及体验优化。

= 1.6.0 =
* 新增腾讯云关键词提取API接入；
* 新增腾讯云和百度云API接口错误码提示信息；
* 优化智能分词API设置默认项设置；
* 修复插件版本更新提示URL点击无效bug；
* 兼容WordPress 6.0。

= 1.5.2 =
* 兼容WordPress 5.9。

= 1.5.1 =
* 新增按WordPress规范加载资源；
* 加强数据安全规范；
* 修复词库批量删除bug；
* 修复古腾堡标签无法调用百度和谷歌关键词bug。

= 1.5.0 =
* 新增古腾堡区块编辑器兼容支持；
* 新增OpenCalais英文分词API支持；
* 新增Dandelion英文分词API支持；
* 新增使用默认分词API自动打标支持；
* 兼容最新版本WP 5.8.

= 1.4.1 =
* 修复新安装插件激活Pro版本异常问题。

= 1.4.0 =
* 新增关键词库功能，支持使用选词工具按主题创建多个词库；
* 新增Pro版本升级入口链接；
* 新增限时优惠活动入口；
* 新增选词工具添加备选、添加至标签库批量操作及加入关键词库等功能；
* 优化选词工具交互体验；
* 优化选词工具
* 优化版本升级提示与WordPress默认样式一致；
* 其他已知问题修复及体验优化。

= 1.3.2 =
* 文章编辑页面新增智能标签一键添加至标签功能按钮；
* 文章列表增加智能标签按钮，实现快速填入智能标签；
* 文章列表增加批量生成智能标签功能，为无标签文章批量生成标签。

= 1.3.1 =
* 修正低版本PHP无法安装插件问题；
* 部分功能描述文字修改。

= 1.3.0 =
* 新增文章编辑智能标签生成功能；
* 新增百度智能云应用API配置功能。

= 1.2.9 =
* 新增插件版本升级提示功能；
* 兼容WordPress5.5优化。

= 1.2.8 =
* 修复部分PHP版本“”PHP Notice: Undefined index.“报错问题

= 1.2.7 =
* 修复选词工具备选关键词区域显示异常bug；
* 修复插件设置页面页脚丢失bug。

= 1.2.6 =
* 新增关键词选词工具；
* 新增英文语言；
* 优化插件设置界面外观。

= 1.2.4 =
* 新增外贸专用热门关键词接口，可根据客户端IP地址搜索不同国家地区的热门关键词。

= 1.2.3 =
* 修正Pro版本获取Key链接错误问题

= 1.2.2 =
* 解决Pro版本状态异常bug

= 1.2.1 =
* 新增域名更换验证机制，解决Pro版本更换域名无法验证bug

= 1.2.0 =
* 新增淘宝搜索关键词引擎
* 增加古腾堡编辑器开关功能

= 1.1.0 =
* 修正已知bug

= 1.0.0 =
* 新增360搜索、Bing搜索引擎热门关键词引擎
* 新增热门关键词推送应用位置选项功能
* 新增关闭WordPress默认标签推荐功能
* 新增360搜索、Bing搜索引擎热门关键词同步功能
