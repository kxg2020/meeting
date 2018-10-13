<?php
namespace app\index\service\template;

class TextCard extends AgentMessage{
    private $value= [];
    public function templateInit(){
        $this->type = "textcard";
        $this->paramsFormat();
        $this->commonTemplateInit();
        $this->fillTemplateValue();
        return $this;
    }

    public function fillTemplateValue(){
        $template = "<br/><div class='normal'>会议名称:%s</div><br/><br/>";
        $template.= "<div class='normal'>会议主持:%s</div><br/><br/>";
        $template.= "<div>开始时间:%s</div><br/><br/>";
        $template.= "<div>结束时间:%s</div><br/>";
        $description =  sprintf($template,$this->params["title"],$this->params["host"],$this->params["start_time"],$this->params["end_time"]);
        $this->value = [
            "description" => $description,
            "title"       => $this->title,
            "url"         => $this->redirect,
            "btntxt"      => $this->btnText,
        ];
       return $this->templateTypeInit($this->type,$this->value);
    }
}