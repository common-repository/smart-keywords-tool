<?php

/**
 * 温馨提示内容
 * '{页面别名}' => array(
 *  'content' => {内容}  // 注1
 *  'title' => '温馨提示', // 选填
 *  'type'  =>  '', // 选填，图标class name
 * )
 * 
 * 注1  内容可以直接是html字符串('<dl><dd>提示a</dd><dd>提示b</dd><dd>提示c</dd></dl>；
 *      也可以是列出每项的数组(array('提示a', '提示b', '提示c'))
 */
$prompt_items = array(
  'suggest' => array(
    'content' => '<dl><dd>关键词引擎数据接口基于<a href="https://www.wbolt.com/tools/keyword-finder" target="_blank">关键词查找工具API</a>。</dd>
    <dd>中文网站内容应优先考虑百度、谷歌、必应和360引擎接口。</dd>
    <dd>国内电商相关网站内容优先考虑淘宝引擎接口。</dd>
    <dd>海外电商独立站或者海外内容站应优先考虑Bing+和谷歌引擎，并且应使用受众所在国家IP访问。</dd></dl>'
  ),
  'tags' => array(
    'content' => array(
      '中文站点建议使用百度或者讯飞的NLP关键词提取接口。',
      '英文站点建议使用OpenCalais和Dandelion分词接口。',
      '理论上，文章文字内容越丰富，分词越精准。',
      '讯飞关键词提取API接口每日20000次限额，百度关键词提取API已经不提供免费额度。',
      '百度关键词API提示“FAIL:110,Token失效，请进行更换”，需手动执行token重置。',
    )
  ),
  'selection' => array(
    'content' => array(
      '选词工具使用<a href="https://www.wbolt.com/tools/keyword-finder" target="_blank">关键词查找工具API</a>',
      '不建议在长尾关键词上使用以词搜词，查询结果可能没有或者非常少。',
      '中文选词优先使用谷歌、百度、必应三大引擎。',
      '英文及其他语种选词优先使用谷歌和Bing+引擎。',
    )
  ),
  'lexicon' => array(
    'content' => '<dl>
    <dd>关键词库仅用于站长关键词研究使用，数据基于<a href="https://www.wbolt.com/tools/keyword-finder" target="_blank">关键词查找工具API</a>。</dd>
    <dd>谨慎使用添加词库的关键词至标签，如需使用，建议添加高频次关键词。</dd>
    <dd>除本关键词库外，站长还可以结合<a
        href="https://www.wbolt.com/top-free-keyword-research-tools.html" target="_blank">更多其他关键词工具</a>进行关键词挖掘和分析工作。</dd>
    <dd>学习<a href="https://www.wbolt.com/keywords-seo-tips.html" target="_blank">如何做好页面关键词查找、布局及优化</a>，以提升关键词挖掘和选词水平。</dd>
  </dl>'
  ),
);
