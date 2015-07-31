/*
 * Anchors Away! by marie
 * Created in java on Apr 8, 2005
 * Updated March, 2011 for Facebook API
 * Updated March, 2012 for PHP interface using Google authentication
 * Updated September 2013 for inclusion in ToMarPentathlon
 * July, 2015 - complete re-write for HTML5
 */
// Create the canvas and game variables
var canvas = document.getElementById("dbCanvas");
var ctx = canvas.getContext("2d");
var MAXLEVEL = 78;
var SIZE = 19;
var COLUMNMARGIN = 2;
var ROWMARGIN = 12;
var LEFTMARGIN = 2;
var TOPMARGIN = 40;
var WORDSPERROW = 6;
var TOTALROWS = 18;
var MAXWORDSIZE = 6;
var POOLROWS = 5;
var FILLER = "?";
var SLOTSPERROW = 47;
var NUMBEROFSLOTS = [2, 3, 3];
var xCOORDINATES = [];
var yCOORDINATES = [];
var INWORD = 0;				// cream, if there's a letter, show it
var POOLINACTIVE = 1;		// chartreuse, blank
var POOLUNSELECTED = 2;		// chartreuse, show letter
var POOLSELECTED = 3;		// yellow, show letter
var POOLUSED = 4;			// dark gray, show letter
var levelWords = [];
var levelPool = [];
var intLevel = 0;
var playLevel = 0;
var lblGiveUp = "GIVE UP";
var lblNextLevel = "LEVEL ";
var lblLevel = "Level: ";
var lblWords = "Words: ";
var clrGiveUp = getColorRED();
var clrNextLevel = getColorLIGHTGREEN();
var intWordsFormed = 0;
var btnWords = new tmButton(780, 0, getColorLIGHTCYAN(), 80, 35, "WORDS", 16);
var btnHint = new tmButton(880, 0, getColorLIGHTYELLOW(), 65, 35, "HINT", 16);
var btnGiveUp = new tmButton(660, 0, clrGiveUp, 100, 35, lblGiveUp, 16);
var clrBACKGROUND = getColorCREAM();
var selectedSlot = null;
var objSlotClassName = "SLOT";
var objAnchorClassName = "ANCHOR";
var wordList = "";

canvas.addEventListener("click", mouseClick);
init();
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

// all the logic to create the board
function init()
{
  for (var i = 0; i < TOTALROWS + POOLROWS; i++)
  {
    yCOORDINATES[i] = TOPMARGIN + (SIZE + ROWMARGIN) * i;
	}
  for (var i = 0; i < (SLOTSPERROW); i++)
  {
    xCOORDINATES[i] = LEFTMARGIN + (SIZE + COLUMNMARGIN) * i;
  }
	if (savedGame == "")
	{
		document.gameForm.start.value = Date.now();
		initLevel();
	}
	else	
	{
		// first 3 characters in save string are the intLevel
		playLevel = document.gameForm.level.value * 1;
		intLevel = (playLevel <= MAXLEVEL) ? playLevel : MAXLEVEL;
		for (var i = 0; i < intLevel; i++)
		{
			levelWords[i] = new objWord(i, savedGame.substring(i * 12, i * 12 + 6).trim());		// 0	0	6		6	12
			levelWords[i].setUp(savedGame.substring(i * 12 + 6, (i+1)*12).trim());	// 1	12	18		18	24
		}
		stringMessage = "Resuming a saved game.";
		makeWordList();
	}	
}
function initLevel()
{
	stringMessage = "";
	playLevel += 1;
//playLevel = 80;
	intLevel = (playLevel <= MAXLEVEL) ? playLevel : MAXLEVEL;
	intWordsFormed = 0;
	btnGiveUp.label = lblGiveUp;
	btnGiveUp.color = clrGiveUp;
	var picks = randomPicks(words.length, intLevel);
	levelWords = [];						// list of original words for this level
	levelPool = [];
	var cookieString = "";
	for (var i = 0; i < intLevel; i++)
	{
		levelWords[i] = new objWord(i, words[picks[i]]);
		levelWords[i].setUp();
		cookieString += levelWords[i].save();
	}	
	makeWordList();
	storeGame(cookieString);
}
function storeGame(cookieString)
{
	var formElement = document.querySelector("gameForm");
	var formData = new FormData(formElement);
	var request = new XMLHttpRequest();
	request.open("POST", "aa.php");
	formData.append("id", document.gameForm.id.value);
	formData.append("nm", document.gameForm.nm.value);
	formData.append("start", document.gameForm.start.value);
	formData.append("tsp", cookieString);
	formData.append("level", playLevel);
	request.send(formData);	
}
function wordPatternFit(word, pattern)
{
	if (word.length === pattern.length)
	{
		for (var i = 0; i < pattern.length; i++)
		{
			if (pattern.substring(i, i + 1) != FILLER)
			{
				if (pattern.substring(i, i + 1) != word.substring(i, i + 1))
				{
					return false;
				}	
			}	
		}	
	}
	else
	{
		return false;
	}	
	return true;
}
function makeWordList()
{
	wordList = "";
	for (var i = 0; i < intLevel; i++)
	{
		wordList += (levelWords[i].pattern + "\n");
		for (var j = 0; j < words.length; j++)
		{	
			if (wordPatternFit(words[j], levelWords[i].pattern))
			{
				wordList += (words[j] + "\n");
			}	
		}	
		wordList += "------------\n";
	}
	// This sorts the letterPool into alphabetical order
	levelPool = levelPool.sort(function(a, b)	{ return (a.letter < b.letter) ? -1 : (a.letter > b.letter) ? 1 : 0; });
	for (var i = 0; i < levelPool.length; i++)
	{	
		levelPool[i].assignSlot(i);
	}	
}
function getSaveString()
{
		var saveString = formatNumber(playLevel, 3);
		for (var i = 0; i < levelWords.length; i++)
		{
			saveString += levelWords[i].originalWord + levelWords[i].pattern;
		}
		return saveString;
	}
