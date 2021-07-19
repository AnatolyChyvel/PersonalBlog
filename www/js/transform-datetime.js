(function(){
    var months = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"];
            
    var srcDate = document.getElementsByClassName("datetime");
    if(srcDate === null) return;
    
    for(let i=0; i<srcDate.length; i++){
        var date = srcDate[i].innerText;
        if(date === "") continue;

        var exp = /(\d{4})-(\d\d)-(\d\d)\s(\d\d):(\d\d):(\d\d)/;
        var result = date.match(exp);
        if(result === null) continue;
                  
        result.shift();
        var objDate = new Date(...result, 0);
        var currentDate = new Date();
        if(objDate.getHours() === currentDate.getHours() && objDate.getDate() === currentDate.getDate()
            && objDate.getMonth() === currentDate.getMonth()+1 && objDate.getFullYear() === currentDate.getFullYear()){
            
            let minutes = currentDate.getMinutes() - objDate.getMinutes();
            
            if(minutes > 5){
                srcDate[i].innerText = minutes + " минут назад"; 
                continue;
            }
            if(minutes <= 1)
                srcDate[i].innerText = "1 минуту назад";
            else if(minutes > 1 && minutes < 5)
                srcDate[i].innerText = minutes + " минуты назад";
        }else{
            srcDate[i].innerText = objDate.getDate() + " " + months[objDate.getMonth()-1] + " " + objDate.getFullYear() + " "
                + objDate.getHours() + ":" + objDate.getMinutes() + ":" + objDate.getSeconds();
        }
    }
}());