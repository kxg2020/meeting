Number.prototype.ConvertToChinese = function() {
  let N = ["零", "一", "二", "三", "四", "五", "六", "七", "八", "九"]
  let str = this.toString()
  let C_Num = []
  for(let i = 0; i < str.length; i++){
    C_Num.push(N[str.charAt(i)])
  }
  return C_Num.join('')
}
window.setTitle = function (title) {
  document.title = title
}