// objWord constructor
function objWord(index, originalWord)
{
  this.index = index;
	this.originalWord = originalWord;
  this.row = Math.floor(index / WORDSPERROW);
  this.col = (index % WORDSPERROW) * (MAXWORDSIZE + 2);
	this.pattern = originalWord;
  this.elements = [];
  this.goodWord = false;	
	this.save = function()
	{
		return padString(this.originalWord, 6) + padString(this.pattern, 6);
	}
	this.drawOriginalWord = function()
	{ 
		var r = Math.floor(index / 10);
		var c = (index % 10) * MAXWORDSIZE;
		printText(this.originalWord, LEFTMARGIN + (c * 17), TOPMARGIN + 5 + (r * 17), getColorDARKBLUE(), 16);	
	}
	this.setUp = function(pattern)
	{
		// if you have a pattern, you're coming from restore
		// either way, you come out of this function with elements filled
		if (pattern === undefined)
		{
			// every element begins life as an Anchor
			for (var i = 0; i < this.originalWord.length; i++)
			{
				var l = this.originalWord.substring(i, i + 1);
				this.elements[i] = new objAnchor(l, xCOORDINATES[this.col + i], yCOORDINATES[this.row + POOLROWS]);
			}	
			var pPicks = randomPicks(this.originalWord.length, NUMBEROFSLOTS[this.originalWord.length - 4])
			for (var j = 0; j < pPicks.length; j++)
			{
				// first, turn the Anchor into a Slot, saving the letter
				var l = this.elements[pPicks[j]].letter;
				this.elements[pPicks[j]] = new objSlot(this.elements[pPicks[j]].x, this.elements[pPicks[j]].y);           // create empty slot with x,y coordinates
				// these will have an empty slot in the word itself, and then a slot in the levelPool
				levelPool[levelPool.length] = new objSlot(l, levelPool.length); 	// create a pool slot with a letter
				// change the character in the pattern to FILLER
				this.pattern = this.pattern.substring(0, pPicks[j]) + FILLER + this.pattern.substring(pPicks[j] + 1);
			}
		}
		else
		{
			this.pattern = pattern;
			for (var i = 0; i < this.originalWord.length; i++)
			{
				var l = this.originalWord.substring(i, i + 1);
				if (FILLER === pattern[i])
				{
					this.elements[i] = new objSlot(xCOORDINATES[this.col + i], yCOORDINATES[this.row + POOLROWS]);
					levelPool[levelPool.length] = new objSlot(l, levelPool.length);
				}
				else
				{
					this.elements[i] = new objAnchor(l, xCOORDINATES[this.col + i], yCOORDINATES[this.row + POOLROWS]);
				}
			}
    }
	}
	this.value = function()
	{
		var wordValue = "";
		for (var i = 0; i < this.elements.length; i++)
		{
			wordValue += this.elements[i].letter;
		}	
		return wordValue;
	}	
	this.draw = function() 
	{
    for (var i = 0; i < this.elements.length; i++)
    {
      this.elements[i].draw(this.goodWord);
    }
	}	
}
function drawLetter(x, y, l, goodWord)
{
	if ("W".indexOf(l) > -1)
	{
		x -= 2;
	}
	else if ("M".indexOf(l) > -1)
	{
		x -= 1;
	}
	else if ("I".indexOf(l) > -1)
	{
		x += 3;
	}
	else if ("J?*".indexOf(l) > -1)
	{
		x += 2;
	}
	else if ("RAELYTP".indexOf(l) > -1)
	{
		x += 1;
	}
  ctx.font = "18px Arial";
	ctx.textAlign = "left";
	ctx.textBaseline = "top";
	ctx.fillStyle = (goodWord === true) ? getColorDARKORANGE() : getColorBLACK();
	ctx.fillText(l, x + 3, y);
}
function objSlot(param1, param2)
{
	// parameters will be either x and y or letter and slotNumber
	if (isNaN(param1))				// it's a letter
	{
		this.letter = param1;
    this.x = xCOORDINATES[param2 % SLOTSPERROW];
    this.y = yCOORDINATES[Math.floor(param2 / SLOTSPERROW)];
		this.status = POOLUNSELECTED;
	}
	else
	{
	  this.x = param1;
    this.y = param2;
    this.letter = FILLER;
		this.status = INWORD;
	}
//console.log("Param1: " + param1 + ", Param2: " + param2 + ", x: " + this.x + ", y: " + this.y);
	this.className = objSlotClassName;
	this.assignSlot = function(n)
	{
    this.x = xCOORDINATES[n % SLOTSPERROW];
    this.y = yCOORDINATES[Math.floor(n / SLOTSPERROW)];
	}
	this.putLetterBack = function()
	{
		for (var i = 0; i < levelPool.length; i++)
		{
			if (levelPool[i].status === POOLUSED)
			{
				if (levelPool[i].letter === this.letter)
				{
					levelPool[i].status = POOLUNSELECTED;
					this.letter = FILLER;
					return;
				}	
			}	
		}	
	}
	this.clicked = function()
	{
		if (currentX >= this.x &&	currentX <= this.x + SIZE)
		{
			if (currentY >= this.y && currentY <= this.y + SIZE)
			{
				return true;
			}
		}	
		return false;
	}
  this.draw = function(goodWord)
  {
    if (this.letter != FILLER)
    {
      if (this.status == POOLSELECTED)
      {
         ctx.fillStyle = getColorGOLD();
      }
			else if (this.status == POOLUSED)
      {
        ctx.fillStyle = getColorDARKGRAY();
      }
      else
      {
        ctx.fillStyle = getColorPALECHARTREUSE();
      }
		}
    else
    {
      ctx.fillStyle = clrBACKGROUND;
    }
    ctx.fillRect(this.x, this.y, SIZE, SIZE);
		ctx.beginPath();
		ctx.lineWidth = "1";
		ctx.strokeStyle = getColorBLACK();
		ctx.rect(this.x, this.y, SIZE, SIZE);
		ctx.stroke();			        
    if (this.letter != FILLER)
    {
			if (this.status != POOLINACTIVE)
			{
				drawLetter(this.x, this.y, this.letter, goodWord);
			}
    }
  }
}
function deselect()
{
	if (selectedSlot != null)
	{
		selectedSlot.status = POOLUNSELECTED;
		selectedSlot = null;
	}
}
function objAnchor(letter, x, y)
{
  this.letter = letter;
  this.x = x;
  this.y = y;
	this.className = objAnchorClassName;
//console.log("Anchor letter " + this.letter + ", x: " + this.x + ", y: " + this.y);
  this.draw = function(goodWord)
  {
    drawLetter(this.x, this.y, letter, goodWord);
  }
}
function mouseClick(event)
{
	var rect = canvas.getBoundingClientRect();
	currentX = event.clientX - rect.left;
	currentY = event.clientY - rect.top;
	stringMessage = "";
	if (btnGiveUp.label === lblGiveUp)              // this means level is in progress
	{
		if (btnGiveUp.clicked(currentX, currentY))
		{
			if (window.confirm("Close this game out, and start a new one from Level 1?") == true) 
			{
				storeGame(Date.now());
				document.reloadForm.submit();
			}	
			return;
		}	
		else if (btnWords.clicked(currentX, currentY))
		{
			alert(wordList);
			return;
		}
		else if (btnHint.clicked(currentX, currentY))
		{
//		if (window.confirm("Put letters back if they don't match the word we had in mind?") == true) 
			{
				for (var targetWord = 0; targetWord < intLevel; targetWord++)
				{	// look at each element to see if it's a slot
					for (var targetWordLetter = 0; targetWordLetter < levelWords[targetWord].elements.length; targetWordLetter++)
					{
							// if it's a slot, if the letter isn't the one in the original word, return it
						if (levelWords[targetWord].elements[targetWordLetter].className === objSlotClassName)
						{
							if (levelWords[targetWord].elements[targetWordLetter].letter != FILLER)
							{
								if (levelWords[targetWord].elements[targetWordLetter].letter != levelWords[targetWord].originalWord.substring(targetWordLetter, targetWordLetter + 1))
								{	// put the letter back
									levelWords[targetWord].elements[targetWordLetter].putLetterBack();
									levelWords[targetWord].goodWord = false;
								}
							}
						}
					}	
				}
			}	
			return;
		}		
		else
		{
				// this is looking at each slot in the letter pool
				// if you click on a slot in the pool
				//     if the slot is empty
				//        if a slot has already been selected
				//			move its letter from that slot to this slot
				//		  otherwise, no action
				//     if there's a letter in the slot
				//		  if this letter was already in selected state, unselect it
				//        else if another slot has already been selected
				//			de-select that one, and select this one
			for (var slotIndex = 0; slotIndex < levelPool.length; slotIndex++)
			{
				if (levelPool[slotIndex].clicked(currentX, currentY))				// if slot was clicked
				{
					if (levelPool[slotIndex].letter != null)					// if there's a letter in the slot
					{
						// if this letter was already selected, and you click again, deselect it
						if (levelPool[slotIndex].status == POOLSELECTED)
						{
							deselect();
						}
						else if (levelPool[slotIndex].status != POOLUSED)
						{ 	// if another letter was already selected, deselect it
							deselect();
							// now select this one
							selectedSlot = levelPool[slotIndex];
							levelPool[slotIndex].status = POOLSELECTED;
						}
					}
					return;
				}
			}
			// this is looking at the slots in each word on the word list
			for (var targetWord = 0; targetWord < intLevel; targetWord++)
			{	// look at each element to see if it's a slot
				for (var targetWordLetter = 0; targetWordLetter < levelWords[targetWord].elements.length; targetWordLetter++)
				{
					if (levelWords[targetWord].elements[targetWordLetter].className === objSlotClassName)
					{
						// for each letterHolder (non-anchor) within the word
						if (levelWords[targetWord].elements[targetWordLetter].clicked(currentX, currentY))
						{	// you've clicked on a destination
							if (levelWords[targetWord].elements[targetWordLetter].letter != null)
							{	//there's already a letter here - return it to the pool
								levelWords[targetWord].elements[targetWordLetter].putLetterBack();
								levelWords[targetWord].goodWord = false;
							}
							if (selectedSlot != null)
							{
								if (selectedSlot.letter != null)
								{
									levelWords[targetWord].elements[targetWordLetter].letter = selectedSlot.letter;
									selectedSlot.status = POOLUSED;
									selectedSlot = null;
									checkWords();
								}
							}
							return;
						}
					}
				}
			}	
		}
	}		
	else
	{
		if (btnGiveUp.clicked(currentX, currentY))
		{
			initLevel();
		}	
	}
}
function checkWords()
{	
	intWordsFormed = 0;
	for (var targetWord = 0; targetWord < intLevel; targetWord++)
	{
		levelWords[targetWord].goodWord = false;
		if (levelWords[targetWord].value().indexOf(FILLER) === -1)
		{
//		console.log("Checking word " + levelWords[targetWord].value());	
			for (var i = 0; i < words.length; i++)
			{
				if (words[i] === levelWords[targetWord].value())
				{
					intWordsFormed += 1;
					levelWords[targetWord].goodWord = true;
//				console.log("Adding 1 for " + levelWords[targetWord].originalWord + " with " + words[i]);
					break;
				}		
			}
		}
//	intWordsFormed = intLevel;
		if (intWordsFormed === intLevel)		
		{
			stringMessage = "Click the button to start level " + (playLevel + 1) + "!";
			btnGiveUp.label = lblNextLevel + (playLevel + 1);
			btnGiveUp.color = clrNextLevel;
		}
	}	
}
// Draw everything
function render() 
{
	ctx.fillStyle = clrBACKGROUND;
	ctx.fillRect(0,0,canvas.width,canvas.height);
  btnHint.draw();
  btnWords.draw();
	btnGiveUp.draw();
	for (var i = 0; i < intLevel; i++)
	{
		levelWords[i].draw();
	}
	if (btnGiveUp.label === lblGiveUp)              // this means level is in progress
	{
		for (var i = 0; i < levelPool.length; i++)
		{
			levelPool[i].draw();
		}
	}
	else
	{
		for (var i = 0; i < intLevel; i++)
		{
			levelWords[i].drawOriginalWord();
		}
	}		
	printText(lblLevel + playLevel, 400, 0, getColorDARKBLUE(), 24);	
	printText(lblWords + intWordsFormed, 520, 0, getColorDARKORANGE(), 24);	
	printText(stringMessage, 0, 0, getColorDARKGREEN(), 24);
}
function printText(string, x, y, color, size)
{
	ctx.font = size + "px Arial";
	ctx.textAlign = "left";
	ctx.textBaseline = "top";
	ctx.fillStyle = color;
	ctx.fillText(string, x, y);
}