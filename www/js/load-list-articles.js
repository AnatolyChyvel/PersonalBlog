function reqReadyStateChange(){
	if (request.readyState == 4){
		var status = request.status;
		if(status == 200){
			var data = JSON.parse(request.response);
			var articles = data["articles"][0];

			let deletedArticlesId = [];
			for(let objArticle of objArticles){
				deletedArticlesId.push(objArticle.getAttribute("id"));
			}
			//проверяет отображается ли на странице удаленная статья
			for(let i=0; i<articles.length; i++){
				let isFinded = false;
				for(let j=0; j<countArticlesInPage; j++){
					if("article" +  articles[i]["id"] == objArticles[j].id){
						isFinded = true;

						let index = deletedArticlesId.indexOf(objArticles[j].getAttribute("id"));
						deletedArticlesId.splice(index,1);
						break;
					}
				}
				if(isFinded) continue;
				// генерация элементов для отображения статьи
				let articleView = document.createElement("div");
				articleView.className = "article__short";
				articleView.id = "article" + articles[i]["id"];
				container.insertBefore(articleView, objArticles[0]);

				let elementH2 = document.createElement("h2");
				articleView.appendChild(elementH2);

				let articleName = document.createElement("a");
				articleName.className = "article__name";
				articleName.innerText = articles[i]["name"];
				articleName.setAttribute("href", "/articles/" + articles[i]["id"])
				elementH2.appendChild(articleName);

				let articleText = document.createElement("p");
				articleText.innerText = articles[i]["text"];
				articleView.appendChild(articleText);
			}
			//удаление со страницы удаленных статей
			for(articleId of deletedArticlesId){
				let article = document.getElementById(articleId);
				container.removeChild(article);
			}
		}
	}
}

function sendRequest(){
	countArticlesInPage = objArticles.length;
	let body = "countArticles=" + countArticlesInPage;
	//обработать в article controller
	request.open("POST", "http://mp.loc/api/list/articles");
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.onreadystatechange = reqReadyStateChange;
	request.send(body);	
}

var request = new XMLHttpRequest();

var container = document.getElementsByClassName("container")[1];

var objArticles = document.getElementsByClassName("article__short");
var countArticlesInPage = objArticles.length;

var timerId = setInterval(sendRequest, 10000);