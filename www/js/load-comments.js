function reqReadyStateChange(){
	if (request.readyState == 4){
		var status = request.status;
		if(status == 200){
			var data = JSON.parse(request.response);
			var comments = data['comments'][0];
			var container = document.getElementsByClassName("comments__for__article")[0];

			for(let i=0; i<comments.length; i++){
				/* проверяет есть ли такие комментарии на странице */
				let isFinded = false;
				
				for(let j=0; j<objComments.length; j++){
					if(objComments[j].getAttribute("id") === "comment" + comments[i]["id"]){
						// проверка на изменения текста комментария
						if(objComments[j].innerText != comments[i]["text"]){
							objComments[j].innerText = comments[i]["text"];
						}
						isFinded = true;
						break;
					}						
				}
				if(isFinded) continue;

				// генерирация и добавление элемнтов DOM для комментария
				let commentView = document.createElement("div");
				commentView.className = "comment__view";
				container.appendChild(commentView);

				let avaNick = document.createElement("div");
				avaNick.className = "ava__nick";
				avaNick.clientWidth = 100;
				commentView.appendChild(avaNick);

				let ava = document.createElement("img");
				ava.setAttribute("src", "data:image/jpg;base64," + comments[i]["image"]);
				ava.setAttribute("width", "100");
				avaNick.appendChild(ava);

				let linkInProfile = document.createElement("a");
				linkInProfile.setAttribute("href", "/users/" + comments[i]["user_id"]);
				linkInProfile.innerText = comments[i]["user_nickname"];
				avaNick.appendChild(linkInProfile);

				let commentData = document.createElement("div");
				commentData.className = "comment__data";
				commentView.appendChild(commentData);

				let datetime = document.createElement("p");
				datetime.className = "datetime";
				datetime.innerText = comments[i]["created_at"];
				commentData.appendChild(datetime);

				let elemBr = document.createElement("br");
				commentData.appendChild(elemBr);

				let commentText = document.createElement("p");
				commentText.setAttribute("id", "comment" + comments[i]["id"]);
				commentText.innerText = comments[i]["text"];
				commentText.className = "comment__text";
				commentData.appendChild(commentText);

				if(comments[i]["isAdminOrOwner"]){
					let linkEdit = document.createElement("a");
					linkEdit.setAttribute("href", "/comments/" + comments[i]["id"] + "/edit");
					linkEdit.innerText = "Редактировать";
					commentData.appendChild(linkEdit);

					let linkDelete = document.createElement("a");
					linkDelete.setAttribute("href", "/comments/" + comments[i]["id"] + "/delete");
					linkDelete.innerText = "Удалить";
					commentData.appendChild(linkDelete);
				}
			}
			include("/js/transform-datetime.js");
		}
	}
}

function sendRequest(){
	countComments = objComments.length;
	let body = "countComments=" + countComments;

	request.open("POST", "http://mp.loc/api/comments/" + articleId);
	request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	request.onreadystatechange = reqReadyStateChange;
	request.send(body);	
}

function include(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementsByClassName("comment__view")[0].appendChild(script);
}

var request = new XMLHttpRequest();

var objArticle = document.getElementsByClassName("article__view");
var articleId = objArticle[0].getAttribute("id");
var objComments = document.getElementsByClassName("comment__text");
var countComments = objComments.length;

var timerId = setInterval(sendRequest, 10000);