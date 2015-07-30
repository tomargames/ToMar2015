// Create the canvas
var canvas = document.getElementById("dbCanvas");
var ctx = canvas.getContext("2d");
// constants
var intNUMBEROFCOLORS = 5;          // easy cheat
var intNUMBEROFROWS = 10;
var intNUMBEROFCOLUMNS = 15;
var intCOLUMNTWO = 680;
var intTOPMARGIN = 33;
var intWIDTH = 27;
var intHEIGHT = 33;
var colors = [[getColorDARKRED(), getColorDARKGREEN(), getColorMAGENTA(), getColorDARKBLUE(), getColorORANGE()],
					[getColorLIGHTPINK(), getColorLIGHTGREEN(), getColorLIGHTMAGENTA(), getColorLIGHTBLUE(), getColorYELLOW()]];
var selects = [];
var columns = [];
var historyStack = [];
var intLevel = 0;
var intPoints = 0;						// this is total points scored during this level
var intLevelPoints = 0;				// this is the points coming into this level, should be 0 during level 1
var intBalloons;
var	intHighestScore = 0;
var intLowestCount = 150;
var boolPopping = true;
var btnBackUp = new tmButton(intCOLUMNTWO, 450, getColorLIGHTBLUE(), 120, 35, "Back Up", 16);
var btnStartOver = new tmButton(intCOLUMNTWO, 520, getColorLIGHTYELLOW(), 120, 35, "Start Over", 16);
var btnNewGame = new tmButton(intCOLUMNTWO, 590, getColorLIGHTMAGENTA(), 120, 35, "New Puzzle", 16);
canvas.addEventListener("mousemove", mouseMove);
canvas.addEventListener("click", mouseClick);
reInit();

