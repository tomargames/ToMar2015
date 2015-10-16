/*
 * BattleShip Playground by marie
 * August, 2015
 */
// Create the canvas and game variables
var canvas = document.getElementById("bCanvas");
var ctx = canvas.getContext("2d");
var UNTESTED = NOSHIP = 0;
var TESTED = SELECTED = 1;
var MISS = 2;
var HIT = 3;
var CONNECTING = "C";
var PLACING = "P";
var ACTIVE = "A";
var INACTIVE = "O";
var GRIDSIZE = 10;
var SQSIZE = 34;
var COLUMNTWO = 550;
var xTEXT = 5;
var yTEXT = 470;
var TOPMARGIN = 100;
var MARGIN = 1;
var SLENGTHS = [5, 4, 3, 3, 2];
var SQCOLORS = [getColorCHARTREUSE(), getColorCHARTREUSE(), getColorDARKBLUE(), getColorDARKBLUE()];
var SQSYMBOLS = ["", "X", "",  "+"];
var playerBoards = [];
var playerNames = [];
var playerIDs = [];
var playerShips = [];
var playerShots = [];
var shipButtons = [];
var shotsFired = 0;
var flipButton = null;
var fireButton = null;
var user = 2;
var opponent = 1;
var nextActor = 0;
var message = "Select a game, or start a new one.";
var actionButton = null;
var actionLabel = "";
var shipButtons = null;
var selectedShip = -1;
var selectedColor = getColorPURPLE();
var buttonColor = getColorGREEN();
var actionColor = getColorRED();
var gameStage = null;
var statusChars = null;

for (var i = 0; i < 2; i++)
{
	playerBoards[i] = [];
	for (var j = 0; j < GRIDSIZE; j++)
	{
		playerBoards[i][j] = [];
		for (var k = 0; k < GRIDSIZE; k++)
		{
			playerBoards[i][j][k] = new objSquare(i, j, k);
		}	
	}	
}	
canvas.addEventListener("click", mouseClick);
// Cross-browser support for requestAnimationFrame
var w = window;
requestAnimationFrame = w.requestAnimationFrame || w.webkitRequestAnimationFrame || w.msRequestAnimationFrame || w.mozRequestAnimationFrame;
main();

