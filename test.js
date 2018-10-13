function test(n){
  if (!/^(0|[1-9]\d*)(\.\d+)?$/.test(n))
  {
    return "数据非法";
  }
  let unit = "千百拾亿千百拾万千百拾元", str = "";
  unit = unit.substr(unit.length - n.length);
  for (let i=0; i < n.length; i++)
  {
    str += '零一二三四五六七八九'.charAt(n.charAt(i)) + unit.charAt(i);
  }
  return str.replace(/零(千|百|拾|角)/g, "零").replace(/(零)+/g, "零").replace(/零(万|亿|元)/g, "$1").replace(/(亿)万|一(拾)/g, "$1$2").replace(/^元零?|零分/g,"").replace(/元$/g, "");
}

console.log(test(1200))