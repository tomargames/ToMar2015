// Create the canvas and game variables
var canvas = document.getElementById("dbCanvas");
var ctx = canvas.getContext("2d");
var dialog = document.getElementById("dialog");
var NUMTILES = 145;
var intSIZE = 70;
var tiles = [];
var levelColors = [getColorLIGHTCYAN(),getColorCHARTREUSE(),getColorDARKGREEN(), getColorDARKRED()];
var hiColor = getColorORANGE();
var textColor = getColorBLACK();
var backGroundColor = getColorCREAM();
var stringLETTERSET = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
var stringDisplayImages = "Show images";
var stringDisplaySymbols = "Show symbols";
var stringCookieName = "shanghaiGame";
var intTilesLeft = NUMTILES - 1;
var intLowest = NUMTILES - 1;
var intMoves = 0;
var intTotalMoves = 0;
var sel = [NUMTILES,NUMTILES];
var finds = [NUMTILES, NUMTILES, NUMTILES, NUMTILES];
var taken1 = [];
var taken2 = [];
var btnBackUp = new tmButton(950, 90, getColorLIGHTBLUE(), 120, 35, "Back Up", 16);
var btnHint = new tmButton(950, 150, getColorLIGHTGREEN(), 120, 35, "Get a Hint", 16);
var btnFind = new tmButton(950, 210, getColorLIGHTPINK(), 120, 35, "Find", 16);
var btnDisplayImages = new tmButton(950, 270, getColorLIGHTPURPLE(), 120, 35, stringDisplaySymbols, 16);
var btnTaken = new tmButton(950, 420, getColorPALECYAN(), 120, 35, "Taken?", 16);
var btnStartOver = new tmButton(950, 480, getColorLIGHTYELLOW(), 120, 35, "Start Over", 16);
var btnNewGame = new tmButton(950, 540, getColorLIGHTMAGENTA(), 120, 35, "New Puzzle", 16);
var btnClearSels = new tmButton(950, 600, getColorORANGE(), 120, 35, "Clear", 16);
var savedGame = getCookie(stringCookieName);
var tileData = [];
loadTileData();
canvas.addEventListener("click", mouseClick);
// cookie will be 145 character string to load tiles with
//                4-digit intTotalMoves
//								3-digit intMoves
//                3-digit taken1 and 3-digit taken2 for each intMoves
initPuzzle();
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