// The main game loop
function main() 
{
	render();
	// Request to do this again ASAP
	requestAnimationFrame(main);
}
function objShot(row, col)
{
	this.row = row;
	this.col = col;
	this.valid = function()
	{
		if (this.row > -1 && this.col > -1 && this.row < GRIDSIZE && this.col < GRIDSIZE)
		{
			return true;
		}
		return false;		
	}
}
function objShip(pNum, sId)
{
	this.player = pNum;
	this.id = sId;
	this.startCoor = new objShot(1, this.id + 1);
	this.horizontal = 0;
	this.selected = false;
	this.safe = false;
	this.takeAction = function(act)
	{
		var r = this.startCoor.row;
		var c = this.startCoor.col;
		for (var i = 0; i < SLENGTHS[this.id]; i++)
		{
			if (act == 0)				// select
			{
				playerBoards[this.player][r][c].status = SELECTED;
			}
			else if (act == 1)			// deselect
			{
				playerBoards[this.player][r][c].status = UNTESTED;
			}
			else if (act == 3)			// erase contents
			{
				if (new objShot(r, c).valid() == true)
				{	
					if (playerBoards[this.player][r][c].status == SELECTED)
					{
						playerBoards[this.player][r][c].contents = NOSHIP;
					}	
					playerBoards[this.player][r][c].status = UNTESTED;
				}	
			}
			c = (this.horizontal == 1) ? c + 1 : c;
			r = (this.horizontal == 1) ? r : r + 1;
		}	
		if (act == 3)
		{
			this.safe = false;						// erased ship is not safely placed
		}		
	}
	this.select = function()
	{
		if (this.selected == false)
		{
			selectedShip = this.id;
			this.selected = true;
			shipButtons[this.id].color = selectedColor;
			this.takeAction(0);					// 0 sets each square to SELECTED
		}
		else
		{
			selectedShip = -1;
			this.selected = false;
			shipButtons[this.id].color = buttonColor;
			this.takeAction(1);					// 1 sets each square to UNTESTED
		}		
	}
	this.flip = function()
	{
		var newDir = (this.horizontal == 1) ? 0 : 1;
		this.place(this.startCoor.row, this.startCoor.col, newDir);
		placeButtons();
	}
	this.place = function(row, col, hor)
	{
//	console.log("player" + this.player + " place " + row + ", " + col + ", " + hor);
   	// first erase where it already is
		this.takeAction(3);					// 3 erases whatever of the ship is there
		this.startCoor.row = r = row;
		this.startCoor.col = c = col;
		this.horizontal = hor;
   	this.safe = true;
   	for (var i = 0; i < SLENGTHS[this.id]; i++)
   	{
			if (new objShot(r, c).valid() == false)
      {
      	this.safe = false;
      }
      else if (playerBoards[this.player][r][c].contents == NOSHIP)
      {	
   			playerBoards[this.player][r][c].contents = this.id + 1;
				playerBoards[this.player][r][c].status = SELECTED;
   		}	
   		else
   		{
   			this.safe = false;
   		}
			c = (this.horizontal == 1) ? c + 1 : c;
			r = (this.horizontal == 1) ? r : r + 1;
		}
  }
	this.place(this.startCoor.row, this.startCoor.col, this.horizontal);
}	
function objSquare(playerNumber, row, col)
{
	this.player = playerNumber;
	this.shot = new objShot(row, col);
	this.x = this.shot.col * (SQSIZE + MARGIN) + (playerNumber * COLUMNTWO);
	this.y = this.shot.row * (SQSIZE + MARGIN) + TOPMARGIN;
	this.status = UNTESTED;
	this.contents = NOSHIP;
	this.clicked = function(mouseX, mouseY)
	{
		if (mouseX > this.x && 
			mouseX < this.x + SQSIZE && 
			mouseY > this.y && 
			mouseY < this.y + SQSIZE)
		{
			return true;
		}
		return false;
	}
	this.decode = function(ch, type)
	{
		var dec = 0;
		if (type == this.player || type == 3)
		{
			if (ch == "a")
			{
				dec = 10;
			}	
			else if (ch == "b")
			{
				dec = 11;
			}
			else
			{
				dec = ch * 1;
			}		
			this.contents = Math.floor(dec/2);
			if (dec % 2 == 1)				// square has been tested
			{
				if (this.contents == 0)
				{
					this.status = 2;
				}	
				else
				{
					this.status = 3;
				}	
			}
		}
		else	
		{
			this.status = ch * 1;
		}
//	console.log("Player" + this.player + ", ch is " + ch + ", status is " + this.status);
	}
  this.draw = function(type)
  {
    ctx.fillStyle = SQCOLORS[this.status];
    ctx.fillRect(this.x, this.y, SQSIZE, SQSIZE);
		ctx.beginPath();
		ctx.lineWidth = "1";
		ctx.strokeStyle = getColorBLACK();
		ctx.rect(this.x, this.y, SQSIZE, SQSIZE);
		ctx.stroke();
		var tc = (this.status == SELECTED) ? selectedColor : getColorWHITE();
		var c = (this.player == type || type == 3) ? this.showBoat() : SQSYMBOLS[this.status];
		printText(c, this.x + 11, this.y + 6, tc, 20);	
  }
	this.showBoat = function()
	{
		return (this.contents > 0) ? this.contents : "";
	}
}
function refreshGrids()
{
	for (var i = 0; i < 2; i++)
	{
		for (var j = 0; j < GRIDSIZE; j++)
		{
			for (var k = 0; k < GRIDSIZE; k++)
			{
				playerBoards[i][j][k].status = UNTESTED;
				playerBoards[i][j][k].contents = NOSHIP;
			}	
		}
	}
}
function populateDisplay(passkey)
{
	selectedShip = -1;
	selectedGame = passkey;
	playerNames[0] = names[selectedGame][0];
	playerNames[1] = names[selectedGame][1];
	user = rels[selectedGame];
	opponent = (user == 1) ? 0 : 1;
	refreshGrids();
	fireButton = null;
	var dataString = strings[selectedGame];
	gameStage = firstChar(dataString);
	if (gameStage == "C")   // you will only get this if the user is player0 or user is spectator
	{
		message = "Waiting for " + playerNames[1] + " to join the game.";
		return;
	}
	nextActor = dataString.substring(1, 2) * 1;
	dataString = dataString.substring(2);
	if (gameStage == "A" || gameStage == "O")
	{
		for (var player = 0; player < 2; player++)
		{
			playerShots[player] = dataString.substr(0, 1);
			dataString = dataString.substr(1);
			for (var j = 0; j < GRIDSIZE; j++)
			{
				for (var k = 0; k < GRIDSIZE; k++)
				{
					if (gameStage == "O")
					{	
						playerBoards[player][j][k].decode(dataString.substr(0, 1), 3);
					}
					else
					{	
						playerBoards[player][j][k].decode(dataString.substr(0, 1), user);
					}
					dataString = dataString.substr(1);
				}	
			}
		}
		if (gameStage == "O")
		{
			message = playerNames[nextActor] + " has won the game!";
		}	
		else if (user == nextActor)
		{
			message = "It's your turn! Click on " + playerNames[opponent] + "'s grid to shoot.";
		}
		else
		{		
			message = "Waiting for " + playerNames[nextActor] + " to play.";
		}	
	}	
 	else if (gameStage == "P")
	{
	// P0 means player0 only needs to place boats (then call bot for placement in a human/bot game)
	// P1 means player1 only needs to place boats (will only happen in a human/human game)
	// P2 means both players need to place; will update to P0 or P1 when someone places
//	console.log("gameStage is " + gameStage + ", user is " + user + ", nextActor is " + nextActor);
		if (user == 2)
		{
			message = "Players are still placing their ships.";
			return;
		}
		playerShips[user] = [];
		if (nextActor == 2 || nextActor == user)  // user needs to place boats
		{
			message = "Use the buttons to select a ship; click on the grid to place it.";
			shipButtons = [];
			for (var i = 0; i < 5; i++)
			{
				shipButtons[i] = new tmButton(430, TOPMARGIN + 10 + 50 * (i + 1), buttonColor, 40, 40, (i + 1), 32);
				shipButtons[i].yLabel = 2;
				playerShips[user][i] = new objShip(user, i);
				playerShips[user][i].takeAction(1);					// deselect it
			}
			placeButtons();
		}
		else 
		{
			message = "Waiting for " + playerNames[opponent] + " to place ships.";
			var charCounter = 0;
//		console.log("datastring is " + dataString);
			for (var i = 0; i < 5; i++)
			{
				var str = dataString.substr(charCounter, 3);
				charCounter += 3;
				var r = str.substr(0,1) * 1;
				var c = str.substr(1,1) * 1;
				var h = str.substr(2,1) * 1;
//			console.log("Ship " + i + " will be placed at " + str + ", r is " + r + ", c is " + c + ", h is " + h);
				playerShips[user][i] = new objShip(user, i);
				playerShips[user][i].place(r, c, h);
				playerShips[user][i].takeAction(1);					// deselect it
			}	
		}
	}
}
function fireReady()
{
	if (gameStage == "A")
	{
		if (nextActor == user)
		{
			if (playerShots[user] == shotsFired)
			{
				fireButton = new tmButton(400, TOPMARGIN, actionColor, 100, 50, "FIRE", 28);	
				return;
			}	
		}
		fireButton = null;	
	}	
}
function placeButtons()
{
	// placeButton should only be created if gameStage is P and all 5 ships are safe
	if (gameStage == "P")
	{
		if (selectedShip > -1)
		{
			flipButton = new tmButton(380, TOPMARGIN + 310, buttonColor, 140, 40, "HORIZONTAL", 18);
			flipButton.label = (playerShips[user][selectedShip].horizontal == 1) ? "VERTICAL" : "HORIZONTAL";
		}
		else
		{
			flipButton = null;
		}	
		for (var i = 0; i < 5; i++)
		{
			if (playerShips[user][i].safe == false)
			{
				fireButton = null;
				return;
			}	
		}
		fireButton = new tmButton(400, TOPMARGIN, actionColor, 100, 50, "PLACE", 24);	
	}	
}	
function render() 
{
	ctx.fillStyle = getColorCREAM();
	ctx.fillRect(0,0,canvas.width,canvas.height);
	if (selectedGame != '')
	{	
		for (var i = 0; i < 2; i++)
		{
			printText(playerNames[i], xTEXT + (i * COLUMNTWO), 5, getColorBLACK(), 32);
			if (gameStage == "A")
			{	
				printText(playerShots[i] + " shots", xTEXT + (i * COLUMNTWO), 50, getColorBLACK(), 32);
			}	
			for (var j = 0; j < GRIDSIZE; j++)
			{
				for (var k = 0; k < GRIDSIZE; k++)
				{
					if (gameStage == "O")
					{	
						playerBoards[i][j][k].draw(3);
					}	
					else
					{	
						playerBoards[i][j][k].draw(user);
					}	
				}	
			}
		}
		if (fireButton != null)
		{
			fireButton.draw();
		}
		if (shipButtons != null)
		{		
			if (flipButton != null)
			{
				flipButton.draw();
			}
			for (var i = 0; i < shipButtons.length; i++)
			{
				if (shipButtons[i] != null)
				{
					shipButtons[i].draw();
				}
			}	
		}	
	}	
	printText(message, xTEXT, yTEXT, getColorBLACK(), 24);	
}
function mouseClick(event)
{
	var rect = canvas.getBoundingClientRect();
	currentX = event.clientX - rect.left;
	currentY = event.clientY - rect.top;
	if (fireButton != null)
	{
		if (gameStage == "A")
		{
			if (fireButton.clicked(currentX, currentY))
			{
				document.gameForm.gm.value = selectedGame;
				var shots = "S" + user;
				for (var j = 0; j < GRIDSIZE; j++)
				{
					for (var k = 0; k < GRIDSIZE; k++)
					{
//					console.log("user is " + user + ", row " + j + " col " + k + ", status is " + playerBoards[user][j][k].status);
						if (playerBoards[opponent][j][k].status == SELECTED)
						{
							shots += "" + j + "" + k;
							console.log(shots);
						}	
					}
				}	
				document.gameForm.ac.value = shots;
//			console.log("form is: " + document.gameForm.ac.value)
				document.gameForm.submit();
			}	
		}		
		else if (gameStage == "P")
		{
			if (fireButton.clicked(currentX, currentY))
			{
				if (window.confirm("This is where you want to hide your ships?") == true) 
				{
					document.gameForm.gm.value = selectedGame;
					var ships = "P" + user;
					for (var i = 0; i < 5; i++)
					{
						ships += playerShips[user][i].startCoor.row;
						ships += playerShips[user][i].startCoor.col;
						ships += playerShips[user][i].horizontal;
					}	
					document.gameForm.ac.value = ships;
					document.gameForm.submit();
				}
			}	
		}	
	}
	if (shipButtons != null)
	{		
		for (var i = 0; i < 5; i++)
		{
			if (shipButtons[i] != null)
			{
				if (shipButtons[i].clicked(currentX, currentY))
				{
					if (selectedShip > -1)
					{
						playerShips[user][selectedShip].select();
					}	
					selectedShip = i;
					playerShips[user][i].select();
					placeButtons();
					return;
				}	
			}	
		}
	}	
	if (flipButton != null)
	{
		if (flipButton.clicked(currentX, currentY))
		{
			if (selectedShip > -1)
			{
				playerShips[user][selectedShip].flip();
			}
			return;
		}	
	}	
	for (var i = 0; i < 2; i++)
	{
		for (var j = 0; j < GRIDSIZE; j++)
		{
			for (var k = 0; k < GRIDSIZE; k++)
			{
				if (playerBoards[i][j][k].clicked(currentX, currentY))
				{
//				console.log("clicked on grid " + i + ", row " + j + ", col " + k);
					if (gameStage == "A")
					{
						if (nextActor == user && i == opponent)
						{
							if (playerBoards[i][j][k].status == UNTESTED)
							{
								if (shotsFired < playerShots[user])
								{
									playerBoards[i][j][k].status = SELECTED;
									console.log("selected grid " + i + ", row " + j + ", col " + k + ", status is " + playerBoards[i][j][k].status);
									shotsFired += 1;
								}	
							}	
							else if (playerBoards[i][j][k].status == SELECTED)
							{
								playerBoards[i][j][k].status = UNTESTED;
								shotsFired -= 1;
							}
							fireReady();	
						}
					}	
					else if (gameStage == "P")
					{
						if (i == user)
						{
							if (selectedShip > -1)
							{
								playerShips[user][selectedShip].place(j, k, playerShips[user][selectedShip].horizontal);
								placeButtons();
							}	
						}	
					}
					return;	
				}
			}	
		}
	}
}
function printText(string, x, y, color, size)
{
	ctx.font = size + "px Arial";
	ctx.textAlign = "left";
	ctx.textBaseline = "top";
	ctx.fillStyle = color;
	ctx.fillText(string, x, y);
}