// objBalloon constructor
function objBalloon(row, col, color) 
{
	this.row = row;
	this.col = col;
	if (color === undefined)
	{
		this.color = getIntRnd(intNUMBEROFCOLORS - 1);			// will yield 0 through 4
	}
	else
	{
		this.color = color;
	}	
	this.x = (this.col + 1) * 1.5 * intWIDTH;		
	this.y = intTOPMARGIN + (this.row + 1) * 1.75 * intHEIGHT;
	this.boolSelected = false;
//	display("Constructed Row=" + this.row + ", col=" + this.col + ", color=" + this.color + ", color coming in was " + color);
	this.setCol = function(newcol)
	{
		this.col = newcol;
		this.x = (this.col + 1) * 1.5 * intWIDTH;		
	}	
	this.setRow = function(newrow)
	{
		this.row = newrow;
		this.y = intTOPMARGIN + (this.row + 1) * 1.75 * intHEIGHT;
	}		
	this.draw = function() 
	{
		ctx.beginPath();
		ctx.ellipse(this.x, this.y, intWIDTH * .75, intWIDTH, 0, 0, 2 * Math.PI);
		ctx.fillStyle = colors[this.isSelected()][this.color];
		ctx.fill();
		ctx.beginPath();
		ctx.moveTo(this.x, this.y + intHEIGHT - 7);
		ctx.lineTo(this.x + 3, this.y + intHEIGHT - 2);
		ctx.lineTo(this.x - 3, this.y + intHEIGHT - 2);
		ctx.closePath();
		ctx.fill();
		ctx.beginPath();
		ctx.fillStyle = getColorWHITE();
		ctx.fillRect(this.x + 5, this.y - 15, 3, 4);
//	printText(this.row + ", " + this.col, this.x - 10, this.y, getColorWHITE(), 12);
	}
	this.isSelected = function()
	{
		return (this.boolSelected === true) ? 1 : 0;
	}
	this.encode = function()
	{
		return formatNumber(this.col, 2) + this.row + this.color;
	}
	this.display = function()
	{
		return "Row=" + this.row + ", col=" + this.col + ", color=" + this.color;
	}	
	this.hit = function()
	{
		if (currentX >= this.x - (intWIDTH * 3/4) && currentX <= this.x + (intWIDTH * 3/4))
		{
			if (currentY >= this.y - (intHEIGHT * 3/4) && currentY <= this.y + intHEIGHT)
			{
				return true;	
			}
		}
		return false;
	}
}
// objColumn -- a column of objBalloons
function objColumn(col)
{
	this.balloons = [];
	for (var r = 0; r < intNUMBEROFROWS; r++)
	{
		this.balloons[r] = new objBalloon(r, col);
	}
	this.renumber = function()
	{
		var newColumn = this.balloons.filter(removeDeleted);
		for (var r = 0; r < newColumn.length; r++)
		{
			newColumn[r].setRow(r);											// set new row and refigure y coordinate
		}
		this.balloons = newColumn;
		return (this.balloons.length === 0) ? false : true;
	}
	this.setColumn = function(c)
	{
		for (var r = 0; r < this.balloons.length; r++)
		{	
			this.balloons[r].setCol(c);
		}
	}
	this.encode = function()
	{
		var codeString = this.balloons.length - 1;					// remember to add it back in during the decode
		for (var r = 0; r < this.balloons.length; r++)
		{	
			codeString += this.balloons[r].encode();
		}
		return codeString;
	}
	this.decode = function(codeString)
	{
		var n = (codeString.substring(0,1) * 1) + 1;				// number of balloons that were in the column
		codeString = codeString.substring(1);
//		display(n + " balloons will be added using this string: " + codeString);
		this.balloons = [];
		for (var r = 0; r < n; r++)
		{
			this.balloons[this.balloons.length] = new objBalloon(codeString.substring(2, 3) * 1, codeString.substring(0, 2) * 1, codeString.substring(3, 4) * 1);
			codeString = codeString.substring(4); 
		}
		return codeString;
	}				
}	
function restoreColumns(s)
{
	var cntr = 0;
	while (s.length > 0)
	{
		if (cntr >= columns.length)
		{
			columns[cntr] = new objColumn(cntr);
		}	
		s = columns[cntr++].decode(s);
//		display("Codestring for column " + cntr + ": " + s);
	}
}
function mouseClick(event)
{
	stringMessage = "";
	if (btnStartOver.clicked(currentX, currentY))
	{
		if (window.confirm("Restart puzzle from the beginning of this level?") == true) 
		{
			if (historyStack.length > 0)
			{
				// this restores the very first thing that was put on the history stack
				var historyString = historyStack[0].substring(3);
				intPoints = 0;
				intBalloons = intNUMBEROFROWS * intNUMBEROFCOLUMNS;
				restoreColumns(historyString);
				historyStack = [];
				stringMessage = "Restored to the beginning of Level " + intLevel + ".";
			}	
    } 	
  }   
	else if (btnNewGame.clicked(currentX, currentY))
	{
		if (window.confirm("Abandon this puzzle and generate a new one?") == true) 
		{
			if (intHighestScore > 0)
			{
				document.gameForm.score.value = intHighestScore;
				document.gameForm.tsp.value = Date.now();
				document.gameForm.submit();
			}
			else
			{		
				initPuzzle();
				stringMessage = "Here is a new puzzle.";
			}	
    } 	
  }   
	else if (btnBackUp.clicked(currentX, currentY))
	{
		backUp();
	}		
	else
	{			
		//pop whatever was in selects
		boolPopping = true;
		if (selects.length > 0)
		{
			var historyString = formatNumber(selects.length, 3);				// beginning of historyString, to be followed by encoded board
			for (var i = 0; i < columns.length; i++)
			{
				historyString += columns[i].encode();
			}	
			historyStack[historyStack.length] = historyString;					// end of saving history
			var p = (selects.length * selects.length * intLevel);
			intPoints += p;
			stringMessage = "Popped " + selects.length + " balloons for " + p + " points.";
			var popCols = [];
			for (var i = 0; i < selects.length; i++)
			{	
				intBalloons -= 1;
				popCols[popCols.length] = selects[i].col;											// column id is on stack to be renumbered
//			display("Popping balloon: " + columns[selects[i].col].balloons[selects[i].row].display()); 			// balloon is now gone
				delete columns[selects[i].col].balloons[selects[i].row]; 			// balloon is now gone
			}
			var renumberColumns = popCols.filter(onlyUnique);								// subscripts of columns from which balloons were removed
			popCols = [];
			for (var i = 0; i < renumberColumns.length; i++)
			{
				if (columns[renumberColumns[i]].renumber() === false)
				{
					popCols[popCols.length] = renumberColumns[i];					// subscript of column being deleted added to stack 
					delete columns[renumberColumns[i]];										// column is now gone
				}
			}
			if (popCols.length > 0)    // renumber the columns because some have been deleted
			{	
				var newBoard = columns.filter(removeDeleted);
				for (var c = 0; c < newBoard.length; c++)
				{	
					newBoard[c].setColumn(c);								// set the new column and refigure x coordinate
				}	
				columns = newBoard;
			}
		}
		selects = [];
		if (intBalloons == 0)
		{	
			intPoints += 2500 * intLevel;
//	  display("Beat level " + intLevel + ", points is " + intPoints);
			intLevelPoints += intPoints;
			reInit();
		}
		boolPopping = false;	
	}	
}
function mouseMove(event)
{
	if (boolPopping == false)
	{
		var rect = canvas.getBoundingClientRect();
		currentX = event.clientX - rect.left;
		currentY = event.clientY - rect.top;
		//deselect whatever was in selects
		for (var i = 0; i < selects.length; i++)
		{	
			selects[i].boolSelected = false;
		}	
		selects = [];
		for (var c = 0; c < columns.length; c++)
		{
			for (var r = 0; r < columns[c].balloons.length; r++)
			{
				if (columns[c].balloons[r].hit(currentX, currentY) == true)
				{
					addToSelects(columns[c].balloons[r]);
					break;
				}	
			}
			if (selects.length > 0)
			{
				break;
			}	
		}
		if (selects.length > 0)
		{
			var sels = 0;
			// check the ones around the selected one
			while (selects.length > sels)
			{
				//get its position
				var c = selects[sels].col;
				var r = selects[sels].row;
				//check north
				if (r > 0)
				{	
					if (columns[c].balloons[r - 1].color === selects[sels].color)
					{
						if (columns[c].balloons[r - 1].boolSelected === false)
						{
							addToSelects(columns[c].balloons[r - 1]);
						}	
					}	
				}	
				//check south
				if (r < (columns[c].balloons.length - 1))
				{	
					if (columns[c].balloons[r + 1].color === selects[sels].color)
					{
						if (columns[c].balloons[r + 1].boolSelected === false)
						{
							addToSelects(columns[c].balloons[r + 1]);
						}	
					}	
				}
				//check west
				if (c > 0 && (r < columns[c - 1].balloons.length))
				{	
					if (columns[c - 1].balloons[r].color === selects[sels].color)
					{
						if (columns[c - 1].balloons[r].boolSelected === false)
						{
							addToSelects(columns[c - 1].balloons[r]);
						}	
					}	
				}
				//check east
				if (c < columns.length - 1 && (r < columns[c + 1].balloons.length))
				{	
					if (columns[c + 1].balloons[r].color === selects[sels].color)
					{
						if (columns[c + 1].balloons[r].boolSelected === false)
						{
							addToSelects(columns[c + 1].balloons[r]);
						}	
					}	
				}
				sels++;
			}
			if (selects.length < 2)
			{
				selects[0].boolSelected = false;
				selects = [];
			}
		}
	}	
}
function getBonus()
	{
		var base = 25 - intBalloons;
		if (base < 0)
		{
			return 0;
		}
		if (base < 10)
		{
			return base * 20 * intLevel;
		}
		if (base < 20)
		{
			return base * 35 * intLevel;
		}
		if (base < 25)
		{
			return base * 50 * intLevel;
		}
		return 1500 * intLevel;
	}
