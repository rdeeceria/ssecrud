<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <head>
		<title>SSE CRUD</title>
		<style>
			#spoiler{
			 display: none;
			}
		</style>
	</head>
    <body>
	<h1>SSE CRUD:</h1>
	<div id="response"></div>
	<p id="counter"></p>

	<form action="javascript:void(0);" method="POST" onsubmit="app.Add()"> 
		<input type="text" id="add-name" placeholder="New country">
		<input type="submit" value="Add">
	</form>

	<div id="spoiler" role="aria-hidden">
		<form action="javascript:void(0);" method="POST" id="saveEdit">
		<input type="text" id="edit-name">
		<input type="submit" value="Edit" /> <a onclick="CloseInput()" aria-label="Close">&#10006;</a>
		</form>
	</div>

	<table>
		<tr>
			<th>Name</th>
		</tr>
		<tbody id="city"></tbody>
	</table>
	
		<script type="application/javascript">
	
		this.queue = [];
	
		var sse = new function () {
			
			if ( typeof(EventSource) !== "undefined" ) {
			
				const stream = new EventSource('event.php');
				const newsList = document.getElementById('response');
				
				stream.addEventListener('load', (e) => {
					let data = JSON.parse(e.data);
					queue = data;
					app.FetchAll();
				});
				
				stream.addEventListener('timeout', (e) => {
					newsList.innerHTML = '<tr><td><strong>' + e.data + '</strong></td></tr>';
				});
				
				stream.addEventListener('open', (e) => {
					newsList.innerHTML = '<tr><td><strong>' + e.data + '</strong></td></tr>';

				});		
				
				stream.addEventListener('message', (e) => {
					let data = JSON.parse(e.data);
					queue = data;
					app.FetchAll();
				});

				stream.addEventListener('error', (e) => {
					switch (e.target.readyState) {
						case 0:
							newsList.innerHTML = '<tr><td><strong> Stream Connecting.. </strong></td></tr>';
							break;
							
						case 2:
							newsList.innerHTML += '<tr><td><strong> Stream Close.. </strong></td></tr>';
							break;							
					};
				});
			
			}	else	{
				newsList.innerHTML = "Sorry, your browser does not support server-sent events...";
			};
			
		};
	
		var app = new function() {
			
			this.el = document.getElementById('city');

			this.Count = function(data) {
				var el = document.getElementById('counter');
				var name = 'city';
				if (data) {
					if (data > 1) {
						name = 'city';
					}
					el.innerHTML = data + ' ' + name ;
				} else {
					el.innerHTML = 'No ' + name;
				};
			};
			
			this.FetchAll = function() {
				var data = '';
				if (queue.length > 0) {
					for (i = 0; i < queue.length; i++) {
						data += '<tr>';
						data += '<td>' + queue[i].name + '</td>';
						data += '<td><button onclick="app.Edit(' + i + ')">Edit</button></td>';
						data += '<td><button onclick="app.Delete(' + i + ')">Delete</button></td>';
						data += '</tr>';
					}
				};
				this.Count(queue.length);
				this.el.innerHTML = data;
			};

			this.Add = function() {
				el = document.getElementById('add-name');
				if (el.value) {
					queue.push({ name: el.value });
					fetch('add.php', {
						method: 'POST',
						headers: { 
							'Content-Type': 'application/json',
							'Access-Control-Allow-Origin': '*'
						},
						body: JSON.stringify(queue.pop())
					});
					el.value = '';
					this.FetchAll();
				}
			};
			
			this.Edit = function(item) {
				var el = document.getElementById('edit-name');
				el.value = queue[item].name;
				document.getElementById('spoiler').style.display = 'block';
				document.getElementById('saveEdit').onsubmit = function() {
					if (el.value) {
						queue.splice(item, 1, {name: el.value});
						fetch('patch.php', {
							method: 'POST',
							headers: { 
								'Content-Type': 'application/json',
								'Access-Control-Allow-Origin': '*'
							},
							body: JSON.stringify(queue)
						})
					};
					CloseInput();
				};
				this.FetchAll();
							
			};
			
			this.Delete = function(item) {
				queue.splice(item, 1)
				fetch('patch.php', {
					method: 'POST',
					headers: { 
						'Content-Type': 'application/json',
						'Access-Control-Allow-Origin': '*'
					},
					body: JSON.stringify(queue)
				})
				this.FetchAll();
			};
			
		};
		
		function CloseInput ()  {
			document.getElementById('spoiler').style.display = 'none';
		};	
		
		</script>
    </body>
</html>