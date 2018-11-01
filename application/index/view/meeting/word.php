<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>会议记录导出</title>
  <style>
    h1 {
      text-align: center;
      margin: 1em 0;
    }
    h2 {
      margin: 1em 0;
    }
    td{
      font-size: 18px;
      line-height: 30px;
      padding: 1em .5em;
    }
    .word{
      font-size: 18px;
      line-height: 22px;
      text-indent: 2em;
      margin: 1em 0;
    }
    .text-label{
      color: #FF0000;
    }
  </style>
</head>
<body>
<div style="width: 100%;">
  <h1>中共白朝乡月坝村党支部党员大会会议记录</h1>
  <table border="1" style="width: 100%;" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">会标（时间）：{$meeting["meetingName"]}({$meeting["meetingCreateTime"]})</td>
    </tr>
    <tr>
      <td colspan="2">
        <span>主持人：{$meeting["create_user"]}</span>
          <br>
        <span>应参会人员：{$meeting["shouldJoinUser"]}</span>
          <br>
        <span>实际参会人员：{$meeting["realJoinUser"]}</span>
      </td>
    </tr>
    {if !empty($meeting["meeting_issue"])}
      {volist name="$meeting['meeting_issue']" id="issue"}
      <tr>
          <td style="width: 10%">{$issue["issueType"]}</td>
          <td>{$issue["meetingIssueName"]}</td>
      </tr>
      {/volist}
      {else /}
      <tr>
          <td colspan="2" style="width: 100%">暂无数据</td>
      </tr>
      {/if}
  </table>
  {if !empty($meeting["meeting_info"])}
    {volist name="$meeting['meeting_info']" id="info" key="key"}
    <h2><span style="color: #ff0000">第{$key}项议题:</span>{$info["title"]}</h2>
    {if $info['type'] == 'yz'}
    <div>
        <div class="word">
            {$info["content"]}
        </div>
        {volist name = "$info['file']" id="in"}
        <div>
            <img style="width: 60%; height: auto;margin: 10px auto; display: block;" src="{$in['url']}" alt="">
        </div>
        {/volist}
        <div><span class="text-label">阅读人员：</span><span>{$info['read_user']}</span></div>
    </div>
    {/if}
    {if $info['type'] == 'bj'}
    <div>
        <div class="word">
            {$info['content']}
        </div>
        {volist name = "$info['file']" id="in"}
        <div>
            <img style="width: 60%; height: auto;margin: 10px auto; display: block;" src="{$in['url']}" alt="">
        </div>
        {/volist}
        {if isset($info['options'])}
        {volist name="$info['options']" id="op" key="key"}
        <div>
            <span>{$key}:{$op['title']}</span>:
            <br>
            <div><span class="text-label">同意：</span><span>{$op['agree']} 票</span></div>
            <div><span class="text-label">反对：</span><span>{$op['oppose']} 票</span></div>
            <div><span class="text-label">弃权：</span><span>{$op['give_up']} 票</span></div>
            <div><span class="text-label">表决结果：</span><span>
                    {if isset($op['result'])}
                    {$op['result']}
                    {/if}
                </span></div>
        </div>
        {/volist}
        {/if}
    </div>
    {/if}
    {if $info['type'] == 'tp'}
    <div>
        <div class="word">
            {$info['content']}
        </div>
        {volist name = "$info['file']" id="in"}
        <div>
            <img style="width: 60%; height: auto;margin: 10px auto; display: block;" src="{$in['url']}" alt="">
        </div>
        {/volist}
        {if $info['options']}
        {volist name="$info['options']" id="op" key="key"}
        <div>
            <div>
                <div><span class="text-label">选项{$key}：</span><span>{$op['option']}{$op['agree']} 票</span></div>
                <div class="word">

                </div>
                {volist name="$op['file']" id="fi"}
                <div>
                    <img style="width: 60%; height: auto;margin: 10px auto; display: block;" src="{$fi['file_url']}" alt="">
                </div>
                {/volist}
            </div>
        </div>
        {/volist}
        {/if}
        <div><span class="text-label">投票结果：</span><span>{$info['result']}</span></div>
    </div>
    {/if}
    {/volist}
    {/if}
</div>
</body>
</html>