function getHighestScore(newScore)
	{
		if (newScore > intHighestScore)
		{
			intHighestScore = newScore;
		}
		return intHighestScore;
	}

function addToSelects(balloon)
{
	balloon.boolSelected = true;
	selects[selects.length] = balloon;
}	
function backUp()
{
	if (historyStack.length > 0)
	{
		var historyString = historyStack.pop();
//	display("History string: " + historyString);
		var p = historyString.substring(0, 3) * 1;				// number of balloons taken in the move being restored
		intPoints -= (p * p * intLevel);									// subtracts the points that had been awarded
		intBalloons += p;																	// adds the balloons back into the count
		stringMessage = "Restored " + p + " balloons.";
		restoreColumns(historyString.substring(3));
	}	
}	
function display(s)
{
	console.log(Date.now() + ": " + s);
}
function getLowestCount(newScore)
{
	if (newScore < intLowestCount)
	{
		intLowestCount = newScore;
	}
	return intLowestCount;
}
function printText(string, x, y, color, sz)
{
	ctx.font = sz + "px Arial";
	ctx.textAlign = "left";
	ctx.textBaseline = "top";
	ctx.fillStyle = color;
	ctx.fillText(string, x, y);
}
// Draw everything
function render() 
{
	if (boolPopping == false)
	{
		ctx.fillStyle = getColorCREAM();
		ctx.fillRect(0,0,canvas.width,canvas.height);
		for (var c = 0; c < columns.length; c++)
		{
			for (var r = 0; r < columns[c].balloons.length; r++)
			{
				columns[c].balloons[r].draw();
			}
		}
		var y = 40;
		printText(stringMessage, 28, 10, getColorDARKGREEN(), 32);
		printText("Level: " + intLevel, intCOLUMNTWO, 18 + 1 * y, getColorDARKBLUE(), 32);
		printText("Points: " + (intPoints + intLevelPoints), intCOLUMNTWO, 18 + 2 * y, getColorDARKGREEN(), 32);
		printText("Selected: " + selects.length, intCOLUMNTWO, 18 + 3 * y, getColorDARKBLUE(), 32);
		printText("Moves: " + historyStack.length, intCOLUMNTWO, 18 + 4 * y, getColorDARKGREEN(), 32);
		printText("Bonus: "  + getBonus(), intCOLUMNTWO, 18 + 5 * y, getColorDARKBLUE(), 32);
		printText("Total: " + (intPoints + intLevelPoints + getBonus()) , intCOLUMNTWO, 18 + 6 * y, getColorDARKGREEN(), 32);
		printText("High: " + getHighestScore(intPoints + intLevelPoints + getBonus()), intCOLUMNTWO, 18 + 7 * y, getColorDARKBLUE(), 32);
		printText("Balloons: " + intBalloons, intCOLUMNTWO, 18 + 8 * y, getColorDARKGREEN(), 32);
		printText("Lowest: " + getLowestCount(intBalloons), intCOLUMNTWO, 18 + 9 * y, getColorDARKBLUE(), 32);
  	btnBackUp.draw();
	  btnStartOver.draw();
  	btnNewGame.draw();
	}	
}
function reInit()
{
	boolPopping = true;
	for (var c = 0; c < intNUMBEROFCOLUMNS; c++)
	{
		columns[c] = new objColumn(c);
	}
	intPoints = 0;
	selects = [];
	intBalloons = intLowestCount = intNUMBEROFCOLUMNS * intNUMBEROFROWS;
	historyStack = [];
	intLevel += 1;
	boolPopping = false;
}	
// The main game loop
function main() 
{
	render();
	// Request to do this again ASAP
	requestAnimationFrame(main);
};
// Cross-browser support for requestAnimationFrame
var w = window;
requestAnimationFrame = w.requestAnimationFrame || w.webkitRequestAnimationFrame || w.msRequestAnimationFrame || w.mozRequestAnimationFrame;
// Let's play this game!
main();