// all the logic to create the puzzle
function initPuzzle()
{
	var charUniverse = stringLETTERSET + stringLETTERSET + stringLETTERSET + stringLETTERSET;
	for (var i = NUMTILES - 1; i > -1; i--)
	{
		tiles[i] = new objTile(i);
		if (i !== 85)	// 85 and 144 are the same tile
		{
			if (savedGame == "")
			{
				var rnd = getIntRnd(charUniverse.length - 1);	
				tiles[i].character = charUniverse.substr(rnd, 1);
				charUniverse = charUniverse.substring(0, rnd) + charUniverse.substring(rnd + 1);
			}
			else
			{
				tiles[i].character = savedGame.substring(i, i + 1);
			}		
		}
	}
	tiles[85].taken = true;
	tiles[85].character = tiles[144].character;
	if (savedGame == "")
	{
		intTotalMoves = 0;
		intMoves = 0;
		intLowest = NUMTILES - 1;
		intTilesLeft = NUMTILES - 1;
	}
	else	
	{
		intTotalMoves = savedGame.substring(145, 149);
		var moves =			savedGame.substring(149, 152);	
		savedGame = savedGame.substring(152);
		intTotalMoves -= moves;						// because they'll get added back in as we apply the moves
		for (var i = 0; i < moves; i++)
		{
			sel[0] = savedGame.substring(0, 3) * 1;
			sel[1] = savedGame.substring(3, 6) * 1;
			savedGame = savedGame.substring(6);
			takeTiles();
		}
		stringMessage = "Resuming a Shanghai game from a previous session.";
	}	
}		
// objTile constructor
function objTile(num) 
{
	this.row = tileData[num].substring(0, 1).valueOf();
  this.column = tileData[num].substring(3, 5).valueOf();
  this.level = tileData[num].substring(6, 7).valueOf() - 1;
  this.tileToLeft = tileData[num].substring(9, 12).valueOf() - 1;
  this.tileToRight = tileData[num].substring(12, 15).valueOf() - 1;
  this.tileAbove = tileData[num].substring(15, 18).valueOf() - 1;
  this.taken = false;
  this.highlighted = false;
  this.character = "X";
  this.getX = function()
  {
   	if (this.column == 16) 
    {
	    return 1 + (Math.round(6.5 * intSIZE));
    }
    return 1 + (this.column - 1) * intSIZE;
  }
  this.getY = function()
  {
    if  (this.row < 9)
    {
      return (this.row * intSIZE);
    }
    return (Math.round(intSIZE * 4.5));
  }
	this.draw = function() 
	{
    if (this.taken == false)
    {
			if (this.highlighted !== true)
      {
				ctx.fillStyle = levelColors[this.level];
      }
			else
      {
				ctx.fillStyle = hiColor;
      }
			var r = this.getY() + 15;
			ctx.fillRect(this.getX(), r, intSIZE, intSIZE);
			ctx.beginPath();
			ctx.lineWidth = "1";
			ctx.strokeStyle = backGroundColor;
			ctx.rect(this.getX(), r, intSIZE, intSIZE);
			ctx.stroke();			
			if (tileReady[this.character] && btnDisplayImages.label == stringDisplaySymbols) 
			{
				ctx.drawImage(tileImage[this.character], this.getX() + 10, r + 10);
			}
      else
      {
      	ctx.font = "42px Arial";
				ctx.textAlign = "left";
				ctx.textBaseline = "top";
				ctx.fillStyle = textColor;
				// if you're ever in this code again, test this with the xFactor method in AAA
				var c = (this.character === "W") ? this.getX() + 16 : (this.character === "I") ? this.getX() + 26 : this.getX() + 20;
				ctx.fillText(this.character, c, r + 12);
			}	
		}	
	}
	this.clicked = function()
	{
		if (currentX >= this.getX() && currentX <= this.getX() + intSIZE)
		{
			if (currentY >= this.getY() && currentY <= this.getY() + intSIZE)
			{
				if (this.taken == false)
				{
					return true;	
				}	
			}
		}
		return false;
	}
	this.isFree = function()
  {   
    // check to see if tile on top is there
    if  (this.tileAbove > -1 && tiles[this.tileAbove].taken == false)
    {
      return false;
    }
    // check for tile on right
    if  (this.tileToRight > -1)
    {   
      if  (tiles[this.tileToRight].taken == false)
      {
      	// right is blocked, check left
        if  (this.tileToLeft > -1)
        {
          if  (tiles[this.tileToLeft].taken == false)
          {
            return false;
          }
          else if (this.tileToLeft == 41 && tiles[53].taken == false)
          {
            // special logic for tile 86
            return false;
          }
          else
          {	
            return true;
          }    
        }
      }    
    }
    return true;
  }    
  this.isVisible = function()
  {   
    // check to see if tile on top is there
    if  (this.tileAbove > -1)
    {   
       if  (tiles[this.tileAbove].taken == false)
       {
         return false;
       }    
    }
    return true;
  }    
}
function mouseClick(event)
{
	var rect = canvas.getBoundingClientRect();
	currentX = event.clientX - rect.left;
	currentY = event.clientY - rect.top;
	stringMessage = "";
	if (btnDisplayImages.clicked(currentX, currentY))
	{
		if (btnDisplayImages.label == stringDisplaySymbols)
		{
			btnDisplayImages.label = stringDisplayImages;
		}
		else	
		{
			btnDisplayImages.label = stringDisplaySymbols;
		}
	}
	else if (btnClearSels.clicked(currentX, currentY))
	{
		resetSel();
	}		
	else if (btnStartOver.clicked(currentX, currentY))
	{
		if (window.confirm("Restart puzzle from the beginning?") == true) 
		{
	    while (intMoves > 0)
  	  {
    	  backUp();
    	}  
    } 	
  }   
	else if (btnNewGame.clicked(currentX, currentY))
	{
		if (window.confirm("Abandon this puzzle and generate a new one?") == true) 
		{
			initPuzzle();
    } 	
  }   
	else if (btnBackUp.clicked(currentX, currentY))
	{
		backUp();
	}				
 	else if (btnHint.clicked(currentX, currentY))
	{
		getHint();
	}
 	else if (btnFind.clicked(currentX, currentY))
	{
 		dialog.showModal();
 		dialog.addEventListener('close', doFind); 
 	}	
 	else if (btnTaken.clicked(currentX, currentY))
	{
 		dialog.showModal();
 		dialog.addEventListener('close', doTaken); 
 	}
	else
	{			
  	for (var i = NUMTILES - 1; i > -1; i--)
		{
			if (tiles[i].clicked())
			{
				// special processing for if a hint has been highlighted, if one is clicked, just take the tiles
				if ((sel[0] == i || sel[1] == i) && sel[0] < NUMTILES && sel[1] < NUMTILES)
				{
          takeTiles();
          stringMessage = "Took the hint!";
				}	
	      // tile must be free 
	      else if (tiles[i].isFree())
	      {     
	        // see if something's already selected
	        if (sel[0] == NUMTILES)
	        {  
	          sel[0] = i;
	          tiles[i].highlighted = true;
	          if (btnDisplayImages.label == stringDisplaySymbols)
	          {
	          	stringMessage = tiles[i].character + "   " + tileName[tiles[i].character];
	          }	
	        }
	        else
	        {
	          // if this is already selected, unselect it
	          if (sel[0] == i)
	          {
	            sel[0] = NUMTILES;
	            tiles[i].highlighted = false;
	            stringMessage = "";
	          }
	          else
	          {
	           	 // tiles characters have to match
	            if (tiles[i].character == tiles[sel[0]].character)
	            {
	              sel[1] = i;	
	              takeTiles();
	              stringMessage = "";
	            }
	            else
	            {
	              stringMessage = "Tiles don't match";
	            }   
	          }    
	        }
	      }    
	      else
	      {     
	       	stringMessage = "Tile not free";
	      }
	      break;	
	    }
	  }     
  }
}
function getHint()
{
	resetSel();
	for (var i = 0; i < NUMTILES; i++)
	{
		if  (tiles[i].taken == false && tiles[i].isFree() == true)
		{
			for (var j = i + 1; j < NUMTILES; j++)
			{
				if  (tiles[j].taken == false && tiles[j].isFree() == true)
				{
					if  (tiles[j].character == tiles[i].character)
					{
						tiles[i].highlighted = true;
						tiles[j].highlighted = true;
						sel[0] = i;
						sel[1] = j;
						return;
					}
				}
			}
		}
	}		
 	stringMessage = "No moves left.";
}
function backUp()
{
  if (intMoves > 0)
  {
  	 resetSel();
     intMoves -= 1;
     // find out what tiles were taken
     tiles[taken1[intMoves]].taken = false;
     tiles[taken2[intMoves]].taken = false;
     intTilesLeft += 2;
  }
}    
// Draw everything
function render() 
{
	ctx.fillStyle = backGroundColor;
	ctx.fillRect(0,0,canvas.width,canvas.height);
	//top part
	printText("TilesLeft: " + intTilesLeft + "    Moves: " + intTotalMoves + "    Lowest: " + intLowest, 12, 5, getColorDARKBLUE(), 24);
	printText(stringMessage, 12, 45, getColorDARKGREEN(), 20);
  //paint all the tiles
  for (var i = 0; i < NUMTILES; i++)
  {
    tiles[i].draw();
  }
  btnBackUp.draw();
  btnHint.draw();
  btnFind.draw();
  btnDisplayImages.draw();
  btnTaken.draw();
  btnStartOver.draw();
  btnNewGame.draw();
  btnClearSels.draw();
}
function printText(string, x, y, color, size)
{
	ctx.font = size + "px Arial";
	ctx.textAlign = "left";
	ctx.textBaseline = "top";
	ctx.fillStyle = color;
	ctx.fillText(string, x, y);
}
function takeTiles()
{
 	tiles[sel[0]].taken = true;
  tiles[sel[1]].taken = true;
  tiles[sel[0]].highlighted = false;
  tiles[sel[1]].highlighted = false;
  // store tiles by move
  taken1[intMoves] = sel[0];
  taken2[intMoves] = sel[1];
  intMoves += 1;
	intTotalMoves += 1;
	// store game to cookie
	var cookieString = "";
	for (var i = 0; i < NUMTILES; i++)
	{
		cookieString = cookieString + tiles[i].character;
	}
	cookieString = cookieString + formatNumber(intTotalMoves, 4);	
	cookieString = cookieString + formatNumber(intMoves, 3);	
	for (var i = 0; i < intMoves; i++)
	{
		cookieString = cookieString + formatNumber(taken1[i], 3) + formatNumber(taken2[i], 3);
	}
	setCookie(stringCookieName, cookieString, 365);	
	intTilesLeft -= 2;
  resetSel();
  if  (intTilesLeft < intLowest)
  {
    intLowest = intTilesLeft;
  }
  if  (intTilesLeft == 0)  // change!!!!!!  ---> this is end-of-game processing
  {
 		document.cookie = stringCookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
		document.gameForm.score.value = intTotalMoves;
		document.gameForm.tsp.value = Date.now();
		document.gameForm.submit();
  }
}   
function resetSel()
{
  for (var i = 0; i < 2; i++)
  {
  	if (sel[i] != NUMTILES)
  	{
  		tiles[sel[i]].highlighted = false;
  		sel[i] = NUMTILES;
  	}	
  }	
  for (var i = 0; i < 4; i++)
  {
  	if (finds[i] != NUMTILES)
  	{
  		tiles[finds[i]].highlighted = false;
  		finds[i] = NUMTILES;
  	}	
  }	
}
function doTaken()
{
	resetSel();
	var takenAt = NUMTILES;
  for (var i = intMoves - 1; i > -1 ; i--)
  {
   	if  (tiles[taken1[i]].character == dialog.returnValue)
    {
      takenAt = i;
      break;
    }
	}
	if (takenAt == NUMTILES)
	{
		stringMessage = dialog.returnValue + " " + tileName[dialog.returnValue] + " has not been taken yet.";
	}
	else
	{
		if (window.confirm(dialog.returnValue + " " + tileName[dialog.returnValue] + " was taken at move " + (NUMTILES - 1 - (2 * takenAt)) + ". Restore?") == true) 
		{
     	while (intMoves > takenAt)
     	{
         backUp();
    	}
			stringMessage = "Restored to where " + dialog.returnValue + " " + tileName[dialog.returnValue] + " was taken.";
    }
	}
	dialog.removeEventListener('close', doTaken);
}		
function doFind()
{
  if (stringLETTERSET.indexOf(dialog.returnValue) > -1)
  {
		resetSel();
		var counter = 0;
		for (var i = 0; i < NUMTILES; i++)
		{
			if  (tiles[i].taken == false && tiles[i].isVisible() == true)
			{
				if  (tiles[i].character == dialog.returnValue)
				{
					tiles[i].highlighted = true;
					finds[counter++] = i;
				}
			}
		}	
	}
	stringMessage = counter + " tiles were found.";
	dialog.removeEventListener('close', doFind);
}
// tile data
function loadTileData()
{
	tileData = [
		"1  2  1  0  2  0  ",   //001
		"1  3  1  1  3  0  ",   //002
		"1  4  1  2  4  0  ",   //003
		"1  5  1  3  5  0  ",   //004
		"1  6  1  4  6  0  ",   //005
		"1  7  1  5  7  0  ",   //006
		"1  8  1  6  8  0  ",   //007
		"1  9  1  7  9  0  ",   //008
		"1  10 1  8  10 0  ",   //009
		"1  11 1  9  11 0  ",   //010
		"1  12 1  10 12 0  ",   //011
		"1  13 1  11 0  0  ",   //012
		"2  4  1  0  14 0  ",   //013
		"2  5  1  13 15 89 ",   //014
		"2  6  1  14 16 90 ",   //015
		"2  7  1  15 17 91 ",   //016
		"2  8  1  16 18 92 ",   //017
		"2  9  1  17 19 93 ",   //018
		"2  10 1  18 20 94 ",   //019
		"2  11 1  19 0  0  ",   //020
		"3  3  1  0  22 0  ",   //021
		"3  4  1  21 23 0  ",   //022
		"3  5  1  22 24 95 ",   //023
		"3  6  1  23 25 96 ",   //024
		"3  7  1  24 26 97 ",   //025
		"3  8  1  25 27 98 ",   //026
		"3  9  1  26 28 99 ",   //027
		"3  10 1  27 29 100",   //028
		"3  11 1  28 30 0  ",   //029
		"3  12 1  29 0  0  ",   //030
		"4  2  1  85 32 0  ",   //031
		"4  3  1  31 33 0  ",   //032
		"4  4  1  32 34 0  ",   //033
		"4  5  1  33 35 101",   //034
		"4  6  1  34 36 102",   //035
		"4  7  1  35 37 103",   //036
		"4  8  1  36 38 104",   //037
		"4  9  1  37 39 105",   //038
		"4  10 1  38 40 106",   //039
		"4  11 1  39 41 0  ",   //040
		"4  12 1  40 42 0  ",   //041
		"4  13 1  41 87 0  ",   //042
		"5  2  1  85 44 0  ",   //043
		"5  3  1  43 45 0  ",   //044
		"5  4  1  44 46 0  ",   //045
		"5  5  1  45 47 107",   //046
		"5  6  1  46 48 108",   //047
		"5  7  1  47 49 109",   //048
		"5  8  1  48 50 110",   //049
		"5  9  1  49 51 111",   //050
		"5  10 1  50 52 112",   //051
		"5  11 1  51 53 0  ",   //052
		"5  12 1  52 54 0  ",   //053
		"5  13 1  53 87 0  ",   //054
		"6  3  1  0  56 0  ",   //055
		"6  4  1  55 57 0  ",   //056
		"6  5  1  56 58 113",   //057
		"6  6  1  57 59 114",   //058
		"6  7  1  58 60 115",   //059
		"6  8  1  59 61 116",   //060
		"6  9  1  60 62 117",   //061
		"6  10 1  61 63 118",   //062
		"6  11 1  62 64 0  ",   //063
		"6  12 1  63 0  0  ",   //064
		"7  4  1  0  66 0  ",   //065
		"7  5  1  65 67 119",   //066
		"7  6  1  66 68 120",   //067
		"7  7  1  67 69 121",   //068
		"7  8  1  68 70 122",   //069
		"7  9  1  69 71 123",   //070
		"7  10 1  70 72 124",   //071
		"7  11 1  71 0  0  ",   //072
		"8  2  1  0  74 0  ",   //073
		"8  3  1  73 75 0  ",   //074
		"8  4  1  74 76 0  ",   //075
		"8  5  1  75 77 0  ",   //076
		"8  6  1  76 78 0  ",   //077
		"8  7  1  77 79 0  ",   //078
		"8  8  1  78 80 0  ",   //079
		"8  9  1  79 81 0  ",   //080
		"8  10 1  80 82 0  ",   //081
		"8  11 1  81 83 0  ",   //082
		"8  12 1  82 84 0  ",   //083
		"8  13 1  83 0  0  ",   //084
		"9  1  1  0  31 0  ",   //085
		"9  16 1  0  0  0  ",   //086
		"9  14 1  42 88 0  ",   //087
		"9  15 1  87 0  0  ",   //088
		"2  5  2  0  90 0  ",   //089
		"2  6  2  89 91 0  ",   //090
		"2  7  2  90 92 0  ",   //091
		"2  8  2  91 93 0  ",   //092
		"2  9  2  92 94 0  ",   //093
		"2  10 2  93 0  0  ",   //094
		"3  5  2  0  96 0  ",   //095
		"3  6  2  95 97 125",   //096
		"3  7  2  96 98 126",   //097
		"3  8  2  97 99 127",   //098
		"3  9  2  98 100128",   //099
		"3  10 2  99 0  0  ",   //100
		"4  5  2  0  1020  ",   //101
		"4  6  2  101103129",   //102
		"4  7  2  102104130",   //103
		"4  8  2  103105131",   //104
		"4  9  2  104106132",   //105
		"4  10 2  1050  0  ",   //106
		"5  5  2  0  1080  ",   //107
		"5  6  2  107109133",   //108
		"5  7  2  108110134",   //109
		"5  8  2  109111135",   //110
		"5  9  2  110112136",   //111
		"5  10 2  1110  0  ",   //112
		"6  5  2  0  1140  ",   //113
		"6  6  2  113115137",   //114
		"6  7  2  114116138",   //115
		"6  8  2  115117139",   //116
		"6  9  2  116118140",   //117
		"6  10 2  1170  0  ",   //118
		"7  5  2  0  1200  ",   //119
		"7  6  2  1191210  ",   //120
		"7  7  2  1201220  ",   //121
		"7  8  2  1211230  ",   //122
		"7  9  2  1221240  ",   //123
		"7  10 2  1230  0  ",   //124
		"3  6  3  0  1260  ",   //125
		"3  7  3  1251270  ",   //126
		"3  8  3  1261280  ",   //127
		"3  9  3  1270  0  ",   //128
		"4  6  3  0  1300  ",   //129
		"4  7  3  129131141",   //130
		"4  8  3  130132142",   //131
		"4  9  3  1310  0  ",   //132
		"5  6  3  0  1340  ",   //133
		"5  7  3  133135143",   //134
		"5  8  3  134136144",   //135
		"5  9  3  1350  0  ",   //136
		"6  6  3  0  1380  ",   //137
		"6  7  3  1371390  ",   //138
		"6  8  3  1381400  ",   //139
		"6  9  3  1390  0  ",   //140
		"4  7  4  0  142145",   //141
		"4  8  4  1410  145",   //142
		"5  7  4  0  144145",   //143
		"5  8  4  1430  145",   //144
		"9  16 1  0  0  0  "   //086 loaded again
			];
}